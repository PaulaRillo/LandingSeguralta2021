<?php

function bravepop_dynamic_woo_mostSold(){
   $mostSoldQuery =  array(
      'limit'     => 5,
      'meta_key' => 'total_sales', 
      'orderby'  => array( 'meta_value_num' => 'DESC', 'title' => 'ASC' ),
   );
   $query    = new WC_Product_Query( $mostSoldQuery );
   $most_soldIDItems = $query->get_products();

   return bravepop_dynamic_woo_loop( $most_soldIDItems );
}

function bravepop_dynamic_woo_upsell(){
   $product = wc_get_product();
   if(!$product){ return array();}
   $upsell_ids = $product->get_upsell_ids();
   if(is_array($upsell_ids) && count($upsell_ids) === 0){
      $upsell_ids = wc_get_related_products( $product->get_id(), 1);
   }
   if($upsell_ids && is_array($upsell_ids) && count($upsell_ids) > 0){
      $upsellProductQuery =  array( 'limit'=> 5, 'include' => $upsell_ids );
      $query    = new WC_Product_Query( $upsellProductQuery );
      $upsellItems = $query->get_products();
      return bravepop_dynamic_woo_loop( $upsellItems );
   }

}

function bravepop_dynamic_woo_crossell(){
   $product = wc_get_product();
   if(!$product){ return array();}
   $crossell_ids = $product->get_cross_sell_ids();

   if(is_array($crossell_ids) && count($crossell_ids) === 0){
      $crossell_ids = wc_get_related_products( $product->get_id(), 1);
   }

   if($crossell_ids && is_array($crossell_ids) && count($crossell_ids) > 0){
      $crossell_idsProductQuery =  array( 'limit'=> 5, 'include' => $crossell_ids );
      $query    = new WC_Product_Query( $crossell_idsProductQuery );
      $crossell_Items = $query->get_products();
      return bravepop_dynamic_woo_loop( $crossell_Items );
   }
}

function bravepop_related_products($count=3, $type='related'){
   $product = wc_get_product();
   if(!$product){ return wc_get_products( array( 'limit' => $count ) ); }
   
   if($type === 'upsell'){
      $product_ids = $product->get_upsell_ids();
   }else if($type === 'cross_sell'){
      $product_ids = $product->get_cross_sell_ids();
   }else{
      $product_ids = wc_get_related_products( $product->get_id(), $count);
   }
   
   if(($type === 'upsell' || $type === 'cross_sell') && is_array($product_ids) && count($product_ids) < $count){
      $relatedCount = $count - count($product_ids);
      $relatedProducts = wc_get_related_products( $product->get_id(), $relatedCount, $product_ids);
      if($relatedProducts && count($relatedProducts) > 0){
         $product_ids = array_merge($product_ids, $relatedProducts);
      }
   }

   $finalProductQuery =  array( 'limit'=> $count, 'include' => $product_ids );
   $query    = new WC_Product_Query( $finalProductQuery );
   return $query->get_products();
}


function bravepop_posts_element_query($postType, $filterType, $postCount, $orderby, $customIds, $categories, $tags){
   if(!$postType){ return; }
   if($customIds && $filterType === 'custom'){
      $the_query = new WP_Query( array( 'post_type' => 'post' ,'orderby' => $orderby, 'posts_per_page' => $postCount, 'post__in' => $customIds ) );
   }elseif($categories && $filterType === 'categories'){
      $the_query = new WP_Query( array( 'post_type' => 'post' ,'orderby' => $orderby, 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1, 'cat' => $categories ? implode(",",$categories) : '' ) );
   }elseif($tags && $filterType === 'tags'){
      $the_query = new WP_Query( array( 'post_type' => 'post' ,'orderby' => $orderby, 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1, 'tag__in' => $tags ) );
   }else{
      $the_query = new WP_Query( array( 'post_type' => 'post' ,'orderby' => $orderby, 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1) );
   }

   if($postType === 'popular'){
      $the_query = new WP_Query( array( 'post_type' => 'post' , 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1, 'orderby' => ($orderby === 'rand' ? 'comment_count rand' : 'comment_count') ) );
   }

   if($postType === 'related'){
      global $post;
      $postCategories = wp_get_post_categories( $post->ID );
      $the_query = new WP_Query( array( 'post_type' => 'post', 'orderby' => $orderby , 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1, 'post__not_in' => array($post->ID),  'category__in' => $postCategories) );
      $totalPosts = $the_query->post_count; 
      if($totalPosts === 0){
         $the_query = new WP_Query( array( 'post_type' => 'post', 'orderby' => $orderby , 'posts_per_page' => $postCount, 'ignore_sticky_posts' => 1, 'post__not_in' => array($post->ID)));
      }
   }

   return $the_query;

}