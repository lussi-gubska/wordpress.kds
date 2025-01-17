<?php

/**
 * Author Followers Listing
 *
 * Template for Author Followerss Listing
 *
 * Override this template by copying it to
 * yourtheme/follow-my-blog-post/author-followers/author-followers-listing.php
 *
 * @package Penci Bookmark Follow
 * @since 1.8.5
 **/

global $penci_bl_model, $penci_bl_options;

//model class
$model = $penci_bl_model;

$prefix = PENCI_BL_META_PREFIX;

$page  = isset( $_POST['paging'] ) ? $_POST['paging'] : '1';
$class = is_user_logged_in() ? 'pcbf-logged-in' : 'pcbf-guest';
?>
<h3><?php esc_html_e( 'Author Followers', 'penci-bookmark-follow' ); ?></h3>
<div class="penci-bf-follow-author-table-container">
    <table class="penci-bf-follow-author-table <?php echo $class; ?>">
        <thead>
        <tr class="penci-bf-follow-author-row-head">
			<?php
			//do action to add header title of followers list before
			do_action( 'penci_bl_author_followers_header_before' );
			?>
            <th class="penci-bf-picture"><?php esc_html_e( 'Avatar', 'penci-bookmark-follow' ); ?></th>
            <th class="penci-bf-col-2"><?php esc_html_e( 'Author Name', 'penci-bookmark-follow' ); ?></th>
            <th class="penci-bf-col-action"><?php esc_html_e( 'Followed Date', 'penci-bookmark-follow' ); ?></th>
			<?php
			//do action to add header title of followers list after
			do_action( 'penci_bl_author_followers_header_after' );
			?>
        </tr>
        </thead>

        <tbody>
		<?php
		foreach ( $followers as $follower ) {

			$authorid = isset( $follower['post_author'] ) && ! empty( $follower['post_author'] ) ? $follower['post_author'] : '';

			// get user email from meta field
			if ( ! empty( $authorid ) ) {

				// Get Author Followers Name
				$author_data      = get_user_by( 'id', $authorid );
				$author_email     = isset( $author_data->data->user_email ) ? $author_data->data->user_email : '';
				$author_name      = isset( $author_data->data->display_name ) ? $author_data->data->display_name : '';
				$disp_author_name = apply_filters( 'penci_bl_change_author_name', $model->penci_bl_short_content( $author_name ), $authorid );

			} else {
				$author_email     = get_post_meta( $follower['ID'], $prefix . 'author_user_email', true );
				$disp_author_name = $author_email . esc_html__( ' ( guest )', 'penci-bookmark-follow' );
			}

			// Get Follow Date
			$followdate = $model->penci_bl_get_date_format( $follower['post_date'] );
			?>
            <tr class="penci-bf-follow-author-row-body">
				<?php
				//do action to add row for followers list before
				do_action( 'penci_bl_author_followers_row_before', $follower['ID'] );
				?>
                <td class="penci-bf-picture">
                    <img src="<?php echo esc_url( apply_filters( 'penci_bl_author_follower_avatar', get_avatar_url( $author_email, 32 ), $author_email ) ); ?>"
                         alt="Profile Photo" width="32">
                </td>
                <td class="penci-bf-col-2"><?php echo $disp_author_name; ?></td>
                <td class="penci-bf-col-action"><?php echo $followdate; ?></td>
				<?php
				//do action to add row for followers list after
				do_action( 'penci_bl_author_followers_row_after', $follower['ID'] );
				?>
            </tr>
			<?php
		} ?>
        </tbody>
        <tfoot>
        <tr class="penci-bf-follow-author-row-foot">
			<?php
			//do action to add row in footer before
			do_action( 'penci_bl_author_followers_footer_before' );
			?>
            <th class="penci-bf-picture"><?php esc_html_e( 'Avatar', 'penci-bookmark-follow' ); ?></th>
            <th><?php esc_html_e( 'Author Name', 'penci-bookmark-follow' ); ?></th>
            <th><?php esc_html_e( 'Followed Date', 'penci-bookmark-follow' ); ?></th>
			<?php
			//do action to add row in footer after
			do_action( 'penci_bl_author_followers_footer_after' );
			?>
        </tr>
        </tfoot>
    </table>
</div>

<div class="penci-bf-paging penci-bf-follow-authors-paging">
    <div id="penci-bf-tablenav-pages" class="penci-bf-tablenav-pages">
		<?php echo $paging->getOutput(); ?>
    </div><!--.penci-bf-tablenav-pages-->
</div><!--.penci-bf-paging-->
<div class="penci-bf-follow-loader penci-bf-follow-authors-loader">
    <img src="<?php echo esc_url( PENCI_BL_IMG_URL ); ?>/loader.gif"/>
</div><!--.penci-bf-sales-loader-->