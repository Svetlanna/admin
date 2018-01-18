<?php
/**
 * Class WPGlobusPlus_Edit
 *
 * @since 1.1.0
 */

if ( ! function_exists( 'convert_to_screen' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'WPGlobusPlus_Editor_Table' ) ) :

	/**
	 * Class WPGlobusPlus_Editor_Table
	 */
	class WPGlobusPlus_Editor_Table extends WP_List_Table {

		public $data = array();

		public $skeleton_data = array();

		public $table_fields = array();

		public $found_data = array();

		public $option_key = 'wpglobus_plus_wpglobeditor';

		/**
		 * Constructor
		 */
		public function __construct() {

			parent::__construct( array(
				'singular' => 'item',     //singular name of the listed records
				'plural'   => 'items',    //plural name of the listed records
				'ajax'     => true        //does this table support ajax?

			) );

			$this->get_data();

			$this->display_table();

		}

		protected function display_table() {

			$data       = $this->data;
			$this->data = $this->skeleton_data;

			$this->prepare_items(); ?>

			<div id="wpglobus-plus-skeleton" class="hidden">
				<?php $this->display(); ?>
			</div>
			<?php
			$this->data = $data;
			$this->prepare_items(); ?>
			<form method="post" id="wpglobus-plus-editor-items">
				<input type="hidden" name="page" value="posts_list_table"><?php
				$this->display(); ?>
			</form>
			<?php
		}

		/**
		 * Get a list of columns. The format is:
		 * 'internal-name' => 'Title'
		 *
		 * @since 1.1.0
		 * @return array
		 */
		public function get_columns() {
			$columns = array();
			foreach ( $this->table_fields as $field => $attrs ) {
				$columns[ $field ] = $attrs['caption'];
			}

			return $columns;
		}

		/**
		 * Get data
		 *
		 * @since  1.1.0
		 * @access public
		 */
		public function get_data() {

			$this->table_fields = array(
				'status'  => array(
					'caption'  => esc_html__( 'Status', 'wpglobus-plus' ),
					'sortable' => false,
				),
				'page'    => array(
					'caption'  => esc_html__( 'Page', 'wpglobus-plus' ),
					'sortable' => false,
					'order'    => 'desc'
				),
				'element' => array(
					'caption'  => sprintf(
					// translators:
						esc_html__( '%1$s or %2$s of the HTML element', 'wpglobus-plus' ),
						'<b>id</b>', '<b>name</b>'
					),
					'sortable' => false
				),
				'action'  => array(
					'caption'  => esc_html__( 'Action', 'wpglobus-plus' ),
					'sortable' => false
				)
			);

			$opts = get_option( $this->option_key );

			if ( ! empty( $opts['page_list'] ) ) {

				$row = array();

				foreach ( $opts['page_list'] as $page => $attrs ) {
					foreach ( $attrs as $key => $element ) {
						$row['ID']      = $page . '-' . $key;
						$row['status']  = ''; // TODO future use
						$row['page']    = $page;
						$row['key']     = $key;
						$row['element'] = $element;
						$row['action']  = '';
						$this->data[]   = $row;
					}
				}

			}

			$this->skeleton_data[0]['ID']      = '';
			$this->skeleton_data[0]['status']  = '';
			$this->skeleton_data[0]['page']    = '';
			$this->skeleton_data[0]['key']     = '';
			$this->skeleton_data[0]['element'] = '';
			$this->skeleton_data[0]['action']  = '';

		}

		/**
		 * @see    WP_List_Table::column_default
		 * @since  1.1.0
		 * @param array $item
		 * @return string
		 */
		protected function column_status( $item ) {
			return sprintf(
				'<span class="wpglobus-plus-editor-status">%s</span>', $item['status']
			);
		}

		/**
		 * @see    WP_List_Table::column_default
		 * @since  1.1.0
		 * @param array $item
		 * @return string
		 */
		protected function column_page( $item ) {
			return sprintf(
				'<input class="wpglobus-plus-ajaxify page" data-action="save-page" type="text" name="page[%s]" id="page-%s" value="%s" data-key="%s" />',
				$item['ID'],
				$item['ID'],
				$item['page'],
				$item['key']
			);
		}

		/**
		 * @see    WP_List_Table::column_default
		 * @since  1.1.0
		 * @access protected
		 * @param array $item
		 * @return string
		 */
		protected function column_element( $item ) {
			return sprintf(
				'<input class="wpglobus-plus-ajaxify element"  data-action="save-element" style="%s" type="text" name="element[%s]"  id="element-%s" value="%s" data-key="%s" />',
				'width:100%',
				$item['ID'],
				$item['ID'],
				$item['element'],
				$item['key']
			);
		}

		/**
		 * @see   WP_List_Table::column_default
		 * @since 1.1.0
		 * @param array $item
		 * @return string
		 */
		protected function column_action( $item ) {
			$content = '';
			/*
			$content = sprintf(
				'<a href="#" data-page="%s" data-key="%s" data-action="toggle" class="wpglobus-plus-action-ajaxify">Toggle</a> | ',
				$item['page'],
				$item['key']
			);
			// */
			$content .= sprintf(
				'<a href="#" data-page="%s" data-key="%s" data-action="remove" class="wpglobus-plus-action-ajaxify">' .
				esc_html__( 'Remove', 'wpglobus-plus' ) .
				'</a>',
				$item['page'],
				$item['key']
			);

			return $content;
		}

		/**
		 * Prepares the list of items for displaying.
		 *
		 * @uses   WP_List_Table::set_pagination_args()
		 * @see    WP_List_Table->prepare_items()
		 * @since  1.1.0
		 * @access public
		 */
		public function prepare_items() {

			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			/**
			 * Optional. You can handle your bulk actions however you see fit. In this
			 * case, we'll handle them within our package just to keep things clean.
			 */
####			$this->process_bulk_action();

			/**
			 * You can handle your row actions
			 */
####			$this->process_row_action();


####			usort( $this->data, array( &$this, 'usort_reorder' ) );

			//if ( isset($this->plugin_options['posts_per_page_text']) && !empty($this->plugin_options['posts_per_page_text'])) {	
			//$per_page = $this->plugin_options['posts_per_page_text'];
			//} else {
			$per_page = 40;
			//}	

			$current_page = $this->get_pagenum();
			$total_items  = count( $this->data );

			// only necessary because we have sample data
			$this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );

			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			) );

			/* 		$this->set_pagination_args( array(
						'total_items' => $total_items,                  //WE have to calculate the total number of items
						'per_page'    => $per_page                     //WE have to determine how many items to show on a page
					) ); */
			$this->items = $this->found_data;
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @since  1.1.0
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {
			if ( 'top' !== $which ) {
				echo '<div class="wpglubus-plus-add" style="width:50%;"><input id="wpglubus-plus-add-item" type="button" class="button button-primary" value="' .
				     esc_attr__( 'Add', 'wpglobus-plus' ) .
				     '" /></div>';
			}
		}

	}


endif;	
