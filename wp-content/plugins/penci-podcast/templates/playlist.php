<?php
if ( empty( $args['id'] ) ) {
	return false;
}
$id            = $args['id'];
$class         = $args['class'];
$episode_items = new WP_Query( [
	'post_type'      => 'podcast',
	'posts_per_page' => $args['num'],
	'tax_query'      => [
		[
			'taxonomy' => 'podcast-series',
			'terms'    => $id,
		]
	]
] );
$img_class     = isset( $args['img_pos'] ) && $args['img_pos'] ? 'img-' . $args['img_pos'] : 'img-left';
$img_size      = isset( $args['size'] ) && $args['size'] ? $args['size'] : 'penci-thumb';
?>
<div class="pencipdc-splaylist <?php echo $class; ?>">
    <div class="pencipdc-splaylist-top <?php echo esc_attr( $img_class ); ?>">
		<?php
		$term        = get_term( $id, 'podcast-series' );
		$featured_id = get_option( "pencipdc_series_$id" );
		$author_id   = pencipdc_get_podcast_author( $id );
		?>
		<?php if ( isset( $featured_id['featured_img'] ) && $featured_id['featured_img'] ): ?>
            <div class="pcpd-splaylist-thumb">
                <div class="penci-image-holder penci-lazy"
                     data-bgset="<?php echo wp_get_attachment_image_url( $featured_id['featured_img'][0], $img_size ); ?>"></div>
            </div>
		<?php endif; ?>
        <div class="pcpd-splaylist-content">
            <h3><a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>

            <div class="penci-podcast-series-meta grid-post-box-meta">
				<?php if ( $args['author'] ): ?>
                    <span class="otherl-date-author author-italic author vcard"><?php echo penci_get_setting( 'penci_trans_by' ); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) :
							coauthors_posts_links();
						else: ?>
                            <a class="author-url url fn n"
                               href="<?php echo get_author_posts_url( $author_id ); ?>"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></a>
						<?php endif; ?></span>
				<?php endif; ?>
				<?php if ( $args['episode'] ): ?>
                    <span>
                            <?php echo sprintf( _n( '%s Episode', '%s Episodes', $episode_items->found_posts ), number_format_i18n( $episode_items->found_posts ) ); ?>
                        </span>
				<?php endif; ?>
            </div>
			<?php if ( $args['sub'] ): ?>
                <div class="penci_podcast_post_option">

                    <div class="follow-wrapper"><a
                                href="<?php echo pencipdc_podcast_feed_link( $id, $term->taxonomy ); ?>"><i
                                    class="fa fa-rss"></i><span><?php _e( 'Subscribe', 'penci-podcast' ); ?></span></a>
                    </div>

                </div>
			<?php endif; ?>
        </div>
    </div>
	<?php if ( $args['desc'] && $term->description ): ?>
        <div class="post-entry penci-category-description penci-archive-description"><?php echo esc_html( $term->description ); ?></div>
	<?php endif; ?>
    <div class="pencipdc_podcast pencipdc_postblock_episode_detail pencipdc_postblock pencipdc_module_hook">
        <ul class="pencipdc_block_container penci-wrapper-data penci-grid">
			<?php
			if ( $episode_items->have_posts() ) : while ( $episode_items->have_posts() ) : $episode_items->the_post();
				load_template( PENCI_PODCAST_DIR . 'templates/loop/item-list.php', false );
			endwhile;endif; ?>
        </ul>
    </div>
</div>
