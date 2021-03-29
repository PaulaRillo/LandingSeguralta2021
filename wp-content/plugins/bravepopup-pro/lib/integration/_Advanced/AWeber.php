<?php
if ( ! class_exists( 'BravePop_Aweber_Advanced' ) ) {
   
   class BravePop_Aweber_Advanced {

      public function get_fields($listID=''){
         if(!$listID){ return error_log('AWEBER List ID MISSING!!!!!'); }
         $AWEBER = new BravePop_Aweber();
         $access_token = $AWEBER->get_access_token();
         $accountID = $AWEBER->get_accountID($access_token);
         
         if(!$access_token){ return error_log('AWEBER access_token MISSING!!!!!'); }
         if(!$accountID){ return error_log('AWEBER accountID MISSING!!!!!'); }

         $theData = array('fields'=>array(), 'tags' => array());

         $headerArgs = array( 'headers' => array(  'Authorization' => 'Bearer ' . $access_token ) );

         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://api.aweber.com/1.0/accounts/'.$accountID.'/lists/'.$listID.'/custom_fields', $headerArgs );

         //error_log(json_encode($fieldsResponse));
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );

            if($fieldsData && isset($fieldsData->entries)){
               $fields = $fieldsData->entries;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->name) ? $field->name : '';
                     $fieldItem->name = isset($field->name) ? $field->name : '';
                     $finalFields[] = $fieldItem;
                  }
               }
               //error_log(json_encode($finalLists));
               $theData['fields'] = $finalFields;
            }
         }


         return json_encode($theData);

      }

   }

}
?>