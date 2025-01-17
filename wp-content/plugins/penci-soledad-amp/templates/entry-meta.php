<?php
global $post;
if( isset( $post->post_author ) ) {
	$user_id = $post->post_author;
	$byline = sprintf( esc_html_x( '%s', 'post author', 'penci-amp' ), '<span class="author vcard author_name post-author">' . get_the_author_meta( 'display_name', $user_id ) . '</span>' );
	echo '<span class="entry-meta-item meta-none penci-amp-author"><i class="fa fa-user"></i> ' . $byline . '</span>';
}
$get_the_date = get_the_date( DATE_W3C );
$get_the_time = get_the_time( get_option('date_format') );
$get_the_datem = get_the_modified_date( DATE_W3C );
$get_the_timem = get_the_modified_date( get_option('date_format') );

if( ! get_theme_mod( 'penci_show_modified_date' ) || ( $get_the_time == $get_the_timem ) ){
	$render_format = $get_the_date;
	$render_time = $get_the_time;
} else {
	$render_format = $get_the_datem;
	$render_time = $get_the_timem;
}

$time_string = '<time class="entry-date post-date published" datetime="'. $render_format .'">'. $render_time .'</time>';

$posted_on = sprintf(
	esc_html_x( '%s', 'post date', 'penci-amp' ),
	'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
);

echo '<span class="entry-meta-item penci-posted-on"><i class="fa fa-clock-o"></i>' . $posted_on . '</span>'; // WPCS: XSS OK.

$output_comment = '<span class="entry-meta-item meta-none penci-comment-count">';
$output_comment .= '<a href="' . esc_url( get_comments_link() ) . '"><i class="fa fa-comment-o"></i>';
$output_comment .= get_comments_number_text( esc_html__( '0', 'penci-amp' ), esc_html__( '1', 'penci-amp' ), '%' . esc_html__( '', 'penci-amp' ) );
$output_comment .= '</a></span>';

echo $output_comment;

if( function_exists( 'penci_get_post_views' ) && function_exists( 'penci_get_setting' ) ){
	echo '<span class="entry-meta-item meta-none penci-amp-pviews"><i class="fa fa-eye"></i>' . penci_get_post_views( get_the_ID() ) . ' ' . penci_get_setting( 'penci_trans_countviews' ) . '</span>';
}