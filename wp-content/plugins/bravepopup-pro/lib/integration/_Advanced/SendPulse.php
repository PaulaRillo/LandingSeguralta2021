<?php
if ( ! class_exists( 'BravePop_SendPulse_Advanced' ) ) {
   
   class BravePop_SendPulse_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['sendpulse']->api)  ? $integrations['sendpulse']->api  : '';
         $this->api_secret = isset($integrations['sendpulse']->secret)  ? $integrations['sendpulse']->secret  : '';
      }

      public function get_access_token($api_key='', $api_secret=''){
         if(!$api_key && !$api_secret){  return error_log('Sendpulse Refresh Token Missing!'); }
         $access_args = array('grant_type'=>'client_credentials', 'client_id'=> $api_key, 'client_secret'=>$api_secret);
         $args = array( 'method' => 'POST','headers' => array( 'Content-Type' => 'application/json' ), 'body' => json_encode($access_args)  );
         $response = wp_remote_post( 'https://api.sendpulse.com/oauth/access_token', $args );
         $body = wp_remote_retrieve_body( $response );
         $data = json_decode( $body );
         
         return isset($data->access_token) ? $data->access_token : '';
      }

      public function get_fields($list_id='', $apiKey='', $clientID=''){
         $api_key  = $api_key ? $api_key : $this->api_key;
         $api_secret  = $api_secret ? $api_secret : $this->api_secret;
         if(!$list_id){  return error_log('Sendpulse List ID Missing!');  }
         if(!$api_key && !$api_secret){  return error_log('Sendpulse API Key/Secret Missing!');  }

         $access_token  = $this->get_access_token($api_key, $api_secret); 
         if(!$access_token){ return error_log('Sendpulse access_token could not be generated!'); }
         
         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $args = array( 'headers' => array(  'Authorization' => 'Bearer ' . $access_token ) );

         $fieldsResponse = wp_remote_get( 'https://api.sendpulse.com/addressbooks/'.$list_id.'/variables', $args );

         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
            error_log(json_encode($fieldsResponse));
            if($fieldsData && isset($fieldsData)){
               $fields = $fieldsData;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->Key) ? $field->Key : '';
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