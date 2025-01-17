<?php
$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) ? get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) : 0;
$unlocked_posts   = get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) ? get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) : array();
?>

<div class="pencipw_manage_status">
    <div class="pencipw_boxed">
        <span><strong><?php echo pencipw_text_translation( 'quotas_left' ); ?>:</strong> <?php echo $unlock_remaining . ' ' . pencipw_text_translation( 'unlocks' ); ?></span>
        <span><strong><?php echo pencipw_text_translation( 'posts_owned' ); ?>:</strong> <?php echo count( $unlocked_posts ) . ' ' . pencipw_text_translation( 'posts' ); ?></span>
    </div>
    <div class="pencipw-frontend-status">
        <h2 class="pencipw-frontend-status-heading"><?php echo pencipw_text_translation( 'posts_collection' ); ?></h2>
		<?php
		if ( ! empty( $unlocked_posts ) ) {
			?>
            <div class="penci-smalllist">
                <div class="pcsl-inner penci-clearfix pcsl-grid pcsl-imgpos-left pcsl-col-1 pcsl-tabcol-2 pcsl-mobcol-1">
					<?php
					foreach (
						$unlocked_posts

						as $post_id
					) {
						setup_postdata( $post_id );
						?>
                        <article class="<?php echo implode( ' ', get_post_class( 'pcsl-item', $post_id ) ); ?>">
                            <div class="pcsl-itemin">
                                <div class="pcsl-iteminer">
                                    <div class="pcsl-thumb">
                                        <a href="<?php echo get_permalink( $post_id ); ?>"
                                           title="<?php echo wp_strip_all_tags( get_the_title( $post_id ) ); ?>"
                                           class="penci-image-holder penci-lazy"
                                           data-bgset="<?php echo penci_get_featured_image_size( $post_id, 'penci-thumb' ); ?>">
                                        </a>
                                    </div>
                                    <div class="pcsl-content">
                                        <div class="pcsl-title">
                                            <a href="<?php echo get_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a>
                                        </div>
                                        <div class="grid-post-box-meta pcsl-meta">
                                            <span class="sl-date"><?php penci_soledad_time_link(); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
					<?php } ?>
                </div>
            </div>
			<?php
		} else {
			echo '<div class="penci-notice">' . pencipw_text_translation( 'noposts' ) . '</div>';
		}
		?>
    </div>
</div>
