<?php
/**
 * Main Module
 *
 * @since      1.0.0
 * @package    WPGlobus Plus
 * @subpackage Administration
 */

/**
 * Class WPGlobusPlus
 */
class WPGlobusPlus {

	/**
	 * All options page
	 */
	const WPGLOBUS_PLUS_OPTIONS_PAGE = 'wpglobus-plus-options';

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_URL = '';

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_PATH = '';

	/** @var string[] List of modules */
	public $modules = array();

	/** @var string Key for the `options` table */
	public $option_key = 'wpglobus_plus_options';

	/** @var array Options */
	public $options = array();


	/**
	 * Module publish
	 * @todo doc
	 */
	public $bulk_status_link;

	/**
	 * @param string[] $modules
	 */
	public function __construct( $modules ) {

		$this->bulk_status_link = admin_url( 'admin.php' ) .
		                          '?page=wpglobus-set-draft' .
		                          '&lang={{language}}' .
		                          '&post_type={{post_type}}';

		$this->modules = $modules;

		$this->options = (array) get_option( $this->option_key );

		add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array(
			$this,
			'on_process_ajax'
		) );

		add_action( 'admin_print_scripts', array(
			$this,
			'on_admin_scripts'
		) );

		add_action( 'admin_print_styles', array(
			$this,
			'on_admin_styles'
		) );

		add_action( 'admin_menu', array(
			$this,
			'on_admin_menu'
		) );

		add_action( 'wpglobus_customize_register', array(
			$this,
			'customize_register'
		) );

