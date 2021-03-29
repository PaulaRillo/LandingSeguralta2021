<?php

function bravepop_shortcode( $atts ) {
   extract(shortcode_atts(array('id' => '', 'align' => 'center'), $atts));
   if(!$id){ return '';}
   $popupID = $id;
   $current_screen = new stdClass();
   if(is_admin()){  $current_screen = get_current_screen(); }
   if(is_admin() && !empty($current_screen->is_block_editor)){ return ''; }
   if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) { return '';}

   if(get_post_status( $popupID ) === 'publish' ){

      //Check if Popup has active ABTest. If does, display a variation randomly
      $post_abtest = json_decode(get_post_meta( $popupID, 'popup_abtest', true ));
      if(isset($post_abtest->active) && $post_abtest->active === true && count($post_abtest->items) > 0){
         $popupVariations = array();
         foreach ($post_abtest->items as $index => $popItem) {
            $popupVariations[] = $popItem->id;
         }
         $popupID = $popupVariations[array_rand($popupVariations)];
      }

      //RENDER THE POPUP
      $thePopup = new BravePop_Popup($popupID, 'content');
      $popupContent = '';
      $lock = !empty($thePopup->popupData->settings->content->lock) ? true : false; 
      $isVisible = $thePopup->userTypeMatch && $thePopup->refererMatch && $thePopup->languageMatch && $thePopup->countryMatch && $thePopup->hasCartItems && !in_array(false, $thePopup->cartFilterMatch) && $thePopup->purchaseMatch && $thePopup->notpurchaseMatch;

      if($isVisible) {

         ob_start();
         $thePopup->popup_render();
         $popupContent = ob_get_contents();
         ob_end_clean();

         $inlineScript = $thePopup->popup_inline_js();
         $inlineStyle = $thePopup->popup_inline_css();

         wp_add_inline_style( 'bravepop_front_js', $inlineStyle );
         wp_add_inline_script( 'bravepop_front_js', $inlineScript );

         if($thePopup->advancedAnimation && $thePopup->hasAnimation) {
            wp_enqueue_script( 'bravepop_animejs', BRAVEPOP_PLUGIN_PATH . 'assets/frontend/anime.min.js' ,'','',true);
            wp_enqueue_script( 'bravepop_animation', BRAVEPOP_PLUGIN_PATH . 'assets/frontend/animate.js' ,'','',true);
         }
         if($thePopup->hasLoginElement){
            wp_enqueue_script( 'bravepop_loginjs', BRAVEPOP_PLUGIN_PATH . 'assets/frontend/login.js' ,'','',true);
            wp_enqueue_style('bravepop_login_element',  BRAVEPOP_PLUGIN_PATH . 'assets/css/wp_login.min.css' );
         }
         if($thePopup->hasWpPosts){
            wp_enqueue_style('bravepop_posts_element',  BRAVEPOP_PLUGIN_PATH . 'assets/css/wp_posts.min.css');
         }
         if($thePopup->hasWpProducts){
            wp_enqueue_style('bravepop_woocommerce_element',  BRAVEPOP_PLUGIN_PATH . 'assets/css/woocommerce.min.css');
         }
         if($thePopup->hasDesktopEmbed || $thePopup->hasMobileEmbed){
            wp_enqueue_script( 'bravepop_embedlock', BRAVEPOP_PLUGIN_PATH . 'assets/frontend/embedlock.js' ,'','',false);
         }
      }

      // error_log(json_encode($atts));
      $theLock = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1200 1200"><path d="M600 0C268.629 0 0 268.629 0 600s268.629 600 600 600s600-268.629 600-600S931.371 0 600 0zm-4.321 274.658c81.026.386 155.088 52.056 186.548 124.146c10.762 25.109 16.479 50.903 16.479 78.882v71.339h98.291v376.317H303.003V549.023h90.381c-.819-50.406-1.856-108.07 15.82-150.221c34.37-75.909 105.448-124.53 186.475-124.144zm-4.395 119.824c-44.881.944-74.48 35.073-78.81 83.202v71.339h167.14v-72.07c-2.061-45.641-36.604-81.214-83.937-82.471a93.24 93.24 0 0 0-4.393 0z" fill="#626262"/></svg>';
      $theLines = ''; for ($i=0; $i < 10; $i++) {   $theLines .= '<span></span>';  }
      $embedLocker = $lock ? '<div class="bravepopup_embedded__locker bravepopup_embedded__locker_'.$popupID.'" style="display:none;">'.$theLines.$theLock.'</div>' :'';
      $spinner = '<span class="brave_embed_loading">'.bravepop_renderIcon('reload', '#ccc').'</span>';
      return $isVisible ? '<div id="bravepopup_embedded_'.$popupID.'" data-popupid="'.$popupID.'" class="bravepopup_embedded bravepopup_embedded--'.$align.' '.($lock  ? 'bravepopup_embedded--lock' : '').'">'.$popupContent.'</div><div class="bravepopup_embedded__floatclear"></div>'.$embedLocker.'':'';
      
   }
}
add_shortcode( 'bravepop', 'bravepop_shortcode' );



