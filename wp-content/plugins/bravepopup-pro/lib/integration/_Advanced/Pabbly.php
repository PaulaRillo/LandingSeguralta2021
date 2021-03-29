<?php
if ( ! class_exists( 'BravePop_Pabbly_Advanced' ) ) {
   
   class BravePop_Pabbly_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['pabbly']->api)  ? $integrations['pabbly']->api  : '';
      }
      
      public function get_fields($apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}

         $args = array(
            'headers' => array(
               'Authorization' => 'Bearer ' .$apiKey
            )
         );

         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://emails.pabbly.com/api/personalization-tags', $args );
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );

            if($fieldsData && isset($fieldsData->personalization_tags)){
               $fields = $fieldsData->personalization_tags;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     //if(isset($field->id)){
                        $fieldItem->id = isset($field->tag_value) ? $field->tag_value : '';
                        $fieldItem->name = isset($field->tag_name) ? $field->tag_name : '';
                        $fieldItem->type = 'TEXT';
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