<?php

include __DIR__ . '/Mailjet.php';
include __DIR__ . '/Hubspot.php';
include __DIR__ . '/ActiveCampaign.php';
include __DIR__ . '/ConvertKit.php';
include __DIR__ . '/GetResponse.php';
include __DIR__ . '/Zoho.php';
include __DIR__ . '/MailerLite.php';
include __DIR__ . '/Mailchimp.php';
include __DIR__ . '/AWeber.php';
include __DIR__ . '/Pabbly.php';
include __DIR__ . '/Klaviyo.php';
include __DIR__ . '/Ontraport.php';
include __DIR__ . '/CampaignMonitor.php';
include __DIR__ . '/SendinBlue.php';
include __DIR__ . '/SendGrid.php';
include __DIR__ . '/Moosend.php';


function bravepop_get_advanced_integration_data($service, $listID=''){
   if(!$service){ return false; }
   $currentSettings = get_option('_bravepopup_settings');
   $currentIntegrations = $currentSettings && isset($currentSettings['integrations']) ? $currentSettings['integrations'] : array() ;

   if($service === 'mailjet' && class_exists('BravePop_Mailjet_Advanced'))   { 
      $mailjet =   new BravePop_Mailjet_Advanced();
      $lists = $mailjet->get_fields();
      return $lists;
   }
   if($service === 'mailerlite' && class_exists('BravePop_MailerLite_Advanced'))   { 
      $mailerlite =   new BravePop_MailerLite_Advanced();
      $lists = $mailerlite->get_fields();
      return $lists;
   }
   if($service === 'activecampaign' && class_exists('BravePop_ActiveCampaign_Advanced'))   { 
      $activecampaign =   new BravePop_ActiveCampaign_Advanced();
      $lists = $activecampaign->get_fields();
      return $lists;
   }
   if($service === 'convertkit' && class_exists('BravePop_ConvertKit_Advanced'))   { 
      $convertkit =   new BravePop_ConvertKit_Advanced();
      $lists = $convertkit->get_fields();
      return $lists;
   }
   if($service === 'getresponse' && class_exists('BravePop_GetResponse_Advanced'))   { 
      $convertkit =   new BravePop_GetResponse_Advanced();
      $lists = $convertkit->get_fields();
      return $lists;
   }
   if($service === 'hubspot' && class_exists('BravePop_Hubspot_Advanced'))   { 
      $hubspot =   new BravePop_Hubspot_Advanced();
      $lists = $hubspot->get_fields();
      return $lists;
   }
   if($service === 'zoho' && class_exists('BravePop_Zoho_Advanced'))   { 
      $zoho =   new BravePop_Zoho_Advanced();
      $lists = $zoho->get_fields();
      return $lists;
   }
   if($service === 'pabbly' && class_exists('BravePop_Pabbly_Advanced'))   { 
      $pabbly =   new BravePop_Pabbly_Advanced();
      $lists = $pabbly->get_fields();
      return $lists;
   }
   if($service === 'klaviyo' && class_exists('BravePop_Klaviyo_Advanced'))   { 
      $klaviyo =   new BravePop_Klaviyo_Advanced();
      $lists = $klaviyo->get_fields();
      return $lists;
   }
   if($service === 'ontraport' && class_exists('BravePop_Ontraport_Advanced'))   { 
      $ontraport =   new BravePop_Ontraport_Advanced();
      $lists = $ontraport->get_fields();
      return $lists;
   }
   if($service === 'aweber' && $listID && class_exists('BravePop_Aweber_Advanced'))   { 
      $aweber =   new BravePop_Aweber_Advanced();
      $lists = $aweber->get_fields($listID);
      return $lists;
   }
   if($service === 'campaignmonitor' && $listID && class_exists('BravePop_CampaignMonitor_Advanced'))   { 
      $campaignmonitor =   new BravePop_CampaignMonitor_Advanced();
      $lists = $campaignmonitor->get_fields($listID);
      return $lists;
   }
   if($service === 'mailchimp' && $listID && class_exists('BravePop_Mailchimp_Advanced'))   { 
      $mailchimp =   new BravePop_Mailchimp_Advanced();
      $lists = $mailchimp->get_fields($listID);
      return $lists;
   }
   if($service === 'sendinblue' && class_exists('BravePop_SendinBlue_Advanced'))   { 
      $sendinblue =   new BravePop_SendinBlue_Advanced();
      $lists = $sendinblue->get_fields();
      return $lists;
   }
   if($service === 'sendgrid' && class_exists('BravePop_SendGrid_Advanced'))   { 
      $sendgrid =   new BravePop_SendGrid_Advanced();
      $lists = $sendgrid->get_fields();
      return $lists;
   }
   if($service === 'moosend' && class_exists('BravePop_Moosend_Advanced'))   { 
      $moosend =   new BravePop_Moosend_Advanced();
      $lists = $moosend->get_fields();
      return $lists;
   }
}



function bravepop_newsletter_cookie_conditions($conditions){
   $cookies = array();
   foreach ($conditions as $condIndex => $condValue) {
      if(!empty($condValue->rules) && is_array($condValue->rules) && count($condValue->rules) > 0){
         foreach ($condValue->rules as $ruleIndex => $ruleValue) {
            if($ruleValue->type === 'cookie' && !empty($ruleValue->cookie)){
               $cookies[] = $ruleValue->cookie;
            }
         }
      }
   }
   return implode(" ",$cookies);
}

