<?php
/**
 * Plugin Name: TG Customized Tags
 * Plugin URI : http://www.tekgazet.com/tg-customized-tags-plugin
 * Description: Display fully customized and configurable tags, categories or other taxonomy in tag-cloud with widget and shortcodes.
 * Version: 1.0
 * Author: Ashok Dhamija
 * Author URI: http://tilakmarg.com/dr-ashok-dhamija/
 * License: GPLv2 or later
 */
 
// Add a menu for our option page
add_action('admin_menu', 'tg_customized_tags_add_page');
function tg_customized_tags_add_page() {
	add_options_page( 'TG Customized Tags Plugin', 'TG Customized Tags', 'manage_options', 'tg_customized_tags', 'tg_customized_tags_option_page' );
}

// Draw the option page
function tg_customized_tags_option_page() {
	
	//Check if it is first time after installation, if so, set default values
	$valid = array();
	$valid = get_option( 'tg_customized_tags_options' );
	if( !$valid ) {	
		$valid['smallest'] = 8;
		$valid['largest'] = 22;
		$valid['unit'] = 'pt';
		$valid['number'] = 45;
		$valid['format'] = 'flat';
		$valid['separator'] = "\n";
		$valid['orderby'] = 'name';
		$valid['order'] = 'ASC';
		$valid['exclude'] = null;
		$valid['include'] = null;
		$valid['taxonomy'] = 'post_tag';

		update_option( 'tg_customized_tags_options', $valid );
	}
	
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>TG Customized Tags Page</h2>
		<form action="options.php" method="post">
			<?php settings_fields('tg_customized_tags_options'); ?>
			<?php do_settings_sections('tg_customized_tags'); ?>
			<input name="Submit" type="submit" value="Save Changes" />
			<input name="Submit2" type="submit" value="Reset to Default Values" />		
			<input name="Submit3" type="submit" value="Cancel changes" />	
		</form>
	</div>
	<?php
}

// Register and define the settings
add_action('admin_init', 'tg_customized_tags_admin_init');
function tg_customized_tags_admin_init(){
	register_setting(
		'tg_customized_tags_options',
		'tg_customized_tags_options',
		'tg_customized_tags_validate_options'
	);
	
	add_settings_section(
		'tg_customized_tags_about',
		'About TG Customized Tags Plugin',
		'tg_customized_tags_section_about_text',
		'tg_customized_tags'
	);
	
	add_settings_section(
		'tg_customized_tags_shortcodes',
		'TG Customized Tags Plugin - ShortCode Usage',
		'tg_customized_tags_section_shortcodes_text',
		'tg_customized_tags'
	);
		
	add_settings_section(
		'tg_customized_tags_main',
		'TG Customized Tags Plugin Settings',
		'tg_customized_tags_section_text',
		'tg_customized_tags'
	);
	
	
	add_settings_field(
		'tg_customized_tags_number',
		'Number of Items to show in tag-cloud:',
		'tg_customized_tags_setting_input_number',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_unit',
		'Select Unit for font size:',
		'tg_customized_tags_setting_select_unit',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_smallest',
		'Smallest font size for tag-cloud:',
		'tg_customized_tags_setting_input_smallest',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_largest',
		'Largest font size for tag-cloud:',
		'tg_customized_tags_setting_input_largest',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_taxonomy',
		'Select taxonomy to show in tag-cloud:',
		'tg_customized_tags_setting_select_taxonomy',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_format',
		'Select format to show tag-cloud (flat or list):',
		'tg_customized_tags_setting_select_format',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_separator',
		'Enter Separator (used in Flat format to separate tags):',
		'tg_customized_tags_setting_input_separator',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_include',
		'Enter comma-separated list of IDs of tags to be included:',
		'tg_customized_tags_setting_input_include',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_exclude',
		'Enter comma-separated list of IDs of tags to be excluded:',
		'tg_customized_tags_setting_input_exclude',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_orderby',
		'Select order by tag-count or by tag name:',
		'tg_customized_tags_setting_select_orderby',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);
	
	add_settings_field(
		'tg_customized_tags_order',
		'Select ascending, descending or random order of tags:',
		'tg_customized_tags_setting_select_order',
		'tg_customized_tags',
		'tg_customized_tags_main'
	);

}

// Draw the section header
function tg_customized_tags_section_about_text() {
	echo '<p>TG Customized Tags is a plugin developed by <a href="http://tilakmarg.com/dr-ashok-dhamija/" target="_blank">Ashok Dhamija</a>. For any help or support issues, please leave your comments at <a href="http://www.tekgazet.com/tg-customized-tags-plugin" target="_blank">TG Customized Plugin Page</a>. If you like this plugin, please vote favorably for it at its <a href="https://wordpress.org/plugins/tg-customized-tags/" target="_blank">WordPress plugin page</a>.</p><hr />';
}



