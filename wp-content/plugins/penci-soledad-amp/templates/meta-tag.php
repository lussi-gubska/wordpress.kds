<?php
$tags_list = get_the_tag_list( '', esc_html__( ' ', 'penci-amp' ) );
if ( ! $tags_list ) {
	return;
}

printf( '<span class="tags-links penci-amp-tags-links">' . esc_html__( '%1$s', 'penci-amp' ) . '</span>', $tags_list ); // WPCS: XSS OK.