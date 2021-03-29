<?php

if ( ! class_exists( 'BravePop_Element_Search' ) ) {
   

   class BravePop_Element_Search {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
      }

      public function render_css() { 

         $fontFamily = isset($this->data->fontFamily) && $this->data->fontFamily !== 'None' ?  'font-family: '.$this->data->fontFamily.';' : '';
         $borderRadius =  isset($this->data->roundNess) ?  'border-radius: '.$this->data->roundNess.'px;' : '';

         //Input
         //$inputWidth = isset(this.state.buttonWidth)width: `calc(100% - ${this.state.buttonWidth}px)`,
         $inputFontSize = isset($this->data->inputFontSize) ?   'font-size: '.$this->data->inputFontSize.'px;' : '';
         $inputColorRGB = isset($this->data->inputColor) && isset($this->data->inputColor->rgb) ? $this->data->inputColor->rgb :'0,0,0';
         $inputColorOpacity = isset($this->data->inputColor) && isset($this->data->inputColor->opacity) ? $this->data->inputColor->opacity :'1';
         $inputColor = 'color: rgba('.$inputColorRGB.', '.$inputColorOpacity.');';

         $backgroundColorRGB = isset($this->data->backgroundColor) && isset($this->data->backgroundColor->rgb) ? $this->data->backgroundColor->rgb :'255,255,255';
         $backgroundColorOpacity = isset($this->data->backgroundColor) && isset($this->data->backgroundColor->opacity) ? $this->data->backgroundColor->opacity :'1';
         $backgroundColor = 'background-color: rgba('.$backgroundColorRGB.', '.$backgroundColorOpacity.');';

         $borderColorRGB = isset($this->data->borderColor) && isset($this->data->borderColor->rgb) ? $this->data->borderColor->rgb :'0,0,0';
         $borderColorOpacity = isset($this->data->borderColor) && isset($this->data->borderColor->opacity) ? $this->data->borderColor->opacity :'0.1';
         $borderColor = 'border-color: rgba('.$borderColorRGB.', '.$borderColorOpacity.');';


         //Button
         $buttonWidth = isset($this->data->buttonWidth) ?   'width: '.$this->data->buttonWidth.'px;' : 'width: 100px;';
         $buttonFontSize = isset($this->data->buttonFontSize) ?   'font-size: '.$this->data->buttonFontSize.'px;' : '';
         $buttonTextColorRGB = isset($this->data->buttonTextColor) && isset($this->data->buttonTextColor->rgb) ? $this->data->buttonTextColor->rgb :'255,255,255';
         $buttonTextColorOpacity = isset($this->data->buttonTextColor) && isset($this->data->buttonTextColor->opacity) ? $this->data->buttonTextColor->opacity :'1';
         $buttonTextColor = 'color: rgba('.$buttonTextColorRGB.', '.$buttonTextColorOpacity.');';
         $buttonBgColorRGB = isset($this->data->buttonBgColor) && isset($this->data->buttonBgColor->rgb) ? $this->data->buttonBgColor->rgb :'109, 120, 216';
         $buttonBgColorOpacity = isset($this->data->buttonBgColor) && isset($this->data->buttonBgColor->opacity) ? $this->data->buttonBgColor->opacity :'1';
         $buttonBgColor = 'background-color: rgba('.$buttonBgColorRGB.', '.$buttonBgColorOpacity.');';
         $iconColor = isset($this->data->icon) && $this->data->icon === false  ? '': 'fill: rgba('.$buttonTextColorRGB.', '.$buttonTextColorOpacity.');';

         $elementInnerStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_element__styler{'. $fontFamily . $borderRadius .'}';

         $elementInputStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' input{
            '. $inputFontSize .  $inputColor .  $backgroundColor .   $borderColor  .
         '}';

         $elementButtonStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' button{'. $buttonWidth . $buttonFontSize . $buttonTextColor .  $buttonBgColor . $borderRadius. '}';
         $elementButtonIconStyle = isset($this->data->icon) && $this->data->icon === false  ? '': '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' svg{'. $iconColor .'}';


         return  $elementInnerStyle . $elementInputStyle . $elementButtonStyle . $elementButtonIconStyle;

      }

      public function get_home_url(){
         $homeURL = esc_url( home_url( '/' ) );
         if(function_exists('pll_current_language')){
            $homeURL = pll_home_url();
         }
         if( class_exists( 'SitePress' )){
            $homeURL = apply_filters( 'wpml_home_url', $homeURL );
         }
         return $homeURL;
      }


      public function render( ) { 
         $iconSize = isset($this->data->buttonFontSize) ? $this->data->buttonFontSize : 13; 
         $searchIcon = isset($this->data->icon) && $this->data->icon === false  ? '':  '<svg xmlns="http://www.w3.org/2000/svg" width="'.$iconSize.'" height="'.$iconSize.'" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg> ';
         $placeholder = isset($this->data->placeholder) ? $this->data->placeholder : 'Search Posts';
         $buttonText = isset($this->data->buttonText) ? $this->data->buttonText : 'Search';
         $homeURL = $this->get_home_url();

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--search">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_element__search_inner">
                              <div class="brave_wpSearch__wrap">
                                 <form role="search" method="get" action="' . $homeURL . '">
                                    <input id="brave_search_input-'.$this->data->id.'" type="search" placeholder="'.$placeholder.'" name="s" value="' . get_search_query() . '" /> 
                                    <button id="brave_search_button-'.$this->data->id.'">'.$searchIcon.''.$buttonText.'</button>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>