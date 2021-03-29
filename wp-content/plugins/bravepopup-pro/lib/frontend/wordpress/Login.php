<?php

if ( ! class_exists( 'BravePop_Element_Login' ) ) {
   

   class BravePop_Element_Login {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
         $social = !empty($this->data->social) ? true : false;
         $social_signup_buttons = isset($this->data->social_signup_buttons) ? $this->data->social_signup_buttons : $this->default_social_buttons('signup');
         $social_login_buttons = isset($this->data->social_login_buttons) ? $this->data->social_login_buttons : $this->default_social_buttons('login');

         if($social && (count($social_signup_buttons) > 0 || count($social_login_buttons) > 0)){
            add_action( 'wp_footer', array( $this, 'enqueue_social_login_js' ), 10 );
         }
      }

      
      public function render_css() { 

         $fontFamily = isset($this->data->fontFamily) && $this->data->fontFamily !== 'None' ?  'font-family: '.$this->data->fontFamily.';' : '';
         $backgroundColor = bravepop_generate_style_props(isset($this->data->backgroundColor) ? $this->data->backgroundColor : '', 'background-color', '255, 255, 255', '0');
         $textColor = bravepop_generate_style_props(isset($this->data->formColor) ? $this->data->formColor : '', 'color', '107, 107, 107', '1');
         $fontSize = bravepop_generate_style_props(isset($this->data->formFontSize) ? $this->data->formFontSize : '', 'font-size', '16');
         $roundNess =  isset($this->data->roundNess) ?  'border-radius: '.$this->data->roundNess.'px;' : 'border-radius: 4px;';
         $inputFontSize = bravepop_generate_style_props(isset($this->data->inputFontSize) ? $this->data->inputFontSize : '', 'font-size', '12');
         $inputColor = bravepop_generate_style_props(isset($this->data->inputColor) ? $this->data->inputColor : '', 'color', '0, 0, 0', '1');
         $borderColor = bravepop_generate_style_props(isset($this->data->borderColor) ? $this->data->borderColor : '', 'border-color', '0, 0, 0', '0.1');
         $fieldBgColor = bravepop_generate_style_props(isset($this->data->fieldBgColor) ? $this->data->fieldBgColor : '', 'background-color', '255, 255, 255', '1');
         $buttonFontSize = bravepop_generate_style_props(isset($this->data->buttonFontSize) ? $this->data->buttonFontSize : '', 'font-size', '14');
         $buttonTextColor = bravepop_generate_style_props(isset($this->data->buttonTextColor) ? $this->data->buttonTextColor : '', 'color', '255, 255, 255', '1');
         $buttonBgColor = bravepop_generate_style_props(isset($this->data->buttonBgColor) ? $this->data->buttonBgColor : '', 'background-color', '109, 120, 216', '1');
         $formLinkColor = bravepop_generate_style_props(isset($this->data->linkColor) ? $this->data->linkColor : '', 'color', '109, 120, 216', '1');
         $formTitleColor = bravepop_generate_style_props(isset($this->data->titleColor) ? $this->data->titleColor : '', 'color', '109, 120, 216', '1');
         $labelFontSize =  isset($this->data->labelFontSize) ?  'font-size: '.$this->data->labelFontSize.'px;' : '';
         $buttonBold =  isset($this->data->buttonBold) ?  'font-weight: bold;' : '';


         $elementInnerStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap{'. $fontFamily . $textColor . $backgroundColor . $fontSize.  '}';
         $elementLinkStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap a{'. $formLinkColor .'}';
         $elementLabelStyle = $labelFontSize ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap label{'. $labelFontSize.  '}' : '';

         $elementInputStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap input{'. $inputFontSize . $inputColor . $borderColor . $fieldBgColor . $roundNess.  '}';
         $elementButtonStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap button{'. $buttonFontSize . $buttonTextColor . $buttonBgColor . $roundNess. $buttonBold.  '}';
         $elementButtonStyle .= !empty($this->data->social) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap .bravepop_login_socialLogin_button{'. $buttonFontSize . $roundNess. $buttonBold.  '}' : '';
         $elementButtonStyle .= !empty($this->data->social) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap .bravepop_login_socialLogin_button--email{'. $buttonBgColor . $buttonTextColor.  '}' : '';
         $elementButtonStyle .= !empty($this->data->social) ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap .brave_wpLogin_social_goBack{'. $buttonTextColor.  '}' : '';


         $elementTitleStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_wpLogin__wrap h3{'. $formTitleColor .'}';

         return  $elementInnerStyle .$elementInputStyle .$elementButtonStyle .$elementLinkStyle . $elementTitleStyle. $elementLabelStyle;

      }

      public function enqueue_social_login_js( ) {
         $socialEnabled = array();
         $currentSettings = get_option('_bravepopup_settings');
         $integrations = $currentSettings && isset($currentSettings['integrations']) ? $currentSettings['integrations'] : array() ;
         $fbAppID = isset($integrations['facebook']) && isset($integrations['facebook']->api) ? $integrations['facebook']->api : ''; 
         $googleClientID = isset($integrations['google']) && isset($integrations['google']->api) ? $integrations['google']->api : ''; 
         $linkedInClientID = isset($integrations['linkedin']) && isset($integrations['linkedin']->api) ? $integrations['linkedin']->api : '';

         $social_signup_buttons = isset($this->data->social_signup_buttons) ? $this->data->social_signup_buttons :$this->default_social_buttons('signup');
         $social_login_buttons = isset($this->data->social_login_buttons) ? $this->data->social_login_buttons : $this->default_social_buttons('login');
         foreach ($social_login_buttons as $key => $item) {  if(!empty($item->enabled)){ $socialEnabled[$item->type] = true;}  }
         foreach ($social_signup_buttons as $key => $item) {  if(!empty($item->enabled)){ $socialEnabled[$item->type] = true;}  }

         if ( !is_admin() ) {
            $vars = array('errors'=>array());

            if(isset($socialEnabled['facebook']) && $fbAppID){
               $vars['facebook_app_id'] = $fbAppID;
               $vars['errors']['facebook'] = __('Sorry, Could not Connect to your Facebook Account.','bravepop');
               wp_enqueue_script('bravepop_facebook_login_js', 'https://connect.facebook.net/en_US/sdk.js#version=v9.0&appId='.$fbAppID.'&cookie=true&xfbml=true');
            }
            if(isset($socialEnabled['google']) && $googleClientID){
               $vars['google_client_id'] = $googleClientID;
               $vars['errors']['google'] = __('Sorry, Could not Connect to your Google Account.','bravepop');
               wp_enqueue_script('bravepop_google_login_js', 'https://apis.google.com/js/api:client.js');
            }
            if(isset($socialEnabled['linkedin']) && $linkedInClientID){
               $vars['linkedin_client_id'] = $linkedInClientID;
               $vars['security'] = wp_create_nonce('brave-linkedin-nonce');
               $vars['ajaxURL'] = esc_url(admin_url( 'admin-ajax.php' ));
               $vars['errors']['linkedin'] = __('Sorry, Could not Connect to your LinkedIn Account.','bravepop');
               $vars['linkedin_rediret_url'] = urlencode(esc_url( home_url( '/' ) ).'?brave_linkedin_auth');
            }
            
            wp_register_script( 'bravepop_social_login_js', BRAVEPOP_PLUGIN_PATH . 'assets/frontend/social_login.js' ,'','',true);
            wp_localize_script( 'bravepop_social_login_js', 'brave_social_global', $vars );
            wp_enqueue_script('bravepop_social_login_js');
         }
      }

      public function renderRegisterForm( ) { 
         $login = isset($this->data->login) ? $this->data->login : true;
         $hideUsername = isset($this->data->hideusername) ? $this->data->hideusername : false;
         $registrationPlaceholder = isset($this->data->registrationPlaceholder) ? $this->data->registrationPlaceholder : true;
         $regiTitle = isset($this->data->registrationTitle) ? $this->data->registrationTitle : 'Create Account';
         $regiSubtitle = isset($this->data->registrationSubtitle) ? $this->data->registrationSubtitle : 'Create a new account';
         $emailPlaceHolder = isset($this->data->emailLabel) ? $this->data->emailLabel : 'Email Address';
         $usernamePlaceHolder = isset($this->data->usernameLabel) ? $this->data->usernameLabel : 'Username';
         $passwordPlaceHolder = isset($this->data->passwordlabel) ? $this->data->passwordlabel : 'Password';
         $fnamePlaceHolder = isset($this->data->fnameLabel) ? $this->data->fnameLabel : 'First Name';
         $lnamePlaceHolder = isset($this->data->lnameLabel) ? $this->data->lnameLabel : 'Last Name';
         $regiButtonText = isset($this->data->registrationButtonText) ? $this->data->registrationButtonText : 'Register';
         $redirectURL = isset($this->data->redirect) ? esc_url($this->data->redirect) : esc_url( home_url('/') );
         $social = !empty($this->data->social) ? true : false;
         $social_buttons = isset($this->data->social_signup_buttons) ? $this->data->social_signup_buttons : $this->default_social_buttons('signup');
         $hasSocial = $social && is_array($social_buttons) && count($social_buttons) > 0 ? true : false;

         $emailLabel =  isset($this->data->emailLabel) ? '<label>'.$this->data->emailLabel.'</label>': '<label>Email Address</label>';
         $usernameLabel =  isset($this->data->usernameLabel) ? '<label>'.$this->data->usernameLabel.'</label>': '<label>Username</label>';
         $passwordlabel =  isset($this->data->passwordlabel) ? '<label>'.$this->data->passwordlabel.'</label>' : '<label>Password</label>';
         $fnameLabel =  isset($this->data->fnameLabel) ? '<label>'.$this->data->fnameLabel.'</label>': '<label>First Name</label>';
         $lnameLabel =  isset($this->data->lnameLabel) ? '<label>'.$this->data->lnameLabel.'</label>' : '<label>Last Name</label>';
         $buttonIconColor = isset($this->data->buttonTextColor) && isset($this->data->buttonTextColor->rgb) ? 'rgb('.$this->data->buttonTextColor->rgb.')' : '#fff';
         $reloadIcon  = '<span id="brave_signup_loading_'.$this->data->id.'" class="brave_login_loading">'.bravepop_renderIcon('reload', $buttonIconColor).'</span>';
         $showRegisterClass = (isset($this->data->registration) && $this->data->registration === false || $login === true) ? '' : 'brave_wpLogin__formWrap--show';
         $socialClass = $hasSocial ? ' brave_wpLogin__formWrap--hideForm':'';
         if(!empty($this->data->registrationFirst)){   $showRegisterClass = 'brave_wpLogin__formWrap--show'; }


         if($registrationPlaceholder === true){ $emailLabel = ''; $passwordlabel = '';  $usernameLabel = ''; $fnameLabel = '';  $lnameLabel = '';}
         if($registrationPlaceholder === false){ $emailPlaceHolder = ''; $passwordPlaceHolder = ''; $usernamePlaceHolder = ''; $fnamePlaceHolder = '';  $lnamePlaceHolder = '';}

         $registrationHTML = '';

         $registrationHTML .= '<div id="brave_wpLogin__regsiter_'.$this->data->id.'" class="brave_wpLogin__registerForm brave_wpLogin__formWrap '.$showRegisterClass.$socialClass.'">';
         $registrationHTML .= $regiTitle ? '<h3>'.$regiTitle.'</h3>' : '';
         $registrationHTML .= $regiSubtitle ? '<div class="loginSubtitle">'.$regiSubtitle.'</div>' : '';
         $registrationHTML .=  $hasSocial ? $this->renderSocialLogins('signup') :'';
         $socialBackButton = $hasSocial ? '<span class="brave_wpLogin_social_goBack" onclick="brave_social_login_goBack( \''.$this->data->id.'\');">'.bravepop_renderIcon('arrow-left', $buttonIconColor).'</span>' :'';

         $registrationHTML .= '<form id="brave_register_form'.$this->data->id.'" action="register" method="post" onsubmit="brave_signupUser(event, \''.$this->data->id.'\', '.$this->popupID.', '.$this->stepIndex.');">';
            $registrationHTML .= '<div class="brave_wpLogin__registerForm__name">';
               $registrationHTML .= '<div class="brave_wpLogin__registerForm__name__first">'.$fnameLabel .'<input id="brave_signup_fname_'.$this->data->id.'" type="text" placeholder="'. $fnamePlaceHolder .'" /></div>';
               $registrationHTML .= '<div class="brave_wpLogin__registerForm__name__last">'.$lnameLabel .'<input id="brave_signup_lname_'.$this->data->id.'" type="text" placeholder="'. $lnamePlaceHolder .'" /></div>';
            $registrationHTML .= '</div>';
            $registrationHTML .= $hideUsername ? '' : '<p>'.$usernameLabel .'<input id="brave_signup_username_'.$this->data->id.'" type="text" placeholder="'. $usernamePlaceHolder .'" /></p>';
            $registrationHTML .= '<p>'.$emailLabel .'<input id="brave_signup_email_'.$this->data->id.'" type="email" placeholder="'. $emailPlaceHolder .'" /></p>';
            $registrationHTML .= '<p>'.$passwordlabel .'<input id="brave_signup_pass_'.$this->data->id.'" type="password" placeholder="'.$passwordPlaceHolder.'" /></p>';
            $registrationHTML .= wp_nonce_field( 'brave-ajax-signup-nonce', 'brave_signup_security'.$this->data->id );
            $registrationHTML .= '<input id="brave_signup_redirect_'.$this->data->id.'" type="hidden" name="brave_redirect" value="'.$redirectURL.'" />';
            $registrationHTML .= '<input id="brave_signup_ajaxURL_'.$this->data->id.'" type="hidden" name="brave_ajaxurl" value="'.esc_url(admin_url( 'admin-ajax.php' )).'" />';
            $registrationHTML .= '<p class="brave_wpLogin__button_wrap">'.$socialBackButton.'<button class="brave_wpLogin__button">'.$reloadIcon . $regiButtonText.'</button></p>';
            $registrationHTML .= $login ? '<div class="brave_wpLogin__registerForm__footer"><div class="brave_wpLogin__registerForm__login"><span>'.__('Have an account?', 'bravepop').'</span> <a onclick="brave_switch_loginForm( \''.$this->data->id.'\', \'login\');">'.__('Login', 'bravepop').'</a></div></div>' : '';
         $registrationHTML .= '</form>';

         $registrationHTML .= $hasSocial && $login ? '<div class="brave_wpLogin_social_message brave_wpLogin_social_message--signup"><span>'.__('Have an account?', 'bravepop').'</span> <a onclick="brave_switch_loginForm( \''.$this->data->id.'\', \'login\');">'.__('Login', 'bravepop').'</a></div>' : '';

         $registrationHTML .= '</div>';

         return $registrationHTML;
      }
      

      public function renderLoginForm( ) { 
         $registration = isset($this->data->registration) ? $this->data->registration : true;
         $loginPlaceholder = isset($this->data->loginPlaceholder) ? $this->data->loginPlaceholder : false;
         $loginTitle = isset($this->data->loginTitle) ? $this->data->loginTitle : 'Login';
         $loginSubtitle = isset($this->data->loginSubtitle) ? $this->data->loginSubtitle : 'Login to your account';
         $emailPlaceHolder = isset($this->data->emailLabel) ? $this->data->emailLabel : 'Email Address';
         $passwordPlaceHolder = isset($this->data->passwordlabel) ? $this->data->passwordlabel : 'Password';
         $loginButtonText = isset($this->data->loginButtonText) ? $this->data->loginButtonText : 'Login';
         $redirectURL = isset($this->data->redirect) ? esc_url($this->data->redirect) : '';
         $social = !empty($this->data->social) ? true : false;
         $social_buttons = isset($this->data->social_login_buttons) ? $this->data->social_login_buttons : $this->default_social_buttons('login');
         $hasSocial = $social && is_array($social_buttons) && count($social_buttons) > 0 ? true : false;


         $emailLabel =  isset($this->data->emailLabel) ? '<label>'.$this->data->emailLabel.'</label>': '<label>Email Address</label>';
         $passwordlabel =  isset($this->data->passwordlabel) ? '<label>'.$this->data->passwordlabel.'</label>' : '<label>Password</label>';
         $registrationButton = $registration ? '<div class="brave_wpLogin__loginForm__register"><a onclick="brave_switch_loginForm( \''.$this->data->id.'\', \'register\');">'.__('Register', 'bravepop').'</a></div>' : '';
         $buttonIconColor = isset($this->data->buttonTextColor) && isset($this->data->buttonTextColor->rgb) ? 'rgb('.$this->data->buttonTextColor->rgb.')' : '#fff';
         $reloadIcon  = '<span id="brave_login_loading_'.$this->data->id.'" class="brave_login_loading">'.bravepop_renderIcon('reload', $buttonIconColor).'</span>';
         $showLoginClass = isset($this->data->login) && $this->data->login === false ? '' : 'brave_wpLogin__formWrap--show'; 
         $socialClass = $hasSocial ? ' brave_wpLogin__formWrap--hideForm':'';
         if(!empty($this->data->registrationFirst)){   $showLoginClass = '';  }

         if($loginPlaceholder === true){ $emailLabel = ''; $passwordlabel = '';}
         if($loginPlaceholder === false){ $emailPlaceHolder = ''; $passwordPlaceHolder = '';}
         //error_log(json_encode($social_buttons));
         $loginHTML = '';

         $loginHTML .= '<div id="brave_wpLogin__login_'.$this->data->id.'" class="brave_wpLogin__loginForm brave_wpLogin__formWrap '.$showLoginClass.$socialClass.'">';
         $loginHTML .= $loginTitle ? '<h3>'.$loginTitle.'</h3>' : '';
         $loginHTML .= $loginSubtitle ? '<div class="loginSubtitle">'.$loginSubtitle.'</div>' : '';
         $loginHTML .= $hasSocial ? $this->renderSocialLogins('login') :'';
         $socialBackButton = $hasSocial ? '<span class="brave_wpLogin_social_goBack" onclick="brave_social_login_goBack( \''.$this->data->id.'\');">'.bravepop_renderIcon('arrow-left', $buttonIconColor).'</span>' :'';
         $loginHTML .= '<form id="brave_login_form-'.$this->data->id.'" action="login" method="post" onsubmit="brave_loginUser(event, \''.$this->data->id.'\', '.$this->popupID.');">';
            $loginHTML .= '<p>'.$emailLabel .'<input id="brave_login_email_'.$this->data->id.'" type="email" name="brave_email" placeholder="'. $emailPlaceHolder .'" /></p>';
            $loginHTML .= '<p>'.$passwordlabel .'<input id="brave_login_pass_'.$this->data->id.'" type="password" name="brave_password" placeholder="'.$passwordPlaceHolder.'" /></p>';
            $loginHTML .= wp_nonce_field( 'brave-ajax-login-nonce', 'brave_login_security'.$this->data->id );
            $loginHTML .= '<input id="brave_login_redirect_'.$this->data->id.'" type="hidden" name="brave_redirect" value="'.$redirectURL.'" />';
            $loginHTML .= '<input id="brave_login_ajaxURL_'.$this->data->id.'" type="hidden" name="brave_ajaxurl" value="'.esc_url(admin_url( 'admin-ajax.php' )).'" />';
            $loginHTML .= '<p class="brave_wpLogin__button_wrap">'.$socialBackButton.'<button class="brave_wpLogin__button">'.$reloadIcon . $loginButtonText.'</button></p>';
            $loginHTML .= '<div class="brave_wpLogin__loginForm__footer"><div class="brave_wpLogin__loginForm__forgot"><a onclick="brave_switch_loginForm( \''.$this->data->id.'\', \'resetpass\');">'.__('Forgot Password?', 'bravepop').'</a></div>'.$registrationButton.'</div>';
         $loginHTML .= '</form>';

         $loginHTML .= $hasSocial && $registration ? '<div class="brave_wpLogin_social_message brave_wpLogin_social_message--login"><a onclick="brave_switch_loginForm( \''.$this->data->id.'\', \'register\');">'.__('Create Account', 'bravepop').'</a></div>' : '';

         $loginHTML .= '</div>';

         return $loginHTML;
      }

      
      public function renderPasswordReset( ) { 
         $registration = isset($this->data->registration) ? $this->data->registration : true;
         $login = isset($this->data->login) ? $this->data->login : true;
         $resetPlaceholder = isset($this->data->resetPlaceholder) ? $this->data->resetPlaceholder : false;
         $resetTitle = isset($this->data->resetTitle) ? $this->data->resetTitle : 'Reset Password';
         $resetSubtitle = isset($this->data->resetSubtitle) ? $this->data->resetSubtitle : '';
         $emailPlaceHolder = isset($this->data->emailLabel) ? $this->data->emailLabel : 'Email Address';
         $resetButtonText = isset($this->data->resetButtonText) ? $this->data->resetButtonText : 'Reset Password';
         
         $emailLabel =  isset($this->data->emailLabel) ? '<label>'.$this->data->emailLabel.'</label>': '<label>Email Address</label>';
         $registrationButton = $registration ? '<div class="brave_wpLogin__loginForm__register"><a onclick="brave_switch_loginForm(\''.$this->data->id.'\', \'register\');">'.__('Register', 'bravepop').'</a></div>' : '';
         $LoginButton = $login ? '<div class="brave_wpLogin__loginForm__forgot"><a onclick="brave_switch_loginForm(\''.$this->data->id.'\', \'login\');">'.__('Login', 'bravepop').'</a></div>' : '';
         $buttonIconColor = isset($this->data->buttonTextColor) && isset($this->data->buttonTextColor->rgb) ? 'rgb('.$this->data->buttonTextColor->rgb.')' : '#fff';
         $reloadIcon  = '<span id="brave_resetpass_loading_'.$this->data->id.'" class="brave_login_loading">'.bravepop_renderIcon('reload', $buttonIconColor).'</span>';

         if($resetPlaceholder === true){ $emailLabel = ''; }
         if($resetPlaceholder === false){ $emailPlaceHolder = ''; }

         $resetHTML = '';

         $resetHTML .= '<div id="brave_wpLogin__reset_'.$this->data->id.'" class="brave_wpLogin__passwordResetForm brave_wpLogin__formWrap">';
         $resetHTML .= $resetTitle ? '<h3>'.$resetTitle.'</h3>' : '';
         $resetHTML .= $resetSubtitle ? '<div class="resetSubtitle">'.$resetSubtitle.'</div>' : '';
         $resetHTML .= '<form id="brave_resetpass_form'.$this->data->id.'" action="resetpass" method="post" onsubmit="brave_resetPass(event, \''.$this->data->id.'\', '.$this->popupID.');">';
            $resetHTML .= '<p>'.$emailLabel .'<input id="brave_resetpass_email_'.$this->data->id.'" type="email" placeholder="'. $emailPlaceHolder .'" /></p>';
            $resetHTML .= '<p><button class="brave_wpLogin__button">'.$reloadIcon . $resetButtonText.'</button></p>';
            $resetHTML .= wp_nonce_field( 'brave-ajax-resetpass-nonce', 'brave_resetpass_security'.$this->data->id );
            $resetHTML .= '<input id="brave_resetpass_ajaxURL_'.$this->data->id.'" type="hidden" name="brave_ajaxurl" value="'.esc_url(admin_url( 'admin-ajax.php' )).'" />';
            $resetHTML .= '<div class="brave_wpLogin__loginForm__footer">'.$LoginButton .$registrationButton.'</div>';
         $resetHTML .= '</form>';
         $resetHTML .= '</div>';

         return $resetHTML;
      }


      protected function default_social_buttons($type='login'){
         $buttons = array();
         $services = array('facebook', 'google', 'linkedin', 'email');
         foreach ($services as $key => $item) {
            $finalItem = new stdClass();
            $finalItem->enabled = true;
            $finalItem->label = ucwords($type === 'signup' ? 'Signup with ' : 'Login with ' ).$item;
            $finalItem->type = $item;
            $buttons[] = $finalItem;
         }
         return $buttons;
      }
      
      protected function renderSocialLogins($type='login'){
         $social_login__buttons = isset($this->data->social_login_buttons) ? $this->data->social_login_buttons : $this->default_social_buttons('login');
         $social_signup__buttons = isset($this->data->social_signup_buttons) ? $this->data->social_signup_buttons : $this->default_social_buttons('signup');

         $social_settings = $type === 'signup' ? $social_signup__buttons : $social_login__buttons;
         $socHTML = '<div class="bravepop_login_socialLogin"  id="bravepop_'.$type.'_socialLogin-'.$this->data->id.'">';
         $socHTML  .= '<div class="brave_social_login_overlay"><div class="brave_social_login_loading_wrap"><span class="brave_social_login_loading">'.bravepop_renderIcon('reload', '#fff').'</span></div></div>';
            $socHTML .= '<div class="bravepop_login_socialLogin_inner">';
               foreach ($social_settings as $key => $item) {  
                  if(!empty($item->enabled)){ 
                     $icons = array(
                        'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="20" height="16"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" fill="#fff"/></svg>',
                        'google' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="16"><path d="M881 442.4H519.7v148.5h206.4c-8.9 48-35.9 88.6-76.6 115.8c-34.4 23-78.3 36.6-129.9 36.6c-99.9 0-184.4-67.5-214.6-158.2c-7.6-23-12-47.6-12-72.9s4.4-49.9 12-72.9c30.3-90.6 114.8-158.1 214.7-158.1c56.3 0 106.8 19.4 146.6 57.4l110-110.1c-66.5-62-153.2-100-256.6-100c-149.9 0-279.6 86-342.7 211.4c-26 51.8-40.8 110.4-40.8 172.4S151 632.8 177 684.6C240.1 810 369.8 896 519.7 896c103.6 0 190.4-34.4 253.8-93c72.5-66.8 114.4-165.2 114.4-282.1c0-27.2-2.4-53.3-6.9-78.5z" fill="#fff" /><rect x="0" y="0" width="1024" height="1024" fill="transparent" /></svg>',
                        'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20" height="16"><path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z" fill="#fff"/></svg>',
                        'email' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="16"><path d="M20.572 5.083l-7.896 7.037a1 1 0 0 1-1.331 0L3.416 5.087A2 2 0 0 1 4 5h16a2 2 0 0 1 .572.083zm1.356 1.385c.047.17.072.348.072.532v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 .072-.534l7.942 7.148a3 3 0 0 0 3.992 0l7.922-7.146z" fill="#fff"/><rect x="0" y="0" width="24" height="24" fill="transparent" /></svg>'
                     );

                     $socHTML .= '<a id="bravepop_login_socialLogin_button-'.$this->data->id.'" class="bravepop_login_socialLogin_button bravepop_'.$type.'_social_button bravepop_login_socialLogin_button--'.$item->type.'" onclick="bavepop_social_login(\''.$item->type.'\', \''.$this->data->id.'\', '.$this->popupID.', \''.$type.'\')" data-id="'.$this->data->id.'" data-popupid="'.$this->popupID.'"><span>'.$icons[$item->type].'</span> '.(isset($item->label) ? $item->label : '').'</a>';
                  }  
               }
            $socHTML .= '</div>';
         $socHTML .= '</div>';

         return $socHTML;
      }

      public function render( ) { 
         $content = isset($this->data->content) ? $this->data->content : '';
         $closeIcon = bravepop_renderIcon('close', '');
         $registrationFirst = !empty($this->data->registrationFirst) ? true: false;
         $loginForm = isset($this->data->login) && $this->data->login === false  ? '': $this->renderLoginForm();
         $registerForm = isset($this->data->registration) && $this->data->registration === false  ? '': $this->renderRegisterForm();
         $passResetForm = isset($this->data->login) && $this->data->login === false  ? '': $this->renderPasswordReset();
         $theForm = $loginForm . $registerForm . $passResetForm;


         if ( is_user_logged_in() ) {
            $theForm = '<div id="brave_wpLogin__logout_'.$this->data->id.'"class="brave_wpLogin__logout"><p>'.__('You are already Logged In.', 'bravepop').' <a href="'.wp_logout_url(esc_url( home_url('/'))).'">'.__('Logout', 'bravepop').'</a></p></div>';
         }

         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--wpLogin">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div class="brave_wpLogin">
                              <div class="brave_wpLogin__wrap">
                              '.$theForm.'
                              <div id="brave_wpLogin_error_'.$this->data->id.'" class="brave_wpLogin_error">
                                 <span onclick="brave_close_loginError(\''.$this->data->id.'\');">'.$closeIcon.'</span>
                                 <div id="brave_wpLogin_error_content_'.$this->data->id.'" class="brave_wpLogin_error__inner"></div>
                              </div>
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