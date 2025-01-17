<?php

class Penci_AMP_Link_Sanitizer {


	public $excl_pid = array();

	public function __construct() {

		add_action( 'wp', array( $this, 'wp_hook' ) );
	}

	public function wp_hook( $wp ) {
		if ( empty( $wp->query_vars['amp'] ) ) {
			return;
		}

		$this->replace_links_to_amp();
	}

	public function replace_links_to_amp() {

		add_filter( 'nav_menu_link_attributes', array( $this, '__href' ) );
		add_filter( 'the_content', array( $this, '__content_links' ) );
		add_filter( 'author_link', array( $this, '__pre_url' ) );
		add_filter( 'term_link', array( $this, '__pre_url' ) );

		add_filter( 'post_link', array( $this, '__pre_post_link' ), 20, 2 );
		add_filter( 'page_link', array( $this, '__pre_post_link' ), 20, 2 );
		add_filter( 'attachment_link', array( $this, '__pre_url' ) );
		add_filter( 'post_type_link', array( $this, '__pre_url' ) );
	}


	public function __href( $attr ) {
		if ( ! isset( $attr['href'] ) ) {
			return $attr;
		}

		$attr['href'] = $this->__pre_url( $attr['href'] );

		return $attr;
	}


	public function __content_links( $content ) {

		return preg_replace_callback( $this->pattern_content_link(), array( $this, '_preg_callback' ), $content );
	}


	public function _preg_callback( $match ) {

		$match_1 = isset( $match[1] ) ? $match[1] : '';
		$match_2 = isset( $match[2] ) ? $match[2] : '';
		$match_3 = isset( $match[3] ) ? $match[3] : '';
		$match_4 = isset( $match[4] ) ? $match[4] : '';
		$link    = empty( $match_4 ) ? $match_3 : $match_4;

		$output = sprintf( '<a %1$shref=%2$s%3$s%2$s', $match_1, $match_2, esc_attr( $this->__pre_url( $link ) ) );

		return $output;
	}


	public static function __pre_url( $link ) {

		$url_format = penci_amp_get_setting( 'penci_amp_url_format' );

		if ( 'simple-point' == $url_format ) {
			return add_query_arg( 'amp', '1', $link );
		}

		if ( false !== strpos( $link, '/' . PENCI_STARTPOINT ) ) {
			return $link;
		}

		$list_http = self::get_list_http();

		$_site_url = site_url();

		$use_site_address_url = get_theme_mod( 'penci_amp_use_site_address_url' );
		if ( $use_site_address_url ) {
			$_site_url = home_url();
		}

		$site_domain_prefix = str_replace( $list_http, '', $_site_url );


		$site_domain_prefix = rtrim( $site_domain_prefix, '/' );

		$preg_match = preg_match( '#^https?://w*\.?' . preg_quote( $site_domain_prefix, '#' ) . '/?([^/]*)/?(.*?)$#', $link, $matched );
		if ( ! $preg_match ) {
			return $link;
		}


		$exclude_subdir = (array) apply_filters( 'penci_amp_pre_exclude_subdir', array() );

		$matched_1 = $first_dir = isset( $matched[1] ) ? $matched[1] : '';

		if ( in_array( $matched_1, $exclude_subdir ) ) {
			$first_dir = isset( $matched[2] ) ? $matched[2] : '';
		}

		if ( $first_dir !== PENCI_STARTPOINT ) {
			$before_amp = '';

			if ( $matched_1 !== 'wp-content' ) {

				$path = '/';

				if ( $matched_1 ) {

					$matched[0] = '';
					if ( in_array( $matched_1, $exclude_subdir ) ) {
						$before_amp = $matched_1;
						$matched[1] = '';
					}

					$path = implode( '/', array_filter( $matched ) );
				}

				if ( 'end-point' == $url_format ) {
					return penci_amp_get_site_url_end_point( $path, $before_amp );
				} else {
					return penci_amp_get_site_url( $path, $before_amp );
				}
			}
		}

		return $link;
	}

	public static function __pre_url_off( $link ) {

		if ( false !== strpos( $link, PENCI_STARTPOINT ) ) {
			$start_point = PENCI_STARTPOINT;
			$get_last    = substr( $link, - 1 );
			if ( '/' == $get_last ) {
				$start_point = PENCI_STARTPOINT . '/';
			}

			$link = str_replace( $start_point, '', $link );
		}

		/* Remove duplicate // on the canonical URL */
		$link = str_replace( ':/', '://', trim( preg_replace( '/\/+/', '/', $link ), '/' ) );

		return $link;
	}


	public function __pre_post_link( $link, $post ) {

		if ( isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
		}


		if ( isset( $this->excl_pid[ $post_id ] ) ) {
			return $link;
		}

		return self::__pre_url( $link );
	}

	public static function get_list_http() {
		return array( 'http://www.', 'https://www.', 'http://', 'https://' );
	}

	public function pattern_content_link() {
		return "'<\s*a\s(.*?)href\s*=\s*([\"\'])?(?(2) (.*?)\\2 | ([^\s\>]+))'isx";
	}
}

new Penci_AMP_Link_Sanitizer;