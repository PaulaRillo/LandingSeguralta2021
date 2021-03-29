<?php
if ( ! class_exists( 'BravePop_Klaviyo_Advanced' ) ) {
   
   class BravePop_Klaviyo_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['klaviyo']->api)  ? $integrations['klaviyo']->api  : '';
      }


      public function get_fields($list_id='', $apiKey='' ){
         $apiKey     = $apiKey ? $apiKey : $this->api_key;
         //if(!$list_id){ return false;}
         if(!$apiKey){ return false;}
         $theData = array('fields'=>array(), 'tags' => array());

         $finalFields = array();

         $fields = array(
            // 'email' => 'Email Address',
            // 'first_name' => 'First Name',
            // 'last_name' => 'Last Name',
            '$phone_number' => 'Phone Number',
            '$organization' => 'Organization',
            '$address1' => 'Address1',
            '$address2' => 'Address2',
            '$region' => 'Region',
            '$country' => 'Country',
            '$zip' => 'Zip',
            '$timezone' => 'Timezone',
            '$latitude' => 'Latitude',
            '$longitude' => 'Longitude',
            '$source' => 'Source',
         );

         foreach ($fields as $key => $field) {
            $fieldItem = new stdClass();
            $fieldItem->id = $key;
            $fieldItem->name = $field;
            $finalFields[] = $fieldItem;
         }
         
         $theData['fields'] = $finalFields;

         return json_encode($theData);

      }

   }

}
?>