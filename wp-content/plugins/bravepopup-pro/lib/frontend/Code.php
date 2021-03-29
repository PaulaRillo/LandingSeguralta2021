<?php

if ( ! class_exists( 'BravePop_Element_Code' ) ) {
   

   class BravePop_Element_Code {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex, $device='desktop', $goalItem=false) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
         $this->goalItem = $goalItem;
      }

      
      public function render_css() { 
         return '';
      }


      public function render( ) { 
         $code = isset($this->data->code) ? $this->data->code : '';

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--code">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_element__code '.($this->goalItem ? 'brave_element__code--goaled':'').'">
                              '.do_shortcode($code).'
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>