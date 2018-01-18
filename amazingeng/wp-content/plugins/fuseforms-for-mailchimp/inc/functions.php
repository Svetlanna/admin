<?php

if ( !defined('FF_MC_PLUGIN_VERSION') ) 
{
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
 * Updates an option in the multi-dimensional option array
 *
 * @param   string   $option		option_name in wp_options
 * @param   string   $option_key	key for array
 * @param   string   $option		new value for array
 *
 * @return  bool			True if option value has changed, false if not or if update failed.
 */
function ff_mc_update_option ( $option, $option_key, $new_value ) 
{
	$options_array = get_option($option);

	if ( isset($options_array[$option_key]) )
	{
		if ( $options_array[$option_key] == $new_value )
			return false; // Don't update an option if it already is set to the value
	}

	if ( !is_array( $options_array ) ) {
		$options_array = array();
	}

	$options_array[$option_key] = $new_value;
	update_option($option, $options_array);

	$options_array = get_option($option);
	return update_option($option, $options_array);
}

/**
 * Logs a debug statement to /wp-content/debug.log
 *
 * @param   string
 */
function ff_mc_log_debug ( $message )
{
	if ( WP_DEBUG === TRUE )
	{
		if ( is_array($message) || is_object($message) )
			error_log(print_r($message, TRUE));
		else 
			error_log($message);
	}
}

/**
 * Deletes an element or elements from an array
 *
 * @param   array
 * @param   wildcard
 * @return  array
 */
function ff_mc_array_delete ( $array, $element )
{
	if ( !is_array($element) )
		$element = array($element);

	return array_diff($array, $element);
}

/**
 * Deletes an element or elements from an array
 *
 * @param   array
 * @param   wildcard
 * @return  array
 */
function ff_mc_get_value_by_key ( $key_value, $array )
{
	foreach ( $array as $key => $value )
	{
		if ( is_array($value) && $value['label'] == $key_value )
			return $value['value'];
	}

	return null;
}

/**
 * Converts all carriage returns into HTML line breaks 
 *
 * @param   string
 * @return  string
 */
function ff_mc_html_line_breaks ( $string ) 
{
	return stripslashes(str_replace('\n', '<br>', $string));
}

/**
 * Search an object by for a value and return the associated index key
 *
 * @param   object 
 * @param   string
 * @param   string
 * @return  key for array index if present, false otherwise
 */
function ff_mc_search_object_by_value ( $haystack, $needle, $search_key )
{
   foreach ( $haystack as $key => $value )
   {
	  if ( $value->$search_key === $needle )
		 return $key;
   }

   return FALSE;
}

/**
 * Check multidimensional arrray for an existing value
 *
 * @param   string 
 * @param   array
 * @return  bool
 */
function ff_mc_in_array_deep ( $needle, $haystack ) 
{
	if ( in_array($needle, $haystack) )
		return TRUE;

	foreach ( $haystack as $element ) 
	{
		if ( is_array($element) && ff_mc_in_array_deep($needle, $element) )
			return TRUE;
	}

	return FALSE;
}

/**
 * Check multidimensional arrray for an existing value
 *
 * @param   string	  needle 
 * @param   array	   haystack
 * @return  string	  key if found, null if not
 */
function ff_mc_array_search_deep ( $needle, $array, $index ) 
{
	foreach ( $array as $key => $val ) 
	{
		if ( $val[$index] == $needle )
			return $key;
	}

   return NULL;
}

/**
 * Calculates the hour difference between MySQL timestamps and the current local WordPress time
 * 
 */
function ff_mc_set_mysql_timezone_offset ()
{
	global $wpdb;

	$mysql_timestamp = $wpdb->get_var("SELECT CURRENT_TIMESTAMP");
	$diff = strtotime($mysql_timestamp) - strtotime(current_time('mysql'));
	$hours = $diff / (60 * 60);

	$wpdb->db_hour_offset = $hours;
}


/**
 * Gets current URL with parameters
 * 
 */
function ff_mc_get_current_url ( )
{
	return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}


/**
 * Returns the user role for the current user
 * 
 */
function ff_mc_get_user_role ()
{
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	return $user_role;
}

/**
 * Checks whether or not to ignore the logged in user in the FuseForms tracking scripts
 * 
 */
function ff_mc_ignore_logged_in_user ()
{
	// ignore logged in users if defined in settings
	if ( is_user_logged_in() )
	{
		if ( array_key_exists('li_do_not_track_' . ff_mc_get_user_role(), get_option('ff_mc_options')) )
			return TRUE;
		else
			return FALSE;
	}
	else
		return FALSE;
}

/**
 * Adds a subcsriber to a specific list
 *
 * @param   string
 * @param   string
 * @param   string
 * @param   string
 * @param   string
 * @return  int/bool		API status code OR false if api key not set
 */
function ff_mc_push_contact_to_list ( $list_id = '', $email = '', $first_name = '', $last_name = '', $phone = '' ) 
{
	$options = get_option('ff_mc_options');
	if ( isset($options['api_key']) && $options['api_key'] && $list_id )
	{
		$MailChimp = new FF_MailChimp_API($options['api_key']);
		$contact_synced = $MailChimp->call("lists/subscribe", array(
			"id" => $list_id,
			"email" => array('email' => $email),
			"send_welcome" => FALSE,
			"email_type" => 'html',
			"update_existing" => TRUE,
			'replace_interests' => FALSE,
			'double_optin' => FALSE,
			"merge_vars" => array(
				'EMAIL' => $email,
				'FNAME' => $first_name,
				'LNAME' => $last_name,
				'PHONE' => $phone
			)
		));

		return $contact_synced;
	}

	return FALSE;
}

function ff_mc_check_tables_exist ()
{
    global $wpdb;

    $q = "SELECT table_name FROM information_schema.tables WHERE table_name = '" . $wpdb->ff_mc_submissions . "'";
    $li_tables = $wpdb->get_results($q);

    return $li_tables;
}

?>