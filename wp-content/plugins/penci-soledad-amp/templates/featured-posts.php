<?php
$archive_layout = penci_amp_get_setting( ( is_archive() ? 'penci_amp_archive_listing' : 'penci_amp_home_listing' ) );
$archive_layout = $archive_layout ? $archive_layout : 'listing-1';

if ( have_posts() ) {
	$slider_i = 0;

	$show_textlasestp = get_theme_mod( 'penci_amp_home_show_textlasestp' );
	if ( $show_textlasestp && is_home() ) {
		?>
		<div class="penci-fcats-lastpost">
			<div class="post-title-box"><h4 class="post-box-title"><?php echo penci_amp_get_setting( 'penci_amp_latest_posts_text' ); ?></h4></div>
		</div>
		<?php
	}

	echo '<div class="penci-archive__list_posts ' . $archive_layout . ( ( is_archive() ? ' penci_amp_archive_listing' : ' penci_amp_home_listing' ) ) . '">';
	while ( have_posts() ) : the_post();
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
} else {
	if( is_search() ){
		echo '<p class="nopost-found">'. penci_amp_get_setting( 'penci_amp_search_not_found' ) . '</p>';
	} else {
		echo '<p class="nopost-found">'. penci_amp_get_setting( 'penci_amp_nopost_found' ) . '</p>';
	}
}
