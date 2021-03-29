<?php
if ( ! class_exists( 'BravePop_ConvertKit_Advanced' ) ) {
   
   class BravePop_ConvertKit_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['convertkit']->api)  ? $integrations['convertkit']->api  : '';
      }


      public function get_fields($apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         if(!$apiKey){ return error_log('API KEY MISSING!!!!!');}
         $args = array(
            'method' => 'GET',
            'user-agent'  => 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)',
            'headers' => array(
               'content-type' => 'application/json',
               'accept-encoding'=> '', //Without Specifying empty accept-encoding convertkit sends compressed data which breaks the response.
            ),
         );

         $theData = array('fields'=>array(), 'tags' => array());

         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://api.convertkit.com/v3/custom_fields?api_key='.$apiKey, $args );
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
   
            if($fieldsData && isset($fieldsData->custom_fields)){
               $fields = $fieldsData->custom_fields;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->key) ? $field->key : '';
                     $fieldItem->name = isset($field->label) ? $field->label : '';
                     $finalFields[] = $fieldItem;
                  }
               }
               //error_log(json_encode($finalLists));
               $theData['fields'] = $finalFields;
            }
         }

         //Tags Request
         $tagsResponse = wp_remote_get( 'https://api.convertkit.com/v3/tags?api_key='.$apiKey, $args );
         if( !is_wp_error( $tagsResponse ) ) {
            $tagsBody = wp_remote_retrieve_body( $tagsResponse );
            $tagsData = json_decode( $tagsBody );
   
            if($tagsData && isset($tagsData->tags)){
               $tags = $tagsData->tags;
               $finalTags = array();
               if($tags && is_array($tags)){
                  foreach ($tags as $key => $tag) {
                     $tagItem = new stdClass();
                     if(isset($tag->id) ){
                        $tagItem->id = isset($tag->id) ? $tag->id : '';
                        $tagItem->name = isset($tag->name) ? $tag->name : '';
                        $finalTags[] = $tagItem;
                     }
                  }
               }
               //error_log(json_encode($finalLists));
               $theData['tags'] = $finalTags;
            }
         }

         return json_encode($theData);

      }


   }

}
?>