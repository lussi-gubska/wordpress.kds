<?php
/* ------------------------------------------------------- */
/* Date
/* ------------------------------------------------------- */
if ( ! function_exists( 'penci_penci_date_shortcode' ) ) {
	function penci_penci_date_shortcode( $atts, $content ) {
		extract( shortcode_atts( array(
			'format' => 'l, F j Y'
		), $atts ) );

		$return = '';
		if ( ! $format ): $format = 'l, F j Y'; endif;

		if ( function_exists( 'wp_date' ) ) {
			if ( get_theme_mod( 'penci_time_sync' ) ) {
				$return = '<span data-format="' . $format . '" class="penci-dtf-real">' . wp_date( $format ) . '</span>';
			} else {
				$return = wp_date( $format );
			}
		}

		return $return;
	}
}