		add_action( 'wpglobus_customize_data', array(
			$this,
			'customize_data'
		) );

	}

	/**
	 * Register data for customizer
	 *
	 * @since 1.1.9
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function customize_data( $data ) {
		$data['sections']['wpglobus_plus_section']                                                          = 'wpglobus_plus_section';
		$data['settings']['wpglobus_plus_section']['wpglobus_customize_plus_selector_menu_style']['type']   = 'select';
		$data['settings']['wpglobus_plus_section']['wpglobus_customize_plus_selector_menu_style']['option'] = 'switcher_menu_style';

		return $data;
	}

	/**
	 * Add settings for customizer
	 *
	 * @param WP_Customize_Manager $wp_customize
	 *
	 * @since 1.1.9
	 */
	public function customize_register( $wp_customize ) {

		/**
		 * SECTION: WPGlobusPlus
		 */
		$wp_customize->add_section( 'wpglobus_plus_section', array(
			'title'    => __( 'WPGlobus Plus', 'wpglobus' ),
			'priority' => 100,
			'panel'    => 'wpglobus_settings_panel'
		) );

		/**
		 * Check for Switcher Menu: Customize the Language Switcher Menu layout
		 */
		$load = true;

		if ( isset( $this->options['menu']['active_status'] ) &&
		     ! $this->options['menu']['active_status']
		) {
			$load = false;
		}

		if ( $load ) :

			/** WPGlobus::Config()->extended_options[ 'switcher_menu_style' ] => wpglobus_customize_plus_selector_menu_style */
			if ( empty( WPGlobus::Config()->extended_options['switcher_menu_style'] ) ) {
				delete_option( 'wpglobus_customize_plus_selector_menu_style' );
			} else {
				update_option( 'wpglobus_customize_plus_selector_menu_style', WPGlobus::Config()->extended_options['switcher_menu_style'] );

			}

			/** Language Selector Menu Style */
			$wp_customize->add_setting( 'wpglobus_customize_plus_selector_menu_style', array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage'
			) );
			$wp_customize->add_control( 'wpglobus_customize_plus_selector_menu_style', array(
				'settings'    => 'wpglobus_customize_plus_selector_menu_style',
				'label'       => __( 'Language Selector Menu Style', 'wpglobus-plus' ),
				'section'     => 'wpglobus_plus_section',
				'type'        => 'select',
				'priority'    => 10,
				'description' => __( 'Drop-down languages menu or Flat (in one line)', 'wpglobus-plus' ),
				'choices'     => array(
					''         => __( 'Do not change', 'wpglobus-plus' ),
					'dropdown' => __( 'Drop-down (vertical)', 'wpglobus-plus' ),
					'flat'     => __( 'Flat (horizontal)', 'wpglobus-plus' ),
				)
			) );

		endif;

		if ( class_exists( 'WPGlobusPlus_Menu_Settings' ) ) :

			/**
			 * WPGlobusPlus link to menu settings page
			 *
			 * @since 1.1.17
			 */
			$wp_customize->add_setting( 'wpglobus_customize_plus_menu_settings_link', array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage'
			) );

			$link_text = __( 'Go to WPGlobus Menu Settings page', 'wpglobus-plus' );
			$link_text = '<div style="text-decoration:underline;">' . esc_html( $link_text ) . '</div>';

			$wp_customize->add_control( new WPGlobusLink( $wp_customize,
				'wpglobus_customize_plus_menu_settings_link', array(
					'settings' => 'wpglobus_customize_plus_menu_settings_link',
					'title'    => __( 'WPGlobus Menu Settings', 'wpglobus-plus' ),
					'section'  => 'wpglobus_plus_section',
					'priority' => 20,
					'href'     => admin_url() . 'admin.php?page=' . WPGlobusPlus_Menu_Settings::MENU_SLUG,
					'text'     => $link_text
				)
			) );

		endif;

		/** WPGlobusPlus link to options page */
		$wp_customize->add_setting( 'wpglobus_customize_plus_link', array(
			'type'       => 'option',
			'capability' => 'manage_options',
			'transport'  => 'postMessage'
		) );

		$link_text = __( 'Go to WPGlobus Plus Options page', 'wpglobus-plus' );
		$link_text = '<div style="text-decoration:underline;">' . esc_html( $link_text ) . '</div>';

		$wp_customize->add_control( new WPGlobusLink( $wp_customize,
			'wpglobus_customize_plus_link', array(
				'settings' => 'wpglobus_customize_plus_link',
				'title'    => __( 'WPGlobus Plus Options', 'wpglobus-plus' ),
				'section'  => 'wpglobus_plus_section',
				'priority' => 30,
				'href'     => admin_url() . 'admin.php?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE,
				'text'     => $link_text
			)
		) );


	}

	/**
	 * Process ajax
	 */
	public function on_process_ajax() {

		$ajax_return = array();

		$order = $_POST['order'];

		switch ( $order['action'] ) :
			case 'activate-module':

				$order['active_status'] = ! empty( $order['active_status'] ) ? $order['active_status'] : '';

				$options = (array) get_option( $this->option_key );

				if ( '' === $order['active_status'] ) {
					$options[ $order['module'] ]['active_status'] = '';
				} else {
					$options[ $order['module'] ]['active_status'] = $order['active_status'];
				}
				$ajax_return['result'] = update_option( $this->option_key, $options, false );

				break;
			case 'wpglobeditor-save-page':
			case 'wpglobeditor-save-element':

				if ( $order['key'] === '0' ) {
					$order['key'] = 0;
				} else {
					$order['key'] = empty( $order['key'] ) ? '' : (int) $order['key'];
				}

				/** @var array $opts */
				$opts = get_option( 'wpglobus_plus_wpglobeditor' );

				$action = 'add';
				if ( empty( $order['page'] ) ) {
					$ajax_return['result']  = 'error';
					$ajax_return['message'] = 'Empty field page';
				} else {
					if ( ! empty( $opts['page_list'] ) && ! empty( $opts['page_list'][ $order['page'] ] ) ) {
						/**
						 * check element existence in option
						 */
						foreach ( $opts['page_list'][ $order['page'] ] as $key => $value ) {
							if ( $value === $order['element'] ) {
								$action = '';
								break;
							}
						}
					}

					if ( ( $order['key'] === 0 && isset( $opts['page_list'][ $order['page'] ][ $order['key'] ] ) ) ||
					     ( ! empty( $order['key'] ) && isset( $opts['page_list'][ $order['page'] ][ $order['key'] ] ) )
					) {
						/**
						 * check key existence for update
						 */
						$action = 'update';
					}

					if ( 'add' === $action ) {
						$opts['page_list'][ $order['page'] ][] = $order['element'];
						$ajax_return['result']                 = update_option( 'wpglobus_plus_wpglobeditor', $opts, false );
					} else if ( 'update' === $action ) {
						$opts['page_list'][ $order['page'] ][ $order['key'] ] = $order['element'];
						$ajax_return['result']                                = update_option( 'wpglobus_plus_wpglobeditor', $opts, false );
					} else {
						$ajax_return['result']  = 'error';
						$ajax_return['message'] = 'Element already exists';
					}
				}
				break;
			case 'wpglobeditor-remove':

				/** @var array $opts */
				$opts = get_option( 'wpglobus_plus_wpglobeditor' );

				if ( ! empty( $opts['page_list'][ $order['page'] ][ $order['key'] ] ) ) {
					unset( $opts['page_list'][ $order['page'] ][ $order['key'] ] );
				}

				if ( empty( $opts['page_list'][ $order['page'] ] ) ) {
					unset( $opts['page_list'][ $order['page'] ] );
				}

				if ( empty( $opts['page_list'] ) ) {
					unset( $opts['page_list'] );
				}

				$ajax_return['result'] = update_option( 'wpglobus_plus_wpglobeditor', $opts, false );

				break;
		endswitch;

		$ajax_return['order'] = $order;

		wp_die( json_encode( $ajax_return ) );

	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_admin_scripts() {

		/**
		 * Module WPGlobus Editor
		 */
		if ( ! empty( $_GET['tab'] ) && 'wpglobeditor' === $_GET['tab'] ) {
			wp_register_script(
				'wpglobus-plus-wpglobeditor',
				plugin_dir_url( __FILE__ ) . 'js/wpglobus-plus-wpglobeditor' . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery' ),
				WPGLOBUS_PLUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-plus-wpglobeditor' );

			wp_localize_script(
				'wpglobus-plus-wpglobeditor',
				'WPGlobusPlusEditor',
				array(
					'version'      => WPGLOBUS_PLUS_VERSION,
					'process_ajax' => __CLASS__ . '_process_ajax',
					'module'       => 'wpglobeditor'
				)
			);
		}

		wp_register_script(
			'wpglobus-plus-main',
			plugin_dir_url( __FILE__ ) . 'js/wpglobus-plus-main' . WPGlobus::SCRIPT_SUFFIX() . ".js",
			array( 'jquery' ),
			WPGLOBUS_PLUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus-plus-main' );

		wp_localize_script(
			'wpglobus-plus-main',
			'WPGlobusPlus',
			array(
				'version'           => WPGLOBUS_PLUS_VERSION,
				'option_page'       => 'admin.php?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE,
				'caption_menu_item' => esc_html__( 'WPGlobus Plus', 'wpglobus-plus' ),
				'process_ajax'      => __CLASS__ . '_process_ajax',
				'tab'               => empty( $_GET['tab'] ) ? '' : $_GET['tab'],
				'bulk_status_link'  => $this->bulk_status_link,
				'customize'         => array(
					'plusOptionPage' => admin_url() . 'admin.php?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE
				)
			)
		);

		/** @global string $pagenow */
		//		global $pagenow;
		//		if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) && self::WPGLOBUS_PLUS_OPTIONS_PAGE === $_GET['page']  ) :
		// maybe later
		//		endif;

	}

	/**
	 * Add hidden submenu
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_admin_menu() {

		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::WPGLOBUS_PLUS_OPTIONS_PAGE,
			array(
				$this,
				'options_page'
			)
		);

	}

	/**
	 * View: options panel
	 */
	public function options_page() {

		$tabs = array();

		$tabs['modules'] = array(
			'href'    => '?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE . '&tab=modules',
			'class'   => 'nav-tab',
			'caption' => esc_html__( 'Modules', 'wpglobus-plus' )
		);

		/**
		 * Tab publish
		 * @since 1.1.8
		 */
		$tabs['publish'] = array(
			'href'    => '?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE . '&tab=publish',
			'class'   => 'nav-tab',
			'caption' => esc_html__( 'Module Publish', 'wpglobus-plus' )
		);

		$tabs['wpglobeditor'] = array(
			'href'    => '?page=' . self::WPGLOBUS_PLUS_OPTIONS_PAGE . '&tab=wpglobeditor',
			'class'   => 'nav-tab',
			'caption' => esc_html__( 'Editor Settings', 'wpglobus-plus' )
		);

		/**
		 * Tab Menu Settings
		 * @since 1.1.17
		 */
		/*
		$tabs['menu_settings'] = array(
			'href' 	=> '?page=wpglobus-menu-settings',
			'class' => 'nav-tab',
			'caption' => esc_html__( 'Menu Settings', 'wpglobus-plus' )
		);	*/

		$active_tab = 'modules'; // default tab
		if ( ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ) {
			$active_tab = $_GET['tab'];
		}

		?>

		<div class="wrap about-wrap wpglobus-about-wrap">
			<h1 class="wpglobus"><span class="wpglobus-wp">WP</span>Globus
				<?php echo esc_html_x( 'Plus', 'Part of the WPGlobus Plus', 'wpglobus-plus' ); ?>
				<span class="wpglobus-version"><?php echo WPGLOBUS_PLUS_VERSION; ?></span>
			</h1>

			<h2 class="nav-tab-wrapper">    <?php
				foreach ( $tabs as $tab => $option ) {
					if ( $active_tab === $tab ) {
						$option['class'] .= ' nav-tab-active';
					} ?>
					<a href="<?php echo $option['href']; ?>" class="<?php echo $option['class']; ?>"><?php echo $option['caption']; ?></a> <?php
				}
				?>
			</h2>

			<div class="feature-section one-col">
				<div class="col">
					<?php

					switch ( $active_tab ) :
						case 'modules':
							?>

							<h3><?php esc_html_e( 'Active Modules', 'wpglobus-plus' ); ?></h3>

							<div style="padding: .5em 0 1em"><?php esc_html_e( 'Uncheck the modules you are not planning to use:', 'wpglobus-plus' ); ?></div>
							<?php

							foreach ( $this->modules as $module => $option ) {

								/**
								 * A module is considered active by default,
								 * so the condition is either unset or true.
								 */
								$is_module_active = (
									! isset( $this->options[ $module ]['active_status'] )
									|| ! empty( $this->options[ $module ]['active_status'] )
								);

								$is_checkbox_disabled = (
									isset( $option['checkbox_disabled'] )
									&& $option['checkbox_disabled']
								);
								?>
								<div class="module-block">
									<span class="wpglobus-plus-spinner"></span>
									<label for="wpglobus-plus-<?php echo $module; ?>" style="display: block">
										<input type="checkbox"
										       class="wpglobus-plus-module"
										       data-module="<?php echo $module; ?>"
										       id="wpglobus-plus-<?php echo $module; ?>"
										       name="wpglobus-plus-<?php echo $module; ?>"
											<?php checked( $is_module_active ) ?>
											<?php disabled( $is_checkbox_disabled ) ?> />
										<strong><?php echo $option['caption']; ?></strong>:
										<?php echo $option['desc']; ?>
									</label>
									<?php if ( ! empty( $option['subtitle'] ) ) { ?>
										<?php echo $option['subtitle']; ?>
									<?php } ?>
								</div>
								<br />
								<?php
							} ?>
							<hr />
							<div class="return-to-dashboard" style="padding-left:10px;">
								<a class="button button-primary" href="admin.php?page=wpglobus_options">
									<?php _e( 'Go to WPGlobus Settings', 'wpglobus' ); ?>
								</a>
							</div>

							<?php
							break;
						case 'publish' :

							$is_module_active = true;
							if ( ! empty( $this->options['publish'] ) ) {
								if ( isset( $this->options['publish']['active_status'] ) && empty( $this->options['publish']['active_status'] ) ) {
									$is_module_active = false;
								}
							}
							if ( $is_module_active ) {

								require_once 'class-wpglobus-plus-publish-extend.php';
								WPGlobusPlus_Publish_Extend::bulk_draft( $this->bulk_status_link );

							} else { ?>
								<h4><?php esc_html_e( 'Please, activate module Publish', 'wpglobus-plus' ); ?></h4>    <?php
							}

							break;
						case 'wpglobeditor' :

							$is_module_active = true;
							if ( ! empty( $this->options['wpglobeditor'] ) ) {
								if ( isset( $this->options['wpglobeditor']['active_status'] ) && empty( $this->options['wpglobeditor']['active_status'] ) ) {
									$is_module_active = false;
								}
							}
							if ( $is_module_active ) {
								include_once( 'class-wpglobus-plus-wpglobeditor.php' );
								/** @noinspection OnlyWritesOnParameterInspection */
								/** @noinspection PhpUnusedLocalVariableInspection */
								$WPGlobusPlus_Editor_Table = new WPGlobusPlus_Editor_Table();

							} else { ?>
								<h4><?php esc_html_e( 'Please, activate module WPGlobus Editor', 'wpglobus-plus' ); ?></h4>    <?php
							}

							break;
					endswitch; ?>
				</div>
			</div>

		</div> <!-- .wrap --> <?php
		// http://www.wpg.dev/wp-admin/admin.php?page=wpglobus-about
	}

	/**
	 * Enqueue admin styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_admin_styles() {

		/**
		 * Module WPGlobus Editor
		 */
		if ( ! empty( $_GET['tab'] ) && 'wpglobeditor' === $_GET['tab'] ) {
			wp_register_style(
				'wpglobus-plus-wpglobeditor',
				plugin_dir_url( __FILE__ ) . '/css/wpglobus-plus-wpglobeditor' . WPGlobus::SCRIPT_SUFFIX() . ".css",
				array(),
				WPGLOBUS_PLUS_VERSION,
				'all'
			);
			wp_enqueue_style( 'wpglobus-plus-wpglobeditor' );
		}

		$enabled_pages = array(
			#Module Publish
			'post.php',
			'post-new.php',
			'admin.php'
		);

		if ( WPGlobus_WP::is_pagenow( $enabled_pages ) ) :

			global $pagenow;

			if (
				$pagenow === 'admin.php' &&
				( empty( $_GET['page'] ) || $_GET['page'] !== 'wpglobus-plus-options' )
			) {
				return;
			}

			wp_register_style(
				'wpglobus-plus-admin',
				plugin_dir_url( __FILE__ ) . '/css/wpglobus-plus-admin' . WPGlobus::SCRIPT_SUFFIX() . ".css",
				array(),
				WPGLOBUS_PLUS_VERSION,
				'all'
			);
			wp_enqueue_style( 'wpglobus-plus-admin' );

		endif;

	}

} // class

# --- EOF