// Draw the section header
function tg_customized_tags_section_shortcodes_text() {
	echo '<p>TG Customized Tags provides you two ShortCodes that can be inserted by you on any post or page to display Customized Tags with several options configured as you like. These two ShortCodes are as under: <strong>[tg_customized_tags_default]</strong>&nbsp;and&nbsp;<strong>[tg_customized_tags_current]</strong></p> <p>Detailed procedure for using these shortcodes, along with their 11 customizable attributes with their permissible values and with sample usages, is given at the <a href="http://www.tekgazet.com/tg-customized-tags-plugin">Plugin page</a>&nbsp;of TG Customized Tags plugin. Please visit that page for full details.</p> <hr />';
	
}


// Draw the section header
function tg_customized_tags_section_text() {
	echo '<p>Enter your settings here for the tag-cloud. After that, you can simply use the normal or default tag-cloud widget [which is available on the Appearance -> Widgets menu of every WordPress site] on your website and the settings entered by you here will be automatically used for that tag-cloud widget. Moreover, if you use the <strong>[tg_customized_tags_current]</strong> shortcode provided by this plugin, it will also use the settings entered by you here.</p>';
	//Display the Save Changes and Reset buttons at the top
	echo '<input name="Submit" type="submit" value="Save Changes" />';
	echo '<input name="Submit2" type="submit" value="Reset to Default Values" />';	
	echo '<input name="Submit3" type="submit" value="Cancel changes" />';
}

// Display and fill the form field
function tg_customized_tags_setting_input_number() {
	// get option 'number' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$number = $options['number'];
	// echo the field
	echo "<input id='number' name='tg_customized_tags_options[number]' type='text' value='$number' />";
	echo '<p>Please enter a number only. Entering 0 will show all Items.<p>';
}

// Display and fill the form field
function tg_customized_tags_setting_select_unit() {
	// get option 'unit' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$unit = $options['unit'];
	
	//display the option select field
	echo '<select name="tg_customized_tags_options[unit]">';
		echo '<option value="pt" '. selected( $unit, 'pt', false ) .'>Points (pt)</option>';
		echo '<option value="px" '. selected( $unit, 'px', false ) .'>Pixels (px)</option>';
		echo '<option value="%" '. selected( $unit, '%', false ) .'>Percent (%)</option>';
		echo '<option value="em" '. selected( $unit, 'em', false ) .'>Ems (em) (%)</option>';
		echo '<option value="rem" '. selected( $unit, 'rem', false ) .'>Root ems (rem)</option>';
		echo '<option value="pc" '. selected( $unit, 'pc', false ) .'>Picas (pc)</option>';
		echo '<option value="mm" '. selected( $unit, 'mm', false ) .'>Millimeters (mm)</option>';
		echo '<option value="cm" '. selected( $unit, 'cm', false ) .'>Centimeters (cm)</option>';
	echo '</select>';
	echo '<p>Please select Unit for font size (to be selected in next two items).<p>';	   
}


// Display and fill the form field
function tg_customized_tags_setting_input_smallest() {
	// get option 'smallest' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$smallest = $options['smallest'];
	// echo the field
	echo "<input id='smallest' name='tg_customized_tags_options[smallest]' type='text' value='$smallest' />";
	echo '<p>Please enter a number only. Number will depend on Unit selected above for font size.<p>';
}

// Display and fill the form field
function tg_customized_tags_setting_input_largest() {
	// get option 'largest' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$largest = $options['largest'];
	// echo the field
	echo "<input id='largest' name='tg_customized_tags_options[largest]' type='text' value='$largest' />";
	echo '<p>Please enter a number only. Number will depend on Unit selected above for font size.<p>';
}


// Display and fill the form field
function tg_customized_tags_setting_select_taxonomy() {
	// get option 'taxonomy' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$taxonomy = $options['taxonomy'];
	
	//display the option select field
	echo '<select name="tg_customized_tags_options[taxonomy]">';
		echo '<option value="post_tag" '. selected( $taxonomy, 'post_tag', false ) .'>Tags</option>';
		echo '<option value="category" '. selected( $taxonomy, 'category', false ) .'>Categories</option>';
		echo '<option value="link_category" '. selected( $taxonomy, 'link_category', false ) .'>Link Categories</option>';
	echo '</select>';
	echo '<p>Please select what taxonomy should be shown in tag-cloud.<p>';	   
}


