<?php
/**
 * Template loop for small list style
 */
?>
<li class="list-post penci-item-listp penci-slistp">
    <article id="post-<?php the_ID(); ?>" class="item hentry">
		<?php if ( has_post_thumbnail() ) : ?>
            <div class="thumbnail">
				<?php
				$thumbnail_load = 'penci-thumb';
				$class_load     = '';
				if ( get_theme_mod( 'penci_grid_nocrop_list' ) ):
					$thumbnail_load = 'penci-masonry-thumb';
					$class_load     = ' penci-list-nocrop-thumb';
				endif;
				?>

				<?php if ( ! get_theme_mod( 'penci_disable_lazyload_layout' ) ) { ?>
                    <a class="penci-image-holder penci-lazy<?php echo penci_class_lightbox_enable() . $class_load; ?>"<?php if ( get_theme_mod( 'penci_grid_nocrop_list' ) ): echo ' style="padding-bottom: ' . penci_get_featured_image_ratio( get_the_ID(), 'penci-masonry-thumb' ) . '%"'; endif; ?>
                       data-bgset="<?php echo penci_image_srcset( get_the_ID(), $thumbnail_load ); ?>"
                       href="<?php penci_permalink_fix(); ?>"
                       title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
                    </a>
				<?php } else { ?>
                    <a class="penci-image-holder<?php echo penci_class_lightbox_enable() . $class_load; ?>"
                       style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), $thumbnail_load ); ?>');<?php if ( get_theme_mod( 'penci_grid_nocrop_list' ) ): echo 'padding-bottom: ' . penci_get_featured_image_ratio( get_the_ID(), 'penci-masonry-thumb' ) . '%;'; endif; ?>"
                       href="<?php penci_permalink_fix(); ?>"
                       title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
                    </a>
				<?php } ?>

				<?php echo pencipdc_podcast_add_media_menu( get_the_ID(), 'episode_overlay' ); ?>

            </div>
		<?php endif; ?>

        <div class="content-list-right content-list-center<?php if ( ! has_post_thumbnail() ) : echo ' fullwidth'; endif; ?>">
            <div class="header-list-style">
				<?php if ( ! get_theme_mod( 'penci_grid_cat' ) ) : ?>
                    <span class="cat"><?php penci_category( '' ); ?></span>
				<?php endif; ?>

                <h2 class="penci-entry-title entry-title grid-title"><a
                            href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
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
