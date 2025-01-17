<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Followed Post Users List
 *
 * The html markup for the followed posts Users list
 * 
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
	
class Penci_Bf_Users_List extends WP_List_Table {
	
	var $model, $per_page;
	
	function __construct(){
		
		global $penci_bl_model;
		
		$this->model = $penci_bl_model;
		
        //Set parent defaults
        parent::__construct( array(
							            'singular'  => 'user',     //singular name of the listed records
							            'plural'    => 'users',    //plural name of the listed records
							            'ajax'      => false       //does this table support ajax?
							        ) );
							        
		$this->per_page	= apply_filters( 'penci_bl_users_list_per_page', 10 ); // Per page
						
	}
    
    /**
	 * Displaying Followed Post Users
	 *
	 * Does prepare the data for displaying followed post users in the table.
	 * 
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */	
	function display_follow_post_users() {
	
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
					'penci_bl_user_list_data'	=> true,
		);
		
		$args['postid'] = $_GET['postid'];
		
		//in case of search make parameter for retriving search data
		if( isset($search) && !empty($search) ) {
			$args['search']	= $search;
		}
		
		if(isset($_REQUEST['penci_bl_status']) && !empty($_REQUEST['penci_bl_status'])) {
			$args['penci_bl_status']	= $_REQUEST['penci_bl_status'];
		}
		
		if( isset( $_GET['user'] ) ) {			
			$args['author'] = trim( $_GET['user'] );
		}
		
		//get followed post list data from database
		$result_data = $this->model->penci_bl_get_follow_post_users_data( $args );
		
		foreach ( $result_data['data'] as $key => $value ){
			
			// get user email from meta field
			$user_email = get_post_meta( $value['ID'], $prefix.'post_user_email', true );
			
			// get user is subscribed or not
			$subscribed = get_post_meta( $value['ID'], $prefix.'follow_status', true );
			
			// get view log link to view log for perticular user
			$permalink = add_query_arg( array( 'page' => 'penci-bf-post', 'postid' => $_GET['postid'], 'logid' => $value['ID'] ), admin_url( 'admin.php' ) );
			
			$logs = '<a href="'.esc_url($permalink).'">'. esc_html__( 'View Log', 'penci-bookmark-follow' ) .'</a>';
			
			$userdata = get_user_by( 'id', $value['post_author'] );									
			
			$user_email_html = '';
			$user = '';			
			if( !empty( $userdata ) ) {	// to display user display name			
				
				$user_email 	= isset( $userdata->user_email ) ? $userdata->user_email : '';
				$user_edit_link = add_query_arg( array( 'user_id' => $userdata->ID ), admin_url( 'user-edit.php' ) );
				$display_name 	= $userdata->display_name;
				
				if( !empty( $user_email ) ) {
					$user_email_html = '<a href="'.esc_url($user_edit_link).'">'.$user_email.'</a>';
					$user_link = add_query_arg( array( 'page' => 'penci-bf-post', 'postid' => $_GET['postid'], 'user' => $userdata->ID ), admin_url( 'admin.php' ) );
					$user = '<a href="'.esc_url($user_link).'">'.$display_name.'</a>';
				}
				$user_type = esc_html__( 'Registered User', 'penci-bookmark-follow' );
			} else {								
				$user_email_html = $user_email;
				$user_link = add_query_arg( array( 'page' => 'penci-bf-post', 'postid' => $_GET['postid'], 'user' => 0 ), admin_url( 'admin.php' ) );
				$user = '<a href="'.esc_url($user_link).'">'.esc_html__('guest', 'penci-bookmark-follow' ).'</a>';
				$user_type = esc_html__( 'Guest', 'penci-bookmark-follow' );
			}
			
			// set data
			$data[$key]['ID']			= 	isset($value['ID']) ? $value['ID'] : '';
			$data[$key]['post_author']	= 	isset($value['post_author']) ? $value['post_author'] : '';
			$data[$key]['post_name']	= 	isset($value['post_name']) ? $value['post_name'] : '';			
			$data[$key]['user_email']   = 	apply_filters( 'penci_bl_follow_post_user_email_column', $user_email_html , $value['ID'] );
			$data[$key]['user']			= 	$user;
			$data[$key]['subscribed']	=	$subscribed;
			$data[$key]['logs']			=	$logs;
			$data[$key]['user_type']	=	$user_type;
										
		}
			
