<?php

if ( ! class_exists( 'BravePop_Element_Social' ) ) {
   
   class BravePop_Element_Social {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
      }


      public function render_social_items(){
         $socialItems = '';
         $socialType = isset($this->data->socialType) ? $this->data->socialType : 'follow'; 
         $socials = isset($this->data->socials) ? $this->data->socials : array();

         $defaultShares = array(); 
         $facebookShare = array('label' => 'Facebook', 'value' => 'facebook', 'enabled' => true); $defaultShares[] = (object)$facebookShare;
         $twitterShare = array('label' => 'Facebook', 'value' => 'twitter', 'enabled' => true); $defaultShares[] = (object)$twitterShare;
         $linkedinShare = array('label' => 'LinkedIn', 'value' => 'linkedin', 'enabled' => true); $defaultShares[] = (object)$linkedinShare;
         $pinterestShare = array('label' => 'Pinterest', 'value' => 'pinterest', 'enabled' => true); $defaultShares[] = (object)$pinterestShare;
         
         $shares = isset($this->data->shares) ? $this->data->shares : $defaultShares;
         //error_log(json_encode($shares));
         $shape = isset($this->data->shape) ? $this->data->shape : 'square'; 
         $newWindow = isset($this->data->newWindow) && $this->data->newWindow === true ? 'target="_blank" ' : '';
         $bgColorRGB = isset($this->data->bgColor) && isset($this->data->bgColor->rgb) ? $this->data->bgColor->rgb :'0,0,0';
         $bgColorOpacity = isset($this->data->bgColor) && isset($this->data->bgColor->opacity) ? $this->data->bgColor->opacity :'1';

         if($socialType === 'follow'){
            foreach ($socials as $key => $socItem) {
               //if(isset($socItem->hide) && $socItem->hide === true){ return ''; }
               if(!empty($socItem->link) && empty($socItem->hide)){ 
                  $shapeData  = new stdClass();
                  $shapeData->fillColor = 'rgba('.$bgColorRGB.', '.$bgColorOpacity.')';
                  $shapeData->width = '100%';
                  $shapeData->height = '100%';
                  $socialIcon = bravepop_getSocialIcon($socItem->link);
                  $iconHTML =  $socialIcon ?  '<div class="brave_social_icon">'.$socialIcon.'</div>' : '';
                  $socialItems .= '<a href="'.esc_url($socItem->link).'" '.$newWindow.'>';
                  $socialItems .= '<div class="brave_social_link" id="'.$socItem->id.'__social">'.$iconHTML;
                  $socialItems .= '<div class="brave_social_link__background">'.renderShape($this->data->id, $shape, $shapeData, null).'</div>';
                  $socialItems .= '</div>';
                  $socialItems .= '</a>';
               }
            }
         }else{
            global $wp;
            $shareUrls = new stdClass();
            $currentURL = home_url( $wp->request);
            $shareUrls->facebook = 'https://www.facebook.com/sharer.php?u='. esc_attr( home_url( $currentURL ) );
            $shareUrls->twitter = 'https://twitter.com/share?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->linkedin = 'https://www.linkedin.com/shareArticle?mini=true&amp;url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->pinterest = 'https://pinterest.com/pin/create/bookmarklet/?is_video=false&url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->stumbleupon = 'https://www.stumbleupon.com/submit?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->digg = 'https://www.digg.com/submit?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->reddit = 'https://reddit.com/submit?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->vk = 'https://vkontakte.ru/share.php?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->buffer = 'https://bufferapp.com/add?url='. esc_attr( home_url( $currentURL ) );
            $shareUrls->email = 'mailto:?subject=Check This Out!&amp;body=' . $currentURL;
            foreach ($shares as $key => $socItem) {
               if(!empty($socItem->enabled)){ 
               $shapeData  = new stdClass();
               $shapeData->fillColor = 'rgba('.$bgColorRGB.', '.$bgColorOpacity.')';
               $shapeData->width = '100%';
               $shapeData->height = '100%';
               $socialIcon = bravepop_getSocialIcon($socItem->value.'.com');
               $socVal = $socItem->value;
               $iconHTML =  $socialIcon ?  '<div class="brave_social_icon">'.$socialIcon.'</div>' : '';
               $socialItems .= '<a href="'.$shareUrls->$socVal.'" target="_blank">';
               $socialItems .= '<div class="brave_social_link" id="'.$this->data->id.'__social'.$socVal.'">'.$iconHTML;
               $socialItems .= '<div class="brave_social_link__background">'.renderShape($this->data->id, $shape, $shapeData, null).'</div>';
               $socialItems .= '</div>';
               $socialItems .= '</a>';
               }
            }
         }



         return $socialItems;
      }

      
      public function render_css() { 
         $socialType = isset($this->data->socialType) ? $this->data->socialType : 'follow'; 
         $customColor = isset($this->data->customColor) ? $this->data->customColor : false ;
         $socialCount = isset($this->data->perRow) ? intval($this->data->perRow) : 4 ;
         //if($socialType === 'share'){   $socialCount = isset($this->data->shares) ? count($this->data->shares) : 4 ; }
         $iconWidth = isset($this->data->width)? (($this->data->width / $socialCount ) - 16) : 0;
         $itemSize = isset($this->data->width)? 'width: '.$iconWidth.'px; height: '.$iconWidth.'px;' : '';
         $iconSize = isset($this->data->size) ? $this->data->size : 14;
         $iconSizeStyle = 'width: '.$iconSize.'px; height: '.$iconSize.'px;';

         $iconColorRGB = isset($this->data->iconColor) && isset($this->data->iconColor->rgb) ? $this->data->iconColor->rgb :'255,255,255';
         $iconColorOpacity = isset($this->data->iconColor) && isset($this->data->iconColor->opacity) ? $this->data->iconColor->opacity :'1';
         $iconColor = isset($this->data->iconColor) ? 'fill: rgba('.$iconColorRGB.', '.$iconColorOpacity.');' : '';
         $bgColorRGB = isset($this->data->bgColor) && isset($this->data->bgColor->rgb) ? $this->data->bgColor->rgb :'0,0,0';
         $bgColorOpacity = isset($this->data->bgColor) && isset($this->data->bgColor->opacity) ? $this->data->bgColor->opacity :'1';
         $iconBGColor = isset($this->data->bgColor) ? 'fill: rgba('.$bgColorRGB.', '.$bgColorOpacity.');' : '';

         $elementInnerStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_social_icon svg{ '. $iconColor . $iconSizeStyle .'}';
         $elementBGStyle = $customColor ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_social_link__background svg{ '. $iconBGColor . '}':'';

         $elementBullet = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_element__social a{  '.$itemSize  . '}';

         return  $elementInnerStyle . $elementBGStyle. $elementBullet;

      }


      public function render( ) { 
         $hover_effect = isset($this->data->hover_effect) ? $this->data->hover_effect : 'enlarge';
         $noSpacing = !empty($this->data->spacing) ? 'brave_element--social_nopspacing' : '';

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--social brave_element--social_hover_'.$hover_effect.' '.$noSpacing.'">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_element__social">
                              '.$this->render_social_items().'
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
      }


   }


}
?>