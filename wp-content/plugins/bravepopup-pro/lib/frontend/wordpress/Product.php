<?php

if ( ! class_exists( 'BravePop_Element_Product' ) ) {
   

   class BravePop_Element_Product {

      function __construct($data=null, $popupID=null, $stepIndex, $elementIndex) {
         $this->data = $data;
         $this->popupID = $popupID;
         $this->stepIndex =  $stepIndex;
         $this->elementIndex = $elementIndex;
      }

      
      public function render_css() { 

         $roundness = isset($this->data->roundness) ?  'border-radius: '.$this->data->roundness.'px;' : '';
         $shadowStr = isset($this->data->shadow) && $this->data->shadow > 30 ? 0.2 : 0.12;
         $shadow = isset($this->data->shadow) ?  'box-shadow: 0 0 '.$this->data->shadow.'px rgba(0, 0, 0, '.$shadowStr.');' : '';
         $titleSize = bravepop_generate_style_props(isset($this->data->titleSize) ? $this->data->titleSize : 18, 'font-size');
         $contentSize = bravepop_generate_style_props(isset($this->data->contentSize) ? $this->data->contentSize : 14, 'font-size');
         $priceSize = bravepop_generate_style_props(isset($this->data->priceSize) ? $this->data->priceSize : 20, 'font-size');  
         $fontFamily = isset($this->data->fontFamily) && $this->data->fontFamily !== 'None' ?  'font-family: '.$this->data->fontFamily.';' : '';
         $titlefontFamily = isset($this->data->titlefontFamily) && $this->data->titlefontFamily !== 'None' ?  'font-family: '.$this->data->titlefontFamily.';' : '';
         $imageWidth =  isset($this->data->imageWidth) ?  'width: '.$this->data->imageWidth.'px;' : 'width: 250px;';
         $contentWidth =  isset($this->data->imageWidth) ?  'width: calc( 100% - '.$this->data->imageWidth.'px);' : 'width: calc( 100% - 250px);';
         $showprice = isset($this->data->price) && $this->data->price == false ?  false : true;

         $backgroundColor = bravepop_generate_style_props(isset($this->data->backgroundColor) ? $this->data->backgroundColor : '', 'background-color', '255, 255, 255', '1');
         $textColor = bravepop_generate_style_props(isset($this->data->contentColor) ? $this->data->contentColor : '', 'color', '107, 107, 107', '1');
         $titleColor = bravepop_generate_style_props(isset($this->data->titleColor) ? $this->data->titleColor : '', 'color', '68, 68, 68', '1');
         $metaColor =  bravepop_generate_style_props(isset($this->data->infoColor) ? $this->data->infoColor : '', 'color', '153,153,153', '1');
         $btnTxtColor =  bravepop_generate_style_props(isset($this->data->buttonTextColor) ? $this->data->buttonTextColor : '', 'color', '255, 255, 255', '1');
         $btnBgColor = bravepop_generate_style_props(isset($this->data->buttonBackgroundColor) ? $this->data->buttonBackgroundColor : '', 'background-color', '109, 120, 216', '1');
         $prcColor = bravepop_generate_style_props(isset($this->data->priceColor) ? $this->data->priceColor : '', 'color', '109, 120, 216', '1'); 
         $saleRibbonTxtColor  = bravepop_generate_style_props(isset($this->data->saleTextColor) ? $this->data->saleTextColor : '', 'color', '255, 255, 255', '1'); 
         $saleRibbonBg  = bravepop_generate_style_props(isset($this->data->saleBgColor) ? $this->data->saleBgColor : '', 'fill', '109, 120, 216', '1'); 


         $elementBGStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product_wrap{ '. $backgroundColor . $roundness. $shadow  .'}';
         //$elementInnerStyle = $shadow ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_element__styler{'. $shadow . '}' : '';
         $elementWrapStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product__content_wrap{ '.$contentWidth . $fontFamily .'}';
         $elementImageStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product__image_wrap{ '.$imageWidth .'}';
         $elementTitleStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_post__title h2{'. $titleColor . $titleSize . $titlefontFamily .'}';
         $elementMetaStyle = $metaColor ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_post__meta a{'. $metaColor . '}' : '';

         $elementPriceStyle = $showprice ? '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product__price{'. $prcColor. $priceSize . '}' : '';
         
         $elementProContenteStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_post__content{'. $textColor. $contentSize . '}';

         $elementRibonStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product_sale_ribon span{'. $saleRibbonTxtColor . '}';
         $elementRibonBGStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product_sale_ribon svg{'. $saleRibbonBg . '}';
         $elementButtonStyle = '#brave_popup_'.$this->popupID.'__step__'.$this->stepIndex.' #brave_element-'.$this->data->id.' .brave_product__readMore a{'. $btnTxtColor . $btnBgColor. '}';

         

         return $elementBGStyle . $elementWrapStyle .$elementImageStyle . $elementTitleStyle .$elementMetaStyle .$elementPriceStyle . $elementProContenteStyle . $elementRibonStyle . $elementRibonBGStyle . $elementButtonStyle;

      }

      public function renderPost(){

         $postType = isset($this->data->postType) ? $this->data->postType : 'post';
         $fistPost = get_posts("post_type='.$postType.'&numberposts=1");
         $fistPostID = $fistPost[0]->ID;
         $singleID = isset($this->data->singleID) ? $this->data->singleID : $fistPostID;
         $displayTitle = isset($this->data->title) && $this->data->title === false ? false : true;
         $displayDate = isset($this->data->date) ? $this->data->date : true;
         $displayCat = isset($this->data->category) ? $this->data->category : true;
         $displayRibbon = isset($this->data->ribbon) ? $this->data->ribbon : false;
         $price  = isset($this->data->price) ? $this->data->price : true;
         $description = isset($this->data->description) ? $this->data->description : true;
         $button = isset($this->data->button) ? $this->data->button : true;
         $saleText = isset($this->data->saleText) ? $this->data->saleText : 'Sale';
         $currency = function_exists('get_woocommerce_currency_symbol') ?  get_woocommerce_currency_symbol() : '$';

         $the_query = new WC_Product_Query( array( 'limit' => 1, 'include' => array( $singleID )) );
         $postHTML = '';
         $products = $the_query->get_products();
         //error_log(json_encode($products));
         $product = count($products) !==0 ? $products[0]: false;
         // The Loop
         if ( $product ) {
            $postHTML .= '<div class="brave_product_wrap" id="brave_product_'.$product->get_id().'">';
               //PRODUCT IMAGE
               $postHTML .=  '<div class="brave_product__image_wrap">';
                  $postHTML .= $displayRibbon ? '<div class="brave_product_sale_ribon"><span>'.$saleText.'</span><svg preserveAspectRatio="none"  version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enableBackground="new 0 0 40 40" xmlSpace="preserve"><polygon points="39.998,0.001 -0.002,40.001 -0.002,0" /></svg></div> ' : '';
                  $postHTML .= get_the_post_thumbnail_url($product->get_id(), 'large') ? '<img class="brave_element_img_item skip-lazy no-lazyload" src="'.bravepop_get_preloader().'" data-lazy="'.get_the_post_thumbnail_url($product->get_id(), 'large').'" alt="' . $product->get_name() . '" />' : '<div class="brave_product__image__fake"><span class="fas fa-image"></span></div>';
               $postHTML .=  '</div>';

               //PRODUCT CONTENT
               $postHTML .=  '<div class="brave_product__content_wrap">';
 
                  //TITLE
                  $postHTML .=  $displayTitle ? '<div class="brave_post__title"><h2><a href="'.get_permalink( $product->get_id() ).'">' . $product->get_name() . '</a></h2></div>' : '';
                  
                  //CONTENT
                  $postHTML .=  '<div class="brave_post__content">';
                                                      
                     if($displayCat){
                        $postHTML .=  '<div class="brave_post__meta">';   
                           $postHTML .=  '<div class="brave_post__content__category">';
                           $cats = $product->get_category_ids();
                           foreach ( $cats as $key=>$categoryID ) {
                              $comma = (count($cats) - 1) !== $key ? ', ' : '';
                              $postHTML .=  '<a href="'.get_term_link($categoryID).'">'.get_term( $categoryID )->name.'</a></li>'.$comma;
                           }
                           $postHTML .=  '</div>';
                        $postHTML .=  '</div>';
                     }

                     $postPrice = $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price();
                     $postHTML .= $price && $postPrice ?'<div class="brave_product__price">'.$currency . $postPrice.'</div>': '';
                     $postHTML .=  $description ? '<div class="brave_post__content__content">'.$product->get_short_description().'</div>' :'';
                     $postHTML .= $button ? '<div class="brave_product__readMore">'.do_shortcode('[add_to_cart show_price="false" style="" id="'.$product->get_id().'"]').'</div>' : '';
                  $postHTML .=  '</div>';


               $postHTML .=  '</div>';
            $postHTML .=  '</div>';
         }
         wp_reset_postdata();

         return $postHTML;         
      }


      public function render( ) { 
         $singleLayout  = isset($this->data->layout) ? $this->data->layout : 1; 
         
         return '<div id="brave_element-'.$this->data->id.'" class="brave_element brave_element--wpSingleProduct">
                  <div class="brave_element__wrap">
                     <div class="brave_element__styler">
                        <div class="brave_element__inner">
                           <div id="wpSingleProduct_'.$this->data->id.'" class="brave_wpSingleProduct brave_wpSingleProduct--'.$singleLayout.'">
                              <div class="brave_wpSingleProduct__wrap">
                                 '.$this->renderPost().'
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