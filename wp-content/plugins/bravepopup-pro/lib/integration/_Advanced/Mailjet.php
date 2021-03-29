<?php
if ( ! class_exists( 'BravePop_Mailjet_Advanced' ) ) {
   
   class BravePop_Mailjet_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['mailjet']->api)  ? $integrations['mailjet']->api  : '';
         $this->secret = isset($integrations['mailjet']->secret)  ? $integrations['mailjet']->secret  : '';

      }

      public function add_fields($customFields, $contactID){
         error_log('MailJet add_fields!!!');
         if(!$this->api_key || !$this->secret){ return error_log('API KEY/SECRET MISSING!!!!!');}

         //Add Custom Field Values
         $fieldValues = array();
         foreach ($customFields as $key => $value) {
            $fieldValues[] = array('Name'=> $key, 'Value' => $value);
         }

         $cfargs = array(
            'method' => 'PUT',
            'headers' => array(
               'content-type' => 'application/json',
               'Authorization' => 'Basic ' . base64_encode( $this->api_key.':'.$this->secret )
            ),
            'body' => json_encode(array( 'Data' => $fieldValues ))
         );

         //https://dev.mailjet.com/email/reference/contacts/contact-properties#v3_put_contactdata_contact_ID
         $cfresponse = wp_remote_post( 'https://api.mailjet.com/v3/REST/contactdata/' . $contactID, $cfargs );
         $cfbody = wp_remote_retrieve_body( $cfresponse );
         $cfdata = json_decode( $cfbody );

         //error_log(json_encode($cfresponse));
         if($cfdata && isset($cfdata->Data)){
            return $cfdata->Data; 
         }else{
            return false;
         }

      }

      public function get_fields($apiKey='', $secretKey=''){
         $apiKey     = $apiKey ? $apiKey : $this->api_key;
         $secretKey  = $secretKey ? $secretKey : $this->secret;
         $theData = array('fields'=>array(), 'tags' => array());
         if(!$apiKey || !$secretKey){ return error_log('API KEY/SECRET MISSING!!!!!');}

         $args = array(
            'headers' => array(
               'Authorization' => 'Basic ' . base64_encode( $apiKey.':'.$secretKey )
            )
         );
         //https://dev.mailjet.com/email/reference/contacts/contact-properties/
         $response = wp_remote_get( 'https://api.mailjet.com/v3/REST/contactmetadata', $args );
         if( is_wp_error( $response ) ) {
            return false; // Bail early
         }

         $body = wp_remote_retrieve_body( $response );
         $data = json_decode( $body );
         //error_log($body);
         if($data && isset($data->Data)){
            $lists = $data->Data;
            $finalLists = array();
            if($lists && is_array($lists)){
               foreach ($lists as $key => $list) {
                  $listItem = new stdClass();
                  $listItem->id = isset($list->Name) ? $list->Name : ''; //Since Mailjet requires Name field for updating contact instead of ID.
                  $listItem->name = isset($list->Name) ? $list->Name : '';
                  $listItem->count = isset($list->SubscriberCount)  ? $list->SubscriberCount : 0;
                  $finalLists[] = $listItem;
               }
            }
            $theData['fields'] = $finalLists;
            return json_encode($theData);
         }else{
            return false;
         }

      }


   }

}
?>