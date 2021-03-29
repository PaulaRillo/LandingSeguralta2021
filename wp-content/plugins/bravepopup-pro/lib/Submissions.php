<?php
if ( ! class_exists( 'BravePop_Submissions' ) ) {
   
   class BravePop_Submissions {

      function __construct() {
         global $wpdb;
         $this->wpdb = $wpdb; 
         $this->table = $wpdb->prefix . 'bravepopup_submissions';
      }


      function fetchSubmissions( $popupID, $count=100, $page=0 ) {
         if(!$popupID){ return; }
         $popupID = absint( $popupID ); $pagination ='';
         $offset = $count *  $page;
         $total  = 0;

         if($page === 0){
            $totalSql   = 'SELECT COUNT(*) FROM ' . $this->table . " WHERE (`popup` = '$popupID' )";
            $total  = $this->wpdb->get_var( $totalSql );
         }

         $sql   = 'SELECT * FROM ' . $this->table . " WHERE (`popup` = '$popupID' ) ORDER BY submitted DESC LIMIT $count OFFSET $offset";
         return array('total'=> $total, 'submissions'=> $this->wpdb->get_results( $sql ));
      }

      function get_submission_csv( $popupID, $fieldsOnly=0 ) {
         if(!$popupID){ return; }
         $sql   = 'SELECT * FROM ' . $this->table . "  WHERE (`popup` = '$popupID' )";
         $allEntries = $this->wpdb->get_results( $sql );
         $submission_entries = array();

         foreach ($allEntries as $key => $entry) {
            $theEntry =  new stdClass();
            $theEntry->id = intval($entry->id);

            $formData = $entry->submission ? json_decode($entry->submission) : '';
            foreach ($formData as $key => $field) {
               $fieldKey = isset($field->uid) ? $field->uid : '';
               if(!$fieldKey && isset($field->uid1)){ $fieldKey = $field->uid1; }
               if(!$fieldKey && isset($field->label)){ $fieldKey = $field->label; }
               if(!$fieldKey && isset($field->id)){ $fieldKey = $field->id; }

               if(isset($field->value)){
                  $theEntry->$fieldKey = is_array($field->value) ? implode(',', $field->value) : sanitize_textarea_field($field->value);
               }
            }

            if(!$fieldsOnly){
               $theEntry->date =  $entry->submitted;
               $theEntry->ip =  $entry->ip;
               $theEntry->country =  $entry->country;
               $theEntry->souce_url =  $entry->url;

               $form_settings = isset($entry->form_settings) ? json_decode($entry->form_settings) : new stdClass();

               if(isset($form_settings->type) && $form_settings->type ==='quiz' && isset($form_settings->quiz_data)){
                  $quiz_score = 0;
                  $quizData = $form_settings->quiz_data;

                  if(isset($quizData->scoring) && $quizData->scoring === 'points' && isset($quizData->userScore)){
                     $quiz_score = $quizData->userScore;
                  }
                  if(isset($quizData->scoring) && $quizData->scoring === 'answer' && isset($quizData->userCorrect)){
                     $quiz_score = $quizData->userCorrect;
                  }
                  $theEntry->quiz_score =  $quiz_score;
               }
            }

            $submission_entries[] = $theEntry;
         }
         return $submission_entries; 
      }


      function getSingleSubmission( $entryID ) {
         if(! $entryID ){ return; }
         $sql   = 'SELECT * FROM ' . $this->table . "  WHERE (`id` = '$entryID' )";
         $submissions = $this->wpdb->get_results( $sql ); 
         $entry = $submissions[0];
         if($submissions[0]){
            $theEntry =  $entry;
            $theEntry->id = intval($theEntry->id);
            $theEntry->popup = intval($theEntry->popup);
            $theEntry->automation = $theEntry->automation ? json_decode($theEntry->automation) :'';
            $theEntry->settings = $theEntry->settings ? json_decode($theEntry->settings) : '';
            $theEntry->form_settings = $theEntry->form_settings ? json_decode($theEntry->form_settings) : '';
            $theEntry->submission = $theEntry->submission ? json_decode($theEntry->submission) : '';

            $userData = $theEntry->user ? json_decode($theEntry->user) : new stdClass();
            if(isset($userData->type) && $userData->type === "registered"){
               $userData =  array('type'=> $userData->type, 'ID'=> $userData->ID, 'avatar'=> get_avatar_url((int)$userData->ID), 'username'=> $userData->username);
            }
            
            $theEntry->user = $userData;
         }
         return $theEntry ? $theEntry : false;
      }

      function insertSubmission( $data ) {
         if(!$data){ return; }
         $this->wpdb->insert( $this->table, $data );
         return  $this->wpdb->insert_id;
      }

      function updateSubmission( $data, $where ) {
         if(!$data || !$where){ return; }
         $updatedEntry = $this->wpdb->update( $this->table, $data, $where );
         return $updatedEntry;
      }

      function deleteSubmissions( $submissionIDs ) {
         if(!$submissionIDs){ return; }
         $sql   = 'DELETE FROM ' . $this->table . "  WHERE id IN($submissionIDs)";
         return $this->wpdb->query( $sql );
      }

      

   }

}
?>