		$result_arr['data']		= !empty($data)	? $data : array();
		$result_arr['total'] 	= isset($result_data['total']) ? $result_data['total'] 	: ''; // Total no of data
			
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
			case 'post_author' 	:
				return $item['user_type'];
			case 'subscribed' 	:
				if( isset( $_GET['paged'] ) ) {
					$status_url = add_query_arg( array( 'paged' => $_GET['paged'] ), admin_url( 'admin.php' ) );
				} else {
					$status_url = admin_url( 'admin.php' );
				}
				if( $item[ $column_name ] == '1' ) {
					$status_url = add_query_arg( array( 'page' => 'penci-bf-post', 'postid' => $_GET['postid'], 'penci_bl_status' => 'subscribe' ), $status_url );
					$status_link = '<a href="' . esc_url($status_url) . '" >' . esc_html__( 'Yes', 'penci-bookmark-follow' ) . '</a>';
				} else {
					$status_url = add_query_arg( array( 'page' => 'penci-bf-post', 'postid' => $_GET['postid'], 'penci_bl_status' => 'unsubscribe' ), $status_url );
					$status_link = '<a href="' . esc_url($status_url) . '" >' . esc_html__( 'No', 'penci-bookmark-follow' ) . '</a>';
				}
				return $status_link;
			default:
				$default_value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';            	
        	  	return apply_filters( 'penci_bl_posts_users_column_value', $default_value, $item, $column_name );				
		}
	}
	
    /**
     * Manage User Email Column
     *
     * @package Penci Bookmark Follow
     * @since 1.0.0
     */
    
    function column_user_email($item){
    	
    	$pagestr = $pagenumber = '';
    	if( isset( $_GET['paged'] ) ) { $pagestr = '&paged=%s'; $pagenumber = $_GET['paged']; }
    	
    	//Build row actions
    	if( $item['subscribed'] == '1' ) {
    		$actions['unsubscribe'] = sprintf('<a href="?page=%s&action=%s&user[]=%s&postid=%s'.$pagestr.'">'.esc_html__('Unsubscribe', 'penci-bookmark-follow').'</a>','penci-bf-post','unsubscribe',$item['ID'],$_GET['postid'], $pagenumber );
    	} else {
    		$actions['subscribe'] = sprintf('<a href="?page=%s&action=%s&user[]=%s&postid=%s'.$pagestr.'">'.esc_html__('Subscribe', 'penci-bookmark-follow').'</a>','penci-bf-post','subscribe',$item['ID'],$_GET['postid'], $pagenumber );
    	}
    	
    	$actions['delete'] = sprintf('<a class="penci-bf-users-delete penci-bf-delete" href="?page=%s&action=%s&user=%s&postid=%s'.$pagestr.'">'.esc_html__('Delete', 'penci-bookmark-follow').'</a>','penci-bf-post','delete',$item['ID'],$_GET['postid'], $pagenumber );
    	
         //Return the title contents	        
        return sprintf('%1$s %2$s',
            $item['user_email'],
            $this->row_actions( $actions )
        );
        
    }
   	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['ID']                //The value of the checkbox should be the record's id
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
	
		global $penci_bl_options;
		
        $columns = array(
    						'cb'      			=>	'<input type="checkbox" />', //Render a checkbox instead of text
				            'user_email'		=>	esc_html__( 'User Email', 'penci-bookmark-follow' ),
				            'post_author'		=>	esc_html__(	'User Type', 'penci-bookmark-follow' ),
				            'subscribed'		=>	esc_html__(	'Subscribed', 'penci-bookmark-follow' ),
				            'user'				=>	esc_html__(	'User', 'penci-bookmark-follow' ),
				        );
        if( isset( $penci_bl_options['enable_log'] ) && $penci_bl_options['enable_log'] == '1' ) {
        	$columns['logs'] = esc_html__(	'View Logs', 'penci-bookmark-follow' );
        }
        return apply_filters( 'penci_bl_posts_users_add_columns', $columns );        
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
    								'user_email'	=>	array( 'user_email', true ),   //true means its already sorted
    								'post_author'	=>	array( 'post_author', true ),
    								'subscribed'	=>	array( 'subscribed', true )
						         );
						         
    	return apply_filters( 'penci_bl_posts_users_add_sortable_column', $sortable_columns );						      
    }
	
	function no_items() {
		//message to show when no records in database table
		esc_html_e( 'No Users Found.', 'penci-bookmark-follow' );
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
        						'subscribe'		=> esc_html__('Subscribe','penci-bookmark-follow'),
        						'unsubscribe'	=> esc_html__('Unsubscribe','penci-bookmark-follow'),
					            'delete'    	=> esc_html__('Delete','penci-bookmark-follow')
					      );
        return $actions;
    }
    
	/**
     * Add filter for subscribe/unscribe
     *
     * Handles to display records for particular subscribe/unscribe
     * 
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
     */
    function extra_tablenav( $which ) {
    	
    	if( $which == 'top' ) {
    		
			$html = '';
			
			$all_status = array(
										'subscribe'	=> esc_html__( 'Subscribed', 'penci-bookmark-follow' ),
										'unsubscribe'	=> esc_html__( 'Unsubscribed', 'penci-bookmark-follow' ),
									);
			
    		$html .= '<div class="alignleft actions">';
    		
				$html .= '<select name="penci_bl_status" id="penci_bl_status" data-placeholder="' . esc_html__( 'All', 'penci-bookmark-follow' ) . '">';
				
				$html .= '<option value="" ' .  selected( isset( $_GET['penci_bl_status'] ) ? $_GET['penci_bl_status'] : '', '', false ) . '>'.esc_html__( 'All', 'penci-bookmark-follow' ).'</option>';
		
				if(isset($_REQUEST['penci_bl_status']) && !empty($_REQUEST['penci_bl_status'])) {
					$args['penci_bl_status']	= $_REQUEST['penci_bl_status'];
				}
				
				foreach ( $all_status as $key => $status ) {
					
					$args = array();
	
					if( !empty( $key ) ) {
						$args['penci_bl_status']	= $key;
						$args['count']	= true;
					}
					
					$args['postid'] = $_GET['postid'];
					
					//in case of search make parameter for retriving search data
					if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
						$args['search']	= $_REQUEST['s'];
					}
					
					//get followed post list count data from database
					$status_count = $this->model->penci_bl_get_follow_post_users_data( $args );
					$status_count = !empty( $status_count ) ? $status_count : '0';
					$status_count = ' (' . $status_count . ')';
					$html .= '<option value="' . $key . '" ' . selected( isset( $_GET['penci_bl_status'] ) ? $_GET['penci_bl_status'] : '', $key, false ) . '>' . $status . $status_count . '</option>';
				}
			
				$html .= '</select>';								
				
    		$html .= '	<input type="submit" value="'.esc_html__( 'Filter', 'penci-bookmark-follow' ).'" class="button" id="post-query-submit" name="">';
    		$html .= '	<input type="submit" id="export_posts_followers" name="export_posts_followers" class="button button-primary" value="'.esc_html__( 'Export Followers', 'penci-bookmark-follow' ).'">';
    		$html .= '</div>';
    		
			echo $html;									
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
		$data_res 	= $this->display_follow_post_users();
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
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_name'; //If no sort, default to title
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
$FollowedUsersListTable = new Penci_Bf_Users_List();
	
//Fetch, prepare, sort, and filter our data...
$FollowedUsersListTable->prepare_items();
		
?>

<div class="wrap">
    <?php 
    	$data = get_post( $_GET['postid'] );
    	$title = !empty( $data->post_title ) ? $data->post_title : '';
    	if( strlen( $title ) > 50 ) {
			$title = substr( $title, 0, 50 );
			$title = $title.'...';
		}
		
		//back url to go back on the page
		$backurl = add_query_arg( array( 'page' => 'penci-bf-post' ), admin_url( 'admin.php' ) );
    ?>
    

	<h2 class="penci-bf-list-title">
    	<?php printf( esc_html__( 'Followers For %s', 'penci-bookmark-follow' ), $title ); ?>
    	<a href="<?php echo esc_url($backurl);?>" class="button"><?php esc_html_e( 'Go Back', 'penci-bookmark-follow' );?></a>
    </h2>
    
    
    <?php 
    
    	//showing sorting links on the top of the list
    	$FollowedUsersListTable->views(); 
    	
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
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <input type="hidden" name="postid" value="<?php echo  $_GET['postid']; ?>" />
        
        <!-- Now we can render the completed list table -->
        <?php $FollowedUsersListTable->display(); ?>
        
    </form>
</div><!--wrap-->