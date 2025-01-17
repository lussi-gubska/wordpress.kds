<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Followed Posts List
 *
 * The html markup for the followed posts list
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Penci_Bf_List extends WP_List_Table {

	var $model, $per_page;

	function __construct() {

		global $penci_bl_model, $page;

		$this->model = $penci_bl_model;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'post',     //singular name of the listed records
			'plural'   => 'posts',    //plural name of the listed records
			'ajax'     => false       //does this table support ajax?
		) );


		$this->per_page = apply_filters( 'penci_bl_per_page', 10 ); // Per page
	}

	/**
	 * Displaying Followed Posts
	 *
	 * Does prepare the data for displaying followed posts in the table.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function display_follow_post() {

		// Taking parameter
		$orderby = isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( trim( $_GET['s'] ) ) : null;

		$args = array(
			'posts_per_page'     => $this->per_page,
			'orderby'            => $orderby,
			'order'              => $order,
			'page'               => isset( $_GET['paged'] ) ? $_GET['paged'] : null,
			'offset'             => ( $this->get_pagenum() - 1 ) * $this->per_page,
			'penci_bl_list_data' => true,
		);

		//in case of search make parameter for retriving search data
		if ( isset( $search ) && ! empty( $search ) ) {
			$args['search'] = $search;
		}

		if ( isset( $_GET['penci_bl_post_type'] ) && ! empty( $_GET['penci_bl_post_type'] ) ) {
			$args['post_type'] = $_GET['penci_bl_post_type'];
		}

		//get followed post list data from database
		$result_data = $this->model->penci_bl_get_follow_post_data( $args );

		foreach ( $result_data['data'] as $key => $value ) {

			$permalink = add_query_arg( array( 'page'   => 'penci-bf-post',
			                                   'postid' => $value['ID']
			), admin_url( 'admin.php' ) );

			$userlist = '<a href="' . esc_url( $permalink ) . '">' . esc_html__( 'View Followers', 'penci-bookmark-follow' ) . '</a>';

			$post_link = add_query_arg( array( 'post' => $value['ID'], 'action' => 'edit' ), admin_url( 'post.php' ) );

			$data[ $key ]['ID']         = isset( $value['post_author'] ) ? $value['post_author'] : '';
			$data[ $key ]['post_id']    = isset( $value['ID'] ) ? $value['ID'] : '';
			$data[ $key ]['post_title'] = '<a href="' . esc_url( $post_link ) . '">' . $value['post_title'] . '</a>';
			$data[ $key ]['users']      = $userlist;
			$data[ $key ]['post_type']  = isset( $value['post_type'] ) ? $value['post_type'] : '';

		}

		$result_arr['data']  = ! empty( $data ) ? $data : array();
		$result_arr['total'] = isset( $result_data['total'] ) ? $result_data['total'] : ''; // Total no of data

		return $result_arr;
	}

	/**
	 * Mange column data
	 *
	 * Default Column for listing table
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_title':
				$title = $item[ $column_name ];
				if ( strlen( $title ) > 50 ) {
					$title = substr( $title, 0, 50 );
					$title = $title . '...';
				}
			default:
				$default_value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';

				return apply_filters( 'penci_bl_posts_column_value', $default_value, $item, $column_name );
		}
	}

	/**
	 * Mange post type column data
	 *
	 * Handles to modify post type column for listing table
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function column_post_type( $item ) {

		// get all custom post types
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		$post_type_sort_link = '';
		if ( ! empty( $item['post_type'] ) && isset( $post_types[ $item['post_type'] ]->label ) ) {
			$post_type_sort_url  = add_query_arg( array(
				'page'               => 'penci-bf-post',
				'penci_bl_post_type' => $item['post_type']
			), admin_url( 'admin.php' ) );
			$post_type_sort_link = '<a href="' . esc_url( $post_type_sort_url ) . '" >' . $post_types[ $item['post_type'] ]->label . '</a>';
		}

		return $post_type_sort_link;
	}

	/**
	 * Manage Post Title Column
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */

	function column_post_title( $item ) {

		$pagestr = $pagenumber = '';
		if ( isset( $_GET['paged'] ) ) {
			$pagestr    = '&paged=%s';
			$pagenumber = $_GET['paged'];
		}

		$actions['delete'] = sprintf( '<a class="penci-bf-post-title-delete penci-bf-delete" href="?page=%s&action=%s&post[]=%s' . $pagestr . '">' . esc_html__( 'Delete', 'penci-bookmark-follow' ) . '</a>', 'penci-bf-post', 'delete', $item['post_id'], $pagenumber );

		//Return the title contents
		return sprintf( '%1$s %2$s',
			$item['post_title'],
			$this->row_actions( $actions )
		);

	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			$item['post_id']                //The value of the checkbox should be the record's id
		);
	}

	/**
	 * Display Columns
	 *
	 * Handles which columns to show in table
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function get_columns() {

		$columns = array(
			'cb'         => '<input type="checkbox" />', //Render a checkbox instead of text
			'post_title' => esc_html__( 'Post Name', 'penci-bookmark-follow' ),
			'users'      => esc_html__( 'View Followers', 'penci-bookmark-follow' ),
			'post_type'  => esc_html__( 'Post Type', 'penci-bookmark-follow' ),
		);

		return apply_filters( 'penci_bl_posts_add_columns', $columns );
	}

	/**
	 * Sortable Columns
	 *
	 * Handles soratable columns of the table
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function get_sortable_columns() {

		$sortable_columns = array(
			'post_title' => array( 'post_title', true ),    //true means its already sorted
			'post_type'  => array( 'post_type', true )
		);

		return apply_filters( 'penci_bl_posts_add_sortable_column', $sortable_columns );
	}

	function no_items() {
		//message to show when no records in database table
		esc_html_e( 'No Followed Posts Found.', 'penci-bookmark-follow' );
	}

	/**
	 * Bulk actions field
	 *
	 * Handles Bulk Action combo box values
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function get_bulk_actions() {
		//bulk action combo box parameter
		//if you want to add some more value to bulk action parameter then push key value set in below array
		$actions = array(
			'delete' => esc_html__( 'Delete', 'penci-bookmark-follow' )
		);

		return $actions;
	}

	/**
	 * Add filter for post types
	 *
	 * Handles to display records for particular post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function extra_tablenav( $which ) {

		if ( $which == 'top' ) {

			// get all custom post types
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			if ( isset( $post_types['attachment'] ) ) { // Check attachment post type exists
				unset( $post_types['attachment'] );
			}

			$html = '';

			$html .= '<div class="alignleft actions">';

			if ( ! empty( $post_types ) ) {

				$html .= '<select name="penci_bl_post_type" id="penci_bl_post_type" data-placeholder="' . esc_html__( 'Select a Post Type', 'penci-bookmark-follow' ) . '">';

				$html .= '<option value="" ' . selected( isset( $_GET['penci_bl_post_type'] ) ? $_GET['penci_bl_post_type'] : '', '', false ) . '>' . esc_html__( 'Select a Post Type', 'penci-bookmark-follow' ) . '</option>';

				foreach ( $post_types as $key => $post_type ) {

					$args = array();

					if ( ! empty( $key ) ) {
						$args['post_type'] = $key;
						$args['count']     = true;
					}

					//get followed post list count data from database
					$post_count = $this->model->penci_bl_get_follow_post_data( $args );
					$post_count = ! empty( $post_count ) ? $post_count : '0';
					$post_count = ' (' . $post_count . ')';
					$html       .= '<option value="' . $key . '" ' . selected( isset( $_GET['penci_bl_post_type'] ) ? $_GET['penci_bl_post_type'] : '', $key, false ) . '>' . $post_type->label . $post_count . '</option>';
				}

				$html .= '</select>';

			}

			$html .= '	<input type="submit" value="' . esc_html__( 'Filter', 'penci-bookmark-follow' ) . '" class="button" id="post-query-submit" name="">';
			$html .= '	<input type="submit" id="export_posts_followers" name="export_posts_followers" class="button button-primary" value="' . esc_html__( 'Export All Followers', 'penci-bookmark-follow' ) . '">';
			$html .= '</div>';

			echo $html;
		}
	}

	function prepare_items() {

		// Get how many records per page to show
		$per_page = $this->per_page;

		// Get All, Hidden, Sortable columns
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();


		// Get final column header
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Get Data of particular page
		$data_res = $this->display_follow_post();
		$data     = $data_res['data'];

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'post_title'; //If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order

			return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
		}

		usort( $data, 'usort_reorder' );

		// Get current page number
		$current_page = $this->get_pagenum();

		// Get total count
		$total_items = $data_res['total'];

		// Get page items
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}

}

//Create an instance of our package class...
$FollowedPostsListTable = new Penci_Bf_List();

//Fetch, prepare, sort, and filter our data...
$FollowedPostsListTable->prepare_items();

?>

<div class="wrap">

    <h2 class="penci-bf-list-title">
		<?php esc_html_e( 'Followed Posts', 'penci-bookmark-follow' ); ?>
    </h2>

	<?php

	//showing sorting links on the top of the list
	$FollowedPostsListTable->views();

	if ( isset( $_GET['message'] ) && ! empty( $_GET['message'] ) ) { //check message

		if ( $_GET['message'] == '3' ) { //check message

			echo '<div class="updated fade" id="message">
						<p><strong>' . esc_html__( "Record (s) deleted successfully.", 'penci-bookmark-follow' ) . '</strong></p>
					</div>';

		}
	}

	?>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="product-filter" method="get" class="penci-bf-form">

        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>

        <!-- Search Title -->
		<?php $FollowedPostsListTable->search_box( esc_html__( 'Search', 'penci-bookmark-follow' ), 'penci-bookmark-follow' ); ?>

        <!-- Now we can render the completed list table -->
		<?php $FollowedPostsListTable->display(); ?>

    </form>
</div><!--wrap-->