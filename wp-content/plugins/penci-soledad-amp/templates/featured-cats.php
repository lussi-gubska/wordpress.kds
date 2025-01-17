<?php
$archive_layout = penci_amp_get_setting( ( is_archive() ? 'penci_amp_featured_cat_listing' : 'penci_amp_featured_cat_listing' ) );
$archive_layout = $archive_layout ? $archive_layout : 'listing-1';

$featured_cats = get_theme_mod( 'penci_amp_home_featured_cat' );
if ( $featured_cats ) {
	$featured_cats       = str_replace( ' ', '', $featured_cats );
	$featured_categories = explode( ',', $featured_cats );
}

$fea_cat_i = 1;
foreach ( $featured_categories as $fea_cat ) {
	$fea_oj = get_category_by_slug( $fea_cat );

	if ( empty ( $fea_oj ) ) {
		continue;
	}

	$fea_cat_id    = $fea_oj->term_id;
	$fea_cat_name  = $fea_oj->name;
	$cat_meta      = get_option( "category_$fea_cat_id" );

	$numbers_posts = get_theme_mod( 'penci_amp_home_featured_cat_numbers' ) ? get_theme_mod( 'penci_amp_home_featured_cat_numbers' ) : '5';

	$attr           = array(
		'post_type' => 'post',
		'showposts' => $numbers_posts,
		'tax_query' => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $fea_cat
			)
		)
	);
	$fea_query      = new WP_Query( $attr );
	$numers_results = $fea_query->post_count;

	if ( ! $fea_query->have_posts() ) {
		continue;
	}

	$slider_i = 0;



	?>
	<div class="penci-featured-cats-wrap<?php echo ( $numers_results == $slider_i ? ' penci-fcats-after' : ' penci-fcats-' . $fea_cat_i ) ?>">
	<div class="post-title-box"><h4 class="post-box-title"><a href="<?php echo esc_url( get_category_link( $fea_cat_id ) ); ?>"><?php echo sanitize_text_field( $fea_cat_name ); ?></a></h4></div>
	<?php
	echo '<div class="penci-archive__list_posts ' . $archive_layout . ' penci_amp_featured_cat_listing">';

	while ( $fea_query->have_posts() ): $fea_query->the_post();
		$slider_i ++; ?>
		<article <?php post_class( 'penci-post-item penci-post-item-' . $slider_i ); ?>>
			<div class="article_content">
				<div class="entry-media">
					<a class="penci-link-post" href="<?php the_permalink(); ?>"><?php penci_amp_post_thumbnail( array( 'post' => get_the_ID(), 'size' => 'penci-thumb' ) ); ?></a>
				</div>
				<div class="entry-text">
					<h3 class="penci__post-title entry-title">
						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
							<?php the_title() ?>
						</a>
					</h3>
					<div class="entry-meta">
						<?php $this->load_parts( array( 'entry-meta' ) ); ?>
					</div>
					<div class="post-excerpt">
						<?php the_excerpt(); ?>
					</div>
					<a class="post-read-more" href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
						<?php echo penci_amp_get_setting( 'penci_amp_text_readmore' ); ?>
						<i class="fa fa-angle-right"></i>
					</a>
				</div>
			</div>
		</article>
		<?php
	endwhile;
	echo '</div>';
	echo '</div><!-- penci-featured-cats-wrap -->';

	$fea_cat_i ++;

	wp_reset_postdata();
}