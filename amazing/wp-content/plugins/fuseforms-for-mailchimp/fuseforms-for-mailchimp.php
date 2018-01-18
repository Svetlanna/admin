<?php
/*
Plugin Name: FuseForms MailChimp Integration for WordPress 
Plugin URI: http://fuseforms.com/mailchimp-integration-for-wordpress
Description: FuseForms MailChimp Integration for WordPress is the easiest way to sync your contacts from your forms to a MailChimp list of your choosing.
Version: 0.1.0
Author: FuseForms
Author URI: http://fuseforms.com
License: GPL2
*/

//=============================================
// Define Constants
//=============================================

if ( !defined('FF_MC_PATH') )
	define('FF_MC_PATH', untrailingslashit(plugins_url('', __FILE__ )));

if ( !defined('FF_MC_PLUGIN_DIR') )
	define('FF_MC_PLUGIN_DIR', untrailingslashit(dirname( __FILE__ )));

if ( !defined('FF_MC_PLUGIN_SLUG') )
	define('FF_MC_PLUGIN_SLUG', basename(dirname(__FILE__)));

if ( !defined('FF_MC_DB_VERSION') )
	define('FF_MC_DB_VERSION', '0.1.0');

if ( !defined('FF_MC_PLUGIN_VERSION') )
	define('FF_MC_PLUGIN_VERSION', '0.1.0');

//=============================================
// Include Needed Files
//=============================================

if ( file_exists(FF_MC_PLUGIN_DIR . '/inc/constants.php') )
	include_once(FF_MC_PLUGIN_DIR . '/inc/constants.php');

require_once(FF_MC_PLUGIN_DIR . '/inc/ajax-functions.php');
require_once(FF_MC_PLUGIN_DIR . '/inc/functions.php');
require_once(FF_MC_PLUGIN_DIR . '/admin/class-admin.php');

require_once(FF_MC_PLUGIN_DIR . '/inc/class-ff-mc.php');



//=============================================
// Hooks & Filters
//=============================================

/**
 * Activate the plugin
 */
function ff_mc_activate ( $network_wide )
{
	if ( is_multisite() && $network_wide ) 
	{ 
		global $wpdb;
 
		$current_blog = $wpdb->blogid;
		$activated = array();
 
		$q = "SELECT blog_id FROM $wpdb->blogs";
		$blog_ids = $wpdb->get_col($q);
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog($blog_id);
			ff_mc_add_defaults();
			$activated[] = $blog_id;
		}
 
		switch_to_blog($current_blog);
	}
	else
	{
		ff_mc_add_defaults();
	}
}

/**
 * Check FuseForms installation and set options
 */
function ff_mc_add_defaults ( )
{
	global $wpdb;

	$options = get_option('ff_mc_options');

	if ( ( isset($options['ff_mc_installed']) && $options['ff_mc_installed'] != 1 ) || ( ! is_array($options) ) )
	{
		$opt = array(
			'ff_mc_installed'	=> 1,
			'ff_mc_version'	=> FF_MC_PLUGIN_VERSION,
			'ff_mc_db_version'	=> FF_MC_DB_VERSION,
			'api_key'	=> '',
			'synced_lists'	=> '',
			'synced_selectors'	=> ''
		);

		// this is a hack because multisite doesn't recognize local options using either update_option or update_site_option...
		if ( is_multisite() )
		{
			$multisite_prefix = ( is_multisite() ? $wpdb->prefix : '' );
			$q = $wpdb->prepare("
				INSERT INTO " . $multisite_prefix . "options 
					( option_name, option_value ) 
				VALUES ('ff_mc_options', %s)", serialize($opt));
			$wpdb->query($q);
		}
		else
			update_option('ff_mc_options', $opt);
		
		ff_mc_db_install();
	}
}

/**
 * Deactivate FuseForms plugin hook
 */
function ff_mc_deactivate ( $network_wide )
{
	if ( is_multisite() && $network_wide ) 
	{ 
		global $wpdb;
 
		// Get this so we can switch back to it later
		$current_blog = $wpdb->blogid;
 
		// Get all blogs in the network and activate plugin on each one
		$q = "SELECT blog_id FROM $wpdb->blogs";
		$blog_ids = $wpdb->get_col($q);
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog($blog_id);
		}
 
		// Switch back to the current blog
		switch_to_blog($current_blog);
	}
}

function ff_mc_activate_on_new_blog ( $blog_id, $user_id, $domain, $path, $site_id, $meta )
{
	global $wpdb;

	if ( is_plugin_active_for_network('fuseforms-for-mailchimp/fuseforms-for-mailchimp.php') )
	{
		$current_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		ff_mc_add_defaults();
		switch_to_blog($current_blog);
	}
}

/**
 * Checks the stored database version against the current data version + updates if needed
 */
function ff_mc_init ()
{
	$ff_mc = new FF_MC();
}

//=============================================
// Database update
//=============================================

/**
 * Creates or updates the FuseForms tables
 */
function ff_mc_db_install ()
{
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$sql = "
		CREATE TABLE " . $wpdb->prefix . "ff_mc_submissions (
		  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `form_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `form_page_title` varchar(255) NOT NULL,
		  `form_page_url` text NOT NULL,
		  `form_fields` text NOT NULL,
		  `form_selector_id` mediumtext NOT NULL,
		  `form_selector_classes` mediumtext NOT NULL,
		  `form_hashkey` varchar(16) NOT NULL,
		  `form_deleted` int(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`form_id`),
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

	dbDelta($sql);

	ff_mc_update_option('ff_mc_options', 'ff_mc_db_version', FF_MC_DB_VERSION);
}

add_action( 'plugins_loaded', 'ff_mc_init', 14 );

if ( is_admin() ) 
{
	// Activate + install FuseForms
	register_activation_hook( __FILE__, 'ff_mc_activate');

	// Deactivate FuseForms
	register_deactivation_hook( __FILE__, 'ff_mc_deactivate');

	// Activate on newly created wpmu blog
	add_action('wpmu_new_blog', 'ff_mc_activate_on_new_blog', 10, 6);
}

?>