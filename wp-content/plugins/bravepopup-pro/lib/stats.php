<?php
defined('ABSPATH') || exit;

global $bravepopup_stats_db_version;
$bravepopup_stats_db_version = '1.0';

function bravepop_init_statdb() {
   $setupCompleted = get_option( 'bravepopup_setup_stats_db' );
   //error_log('bravepop_init_statdb '. $setupCompleted);
   if ($setupCompleted !== 'complete' ) {
      //error_log('RUN DB SETUP!!');
      global $wpdb;
      global $bravepopup_stats_db_version;

      $goals_table_name = $wpdb->prefix . 'bravepopup_goal_stats';
      $stats_table_name = $wpdb->prefix . 'bravepopup_stats';
      
      $charset_collate = $wpdb->get_charset_collate();

      $goalTablesql = "CREATE TABLE $goals_table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         goal_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         popup INT NOT NULL, 
         country varchar(50),
         ip varchar(50),
         device varchar(20) DEFAULT '',
         goaltype varchar(20),
         actiontype varchar(20),
         actiondata varchar(150),
         autotracked INT DEFAULT 0,
         url varchar(155) DEFAULT '' NOT NULL,
         user INT DEFAULT 0 NOT NULL,
         viewed INT DEFAULT 0 NOT NULL,
         PRIMARY KEY  (id)
      ) $charset_collate;";

      $statsTablesql = "CREATE TABLE $stats_table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         popup INT NOT NULL, 
         stats text DEFAULT '' NOT NULL,
         PRIMARY KEY  (id)
      ) $charset_collate;";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $goalTablesql );
      dbDelta( $statsTablesql );

      add_option( 'bravepopup_setup_stats_db', 'complete' );
      add_option( 'bravepopup_stats_db_version', $bravepopup_stats_db_version );
   }
}
add_action( 'admin_init', 'bravepop_init_statdb' );

?>