<?php
if ( ! class_exists( 'BravePop_CampaignMonitor_Advanced' ) ) {
   
   class BravePop_CampaignMonitor_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['campaignmonitor']->api)  ? $integrations['campaignmonitor']->api  : '';
         $this->clientID = isset($integrations['campaignmonitor']->secret)  ? $integrations['campaignmonitor']->secret  : '';
      }


      public function get_fields($list_id='',  $apiKey='', $clientID=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         $clientID  = $clientID ? $clientID : $this->clientID;
         if(!$list_id){ return error_log('CampaignMonitor List ID MISSING!!!!!');  }
         if(!$apiKey || !$clientID){ return error_log('CampaignMonitor API Key / Client ID MISSING!!!!!');  }
         
         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $args = array(
            'method' => 'GET',
            'headers' => array(  'content-type' => 'application/json', 'Authorization' => 'Basic '.base64_encode($apiKey.":x" ) ),
         );

         $fieldsResponse = wp_remote_get( 'https://api.createsend.com/api/v3.2/lists/'.$list_id.'/customfields.json', $args );

         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            if($fieldsData && isset($fieldsData)){
               $fields = $fieldsData;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->Key) ? str_replace(array( '[', ']' ), '', $field->Key) : '';
                     $fieldItem->name = isset($field->FieldName) ? $field->FieldName : '';
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