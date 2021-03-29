<?php
if ( ! class_exists( 'BravePop_Moosend_Advanced' ) ) {
   
   class BravePop_Moosend_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['moosend']->api)  ? $integrations['moosend']->api  : '';
      }


      public function get_fields($apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         $args = array(
            'method' => 'GET',
            'headers' => array('content-type' => 'application/json'),
         );

         $response = wp_remote_get( 'https://api.moosend.com/v3/lists.json?apikey='.$apiKey.'&WithStatistics=true&ShortBy=CreatedOn&SortMethod=ASC', $args );
         if( is_wp_error( $response ) ) {
            return false; // Bail early
         }
         $body = wp_remote_retrieve_body( $response );
         $data = json_decode( $body );
         
         if($data && isset($data->Context->MailingLists)){
            $lists = $data->Context->MailingLists;
            $theData = array('fields'=>array(), 'tags' => array());
            if($lists && is_array($lists)){
               foreach ($lists as $key => $list) {
                  if(isset($list->CustomFieldsDefinition) && is_array($list->CustomFieldsDefinition)){
                     foreach ($list->CustomFieldsDefinition as $key => $fld) {
                        $field = new stdClass();
                        $field->id = isset($fld->Name) ? $fld->Name : '';
                        $field->name = isset($fld->Name) ? $fld->Name : '';
                        $finalLists[] = $field;
                     }
                  }
               }
            }
            //error_log(json_encode($finalLists));
            $theData['fields'] = $finalLists;
            return json_encode($theData);
         }else{
            return false;
         }

      }

   }

}
?>