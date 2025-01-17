<?php
$id             = $args['term_id'];
$term           = get_term( $id );
$link           = get_term_link( $term );
$title          = $term->name;
$total          = sprintf( _n( '%s Episode', '%s Episodes', $term->count ), number_format_i18n( $term->count ) );
$thumbnail_load = 'penci-thumb';
if ( get_theme_mod( 'penci_grid_nocrop_list' ) ):
	$thumbnail_load = 'penci-masonry-thumb';
endif;
?>
<div class="pencipdc-item">
	<?php if ( pencipdc_get_series_image_id( $id ) && get_theme_mod( 'pencipodcast_single_show_featured', true ) ): ?>
        <div class="pencipdc-thumb">
            <div class="pencipdc-thumbin">
                <a title="<?php echo wp_strip_all_tags( $title ); ?>" href="<?php echo esc_url( $link ); ?>"
                   class="penci-image-holder penci-lazy"
                   data-bgset="<?php echo wp_get_attachment_image_url( pencipdc_get_series_image_id( $id ), $thumbnail_load ); ?>"></a>
            </div>
        </div>
	<?php endif; ?>
    <div class="pencipdc-content">
        <div class="pencipdc-heading">
            <h2 class="pencipdc-title">
                <a title="<?php echo wp_strip_all_tags( $title ); ?>"
                   href="<?php echo esc_url( $link ); ?>"><?php echo wp_strip_all_tags( $title ); ?></a>
            </h2>
        </div>
        <div class="pencipdc-meta grid-post-box-meta">
			<?php if ( get_theme_mod( 'pencipodcast_single_show_podcast_total_episode', true ) ): ?>
                <span class="pencipdc-meta-episode"><?php echo $total; ?></span>
			<?php endif; ?>
			<?php if ( get_theme_mod( 'pencipodcast_single_show_podcast_author', true ) ):
				$author_id = pencipdc_get_podcast_author( $id );
				?>
                <span class="pencipdc-meta-author"><a class="author-url url fn n"
                                                      href="<?php echo get_author_posts_url( $author_id ); ?>"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></a></span>
			<?php endif; ?>
        </div>
		<?php if ( $term->description && get_theme_mod( 'pencipodcast_single_enable_post_excerpt', true ) ): ?>
            <div class="pencipdc-meta-desc">
                <p><?php echo penci_trim_excerpt( $term->description, get_theme_mod( 'pencipodcast_single_excerpt_length', 20 ) ); ?></p>
            </div>
		<?php endif; ?>
		<?php echo pencipdc_podcast_add_media_menu( $id, 'podcast' ); ?>
    </div>
</div>