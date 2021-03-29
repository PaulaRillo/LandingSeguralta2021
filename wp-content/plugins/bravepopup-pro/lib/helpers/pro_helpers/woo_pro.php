<?php

function bravepop_get_cart_data( ){
   $cartData = new stdClass();
   $cartData->count = 0;
   $cartData->value = 0;
   $cartData->productIDs = array();
   $cartData->products = array();

   if(function_exists('WC')){
      $woocommerce = WC();
      if(!$woocommerce->cart->is_empty()){
         $cartItems = $woocommerce->cart->get_cart();
         $cartData->value = (float)$woocommerce->cart->get_cart_contents_total();
         $cartData->count = is_array($cartItems) ? count($cartItems) : 0;

         foreach ( $cartItems as $cart_item_key => $cart_item ) {
            if(isset($cart_item['product_id'])){   $cartData->productIDs[] = $cart_item['product_id'];  }
            //if(isset($cart_item->productIDs)){   $cartData->products[] = $cart_item;  }
         }
      }
   }
   return $cartData;
}

function bravepop_woo_cart_filter($wooFilters){
   $cartFilter = new stdClass();
   $cartFilter->cart_includes =  true;
   $cartFilter->cart_excludes =  true;
   $cartFilter->cart_value =  true;

   if(!isset($GLOBALS['bravepop_cart_data'])){   $GLOBALS['bravepop_cart_data'] = bravepop_get_cart_data();  }
   if(isset($GLOBALS['bravepop_cart_data'])){
      $cartData = $GLOBALS['bravepop_cart_data'];
      $cartIncludesMatch = false; $cartExcludesMatch = false;
      if(isset($cartData->productIDs) && isset($wooFilters->cart_includes) && is_array($wooFilters->cart_includes) && count($wooFilters->cart_includes) > 0){
         $cartIncludesIntersect = array_intersect($cartData->productIDs, $wooFilters->cart_includes);
         if(is_array($cartIncludesIntersect) && count($cartIncludesIntersect) === 0){
            $cartIncludesMatch = true; 
            $cartFilter->cart_includes = false;
         }
      }
      if(isset($cartData->productIDs) && isset($wooFilters->cart_excludes) && is_array($wooFilters->cart_excludes) && count($wooFilters->cart_excludes) > 0){
         $itemstoExlcude = array();
         $difference = array_diff($wooFilters->cart_excludes, $cartData->productIDs);
         if(count($difference) === 0){
            $cartFilter->cart_excludes = false;
         }else{
            $cartFilter->cart_excludes  = true;
         }
      }   
   }

   return $cartFilter;
}

function bravepop_woo_purchase_filter($wooFilters){
   $purchaseFilter = new stdClass();
   $purchaseFilter->purchased =  true;
   $purchaseFilter->notpurchased = true;
   $current_user = wp_get_current_user();
   $userID = isset($current_user->data->ID) ? $current_user->data->ID :'';
   $customer_email = isset($current_user->data->user_email) ? $current_user->data->user_email :'';

   if($userID && $customer_email){
      if(isset($wooFilters->purchased) && is_array($wooFilters->purchased) && count($wooFilters->purchased) > 0 && function_exists('wc_customer_bought_product')){
         //$purchaseFilter->purchased =  false;
         foreach ($wooFilters->purchased as $key => $productID) {
            $hasProduct = wc_customer_bought_product($customer_email, $userID, (int)$productID);
            if($purchaseFilter->purchased && !$hasProduct){
               $purchaseFilter->purchased =  false;
            }
         }
      }
      if(isset($wooFilters->notpurchased) && is_array($wooFilters->notpurchased) && count($wooFilters->notpurchased) > 0 && function_exists('wc_customer_bought_product')){
         //$purchaseFilter->notpurchased = false;
         foreach ($wooFilters->notpurchased as $key => $productID) {
            $hasProduct = wc_customer_bought_product($customer_email, $userID, (int)$productID);
            if($purchaseFilter->purchased && $hasProduct){
               $purchaseFilter->notpurchased =  false;
            }
         }
      }
   }


   return $purchaseFilter;
}
