<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/*
 * Remove gutenberg styles
 * Priority set to 100 to make sure it running after enqueue wp-block-library style
 */
function penci_disable_gutenberg_styles() {
    if( get_theme_mod( 'penci_speed_remove_gutenbergcss' ) ) {
        wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
    }
    
}
add_action( 'wp_print_styles', 'penci_disable_gutenberg_styles', 100 );

/*
 * Function cover string to array from inputs
 */
function penci_speed_strto_array( $string ){
	$string_replace = str_replace( ' ', '', $string );
	if( ! $string_replace ){
		return array();
	}
	$return_array = explode( ',', $string_replace );
	
	return $return_array;
}
