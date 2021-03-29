<?php
if ( ! class_exists( 'BravePop_Hubspot_Advanced' ) ) {

   class BravePop_Hubspot_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['hubspot']->api)  ? $integrations['hubspot']->api  : '';
      }

      
      public function get_fields($apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return false; }
         //https://legacydocs.hubspot.com/docs/methods/contacts/v2/get_contacts_properties

         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://api.hubapi.com/properties/v1/contacts/properties?hapikey='.$apiKey);
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );

            if($fieldsData && is_array($fieldsData)){
               $fields = $fieldsData;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->name) ? $field->name : '';
                     $fieldItem->name = isset($field->label) ? $field->label : '';
                     $fieldItem->type = isset($field->type) ? $field->type : '';
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