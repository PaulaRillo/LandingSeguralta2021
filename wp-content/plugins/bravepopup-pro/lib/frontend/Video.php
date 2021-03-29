<?php

if ( ! class_exists( 'BravePop_Element_Video' ) ) {
   

   class BravePop_Element_Video {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
      }

      public function render_js() { ?>
         <script>

         </script>

      <?php }


      public function renderYoutubeVimeo() { 
         $id = $this->data->id; 
         $videoType = isset($this->data->videoType) ? $this->data->videoType : 'youtube'; 
         $videoUrl = isset($this->data->videoUrl) ? $this->data->videoUrl : 'https://www.youtube.com/watch?v=07d2dXHYb94'; 
         $controls = isset($this->data->controls) ? $this->data->controls : true; 
         $controls = $controls === true ? 1 : 0; 
         $autoPlay= isset($this->data->autoplay) && $this->data->autoplay === true ? 1 : 0; 
         $mute= isset($this->data->mute) && $this->data->mute == true ? 1 : 0; 
         $url = ''; 
         $videoID = '';
         $vidID = preg_replace('/[^a-zA-Z0-9]+/', '', $this->data->id);

         if($videoType ==='vimeo'){
            $vimeo_regex = '/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/';
            preg_match($vimeo_regex, $videoUrl, $match);
            $videoID = $match[3];
            $url = 'https://player.vimeo.com/video/'.$videoID.'?autoplay='.$autoPlay.'&autopause=0&muted='.$mute.'';
            
         }
         if($videoType ==='youtube'){
            $youtube_regex = '/^.*(youtu\.be\/|vi?\/|u\/\w\/|embed\/|\?vi?=|\\&vi?=)([^#\\&\\?]*).*/';
            preg_match($youtube_regex, $videoUrl, $match);
            $videoID = $match[2];
            
            $url = 'https://www.youtube.com/embed/'.$videoID.'?autoplay='.$autoPlay.'&loop=0&controls='.$controls.'&mute='.$mute.'&enablejsapi=true';
         }

         return '<div id="brave_video_iframe'.$vidID.'" class="brave_video_iframe"></div>';
      }

      public function renderCustomVideo() { 
         $id = $this->data->id; 
         $background= isset($this->data->background) ? $this->data->background : ''; 
         $customVideo= isset($this->data->customVideo) ? $this->data->customVideo : '';  
         $ccontrols= isset($this->data->controls) ? $this->data->controls : true; 
         $cautoPlay= isset($this->data->autoplay) ? $this->data->autoplay : false; 
         $cmute= isset($this->data->mute) ? $this->data->mute : false; 
         
         $playerControls = $background ? false: $ccontrols;
         $playerControls = $playerControls ? 'controls' : '';
         $playerLoop = $background ? true: false;
         $playerLoop = $playerLoop ? 'loop' : '';
         $playerAutoPlay = $background ? '' : '';
         $playerMuted = ($cmute || $background) ? 'muted' : '';
         $autoPlayClass = $background ? ' brave_video_autoplay' : '';
         $mutedClass = ($playerMuted || $background) ? ' brave_video_muted' : '';
         $backgroundClass = $background ? ' brave_video_custom--background ' : '';

         return '<video id="brave_video_custom_'.$id.'" class="brave_video_custom '.$mutedClass.$backgroundClass.$autoPlayClass.'" '.$playerControls.' '.$playerAutoPlay.' '.$playerLoop.' '.$playerMuted.'>
                     <source src="'.$customVideo.'"></source>
                  </video>';
 
      }
      
      
      public function render_css() { 

         $iconColorRGB = isset($this->data->iconColor) && isset($this->data->iconColor->rgb) ? $this->data->iconColor->rgb :'0,0,0';
         $iconColorOpacity = isset($this->data->iconColor) && isset($this->data->iconColor->opacity) ? $this->data->iconColor->opacity :'1';
         $iconColor = 'fill: rgba('.$iconColorRGB.', '.$iconColorOpacity.');';
         $iconShadowStyle = '';

         if( isset($this->data->iconShadow) && $this->data->iconShadow === true){
            $iconShadowColorRGB = isset($this->data->iconShadowColor) && isset($this->data->iconShadowColor->rgb) ? $this->data->iconShadowColor->rgb :'0,0,0';
            $iconShadowColorOpacity = isset($this->data->iconShadowColor) && isset($this->data->iconShadowColor->opacity) ? $this->data->iconShadowColor->opacity :'1';
            $iconShadowStyle = 'filter: drop-shadow(0 0 20px rgba('.$iconShadowColorRGB.', '.$iconShadowColorOpacity.'));';
         }

         $thumbnailStyle =  isset($this->data->customImage) ? 'background-image: url('.$this->data->customImage.')' :'';
         $iconSize = isset($this->data->iconSize) ? 'width: '.$this->data->iconSize.'px;' :'';

         $elementInnerStyle = $thumbnailStyle ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_video_thumbnail{ '. $thumbnailStyle . '}' : '';
         $elementplayBtnStyle = isset($this->data->customImage) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_video_playBtn{ '. $iconColor . $iconSize . $iconShadowStyle .'}' : '';

         return $elementInnerStyle .$elementplayBtnStyle;

      }


      public function render( ) { 
         $videoHTML = '';
         $videoType = isset($this->data->videoType) ? $this->data->videoType : 'youtube'; 
         $tracking = isset($this->data->action->track) && isset($this->data->action->trackData) && $this->data->action->track ===true ? $this->data->action->trackData : null;
         $trackingCat = $tracking && isset($tracking->eventCategory) ? $tracking->eventCategory : '';
         $trackingAction = $tracking && isset($tracking->eventAction) ? $tracking->eventAction : '';
         $trackingLabel = $tracking && isset($tracking->eventLabel) ? $tracking->eventLabel : '';
         $clickAction = 'onclick="brave_play_video('.$this->popupID.', \''.$this->data->id.'\', \''.$videoType.'\', null, true);"';
         $overlay = '<div class="brave_video_element_overlay"></div>';
         $playIcon = !empty($this->data->customImage) ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>' : '';
         $customImage = '<div class="brave_video_thumbnail" '.((!isset($this->data->customImage) || !$this->data->customImage) ? $clickAction : '').'><div id="brave_play_video-'.$this->data->id.'" '.$clickAction.' class="brave_video_playBtn" data-trackcategory="'.$trackingCat.'"  data-trackaction="'.$trackingAction.'"  data-tracklabel="'.$trackingLabel.'">'.$playIcon.'</div></div>';
         $videoHTML = isset($this->data->videoType) && $this->data->videoType !== 'custom' ? $this->renderYoutubeVimeo() : $this->renderCustomVideo();

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--video">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                              '.$overlay . $customImage . $videoHTML.'
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>