function bravepop_get_newsletter_customFields($formFields, $customFields, $userQuizData=false){
   if(!$formFields || !$customFields){ return array();}
   $mappedValues = array();
   foreach ((array)$customFields as $fieldIndex => $field) {
      if(!empty($field->key) && !empty($field->id)){
         $fieldID = $field->id; $fieldKey = $field->key;

         if($fieldID === 'quiz_score' && $userQuizData && isset($userQuizData->userScore)){
            $mappedValues[$fieldKey] = $userQuizData->userScore;
         }else if($fieldID === 'quiz_correct' && $userQuizData && isset($userQuizData->userCorrect)){
            $mappedValues[$fieldKey] = $userQuizData->userCorrect;
         }else{
            if(!empty($formFields->{$fieldID}->value)){
               $mappedValues[$fieldKey] = $formFields->{$fieldID}->value;
            }
         }

      }
   }
   //error_log('Mapped Values: '.json_encode($mappedValues));
   return $mappedValues;
}

function bravepop_get_conditional_list($defaultList='', $defaultTags=array(), $formFields, $conditions, $cookies=false, $userQuizData=false){
   $listID = $defaultList; $tags = $defaultTags; $conditionMatched = false;

   if(!$formFields || !$conditions){ return array('list'=> '', 'tags'=> array());}

   foreach ($conditions as $condIndex => $condValue) {
      $condMatch = 0;
      if(!empty($condValue->rules) && is_array($condValue->rules) && !$conditionMatched){
         foreach ($condValue->rules as $ruleIndex => $ruleValue) {

               //FIELD VALUE
               if($ruleValue->type === 'field' && !empty($ruleValue->fieldKey) && !empty($ruleValue->fieldValue) ){
                  $fieldKey = $ruleValue->fieldKey;
                  if(!empty($formFields->{$fieldKey}->value)){
                     $fieldValsArray = explode(",",  $ruleValue->fieldValue);
                     if(is_array($formFields->{$fieldKey}->value)){
                        if(array_intersect($formFields->{$fieldKey}->value, $fieldValsArray)){
                           //error_log('$Field match ');
                           $condMatch = $condMatch+1;
                        }
                     }else{
                        if(in_array($formFields->{$fieldKey}->value, $fieldValsArray)){
                           //error_log('$Field match ');
                           $condMatch = $condMatch+1;
                        }
                     }

                  }
               }
               
               //ROLE TYPE
               if($ruleValue->type === 'role' && !empty($ruleValue->role)){
                  $registered = is_user_logged_in();
                  if(!$registered && $ruleValue->role === 'visitor'){
                     $condMatch = $condMatch+1;
                  }
                  if($registered && $ruleValue->role === 'registered'){
                     //error_log('$registered match ');
                     $condMatch = $condMatch+1;
                  }
                  if($registered && $ruleValue->role !== 'visitor' && $ruleValue->role !== 'registered'){
                     $user = wp_get_current_user(); $roles = ( array ) $user->roles;
                     if(in_array($ruleValue->role, $roles)){
                        $condMatch  = $condMatch+1;
                     }
                  }
               }

               //Cookie Match
               if($ruleValue->type === 'cookie' && !empty($ruleValue->cookie)){
                  $cookieName = $ruleValue->cookie;
                  //error_log('$cookieName: '.json_encode($cookies).' found cookies: '.json_encode($cookies->$cookieName));
                  if(!empty($cookies->$cookieName)){
                     $condMatch = $condMatch+1;
                  }
               }

               //Quiz Match 
               if($ruleValue->type === 'quiz' && !empty($ruleValue->quizFilter) && !empty($ruleValue->quizValue) && $userQuizData){
                  //error_log('$quizValue: '.$ruleValue->quizFilter.':'.($ruleValue->quizValue).' userQuizData: '.json_encode($userQuizData));
                  $quizScore = isset($userQuizData->userScore) ? $userQuizData->userScore : 0;
                  $quizCorrect = isset($userQuizData->userCorrect) ? $userQuizData->userCorrect : 0;
                  $quizVal = $quizScore; 
                  if( strpos(trim($ruleValue->quizValue), 'answer') !== false ){
                      $quizVal = $quizCorrect;   
                  }

                  if(strpos(trim($ruleValue->quizValue), '-') === false ){
                     if(strpos(trim($ruleValue->quizFilter), 'equal') !== false && $quizVal === intval($ruleValue->quizValue)){
                        $condMatch = $condMatch+1;
                     }
                     if(strpos(trim($ruleValue->quizFilter), 'below') !== false && $quizVal < intval($ruleValue->quizValue)){
                        $condMatch = $condMatch+1;
                     }
                     if(strpos(trim($ruleValue->quizFilter), 'above') !== false && $quizVal > intval($ruleValue->quizValue)){
                        $condMatch = $condMatch+1;
                     }
                  }

                  if(($ruleValue->quizFilter === 'quiz_score_between' || $ruleValue->quizFilter === 'quiz_answer_between') && strpos(trim($ruleValue->quizValue), '-') !== false){
                     $scoreRange = explode("-",  trim($ruleValue->quizValue));
                     $startScore = isset($scoreRange[0]) ? intval(trim($scoreRange[0])) : false;
                     $endScore = isset($scoreRange[1]) ? intval(trim($scoreRange[1])) : false;
                     if($startScore !==false && $endScore !==false){
                        if(in_array($quizVal, range($startScore, $endScore))) {
                           $condMatch = $condMatch+1;
                        }
                     }
                  }
  
               }
         }
      }
      if($condMatch === count($condValue->rules) && isset($condValue->listID)){
         $listID = $condValue->listID;
         if(isset($condValue->tags)){
            $tags = $condValue->tags;
            //error_log('$tags: '.json_encode($condValue->tags));
         }
         $conditionMatched = true;
      }
      //error_log('Condition Total Matched'.$condMatch.'/'.count($condValue->rules));
   }
   // error_log('$listID: '.$listID);
   // error_log('$tags: '.json_encode($tags));

   return array('list'=> $listID, 'tags'=>$tags);
}