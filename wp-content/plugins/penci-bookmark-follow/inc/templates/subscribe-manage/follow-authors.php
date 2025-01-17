<?php

/**
 * Template For Manage Follow Authors Page
 *
 * Handles to return design of manage follow authors
 * page
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/subscribe-manage/follow-authors.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="penci-bf-follows penci-bf-manage-follow-authors"
     data-only-following="<?php echo isset( $only_following ) ? $only_following : '' ?>">
	<?php
	global $current_user, $penci_bl_model, $penci_bl_options;
	$prefix        = PENCI_BL_META_PREFIX;
	$followauthors = [];

	//Check if pased author is not match with curent user OR Logged in
	if ( is_user_logged_in() || ! empty( $author_id ) ) { //check user is logged in or not

		$perpage = get_theme_mod( 'pencibf_number_ppp', 6 );

		//model class
		$model = $penci_bl_model;

		//Get author id
		$author_id        = ! empty( $author_id ) ? $author_id : $current_user->ID;
		$disabled_actions = isset( $disabled_actions ) ? $disabled_actions : ( empty( $current_user->ID ) ? true : false );

		// creating new array for all follow authors count
		$argscount = array(
			'author' => $author_id,
			'count'  => '1'
		);

		//Check if argument for get only following
		if ( isset( $only_following ) && $only_following == '1' ) {
			$argscount['penci_bl_status'] = 'subscribe';
		}

		//getting all sold follow authors count
		$datacount = $model->penci_bl_get_follow_author_users_data( $argscount );

		// start paging
		$paging = new Penci_Bf_Pagination_Public( 'penci_bl_follow_author_ajax_pagination' );

		$paging->items( $datacount );
		$paging->limit( $perpage ); // limit entries per page

		if ( isset( $_POST['paging'] ) ) {
			$paging->currentPage( $_POST['paging'] ); // gets and validates the current page
		}

		$paging->calculate(); // calculates what to show
		$paging->parameterName( 'paging' );

		// setting the limit to start
		$limit_start = ( $paging->page - 1 ) * $paging->limit;

		if ( isset( $_POST['paging'] ) ) {

			//ajax call pagination
			$argsdata = array(
				'author'         => $author_id,
				'posts_per_page' => $perpage,
				'paged'          => $_POST['paging']
			);

		} else {
			//on page load
			$argsdata = array(
				'author'         => $author_id,
				'posts_per_page' => $perpage,
				'paged'          => '1'
			);
		}

		//Check if argument for get only following
		$argsdata['penci_bl_status'] = 'subscribe';

		$followauthors = $model->penci_bl_get_follow_author_users_data( $argsdata );

	} elseif ( isset( $_COOKIE['penci-bf-author-ids'] ) && $_COOKIE['penci-bf-author-ids'] ) {
		$paging           = false;
		$disabled_actions = false;
		$paging           = false;
		$followauthors    = explode( ',', str_replace( [
			'\"',
			'[',
			']'
		], '', $_COOKIE['penci-bf-author-ids'] ) );
	}

	echo '<h3>' . pencibf_get_text( 'authorIfollow' ) . '</h3>';

	$followauthors = is_array( $followauthors ) ? array_filter( $followauthors ) : null;

	if ( ! empty( $followauthors ) ) { //check follow authors are not empty

		//do action add something before follow authors table
		do_action( 'penci_bl_follow_authors_table_before', $followauthors, $disabled_actions );

		// start displaying the paging if needed
		//do action add follow authors listing table
		do_action( 'penci_bl_follow_authors_table', $followauthors, $paging, $disabled_actions );

		//do action add something after follow authors table after
		do_action( 'penci_bl_follow_authors_table_after', $followauthors, $disabled_actions );

	} else { //if user is not follow any authors
		?>

        <div class="penci-bf-no-record-message"><?php echo pencibf_get_text( 'noauthorfollow' ); ?></div>

		<?php

	} //end else


	?>
</div><!--.penci-bf-follows-->