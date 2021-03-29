<?php

if ( ! class_exists( 'BravePop_Element_Dynamic' ) ) {
   

   class BravePop_Element_Dynamic {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
         $layout = !empty($this->data->layout) ? $this->data->layout  : 'slider';
         $itemPerSlide = isset($this->data->itemPerSlide) ? $this->data->itemPerSlide : 4;
         $this->slides = ($layout === 'news' || $layout === 'toggle') ? array_chunk($this->data->slides, $itemPerSlide) : $this->data->slides;
      }


      public function render_js() { ?>
         <script>
            <?php if(isset($this->data->autoSlide) && $this->data->autoSlide === true && isset($this->data->slides)) { ?>
               document.addEventListener("DOMContentLoaded", function(event) {
                  console.log('Slider JS Loaded!!!');
                  setInterval("brave_autochange_slide('<?php print_r(esc_attr($this->data->id)); ?>')", <?php print_r(isset($this->data->slideDuration) ? absint($this->data->slideDuration) * 1000 : 2000) ; ?>);
               });
            <?php } ?>
         </script>

      <?php }

      
      public function render_css() { 
         $layout = isset($this->data->layout) ?  $this->data->layout : 'slider';
         $width = isset($this->data->width) ?  'width: '.$this->data->width.'px;' : '';
         $sliderWidth = isset($this->slides) && count($this->slides) > 1 ? (count($this->slides) * $this->data->width).'px' : $this->data->width.'px';
         $showContent = (!isset($this->data->showContent) || (isset($this->data->showContent) && $this->data->showContent === true)) ? true : false;

            
         $fontFamily = isset($this->data->fontFamily) && $this->data->fontFamily !== 'None' ?  'font-family: '.$this->data->fontFamily.';' : '';
         $contentBoxWidth = isset($this->data->contentWidth) ?  'width: '.$this->data->contentWidth.'%;' : '';
         $contentBgColorRGB = isset($this->data->contentBgColor) && isset($this->data->contentBgColor->rgb) ? $this->data->contentBgColor->rgb :'';
         $contentBgColorOpacity = isset($this->data->contentBgColor) && isset($this->data->contentBgColor->opacity) ? $this->data->contentBgColor->opacity :'';
         $contentBgColor = $contentBgColorRGB ? 'background-color: rgba('.$contentBgColorRGB.', '.$contentBgColorOpacity.');' : '';

         //Title Style
         $titleColorRGB = isset($this->data->titleColor) && isset($this->data->titleColor->rgb) ? $this->data->titleColor->rgb :'64, 99, 215';
         $titleColorOpacity = isset($this->data->titleColor) && isset($this->data->titleColor->opacity) ? $this->data->titleColor->opacity :1;
         $titleColor = $titleColorRGB ? 'color: rgba('.$titleColorRGB.', '.$titleColorOpacity.');' : '';
         $ttFontSize = isset($this->data->titleFontSize) ?  'font-size: '.$this->data->titleFontSize.'px;' : '';

         //Description Style
         $descColorRGB = isset($this->data->descColor) && isset($this->data->descColor->rgb) ? $this->data->descColor->rgb :'168, 169, 188';
         $descColorOpacity = isset($this->data->descColor) && isset($this->data->descColor->opacity) ? $this->data->descColor->opacity :1;
         $descColor = $descColorRGB ? 'color: rgba('.$descColorRGB.', '.$descColorOpacity.');' : '';
         $descFontSize = isset($this->data->descFontSize) ?  'font-size: '.$this->data->descFontSize.'px;' : '';

         //Button Style
         $buttonFontSize = isset($this->data->buttonFontSize) ?  'font-size: '.$this->data->buttonFontSize.'px;' : '';
         $buttonColorRGB = isset($this->data->buttonColor) && isset($this->data->buttonColor->rgb) ? $this->data->buttonColor->rgb :'';
         $buttonColorOpacity = isset($this->data->buttonColor) && isset($this->data->buttonColor->opacity) ? $this->data->buttonColor->opacity :'';
         $buttonColor = $buttonColorRGB ? 'color: rgba('.$buttonColorRGB.', '.$buttonColorOpacity.');' : '';
         $buttonBgColorRGB = isset($this->data->buttonBgColor) && isset($this->data->buttonBgColor->rgb) ? $this->data->buttonBgColor->rgb :'';
         $buttonBgColorOpacity = isset($this->data->buttonBgColor) && isset($this->data->buttonBgColor->opacity) ? $this->data->buttonBgColor->opacity :'';
         $buttonBgColor = $buttonBgColorRGB ? 'background-color: rgba('.$buttonBgColorRGB.', '.$buttonBgColorOpacity.');' : '';
      
         //Nav Style
         $navStyle = isset($this->data->navStyle) ? $this->data->navStyle :'circles';
         $navColorRGB = isset($this->data->navColor->rgb) ? $this->data->navColor->rgb :'';
         $navColorOpacity = isset($this->data->navColor->opacity) ? $this->data->navColor->opacity :'1';
         $navColorStyle = $navColorRGB ? 'color: rgba('.$navColorRGB.', '.$navColorOpacity.');' : '';
         $navActiveColorRGB = isset($this->data->navActiveColor->rgb) ? $this->data->navActiveColor->rgb :'';
         $navActiveColorOpacity = isset($this->data->navActiveColor->opacity) ? $this->data->navActiveColor->opacity :'1';
         $navActiveColorStyle = $navColorRGB ? 'background: rgba('.$navActiveColorRGB.', '.$navActiveColorOpacity.'); color: rgba('.$navActiveColorRGB.', '.$navActiveColorOpacity.');' : '';
         $navSpanStyle = $navStyle !== 'circles'&& $navColorRGB ? 'background: rgba('.$navColorRGB.', '.$navColorOpacity.');' : '';


         $elementSlidesStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slides{width:'. $sliderWidth .';}';
         $elementSlideStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_carousel__slide{'. $width. $fontFamily  . '}';
         
         $elementContentBoxStyle =  '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slide__content{'. $contentBoxWidth . $contentBgColor .'}';
         $elementContentTitleStyle =  ($ttFontSize || $titleColor) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slide__title{'. $ttFontSize . $titleColor .'}' : '';
         $elementContentDescStyle = ($descFontSize || $descColor) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slide__desc{'. $descFontSize . $descColor .'}' : '';
         $elementContentButtonStyle =  ($buttonFontSize || $buttonColor || $buttonBgColor) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slide__button{'. $buttonFontSize. $buttonColor. $buttonBgColor.'}' : '';

         $testimonialDesignation = ($layout === 'testimonial' || $layout === 'review') ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .bravepopup_carousel__slide__theAuthor{'. $ttFontSize . $descColor .'}' : '';

         $navStyleCSS =  '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_carousel__slide__navs li{'. $navColorStyle .'}';
         $navSpanStyleCSS = $navSpanStyle ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_carousel__slide__navs span{'. $navSpanStyle .'}' : '';
         $navActiveStyleCSS =  '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .slide__nav__active span{'. $navActiveColorStyle .'}';


         return  $elementSlidesStyle . $elementSlideStyle . $elementContentBoxStyle . $elementContentTitleStyle. $elementContentDescStyle. $elementContentButtonStyle. $testimonialDesignation. $navStyleCSS. $navSpanStyleCSS. $navActiveStyleCSS;

      }
      
      public function renderBodyinChunks($slides, $index, $layout){
         $slideHTML = '';
         foreach ($slides as $key => $slide) {
            if(!empty($slide->hide)){ }else{
               $slideHTML .= '<div class="bravepopup_carousel__slide__content_wrap">';
               if($layout === 'news'){
                  $slideHTML .=  '<div class="bravepopup_carousel__slide__content">';
                  $slideHTML .=  isset($slide->title) ? '<h4 class="bravepopup_carousel__slide__title">'.$slide->title.'</h4>' : '';
                  $slideHTML .=  isset($slide->description) ? '<div class="bravepopup_carousel__slide__desc">'.html_entity_decode($slide->description).'</div>' : '';
                  $slideHTML .=  !empty($slide->button_text) ? '<a class="bravepopup_carousel__slide__button">'.$slide->button_text.'</a>' : '';
                  $slideHTML .=  '</div>';
                  $slideHTML .=  isset($slide->image) ? '<div class="bravepopup_carousel__slide__image"><img class="brave_element_img_item skip-lazy no-lazyload" data-lazy="'.$slide->image.'" src="'.bravepop_get_preloader().'"  /></div>' : '';
               }
               if($layout === 'toggle'){
                  $slideHTML .=  '<div class="bravepopup_carousel__slide__content bravepopup_carousel__toggle_'.$index.'_'.$key.'">';
                  $slideHTML .=  isset($slide->title) ? '<h4 class="bravepopup_carousel__slide__title" onclick="brave_toggle_item(\''.$this->data->id.'\', \'bravepopup_carousel__toggle_'.$index.'_'.$key.'\')">'.$slide->title.'</h4>' : '';
                  $slideHTML .=  isset($slide->description) ? '<div class="bravepopup_carousel__slide__desc">'.html_entity_decode($slide->description).'</div>' : '';
                  $slideHTML .=  '</div>';
               }
               $slideHTML .= '</div>';
            }
         }

         return $slideHTML;
      }

      public function renderBody($slide, $layout){
         $slideHTML = '';
            $showContent = empty($this->data->showContent) || (isset($this->data->showContent) && $this->data->showContent === true) ? true : false; 
            if($layout === 'slider' || $layout === 'features'){
               if(($layout === 'slider' && $showContent) || ($layout === 'features')){
                  $slideHTML .=  '<div class="bravepopup_carousel__slide__content">';
                  $slideHTML .=  isset($slide->title) ? '<h4 class="bravepopup_carousel__slide__title">'.$slide->title.'</h4>' : '';
                  $slideHTML .=  isset($slide->description) ? '<div class="bravepopup_carousel__slide__desc">'.html_entity_decode($slide->description).'</div>' : '';
                  $slideHTML .=  !empty($slide->button_text) ? '<a class="bravepopup_carousel__slide__button">'.$slide->button_text.'</a>' : '';
                  $slideHTML .=  '</div>';
               } 
               $slideHTML .=  isset($slide->image) ? '<div class="bravepopup_carousel__slide__image"><img class="brave_element_img_item skip-lazy no-lazyload" data-lazy="'.$slide->image.'" src="'.bravepop_get_preloader().'"  /></div>' : '';
            }

            if($layout === 'testimonial' || $layout === 'review'){
               $star = bravepop_renderIcon('star2');
               $slideHTML .=  '<div class="bravepopup_carousel__slide__content">';
               $slideHTML .=     $layout === 'testimonial' ? '<div class="bravepopup_carousel__slide__qoutes">'.bravepop_renderIcon('quotes').'</div>' : '';
               $slideHTML .=     isset($slide->description) ? '<div class="bravepopup_carousel__slide__desc">'.html_entity_decode($slide->description).'</div>' : '';
               $slideHTML .=  '</div>';

               $slideHTML .=  '<div class="bravepopup_carousel__slide__author '.($layout === 'review' && empty($slide->button_text) ? 'bravepopup_carousel__slide__author__noTitle':'').'">';
                  $slideHTML .=  isset($slide->image) ? '<div class="bravepopup_carousel__slide__image"><img class="brave_element_img_item skip-lazy no-lazyload" data-lazy="'.$slide->image.'" src="'.bravepop_get_preloader().'"  /></div>' : '';
                  $slideHTML .=  '<div class="bravepopup_carousel__slide__theAuthor">';
                  $slideHTML .=     isset($slide->title) ? '<h4 class="bravepopup_carousel__slide__title">'.$slide->title.'</h4>' : '';
                  $slideHTML .=     !empty($slide->button_text) ? '<span>'.$slide->button_text.'</span>' : '';
                  $slideHTML .=  '</div>';
                  if($layout === 'review' && !empty($slide->button_link) ){
                     $slideHTML .=  '<div class="bravepopup_carousel__slide__reviewRating bravepopup_carousel__slide__reviewRating--rating_'.$slide->button_link.'">';
                     $slideHTML .=     "<span>{$star}</span><span>{$star}</span><span>{$star}</span><span>{$star}</span><span>{$star}</span>";
                     $slideHTML .=  '</div>';
                  }

               $slideHTML .=  '</div>';
            }
         return $slideHTML;
      }


      public function renderSlides($layout){
         $slideDuration = isset($this->data->slideDuration) ? $this->data->slideDuration : 2000;
         $slideHTML = '<div class="bravepopup_carousel__slides" id="brave_carousel__slides-'.$this->data->id.'" data-totalslides="'.count($this->data->slides).'" data-width="'.$this->data->width.'" data-duration="'.$slideDuration.'" data-hovered="false" onmouseenter="brave_carousel_pause(\''.$this->data->id.'\', false)" onmouseleave="brave_carousel_pause(\''.$this->data->id.'\', true)">';
         $roundedImg = ($layout === 'testimonial' || $layout === 'review') && !empty($this->data->roundedImage) ? 'brave_carousel__slide--roundedImg'  : '';

         foreach ($this->slides as $key => $slide) {
            $slideHTML .=  '<div class="brave_carousel__slide '.(empty($slide->image) ? 'bravepopup_carousel__slide--noImg' : '').' '.$roundedImg.'">';
            $slideHTML .=     ($layout === 'news' || $layout === 'toggle') ?  $this->renderBodyinChunks($slide, $key, $layout) : $this->renderBody($slide, $layout);
            $slideHTML .=  '</div>';
         }
         $slideHTML .=  '</div>';
         return $slideHTML;
      }

      public function renderNav(){
         $navStyle = !empty($this->data->navStyle) ? $this->data->navStyle  : 'circles';
         $navAlign = !empty($this->data->navAlign) ? $this->data->navAlign  : 'left';
         $navHTML = '';
         if($this->slides && count($this->slides) > 1){
            $navHTML .= '<div class="brave_carousel__slide__navs brave_carousel__slide__navs--style_'.$navStyle.' brave_carousel__slide__navs--align_'.$navAlign.'" id="brave_carousel__navs-'.$this->data->id.'" data-currentslide="0"><ul>';
            foreach ($this->slides as $key => $slide) {
               //if(!empty($slide->hide)){ }else{
                  $navHTML .=  '<li id="brave_carousel__nav-'.$this->data->id.'_'.$key.'" onclick="brave_change_slide(\''.$this->data->id.'\', \''.$key.'\', \''.$this->data->width.'\');" class="'.($key === 0 ? 'slide__nav__active':'').'"><span>'.$key.'</span></li>';
               //}
            }
            $navHTML .=  '</ul></div>';
         }
         return $navHTML;
      }

      public function render( ) {
         $layout = !empty($this->data->layout) ? $this->data->layout  : 'slider';
         $theSlides = isset($this->data->slides) ? $this->renderSlides($layout) : '';
         $slideNav = isset($this->data->showNavigation) && $this->data->showNavigation === false ? false : true;
         $theSliderNavs = isset($this->data->slides) && $slideNav ? $this->renderNav() : '';
         $contentPositon= isset($this->data->contentPositon) ? '_'.$this->data->contentPositon : '_bottom_right';
         $navClass = $this->slides && count($this->slides) > 1 ? 'bravepopup_carousel--hasNav' : 'bravepopup_carousel--noNav'; 

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--carousel">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_element__carousel_inner bravepopup_carousel__slider--content'.$contentPositon.' bravepopup_carousel--'.$layout.' '.$navClass.'">
                              <div class="bravepopup_carousel__slider_wrap">
                                 '.$theSlides. $theSliderNavs.'
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