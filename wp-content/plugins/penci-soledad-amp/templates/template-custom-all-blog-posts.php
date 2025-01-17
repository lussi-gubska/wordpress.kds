<!doctype html>
<html ⚡ <?php echo Penci_AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ); ?>>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<?php do_action( 'penci_amp_post_template_head', $this ); ?>
	<?php do_action( 'amp_post_template_head', $this ); ?>

	<style amp-custom>
		<?php $this->load_parts( array( 'style' ) ); ?>
		<?php do_action( 'penci_amp_post_template_css', $this ); ?>
		<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>

<?php $sticky_header = penci_amp_get_setting( 'penci_amp_sticky_header' ) ? ' sticky-header' : ''; ?>
<body class="<?php echo join( ' ', get_body_class( 'penci-amp-body' . $sticky_header ) ); ?>">
<?php do_action( 'penci_amp_after_body_tag', $this ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>
<div class="penci-amp-wrapper">
	<?php $this->load_parts( array( 'header-bar' ) ); ?>
	<div class="wrap">
		<article class="amp-wp-article">
			<header class="amp-wp-article-header">
				<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>
			</header>
		</article>
		<?php
		$archive_layout = penci_amp_get_setting( 'penci_amp_archive_listing' );
		$archive_layout = $archive_layout ? $archive_layout : 'listing-1';

		$paged = max( get_query_var( 'paged' ), get_query_var( 'page' ), 1 );
		$args = array( 'post_type' => 'post', 'paged' => $paged );
		$query_custom = new WP_Query( $args );
		if ( $query_custom->have_posts() ) :
			$slider_i = 0;
			echo '<div class="penci-archive__list_posts ' . $archive_layout . ' penci_amp_archive_listing">';
			while ( $query_custom->have_posts() ) : $query_custom->the_post(); $slider_i ++; ?>
				<article <?php post_class( 'penci-post-item penci-post-item-' . $slider_i  ); ?>>
					<div class="article_content">
						<div class="entry-media">
							<a class="penci-link-post" href="<?php the_permalink(); ?>"><?php penci_amp_post_thumbnail( array( 'post'   => get_the_ID() ,'size'   => 'penci-thumb' ) ); ?></a>
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
		endif;
		wp_reset_postdata();

		penci_amp_custom_pagination_numbers($query_custom);
		?>
		<?php $this->load_parts( array( 'footer' ) ); ?>
	</div>
	<?php
	do_action( 'penci_amp_post_template_footer', $this );
	do_action( 'amp_post_template_footer', $this );
	?>
</div>
</body>
</html>
