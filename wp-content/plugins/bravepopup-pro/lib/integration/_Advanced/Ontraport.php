<?php
if ( ! class_exists( 'BravePop_Ontraport_Advanced' ) ) {
   
   class BravePop_Ontraport_Advanced {

      function __construct() {
         // $braveSettings = get_option('_bravepopup_settings');
         // $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         // $this->api_key = isset($integrations['ontraport']->api)  ? $integrations['ontraport']->api  : '';
         // $this->api_secret = isset($integrations['ontraport']->secret)  ? $integrations['ontraport']->secret  : '';
      }


      public function get_fields(){

         $theData = array('fields'=>array(), 'tags' => array());

         $finalFields = array();

         $fields = array(
            // 'email' => 'Email Address',
            // 'firstname' => 'First Name',
            // 'lastname' => 'Last Name',
            'office_phone' => 'Office Phone Number',
            'home_phone' => 'Home Phone',
            'cell_phone' => 'Cell Phone Number',
            'sms_number' => 'SMS Number ',
            'fax' => 'Fax',
            'company' => 'Company',
            'title' => 'Title',
            'website' => 'Website',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'zip' => 'Zip',
            'birthday' => 'Birthday',
            'priority' => 'Priority',
            'num_purchased' => 'Total orders',
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