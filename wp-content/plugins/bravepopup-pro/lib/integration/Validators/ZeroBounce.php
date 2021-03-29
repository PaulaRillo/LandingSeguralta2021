<?php
if ( ! class_exists( 'BravePop_ZeroBounce' ) ) {
   
   class BravePop_ZeroBounce {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['zerobounce']->api)  ? $integrations['zerobounce']->api  : '';
         $this->suggestionOpt= true;
      }

      public function validate_email($email, $apiKey=''){
         if(!$email){ return null; }
         if(!$this->api_key && !$apiKey){    return false; }
         $APIKEY = $apiKey ? $apiKey : $this->api_key;

         $response = wp_remote_get( 'https://api.zerobounce.net/v2/validate?api_key='.$APIKEY.'&email='.urlencode($email).'&ip_address=' );

         $body = wp_remote_retrieve_body( $response );
         $data = json_decode( $body );
         //error_log('BravePop_ZeroBounce Response: '.json_encode($response));
         if($data && isset($data->status)){
            $result = array();

            if($data->status === 'invalid'){
               $result['status'] = 'invalid';
            }else{ 
               $result['status'] = 'valid';
            }
            if($data->status === 'invalid'){
               $result['errorMsg'] =__('This Email is Inactive','bravepop');
            }
            if($data->sub_status === 'disposable'){
               $result['disposable'] = true;
               $result['errorMsg'] =__('Disposable Email not Allowed','bravepop');
            }

            if($data->did_you_mean && $data->status === 'invalid' && $this->suggestionOpt){
               $result['suggestion'] = $data->did_you_mean;
               $result['suggestionMsg'] =__('Did you mean ','bravepop').$data->did_you_mean.' ?';
            }
            return $result; 
            return $data; 
         }else{
            return false;
         }
      }
   }
}
?>