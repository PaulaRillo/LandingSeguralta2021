<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php _e('LinkedIn Authentication', 'bravepop');?></title>
   <style>
      .brave_form_loading { width: 20px;height: 20px; position: absolute; margin-left: -30px; margin-top: -2px; animation-name: bravespin; animation-duration: 1500ms; animation-iteration-count: infinite;  animation-timing-function: linear; }
      .bravepop_linkedIn_loader { width: 500px; position: absolute; z-index: 9; left: 0;  right: 0; margin: 0 auto; top: 46%; font-family: sans-serif; font-weight: bold;color: #676767;font-size: 14px;text-align: center; }
      @keyframes bravespin { from {  transform:rotate(0deg); } to { transform:rotate(360deg); } }
   </style>
</head>
<body>
<div class="braveshots_wrapper"> 
   <div class="bravepop_linkedIn_loader"><span class="brave_form_loading"><?php echo bravepop_renderIcon('reload', '#999999');?></span> <?php _e('Fetching Data...', 'bravepop');?></div>
</div>
</body>
<script>
      var brave_social_global = {};
      var ajaxURL = '<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>';
      var currentURL = new URL(window.location.href);
      var linkedinCode = currentURL.searchParams.get("code");
      var dataToSend = 'code='+linkedinCode+'&security=<?php echo wp_create_nonce('brave-linkedin-nonce');?>&action=bravepop_linkedin_authenticate_user';
      var messageHolder =  document.querySelector('.bravepop_linkedIn_loader');
      console.log(linkedinCode, messageHolder);
      if(linkedinCode){
         var request = new XMLHttpRequest();
         request.open('POST', ajaxURL, true);
         request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded' );
         request.onload = function () {  
            if (this.status >= 200 && this.status < 400) { 
               console.log('success', this.response);
               if(this.response){
                  var parsedData = JSON.parse(this.response);
                  console.log(parsedData);
                  if(parsedData.user && parsedData.user.name && parsedData.user.email){
                     console.log(parsedData.user);
                     localStorage.setItem( 'brave_linkedin_auth', JSON.stringify(parsedData.user) );
                     messageHolder.innerHTML = '✓ '+'<?php _e('Successully Connected! Closing...', 'bravepop');?>'
                  }else{
                     messageHolder.innerHTML = '❌ '+'<?php _e('Could not Connect to your LinkedIn Account. <br> Please Close this Window and Try again.', 'bravepop');?>'
                  }
               }
            } else {   
               console.log('error', this.response);
               messageHolder.innerHTML = '❌ '+'<?php _e('Could not Connect to your LinkedIn Account. <br> Please Close this Window and Try again.', 'bravepop');?>'
            }  
         };
         //request.onerror = function(error) {  console.log(error);   };
         request.send(dataToSend);
      }

   </script>
</html>