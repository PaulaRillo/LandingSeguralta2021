<?php

function bravepop_sociallogin_script_attrs( string $tag, string $handle ): string {
   if ( 'bravepop_facebook_login_js' === $handle || 'bravepop_google_login_js' === $handle ) {
         return str_replace( ' src', ' async defer crossorigin="anonymous" src', $tag );
   }
   return $tag;
}

add_filter( 'script_loader_tag', 'bravepop_sociallogin_script_attrs', 10, 2 );


//LinkedIn
//********************************************/

//Linkedin Authentication Template Redirect
add_action('wp_loaded', 'bravepop_linkedin_auth_template');
function bravepop_linkedin_auth_template(){
   if ( isset($_GET['brave_linkedin_auth']) ) {
      $page_template = dirname( __FILE__ ) . '/bravelinkedin-template.php';
      load_template( $page_template, true);
      if ($page_template) {
         exit();
      }
   }
}

//Fetch User Data from LinkedinIn
add_action('wp_ajax_bravepop_linkedin_authenticate_user', 'bravepop_linkedin_authenticate_user', 0);
add_action('wp_ajax_nopriv_bravepop_linkedin_authenticate_user', 'bravepop_linkedin_authenticate_user');
function bravepop_linkedin_authenticate_user(){
   error_log('bravepop_linkedin_authenticate_user Call!');
   if(!isset($_POST['code']) ){ print_r(json_encode(array('success'=>false, 'message'=>__('LinkedIn Auth Code Not Provided!', 'bravepop')))); wp_die(); }
   
   // First check the nonce, if it fails the function will break
   $securityPassed = check_ajax_referer('brave-linkedin-nonce', 'security', false);
   if($securityPassed === false) {
      error_log('LinkedIn Security Did not Pass!');
      print_r(json_encode(array('success'=>false, 'message'=>__('Secuity Code Failed!', 'bravepop'))));
      wp_die();
   }

   //fetch the LinkedIn Client ID and Secret key
   $currentSettings = get_option('_bravepopup_settings');
   $integrations = $currentSettings && isset($currentSettings['integrations']) ? $currentSettings['integrations'] : array() ;
   $linkedInClientID = isset($integrations['linkedin']) && isset($integrations['linkedin']->api) ? $integrations['linkedin']->api : '';
   $linkedInClientSecret = isset($integrations['linkedin']) && isset($integrations['linkedin']->secret) ? $integrations['linkedin']->secret : '';

   //Abort if the LinkedIn Client ID and Secret key is not found
   if(!$linkedInClientID || !$linkedInClientSecret){ print_r(json_encode(array('success'=>false, 'message'=>__('LinkedIn is not Properly Integrated.', 'bravepop')))); wp_die(); }

   $client_id = $linkedInClientID ;
   $client_secret = $linkedInClientSecret;
   $redirect = urlencode(esc_url( home_url( '/' ) ).'?brave_linkedin_auth');
   $code = $_POST['code'];
   $accessParams = '?client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=authorization_code&code='.$code.'&redirect_uri='.$redirect;
   $accessTokenError = json_encode(array('success'=>false, 'message'=>__('Access Token Not Found!', 'bravepop')));

   //Get Users Access Token
   $accessResponse = wp_remote_post( 'https://www.linkedin.com/oauth/v2/accessToken'.$accessParams );
   if( is_wp_error( $accessResponse ) ) {
      print_r($accessTokenError); wp_die();
   }

   $body = wp_remote_retrieve_body( $accessResponse );
   $data = json_decode( $body );
   //error_log('#URL: '.'https://www.linkedin.com/oauth/v2/accessToken'.$accessParams);
   //error_log(json_encode($data));

   if(isset($data->access_token)){
      //error_log(json_encode($data->access_token));

      //Get Users Name
      $args = array( 'headers' => array('Authorization' => 'Bearer ' . $data->access_token, 'X-RestLi-Protocol-Version' => '2.0.0' ));
      $userResponse = wp_remote_get( 'https://api.linkedin.com/v2/me', $args );
      $userBody = wp_remote_retrieve_body( $userResponse );
      $userData = json_decode( $userBody );
      $userDataError = json_encode(array('success'=>false, 'message'=>__('Could not Fetch User Data', 'bravepop')));
      //error_log(json_encode($userData));

      if(isset($userData->id)){

         $fullName = isset($userData->localizedFirstName) ? $userData->localizedFirstName : '';
         if(isset($userData->localizedLastName)){
            $fullName = $fullName.' '.$userData->localizedLastName;
         }
         //Get Users Email Address
         $emailResponse = wp_remote_get( 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))', $args );
         $emailBody = wp_remote_retrieve_body( $emailResponse );
         $emailData = json_decode( $emailBody );
         $handle = 'handle~';
         if(isset($emailData->elements[0]->$handle->emailAddress)){
            print_r(json_encode(array('success'=>true, 'user'=> array('name'=> $fullName, 'email' => $emailData->elements[0]->$handle->emailAddress, 'id'=> $userData->id, 'token'=> $data->access_token ) )));
         }else{
            print_r($userDataError);
         }
         
      }else{
         print_r($userDataError);
      }

      wp_die();
   }else{
      print_r($accessTokenError); wp_die();
   }

}
function bravepop_default_social_buttons($type='optin'){
   $buttons = array();
   $services = array('facebook', 'google', 'linkedin', 'email');
   $verb = 'Continue';
   if($type==='signup'){  $verb = 'Signup'; }
   if($type==='login'){  $verb = 'Login'; }

   foreach ($services as $key => $item) {
      $finalItem = new stdClass();
      $finalItem->enabled = true;
      $finalItem->label = $verb.' with '.$item;
      $finalItem->type = $item;
      $buttons[] = $finalItem;
   }
   return $buttons;
}