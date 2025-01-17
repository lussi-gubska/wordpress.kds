<?php 

/**
 * Template For Manage author's followers Page
 * 
 * Handles to return design of manage author's followers
 * page
 * 
 * Override this template by copying it to yourtheme/follow-my-blog-post/author-followers/author-followers.php
 *
 * @package Penci Bookmark Follow
 * @since 1.8.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="penci-bf-follows penci-bf-author-followers">
	<?php 
		global $current_user,$penci_bl_model,$penci_bl_options;

		// Get author id		
		$authorid = !empty( $author_id ) ? $author_id : $current_user->ID;

		$perpage = '10';
		
		//model class
		$model = $penci_bl_model;
		
		// creating new array for all author's followers count
		$argscount = array(
								'authorid' 		=>	$authorid,
								'penci_bl_status'	=> 'subscribe',
								'count'			=>	'1'
							);

		//Exclude Guest user
		if( isset( $exclude_guest ) && $exclude_guest ) {			
			$argscount['author__not_in'] = array(-0);
		}

		//getting all sold author's followers count
		$datacount = $model->penci_bl_get_follow_author_users_data( $argscount );
		
		// start paging
		$paging = new Penci_Bf_Pagination_Public( 'penci_bl_author_followers_ajax_pagination' );
			
		$paging->items( $datacount ); 
		$paging->limit( $perpage ); // limit entries per page
		
		if( isset( $_POST['paging'] ) ) {
			$paging->currentPage( $_POST['paging'] ); // gets and validates the current page
		}
		
		$paging->calculate(); // calculates what to show
		$paging->parameterName( 'paging' );
		
		// setting the limit to start
		$limit_start = ( $paging->page - 1 ) * $paging->limit;
		
		if(isset($_POST['paging'])) { 
			
			//ajax call pagination
			$argsdata = array(
								'authorid' 			=>	$authorid,
								'penci_bl_status'		=> 'subscribe',
								'posts_per_page' 	=>	$perpage,
								'paged'				=>	$_POST['paging']
							);
			
		} else {
			//on page load 
			$argsdata = array(
								'authorid' 			=>	$authorid,
								'penci_bl_status'		=> 'subscribe',
								'posts_per_page' 	=>	$perpage,
								'paged'				=>	'1'
							);
		}

		//Exclde Guest user
		if( isset( $exclude_guest ) && $exclude_guest ) {			
			$argsdata['author__not_in'] = array(-0);
		}

		$followers = $model->penci_bl_get_follow_author_users_data( $argsdata );

		if( !empty( $followers ) ) { //check author's followers are not empty
			
			//do action add something before author's followers table
			do_action( 'penci_bl_author_followers_table_before', $followers );
					
			// start displaying the paging if needed
			//do action add author's followers listing table
			do_action( 'penci_bl_author_followers_table', $followers, $paging );
	
			//do action add something after author's followers table after	
			do_action( 'penci_bl_author_followers_table_after', $followers );
		
		} else { //if user doesnt have any followers
		?>
			
			<div class="penci-bf-no-record-message"><?php esc_html_e( 'No any followers yet.','penci-bookmark-follow' );?></div>
			
		<?php
		
		} //end else ?>

</div><!--.penci-bf-follows-->