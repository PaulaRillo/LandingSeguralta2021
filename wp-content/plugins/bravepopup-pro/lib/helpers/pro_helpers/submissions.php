<?php
defined('ABSPATH') || exit;

global $bravepopup_submission_db_version;
$bravepopup_submission_db_version = '1.0';

function bravepop_init_submissiondb() {
   $setupCompleted = get_option( 'bravepopup_setup_submission_db' );
   //error_log('bravepop_init_submissiondb '. $setupCompleted);
   if ($setupCompleted !== 'complete' ) {
      //error_log('RUN DB SETUP!!');
      global $wpdb;
      global $bravepopup_submission_db_version;

      $submission_table_name = $wpdb->prefix . 'bravepopup_submissions';
      $charset_collate = $wpdb->get_charset_collate();

      $submissionTablesql = "CREATE TABLE $submission_table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         title varchar(50) DEFAULT '',
         submitted datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         settings TEXT,
         submission TEXT,
         automation TEXT,
         popup INT NOT NULL, 
         form_id varchar(50),
         form_settings TEXT,
         tags TEXT,
         country varchar(50),
         ip varchar(50),
         device varchar(20) DEFAULT '',
         url varchar(155) DEFAULT '' NOT NULL,
         user varchar(155) DEFAULT '',
         PRIMARY KEY  (id)
      ) $charset_collate;";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $submissionTablesql );

      add_option( 'bravepopup_setup_submission_db', 'complete' );
      add_option( 'bravepopup_submission_db_version', $bravepopup_submission_db_version );
   }
}
add_action( 'admin_init', 'bravepop_init_submissiondb' );



//Add Submission to DB
function bravepop_save_form_submission($popupID, $formSettings, $pageURL='', $completed_actions=array(), $userData=array()){
   //error_log('bravepop_save_form_submission! '.json_encode($formSettings['actions']));
   if(!$popupID || !isset($formSettings['fields']) || !isset($formSettings['actions'])){   return;  }

   // If its a Quiz, add a quiz score.
   // Multiselect data should be converted to string.

   //Process the Form Submission Data
   $formSubmission = array();
   foreach ((array)$formSettings['fields'] as $key => $field) {
      if($field->type !== 'label' && $field->type !== 'media' && $field->type !== 'step'){
         $item = new stdClass();
         $item->id = $field->id;
         $item->uid = $field->uid;
         $item->type = $field->type;
         $item->required = $field->required;
         $item->validation = isset($field->validation) ? $field->validation : '';
         $item->value = is_array($field->value) ? implode( ',', array_map( 'sanitize_textarea_field', $field->value) )  : sanitize_textarea_field($field->value);
         $item->label = $field->label ? $field->label : ($field->placeholder ? $field->placeholder : ''); 
         
         if($field->type === 'input' && isset($field->validation) && $field->validation === 'name'){
            if(!empty($field->uid1)){    $item->uid1 = $field->uid1;  }
            $item->secondlabel = $field->secondLabel ? $field->secondLabel : ($field->secondPlaceholder ? $field->secondPlaceholder : ''); 
         }
         $formSubmission[] = $item;
      }
   }


   //If Tracking is Enabled, add that to $completed_actions
   if(!empty($formSettings['actions']->track->enable)){
      $sentTo = array();
      if(!empty($formSettings['actions']->track->eventCategory)){
         $sentTo[] = 'Google Analytics';
      }
      if(!empty($formSettings['actions']->track->fbq_event_type)){
         $sentTo[] = 'Facebook';
      }
      if(count($sentTo) > 0){
         $completed_actions['tracking'] = $sentTo;
      }
   }

   $submission = array(
      'submitted' => current_time( 'mysql' ),
      'title'=>'',
      'submission'=> json_encode($formSubmission),
      'automation'=> $completed_actions ? json_encode($completed_actions) : json_encode(new stdClass()), 
      'form_id'=> isset($formSettings['id']) ? $formSettings['id'] : '',
      'settings'=>'',
      'form_settings'=>json_encode(
         array(
            'type' => isset($formSettings['options']->type) && $formSettings['options']->type === 'quiz' ? 'quiz' : 'general',
            'quiz_data'=> isset($formSettings['quiz']) ? $formSettings['quiz'] : ''
            )
      ),
      'popup' => intval($popupID),
      'country' => isset($userData['country']) ? $userData['country'] : '',
      'ip' => isset($userData['ip']) ? $userData['ip'] : '',
      'device' => isset($userData['device']) ? $userData['device'] : '',
      'url' =>  str_replace( get_site_url(), '', esc_url($pageURL)),
      'user' => isset($userData['ID']) ? json_encode(array('type'=>'registered', 'ID'=>$userData['ID'], 'username' => $userData['username'])) : json_encode(array('type'=> 'visitor')),
   );

   // error_log('$formSubmission: '.json_encode($submission));

   $submissionClass =  new BravePop_Submissions();
   $submissionAdded = $submissionClass->insertSubmission( $submission );
   // error_log(' $submissionAdded: '. $submissionAdded );
   return $submissionAdded;
}
