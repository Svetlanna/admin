<?php
/**
 * Class WPGlobusPlus_Slug
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'WPGlobusPlus_Slug' ) ) :

	/**
	 * Class WPGlobusPlus_Slug
	 */
	class WPGlobusPlus_Slug {

		/**
		 * @var string $post_name Store new post name
		 * @see get_sample_permalink_html()
		 */
		public $post_name = '';

		/**
		 * @var string $meta_key For get/put wp_postmeta table
		 */
		public $meta_key = '_wpglobus_slug_';

		/** @var bool Prevents recursion and double-filtering */
		protected $_do_filter_permalinks = true;

		/** */
		public function __construct() {

			if ( is_admin() ) {

				add_filter( 'wpglobus_plus_slug_meta_key', array(
					$this,
					'get_slug_meta_key'
				) );

				add_filter( 'wpglobus_wpseo_permalink', array(
					$this,
					'filter__wpglobus_plus_localize_url'
				), 10, 2 );

				add_filter( 'wpglobus_edit_slug_box', array(
					$this,
					'on_edit_slug_box'
				), 10, 2 );

				add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array(
					$this,
					'on_process_ajax'
				) );

				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_scripts'
				) );

			} else {

				add_action( 'pre_get_posts', array(
					$this,
					'filter__pre_get_posts'
				), 0 );

				/**
				 * @since 1.1.9
				 */
				add_action( 'pre_get_posts', array(
					$this,
					'filter__pre_get_pages'
				), 0 );

				add_filter( 'the_posts', array(
					$this,
					'filter__the_posts'
				), 0, 2 );

				add_filter( 'wpglobus_pre_localize_current_url', array(
					$this,
					'filter__wpglobus_pre_localize_current_url'
				), 0, 2 );

				/**
				 * @since 1.1.3
				 */
				add_filter( 'template_include', array(
					$this,
					'filter__template_include'
				), 0 );

				/**
				 * @since 1.1.14
				 */
				add_action( 'template_redirect', array(
					$this,
					'check_hierarchical_post_url'
				), 0 );

				add_filter( 'post_link', array( $this, 'filter__post_link' ), 0, 2 );
				add_filter( 'page_link', array( $this, 'filter__post_link' ), 0, 2 );

				/**
				 * @since 1.1.3
				 */
				add_filter( 'post_type_link', array( $this, 'filter__post_type_link' ), 0, 4 );


			}

		}

		/**
		 * Check and fix part of url for extra language ( hierarchical post types )
		 *
		 * @since 1.1.14
		 * ticket 6922
		 */
		public function check_hierarchical_post_url() {

			if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
				return;
			}

			/** @global WP_Query $wp_query */
			global $wp_query;

			if ( isset( $wp_query->queried_object->post_type, $wp_query->query['pagename'] ) && is_singular() && is_post_type_hierarchical( $wp_query->queried_object->post_type ) ) {

				$redirect = false;

				$query_slugs = explode( '/', urldecode( $wp_query->query['pagename'] ) );

				$ancestors = array_reverse( get_ancestors( $wp_query->queried_object_id, $wp_query->queried_object->post_type ) );

				$ancestors = array_merge( $ancestors, array( $wp_query->queried_object_id ) );

				$wpglobus_slugs = array();

				foreach ( $ancestors as $key => $ancestor_id ) {
					$slug = get_post_meta( $ancestor_id, $this->meta_key . WPGlobus::Config()->language, true );
					if ( empty( $slug ) ) {
						$wpglobus_slugs[ $key ] = get_post_field( 'post_name', $ancestor_id );
					} else {
						$wpglobus_slugs[ $key ] = $slug;
					}
				}

				if ( empty( $query_slugs ) ) {
					$redirect = true;
				} else {
					foreach ( $wpglobus_slugs as $key => $_ ) {

						if ( empty( $query_slugs[ $key ] ) ) {
							$redirect = true;
							break;
						}
						if ( $_ !== $query_slugs[ $key ] ) {
							$redirect = true;
							break;
						}

					}
				}

				if ( $redirect ) {

					foreach ( $wpglobus_slugs as $key => $_ ) {
						/** @noinspection AlterInForeachInspection */
						$wpglobus_slugs[ $key ] = urlencode( $_ );
					}

					$_s = implode( '/', $wpglobus_slugs );

					$url = $_SERVER['REQUEST_SCHEME'] .
					       '://' .
					       $_SERVER['SERVER_NAME'] .
					       '/' .
					       WPGlobus::Config()->language .
					       '/' .
					       $_s;

					if ( wp_redirect( $url, 301 ) ) {
						exit;
					}
				}

			}


		}

		/**
		 * Redirect when trying opening post in extra language with post_name from default language
		 * ex: site/de/music-world/ will be redirected to site/de/musik-welt/
		 *
		 * @since 1.1.3
		 *
		 * @param string $template
		 *
		 * @return string
		 */
		public function filter__template_include( $template ) {

			if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
				return $template;
			}

			global $wp_query;

			if ( is_singular() ) :

				$wpglobus_slug = get_post_meta( $wp_query->post->ID, $this->meta_key . WPGlobus::Config()->language, true );

				$wpglobus_slug = urlencode( $wpglobus_slug );

				if ( $wpglobus_slug && false === strpos( $_SERVER['REQUEST_URI'], $wpglobus_slug ) ) {

					if ( false === strpos( $_SERVER['REQUEST_URI'], urlencode( $wp_query->post->post_name ) ) ) {
						/**
						 * @todo may be need set 404 page
						 */
						//$wp_query->set_404();
						//return get_404_template();
					} else {

						wp_redirect( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace( urlencode( $wp_query->post->post_name ), $wpglobus_slug, $_SERVER['REQUEST_URI'] ) );
						exit;
					}

				}

			endif;

			return $template;

		}


		/**
		 * Return $meta_key
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return string
		 */
		public function get_slug_meta_key() {

			return $this->meta_key;

		}

		/**
		 * Filter pre_get_posts.
		 * Fixes the problem of hierarchical pages 2nd and more level
		 *
		 * @since 1.1.9
		 *
		 * @param WP_Query $query
		 */
		public function filter__pre_get_pages( $query ) {

			if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
				return;
			}

			/** @noinspection PhpUnusedLocalVariableInspection */
			$type = ''; // TODO see below
			if ( ! empty( $query->query['attachment'] ) ) {
				/**
				 * url like site/ru/северная-америка/сша/
				 */
				$type = 'attachment';
			} else {
				/**
				 * url like site/ru/северная-америка/сша/алабама/
				 */
				if ( ! empty( $query->query['error'] ) ) {
					$type = 'error';
				}
			}

			if ( empty( $type ) ) {
				return;
			}

			switch ( $type ) :
				case 'attachment' :
					if ( false === strpos( $_SERVER['REQUEST_URI'], $query->query['attachment'] ) ) {
						return;
					}
					break;
				case 'error' :
					/** do nothing */
					break;
			endswitch;

			$uri = explode( '/', $_SERVER['REQUEST_URI'] );

			foreach ( $uri as $key => $piece ) {
				if ( empty( $piece ) ) {
					unset( $uri[ $key ] );
					continue;
				}
				if ( $piece === WPGlobus::Config()->language ) {
					unset( $uri[ $key ] );
					continue;
				}
			}

			if ( empty( $uri ) ) {
				return;
			}

			$value = end( $uri );

			global $wpdb;

			$meta_query =
				$wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND post_type = 'page' AND meta_key = %s AND meta_value = %s",
					$this->meta_key . WPGlobus::Config()->language,
					urldecode( $value )
				);


			$id = (int) $wpdb->get_var( $meta_query );

			if ( $id === 0 ) {
				/**
				 * We know that localized slug was not created for this post.
				 * We can have $value as post_name for default language, let's check
				 */
				$post_query =
					$wpdb->prepare(
						"SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' AND post_name = %s",
						urldecode( $value )
					);

				$id = (int) $wpdb->get_var( $post_query );
			}

			if ( $id === 0 ) {
				/**
				 * Excuse, we can not find needed post.
				 */
				return;
			}

			switch ( $type ) :
				case 'attachment' :
					$query->queried_object_id = $id;
					$query->queried_object    = get_post( $id );

					$query->is_attachment = false;

					/** WP_Query->query */
					unset( $query->query['attachment'] );
					$query->query['page']     = '';
					$query->query['pagename'] = implode( '/', $uri );

					/** WP_Query->query_vars */
					$query->set( 'page', '' );
					$query->set( 'pagename', implode( '/', $uri ) );
					$query->set( 'attachment', '' );
					$query->set( 'attachment_id', 0 );

					$query->is_page   = true;
					$query->is_singular = true;
					$query->is_single = false;
					break;

				case 'error' :
					$query->queried_object_id = $id;
					$query->queried_object    = get_post( $id );

					$query->is_attachment = false;

					/** WP_Query->query */
					unset( $query->query['error'] );
					$query->query['page']     = '';
					$query->query['pagename'] = implode( '/', $uri );

					/** WP_Query->query_vars */
					$query->set( 'page', '' );
					$query->set( 'pagename', implode( '/', $uri ) );
					$query->set( 'attachment', '' );
					$query->set( 'attachment_id', 0 );
					$query->set( 'page_id', 0 );
					$query->set( 'error', '' );

					$query->is_page     = true;
					$query->is_singular = true;
					$query->is_single   = false;
					$query->is_404      = false;

					break;
			endswitch;

		}

		/**
		 * Filter pre_get_posts.
		 * Fixes the problem of /{language}/ treated as a category by WP with permalink structure like /%category%/%postname%/
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Query $query
		 */
		public function filter__pre_get_posts( $query ) {

			if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
				return;
			}
			if ( empty( $query->query['category_name'] ) ) {
				return;
			}

			// /%category%/%postname%/
			if ( 0 !== strpos( get_option( 'permalink_structure' ), '/%category%' ) ) {
				return;
			}

			$has_ancestors = false;
			if ( $query->is_category() ) {
				$meta_value = $query->query['category_name'];
			} else {
				$has_ancestors = true;
				$meta_value    = $query->query['name'];
			}

			global $wpdb;

			$meta_query =
				$wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND post_type = 'page' AND meta_key = %s AND meta_value = %s",
					$this->meta_key . WPGlobus::Config()->language,
					urldecode( $meta_value )
				);


			$id = (int) $wpdb->get_var( $meta_query );

			if ( $id === 0 ) {
				return;
			}

			$ancestors = array();
			if ( $has_ancestors ) {
				$ancestors = get_ancestors( $id, 'page' );
			}

			if ( empty( $ancestors ) ) {

				$query->set( 'page', '' );
				$query->set( 'pagename', $query->query['category_name'] );
				$query->query['pagename'] = $query->query['category_name'];

				$query->queried_object_id = $id;
				$query->queried_object    = get_post( $id );

				$query->set( 'category_name', '' );
				unset( $query->query['category_name'] );

				$query->is_category = false;
				$query->is_page     = true;
				$query->is_singular = true;

				/**
				 * With /{language}/ treated as a category by WP $query->is_archive is set to true by default
				 * We need to set it false
				 * @since 1.1.6
				 */
				$query->is_archive = false;

			} else {

				$query->set( 'pagename', $query->query['category_name'] . '/' . $query->query['name'] );
				$query->query['pagename'] = $query->query['category_name'] . '/' . $query->query['name'];

				$query->queried_object_id = $id;
				$query->queried_object    = get_post( $id );

				$query->set( 'category_name', '' );
				unset( $query->query['category_name'] );

				unset( $query->query['name'] );
				$query->query_vars['name'] = '';

				$query->is_single = false;
				$query->is_page   = true;

			}

		}

		/**
		 * Filter the permalink for a post.
		 * Only applies to posts with post_type of 'post' or 'page'.
		 *
		 * @since 1.0.0
		 *
		 * @param string      $permalink The post's permalink.
		 * @param int|WP_Post $post      The post in question.
		 *
		 * @return string
		 */
		public function filter__post_link( $permalink, $post ) {

			if ( ! $this->_do_filter_permalinks ) {
				return $permalink;
			}

			// post_link filter passes WP_Post
			// page_link filter passes int
			if ( is_numeric( $post ) ) {
				/** @noinspection CallableParameterUseCaseInTypeContextInspection */
				$post = get_post( $post );
			}

			// precaution
			if ( ! $post ) {
				return $permalink;
			}

			$language = WPGlobus::Config()->language;
			// Check if there is a slug meta
			$wpglobus_slug = get_post_meta( $post->ID, $this->meta_key . $language, true );

			if ( $wpglobus_slug ) {

				// Work with a copy of the post object
				$post_clone = new WP_Post( $post );

				// If slug meta is set, set the post slug (post_name) to it,
				// so that get_permalink will use it
				$post_clone->post_name = urlencode( $wpglobus_slug );

				$save_do_filter_permalinks   = $this->_do_filter_permalinks;
				$this->_do_filter_permalinks = false;

				// Get the permalink and localize it (set the language prefix)
				$permalink = WPGlobus_Utils::localize_url( get_permalink( $post_clone ), $language );

				$this->_do_filter_permalinks = $save_do_filter_permalinks;

				// Do not need the post copy anymore
				unset( $post_clone );

			}

			/**
			 * We check case when current post has parents and need to localize parent's post name as part of url in menu items
			 * @since 1.1.13
			 * @see   ticket 6662
			 */
			if ( is_post_type_hierarchical( $post->post_type ) ) {
				$ancestors = get_ancestors( $post->ID, $post->post_type );

				foreach ( $ancestors as $ancestor ) {
					$_slug = get_post_meta( $ancestor, $this->meta_key . $language, true );
					if ( $_slug ) {
						$permalink = str_replace(
							get_post_field( 'post_name', $ancestor ),
							urlencode( $_slug ),
							$permalink
						);
					}
				}
			}

			return $permalink;
		}


		/**
		 * Filter the permalink for a post with a custom post type.
		 *
		 * @since 1.1.3
		 *
		 * @param string      $post_link The post's permalink.
		 * @param int|WP_Post $post      The post in question.
		 * @param bool        $leavename Defaults to false. Whether to keep post name.
		 * @param bool        $sample    Defaults to false. Is it a sample permalink..
		 *
		 * @return string
		 */
		public function filter__post_type_link(
			$post_link, $post, $leavename,
			/** @noinspection PhpUnusedParameterInspection */
			$sample
		) {

			if ( ! $leavename ) {
				/**
				 * Do something only when $leavename is false.
				 * *
				 * Whether to keep the post name. When set to true, a structural link will be returned, rather than the actual URI.
				 * @see get_post_permalink()
				 */
				$post_link = $this->filter__post_link( $post_link, $post );

			}

			return $post_link;

		}

		/**
		 * Filter for localize url
		 *
		 * @since 1.0.0
		 * @scope admin
		 * @global WP_Post $post
		 *
		 * @param string   $url
		 * @param string   $language
		 *
		 * @return string
		 */
		public function filter__wpglobus_plus_localize_url( $url = '', $language = '' ) {

			if ( '' === $url ) {
				return $url;
			}

			global $post;

			if ( $language === WPGlobus::Config()->default_language ) {

				$url = WPGlobus_Utils::localize_url( $url, $language );

			} else {

				$wpglobus_slug = get_post_meta( $post->ID, $this->meta_key . $language, true );
				if ( $wpglobus_slug ) {
					/**
					 * @see get_sample_permalink_html()
					 */
					list( $permalink, $post_name ) = get_sample_permalink( $post->ID, '', $wpglobus_slug );

					$pretty_permalink =
						str_replace( array( '%pagename%', '%postname%' ), $post_name, urldecode( $permalink ) );

					$url = WPGlobus_Utils::localize_url( $pretty_permalink, $language );

				}
			}

			return $url;

		}

		/**
		 * Use the slug metas in URL localization
		 *
		 * @since 1.0.0
		 *
		 * @param string    $url
		 * @param string    $language
		 *
		 * @return string
		 * @global WP_Query $wp_query
		 */
		public function filter__wpglobus_pre_localize_current_url( $url = '', $language = '' ) {
			global $wp_query;

			// Single post/page
			if ( $wp_query->is_singular && $wp_query->is_main_query() ) {

				// Work with a copy of the post object
				$post_clone = new WP_Post( $wp_query->post );

				// Check if there is a slug meta
				$wpglobus_slug = get_post_meta( $wp_query->post->ID, $this->meta_key . $language, true );

				// If slug meta is set, set the post slug (post_name) to it,
				// so that get_permalink will use it
				if ( $wpglobus_slug ) {
					$post_clone->post_name = $wpglobus_slug;
				}

				$save_do_filter_permalinks   = $this->_do_filter_permalinks;
				$this->_do_filter_permalinks = false;

				// Get the permalink and localize it (set the language prefix)
				$url = WPGlobus_Utils::localize_url( get_permalink( $post_clone ), $language );

				if (
					$wp_query->is_page() /** post type page */
					||
					( ! empty( $wp_query->query['post_type'] ) && is_post_type_hierarchical( $wp_query->query['post_type'] ) )
					/** hierarchical custom post type */
				) {

					$ancestors = get_ancestors( $wp_query->post->ID, 'page' );

					foreach ( $ancestors as $ancestor ) {

						// Check if there is a slug meta for ancestor
						$wpglobus_slug = get_post_meta( $ancestor, $this->meta_key . $language, true );

						if ( $wpglobus_slug ) {
							$url = str_replace( get_post_field( 'post_name', $ancestor ), $wpglobus_slug, $url );
						}

					}

				}

				$this->_do_filter_permalinks = $save_do_filter_permalinks;

				// Do not need the post copy anymore
				unset( $post_clone );

				// If there were any URL query parameters, restore them. Permalink did not have them.
				if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
					$url .= '?' . $_SERVER['QUERY_STRING'];
				}

			}

			return $url;
		}

		/**
		 * Filter the_posts
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Post[] $posts
		 * @param WP_Query  $wp_query
		 *
		 * @return array
		 */
		public function filter__the_posts( $posts, WP_Query $wp_query ) {

			if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
				return $posts;
			}

			$post_or_page_name = '';

			if ( '' !== $wp_query->query_vars['name'] ) {

				$post_or_page_name = $wp_query->query_vars['name'];

			} else if ( '' !== $wp_query->query_vars['pagename'] ) {

				$post_or_page_name = $wp_query->query_vars['pagename'];

			} else if ( '' !== $wp_query->query_vars['category_name'] ) {

				// case when option 'permalink_structure' == /%category%/%postname%/
				$post_or_page_name = $wp_query->query_vars['category_name'];

			}

			if ( 0 === $wp_query->post_count && '' !== $post_or_page_name ) {

				$meta_key = $this->meta_key . WPGlobus::Config()->language;

				$post_type = $wp_query->query_vars['post_type'];

				global $wpdb;

				if ( empty( $post_type ) ) {

					$query =
						$wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND ( post_type = 'post' OR post_type = 'page' ) AND meta_key = %s AND meta_value = %s",
							$meta_key,
							urldecode( $post_or_page_name
							) );

				} else {

					$query =
						$wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = %s AND meta_value = %s",
							$post_type,
							$meta_key,
							urldecode( $post_or_page_name
							) );

				}

				$id = (int) $wpdb->get_var( $query );

				if ( $id === 0 ) {

					/**
					 * @todo maybe need to set 404 in some cases, need to investigate
					 */
					//$wp_query->set_404();
					//status_header(404);
					//nocache_headers();
					//return array();

				} else {

					$post = get_post( $id );

					if ( $post ) {

						if ( 'page' === $post->post_type ) {

							if ( ! $wp_query->is_page ) {
								/**
								 * Noted for permalink settings http://sitename/%postname%/
								 */

								/**
								 * Make $wp_query compatible with page
								 * @see $wp_query: we have different $wp_query->request for default language and for extra language
								 */
								$wp_query->query['pagename']      = $wp_query->query['name'];
								$wp_query->query_vars['pagename'] = $wp_query->query['name'];
								$wp_query->query_vars['name']     = $wp_query->query['name'];

								unset( $wp_query->query['name'] );

								$wp_query->queried_object    = $post;
								$wp_query->queried_object_id = $post->ID;

								$wp_query->posts = array( $post );

								$wp_query->found_posts = 1;
								$wp_query->is_single   = false;
								$wp_query->is_page     = true;

							}

						} else {

							/**
							 * Make $wp_query compatible with post
							 * @see $wp_query: we have different text in post_content, post_title ( $wp_query->posts )
							 */
							$wp_query->posts = array( $post );

							$wp_query->found_posts = 1;

						}

						return array( $post );
					}

				}

			}

			return $posts;

		}

		/**
		 * @param string    $slug_box Unused
		 * @param string    $language
		 *
		 * @return string
		 * @global stdClass $post_type_object
		 * @global WP_Post  $post
		 */
		public function on_edit_slug_box(
			/** @noinspection PhpUnusedParameterInspection */
			$slug_box, $language
		) {

			global $post_type_object, $post;

			$permalink = get_permalink( $post->ID );
			if ( ! $permalink ) {
				$permalink = '';
			}

			$slug_box = '<div class="inside">';

			$sample_permalink_html =
				$post_type_object->public ? $this->get_sample_permalink_html( $post->ID, null, null, $language ) : '';
			$shortlink             = wp_get_shortlink( $post->ID, 'post' );

			if ( ! empty( $shortlink ) && $shortlink !== $permalink && $permalink !== home_url( '?page_id=' . $post->ID ) ) {
				$sample_permalink_html .= '<input id="shortlink-' . $language . '" type="hidden" value="' . esc_attr( $shortlink ) . '" /><a href="#" class="button button-small" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink-' . $language . '\').val()); return false;">' . __( 'Get Shortlink' ) . '</a>';
			}

			if ( $post_type_object->public && ! ( 'pending' === get_post_status( $post ) && ! current_user_can( $post_type_object->cap->publish_posts ) ) ) {

				$has_sample_permalink = $sample_permalink_html && 'auto-draft' !== $post->post_status;

				$slug_box .= '<div id="edit-slug-box-' . $language . '" class="wpglobus-edit-slug-box hide-if-no-js" style="' . apply_filters( 'wpglobus_plus_slug_box_style', '', $language ) . '">';
				if ( $has_sample_permalink ) {
					$slug_box .= $sample_permalink_html;
				}
				$slug_box .= '</div>';

			}

			$slug_box .= '</div>';

			return $slug_box;

		}

		/**
		 * Returns the HTML of the sample permalink slug editor.
		 *
		 * @see   original get_sample_permalink_html()
		 * @since 1.0.0
		 *
		 * @param int    $id        Post ID or post object.
		 * @param string $new_title Optional. New title. Default null.
		 * @param string $new_slug  Optional. New slug. Default null.
		 * @param string $language
		 *
		 * @return string The HTML of the sample permalink slug editor.
		 */
		public function get_sample_permalink_html( $id, $new_title = null, $new_slug = null, $language ) {
			$post = get_post( $id );
			if ( ! $post ) {
				return '';
			}

			list( $permalink, $post_name ) = get_sample_permalink( $post->ID, $new_title, $new_slug );

			if ( $new_title === null && $new_slug === null ) {

				$slug = get_post_meta( $post->ID, $this->meta_key . $language, true );
				if ( ! empty( $slug ) ) {
					$post_name = $slug;
				}

			} else {

				if ( $language !== WPGlobus::Config()->default_language ) :

					/**
					 * Make unique slug for extra language
					 *
					 * @see wp_unique_post_slug()  https://core.trac.wordpress.org/browser/tags/4.2.4/src/wp-includes/post.php#L0
					 * @see $check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d LIMIT 1";
					 */

					/** @global wpdb $wpdb */
					global $wpdb;

					$meta_key        = $this->meta_key . $language;
					$check_sql       =
						"SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '%s' AND meta_value = '%s' AND post_id != %d LIMIT 1";
					$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $meta_key, $post_name, $id ) );

					if ( $post_name_check ) {
						$suffix = 2;
						do {
							$alt_post_name   =
								_truncate_post_slug( $post_name, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
							$post_name_check =
								$wpdb->get_var( $wpdb->prepare( $check_sql, $meta_key, $alt_post_name, $id ) );
							$suffix ++;
						} while ( $post_name_check );
						$post_name = $alt_post_name;
					}

				endif;
			}

			if ( current_user_can( 'read_post', $post->ID ) ) {
				$ptype     = get_post_type_object( $post->post_type );
				$view_post = $ptype->labels->view_item;
			}

			if ( 'publish' === get_post_status( $post ) ) {
				$title = __( 'Click to edit this part of the permalink' );
			} else {
				$title = __( 'Temporary permalink. Click to edit this part.' );
			}

			if ( false === strpos( $permalink, '%postname%' ) && false === strpos( $permalink, '%pagename%' ) ) {
				$return =
					'<strong>' . __( 'Permalink:' ) . "</strong>\n" . '<span id="sample-permalink-' . $language . '" tabindex="-1">' . WPGlobus_Utils::localize_url( $permalink, $language ) . "</span>\n";
				if ( '' === get_option( 'permalink_structure' ) && current_user_can( 'manage_options' ) && ! ( 'page' === get_option( 'show_on_front' ) && $id === get_option( 'page_on_front' ) ) ) {
					$return .= '<span id="change-permalinks-' . $language . '"><a href="options-permalink.php" class="button button-small" target="_blank">' . __( 'Change Permalinks' ) . "</a></span>\n";
				}
			} else {
				if ( function_exists( 'mb_strlen' ) ) {
					if ( mb_strlen( $post_name ) > 30 ) {
						$post_name_abridged =
							mb_substr( $post_name, 0, 14 ) . '&hellip;' . mb_substr( $post_name, - 14 );
					} else {
						$post_name_abridged = $post_name;
					}
				} else {
					if ( strlen( $post_name ) > 30 ) {
						$post_name_abridged = substr( $post_name, 0, 14 ) . '&hellip;' . substr( $post_name, - 14 );
					} else {
						$post_name_abridged = $post_name;
					}
				}

				$this->post_name = $post_name;

				$post_name_html   =
					'<span id="editable-post-name-' . $language . '" class="wpglobus-editable-post-name" data-language="' . $language . '" title="' . $title . '">' . $post_name_abridged . '</span>';
				$display_link     =
					str_replace( array( '%pagename%', '%postname%' ), $post_name_html, urldecode( $permalink ) );
				$pretty_permalink =
					str_replace( array( '%pagename%', '%postname%' ), $post_name, urldecode( $permalink ) );

				$return = '<strong>' . __( 'Permalink:' ) . "</strong>\n";
				$return .= '<span id="sample-permalink-' . $language . '" class="wpglobus-sample-permalink" tabindex="-1">' . WPGlobus_Utils::localize_url( $display_link, $language ) . "</span>\n";
				$return .= '&lrm;'; // Fix bi-directional text display defect in RTL languages.
				$return .= '<span id="edit-slug-buttons-' . $language . '" class="wpglobus-edit-slug-buttons"><a href="#post_name" class="edit-slug button button-small hide-if-no-js" onclick="WPGlobusSlug.editPermalink(' . $id . ',\'' . $language . '\'); return false;">' . __( 'Edit' ) . "</a></span>\n";
				$return .= '<span id="editable-post-name-full-' . $language . '" class="wpglobus-editable-post-name-full">' . $post_name . "</span>\n";
			}

			if ( isset( $view_post ) ) {
				if ( 'draft' === $post->post_status ) {
					$preview_link = set_url_scheme( get_permalink( $post->ID ) );
					/** This filter is documented in wp-admin/includes/meta-boxes.php */
					$preview_link =
						apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ), $post );
					$preview_link = WPGlobus_Utils::localize_url( $preview_link, $language );
					$return .= "<span id='view-post-btn-" . $language . "' class='wpglobus-view-post-btn'><a href='" . esc_url( $preview_link ) . "' class='button button-small' target='wp-preview-{$post->ID}'>$view_post</a></span>\n";
				} else {
					if ( empty( $pretty_permalink ) ) {
						$pretty_permalink = $permalink;
					}
					$pretty_permalink = WPGlobus_Utils::localize_url( $pretty_permalink, $language );
					$return .= "<span id='view-post-btn-" . $language . "' class='wpglobus-view-post-btn'><a href='" . $pretty_permalink . "' class='button button-small'>$view_post</a></span>\n";
				}
			}

			/**
			 * Filter the sample permalink HTML markup.
			 *
			 * @since 2.9.0
			 *
			 * @param string      $return    Sample permalink HTML markup.
			 * @param int|WP_Post $id        Post object or ID.
			 * @param string      $new_title New sample permalink title.
			 * @param string      $new_slug  New sample permalink slug.
			 */

			//$return = apply_filters( 'get_sample_permalink_html', $return, $id, $new_title, $new_slug );

			return $return;
		}


		public function on_admin_scripts() {

			if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) :

				wp_register_script(
					'wpglobus-plus-slug',
					plugin_dir_url( __FILE__ ) . 'js/wpglobus-plus-slug' . WPGlobus::SCRIPT_SUFFIX() . ".js",
					array( 'jquery' ),
					WPGLOBUS_PLUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-plus-slug' );
				wp_localize_script(
					'wpglobus-plus-slug',
					'WPGlobusSlug',
					array(
						'data' => array(
							'version'      => WPGLOBUS_PLUS_VERSION,
							'process_ajax' => __CLASS__ . '_process_ajax',
							'slug_type'    => 'meta'
						)
					)
				);

			endif;

		}

		public function on_process_ajax() {

			$ajax_return = array();

			$order = $_POST['order'];

			switch ( $order['action'] ) :
				case 'wpglobus-sample-permalink':
					/**
					 * @see wp_ajax_sample_permalink()
					 */
					//check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
					$order['post_id']   = isset( $order['post_id'] ) ? (int) $order['post_id'] : 0;
					$order['new_title'] = isset( $order['new_title'] ) ? $order['new_title'] : '';
					$order['new_slug']  = isset( $order['new_slug'] ) ? $order['new_slug'] : null;

					if ( $order['post_id'] !== 0 ) :

						/** @noinspection NestedPositiveIfStatementsInspection */
						if ( $order['slug_type'] === 'meta' ) {
							$ajax_return = $this->update_post_meta( $order );
						}

					endif;
					break;
			endswitch;

			wp_die( json_encode( $ajax_return ) );

		}

		/**
		 * update_post_meta
		 *
		 * @since 1.0.0
		 *
		 * @param array $order
		 *
		 * @return string
		 */
		public function update_post_meta( $order ) {

			$ajax_return =
				$this->get_sample_permalink_html( $order['post_id'], $order['new_title'], $order['new_slug'], $order['language'] );

			/**
			 * for $this->post_name @see $this->get_sample_permalink_html()
			 */
			update_post_meta( $order['post_id'], $this->meta_key . $order['language'], $this->post_name );

			return $ajax_return;

		}

	}    // WPGlobusPlus_Slug

endif;

# --- EOF
