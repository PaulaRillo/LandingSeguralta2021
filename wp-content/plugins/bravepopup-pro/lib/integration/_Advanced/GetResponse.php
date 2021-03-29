<?php
if ( ! class_exists( 'BravePop_GetResponse_Advanced' ) ) {
   
   class BravePop_GetResponse_Advanced {

      function __construct() {
         $braveSettings = get_option('_bravepopup_settings');
         $integrations = $braveSettings && isset($braveSettings['integrations']) ? $braveSettings['integrations'] : array() ;
         $this->access_key = isset($integrations['getresponse']->access)  ? $integrations['getresponse']->access  : '';
      }

      public function get_fields($accessKey=''){
         $accessKey = $accessKey ? $accessKey : $this->access_key;
         if(!$accessKey){ return error_log('ACCESS KEY MISSING!!!!!');}

         $args = array(
            'headers' => array(
               'Authorization' => 'Bearer ' . $accessKey
            )
         );

         $theData = array('fields'=>array(), 'tags' => array());
         
         //Fields Request
         $fieldsResponse = wp_remote_get( 'https://api.getresponse.com/v3/custom-fields', $args );
         if( !is_wp_error( $fieldsResponse ) ) {
            $fieldsBody = wp_remote_retrieve_body( $fieldsResponse );
            $fieldsData = json_decode( $fieldsBody );
   
            //error_log(json_encode($fieldsResponse));
            if($fieldsData ){
               $fields = $fieldsData;
               $finalFields = array();
               if($fields && is_array($fields)){
                  foreach ($fields as $key => $field) {
                     $fieldItem = new stdClass();
                     $fieldItem->id = isset($field->customFieldId) ? $field->customFieldId : '';
                     $fieldItem->name = isset($field->name) ? $field->name : '';
                     $fieldItem->type = isset($field->fieldType) ? $field->fieldType : '';
                     $finalFields[] = $fieldItem;
                  }
               }
               //error_log(json_encode($finalLists));
               $theData['fields'] = $finalFields;
            }
         }

         //Tags Request
         $tagsResponse = wp_remote_get( 'https://api.getresponse.com/v3/tags', $args );
         if( !is_wp_error( $tagsResponse ) ) {
            $tagsBody = wp_remote_retrieve_body( $tagsResponse );
            $tagsData = json_decode( $tagsBody );
   
            if($tagsData){
               $tags = $tagsData;
               $finalTags = array();
               if($tags && is_array($tags)){
                  foreach ($tags as $key => $tag) {
                     $tagItem = new stdClass();
                     if(isset($tag->tagId)){
                        $tagItem->id = isset($tag->tagId) ? $tag->tagId : '';
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