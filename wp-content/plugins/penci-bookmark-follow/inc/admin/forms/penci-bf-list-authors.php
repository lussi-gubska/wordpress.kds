<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Followed authors List
 *
 * The html markup for the followed authors list
 * 
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
	
class Penci_Bf_List_Authors extends WP_List_Table {
	
	var $model, $per_page;
	
	function __construct(){
		
		global $penci_bl_model;
		
		$this->model = $penci_bl_model;
		
        //Set parent defaults
        parent::__construct( array(
							            'singular'  => 'author',     //singular name of the listed records
							            'plural'    => 'authors',    //plural name of the listed records
							            'ajax'      => false       //does this table support ajax?
							        ) );   
		$this->per_page	= apply_filters( 'penci_bl_list_authors_per_page', 10 ); // Per page
	}
    
    /**
	 * Displaying Followed author
	 *
	 * Does prepare the data for displaying followed author in the table.
	 * 
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */	
	function display_follow_author() {
	
		$prefix = PENCI_BL_META_PREFIX;
		
		// Taking parameter
		$orderby 	= isset( $_GET['orderby'] )	? urldecode( $_GET['orderby'] )		: 'date';
		$order		= isset( $_GET['order'] )	? $_GET['order']                	: 'DESC';
		$search 	= isset( $_GET['s'] ) 		? sanitize_text_field( trim($_GET['s']) )	: null;
		
		$args = array(
					'posts_per_page'	=> $this->per_page,
					'orderby'			=> $orderby,
					'order'				=> $order,
					'page'				=> isset( $_GET['paged'] ) ? $_GET['paged'] : null,
					'offset'  			=> ( $this->get_pagenum() - 1 ) * $this->per_page,
					'penci_bl_list_author_data'	=> true,
		);
		
		//in case of search make parameter for retriving search data
		if(isset($search) && !empty($search)) {
			$args['search']	= $search;
		}
	
		//get followed post list data from database
		$result_data = array();
		$result_data = $this->model->penci_bl_get_follow_author_data( $args );
				
		if( isset( $result_data['data'] ) && is_array( $result_data['data'] ) && !empty( $result_data['data'] ) ){
			foreach ( $result_data['data'] as $key => $value ){
				
				$permalink = add_query_arg( array( 'page' => 'penci-bf-author', 'authorid' => $value['post_parent'] ), admin_url( 'admin.php' ) );
				
				$userlist = '<a href="'.esc_url($permalink).'">'. esc_html__( 'View Followers', 'penci-bookmark-follow' ) .'</a>';
				
				$data[$key]['users'] = $userlist;
				
				$authordata = get_user_by( 'id', $value['post_parent'] );
				
				$author_link = add_query_arg( array( 'user_id' => $value['post_parent'] ), admin_url( 'user-edit.php' ) );
				$data[$key]['authorname'] = !empty( $authordata ) && isset( $authordata->display_name ) ? '<a href="'.esc_url($author_link).'" title="'.$authordata->display_name.'">'.$authordata->display_name.'</a><br/>'.$authordata->user_email : '';
				
				$data[$key]['authorid'] = $value['post_parent'];
				
			}
		}
		
		$result_arr['data']		= !empty($data)	? $data : array();
		$result_arr['total'] 	= isset($result_data['total']) ? intval( $result_data['total'] ) 	: ''; // Total no of data
			
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
	function column_default( $item, $column_name ){
		switch( $column_name ) {
			case 'authorname':
				$title = $item[ $column_name ];
		    	if( strlen( $title ) > 50 ) {
					$title = substr( $title, 0, 50 );
					$title = $title.'...';
				}
            default:
				$default_value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';            	
        	  	return apply_filters( 'penci_bl_authors_column_value', $default_value, $item, $column_name );
        }
    }
    
    /**
     * Manage author name Column
     *
     * @package Penci Bookmark Follow
     * @since 1.0.0
     */
    
    function column_authorname($item){
    	
    	$pagestr = $pagenumber = '';
    	if( isset( $_GET['paged'] ) ) { $pagestr = '&paged=%s'; $pagenumber = $_GET['paged']; }
    	 
    	$actions['delete'] = sprintf('<a class="penci-bf-post-title-delete penci-bf-delete" href="?page=%s&action=%s&author[]=%s'.$pagestr.'">'.esc_html__('Delete', 'penci-bookmark-follow').'</a>','penci-bf-author','delete',$item['authorid'], $pagenumber );
    	
         //Return the title contents	        
        return sprintf('%1$s %2$s', 
        	$item['authorname'],
        	$this->row_actions( $actions )
        );
        
    }
   	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['authorid']          //The value of the checkbox should be the record's id
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
	function get_columns(){
	
        $columns = array(
    						'cb'      			=>	'<input type="checkbox" />', //Render a checkbox instead of text
				            'authorname'		=>	esc_html__( 'Author Name', 'penci-bookmark-follow' ),
				            'users'				=>	esc_html__(	'View Followers', 'penci-bookmark-follow' ),
				        );
    	return apply_filters( 'penci_bl_authors_add_columns', $columns );		
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
    								'authorname'	=>	array( 'authorname', true ),    //true means its already sorted
						         );
						         
     	return apply_filters( 'penci_bl_authors_add_sortable_column', $sortable_columns );						         
    }
	
