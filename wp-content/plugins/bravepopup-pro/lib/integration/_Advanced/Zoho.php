<?php
if ( ! class_exists( 'BravePop_Zoho_Advanced' ) ) {
   
   class BravePop_Zoho_Advanced {

      public function get_fields(){
         $zohoParent = new BravePop_Zoho();
         $access_token  = $zohoParent->get_access_token(); 
         if(!$access_token){ return error_log('Zoho ACCESS KEY/URL MISSING');}

         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $args = array(  'method' => 'GET','headers' => array( 'Authorization' => 'Zoho-oauthtoken ' . $access_token ) );
         $fieldsResponse = wp_remote_get( 'https://campaigns.zoho.com/api/v1.1/contact/allfields?type=json', $args );
         //error_log(json_encode($fieldsResponse));
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            if(isset($fieldsData->response->fieldnames->fieldname)){
               $fields = $fieldsData->response->fieldnames->fieldname;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->DISPLAY_NAME) ? $field->DISPLAY_NAME : '';
                     $fieldItem->name = isset($field->DISPLAY_NAME) ? $field->DISPLAY_NAME : '';
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