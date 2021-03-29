<?php
if ( ! class_exists( 'BravePop_TNP' ) ) {

   class BravePop_TNP {


      public function get_lists(){
         if(class_exists('TNP') && class_exists('Newsletter')){
            $lists = get_option( 'newsletter_subscription_lists', array() );

            //error_log('TNP Lists: '.json_encode($lists));
            $noListItem = new stdClass();
            $noListItem->id = 'no_list';
            $noListItem->name = 'No List'; 
            $finalLists = array($noListItem);

            for ($i=0; $i < 41; $i++) { 
               # code...
               if(!empty($lists["list_$i"]) && $lists["list_".$i."_status"] === '1'){
                  $listItem = new stdClass();
                  $listItem->id =  $i;
                  $listItem->name = !empty($lists["list_$i"]) ? $lists["list_$i"]: ''; 
                  $listItem->count =  0;
                  $finalLists[] = $listItem;
               }

            }

            //error_log(json_encode($finalLists));
            return json_encode($finalLists);
         }
         
      }


      public function add_to_lists($email, $list_id, $fname='', $lname='', $phone=''){
         if(!class_exists('TNP')){ return null; }
         if(!$email){ return null; }

         $firstname = trim($fname);
         $lastname = trim($lname);

         // Check if subscriber exists. If subscriber doesn't exist an exception is thrown
         // $get_subscriber = '';
         // try {
         //    $get_subscriber = $this->api_key->getSubscriber($subscriber['email']);
         // } catch (\Exception $e) {}
         $theList = array();
         if($list_id && $list_id !== 'no_list'){
            $theList[] = (int)$list_id;
         }

         //error_log(json_encode($theList));

         $userAdded = TNP::subscribe(['email'=> $email, 'name'=> trim($firstname), 'lists' => $theList, 'send_emails'=> true]);

         if(is_wp_error($userAdded)){
            return false;
         }else{
            return true;
         }

      }


   }

}
?>