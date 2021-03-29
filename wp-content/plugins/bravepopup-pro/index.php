<?php
   /**
    * Plugin Name: Brave Conversion Engine (PRO)
    * Plugin URI:  https://getbrave.io
    * Description: A plugin to create highly effective conversion widgets and Interactive content to convert your visitors to leads or Customers.
    * Version:     0.3.5
    * Author:      Brave
    * Author URI:  https://getbrave.io/
    * Text Domain: bravepop
    * Domain Path: /languages
    */
   $bravepop_settings = get_option('_bravepopup_settings');
   $bravepop_global = array('status_array'=> array(), 'autoEmbedded'=>array());
   require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
   /** Define plugin FILE constant for the Plugin updater.php */
   if (!defined('BRAVEPOP_PLUGIN_PATH')) {  define('BRAVEPOP_PLUGIN_FILE', __FILE__); }
   if (!defined('BRAVEPOP_WOO_ACTIVE')) {  define('BRAVEPOP_WOO_ACTIVE', in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )); }
  
   add_action( 'plugins_loaded', 'bravepop_require_files_pro', 1 );
   function bravepop_require_files_pro() {
      if ( function_exists( 'bravepop_require_files_free' ) ) {
         remove_action( 'plugins_loaded', 'bravepop_require_files_free' );
         deactivate_plugins(  'bravepopup-free/index.php'  );
      }

      include __DIR__ . '/lib/helpers/dynamic.php';
      //include __DIR__ . '/lib/rate-brave.php';

      // PRO Files
      include __DIR__ . '/lib/helpers/pro_helpers/init.php';
      include __DIR__ . '/lib/helpers/geolite2/geolocation.php';
      include __DIR__ . '/lib/helpers/imageFrames.php';
      include __DIR__ . '/lib/embed.php';
      include __DIR__ . '/lib/Analytics.php';
      include __DIR__ . '/includes.php';
      include __DIR__ . '/lib/updater/updater.php';
   }
   