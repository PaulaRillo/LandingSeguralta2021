<?php
if ( ! class_exists( 'BravePop_Mailchimp_Advanced' ) ) {
   
   class BravePop_Mailchimp_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['mailchimp']->api)  ? $integrations['mailchimp']->api  : '';
         $this->dc = substr($this->api_key,strpos($this->api_key,'-')+1); 
      }


      public function get_fields($list_id='',  $apiKey='' ){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         $dc      = $apiKey ?substr($apiKey,strpos($apiKey,'-')+1) : $this->dc;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}
         if(!$list_id){ return error_log('Mailchimp List ID MISSING!!!!!'); }
         
         $theData = array('fields'=>array(), 'tags' => array());

         $args = array(
            'headers' => array(
               'Authorization' => 'Basic ' . base64_encode( 'user:'.  $apiKey )
            )
         );
  
         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://'.$dc.'.api.mailchimp.com/3.0/lists/'.$list_id.'/merge-fields?count=200', $args );
         //error_log(json_encode($fieldsResponse));
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            if($fieldsData && isset($fieldsData->merge_fields)){
               $fields = $fieldsData->merge_fields;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->tag) ? $field->tag : '';
                     $fieldItem->name = isset($field->name) ? $field->name : '';
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