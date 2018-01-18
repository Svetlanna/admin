<?php
//=============================================
// FF_MC_Sync_Editor Class
//=============================================
class FF_MC_Sync_Editor {
	
	/**
	 * Variables
	 */
	var $sync_id;
	var $details;
	var $selectors;

	/**
	 * Class constructor
	 */
	function __construct ( $sync_id = FALSE ) 
	{
		if ( $sync_id )
			$this->sync_id = $sync_id;

		$this->selectors = $this->get_form_selectors();
	}

	/**
	 * Get all the details for a sync
	 *
	 * @param   int
	 * @return  object
	 */
	function get_sync_details ( $sync_id )
	{
		global $wpdb;

		$q = $wpdb->prepare("SELECT * FROM $wpdb->ff_mc_syncs WHERE sync_id = %d", $this->sync_id);
		$this->details =  $wpdb->get_row($q);
	}

	/**
	 * Get all the existing form selectors sorted by frequency
	 *
	 * @return  array
	 */
	function get_form_selectors ( )
	{
		global $wpdb;
		$selectors = array();

		$q = "SELECT COUNT(form_selector_classes) AS freq, form_selector_classes FROM $wpdb->ff_mc_submissions WHERE form_selector_classes != '' GROUP BY form_selector_classes ORDER BY freq DESC";
		$classes = $wpdb->get_results($q);
		
		if ( count($classes) )
		{
			foreach ( $classes as $class )
			{
				foreach ( explode(',', $class->form_selector_classes) as $class_selector )
				{
					if ( ! in_array('.' . $class_selector, $selectors) && $class_selector )
						array_push($selectors, '.' . $class_selector);
				}
			}
		}

		$q = "SELECT COUNT(form_selector_id) AS freq, form_selector_id FROM $wpdb->ff_mc_submissions WHERE form_selector_id != '' GROUP BY form_selector_id ORDER BY freq DESC";
		$ids = $wpdb->get_results($q);

		if ( count($ids) )
		{
			foreach ( $ids as $id )
			{
				if ( ! in_array('#' . $id->form_selector_id, $selectors) && $id->form_selector_id )
					array_push($selectors, '#' . $id->form_selector_id);
			}
		}

		return $selectors;
	}

	/**
	 * Add a new sync in the ff_syncs table
	 *
	 * @param 	string
	 * @param 	string
	 * @param 	string
	 * @return  int 		sync_id of last inserted sync
	 */
	function add_sync ( $sync_text, $sync_form_selectors, $sync_connected_lists )
	{
		global $wpdb;

		$q = "SELECT MAX(sync_order) FROM $wpdb->ff_mc_syncs";
		$sync_order = $wpdb->get_var($q);
		$sync_order = ( $sync_order ? $sync_order + 1 : 1 );
		$sync_slug = $this->generate_slug($sync_text);

		$q = $wpdb->prepare("
			INSERT INTO $wpdb->ff_mc_syncs ( sync_text, sync_slug, sync_form_selectors, sync_connected_lists, sync_order )
			VALUES ( %s, %s, %s, %s, %d )", $sync_text, $sync_slug, $sync_form_selectors, $sync_connected_lists, $sync_order);
		$wpdb->query($q);

		return $wpdb->insert_id;
	}

	/**
	 * Update an existing sync in the ff_syncs  table
	 *
	 * @param 	int
	 * @param 	string
	 * @param 	string
	 * @param 	string 		serialized array(array('esp' => 'mailchimp', 'list_id' => 'abc123'))
	 * @return  bool
	 */
	function save_sync ( $sync_id, $sync_text, $sync_form_selectors, $sync_connected_lists )
	{
		global $wpdb;

		$sync_slug = $this->generate_slug($sync_text, $sync_id);

		$q = $wpdb->prepare("
			UPDATE $wpdb->ff_mc_syncs 
			SET sync_text = %s, sync_slug = %s, sync_form_selectors = %s, sync_connected_lists = %s
			WHERE sync_id = %d", $sync_text, $sync_slug, $sync_form_selectors, $sync_connected_lists, $sync_id);
		$result = $wpdb->query($q);

		// Add a method to loop through all the lists here and 

		return $result;
	}

	/**
	 * Delete a sync from the ff_syncs table
	 *
	 * @param 	int
	 * @param 	string
	 * @return  bool
	 */
	function delete_sync ( $sync_id )
	{
		global $wpdb;

		$q = $wpdb->prepare("
			UPDATE $wpdb->ff_mc_syncs 
			SET sync_deleted = 1
			WHERE sync_id = %d", $sync_id);

		$result = $wpdb->query($q);

		return $result;
	}

	/**
	 * Generates a slug based off a string. If slug exists, then appends -N until the slug is free
	 *
	 * @param 	string
	 * @param 	int
	 * @return  string
	 */
	function generate_slug ( $sync_text, $sync_id = 0 )
	{
		global $wpdb;

		$sync_slug = sanitize_title($sync_text);
		$slug_used = TRUE;
		$slug_int_check = 0;

		while ( $slug_used )
		{
			// slug_int_check is set to 1, but we only want to use it if the slug exists, so kill it in the first iteration
			
			if ( $slug_int_check )
				$sync_slug_modified = $sync_slug . '-' . $slug_int_check;
			else
				$sync_slug_modified = $sync_slug;

			$q = $wpdb->prepare("SELECT sync_slug FROM $wpdb->ff_mc_syncs WHERE sync_slug = %s " . ( $sync_id ? $wpdb->prepare(" AND sync_id != %d ", $sync_id) : '' ), $sync_slug_modified);
			$slug_used = $wpdb->get_var($q);

			if ( $slug_used )
				$slug_int_check++;
			else
				$sync_slug = $sync_slug_modified;
		}

		return $sync_slug;
	}
}
?>