// Display and fill the form field
function tg_customized_tags_setting_select_format() {
	// get option 'format' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$format = $options['format'];
	
	//display the option select field
	echo '<select name="tg_customized_tags_options[format]">';
		echo '<option value="flat" '. selected( $format, 'flat', false ) .'>Flat</option>';
		echo '<option value="list" '. selected( $format, 'list', false ) .'>List</option>';
	echo '</select>';
	echo '<p>Please select either flat or list format in which to show tag-cloud.<p>';	   
}

// Display and fill the form field
function tg_customized_tags_setting_input_separator() {
	// get option 'separator' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$separator = $options['separator'];
	// echo the field
	echo "<input id='separator' name='tg_customized_tags_options[separator]' type='text' value='$separator' />";
	echo '<p>Please enter one or more characters with which you want to separate tags in the Flat format. Default is space character<p>';
}

// Display and fill the form field
function tg_customized_tags_setting_input_include() {
	// get option 'include' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$include = $options['include'];
	// echo the field
	echo "<input id='include' name='tg_customized_tags_options[include]' type='text' value='$include' />";
	echo '<p>Comma separated list of tags (term_id) to include. For example, 5,27 means tags that have term_id 5 or 27 will be the only tags displayed. Leaving it blank will include everything, subject to maximum number selected by you above.<p>';
}

// Display and fill the form field
function tg_customized_tags_setting_input_exclude() {
	// get option 'exclude' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$exclude = $options['exclude'];
	// echo the field
	echo "<input id='exclude' name='tg_customized_tags_options[exclude]' type='text' value='$exclude' />";
	echo '<p>Comma separated list of tags (term_id) to exclude. For example, 5,27 means tags that have term_id 5 or 27 will NOT be displayed. Leaving it blank will exclude nothing, subject to maximum number selected by you above.<p>';
}

// Display and fill the form field
function tg_customized_tags_setting_select_orderby() {
	// get option 'orderby' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$orderby = $options['orderby'];
	
	//display the option select field
	echo '<select name="tg_customized_tags_options[orderby]">';
		echo '<option value="name" '. selected( $orderby, 'name', false ) .'>Tag name</option>';
		echo '<option value="count" '. selected( $orderby, 'count', false ) .'>Number of posts</option>';
	echo '</select>';
	echo '<p>Please select whether to order by tag-count (number of posts with a tag) or by tag-name.<p>';	   
}

// Display and fill the form field
function tg_customized_tags_setting_select_order() {
	// get option 'order' value from the database
	$options = get_option( 'tg_customized_tags_options' );
	$order = $options['order'];
	
	//display the option select field
	echo '<select name="tg_customized_tags_options[order]">';
		echo '<option value="ASC" '. selected( $order, 'ASC', false ) .'>Ascending</option>';
		echo '<option value="DESC" '. selected( $order, 'DESC', false ) .'>Descending</option>';
		echo '<option value="RAND" '. selected( $order, 'RAND', false ) .'>Random</option>';
	echo '</select>';
	echo '<p>Please select whether to order tags in ascending, descending or random order.<p>';	   
}


