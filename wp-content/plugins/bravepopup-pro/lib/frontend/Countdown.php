<?php

if ( ! class_exists( 'BravePop_Element_Countdown' ) ) {
   

   class BravePop_Element_Countdown {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex, $device) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
         $this->device = $device;
      }
      
      public function render_js() { ?>
         <script>
            <?php if(isset($this->data->timerType)) { ?>
               document.addEventListener("DOMContentLoaded", function(event) {
                  console.log('Countdown JS Loaded!!!');
                  <?php 
                     $elemid =  esc_html($this->data->id);
                     $customTime = isset($this->data->customTime) ? json_encode($this->data->customTime) : '';
                     $customDate = isset($this->data->customTime) && isset($this->data->customTime->date) ? $this->data->customTime->date : '';
                     $customHour = isset($this->data->customTime) && isset($this->data->customTime->time) && isset($this->data->customTime->time->hour) ? $this->data->customTime->time->hour : '';
                     $customMins = isset($this->data->customTime) && isset($this->data->customTime->time) && isset($this->data->customTime->time->minutes) ? $this->data->customTime->time->minutes : '';
                     $hideDays = isset($this->data->hideDays) ? json_encode($this->data->hideDays) : 'false';
                     $autoTimerRepeat = !empty($this->data->autoTimeRepeat) ? absint($this->data->autoTimeRepeat) : false;                
                     //$autoData = isset($this->data->timerType) && $this->data->timerType === 'auto' ? '{autoTime:'.absint($this->data->autoTime).', autoTimeType: \''.$this->data->autoTimeType.'\' }' : 'false';

                     if($this->data->timerType === 'auto'&& isset($this->data->autoTime) && isset($this->data->autoTimeType) ){
                        $autoVar = preg_replace('/[^a-zA-Z0-9]+/', '', $this->data->id); 
                        $autoVar = $autoVar.'__'.absint($this->data->autoTime).'__';
                        print_r($autoVar.'brave_endDate =  false;');

                        $multiplier = (86400000 * 30);

                        if(($this->data->autoTimeType=== 'month')){ $multiplier = (86400000 * 30); }
                        if(($this->data->autoTimeType=== 'day')){ $multiplier = 86400000; }
                        if(($this->data->autoTimeType=== 'hour')){ $multiplier = 3600000; }
                        if(($this->data->autoTimeType=== 'minute')){ $multiplier = 60000; }

                        print_r($autoVar.'brave_endDate =  new Date(+new Date() + ('.absint($this->data->autoTime).' *'.$multiplier.') );');

                        print_r('if(localStorage.getItem("'.$autoVar.'brave_endDate")){ '.$autoVar.'brave_endDate = new Date(localStorage.getItem("'.$autoVar.'brave_endDate"));  }else{  localStorage.setItem("'.$autoVar.'brave_endDate", '.$autoVar.'brave_endDate); }');

                        //If Evergreen timer is set to repeat 
                        if(($autoTimerRepeat)){
                           $autoRepeatMultiplier = $autoTimerRepeat === 1 ? 1 : 3600000;
                           $autoRepeatJS = 'if(new Date('.$autoVar.'brave_endDate).getTime() < new Date().getTime()){';
                              $autoRepeatJS .= 'if(new Date().getTime() > new Date(new Date('.$autoVar.'brave_endDate).getTime() + ('.absint($autoTimerRepeat).' * '.$autoRepeatMultiplier.' )).getTime()){';
                                 $autoRepeatJS .= 'console.log("Repeat Evergreen Timer!!!!!!!!");';
                                 $autoRepeatJS .= $autoVar.'brave_endDate =  new Date(+new Date() + ('.absint($this->data->autoTime).' *'.$multiplier.') );';
                                 $autoRepeatJS .= 'localStorage.setItem("'.$autoVar.'brave_endDate", '.$autoVar.'brave_endDate);';
                              $autoRepeatJS .= '}';
                           $autoRepeatJS .= '}';
                           print_r($autoRepeatJS);
                        }

                        print_r("brave_popup_data[{$this->popupID}].timers.push({ device:'{$this->device}', step: {$this->stepIndex}, ended: new Date().getTime() > {$autoVar}brave_endDate.getTime() })");
                     }
                   ?>
                  
                  <?php if($this->data->timerType !== 'auto' && $customDate){ ?>
                     <?php $theEndDate = explode('/', $customDate); ?>
                     brave_popup_data[<?php print_r($this->popupID);?>].timers.push({device: '<?php print_r($this->device);?>', step:<?php print_r($this->stepIndex);?>, ended: new Date().getTime() > new Date('<?php print_r("{$theEndDate[1]}/{$theEndDate[0]}/{$theEndDate[2]} {$customHour}:{$customMins}:00");?>').getTime() });
                  <?php } ?>

                  setInterval("brave_countdown('<?php print_r($elemid); ?>', '<?php print_r($customDate); ?>', '<?php print_r($customHour); ?>', '<?php print_r($customMins); ?>', <?php print_r($hideDays); ?>, <?php print_r(isset($autoVar) ? $autoVar.'brave_endDate' : false); ?>)", 1000);
               });
            <?php } ?>
         </script>

      <?php }
      
      public function render_css() { 

         $timerColorRGB = isset($this->data->timerColor) && isset($this->data->timerColor->rgb) ? $this->data->timerColor->rgb :'0,0,0';
         $timerColorOpacity = isset($this->data->timerColor) && isset($this->data->timerColor->opacity) ? $this->data->timerColor->opacity :'1';
         $timerColor = 'color: rgba('.$timerColorRGB.', '.$timerColorOpacity.');';

         $timerBackgroundRGB = isset($this->data->timerBackground) && isset($this->data->timerBackground->rgb) ? $this->data->timerBackground->rgb :'0,0,0';
         $timerBackgroundOpacity = isset($this->data->timerBackground) && isset($this->data->timerBackground->opacity) ? $this->data->timerBackground->opacity :'1';
         $timerBackground = 'background-color: rgba('.$timerBackgroundRGB.', '.$timerBackgroundOpacity.');';
         $timerTextShadow = isset($this->data->timerGlow) && $this->data->timerGlow === true ? 'text-shadow: 0 0 20px rgba('.$timerBackgroundRGB.', '.$timerBackgroundOpacity.');': '';

         $labelColorRGB = isset($this->data->labelColor) && isset($this->data->labelColor->rgb) ? $this->data->labelColor->rgb :'0,0,0';
         $labelColorOpacity = isset($this->data->labelColor) && isset($this->data->labelColor->opacity) ? $this->data->labelColor->opacity :'1';
         $labelColor = 'color: rgba('.$labelColorRGB.', '.$labelColorOpacity.');';
         
         $labelBackgroundRGB = isset($this->data->labelBackground) && isset($this->data->labelBackground->rgb) ? $this->data->labelBackground->rgb :'0,0,0';
         $labelBackgroundOpacity = isset($this->data->labelBackground) && isset($this->data->labelBackground->opacity) ? $this->data->labelBackground->opacity :'1';
         $labelBackground = 'background-color: rgba('.$labelBackgroundRGB.', '.$labelBackgroundOpacity.');';


         $labelSize = isset($this->data->labelSize) ?  'font-size: '.$this->data->labelSize.'px;' : '';
         $labelWeight = isset($this->data->labelBold) && $this->data->labelBold === true ?  'font-weight: bold;' : '';

         $timerFont = isset($this->data->fontFamily) && $this->data->fontFamily !== 'None' ?  'font-family: '.$this->data->fontFamily.';' : '';
         $timerSize = isset($this->data->timerSize) ?  'font-size: '.$this->data->timerSize.'px;' : '';
         $timerLineHeight = isset($this->data->timerSize) ?  'line-height: '.$this->data->timerSize.'px;' : '';


         $elementTimeStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_countdown_time{
            ' . $timerFont . $timerSize . $timerColor .
         '}';

         $elementTimeBGStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_countdown__remaining{
            ' . $timerTextShadow . $timerBackground . $timerLineHeight. 
         '}';

         $elementLabelStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_countdown__label{
            ' . $labelSize . $labelWeight . $labelColor . $labelBackground .
         '}';

         return $elementTimeBGStyle . $elementTimeStyle . $elementLabelStyle;

      }


      public function render( ) { 
         $hideDays = isset($this->data->hideDays) ? $this->data->hideDays : false;
         $labelPosition = isset($this->data->labelPosition) ? $this->data->labelPosition : 'bottom';
         $stampsVerb = array(__('days', 'bravepop'), __('hours', 'bravepop'), __('minutes', 'bravepop'), __('seconds', 'bravepop'));
         $stamps = array('days', 'hours','minutes', 'seconds');
         $remaining = array();
         $dateHTML = '';

         //sprintf( _n( 'Day', 'Days', $remaining[$stamp], 'bravepop' ), $remaining[$stamp] )

         foreach ($stamps as $index => $stamp) {
            $dateHTML .= '<div class="brave_countdown_time brave_countdown_time--'.$stamp.'">';
            $dateHTML .= $labelPosition === 'top' ? '<div class="brave_countdown__label brave_countdown__label--'.$stamp.'">'.$stampsVerb[$index].'</div>' : '';
            $dateHTML .= '<span id="brave_rem_'.$stamp.'-'.$this->data->id.'" class="brave_countdown__remaining">10</span>';
            $dateHTML .= $labelPosition !== 'top' ? '<div class="brave_countdown__label brave_countdown__label--'.$stamp.'">'.$stampsVerb[$index].'</div>' : '';
            $dateHTML .= '</div>';
         }

         // hideDays
         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--countdown">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_countdown_wrap brave_countdown_wrap--'.$labelPosition.' '.($hideDays ? 'brave_countdown_wrap--hideDays' :'').'">
                              '.$dateHTML.'
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>