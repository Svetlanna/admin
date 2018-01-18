<?php
/**
 * Support of Yoast SEO 3.*
 * @package WPGlobus Plus
 * @since   1.1.5
 */

if ( ! class_exists( 'WPGlobusPlusYoastSeo' ) ) :

	/**
	 * Class WPGlobusPlusYoastSeo
	 * @since 1.1.5
	 */
	class WPGlobusPlusYoastSeo {

		/**
		 * Array of post data.
		 * @since 1.1.18
		 *
		 * @var array
		 */
		public static $postarr = array();

		public static function controller() {

			if ( is_admin() ) {

				if ( ! WPGlobus_WP::is_doing_ajax() ) {

					add_action( 'admin_print_scripts', array(
						__CLASS__,
						'action__admin_print_scripts'
					), 99 );

					/**
					 * @since 1.1.18
					 */
					add_filter( 'wpglobus_save_post_data', array(
						__CLASS__,
						'filter__wpglobus_save_post_data'
					), 11, 3 );

					/**
					 * Fix after sanitize post meta by yoast
					 * @since 1.1.18
					 */
					add_filter( 'wpseo_sanitize_post_meta__yoast_wpseo_focuskw', array(
						__CLASS__,
						'filter__wpseo_sanitize_post_meta'
					), 10, 4 );

				}

			}

		}

		/**
		 * Filter to validate the yoast seo post meta values.
		 *
		 * @see   wordpress-seo\inc\class-wpseo-meta.php
		 * @since 1.1.18
		 *
		 * @param  string $clean      Validated meta value.
		 * @param  mixed  $meta_value The new value.
		 * @param  array  $field_def  Field definitions.
		 * @param  string $meta_key   The full meta key (including prefix).
		 *
		 * @return string                Validated meta value
		 */
		public static function filter__wpseo_sanitize_post_meta(
			$clean,
			/** @noinspection PhpUnusedParameterInspection */
			$meta_value,
			/** @noinspection PhpUnusedParameterInspection */
			$field_def,
			/** @noinspection PhpUnusedParameterInspection */
			$meta_key
		) {

			if ( WPGlobus_Core::has_translations( self::$postarr['yoast_wpseo_focuskw'] ) ) {
				$clean = self::$postarr['yoast_wpseo_focuskw'];
			}

			return $clean;

		}

		/**
		 * Filter before save post.
		 *
		 * @see   class-wpglobus.php
		 * @since 1.1.18
		 *
		 * @param  array $data    Validated meta value.
		 * @param  array $postarr The new value.
		 * @param  bool  $devmode Developer's mode.
		 *
		 * @return array
		 */
		public static function filter__wpglobus_save_post_data(
			$data, $postarr,
			/** @noinspection PhpUnusedParameterInspection */
			$devmode
		) {
			$postarr['yoast_wpseo_focuskw'] = $postarr['yoast_wpseo_focuskw_text_input'];
			self::$postarr                  = $postarr;

			return $data;
		}

		/**
		 * Enqueue JS for YoastSEO support.
		 * @since 1.1.5
		 */
		public static function action__admin_print_scripts() {

			if ( 'off' === WPGlobus::Config()->toggle ) {
				return;
			}

			if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {

				$scr_version = '30';
				if ( version_compare( WPSEO_VERSION, '3.3.0', '>=' ) ) {
					$scr_version = '33';
				}

				wp_register_script(
					'wpglobus-plus-yoastseo',
					WP_PLUGIN_URL . '/wpglobus-plus/includes/js/wpglobus-plus-yoastseo' . $scr_version . WPGlobus::SCRIPT_SUFFIX() . '.js',
					array( 'jquery' ),
					WPGLOBUS_PLUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-plus-yoastseo' );
				wp_localize_script(
					'wpglobus-plus-yoastseo',
					'WPGlobusPlusYoastSeo',
					array(
						'wpglobus_plus_version' => WPGLOBUS_PLUS_VERSION,
						'wpseo_version'         => WPSEO_VERSION
					)
				);

			}

		}

	}

endif;

/* EOF */
