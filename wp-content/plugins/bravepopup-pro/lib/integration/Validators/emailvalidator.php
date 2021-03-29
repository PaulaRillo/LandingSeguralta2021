<?php

include __DIR__ . '/BraveMailValidator.php';
include __DIR__ . '/NeverBounce.php';
include __DIR__ . '/ZeroBounce.php';
include __DIR__ . '/TrueMail.php';


add_action('wp_ajax_bravepopup_validate_email', 'bravepopup_validate_email', 0);
add_action('wp_ajax_nopriv_bravepopup_validate_email', 'bravepopup_validate_email');
function bravepopup_validate_email(){
   
   if(class_exists( 'BravePop_MailValidator' ) ){
      if(!isset($_POST['formData']) || !isset($_POST['security'])){ wp_die(); }
      check_ajax_referer('brave-ajax-form-nonce', 'security');

      $formData = json_decode(stripslashes($_POST['formData']));

      if(is_array($formData)){
         $settings = get_option('_bravepopup_settings');
         $validatorType = isset($settings['emailvalidator']->active) && $settings['emailvalidator']->active !== 'disabled' ? $settings['emailvalidator']->active : false;
         $disposableCheck = isset($settings['emailvalidator']->disposable) && $settings['emailvalidator']->disposable === false ? false : true;
         $mxCheck = isset($settings['emailvalidator']->active) && $settings['emailvalidator']->active === false ? false : true;
         $preventFree = isset($settings['emailvalidator']->preventfree) && $settings['emailvalidator']->preventfree === true ? true : false;
         $suggestionCheck = isset($settings['emailvalidator']->suggestion) && $settings['emailvalidator']->suggestion === false ? false : true;
         if($validatorType){
            foreach ($formData as $index => $field) {
               $email = $field->value;
               if($email && filter_var($email, FILTER_VALIDATE_EMAIL)){
                  if($validatorType === 'brave' && class_exists('BravePop_MailValidator') ){   $validator = new BravePop_MailValidator($email, $mxCheck, $disposableCheck, $suggestionCheck, $preventFree);  }
                  if($validatorType === 'neverbounce' && class_exists('BravePop_NeverBounce') ){   $validator = new BravePop_NeverBounce($suggestionCheck);  }
                  if($validatorType === 'zerobounce' && class_exists('BravePop_ZeroBounce') ){   $validator = new BravePop_ZeroBounce($suggestionCheck);  }
                  if($validatorType === 'truemail' && class_exists('BravePop_TrueMail') ){   $validator = new BravePop_TrueMail($suggestionCheck);  }
                  if($validator){
                     $validationData = $validator->validate_email($email);
                     $formData[$index]->validation = $validationData;
                     //error_log(json_encode($validationData));
                  }
               }
            }
         }

      }

      echo json_encode($formData);

      wp_die();
   }else{
      wp_die();
   }
}