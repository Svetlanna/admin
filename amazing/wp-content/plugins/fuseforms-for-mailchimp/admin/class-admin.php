<?php

if ( !defined('FF_MC_PLUGIN_VERSION') ) 
{
	header('HTTP/1.0 403 Forbidden');
	die;
}

//=============================================
// Define Constants
//=============================================

if ( !defined('FF_MC_ADMIN_PATH') )
	define('FF_MC_ADMIN_PATH', untrailingslashit(__FILE__));

//=============================================
// Include Needed Files
//=============================================

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if ( ! class_exists('FF_MC_Sync_Editor') )
	require_once FF_MC_PLUGIN_DIR . '/admin/inc/class-ff-mc-sync-editor.php';

if ( ! class_exists('FF_MailChimp_API') )
	require_once(FF_MC_PLUGIN_DIR . '/admin/inc/integration/class-ff-mailchimp-api.php');

//=============================================
// FF_MC_Admin Class
//=============================================

class FF_MC_Admin {
	
	var $action;
	var $options;
	var $api_connected;
	var $api_authed;

	/**
	 * Class constructor
	 */
	function __construct ( )
	{
		$this->options = get_option('ff_mc_options');
		$this->action = $this->current_action();

		// If the plugin version matches the latest version escape the update function
		if ( $this->options['ff_mc_version'] != FF_MC_PLUGIN_VERSION )
			self::check_for_update();

		if ( isset($this->options['api_key']) && $this->options['api_key'] )
		{
			$this->api_authed = TRUE;
			$this->api_connected = $this->check_api_is_connected($this->options['api_key']);
		}
		else
		{
			$this->api_authed = FALSE;
		}

		add_action('admin_menu', array(&$this, 'add_menu_items'));
		add_action('admin_init', array(&$this, 'build_settings_page'));
		add_action('admin_print_styles', array(&$this, 'add_admin_styles'));
		add_action('admin_print_scripts', array(&$this, 'add_admin_scripts'));
		add_filter('plugin_action_links_' . 'fuseforms-for-mailchimp/fuseforms-for-mailchimp.php', array($this, 'plugin_list_settings_link'));
	}

	function check_for_update ()
	{
		$this->options = get_option('ff_mc_options');

		// Set the plugin version
		ff_mc_update_option('ff_mc_options', 'version', FF_MC_PLUGIN_VERSION);
	}
	
	//=============================================
	// Menus
	//=============================================

	/**
	 * Adds FuseForms menu to /wp-admin sidebar
	 */
	function add_menu_items ()
	{
		add_options_page( 'FuseForms for MC', 'FuseForms for MC', 'manage_options', 'ff_mc_settings', array($this, 'plugin_options') );
	}

