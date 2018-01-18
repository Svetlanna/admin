<?php

if ( !defined('FF_MC_PLUGIN_VERSION') )
{
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * Inserts a new form submisison into the ff_mc_submissions table
 *
 */
function ff_mc_insert_form_submission ()
{
	global $wpdb;

	$submission_hash 		= $_POST['ff_submission_id'];
	$page_title 			= $_POST['ff_title'];
	$page_url 				= $_POST['ff_url'];
	$form_json 				= $_POST['ff_fields'];
	$email 					= $_POST['ff_email'];
	$first_name 			= $_POST['ff_first_name'];
	$last_name 				= $_POST['ff_last_name'];
	$phone 					= $_POST['ff_phone'];
	$form_selector_id 		= $_POST['ff_form_selector_id'];
	$form_selector_classes 	= $_POST['ff_form_selector_classes'];
	$options 				= get_option('ff_mc_options');
	$ff_admin_email 		= ( isset($options['ff_email']) ) ? $options['ff_email'] : '';

	// Check to see if the form_hashkey exists, and if it does, don't run the insert or send the email
	$q = $wpdb->prepare("SELECT form_hashkey FROM $wpdb->ff_mc_submissions WHERE form_hashkey = %s AND form_deleted = 0", $submission_hash);
	$submission_hash_exists = $wpdb->get_var($q);

	if ( $submission_hash_exists )
	{
		// The form has been inserted successful so send back a trigger to clear the cached submission cookie on the front end
		return 1;
		exit;
	}

	// Prevent duplicate form submission entries by deleting existing submissions if it didn't finish the process before the web page refreshed
	$q = $wpdb->prepare("UPDATE $wpdb->ff_mc_submissions SET form_deleted = 1 WHERE form_hashkey = %s", $submission_hash);
	$wpdb->query($q);

	// Insert the form fields and hash into the submissions table
	$result = $wpdb->insert(
		$wpdb->ff_mc_submissions,
		array(
			'form_hashkey' 			=> $submission_hash,
			'form_page_title' 		=> $page_title,
			'form_page_url' 		=> $page_url,
			'form_fields' 			=> $form_json,
			'form_selector_id' 		=> $form_selector_id,
			'form_selector_classes' => $form_selector_classes
		),
		array(
			'%s', '%s', '%s', '%s', '%s', '%s'
		)
	);

	$synced = FALSE;
	$synced_lists = ( $options['synced_lists'] ? explode(',', str_replace(' ', '', $options['synced_lists'])) : '');

	// Apply the sync relationship to contacts for form id rules
	if ( $form_selector_id )
	{
		if ( isset($options['synced_selectors']) && strstr($options['synced_selectors'], $form_selector_id) )
		{
			if ( count($synced_lists) )
			{
				foreach ( $synced_lists as $list_id ) 
				{
					ff_mc_push_contact_to_list($list_id, $email, $first_name, $last_name, $phone);
				}
			}

			$synced = TRUE;		
		}
	}

	if ( ! $synced )
	{
		$form_classes = '';
		if ( $form_selector_classes )
			$form_classes = explode(',', $form_selector_classes);

		if ( count($form_classes) )
		{
			foreach ( $form_classes as $class )
			{
				if ( isset($options['synced_selectors']) && strstr($options['synced_selectors'], '.' . $class) )
				{
					if ( count($synced_lists) )
					{
						foreach ( $synced_lists as $list_id ) 
						{
							ff_mc_push_contact_to_list($list_id, $email, $first_name, $last_name, $phone);
						}
					}

					$synced = TRUE;
					break;
				}
			}
		}
	}

	die();
}

add_action('wp_ajax_ff_mc_insert_form_submission', 'ff_mc_insert_form_submission'); // Call when user logged in
add_action('wp_ajax_nopriv_ff_mc_insert_form_submission', 'ff_mc_insert_form_submission'); // Call when user is not logged in

/**
 * Checks for properly installed FuseForms instance
 *
 */
function ff_mc_print_debug_values ( )
{
	global $wpdb;
	global $wp_version;

	$debug_string = '';
	$error_string = '';

	$debug_string .= "FuseForms for MailChimp version: " . FF_MC_PLUGIN_VERSION . "\n";
	$debug_string .= "WordPress version: " . $wp_version . "\n";
	$debug_string .= "Multisite : " . ( is_multisite() ? "YES" : "NO" ) . "\n";
	$debug_string .= "cURL enabled: " . ( function_exists('curl_init') ? 'YES' : 'NO' ) . "\n";

	if ( version_compare('3.7', $wp_version) != -1 )
	{
		$error_string .= "- WordPress version < 3.7. FuseForms Integration for MailChimp requires WordPress 3.7+\n";
	}

	$ff_mc_tables = ff_mc_check_tables_exist();
	
	$ff_mc_tables_count = count($ff_mc_tables);

	if ( $ff_mc_tables_count )
	{
		$debug_string .= "FuseForms tables installed:\n";

		foreach ( $ff_mc_tables as $table )
		{
			$debug_string .= "- " . $table->table_name . "\n";
		}
	}
	else
	{
		$error_string .= "- Database tables not installed\n";
	}

	echo $debug_string;

	if ( $error_string )
		echo "\n\n ERRORS:\n------------\n" . $error_string;

	die();
}

add_action('wp_ajax_ff_mc_print_debug_values', 'ff_mc_print_debug_values'); // Call when user logged in
add_action('wp_ajax_nopriv_ff_mc_print_debug_values', 'ff_mc_print_debug_values'); // Call when user is not logged in


?>