// Validate user input 
function tg_customized_tags_validate_options( $input ) {
		
	$valid = array();
	
	//Reset all to default values, if needed
	if ( isset( $_POST['Submit2'] ) ) 
	{ 
		$valid['smallest'] = 8;
		$valid['largest'] = 22;
		$valid['unit'] = 'pt';
		$valid['number'] = 45;
		$valid['format'] = 'flat';
		$valid['separator'] = "\n";
		$valid['orderby'] = 'name';
		$valid['order'] = 'ASC';
		$valid['exclude'] = null;
		$valid['include'] = null;
		$valid['taxonomy'] = 'post_tag';
		
		//Show message for defaults restored
		add_settings_error(
			'tg_customized_tags_option_page',
			'tg_customized_tags_texterror',
			'Default values have been restored.',
			'updated'
			);	
			
		return $valid;
	}
	
	
	//Cancel changes, if needed
	if ( isset( $_POST['Submit3'] ) ) 
	{ 
		$options = get_option( 'tg_customized_tags_options' );
		
		$valid['smallest'] = $options['smallest'] ;
		$valid['largest'] = $options['largest'] ;
		$valid['unit'] = $options['unit'] ;
		$valid['number'] = $options['number'] ;
		$valid['format'] = $options['format'] ;
		$valid['separator'] = $options['separator'] ;
		$valid['orderby'] = $options['orderby'] ;
		$valid['order'] = $options['order'] ;
		$valid['exclude'] = $options['exclude'] ;
		$valid['include'] = $options['include'] ;
		$valid['taxonomy'] = $options['taxonomy'] ;
		
		//Show message for defaults restored
		add_settings_error(
			'tg_customized_tags_option_page',
			'tg_customized_tags_texterror',
			'Cancelled the changes made.',
			'updated'
			);	
			
		return $valid;
	}
		
	if ((is_numeric ($input['number'])) AND ($input['number'] >= 0))
	{
		$valid['number'] = absint( $input['number'] );
	}
	else 
	{
		add_settings_error(
			'tg_customized_tags_number',
			'tg_customized_tags_texterror',
			'Error: Please enter a valid positive numeric value for Number of items to show!',
			'error'
			);
	}
				
	
	

	if ((is_numeric ($input['smallest'])) AND ($input['smallest'] >= 0))
	{
		$valid['smallest'] = absint( $input['smallest'] );
	}
	else 
	{
		add_settings_error(
			'tg_customized_tags_smallest',
			'tg_customized_tags_texterror',
			'Error: Please enter a valid positive numeric value for Smallest font size!',
			'error'
			);		
	}
	
	
	if ((is_numeric ($input['largest'])) AND ($input['largest'] >= 0))
	{
		$valid['largest'] = absint( $input['largest'] );
	}
	else 
	{
		add_settings_error(
			'tg_customized_tags_largest',
			'tg_customized_tags_texterror',
			'Error: Please enter a valid numeric value for Largest font size!',
			'error'
			);		
	}
	
	$valid['taxonomy'] = $input['taxonomy'] ;
	$valid['format'] = $input['format'] ;
	$valid['separator'] = $input['separator'] ;
	$valid['include'] = $input['include'] ; 
	$valid['exclude'] = $input['exclude'] ; 
	$valid['unit'] = $input['unit'] ;
	$valid['orderby'] = $input['orderby'] ;
	$valid['order'] = $input['order'] ;
	
	return $valid;
		
}



//
function tg_customized_tags_change_widget( $args ) {
	
	$options = get_option( 'tg_customized_tags_options' );
 
	$args['smallest'] = $options['smallest'] ;
	$args['largest'] = $options['largest'] ;
	$args['unit'] = $options['unit'] ;
	$args['number'] = $options['number'] ;
	$args['format'] = $options['format'] ;
	$args['separator'] = $options['separator'] ;
	$args['orderby'] = $options['orderby'] ;
	$args['order'] = $options['order'] ;
	$args['exclude'] = $options['exclude'] ;
	$args['include'] = $options['include'] ;
	$args['taxonomy'] = $options['taxonomy'] ;

	return $args;
   
}

add_filter( 'widget_tag_cloud_args', 'tg_customized_tags_change_widget' );
 

//shortcode for inserting customized tags (default) in post or page
function tg_customized_tags_shortcode_default($atts) {

   $defaults = array('smallest'                  => 8, 
                     'largest'                   => 22,
                     'unit'                      => 'pt', 
                     'number'                    => 45,  
                     'format'                    => 'flat',
                     'separator'                 => "\n",
                     'orderby'                   => 'name', 
                     'order'                     => 'ASC',
                     'exclude'                   => null, 
                     'include'                   => null, 
                     'link'                      => 'view',
                     'echo'                      => false,
                     'taxonomy'                  => 'post_tag');
   
     					 
  $return = shortcode_atts( $defaults, $atts );
  return wp_tag_cloud($return);
}


//shortcode for inserting customized tags (current) in post or page
function tg_customized_tags_shortcode_current($atts) {

	$options = get_option( 'tg_customized_tags_options' );
 
   $defaults = array('smallest'                  => $options['smallest'], 
                     'largest'                   => $options['largest'],
                     'unit'                      => $options['unit'], 
                     'number'                    => $options['number'],  
                     'format'                    => $options['format'],
                     'separator'                 => $options['separator'],
                     'orderby'                   => $options['orderby'], 
                     'order'                     => $options['order'],
                     'exclude'                   => $options['exclude'], 
                     'include'                   => $options['include'], 
                     'link'                      => 'view',
                     'echo'                      => false,
                     'taxonomy'                  => $options['taxonomy']);
   
     					 
  $return = shortcode_atts( $defaults, $atts );
  return wp_tag_cloud($return);
}

add_shortcode('tg_customized_tags_default', 'tg_customized_tags_shortcode_default');

add_shortcode('tg_customized_tags_current', 'tg_customized_tags_shortcode_current');
 
 
?>