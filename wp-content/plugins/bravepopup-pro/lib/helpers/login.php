<?php

add_action('wp_ajax_bravepop_ajax_login', 'bravepop_ajax_login', 0);
add_action('wp_ajax_nopriv_bravepop_ajax_login', 'bravepop_ajax_login');

function bravepop_ajax_login(){
   if(!isset($_POST['email']) || !isset($_POST['password'])){ wp_die(); }
   // First check the nonce, if it fails the function will break
   check_ajax_referer('brave-ajax-login-nonce', 'security');
   $wpUserID = false;

   if(!empty($_POST['social']) && isset($_POST['social_login_data'])){
      $socialData = json_decode(str_replace('\"', '"', $_POST['social_login_data']));
      if(!$socialData->service || !$socialData->uid || !$socialData->email || !$socialData->token){
         print_r(json_encode(array('loggedin'=>false, 'message'=> (isset($socialData->service) ? ucwords($socialData->service) : '').__(' Authentication Failed!', 'bravepop'))));
         wp_die();
      }else{
         $authenticated = bravepop_authenticate_social_user($socialData);
         if($authenticated){
            $userOBJ = get_user_by( 'email',  $socialData->email);
            if(isset($userOBJ->ID)){
               $wpUserID = $userOBJ->ID;
            }else{
               print_r(json_encode(array('loggedin'=>false, 'message'=> __('Account Not Found!', 'bravepop'))));
               wp_die();
            }
         }else{
            print_r(json_encode(array('loggedin'=>false, 'message'=> (isset($socialData->service) ? ucwords($socialData->service) : '').__(' Authentication Failed!', 'bravepop'))));
            wp_die();
         }
      }

   }else{
         // Nonce is checked, get the POST data and sign user on
         $userData = array();
         $userData['user_login'] = sanitize_text_field(wp_unslash($_POST['email']));
         $userData['user_password'] = sanitize_text_field(wp_unslash($_POST['password']));
         $userData['remember'] = true;


      $user_signon = wp_signon( $userData, false );
      if ( is_wp_error($user_signon) ){

         //Check if Social Users are trying to login with email/password, If they are, notify them to login with their social account.
         $foundUser = get_user_by('email', $userData['user_login'] );
         if( $foundUser && isset( $foundUser->ID)){
            $brave_social_login_type = get_user_meta( $foundUser->ID, '_brave_social_login_type', true );
            $brave_social_login_id = get_user_meta( $foundUser->ID, '_brave_social_login_id', true );
            // error_log('$brave_social_login_id: '.json_encode($brave_social_login_id));
            if($brave_social_login_type && $brave_social_login_type){
               print_r(json_encode(array('loggedin'=>false, 'message'=>__('Login Failed! You Signed up with ', 'bravepop').ucwords($brave_social_login_type) )));
               wp_die();
            }
         }

         print_r(json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.', 'bravepop'))));
         wp_die();
      } else {
         $wpUserID = $user_signon->ID;
      }
   }

   //If the User ID is found, Log the User in.
   if($wpUserID){
      wp_set_current_user($wpUserID);
      wp_set_auth_cookie($wpUserID);

      $wp_user = get_userdata($wpUserID); 
      $user_roles = $wp_user->roles; 
      do_action( 'brave_user_loggedin', $wp_user );
      if(isset($wp_user->user_login)){
         do_action( 'wp_login', $wp_user->user_login, $wp_user );
      }

      if (in_array("administrator", $user_roles)){
         $redirect = esc_url(admin_url( '/' ));
      }else{
         $redirect = isset($_POST['redirect']) ? esc_url($_POST['redirect']) : esc_url( home_url( '/' ) );
      }
      print_r(json_encode(array('loggedin'=>true, 'redirect'=> $redirect, 'message'=>__('Login successful, redirecting...', 'bravepop'))));
   }

   wp_die();
}


add_action('wp_ajax_bravepop_ajax_signup', 'bravepop_ajax_signup', 0);
add_action('wp_ajax_nopriv_bravepop_ajax_signup', 'bravepop_ajax_signup');
function bravepop_ajax_signup(){
   //First check the nonce, if it fails the function will break
   check_ajax_referer('brave-ajax-signup-nonce', 'signupsecurity');

   //Nonce is checked, get the POST data and create user
   if(!isset($_POST['email'])){
      print_r(json_encode(array('created'=>false, 'message'=>__('Email is mandatory.', 'bravepop')))); wp_die();
   }
   if(!isset($_POST['password'])){
      print_r(json_encode(array('created'=>false, 'message'=>__('Please Set your Password.', 'bravepop')))); wp_die();
   }

   $signupGoal = isset($_POST['goalData']) && $_POST['goalData'] != 'false' ? json_decode( sanitize_text_field(wp_unslash($_POST['goalData'])) ) : false;
   $new_username = isset($_POST['username']) && $_POST['username'] != 'false' ? sanitize_text_field(wp_unslash($_POST['username'])) : sanitize_text_field(wp_unslash($_POST['email']));
   $new_user_email = isset($_POST['email']) ? sanitize_text_field(wp_unslash($_POST['email'])) : '';
   $new_user_password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
   $user_nice_name = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
   $new_user_first_name = isset($_POST['firstname']) ? sanitize_text_field(wp_unslash($_POST['firstname'])) : '';
   $new_user_last_name = isset($_POST['lastname']) ? sanitize_text_field(wp_unslash($_POST['lastname'])) : '';
   $new_user_full_name = isset($_POST['firstname']) && isset($_POST['lastname']) ? $new_user_first_name.' '.$new_user_last_name: '';
   $popupID = isset($_POST['popupID']) ? $_POST['popupID']: '';
   $elementID = isset($_POST['elementID']) ? $_POST['elementID']: '';
   $stepIndex = isset($_POST['stepIndex']) ? (int)$_POST['stepIndex']  : false;
   $socialData = false;


   $user_data = get_user_by( 'email', trim( wp_unslash( $new_user_email ) ) );
   if ( isset($user_data->ID) ) {
      print_r(json_encode(array('created'=>false, 'message'=>__('An account already exist with your email address. Try logging in.', 'bravepop'))));
      wp_die();
   }

   if(!empty($_POST['social']) && isset($_POST['social_login_data'])){
      $socialData = json_decode(str_replace('\"', '"', $_POST['social_login_data']));
   
      if(!$socialData->service || !$socialData->uid || !$socialData->email || !$socialData->name || !$socialData->token){
         print_r(json_encode(array('created'=>false, 'message'=> $socialData->service.__(' Authentication Failed!', 'bravepop'))));
         wp_die();

      }else{
         $authenticated = bravepop_authenticate_social_user($socialData);
         // error_log('authenticated: '.json_encode($authenticated));
         if($authenticated){
            $firstname = $socialData->name; $lastname = '';
            if(strpos($socialData->name, ' ') !== false){
               $splitted = explode(" ",$socialData->name);
               $firstname = $splitted[0] ? $splitted[0] : '';
               $lastname = $splitted[1] ? $splitted[1] : '';
            }
            $new_username =  sanitize_text_field(wp_unslash($socialData->email));
            $new_user_email = sanitize_text_field(wp_unslash($socialData->email));
            $new_user_password = wp_generate_password();
            $new_user_first_name =  $firstname;
            $new_user_last_name = $lastname;
            $new_user_full_name = $socialData->name;

         }else{
            print_r(json_encode(array('created'=>false, 'message'=> $socialData->service.__(' Authentication Failed!', 'bravepop'))));
            wp_die();
         }
      }
   }

   //Get Login Element Settings;
   $signupRole = 'subscriber';
   $welcomeEmail = ''; $welcomeEmailSubject = __('Thank You For Signing Up!', 'bravepop');

   if($popupID && $elementID && is_int($stepIndex)){
      $popupData = json_decode(get_post_meta($popupID, 'popup_data', true));
      $desktopContent =  isset($popupData->steps[$stepIndex]->desktop->content) ? $popupData->steps[$stepIndex]->desktop->content : array();
      $mobileContent =  isset($popupData->steps[$stepIndex]->mobile->content) ? $popupData->steps[$stepIndex]->mobile->content : array();
      $allElements = array_merge($desktopContent, $mobileContent);

      foreach ($allElements as $key => $element) {
         if(($element->id === $elementID) && $element->type === 'login'){
            if(!empty($element->signupRole)){
               $signupRole = $element->signupRole;
            }
            if(isset($element->welcomeEmail) && ($element->welcomeEmail === 'text' || $element->welcomeEmail === 'html') && !empty($element->welcomeEmailContent)){
               $message = $element->welcomeEmailContent;
               $formattedMsg = json_encode($message);
               $theMessage =  str_replace('\n', '&lt;br&gt;',  $formattedMsg);
               $theMessage = json_decode($theMessage);
               $theMessage = html_entity_decode($theMessage); 
               $theMessage = str_replace('[firstname]', $new_user_first_name, $theMessage);
               $theMessage = str_replace('[lastname]', $new_user_last_name, $theMessage);
               $theMessage = str_replace('[email]', $new_user_email, $theMessage);

               $welcomeEmail = $theMessage;
               if(!empty($element->welcomeEmailSubject)){
                  $welcomeEmailSubject = $element->welcomeEmailSubject;
               }
            }
         }
      }
   }


   $user_data = array(
       'user_login' => $new_username,
       'user_email' => $new_user_email,
       'user_pass' => $new_user_password,
       'user_nicename' => $user_nice_name,
       'first_name' => $new_user_first_name,
       'last_name' => $new_user_last_name,
       'display_name' => $new_user_full_name,
       'role' => $signupRole
      );
   
   $user_data = apply_filters( 'bravepop_wp_signup_user', $user_data );

   //Create New User
   $user_id = wp_insert_user($user_data);

   if (!is_wp_error($user_id)) {

      if($socialData && isset($socialData->service) && isset($socialData->uid)){
         //error_log(json_encode($user_id));
         update_user_meta( $user_id, '_brave_social_login_type', $socialData->service );
         update_user_meta( $user_id, '_brave_social_login_id', $socialData->uid );
      }

      //Log the User In
      $loginData = array();
      $loginData['user_login'] = $new_user_email;
      $loginData['user_password'] = $new_user_password;
      $loginData['remember'] = true;

      $userObj = get_user_by('id',$user_id);
      wp_signon( $loginData, false );
      wp_set_current_user($user_id);
      wp_set_auth_cookie($user_id);

      wp_new_user_notification( $user_id, null, 'admin' );
      // wp_new_user_notification( $user_id,'','user' );

      do_action( 'brave_user_signedup', $userObj );
      do_action( 'wp_login', $new_username, $userObj );

      $user_roles=$userObj->roles; 
      if (in_array("administrator", $user_roles)){
         $redirect = esc_url(admin_url( '/' ));
      }else{
         $redirect = isset($_POST['redirect']) ? esc_url($_POST['redirect']) : esc_url( home_url( '/' ) );
      }

      if($signupGoal && $signupGoal->popupID){
         bravepop_popup_complete_goal($signupGoal->popupID, $signupGoal->goalType, $signupGoal->views, $signupGoal->goalTime, $signupGoal->goalDate, $signupGoal->goalUTCTime, $signupGoal->device, $signupGoal->pageURL);
      }

      if(!empty($welcomeEmail)){
         $headers = array('Content-Type: text/html;');
         wp_mail( $new_user_email, $welcomeEmailSubject, $welcomeEmail, $headers);
      }

      print_r(json_encode(array('created'=>true, 'redirect'=> $redirect, 'message'=>__('Account Created. Redirecting..', 'bravepop'))));

   } else {
       if (isset($user_id->errors['empty_user_login'])) {
             print_r(json_encode(array('created'=>false, 'message'=>__('User Name and Email are mandatory.', 'bravepop'))));
         } elseif (isset($user_id->errors['existing_user_login'])) {
             print_r(json_encode(array('created'=>false, 'message'=>__('User name already exists.', 'bravepop'))));
         }elseif (isset($user_id->errors['existing_user_email'])) {
             print_r(json_encode(array('created'=>false, 'message'=>__('Email address already in use.', 'bravepop'))));
         } else {
             print_r(json_encode(array('created'=>false, 'message'=>__('Error Occured please fill up the sign up form carefully.', 'bravepop'))));
         }
   }

   wp_die();
}



add_action('wp_ajax_bravepop_ajax_resetpass', 'bravepop_ajax_resetpass', 0);
add_action('wp_ajax_nopriv_bravepop_ajax_resetpass', 'bravepop_ajax_resetpass');
function bravepop_ajax_resetpass(){
   if(!isset($_POST['email'])){ wp_die(); }
   // First check the nonce, if it fails the function will break
   check_ajax_referer('brave-ajax-resetpass-nonce', 'security');
   // Nonce is checked, get the POST data and sign user on
   $user_email = sanitize_text_field(wp_unslash($_POST['email']));
   $resetSent = bravepop_retrieve_password($user_email);

   print_r($resetSent);
   wp_die();
}


//Modfied Core retrieve_password function
function bravepop_retrieve_password($user_email) {
	$errors = new WP_Error();
	if ( empty( $user_email ) || ! is_string( $user_email ) ) {
      //$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.' ) );
      return json_encode(array('sent'=>false, 'message'=>__('Enter a username or email address.', 'bravepop')));
	} elseif ( strpos( $user_email, '@' ) ) {
		$user_data = get_user_by( 'email', trim( wp_unslash( $user_email ) ) );
		if ( empty( $user_data ) ) {
         //$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no account with that username or email address.' ) );
         return json_encode(array('sent'=>false, 'message'=>__('There is no account with that username or email address.', 'bravepop')));
		}
	} else {
		$login     = trim( $user_email );
		$user_data = get_user_by( 'login', $login );
	}

	do_action( 'lostpassword_post', $errors );
	if ( $errors->has_errors() ) {
		return $errors;
	}
	if ( ! $user_data ) {
      //$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: There is no account with that username or email address.' ) );
      return json_encode(array('sent'=>false, 'message'=>__('There is no account with that username or email address.', 'bravepop')));
		return $errors;
	}
	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key        = get_password_reset_key( $user_data );
	if ( is_wp_error( $key ) ) {
		return $key;
	}
	if ( is_multisite() ) {
		$site_name = get_network()->site_name;
	} else {

		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
	$message = __( 'Someone has requested a password reset for the following account:' ) . "\r\n\r\n";
	/* translators: %s: Site name. */
	$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\r\n\r\n";
	/* translators: %s: User login. */
	$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
	$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";
	/* translators: Password reset notification email subject. %s: Site title. */
	$title = sprintf( __( '[%s] Password Reset' ), $site_name );

	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
		// $errors->add(
		// 	'retrieve_password_email_failure',
		// 	sprintf(
		// 		/* translators: %s: Documentation URL. */
		// 		__( '<strong>ERROR</strong>: The email could not be sent. Your site may not be correctly configured to send emails. <a href="%s">Get support for resetting your password</a>.' ),
		// 		esc_url( __( 'https://wordpress.org/support/article/resetting-your-password/' ) )
		// 	)
      // );
      return json_encode(array('sent'=>false, 'message'=>__('The email could not be sent. Your site may not be correctly configured to send emails.', 'bravepop')));
   }
   
	return json_encode(array('sent'=>true, 'message'=>__('Please check your Email to reset the Password.', 'bravepop')));
}


function bravepop_authenticate_social_user($socialData){

   $service = $socialData->service; $uid = $socialData->uid;  $token = $socialData->token;
   $authenticated = false;

   //Authenticate Facebook User
   if($service === 'facebook'){
      $response = wp_remote_get( 'https://graph.facebook.com/'.$uid.'?fields=id&access_token='.$token );
      if( is_wp_error( $response ) ) { return false; }
      $body = wp_remote_retrieve_body( $response ); $data = json_decode( $body );
      if(isset($data->id)){
         $authenticated = true;
      }
   }

   //Authenticate Google User
   if($service === 'google'){
      $response = wp_remote_get( 'https://oauth2.googleapis.com/tokeninfo?id_token='.$token );
      if( is_wp_error( $response ) ) { return false;  }
      $body = wp_remote_retrieve_body( $response ); $data = json_decode( $body );
      if(isset($data->sub) && $data->sub === $uid){
         $authenticated = true;
      }
   }

   //Authenticate LinkedIn User
   if($service === 'linkedin'){
      $args = array( 'headers' => array('Authorization' => 'Bearer ' . $token, 'X-RestLi-Protocol-Version' => '2.0.0' ));
      $userResponse = wp_remote_get( 'https://api.linkedin.com/v2/me', $args );
      if( is_wp_error( $userResponse ) ) {  return false; }
      $userBody = wp_remote_retrieve_body( $userResponse );
      $userData = json_decode( $userBody );
      if(isset($userData->id)){
         $authenticated = true;
      }
   }

   return $authenticated;
}