	function no_items() {
		//message to show when no records in database table
		esc_html_e( 'No Followed Authors Found.', 'penci-bookmark-follow' );
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
        $actions = array( 'delete'    => esc_html__('Delete','penci-bookmark-follow') );
        return $actions;
    }

	/**
     * Add Export button
     *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
     */
    function extra_tablenav( $which ) {
    	
    	if( $which == 'top' ) {

    		echo '	<input type="submit" name="export_authors_followers" id="export_authors_followers" class="button button-primary" value="'. esc_html__('Export All Followers','penci-bookmark-follow') .'">';
    	}
    }
    
    function prepare_items() {
        
		// Get how many records per page to show
		$per_page		= $this->per_page;
       
        // Get All, Hidden, Sortable columns              
		$columns		= $this->get_columns();
		$hidden			= array();
		$sortable		= $this->get_sortable_columns();
        
        
        // Get final column header              
		$this->_column_headers = array($columns, $hidden, $sortable);

		// Get Data of particular page
		$data_res 	= $this->display_follow_author();
		$data 		= $data_res['data'];
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'authorid'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
                
        // Get current page number
		$current_page	= $this->get_pagenum();
       
        // Get total count
		$total_items	= $data_res['total'];
             
       // Get page items
		$this->items	= $data;
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
									            'total_items' => $total_items,                  //WE have to calculate the total number of items
									            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
									            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
									        ) );
    }
    
}

//Create an instance of our package class...
$FollowedAuthorListTable = new Penci_Bf_List_Authors();
	
//Fetch, prepare, sort, and filter our data...
$FollowedAuthorListTable->prepare_items();
		
?>

<div class="wrap">


	<h2 class="penci-bf-list-title">
    	<?php esc_html_e( 'Followed Authors', 'penci-bookmark-follow' ); ?>
    </h2>
    
    <?php 
    
    	//showing sorting links on the top of the list
    	$FollowedAuthorListTable->views(); 
    	
		if(isset($_GET['message']) && !empty($_GET['message']) ) { //check message
			
			if( $_GET['message'] == '3' ) { //check message
				
				echo '<div class="updated fade" id="message">
						<p><strong>'.esc_html__("Record (s) deleted successfully.",'penci-bookmark-follow').'</strong></p>
					</div>'; 
				
			} 
		}
		
    ?>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="product-filter" method="get" class="penci-bf-form">
        
    	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        
        <!-- Search Title -->
        <?php $FollowedAuthorListTable->search_box( esc_html__( 'Search', 'penci-bookmark-follow' ), 'penci-bookmark-follow' ); ?>
        
        <!-- Now we can render the completed list table -->
        <?php $FollowedAuthorListTable->display(); ?>
        
    </form>
</div><!--wrap-->