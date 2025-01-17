<?php
// Callbacks for adding AMP-related things to the main theme

add_action( 'wp_head', 'penci_amp_frontend_add_canonical' );
function penci_amp_frontend_add_canonical() {
	if ( ! penci_is_disable_amp_option() ) {
		global $wp_rewrite;

		if ( false === apply_filters( 'penci_amp_frontend_show_canonical', true ) ) {
			return;
		}

		$canonical_url = penci_amp_get_canonical_url();

		$current_page = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$url_format   = penci_amp_get_setting( 'penci_amp_url_format' );

		if ( 'end-point' == $url_format && $current_page > 1 ) {
			$pagination_base = $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/' . $current_page, 'paged' ) : '?paged=' . $current_page;
			if ( $pagination_base ) {
				$string_end = substr( $pagination_base, - 1 );
				if ( '/' == $string_end ) {
					$pagination_base = substr( $pagination_base, 0, - 1 );
				}
				$canonical_url = str_replace( $pagination_base, PENCI_STARTPOINT . '/' . $pagination_base, $canonical_url );
			}
		}

		$penci_amp_url = Penci_AMP_Link_Sanitizer::__pre_url( $canonical_url );

		if ( 'simple-point' == $url_format ) {
			$penci_amp_url = add_query_arg( 'amp', '1', $canonical_url );
		}

		printf( '<link rel="amphtml" href="%s" />', esc_url( $penci_amp_url ) );
	}
}
