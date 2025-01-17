<?php
/**
 * Template loop for small list style
 */
?>
<li class="list-post penci-item-listp penci-slistp">
    <article id="post-<?php the_ID(); ?>" class="item hentry">
        <div class="content-list-right content-list-center<?php if ( ! has_post_thumbnail() ) : echo ' fullwidth'; endif; ?>">
	        <div class="left-play-btn">
		        <?php echo pencipdc_podcast_add_media_menu( get_the_ID(), 'episode play_btn' ); ?>
            </div>
            <div class="header-list-style">
                <h4 class="penci-entry-title entry-title grid-title"><a
                            href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
				<?php penci_soledad_meta_schema(); ?>
				<?php $hide_readtime = get_theme_mod( 'penci_grid_readingtime' ); ?>
				<?php if ( ! get_theme_mod( 'penci_grid_date' ) || ! get_theme_mod( 'penci_grid_author' ) || get_theme_mod( 'penci_grid_countviews' ) || get_theme_mod( 'penci_grid_comment_other' ) || penci_isshow_reading_time( $hide_readtime ) ) : ?>
                    <div class="grid-post-box-meta">
						<?php if ( ! get_theme_mod( 'penci_grid_date' ) ) : ?>
                            <span class="otherl-date"><?php penci_soledad_time_link(); ?></span>
						<?php endif; ?>
						<?php
						if ( get_theme_mod( 'penci_grid_countviews' ) ) {
							echo '<span>';
							echo penci_get_post_views( get_the_ID() );
							echo ' ' . penci_get_setting( 'penci_trans_countviews' );
							echo '</span>';
						}
						?>
						<?php if ( penci_isshow_reading_time( $hide_readtime ) ): ?>
                            <span class="otherl-readtime"><?php penci_reading_time(); ?></span>
						<?php endif; ?>
						<?php if ( get_post_meta( get_the_ID(), 'pencipc_media_duration', true ) ): ?>
                            <span class="media-duration"><?php echo get_post_meta( get_the_ID(), 'pencipc_media_duration', true ); ?></span>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
            </div>
			<?php echo pencipdc_podcast_add_media_menu( get_the_ID(), 'episode_overlay_more' ); ?>
        </div>
    </article>
</li>
