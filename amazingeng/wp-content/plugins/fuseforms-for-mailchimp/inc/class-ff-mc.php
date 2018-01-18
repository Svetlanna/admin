<?php

//=============================================
// WPIMC Class
//=============================================
class FF_MC {

	/**
	 * Class constructor
	 */
	function __construct ()
	{
		global $pagenow;

		self::ff_mc_set_wpdb_tables();

		if ( is_admin() )
		{
			if ( ! defined('DOING_AJAX') || ! DOING_AJAX )
				$li_wp_admin = new FF_MC_Admin();
		}
		else
		{
			add_action('wp_footer', array($this, 'append_version_number'));

			// Adds the fuseforms-tracking script to wp-login.php page which doesnt hook into the enqueue logic
			if ( in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) )
				add_action('login_enqueue_scripts', array($this, 'add_frontend_scripts'));
			else
				add_action('wp_enqueue_scripts', array($this, 'add_frontend_scripts'));
		}
	}

	//=============================================
	// Scripts & Styles
	//=============================================

	/**
	 * Adds front end javascript + initializes ajax object
	 */
	function add_frontend_scripts ()
	{
		wp_register_script('fuseforms', FF_MC_PATH . '/assets/js/build/fuseforms.js', array ('jquery'), FALSE, TRUE);
		wp_enqueue_script('fuseforms');
		
		// replace https with http for admin-ajax calls for SSLed backends 
		$admin_url = admin_url('admin-ajax.php');
		wp_localize_script(
			'fuseforms', 
			'ff_ajax', 
			array('ajax_url' => ( is_ssl() ? str_replace('http:', 'https:', $admin_url) : str_replace('https:', 'http:', $admin_url) ))
		);
	}

	/**
	 * Adds FuseForms version number to the source code for debugging purposes
	 */
	function append_version_number ()
	{
		echo "\n\n<!-- This site is collecting contacts with FuseForms MailChimp Integration for WordPress v" . FF_MC_PLUGIN_VERSION . " - http://fuseforms.com/mailchimp-integration --> \n";
	}

	/**
	 * Sets the wpdb tables to the current blog for easier queries
	 * 
	 */
	function ff_mc_set_wpdb_tables ()
	{
		global $wpdb;

		$wpdb->ff_mc_submissions	   = $wpdb->prefix . 'ff_mc_submissions';
		$wpdb->ff_mc_syncs			  = $wpdb->prefix . 'ff_mc_syncs';
	}
}

//=============================================
// FuseForms Init
//=============================================

global $li_wp_admin;