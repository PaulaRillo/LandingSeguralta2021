<?php
if ( ! class_exists( 'BravePop_ActiveCampaign_Advanced' ) ) {
   
   class BravePop_ActiveCampaign_Advanced {

      function __construct() {

         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->api_key = isset($integrations['activecampaign']->api)  ? $integrations['activecampaign']->api  : '';
         $this->api_url = isset($integrations['activecampaign']->url)  ? $integrations['activecampaign']->url  : '';
      }


      public function get_fields($apiURL='', $apiKey=''){
         $apiKey  = $apiKey ? $apiKey : $this->api_key;
         $apiURL  = $apiURL ? $apiURL : $this->api_url;
         if(!$apiURL || !$apiKey){ return error_log('API KEY/URL MISSING!!!!!');}

         $theData = array('fields'=>array(), 'tags' => array());
         $args = array(
            'headers' => array(
               'Api-Token' => $apiKey
            )
         );

         //Fields Request
         $fieldsResponse = wp_remote_get( $apiURL.'/api/3/fields?limit=100', $args ); 
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            if($fieldsData && isset($fieldsData->fields)){
               $fields = $fieldsData->fields;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->id) ? $field->id : '';
                     $fieldItem->name = isset($field->title) ? $field->title : '';
                     $finalFields[] = $fieldItem;
                  }
               }
               //error_log(json_encode($finalLists));
               $theData['fields'] = $finalFields;
            }
         }

         //Tags Request
         $tagsResponse = wp_remote_get( $apiURL.'/api/3/tags?limit=100', $args );
         if( !is_wp_error( $tagsResponse ) ) {
            $tagsBody = wp_remote_retrieve_body( $tagsResponse );
            $tagsData = json_decode( $tagsBody );
   
            if($tagsData && isset($tagsData->tags)){
               $tags = $tagsData->tags;
               $finalTags = array();
               if($tags && is_array($tags)){
                  foreach ($tags as $key => $tag) {
                     $tagItem = new stdClass();
                     if(isset($tag->tagType) && $tag->tagType === 'contact'){
                        $tagItem->id = isset($tag->id) ? $tag->id : '';
                        $tagItem->name = isset($tag->tag) ? $tag->tag : '';
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