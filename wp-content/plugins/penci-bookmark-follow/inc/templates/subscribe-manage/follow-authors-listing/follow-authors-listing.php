<?php

/**
 * Follow Author Listing
 *
 * Template for follow authors Listing
 *
 * Override this template by copying it to
 * yourtheme/follow-my-blog-post/subscribe-manage/follow-authors-listing/follow-authors-listing.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 **/

global $penci_bl_model, $penci_bl_options;

//model class
$model = $penci_bl_model;

$prefix = PENCI_BL_META_PREFIX;

$page = isset( $_POST['paging'] ) ? $_POST['paging'] : '1';

// get all custom post types
$post_types = get_post_types( array( 'public' => true ), 'objects' );

$followtext    = $penci_bl_options['authors_follow_buttons']['follow'];
$followingtext = $penci_bl_options['authors_follow_buttons']['following'];
$unfollowtext  = $penci_bl_options['authors_follow_buttons']['unfollow'];

$followtext    = str_replace( '{author_name}', '', $followtext );
$followingtext = str_replace( '{author_name}', '', $followingtext );
$unfollowtext  = str_replace( '{author_name}', '', $unfollowtext );
$class         = is_user_logged_in() ? 'pcbf-logged-in' : 'pcbf-guest';
?>
    <div class="penci-bf-follow-author-table-container">
        <table class="penci-bf-follow-author-table <?php echo $class; ?>">
            <thead>
            <tr class="penci-bf-follow-author-row-head">
				<?php

				//do action to add header title of orders list before
				do_action( 'penci_bl_follow_author_header_before' );

				?>
                <th class="penci-bf-picture" width="10%"><?php echo pencibf_get_text( 'avatar' ); ?></th>
                <th class="penci-bf-col-2"><?php echo pencibf_get_text( 'authorname' ); ?></th>
				<?php if ( is_user_logged_in() ): ?>
                    <th class="penci-bf-col-date"><?php echo pencibf_get_text( 'followeddate' ); ?></th>
				<?php endif; ?>
				<?php
				if ( ! $disabled_actions ) {
					echo '<th class="penci-bf-col-action">' . pencibf_get_text( 'actions' ) . '</th>';
				}

				//do action to add header title of orders list after
				do_action( 'penci_bl_follow_author_header_after' );
				?>
            </tr>
            </thead>

            <tbody>
			<?php
			foreach ( $followauthors as $followauthor ) {

				if ( is_user_logged_in() ) {
					$authorid = isset( $followauthor['post_parent'] ) && ! empty( $followauthor['post_parent'] ) ? $followauthor['post_parent'] : '';
				} else {
					$authorid = $followauthor;
				}

				$id_check = is_user_logged_in() ? $followauthor['ID'] : $followauthor;

				if ( ! empty( $authorid ) ) { // Check post parent is not empty

					// Get Follow author Name
					$author_data  = get_user_by( 'id', $authorid );
					$author_name  = isset( $author_data->data->display_name ) ? $author_data->data->display_name : '';
					$author_email = isset( $author_data->data->user_email ) ? $author_data->data->user_email : '';

					// Get Follow Date
					if ( is_user_logged_in() ) {
						$followdate = $model->penci_bl_get_date_format( $followauthor['post_date'] );
					} else {
						$followdate = false;
					}

					if ( empty( $author_data ) && empty( $author_name ) ) {
						$author_name = pencibf_get_text( 'deletedauthor' );
					}
					?>
                    <tr class="penci-bf-follow-author-row-body">
						<?php

						//do action to add row for orders list before
						do_action( 'penci_bl_follow_author_row_before', $id_check );

						?>
                        <td class="penci-bf-picture">
                            <a href="<?php echo esc_url( get_author_posts_url( $authorid ) ); ?>">
                                <img src="<?php echo esc_url( apply_filters( 'penci_bl_author_following_avatar', get_avatar_url( $author_email, 32 ), $author_email ) ); ?>"
                                     alt="Profile Photo" width="32"></a>
                        </td>
                        <td class="penci-bf-col-2">
                            <a href="<?php echo esc_url( get_author_posts_url( $authorid ) ); ?>"><?php echo apply_filters( 'penci_bl_change_author_name', $model->penci_bl_short_content( $author_name ), $authorid ); ?></a>
                        </td>
						<?php if ( is_user_logged_in() ): ?>
                            <td class="penci-bf-col-date"><?php echo $followdate; ?></td>
						<?php endif; ?>
						<?php if ( ! $disabled_actions ) { ?>
                            <td class="penci-bf-col-action">
								<?php
								$args = array(
									'author_id'       => $authorid,
									'current_post_id' => $id_check,
									'follow_message'  => '',
									'follow_buttons'  => array(
										'follow'    => trim( $followtext ),
										'following' => trim( $followingtext ),
										'unfollow'  => trim( $unfollowtext ),
									),
								);
								do_action( 'penci_bl_follow_author', $args );
								?>
                            </td>
							<?php
						}

						//do action to add row for orders list after
						do_action( 'penci_bl_follow_author_row_after', $id_check ); ?>
                    </tr>
				<?php }
			} ?>
            </tbody>
            <tfoot>
            <tr class="penci-bf-follow-author-row-foot">
				<?php

				//do action to add row in footer before
				do_action( 'penci_bl_follow_author_footer_before' );

				?>
                <th class="penci-bf-picture"><?php echo pencibf_get_text( 'avatar' ); ?></th>
                <th><?php echo pencibf_get_text( 'authorname' ); ?></th>
				<?php if ( is_user_logged_in() ): ?>
                    <th><?php echo pencibf_get_text( 'followeddate' ); ?></th>
				<?php endif; ?>
				<?php
				if ( ! $disabled_actions ) {
					echo '<th>' . pencibf_get_text( 'actions' ) . '</th>';
				}

				//do action to add row in footer after
				do_action( 'penci_bl_follow_author_footer_after' );
				?>
            </tr>
            </tfoot>
        </table>
    </div>
<?php if ( $paging && $paging->is_render() ): ?>
    <div class="penci-bf-paging penci-bf-follow-authors-paging">
        <div id="penci-bf-tablenav-pages" class="penci-bf-tablenav-pages">
			<?php echo $paging->getOutput( 'author' ); ?>
        </div><!--.penci-bf-tablenav-pages-->
    </div><!--.penci-bf-paging-->
<?php endif; ?>