<?php
if ( ! class_exists( 'BravePop_MailerLite_Advanced' ) ) {
   
   class BravePop_MailerLite_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['mailerlite']->api)  ? $integrations['mailerlite']->api  : '';
      }
      
      public function get_fields($apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}

         $args = array(
            'headers' => array(
               'X-MailerLite-ApiKey' => $apiKey
            )
         );

         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://api.mailerlite.com/api/v2/fields', $args );
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );

            if($fieldsData){
               $fields = $fieldsData;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     //if(isset($field->id)){
                        $fieldItem->id = isset($field->key) ? $field->key : '';
                        $fieldItem->name = isset($field->title) ? $field->title : '';
                        $fieldItem->type = isset($field->type) ? $field->type : 'TEXT';
                        $finalFields[] = $fieldItem;
                     //}
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