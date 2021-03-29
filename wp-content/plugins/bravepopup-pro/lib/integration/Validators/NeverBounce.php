<?php
if ( ! class_exists( 'BravePop_NeverBounce' ) ) {
   
   class BravePop_NeverBounce {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['neverbounce']->api)  ? $integrations['neverbounce']->api  : '';
         $this->suggestionOpt= true;
      }

      public function validate_email($email, $apiKey=''){
         if(!$email){ return null; }
         if(!$this->api_key && !$apiKey){    return false; }
         $APIKEY = $apiKey ? $apiKey : $this->api_key;
         $response = wp_remote_post( 'https://api.neverbounce.com/v4/single/check?key='.$APIKEY.'&email='.$email );

         $body = wp_remote_retrieve_body( $response );
         $data = json_decode( $body );
         //error_log('BravePop_NeverBounce Response: '.json_encode($response));

         if($data && isset($data->status) && $data->status === 'success'){
            $result = array();

            if($data->result === 'invalid'){
               $result['status'] = 'invalid';
            }else{ 
               $result['status'] = 'valid';
            }
            if($data->result === 'invalid'){
               $result['errorMsg'] =__('This Email is Inactive','bravepop');
            }
            if($data->result === 'disposable'){
               $result['disposable'] = true;
               $result['errorMsg'] =__('Disposable Email not Allowed','bravepop');
            }
            if($data->suggested_correction && $data->result === 'invalid' && $this->suggestionOpt){
               $result['suggestion'] = $data->suggested_correction;
               $result['suggestionMsg'] =__('Did you mean ','bravepop').$data->suggested_correction.' ?';
            }
            return $result; 
         }else{
            return false;
         }

      }
   }
}
?>