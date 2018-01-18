<?php
/**
 * File: wpglobus-plus.php
 *
 * @package   WPGlobus-Plus
 * @author    WPGlobus
 * @category  Extension
 * @copyright Copyright 2014-2016 The WPGlobus Team: Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 */

/**
 * Plugin Name: WPGlobus Plus
 * Plugin URI: http://www.wpglobus.com/product/wpglobus-plus/
 * Description: Extend functionality of the <a href="https://wordpress.org/plugins/wpglobus/">WPGlobus Multilingual Plugin</a>.
 * Text Domain: wpglobus-plus
 * Domain Path: /languages/
 * Version: 1.1.20
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/includes/wpglobus-plus-tablepress-functions.php';

add_action( 'plugins_loaded', 'wpglobus_plus_load', 11 );

/**
 * Load after the Main WPGlobus plugin.
 */
function wpglobus_plus_load() {

	// Main WPGlobus plugin is required.
	if ( ! defined( 'WPGLOBUS_VERSION' ) ) {
		return;
	}

	if ( 'off' === WPGlobus::Config()->toggle ) {
		return;
	}

	define( 'WPGLOBUS_PLUS_VERSION', '1.1.20' );

	require_once dirname( __FILE__ ) . '/includes/wpglobus-plus-main.php';
	WPGlobusPlus::$PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );
	WPGlobusPlus::$PLUGIN_DIR_URL  = plugin_dir_url( __FILE__ );

	// Load translations.
	load_plugin_textdomain( 'wpglobus-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * Load modules.
	 * Modules can be switched off in the WPGlobus Plus admin panel.
	 */

	$plus_modules = array();

	$warning_module_disabled =
		// Translators: %1$s - plugin name (eg ACF); %2$s - plugin version (eg 1.2.3).
		esc_html_x( 'To use this module, you need to install and activate the %1$s plugin version %2$s or later.',
			'List of modules', 'wpglobus-plus'
		);

	$plus_modules['acf'] = array(
		'caption'           => esc_html_x( 'ACF Plus', 'List of modules', 'wpglobus-plus' ),
		'checkbox_disabled' => false,
	);
	// ACF is active and at least version 4.4.3 (WYSIWYG bugs before).
	$plus_modules['acf']['desc'] =
		esc_html_x( 'Multilingual WYSIWYG Advanced Custom Fields', 'List of modules', 'wpglobus-plus' );
	/* @noinspection PhpUndefinedFunctionInspection */
	if ( ! class_exists( 'acf' ) || version_compare( acf()->settings['version'], '4.4.3', '<' ) ) {
		$plus_modules['acf']['desc'] .=
			'<div style="color:#c00; margin-left: 4em">' . sprintf( $warning_module_disabled, 'ACF', '4.4.3' ) . '</div>';

		$plus_modules['acf']['checkbox_disabled'] = true;
	}

	$plus_modules['publish'] = array(
		'caption' => esc_html_x( 'Publish', 'List of modules', 'wpglobus-plus' ),
		'desc'    => esc_html_x( 'Publish only the completed translations', 'List of modules', 'wpglobus-plus' ),
	);

	$plus_modules['slug'] = array(
		'caption' => esc_html_x( 'Slug', 'List of modules', 'wpglobus-plus' ),
		'desc'    => esc_html_x( 'Translate post/page URLs', 'List of modules', 'wpglobus-plus' ),
	);

	$plus_modules['menu'] = array(
		'caption' => esc_html_x( 'Switcher Menu', 'List of modules', 'wpglobus-plus' ),
		'desc'    => esc_html_x( 'Customize the Language Switcher Menu layout', 'List of modules', 'wpglobus-plus' ),
	);

	if ( version_compare( WPGLOBUS_VERSION, '1.5.8', '>=' ) ) :
		/**
		 * Module Menu Settings
		 *
		 * @since    1.1.17
		 * @requires WPGLOBUS_VERSION 1.5.8
		 */
		$plus_modules['menu-settings']             = array(
			'caption' => esc_html_x( 'Menu Settings', 'List of modules', 'wpglobus-plus' ),
			'desc'    => esc_html_x( 'Associate different menus with different languages', 'List of modules', 'wpglobus-plus' ),
		);
		$plus_modules['menu-settings']['subtitle'] = '<div class="subtitle-menu-settings" style="color:#00a0d2; margin-left: 4em"><a href="' . admin_url( 'admin.php?page=wpglobus-plus-menu-settings' ) . '">' . esc_html__( 'Go to WPGlobus Menu Settings page', 'wpglobus-plus' ) . '</a></div>';
	endif;

	$plus_modules['wpglobeditor'] = array(
		'caption' => esc_html_x( 'Editor', 'List of modules', 'wpglobus-plus' ),
		'desc'    => esc_html_x( 'Universal Multilingual Editor', 'List of modules', 'wpglobus-plus' ),
	);

	$plus_modules['tablepress']         = array(
		'caption'           => esc_html_x( 'TablePress', 'List of modules', 'wpglobus-plus' ),
		'checkbox_disabled' => false,
	);
	$plus_modules['tablepress']['desc'] = esc_html_x( 'Multilingual TablePress', 'List of modules', 'wpglobus-plus' );

	/* @noinspection PhpUndefinedClassInspection */
	if ( ! class_exists( 'TablePress' ) || version_compare( TablePress::version, '1.6.1', '<' ) ) {
		$plus_modules['tablepress']['desc'] .=
			'<div style="color:#c00; margin-left: 4em">' . sprintf( $warning_module_disabled, 'TablePress', '1.6.1' ) . '</div>';

		$plus_modules['tablepress']['checkbox_disabled'] = true;
	}

	$plus_modules['wpseo'] = array(
		'caption'           => esc_html_x( 'Yoast SEO Plus', 'List of modules', 'wpglobus-plus' ),
		'checkbox_disabled' => false,
	);
	/**
	 * Yoast is active and version at least 2.3.4.
	 * That's when we started; do not want to support old bugs.
	 */
	$plus_modules['wpseo']['desc'] =
		esc_html_x( 'Multilingual Focus Keywords and Page Analysis', 'List of modules', 'wpglobus-plus' );
	/* @noinspection PhpInternalEntityUsedInspection */
	if ( ! defined( 'WPSEO_VERSION' ) || version_compare( WPSEO_VERSION, '2.3.4', '<' ) ) {
		$plus_modules['wpseo']['desc'] .=
			'<div style="color:#c00; margin-left: 4em">' . sprintf( $warning_module_disabled, 'Yoast SEO', '2.3.4' ) . '</div>';

		$plus_modules['wpseo']['checkbox_disabled'] = true;
	}

	// Not a global.
	$wpg_plus = new WPGlobusPlus( $plus_modules );

	foreach ( $plus_modules as $module => $option ) {
		$load = true;

		if ( isset( $wpg_plus->options[ $module ]['active_status'] ) &&
		     ! $wpg_plus->options[ $module ]['active_status']
		) {
			$load = false;
		}

		if ( $load ) {
			/* @noinspection PhpIncludeInspection */
			require_once dirname( __FILE__ ) . '/includes/wpglobus-plus-' . $module . '.php';
		}
	}

	/**
	 * Old updater setup - for WPGlobus before 1.5.9.
	 */
	if (
		version_compare( WPGLOBUS_VERSION, '1.5.9', '<' )
		&& class_exists( 'WPGlobus_Updater' )
		&& WPGlobus_WP::in_wp_admin()
	) :
		/* @noinspection PhpUndefinedClassInspection */
		new WPGlobus_Updater(
			array(
				'product_id'     => 'WPGlobus Plus',
				'url_product'    => 'http://www.wpglobus.com/product/wpglobus-plus/',
				'url_my_account' => 'http://www.wpglobus.com/my-account/',
				'plugin_file'    => __FILE__,
			)
		);
	endif;

}

/**
 * Setup updater.
 *
 * @since    1.1.19
 * @requires WPGLOBUS_VERSION 1.5.9
 */
function wpglobus_plus__setup_updater() {
	/* @noinspection PhpUndefinedClassInspection */
	new TIVWP_Updater( array(
		'plugin_file' => __FILE__,
		'product_id'  => 'WPGlobus Plus',
		'url_product' => 'http://www.wpglobus.com/product/wpglobus-plus/',
	) );
}

add_action( 'tivwp_updater_factory', 'wpglobus_plus__setup_updater' );

/*EOF*/
