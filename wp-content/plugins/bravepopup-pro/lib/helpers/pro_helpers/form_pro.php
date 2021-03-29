<?php

function bravepop_get_conditional_redirection_data($actionType, $default, $conditions, $formFields, $userQuizData) {
   $finalStep = $default; $conditionMatched = false;
   
   foreach ($conditions as $ruleIndex => $cond) {
      //FIELD VALUE
      if(!empty($cond->fieldKey) && !empty($cond->values) && !empty($cond->operator) && isset($cond->step) && $conditionMatched === false ){
         $fieldKey = $cond->fieldKey;
         if(!empty($formFields->{$fieldKey}->value) && (strpos($fieldKey, 'brave_quiz_') === false)){
            $fieldValsArray = explode(",",  $cond->values);

            if($cond->operator === 'equal'){
               if(is_array($formFields->{$fieldKey}->value)){
                  if(array_intersect($formFields->{$fieldKey}->value, $fieldValsArray)){
                     //error_log('$equal Array match '.json_encode($fieldValsArray).'---'.json_encode($formFields->{$fieldKey}->value));
                     $finalStep = $cond->step;
                     $conditionMatched = true;
                  }
               }else{
                  if(in_array($formFields->{$fieldKey}->value, $fieldValsArray)){
                     //error_log('$equal match '.json_encode($fieldValsArray).'---'.json_encode($formFields->{$fieldKey}->value));
                     $finalStep = $cond->step;
                     $conditionMatched = true;
                  }
               }
            } 
            if($cond->operator === 'notequal'){
               if(is_array($formFields->{$fieldKey}->value)){
                  if(!array_intersect($formFields->{$fieldKey}->value, $fieldValsArray)){
                     //error_log('$notequal Array match '.json_encode($fieldValsArray).'---'.json_encode($formFields->{$fieldKey}->value));
                     $finalStep = $cond->step;
                     $conditionMatched = true;
                  }
               }else{
                  if(!in_array($formFields->{$fieldKey}->value, $fieldValsArray)){
                     //error_log('$notequal match '.json_encode($fieldValsArray).'---'.json_encode($formFields->{$fieldKey}->value));
                     $finalStep = $cond->step;
                     $conditionMatched = true;
                  }
               }
            } 
         }
         if(isset($userQuizData) && (strpos($fieldKey, 'brave_quiz_') !== false)){
            //error_log($fieldKey.'--'.$cond->values.'---'.$cond->operator.'---'.$userQuizData->userScore);
            $condVal = $cond->values; $operator =  $cond->operator; $quizVal = 0;
            if((strpos(trim($cond->values), '-') === false)){ $condVal = intval($condVal); }
            if($fieldKey === 'brave_quiz_points' && isset($userQuizData->userScore)){
               $quizVal = $userQuizData->userScore;
            }
            if($fieldKey === 'brave_quiz_correct' && isset($userQuizData->userCorrect)){
               $quizVal = $userQuizData->userCorrect;
            }
            if($cond->operator === 'equal' && $condVal === $quizVal){
               //error_log('quiz $equal match!'.$quizVal);
               $finalStep = $cond->step;
               $conditionMatched = true;
            } 
            if($cond->operator === 'notequal' && $condVal !== $quizVal){
               //error_log('quiz $notequal match!'.$quizVal);
               $finalStep = $cond->step;
               $conditionMatched = true;
            }
            if($cond->operator === 'between' && (strpos(trim($cond->values), '-') !== false)){
               $scoreRange = explode("-",  $cond->values);
               $startScore = isset($scoreRange[0]) ? intval(trim($scoreRange[0])) : false;
               $endScore = isset($scoreRange[1]) ? intval(trim($scoreRange[1])) : false;
               if($startScore !==false && $endScore !==false){
                  if(in_array($quizVal, range($startScore, $endScore))) {
                     //error_log('quiz $between match!'.$quizVal);
                     $finalStep = $cond->step;
                     $conditionMatched = true;
                  }
               }
            }
         }
         
      }
   }

   //error_log('bravepop_get_conditional_redirection_data! Final Step: '.$finalStep);

   return $finalStep;
}


function bravepop_replace_quizScore_shortcode($message){
   $finalMessage = $message;
   $regex = "/\{{(quizscore.*?)\}}/";
   preg_match_all($regex, $message, $matches);
   $theShortcode = $matches[0][0];
   if($theShortcode){
      preg_match_all("/id=\"(\S+)\"/", $theShortcode, $idMatch);
      preg_match_all("/total=\"(\S+)\"/", $theShortcode, $totalMatch);
      $shortcodeHTML = isset($idMatch[1][0]) ? '<div class="bravepop_quizScore bravepop_quizScore-'.$idMatch[1][0].'" data-form="'.$idMatch[1][0].'" data-total="'.(isset($totalMatch[1][0]) ? $totalMatch[1][0] : 'true').'"></div>' : $theShortcode;
      $finalMessage = str_replace($matches[0][0], $shortcodeHTML, $finalMessage);
   }

   return $finalMessage ;
}


//Quiz Score Condtional Content Shortcode
function bravepop_quizcondition_shortcode( $atts, $content = null ) {
   extract(shortcode_atts(array('match' => 'equal', 'type'=> 'points',  'score' => 0, 'correct' => 0, 'value'=> ''  ), $atts));
   //error_log(json_encode($atts));
   $finalContent = '';
   $condVal = $value;
   $operator = $match ; 
   $quizVal = 0;

   if($type === 'points' ){   $quizVal = intval($score); }
   if($type === 'answer'){   $quizVal = intval($correct); }

   if((strpos(trim($condVal), '-') === false)){ $condVal = intval($condVal); }


   if($operator === 'equal' && $condVal === $quizVal){
      // error_log('quiz $equal match!'.$quizVal);
      $finalContent = $content;
   }

   if($operator === 'notequal' && $condVal !== $quizVal){
      // error_log('quiz $notequal match!'.$quizVal);
      $finalContent = $content;
   }

   if($operator === 'between' && (strpos(trim($condVal), '-') !== false)){
      $scoreRange = explode("-",  $condVal);
      $startScore = isset($scoreRange[0]) ? intval(trim($scoreRange[0])) : false;
      $endScore = isset($scoreRange[1]) ? intval(trim($scoreRange[1])) : false;
      if($startScore !==false && $endScore !==false){
         if(in_array($quizVal, range($startScore, $endScore))) {
            // error_log('quiz $between match!'.$quizVal);
            $finalContent = $content;
         }
      }
   }

   return $finalContent;

}

add_shortcode( 'brave_quizcondition', 'bravepop_quizcondition_shortcode' );