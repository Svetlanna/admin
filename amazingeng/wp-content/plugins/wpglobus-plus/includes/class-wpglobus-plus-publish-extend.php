<?php
/**
 * File: class-wpglobus-plus-publish-extend.php
 *
 * @package    WPGlobus Plus
 */

if ( ! class_exists( 'WPGlobusPlus_Publish_Extend' ) ) :

	/**
	 * Class WPGlobusPlus_Publish_Extend
	 */
	class WPGlobusPlus_Publish_Extend {

		/**
		 * Bulk set draft
		 *
		 * @param string $bulk_status_link Link to page to set draft status.
		 *
		 * @return void
		 */
		public static function bulk_draft( $bulk_status_link = '' ) {

			$custom_post_types = get_post_types( array( '_builtin' => false ) );

			$post_types = array(
				'post',
				'page',
			);

			foreach ( $custom_post_types as $type ) {
				if ( ! in_array( $type, WPGlobus::Config()->disabled_entities, true ) ) {
					$post_types[] = $type;
				}
			}

			echo '<h3>';
			esc_html_e( 'Set draft status', 'wpglobus-plus' );
			echo '</h3>';

			echo '<p style="color: white; background-color: red; padding: .5em">';
			esc_html_e( 'WARNING: this operation is non-reversible. It is strongly recommended that you backup your database before proceeding.', 'wpglobus-plus' );
			echo '</p>';

			echo '<p>';
			esc_html_e( 'By default, when you publish a post, all languages get the "published" status. By using this tool, you can set specific language(s) to draft, for any post, page or custom post type.', 'wpglobus-plus' );
			echo '</p>';

			echo '<p>';
			esc_html_e( '1. Select a language and the post type:', 'wpglobus-plus' );
			echo '</p>';

			/**
			 * Set languages
			 */
			$select = '<p><select id="language" class="wpglobus-select" data-mask="{{language}}">';
			$select .= '<option value="{{language}}">-- ' .
			           /* translators: drop-down menu prompt */
			           esc_html( __( 'select a language', 'wpglobus-plus' ) ) .
			           ' --</option>';
			foreach ( WPGlobus::Config()->enabled_languages as $language ) {

				if ( WPGlobus::Config()->default_language !== $language ) {
					$select .= '<option value="' . $language . '">' . WPGlobus::Config()->en_language_name[ $language ] . '</option>';
				}
			}
			$select .= '</select></p>';

			echo $select; // WPCS: XSS ok.

			/**
			 * Set post types
			 */
			$select = '<p><select id="post_type" class="wpglobus-select" data-mask="{{post_type}}">';
			$select .= '<option value="{{post_type}}">-- ' .
			           /* translators: drop-down menu prompt */
			           esc_html( __( 'select a post type', 'wpglobus-plus' ) ) .
			           ' --</option>';
			foreach ( $post_types as $type ) {
				$select .= "<option data-mask='{{post_type}}' value='$type'>" . $type . '</option>';
			}
			$select .= '</select></p>';

			echo $select; // WPCS: XSS ok.

			echo '<p>';
			esc_html_e( '2. Click the link below:', 'wpglobus-plus' );
			echo '</p>';
			?>
			<p><a class="wpglobus-bulk_status_link" href="<?php echo esc_url( $bulk_status_link ); ?>">
					<?php echo esc_html( $bulk_status_link ); ?>
				</a></p>

			<?php
		}
	}

endif;

/*EOF*/
