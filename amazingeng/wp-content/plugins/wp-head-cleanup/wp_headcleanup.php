<?php
/*
=== WP Head Cleanup ===
Contributors: nablasol
Plugin Name: WP Head Cleanup
Plugin URI: http://www.wpshore.com/plugins
Donate link: http://www.wpshore.com/services
Tags: head cleanup, wphead cleaner, cleaner
Version: 1.0
Author: Nablasol
Description: WP Head Cleanup helps you to remove unnecessary extra links from the page header.
Author URI: http://www.nablasol.com/
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// create custom plugin settings menu
add_action('admin_menu', 'wphead_cleanup_create_menu');
add_action( 'init', 'wphead_init' );		

function wphead_cleanup_create_menu() {

	//create new top-level menu
	add_options_page('WP Head Cleanup Settings', 'WP head Cleanup', 'administrator', __FILE__, 'wphead_Cleanup_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_wphead_settings' );
}


	function wphead_init() {
		
		/* This will remove Really Simple Discovery link from the header */
		if(get_option( 'remove_rsd_link' ) == true ){
			remove_action('wp_head', 'rsd_link');
		}
		
		/* This will remove the Wordpress generator tag  */
        if(get_option( 'remove_wp_generator' ) == true ){
			remove_action('wp_head', 'wp_generator');
		}
		
		/* This will remove the standard feed links */
        if(get_option( 'remove_feed_links' ) == true ){
			remove_action( 'wp_head', 'feed_links', 2 );
		}
		
		/* This will remove the extra feed links */
		if(get_option( 'remove_feed_links_extra' ) == true ){
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
		
		/* This will remove index link */
        if(get_option( 'remove_index_rel_link' ) == true ){
			remove_action('wp_head', 'index_rel_link');
		}

		/* This will remove wlwmanifest */
		if(get_option( 'remove_wlwmanifest_link' ) == true ){
			remove_action('wp_head', 'wlwmanifest_link');
		}	
		
		/* This will remove parent post link */
		if(get_option( 'remove_parent_post_rel_link' ) == true ){
			remove_action('wp_head', 'parent_post_rel_link', 10, 0);
		}
		
		/*This will remove start post link */
		if(get_option( 'remove_start_post_rel_link' ) == true ){
			remove_action('wp_head', 'start_post_rel_link');
		}

		/* This will remove the prev and next post link */
		if(get_option( 'remove_adjacent_posts_rel_link' ) == true ){
			remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
		}	

        /* This will remove shortlink for the page */
		if(get_option( 'remove_wp_shortlink_wp_head' ) == true ){
			remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
		}			
		
}		
function register_wphead_settings() {
	//register our settings
	register_setting( 'wphead_cleanup-settings-group', 'remove_rsd_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_wp_generator' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_feed_links' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_feed_links_extra' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_index_rel_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_wlwmanifest_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_parent_post_rel_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_start_post_rel_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_adjacent_posts_rel_link' );
	register_setting( 'wphead_cleanup-settings-group', 'remove_wp_shortlink_wp_head' );
}


function wphead_Cleanup_settings_page() {
?>
<div class="wrap">
<h2>WP Head Cleanup</h2>
<h3>Select the elements you want to remove from the head section:</h3>

<form method="post" action="options.php">
    <?php settings_fields( 'wphead_cleanup-settings-group' ); ?>
    
    <table class="form-table">
        <tr valign="top">
		
        <th scope="row">Really Simple Discovery</th>
        <td>
		
		<input type="checkbox" name="remove_rsd_link" value="1" <?php if (get_option('remove_rsd_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Wordpress generator tag</th>
        <td><input type="checkbox" name="remove_wp_generator" value="1" <?php if (get_option('remove_wp_generator')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Remove the standard feed links</th>
        <td><input type="checkbox" name="remove_feed_links" value="1" <?php if (get_option('remove_feed_links')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Extra feeds such as category feeds</th>
        <td><input type="checkbox" name="remove_feed_links_extra" value="1" <?php if (get_option('remove_feed_links_extra')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Post Relational Links - Index</th>
        <td><input type="checkbox" name="remove_index_rel_link" value="1" <?php if (get_option('remove_index_rel_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Remove wlwmanifest</th>
        <td><input type="checkbox" name="remove_wlwmanifest_link" value="1" <?php if (get_option('remove_wlwmanifest_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Remove parent post link</th>
        <td><input type="checkbox" name="remove_parent_post_rel_link" value="1" <?php if (get_option('remove_parent_post_rel_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Post Relational Links - Start</th>
        <td><input type="checkbox" name="remove_start_post_rel_link" value="1" <?php if (get_option('remove_start_post_rel_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Remove the prev and next post link</th>
        <td><input type="checkbox" name="remove_adjacent_posts_rel_link" value="1" <?php if (get_option('remove_adjacent_posts_rel_link')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Remove shortlink for the page</th>
        <td><input type="checkbox" name="remove_wp_shortlink_wp_head" value="1" <?php if (get_option('remove_wp_shortlink_wp_head')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>