<?php

/**
 * Follow Posts Listing
 *
 * Template for follow posts Listing
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/subscribe-manage/follow-posts-listing/follow-posts-listing.php
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
if ( ! function_exists( 'penci_get_setting' ) ) {
	return false;
}
?>


    <div class="penci-smalllist pcsl-wrapper pwsl-id-default">


        <div class="pcsl-inner penci-clearfix pcsl-grid pencipw-hd-text pcsl-imgpos-left pcsl-col-2 pcsl-tabcol-2 pcsl-mobcol-1">
			<?php
			foreach ( $followposts as $followpost ) {

				$post_parent = isset( $followpost['post_parent'] ) && ! empty( $followpost['post_parent'] ) ? $followpost['post_parent'] : '';

				if ( ! empty( $post_parent ) ) { // Check post parent is not empty

					$posts = get_post( $post_parent );
					if ( ! empty( $posts ) ) {

						// Get Follow Post Name
						$post_name = ! empty( $posts->post_title ) ? $posts->post_title : pencibf_get_text( 'notitle' );

					} else {
						$post_name = pencibf_get_text( 'deletedpost' );
					}

					// Get Follow Post Type
					$posttype = ! empty( $posts->post_type ) ? $posts->post_type : '';

					// Get Follow Post Type Name
					$post_type_name = ! empty( $posttype ) && isset( $post_types[ $posttype ]->labels->singular_name ) ? $post_types[ $posttype ]->labels->singular_name : '';

					// Get Follow Date
					$followdate = $model->penci_bl_get_date_format( $followpost['post_date'] );
					if ( ! empty( $posts->ID ) ) {
						?>
                        <div class="pcsl-item">
                            <div class="pcsl-itemin">
								<?php
								//do action to add row for orders list before
								do_action( 'penci_bl_follow_post_row_before', $followpost['ID'] );
								?>

                                <div class="pcsl-iteminer">
                                    <div class="pcsl-thumb">
										<?php
										$args = array(
											'post_id'          => $post_parent,
											'current_post_id'  => $followpost['ID'],
											'follow_message'   => '',
											'follow_pos_class' => 'small',
										);
										do_action( 'penci_bl_follow_post', $args );
										?>
                                        <a title="<?php echo wp_strip_all_tags( get_the_title( $posts->ID ) ); ?>"
                                           href="<?php echo get_the_permalink( $posts->ID ); ?>"
                                           class="penci-image-holder"
                                           style="background-image: url('<?php echo penci_get_featured_image_size( $posts->ID, 'penci-thumb' ); ?>');padding-bottom:<?php echo penci_get_featured_image_padding_markup( $posts->ID, 'penci-thumb', true ); ?>;">
                                        </a>
                                    </div>
                                    <div class="pcsl-content">

                                        <div class="pcsl-title">
                                            <a href="<?php echo get_permalink( $posts->ID ); ?>"><?php echo get_the_title( $posts->ID ); ?></a>
                                        </div>

										<?php if ( ( get_theme_mod( 'pencibf_show_reading' ) && penci_reading_time( false, $posts->ID ) ) || get_theme_mod( 'pencibf_show_views' ) || get_theme_mod( 'pencibf_show_author', true ) || get_theme_mod( 'pencibf_show_postdate', true ) || get_theme_mod( 'pencibf_show_comments' ) ) { ?>


                                            <div class="grid-post-box-meta pcsl-meta">

												<?php if ( get_theme_mod( 'pencibf_show_author', true ) ) : ?>
                                                    <span class="sl-date-author author-italic">
													<?php echo penci_get_setting( 'penci_trans_by' ); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) :
															coauthors_posts_links();
														else : ?>
                                                            <a class="author-url url fn n"
                                                               href="<?php echo get_author_posts_url( $posts->post_author ); ?>"><?php echo get_the_author_meta( 'nicename', $posts->post_author ); ?></a>
														<?php endif; ?>
													</span>
												<?php endif; ?>
												<?php if ( get_theme_mod( 'pencibf_show_postdate', true ) ) : ?>
                                                    <span class="sl-date"><?php pencibf_soledad_time_link( $posts->ID, '' ); ?></span>
												<?php endif; ?>

												<?php if ( get_theme_mod( 'pencibf_show_comments' ) ) : ?>
                                                    <span class="sl-comment">
												<a href="<?php echo get_comments_link( $posts->ID ); ?> "><?php comments_number( '0 ' . penci_get_setting( 'penci_trans_comment' ), '1 ' . penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ), $posts->ID ); ?></a>
											</span>
												<?php endif; ?>

												<?php if ( get_theme_mod( 'pencibf_show_views' ) ) : ?>
                                                    <span class="sl-views"><?php echo penci_get_post_views( $posts->ID ) . ' ' . penci_get_setting( 'penci_trans_countviews' ); ?></span>
												<?php endif; ?>

												<?php if ( get_theme_mod( 'pencibf_show_reading' ) && penci_reading_time( false, $posts->ID ) ) : ?>

                                                    <span class="sl-readtime"><?php penci_reading_time( true, $posts->ID ); ?></span>
												<?php endif; ?>
                                            </div>

										<?php } ?>


                                    </div>
                                </div>
								<?php
								//do action to add row for orders list after
								do_action( 'penci_bl_follow_post_row_after', $followpost['ID'] );
								?>
                            </div>
                        </div>
						<?php
					}
				} else if ( ! is_user_logged_in() ) {
					$posts = get_post( $followpost['ID'] );
					if ( ! empty( $posts ) ) {

						// Get Follow Post Name
						$post_name = ! empty( $posts->post_title ) ? $posts->post_title : esc_html__( '(no title)', 'penci-bookmark-follow' );

					} else {
						$post_name = pencibf_get_text( 'deletedpost' );
					}

					// Get Follow Post Type
					$posttype = ! empty( $posts->post_type ) ? $posts->post_type : '';

					// Get Follow Post Type Name
					$post_type_name = ! empty( $posttype ) && isset( $post_types[ $posttype ]->labels->singular_name ) ? $post_types[ $posttype ]->labels->singular_name : '';

					// Get Follow Date
					$followdate = $model->penci_bl_get_date_format( $followpost['post_date'] );
					?>
                    <div class="pcsl-item">
                        <div class="pcsl-itemin">
							<?php
							//do action to add row for orders list before
							do_action( 'penci_bl_follow_post_row_before', $followpost['ID'] );
							?>

                            <div class="pcsl-iteminer">
                                <div class="pcsl-thumb">
									<?php
									$args = array(
										'post_id'          => $posts->ID,
										'current_post_id'  => $followpost['ID'],
										'follow_message'   => '',
										'follow_pos_class' => 'small',
									);
									do_action( 'penci_bl_follow_post', $args );
									?>
                                    <a title="<?php echo wp_strip_all_tags( get_the_title( $posts->ID ) ); ?>"
                                       href="<?php echo get_the_permalink( $posts->ID ); ?>" class="penci-image-holder"
                                       style="background-image: url('<?php echo penci_get_featured_image_size( $posts->ID, 'penci-thumb' ); ?>');padding-bottom:<?php echo penci_get_featured_image_padding_markup( $posts->ID, 'penci-thumb', true ); ?>;">
                                    </a>
                                </div>
                                <div class="pcsl-content">
                                    <div class="pcsl-title">
                                        <a href="<?php echo get_permalink( $posts->ID ); ?>"><?php echo get_the_title( $posts->ID ); ?></a>
                                    </div>

									<?php if ( ( get_theme_mod( 'pencibf_show_reading' ) && penci_reading_time( false, $posts->ID ) ) || get_theme_mod( 'pencibf_show_views' ) || get_theme_mod( 'pencibf_show_author', true ) || get_theme_mod( 'pencibf_show_postdate', true ) || get_theme_mod( 'pencibf_show_comments' ) ) { ?>

                                        <div class="grid-post-box-meta pcsl-meta">

											<?php if ( get_theme_mod( 'pencibf_show_author', true ) ): ?>
                                                <span class="sl-date-author author-italic">
													<?php echo penci_get_setting( 'penci_trans_by' ); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) :
														coauthors_posts_links();
													else: ?>
                                                        <a class="author-url url fn n"
                                                           href="<?php echo get_author_posts_url( $posts->post_author ); ?>"><?php echo get_the_author_meta( 'nicename', $posts->post_author ); ?></a>
													<?php endif; ?>
													</span>
											<?php endif; ?>
											<?php if ( get_theme_mod( 'pencibf_show_postdate', true ) ): ?>
                                                <span class="sl-date"><?php pencibf_soledad_time_link( $posts->ID, '' ); ?></span>
											<?php endif; ?>

											<?php if ( get_theme_mod( 'pencibf_show_comments' ) ): ?>
                                                <span class="sl-comment">
												<a href="<?php echo get_comments_link( $posts->ID ); ?> "><?php comments_number( '0 ' . penci_get_setting( 'penci_trans_comment' ), '1 ' . penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ), $posts->ID ); ?></a>
											</span>
											<?php endif; ?>

											<?php if ( get_theme_mod( 'pencibf_show_views' ) ): ?>
                                                <span class="sl-views"><?php echo penci_get_post_views( $posts->ID ) . ' ' . penci_get_setting( 'penci_trans_countviews' ); ?></span>
											<?php endif; ?>

											<?php if ( get_theme_mod( 'pencibf_show_reading' ) && penci_reading_time( false, $posts->ID ) ): ?>

                                                <span class="sl-readtime"><?php penci_reading_time( true, $posts->ID ); ?></span>
											<?php endif; ?>
                                        </div>

									<?php } ?>
                                </div>
                            </div>
							<?php
							//do action to add row for orders list after
							do_action( 'penci_bl_follow_post_row_after', $followpost['ID'] );
							?>
                        </div>
                    </div>
					<?php
				}
			} ?>
        </div>
    </div>

<?php if ( $paging && $paging->is_render() ): ?>

    <div class="penci-bf-paging penci-bf-follow-posts-paging">
        <div id="penci-bf-tablenav-pages" class="penci-bf-tablenav-pages">
			<?php echo $paging->getOutput(); ?>
        </div><!--.penci-bf-tablenav-pages-->
    </div><!--.penci-bf-paging-->
<?php endif; ?>