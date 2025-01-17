<?php

$prev = '<i class="fa fa-angle-left"></i>' . penci_amp_get_setting( 'penci_amp_prev' );
$next = penci_amp_get_setting( 'penci_amp_next' ) . '<i class="fa fa-angle-right"></i>';

global $wp_query;
$max_num_pages = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : '';

echo '<div class="penci-amp-pagination">';
penci_amp_the_posts_pagination( array(
	'mid_size'           => 0,
	'prev_text'          => $prev,
	'next_text'          => $next,
) );
echo '</div>';