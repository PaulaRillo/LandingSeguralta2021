<?php

if ( ! class_exists( 'BravePop_Element_Sticker' ) ) {
   

   class BravePop_Element_Sticker {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex, $device='desktop', $goalItem=false, $dynamicData) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
         $this->goalItem = $goalItem;
         $this->dynamicData = $dynamicData;
      }

      
      public function render_css() { 

         $flip = isset($this->data->flip) && $this->data->flip === true ? 'transform: scaleX(-1);' : '';
         $aspectRatio = !isset($this->data->aspectRatio) ? true : $this->data->aspectRatio;
         $verticalPosition = isset($this->data->verticalPosition) ? $this->data->verticalPosition : 50;
         $horizontalPosition = isset($this->data->horizontalPosition) ? $this->data->horizontalPosition : 0;
         $objectPosition = $aspectRatio === false ? 'object-position: '.$horizontalPosition.' '.$verticalPosition.'%;' : '';
         $scale = isset($this->data->size) && $this->data->size > 1 ? 'transform: scale('.$this->data->size.');':'';
         $contrast = isset($this->data->contrast)  ? 'contrast('.$this->data->contrast.'%)'  : '';
         $brightness = isset($this->data->brightness) ? 'brightness('.$this->data->brightness.'%)' : '';
         $grayscale = isset($this->data->grayscale) && $this->data->grayscale === true  ? 'grayscale(100%)' : '';
         $blur = isset($this->data->blur)  ? 'blur('.$this->data->blur.'px);' : '';
         $filter = ($contrast || $grayscale || $brightness || $blur) ? 'filter: '.$contrast.' '.$grayscale.' '.$brightness.' '.$blur. ';' : '';


         $elementInnerStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_element__styler{ '. $flip . '}';
      
         $elementImageStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' img{ '. $objectPosition . $filter . $scale . '}';
         

         return  $elementInnerStyle . $elementImageStyle;

      }

      public function clickable_html( ) { 
         $clickable = isset($this->data->clickable) ? $this->data->clickable : false;
         $actionType = isset($this->data->action->type) ? $this->data->action->type : 'none';
         $track = isset($this->data->action->track) ? $this->data->action->track : false;
         $eventCategory = isset($this->data->action->trackData->eventCategory) ? $this->data->action->trackData->eventCategory : 'popup';
         $eventAction = isset($this->data->action->trackData->eventAction) ? $this->data->action->trackData->eventAction : 'click';
         $eventLabel = isset($this->data->action->trackData->eventLabel) ? $this->data->action->trackData->eventLabel : '';
         $actionTrack = ($actionType !== 'step' || $actionType !== 'close') && $track && $clickable ? ' onclick="brave_send_ga_event(\''.$eventCategory.'\', \''.$eventAction.'\', \''.$eventLabel.'\');"':'';
         $actionInlineTrack = ($actionType === 'step' || $actionType === 'close') && $track && $clickable ? ' brave_send_ga_event(\''.$eventCategory.'\', \''.$eventAction.'\', \''.$eventLabel.'\');':'';
         $goalAction = $this->goalItem ? 'brave_complete_goal('.$this->popupID.', \'click\');"':'';
         
         $actionJS = $actionType === 'javascript' && isset($this->data->action->actionData->javascript) ? 'onclick="'.$this->data->action->actionData->javascript.' '.$actionInlineTrack.' '.$goalAction.'"': '';
         $actionURL  = isset($this->data->action->actionData->url) ? $this->data->action->actionData->url : '';
         $actionPhone  = !empty($this->data->action->actionData->phone) ? $this->data->action->actionData->phone : '';
         $actionDownload = !empty($this->data->action->actionData->download) ? 'download': '';
         $actionNewWindow  = isset($this->data->action->actionData->new_window) ? $this->data->action->actionData->new_window : '';
         $actionNoFollow  = isset($this->data->action->actionData->nofollow) ? $this->data->action->actionData->nofollow : '';
         $actionStepNum  = isset($this->data->action->actionData->step) ? (Int)$this->data->action->actionData->step  - 1 : '';
         if(isset($this->data->action->actionData->dynamicURL)){
            $dynamicURL  = bravepopup_dynamicLink_data($this->data->action->actionData, $this->dynamicData, $this->data->id);
            if(isset($dynamicURL->link)){   $actionURL  =  $dynamicURL->link;   }
         }
         $actionLink = $clickable && ($actionType === 'url' || $actionType === 'dynamic') && $actionURL ? 'onclick="'.$goalAction.'" href="'.$actionURL.'" '.($actionNewWindow ? 'target="_blank"' : '').' '.($actionNoFollow ? 'rel="nofollow"' : '').'':'';
         $actionCall = ($actionType === 'call') && $actionPhone ? 'onclick="'.$goalAction.'" href="tel:'.$actionPhone.'"':'';
         $actionStep = $clickable && $actionType === 'step' && $actionStepNum >=0 ? 'onclick="brave_action_step('.$this->popupID.', '.$this->stepIndex.', '.$actionStepNum.'); '.$actionInlineTrack.' '.$goalAction.'"':'';
         $actionClose = $clickable && $actionType === 'close' ? 'onclick="brave_close_popup(\''.$this->popupID.'\', \''.$this->stepIndex.'\'); '.$actionInlineTrack.' '.$goalAction.'"':'';

         $html = new stdClass();
         $html->start = '<a class="brave_element__inner_link" '.$actionLink.' '.$actionCall.' '.$actionDownload.' '.$actionStep . $actionClose. $actionTrack.$actionJS.'>';
         $html->end = '</a>';

         return $html;
      }

      public function render( ) { 
         $sticker = isset($this->data->image) ? '<img class="brave_element__sticker brave_element_img_item skip-lazy no-lazyload" src="'.bravepop_get_preloader().'" data-lazy="'.$this->data->image.'" />' : '';

         $clickable = isset($this->data->clickable) ? $this->data->clickable : false;
         $clickableHTML = $this->clickable_html();
         $clickStart = $clickable && isset($clickableHTML->start) ? $clickableHTML->start : '';
         $clickEnd = $clickable && isset($clickableHTML->end) ? $clickableHTML->end : '';


         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--sticker '.$clickable.'">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           '.$clickStart.'
                              '.$sticker.'
                           '.$clickEnd.'
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>