<?php

/**
 * Template For Manage Follow Posts Page
 *
 * Handles to return design of manage follow posts
 * page
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/subscribe-manage/follow-posts.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="penci-bf-follows penci-bf-manage-follow-posts"
     data-only-following="<?php echo isset( $only_following ) ? $only_following : '' ?>">
	<?php
	global $current_user, $penci_bl_model, $penci_bl_options;
	$prefix = PENCI_BL_META_PREFIX;


	$perpage = get_theme_mod( 'pencibf_number_ppp', 6 );

	//model class
	$model = $penci_bl_model;

	$argsdata = $followposts = [];

	if ( is_user_logged_in() ) {

		// creating new array for all follow posts count
		$argscount = array(
			'author' => $current_user->ID,
			'count'  => '1'
		);

		$argscount['penci_bl_status'] = 'subscribe';

		//getting all sold follow posts count
		$datacount = $model->penci_bl_get_follow_post_users_data( $argscount );

		// start paging
		$paging = new Penci_Bf_Pagination_Public( 'penci_bl_follow_post_ajax_pagination' );

		$paging->items( $datacount );
		$paging->limit( $perpage ); // limit entries per page

		if ( isset( $_POST['paging'] ) ) {
			$paging->currentPage( $_POST['paging'] ); // gets and validates the current page
		}

		$paging->calculate(); // calculates what to show
		$paging->parameterName( 'paging' );

		// setting the limit to start
		$limit_start = ( $paging->page - 1 ) * $paging->limit;

		if ( isset( $_POST['paging'] ) && $_POST['paging'] ) {

			//ajax call pagination
			$argsdata = array(
				'author'         => $current_user->ID,
				'posts_per_page' => $perpage,
				'paged'          => $_POST['paging'],
			);

		} else {
			//on page load
			$argsdata = array(
				'author'         => $current_user->ID,
				'posts_per_page' => $perpage,
				'paged'          => 1,
			);
		}

		//Check if argument for get only following
		$argsdata['penci_bl_status'] = 'subscribe';

	} elseif ( isset( $_COOKIE['penci-bf-posts-ids'] ) && $_COOKIE['penci-bf-posts-ids'] ) {
		$argsdata             = [];
		$paging               = false;
		$post_ids             = explode( ',', str_replace( [
			'\"',
			'[',
			']'
		], '', $_COOKIE['penci-bf-posts-ids'] ) );
		$argsdata['post__in'] = $post_ids;
	}

	if ( $argsdata ) {
		$followposts = $model->penci_bl_get_follow_post_users_data( $argsdata );
	}

	echo '<h3>' . pencibf_get_text( 'postIfollow' ) . '</h3>';


	if ( ! empty( $followposts ) ) { //check follow posts are not empty

		//do action add something before follow posts table
		do_action( 'penci_bl_follow_posts_table_before', $followposts );

		// start displaying the paging if needed
		//do action add follow posts listing table
		do_action( 'penci_bl_follow_posts_table', $followposts, $paging );

		//do action add something after follow posts table after
		do_action( 'penci_bl_follow_posts_table_after', $followposts );

	} else { //if user is not follow any posts
		?>

        <div class="penci-bf-no-record-message"><?php echo pencibf_get_text('nopostfollow'); ?></div>

		<?php

	} //end else


	?>
</div><!--.penci-bf-follows-->