<?php
$featured_args = array(
	'post_type'           => 'post',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => TRUE
);

$featured_slider_query = new WP_Query( $featured_args );
if ( $featured_slider_query->have_posts() ) {
?>
<div class="homepage-slider" >
	<amp-carousel class="amp-slider amp-featured-slider" layout="responsive" type="slides" width="780" height="500" delay="3500">
	<?php
		while ( $featured_slider_query->have_posts() ) {
			$featured_slider_query->the_post();

			$src = penci_amp_post_thumbnail( array(
					'post' => get_the_ID(),
					'size' => 'penci-thumb-760-570',
					'return_url' => true
				)
			);

			echo '<div class="slider-item">';
			echo '<div id="penci-slider-img-holder' . get_the_ID() . '" class="img-holder"></div>';
			echo '<div class="content-holder"><h3><a href="' . get_the_permalink( get_the_ID() ) . '">' . get_the_title( get_the_ID() ) . '</a></h3></div>';
			echo '</div>';
		}
	?>
	</amp-carousel>
</div>
<?php
	wp_reset_postdata();
}
?>