<?php
 //wordpress
  define('WP_USE_THEMES', false);
   $wp_root = dirname(__FILE__) .'/../../../';
   if(file_exists($wp_root . 'wp-load.php')) {
	require_once($wp_root . "wp-load.php");
   } else if(file_exists($wp_root . 'wp-config.php')) {
	require_once($wp_root . "wp-config.php");
  } else {
	exit;
  }

  if ( !current_user_can('manage_options') ) {
   echo "You don't have permission to perform this operation!";
		exit(0);
  }

  global $wpdb,$wp_dlm_root, $wp_dlm_db;
  $table_item = $wpdb->prefix . "ebd_item";

  $sql=" ALTER TABLE `".$table_item."` ADD COLUMN `youtube_link` varchar(255) NULL" ;
  $wpdb->query($sql);
 
?>
The Email Before Download log entries have been cleared!