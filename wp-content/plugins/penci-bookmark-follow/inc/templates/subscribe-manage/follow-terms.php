<?php

/**
 * Template For Manage Follow Terms Page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<div class="penci-bf-follows penci-bf-manage-follow-terms"
     data-only-following="<?php echo isset( $only_following ) ? $only_following : '' ?>">
	<?php
	global $current_user, $penci_bl_model, $penci_bl_options;
	$prefix = PENCI_BL_META_PREFIX;

	$perpage = get_theme_mod( 'pencibf_number_ppp', 6 );
	$model = $penci_bl_model;


	if ( is_user_logged_in() ) { //check user is logged in or not


		// creating new array for all follow terms count
		$argscount = array(
			'author' => $current_user->ID,
			'count'  => '1'
		);

		//Check if argument for get only following
		$argscount['penci_bl_status'] = 'subscribe';
		
		//getting all sold follow terms count
		$datacount = $model->penci_bl_get_follow_term_users_data( $argscount );

		// start paging
		$paging = new Penci_Bf_Pagination_Public( 'penci_bl_follow_term_ajax_pagination' );

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
				'author'         => $current_user->ID,
				'posts_per_page' => $perpage,
				'paged'          => $_POST['paging']
			);

		} else {
			//on page load 
			$argsdata = array(
				'author'         => $current_user->ID,
				'posts_per_page' => $perpage,
				'paged'          => '1'
			);
		}

		//Check if argument for get only following
		$argsdata['penci_bl_status'] = 'subscribe';

		$followterms = $model->penci_bl_get_follow_term_users_data( $argsdata );

	} elseif ( isset( $_COOKIE['penci-bf-terms-ids'] ) && $_COOKIE['penci-bf-terms-ids'] ) {
		$argsdata         = [];
		$paging           = false;
		$disabled_actions = false;
		$followterms      = explode( ',', str_replace( [
			'\"',
			'[',
			']'
		], '', $_COOKIE['penci-bf-terms-ids'] ) );
	}


	echo '<h3>' . pencibf_get_text( 'catIfollow' ) . '</h3>';

	if ( ! empty( $followterms ) ) { //check follow terms are not empty

		//do action add something before follow terms table
		do_action( 'penci_bl_follow_terms_table_before', $followterms );

		// start displaying the paging if needed
		//do action add follow terms listing table
		do_action( 'penci_bl_follow_terms_table', $followterms, $paging );

		//do action add something after follow terms table after
		do_action( 'penci_bl_follow_terms_table_after', $followterms );

	} else { //if user is not follow any terms
		?>

        <div class="penci-bf-no-record-message"><?php esc_html_e( 'You have not follow any terms yet.', 'penci-bookmark-follow' ); ?></div>

		<?php

	} //end else


	?>
</div><!--.penci-bf-follows-->