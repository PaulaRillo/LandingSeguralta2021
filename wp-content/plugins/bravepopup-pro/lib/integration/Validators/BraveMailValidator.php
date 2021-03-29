<?php
if ( ! class_exists( 'BravePop_MailValidator' ) ) {
   
   class BravePop_MailValidator {
      const EMAIL_PROVIDERS = __DIR__ . '/data/email-providers.php'; 
      const TOP_LEVEL_DOMAINS = __DIR__ . '/data/top-level-domains.php';
      const DISPOSABLE_EMAIL_PROVIDERS = __DIR__ . '/data/disposable-email-providers.php';

      function __construct($email, $mxOpt=true, $disposableOpt=true, $suggestionOpt=false, $preventFree=false) {
         $this->email = $email; $email_parts = explode('@', $this->email);
         $this->alias = isset($email_parts[0]) ? $email_parts[0] : ''; 
         $this->domain = isset($email_parts[1]) ? $email_parts[1] : ''; 
         $this->tld = $this->domain ? explode('.', $this->domain)[1] :'';

         $this->mxOpt =$mxOpt; $this->disposableOpt= $disposableOpt; $this->suggestionOpt= $suggestionOpt;  $this->preventFree= $preventFree;
      }

      //Syntax Check
      private function emailSyntaxValid(){
         return filter_var($this->email, FILTER_VALIDATE_EMAIL);
      }

      //Disposable Check
      private function emailDisposable(){
         //error_log(json_encode(include self::DISPOSABLE_EMAIL_PROVIDERS));
         return in_array( $this->domain, include self::DISPOSABLE_EMAIL_PROVIDERS );
      }

      private function findFreeEmailProviders(){
         return in_array( $this->domain, include self::EMAIL_PROVIDERS );
      }

      //FIND EMAIL SUGGESTION----------------------------------------------
      private function findEmailAddressSuggestion(){
         $domainSuggestion = $this->findDomainSuggestion();
         if ($domainSuggestion) {
               return str_replace($this->domain, $domainSuggestion, $this->email);
         }
         $topLevelDomainSuggestion = $this->findTopLevelDomainSuggestion();
         if ($topLevelDomainSuggestion) {
               return str_replace($this->tld , $topLevelDomainSuggestion, $this->email);
         }
         return '';
      }

      private function findTopLevelDomainSuggestion(){
         $possibleTopLevelMatch = $this->findClosestWord( $this->tld, include self::TOP_LEVEL_DOMAINS, 1 );
         return $this->tld === $possibleTopLevelMatch ? null : $possibleTopLevelMatch; //@return bool|null|string
      }

      private function findDomainSuggestion(){
         $possibleMatch = $this->findClosestWord( $this->domain, include self::EMAIL_PROVIDERS, 2 );
         return $this->domain === $possibleMatch ? null : $possibleMatch; //@return bool|null|string
      }


      private function findClosestWord(string $stringToCheck, array $wordsToCheck, int $minimumDistance){
          if (in_array($stringToCheck, $wordsToCheck)) {
              return $stringToCheck;
          }
  
          $closestMatch = '';
          foreach ($wordsToCheck as $testedWord) {
              $distance = levenshtein($stringToCheck, $testedWord);
              if ($distance <= $minimumDistance) {
                  $minimumDistance = $distance - 1;
                  $closestMatch = $testedWord;
              }
          }
  
          return $closestMatch;
      }

      //MX LookUP----------------------------------------------
      private function emailMxCheck(){
         return checkdnsrr($this->domain, 'MX');
      }

      //Host Lookup
      private function emailHostCheck(){
         $domainIP = gethostbyname($this->domain);
         return $domainIP !== $this->domain; 
      }
      
      //Final Result
      public function validate_email(){
         $result = array();
         $mxVal = $this->mxOpt ? $this->emailMxCheck() : true;  
         $disposable = $this->disposableOpt ? $this->emailDisposable() : false;
         $suggestion = $this->suggestionOpt ? $this->findEmailAddressSuggestion() : '';
         $freeMail = $this->preventFree ? $this->findFreeEmailProviders() : false;  
         
         $result['mx'] = $mxVal;
         $result['disposable'] = $disposable;
         $result['suggestion'] = $suggestion;
         $result['status'] = ($mxVal === true && $disposable === false && $freeMail === false) ? 'valid' : 'invalid' ;

         if($mxVal === false){
            $result['errorMsg'] =__('This Email is Inactive','bravepop');
         }
         if($disposable === true){
            $result['errorMsg'] =__('Disposable Email not Allowed','bravepop');
         }
         if($freeMail === true){
            $result['errorMsg'] =__('Kindly Provide your work/business email.','bravepop');
         }
         if($suggestion && $result['status'] === 'invalid'){
            $result['suggestionMsg'] =__('Did you mean ','bravepop').$suggestion.' ?';
         }

         return $result;
      }

   }

}
?>