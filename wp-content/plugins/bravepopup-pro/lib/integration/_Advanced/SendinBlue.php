<?php
if ( ! class_exists( 'BravePop_SendinBlue_Advanced' ) ) {
   
   class BravePop_SendinBlue_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['sendinblue']->api)  ? $integrations['sendinblue']->api  : '';
      }

      public function get_fields( $apiKey='' ){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}
         
         $theData = array('fields'=>array(), 'tags' => array());

         $args = array( 'headers' => array( 'api-key' => $apiKey ) );

         $fieldsResponse = wp_remote_get( 'https://api.sendinblue.com/v3/contacts/attributes', $args );
         //error_log(json_encode($fieldsResponse));
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            if($fieldsData && isset($fieldsData->attributes)){
               $fields = $fieldsData->attributes;
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