add_filter('the_content', 'bravepopup_content_auto_embed');
function bravepopup_content_auto_embed($content){
   global $bravepop_global;

   if( is_singular() && in_the_loop() && is_main_query() ) {
      if(isset($bravepop_global['autoEmbedded']) && is_array($bravepop_global['autoEmbedded']) && count($bravepop_global['autoEmbedded']) > 0){
         $autoEmbedded = $bravepop_global['autoEmbedded'];
         $topContent = ''; $bottomContent = '';  $firstParaContent = '';  $secondParaContent = '';  $thirdParaContent = '';
         foreach ($autoEmbedded as $key => $braveContent) {
            if(isset($braveContent->id)){
               $embedAlignment = isset($braveContent->placement->autoEmbedAlign) ? $braveContent->placement->autoEmbedAlign : 'center'; 
               $embedPosition = isset($braveContent->placement->autoEmbedPosition) ? $braveContent->placement->autoEmbedPosition : 'top'; 
               $contentShortcode = '[bravepop id="'.$braveContent->id.'" align="'.$embedAlignment.'"]';
               if($embedPosition === 'top'){   $topContent .= $contentShortcode;     }
               if($embedPosition === 'bottom'){   $bottomContent .= $contentShortcode;     }
               if($embedPosition === 'after_first_para'){   $firstParaContent .= $contentShortcode;     }
               if($embedPosition === 'after_second_para'){   $secondParaContent .= $contentShortcode;     }
               if($embedPosition === 'after_third_para'){   $thirdParaContent .= $contentShortcode;     }
            }
         }

         if($topContent){        $content = $topContent.$content;       }
         if($bottomContent){     $content = $content.$bottomContent;    }

         if($firstParaContent || $secondParaContent || $thirdParaContent){
            $paragraphs = explode("</p>", $content);
            $paragraphCount = count($paragraphs);
            $new_content = '';
            if($paragraphCount > 0){
               for ($i = 0; $i < $paragraphCount; $i++) {
                  if ($firstParaContent && $i === 0 ) {   $new_content .=  $paragraphs[$i] . "</p>".$firstParaContent; }
                  else if ($secondParaContent && $i === 1 ) {   $new_content .=  $paragraphs[$i] . "</p>".$secondParaContent; }
                  else if ($secondParaContent && $i === 2 ) {   $new_content .=  $paragraphs[$i] . "</p>".$thirdParaContent; }
                  else{
                     $new_content .= $paragraphs[$i] . "</p>";
                  }
               }
            }
            if($new_content){
               $content  = $new_content; 
            }
         }
         
      }
   }
   
   return $content;

}

add_action('wp_loaded', 'bravepopup_shots_init');
function bravepopup_shots_init(){
   if ( isset($_GET['braveshot']) && isset($_GET['brave_id']) && !is_admin() ) {
      $page_template = dirname( __FILE__ ) . '/helpers/braveshot-template.php';
      load_template( $page_template, true);
      if ($page_template) {
         exit();
      }
   }
}