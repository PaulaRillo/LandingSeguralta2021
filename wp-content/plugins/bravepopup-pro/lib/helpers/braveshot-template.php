<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>BraveShots</title>
   <?php
      print_r('<script> var brave_popup_data = {}; var bravepop_global = {}; var brave_popup_videos = {};  var brave_popup_formData = {};var brave_popup_adminUser = true; var brave_popup_pageInfo = '.( function_exists('bravepop_get_current_pageInfo') ? json_encode(bravepop_get_current_pageInfo()) :'{}').';  var bravepop_emailSuggestions={};</script>');
      print_r("<link rel='stylesheet' id='bravepop_front_css'  href='".BRAVEPOP_PLUGIN_PATH . 'assets/css/frontend.min.css'."' type='text/css' media='all' />");
      print_r("<link rel='stylesheet' id='bravepop_login_element'  href='".BRAVEPOP_PLUGIN_PATH . 'assets/css/wp_login.min.css'."' type='text/css' media='all' />");
      print_r("<link rel='stylesheet' id='bravepop_posts_element'  href='".BRAVEPOP_PLUGIN_PATH . 'assets/css/wp_posts.min.css'."' type='text/css' media='all' />");
      print_r("<link rel='stylesheet' id='bravepop_woocommerce_element'  href='".BRAVEPOP_PLUGIN_PATH . 'assets/css/woocommerce.min.css'."' type='text/css' media='all' />");
      print_r("<style>
      .braveshots_wrapper .brave_popup .brave_popup__step .brave_popup__step__inner { top: 0;  margin-top: 0!important; left: 0; right: auto;} 
      .braveshots_wrapper .brave_popup .brave_popup__step .brave_popup__step__inner .brave_popupMargin__wrap { top: 0!important;left: 0!important;  padding: 0!important; }
      </style>");
      //Get a Bigger version of the Image
      //.braveshots_wrapper .brave_popup{zoom: 120%;}
      // OR
      //.braveshots_wrapper .brave_popup .brave_popup__step .brave_popup__step__inner {transform: scale(1.2) perspective(1px) translateZ(0); transform-origin: top left; backface-visibility: hidden; display: inline-table;
   ?>
</head>
<body>
<div class="braveshots_wrapper"> 
   <?php
      $popupID = isset($_GET['brave_id']) ? (int)$_GET['brave_id'] : '';
      $braveStep = isset($_GET['brave_step']) ? (int)$_GET['brave_step'] : false;
      if($popupID){
         $thePopup = new BravePop_Popup($popupID, 'popup', true, $braveStep);
         $popupContent = '';
         ob_start();
         $thePopup->popup_render();
         $popupContent = ob_get_contents();
         ob_end_clean();
         wp_add_inline_style( 'bravepop_front_css', $thePopup->popup_inline_css() );
         wp_add_inline_script( 'bravepop_front_js', $thePopup->popup_inline_js() );

         echo $popupContent;
      }
   ?>
</div>
</body>
<?php print_r("<script type='text/javascript' src='".BRAVEPOP_PLUGIN_PATH . 'assets/frontend/brave.js' ."' id='bravepop_front_js'></script>");?>
</html>