	/**
	 * Adds setting link for FuseForms to plugins management page 
	 *
	 * @param   array $links
	 * @return  array
	 */
	function plugin_list_settings_link ( $links )
	{
		$url = get_admin_url() . 'options-general.php?page=ff_mc_settings';
		$settings_link = '<a href="' . $url . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	//=============================================
	// Settings Page
	//=============================================

	/**
	 * Creates settings page
	 */
	function plugin_options ()
	{
		$ff_mc_options = get_option('ff_mc_options');
		$this->plugin_settings();
	}

	/**
	 * Creates settings options
	 */
	function build_settings_page ()
	{		
		register_setting(
			'ff_mc_settings_options',
			'ff_mc_options',
			array($this, 'sanitize')
		);

		add_settings_section(
			'ff_mc_api_settings_section',
			'MailChimp API Settings',
			array($this, 'ff_mc_api_settings_section_callback'),
			FF_MC_ADMIN_PATH
		);

		add_settings_field(
			'ff_mc_api_status', 
			'API Status', 
			array($this, 'ff_mc_api_status_callback'), 
			FF_MC_ADMIN_PATH,
            'ff_mc_api_settings_section'
		);

		add_settings_field(
			'api_key', 
			'API key', 
			array($this, 'api_key_callback'), 
			FF_MC_ADMIN_PATH,
            'ff_mc_api_settings_section'
		);

		if ( $this->api_connected )
		{
			add_settings_section(
				'ff_mc_sync_settings_section',
				'Form Sync Settings',
				array($this, 'ff_mc_sync_settings_section_callback'),
				FF_MC_ADMIN_PATH
			);
		}

		add_filter(
			'update_option_ff_mc_options',
			array($this, 'update_option_ff_mc_options_callback'),
			10,
			2
		);
	}

	function ff_mc_api_settings_section_callback ( )
	{
		$this->print_hidden_settings_fields();
	}

	function print_hidden_settings_fields ()	
	{
		 // Hacky solution to solve the Settings API overwriting the default values
		$this->options = get_option('ff_mc_options');

		$ff_mc_installed	= ( isset($this->options['ff_mc_installed']) ? $this->options['ff_mc_installed'] : 1 );
		$ff_mc_db_version	= ( isset($this->options['ff_mc_db_version']) ? $this->options['ff_mc_db_version'] : FF_MC_DB_VERSION );
		$ff_mc_version		= ( isset($this->options['ff_mc_version']) ? $this->options['ff_mc_version'] : FF_MC_PLUGIN_VERSION );
		$synced_selectors	= ( isset($this->options['synced_selectors']) ? $this->options['synced_selectors'] : '' );
		$synced_lists		= ( isset($this->options['synced_lists']) ? $this->options['synced_lists'] : '' );
		
		printf(
			'<input id="ff_mc_installed" type="hidden" name="ff_mc_options[ff_mc_installed]" value="%d"/>',
			$ff_mc_installed
		);

		printf(
			'<input id="ff_mc_db_version" type="hidden" name="ff_mc_options[ff_mc_db_version]" value="%s"/>',
			$ff_mc_db_version
		);

		printf(
			'<input id="ff_mc_version" type="hidden" name="ff_mc_options[ff_mc_version]" value="%s"/>',
			$ff_mc_version
		);

		printf(
			'<input id="synced_selectors" type="hidden" name="ff_mc_options[synced_selectors]" value="%s"/>',
			$synced_selectors
		);

		printf(
			'<input id="synced_lists" type="hidden" name="ff_mc_options[synced_lists]" value="%s"/>',
			$synced_lists
		);
	}

	function update_option_ff_mc_options_callback ( $old_value, $new_value )
	{

	}

	/**
	 * Creates default settings page
	 */
	function plugin_settings ()
	{
		global  $wp_version;

		echo '<div id="fuseforms" class="ff-settings wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
		
		$this->header('FuseForms for MailChimp Settings', 'ff_mc_settings', 'Loaded Settings Page');
		
		?>
			<div class="fuseforms-settings__content">
				<form method="POST" action="options.php">
					<?php 
						settings_fields('ff_mc_settings_options');
						do_settings_sections(FF_MC_ADMIN_PATH);
						submit_button('Save Settings');
					?>
				</form>
			</div>
		<?php

		$this->footer();

		//end wrap
		echo '</div>';
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize ( $input )
	{
		$new_input = array();

		if ( isset($input['ff_mc_installed']) )
			$new_input['ff_mc_installed'] = $input['ff_mc_installed'];

		if ( isset($input['ff_mc_db_version']) )
			$new_input['ff_mc_db_version'] = $input['ff_mc_db_version'];

		if ( isset($input['ff_mc_version']) )
			$new_input['ff_mc_version'] = $input['ff_mc_version'];
			
		if ( isset($input['api_key']) )
			$new_input['api_key'] = $input['api_key'];

		if ( isset($input['synced_lists']) )
			$new_input['synced_lists'] = $input['synced_lists'];

		if ( isset($input['synced_selectors']) )
			$new_input['synced_selectors'] = $input['synced_selectors'];

		return $new_input;
	}

	//=============================================
	// Admin Styles & Scripts
	//=============================================

	/**
	 * Adds admin style sheets
	 */
	function add_admin_styles ()
	{
		wp_register_style('fuseforms-admin-css', FF_MC_PATH . '/assets/css/build/fuseforms-admin.css');
		wp_enqueue_style('fuseforms-admin-css');
	}

	/**
	 * Adds admin style sheets
	 */
	function add_admin_scripts ()
	{
		wp_register_script('fuseforms-admin', FF_MC_PATH . '/assets/js/build/fuseforms-admin.js', array ('jquery'), FALSE, TRUE);
		wp_enqueue_script('fuseforms-admin');
	}

	//=============================================
	// Internal Class Functions
	//=============================================

	/**
	 * Prints the admin page title, icon and help notification
	 *
	 * @param string
	 */
	function header ( $page_title = '', $css_class = '', $event_name = '' )
	{
		$ff_mc_options = get_option('ff_mc_options');

		?>
		
		<?php if ( $page_title ) : ?>
			<h2 class="<?php echo $css_class ?>"><?php echo $page_title; ?></h2>
		<?php endif; ?>

		<?php
	}

	function footer ()
	{
		$ff_mc_options = get_option('ff_mc_options');
		global  $wp_version;

		?>
		<div id="fuseforms-footer">
			<p class="support">			
				<a href="http://fuseforms.com">FuseForms</a> <?php echo FF_MC_PLUGIN_VERSION; ?>
				<span style="padding: 0px 5px;">|</span><a href="http://wordpress.org/support/view/plugin-reviews/fuseforms-for-mailchimp?rate=5#postform">Leave us a review</a>
			</p>

			<p class="sharing"><a href="https://twitter.com/fuseforms" class="twitter-follow-button" data-show-count="false">Follow @FuseForms</a><p>
		</div>

		<?php
	}

	/**
	 * GET and set url actions into readable strings
	 * @return string if actions are set,   bool if no actions set
	 */
	function current_action ()
	{
		if ( isset($_REQUEST['action']) && -1 != $_REQUEST['action'] )
			return $_REQUEST['action'];

		if ( isset($_REQUEST['action2']) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return FALSE;
	}

	/**
	 * Use MailChimp API key to try to grab corresponding user profile to check validity of key
	 *
	 * @param string
	 * @return bool	
	 */
	function check_api_is_connected ( $api_key )
	{
		$MailChimp = new FF_MailChimp_API($api_key);

		// can also use - helper/ping
		$response = $MailChimp->call('helper/ping');

		if ( isset($response['msg']) && $response['msg'] == "Everything's Chimpy!" )
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Format API-returned lists into parseable format on front end
	 *
	 * @return array	
	 */
	function get_lists ( )
	{
		$lists = $this->get_api_lists($this->options['api_key']);
		
		$sanitized_lists = array();
		if ( count($lists['data']) )
		{
			foreach ( $lists['data'] as $list )
			{
				$list_obj = (Object)NULL;
				$list_obj->id = $list['id'];
				$list_obj->name = $list['name'];

				array_push($sanitized_lists, $list_obj);;
			}
		}
		
		return $sanitized_lists;
	}

	/**
	 * Get lists from MailChimp account
	 *
	 * @param string
	 * @return array	
	 */
	function get_api_lists ( $api_key )
	{
		$MailChimp = new FF_MailChimp_API($api_key);

		$lists = $MailChimp->call("lists/list", array(
			"start" => 0, // optional, control paging of lists, start results at this list #, defaults to 1st page of data (page 0)
			"limit" => 25, // optional, control paging of lists, number of lists to return with each call, defaults to 25 (max=100)
			"sort_field" => "created", // optional, "created" (the created date, default) or "web" (the display order in the web app). Invalid values will fall back on "created" - case insensitive.
			"sort_dir" => "DESC" // optional, "DESC" for descending (default), "ASC" for Ascending. Invalid values will fall back on "created" - case insensitive. Note: to get the exact display order as the web app you'd use "web" and "ASC"
		));

		return $lists;
	}

	/**
	 * Prints API key input for settings page
	 */
	function ff_mc_api_status_callback ()
	{
		printf(
			'<span class="api-status %s">%s</span>', ( $this->api_connected ? 'api-status-connected' : '' ), ( $this->api_connected ? 'CONNECTED' : 'NOT CONNECTED' )
		);
	}

	/**
	 * Prints API key input for settings page
	 */
	function api_key_callback ()
	{
		$api_key = ( isset($this->options['api_key']) && $this->options['api_key'] ? $this->options['api_key'] : '' ); // Get header from options, or show default
		
		printf(
			'<input id="api_key" type="text" id="title" name="ff_mc_options[api_key]" value="%s" style="width: 430px;"/>',
			$api_key
		);

		$integration_name = 'MailChimp';
		$integration_url = 'mailchimp.com';
		$integration_list_url = 'http://admin.mailchimp.com/lists/new-list/';
		$settings_page_anchor_id = '#ff_mc_mls_api_key';
		
		$no_api_key_message = '<p>Get your API key from <a href="http://admin.mailchimp.com/account/api/" target="_blank">MailChimp.com here</a> or read the guide on <a target="_blank" href="http://kb.mailchimp.com/accounts/management/about-api-keys#Find-or-Generate-Your-API-Key">how to get an API key here</a></p>';
		$invalid_key_message = 'It looks like your ' . $integration_name . ' API key is invalid...';
		$invalid_key_link = 'Get your API key from <a href="http://admin.mailchimp.com/account/api/" target="_blank">MailChimp.com</a>';

		if ( isset($api_key) && ! $api_key )
		{
			echo $no_api_key_message;
		}
		else if ( $this->api_authed && ! $this->api_connected )
		{
			echo '<p style="color: red;">' . $invalid_key_message . '</p>';
			echo '<p>' . $invalid_key_link . ' then try copying and pasting it again.</p>';
		}

	}

	function ff_mc_sync_settings_section_callback ( )
	{
		?>
		<div class="fuseforms-contacts">
			<?php
				$syncer = new FF_MC_Sync_Editor(FALSE);
			?>

			<div class="">
				<table class="form-table"><tbody>
					<tr>
						<th scope="row">Form selectors to trigger integration</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Automatically sync contacts who fill out any of these forms</span></legend>
								<?php 
									$sync_form_selectors = ( $this->options['synced_selectors'] ? explode(',', str_replace(' ', '', $this->options['synced_selectors'])) : '');
									
									if ( ! in_array('#registerform', $syncer->selectors) )
									{
										array_push($syncer->selectors, '#registerform');
									}

									if ( ! in_array('#commentform', $syncer->selectors) )
									{
										array_push($syncer->selectors, '#commentform');
									}

									foreach ( $syncer->selectors as $selector )
									{
										$html_id = 'form_syncs_' . str_replace(array('#', '.'), array('id_', 'class_'), $selector); 
										$selector_set = FALSE;
										
										if ( isset($this->options['synced_selectors']) && strstr($this->options['synced_selectors'], $selector) )
										{
											$selector_set = TRUE;
											$key = array_search($selector, $sync_form_selectors);
											if ( $key !== FALSE )
												unset($sync_form_selectors[$key]);
										}
										
										echo '<label for="' . $html_id . '">';
											echo '<input class="form_sync" name="' . $html_id . '" type="checkbox" id="' . $html_id . '" value="" ' . ( $selector_set ? 'checked' : '' ) . '>';
											echo $selector;
										echo '</label><br>';
									}

									// 
								?>
							</fieldset>
							<br>
							<input id="form_syncs_custom" name="form_syncs_custom" type="text" value="<?php echo ( $sync_form_selectors ? implode(', ', $sync_form_selectors) : ''); ?>" class="regular-text" placeholder="#form-id, .form-class">
							<p class="description">Include additional form's css selectors.</p>
						</td>
					</tr>

					
					<?php
						$integraton_name = 'MailChimp';

						if ( $this->api_authed && $this->api_connected )
							$lists = $this->get_lists();
						
						$synced_lists = ( isset($this->options['synced_lists']) ? $this->options['synced_lists'] : '' );
						$integration_name = 'MailChimp';
						$integration_url = 'mailchimp.com';
						$integration_list_url = 'http://admin.mailchimp.com/lists/new-list/';
						$settings_page_anchor_id = '#ff_mc_mls_api_key';
						$invalid_key_message = 'It looks like your ' . $integration_name . ' API key is invalid...<br/><br/>';
						$invalid_key_link = 'Get your API key from <a href="http://admin.mailchimp.com/account/api/" target="_blank">MailChimp.com here</a>';

						echo '<tr>';
							echo '<th scope="row">Push contacts to these ' . $integraton_name . ' lists</th>';
							echo '<td>';
								echo '<fieldset>';
									echo '<legend class="screen-reader-text"><span>Push contacts to these ' . $integraton_name . ' email lists</span></legend>';

									if ( count($lists) )
									{
										foreach ( $lists as $list )
										{
											$synced = FALSE;

											if ( $synced_lists )
											{
												if ( isset($this->options['synced_lists']) && strstr($this->options['synced_lists'], $list->id) )
												{
													$synced = TRUE;
												}
											}

											echo '<label for="' . $list->id  . '">';
												echo '<input class="list_sync" name="' . $list->id  . '" type="checkbox" id="' . $list->id  . '" value="' . $list->name . '" ' . ( $synced ? 'checked' : '' ) . '>';
												echo $list->name;
											echo '</label><br>';
										}
									}
									else
									{
										echo 'It looks like you don\'t have any ' . $integration_name . ' lists yet...<br/><br/>';
										echo '<a href="' . $integration_list_url . '" target="_blank">Create a list on ' . $integration_url . '</a>';
									}

								echo '</fieldset>';
							echo '</td>';
						echo '</tr>';
					?>
					
				</tbody></table>
				<input type="hidden" name="sync_id" value="<?php echo $syncer->sync_id; ?>"/>	
			</div>

		</div>

		<?php
	}
}

?>