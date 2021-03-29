<?php
function bravepop_send_goal_notification($popupID, $pageURL, $goalType='step', $country='', $city='', $goalUTCTime){

   //Send Goal Notification
   $popupData = json_decode(get_post_meta($popupID, 'popup_data', true));
   $popupTItle = get_the_title($popupID);
   $siteTitle = get_bloginfo('name'); 
   $current_user = bravepop_getCurrentUser();
   $userType = 'Visitor';
   if(!empty($current_user['username'])){
      $userType = 'Registered User'.'';
      if(!empty($current_user['name']) || !empty($current_user['username'])){
         $userType .= ': '.$current_user['name'].' ('.$current_user['username'].')';
      }else if(empty($current_user['name']) || !empty($current_user['username'])){
         $userType .= ': '.$current_user['username'];
      }
   }

   //Incorporate Field Settings with Given Value
   $notificationSettings = isset($popupData->settings->notification) ? $popupData->settings->notification : new stdClass();

   //Email Notification
   if(isset($notificationSettings->email) && isset($notificationSettings->emailAddresses) && $notificationSettings->email && $notificationSettings->emailAddresses){
      $goalAction = 'Viewed';
      if($goalType ==='click'){ $goalAction = 'Clicked'; }
      if($goalType === 'form'){ $goalAction = 'Submitted a Form in'; }
      $from = ''; $cityVerb = '';
      if($city){ $cityVerb = $city.', ';}

      if($city || $country){
         $from = 'from '.$cityVerb.$country;
      }

      $sendto =  $notificationSettings->emailAddresses;
      $subject = '['.$siteTitle.'] Someone Just Completed a Campaign Goal';
      $headers = "Content-Type: text/plain; charset=\"iso-8859-1\"";
      $theMessage= 'A '.$userType.' '.$from.' '.$goalAction.' Your Campaign: '.$popupTItle. ' on page: '.$pageURL;
      wp_mail( $sendto, $subject, $theMessage, $headers);
   }


   //Zapier Notification
   if(isset($notificationSettings->zapier) && isset($notificationSettings->zapierURL) && $notificationSettings->zapier && $notificationSettings->zapierURL){

      $goalAction = 'View';
      if($goalType ==='click'){ $goalAction = 'Click'; }
      if($goalType === 'form'){ $goalAction = 'Form'; }

      $data = new stdClass();
      $data->user_type = $userType;
      $data->user_country = $country;
      $data->user_city = $city;
      $data->action_type = $goalAction;
      $data->action_time = $goalUTCTime;
      $data->popup_name = $popupTItle;
      $data->popup_id = $popupID;
      $data->pageURL = $pageURL;

      $args = array( 'method' => 'POST',  'headers' => array(    'content-type' => 'application/json',  ), 'body' => json_encode($data) );
      return wp_remote_post( $notificationSettings->zapierURL, $args );
   }


}