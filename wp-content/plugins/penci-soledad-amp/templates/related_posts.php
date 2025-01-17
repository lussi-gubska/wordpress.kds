<?php
/**
 * Related post template
 * Render list related posts
 */

global $post;

$orig_post = $post;
$numbers_related = 3;

$amp_related_by = get_theme_mod( 'penci_amp_related_by' );
$args = array();

if ( 'tags' == $amp_related_by ) {
	$tags = wp_get_post_tags( $post->ID );
	if ( $tags ) {
		$tag_ids = array();
		foreach ( $tags as $individual_tag ) {
			$tag_ids[] = $individual_tag->term_id;
		}
		$args = array(
			'tag__in'             => $tag_ids,
			'post__not_in'        => array( $post->ID ),
			'posts_per_page'      => $numbers_related,
			'ignore_sticky_posts' => 1,
		);
	}
}else{
	$categories = get_the_category( $post->ID );

	if (  $categories ) {
		$category_ids = array();
		foreach ( $categories as $individual_category ) {
			$category_ids[] = $individual_category->term_id;
		}

		$args = array(
			'category__in'        => $category_ids,
			'post__not_in'        => array( $post->ID ),
			'posts_per_page'      => $numbers_related, // Number of related posts that will be shown.
			'ignore_sticky_posts' => 1,
		);
	}
}


$related_query = new wp_query( $args );

if ( ! $related_query->have_posts() ) {
	return;
}

$post_items = '';
while ( $related_query->have_posts() ) :
	$related_query->the_post();

	$class_post = 'item-related';
	$class_post .= ! has_post_thumbnail() ? ' penci-no-thumb' : '';

	$post_items .= '<div  class="' . join( ' ', get_post_class( $class_post, get_the_ID() ) ) . '">';
	$post_items .= penci_amp_post_thumbnail( array(
		'post'   => get_the_ID(),
		'size'   => 'penci-thumb',
		'before' => '<a class="related-thumb" href="' . get_the_permalink( get_the_ID() ) . '">',
		'after'  => '</a>',
		'echo'   => false,
	) );
	$post_items .= '<h4><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h4>';
	$post_items .= '</div>';
endwhile;

$post = $orig_post;
wp_reset_postdata();

$related_title = penci_amp_get_setting( 'penci_amp_tex_single_related' );

$output = '<div class="penci-post-related">';
$output .= '<div class="post-title-box"><h4 class="post-box-title">' . esc_attr( $related_title ) . '</h4></div>';
$output .= '<div class="post-related_content">';
$output .= $post_items;
$output .= '</div>';
$output .= '</div>';

echo( $output );
