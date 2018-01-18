<?php
/**
 * Module WPGlobus Editor
 *
 * @since 1.1.0
 *
 * @package WPGlobus Plus
 * @subpackage Administration
 */

/**
 * @see include_once( 'class-wpglobus-plus-wpglobeditor.php' ) in wpglobus-plus-main.php
 */

if ( ! class_exists('WPGlobus_Editor') ) :
	
	class WPGlobus_Editor {
	
		var $option_key = 'wpglobus_plus_wpglobeditor';
		
		var $opts = array();
		
		function __construct() {
			
			if ( ! empty( $_GET['page'] ) && WPGlobusPlus::WPGLOBUS_PLUS_OPTIONS_PAGE == $_GET['page'] ) 
			{
				/**
				 * Don't run at WPGLOBUS_PLUS_OPTIONS_PAGE page
				 */
				return;	
			}	

			$this->opts = get_option( $this->option_key );
			
			add_filter( 'wpglobus_enabled_pages', array( $this, 'enable_pages' ) );
			add_filter( 'admin_print_scripts', array( $this, 'on_admin_scripts' ) );
			
		}

		/**
		 * Enqueue admin scripts
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public function on_admin_scripts() {
			
			global $pagenow;

			$elements = array();
			if ( ! empty( $this->opts['page_list'][$pagenow] ) ) {
				$elements = $this->opts['page_list'][$pagenow];	
			} else if ( ! empty($_GET['page']) && ! empty( $this->opts[ 'page_list' ][ $_GET['page'] ] ) ) {
				$elements = $this->opts[ 'page_list' ][ $_GET['page'] ];	
			}

			if ( empty( $elements ) ) {
				return;	
			}	
			
			/**
			 * Module WPGlobus Editor
			 */		
			wp_register_script(
				'wpglobus-plus-wpglobeditor',
				plugin_dir_url( __FILE__ ) . 'js/wpglobus-plus-wpglobeditor' . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery', 'wpglobus-admin' ),
				WPGLOBUS_PLUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-plus-wpglobeditor' );

			wp_localize_script(
				'wpglobus-plus-wpglobeditor',
				'WPGlobusPlusEditor',
				array(
					'version'      => WPGLOBUS_PLUS_VERSION,
					'mode'	       => 'ueditor',
					'process_ajax' => __CLASS__ . '_process_ajax',
					'module'	   => 'wpglobeditor',
					'pagenow'	   => $pagenow,
					'page'		   => empty( $_GET['page'] ) ? '' : $_GET['page'], 
					'elements'	   => $elements 	
				)
			);
		
		}	
		
		function enable_pages( $pages ) {
			
			if ( ! empty( $this->opts['page_list'] ) ) {
				
				foreach( $this->opts['page_list'] as $page=>$elements ) {

					$pages[] = $page;

				}	
				
			}	
			
			return $pages;
			
		}		
		
	}
	
	$WPGlobus_Editor = new WPGlobus_Editor();
	
endif;