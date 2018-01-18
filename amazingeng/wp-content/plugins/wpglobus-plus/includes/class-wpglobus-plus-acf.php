<?php
/**
 * Class WPGlobusPlus_Acf
 * @since 1.0.0
 */
 
if ( ! class_exists( 'WPGlobusPlus_Acf' ) ) :
	
	class WPGlobusPlus_Acf {
		
		/**
		 * Constructor
		 */
		public function __construct() {
			
			$enabled_pages = array(
				'post.php',
				'post-new.php'
			);

			if ( WPGlobus_WP::is_pagenow( $enabled_pages ) ) :
			
				add_filter( 'acf/fields/wysiwyg/toolbars', array( $this, 'add_buttons' ), 1 );
				add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ), 1 );
				add_action( 'admin_print_scripts', array( $this, 'admin_scripts' ) );
			
			endif;
			
		}	

		/**
		 * Add language buttons to toolbars
		 *
		 * @since 1.0.0
		 *
		 * @param array $buttons
		 * @return array
		 */
		function add_buttons( $buttons ){

			$buttons['Full'][1][] = 'wpglobus_plus_acf_separator';
			$buttons['Basic'][1][] = 'wpglobus_plus_acf_separator';
			
			foreach( WPGlobus::Config()->enabled_languages as $language ) {
				$buttons['Full'][1][]  = 'wpglobus_plus_acf_button_' . $language;
				$buttons['Basic'][1][] = 'wpglobus_plus_acf_button_' . $language;
			}	
			
			return $buttons;
		}	

		/** 
		 * Declare script for new buttons
		 *
		 * @since 1.0.0
		 *
		 * @param array $plugin_array
		 * @return array
		 */
		function mce_external_plugins( $plugin_array ) {
			$plugin_array['wpglobus_plus_acf_separator'] = 
				WPGlobusPlus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-plus-acf'  . WPGlobus::SCRIPT_SUFFIX() . '.js';
			foreach( WPGlobus::Config()->enabled_languages as $language ) {
				$plugin_array['wpglobus_plus_acf_button_' . $language] = 
					WPGlobusPlus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-plus-acf' . WPGlobus::SCRIPT_SUFFIX() . '.js';
			}

			return $plugin_array;
		}
		
		/** 
		 * Admin print scripts
		 *
		 * @since 1.1.11
		 *
		 * @return void
		 */		
		function admin_scripts() {
			
			wp_register_script(
				'wpglobus-plus-acf-init',
				WPGlobusPlus::$PLUGIN_DIR_URL . "includes/js/wpglobus-plus-acf-init" . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery' ),
				WPGLOBUS_PLUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-plus-acf-init' );
			wp_localize_script(
					'wpglobus-plus-acf-init',
					'WPGlobusPlusAcf',
					array(
						'wpglobus_plus_version'  => WPGLOBUS_PLUS_VERSION,
						'removeEmptyP' => apply_filters( 
							/**
							 * Filter to remove empty p from ACF wysiwyg editor.
							 * Returning boolean.
							 * @since 1.1.11
							 *
							 * @param boolean False.
							 */
							'wpglobus_plus_acf_remove_empty_p', 
							false 
						)
					)
				);				
			
		}	
		
	}	// end class WPGlobusPlus_Acf
	
endif;
