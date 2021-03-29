<?php
if ( ! class_exists( 'BravePop_SendGrid_Advanced' ) ) {
   
   class BravePop_SendGrid_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['sendgrid']->api)  ? $integrations['sendgrid']->api  : '';
      }


      public function get_fields( $apiKey='' ){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}
         
         $theData = array('fields'=>array(), 'tags' => array());

         $args = array(  'headers' => array( 'Authorization' => 'Bearer ' . $apiKey  ));
  
         $fieldsResponse = wp_remote_get( 'https://api.sendgrid.com/v3/marketing/field_definitions', $args );

         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
            // error_log(json_encode($fieldsData));
            if($fieldsData && isset($fieldsData->reserved_fields)){
               $finalFields = array();

               $reserved_fields = $fieldsData->reserved_fields;
               if(is_array($reserved_fields)){
                  foreach ($reserved_fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->name) ? $field->name : '';
                     $fieldItem->name = isset($field->name) ? $field->name : '';
                     $finalFields[] = $fieldItem;
                  }
               }

               $fields = isset($fieldsData->custom_fields) ? $fieldsData->custom_fields : array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->id) ? $field->id : '';
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