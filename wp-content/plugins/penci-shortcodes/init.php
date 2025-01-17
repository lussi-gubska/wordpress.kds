<?php
/*
Plugin Name: Penci Shortcodes & Performance
Plugin URI: http://pencidesign.com/
Description: Shortcodes & Improve Performance Plugin for Soledad theme.
Version: 5.8
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/


define( 'PENCI_SOLEDAD_SHORTCODE_PERFORMANCE', '5.8' );

/* ------------------------------------------------------- */
/* Optimize Speed
/* ------------------------------------------------------- */
include_once( 'optimize/general.php' );
include_once( 'optimize/css.php' );
include_once( 'optimize/javascript.php' );
include_once( 'optimize/html.php' );
include_once( 'pagespeed/bootstrap.php' );

/* ------------------------------------------------------- */
/* Include MCE button
/* ------------------------------------------------------- */
require_once( dirname( __FILE__ ) . '/mce/mce.php' );


/* ------------------------------------------------------- */
/* Remove empty elements
/* ------------------------------------------------------- */
add_filter( 'the_content', 'penci_pre_process_shortcode', 7 );

// Allow Shortcodes in Widgets
add_filter( 'widget_text', 'penci_pre_process_shortcode', 7 );
if ( ! function_exists( 'penci_pre_process_shortcode' ) ) {
	function penci_pre_process_shortcode( $content ) {
		$shortcodes = 'blockquote, columns, penci_video, penci_button, penci_date';
		$shortcodes = explode( ",", $shortcodes );
		$shortcodes = array_map( "trim", $shortcodes );

		global $shortcode_tags;

		// Backup current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		$shortcode_tags      = array();

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( $shortcode, 'penci_' . $shortcode . '_shortcode' );
		}
		// Do the shortcode (only the one above is registered)
		$content = do_shortcode( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}
}

/* ------------------------------------------------------- */
/* Include Shortcode File - Add shortcodes to everywhere use*
/* ------------------------------------------------------- */
$shortcodes = 'blockquote, columns, icon, penci_video, penci_button, penci_date';
$shortcodes = explode( ",", $shortcodes );
$shortcodes = array_map( "trim", $shortcodes );

foreach ( $shortcodes as $short_code ) {
	require_once( dirname( __FILE__ ) . '/inc/' . $short_code . '.php' );
	add_shortcode( $short_code, 'penci_' . $short_code . '_shortcode' );
}

/**
 * Add pencilang shortcode
 * Return language text with current lang
 *
 * @since Soledad v4.0
 */
if ( ! function_exists( 'penci_language' ) ) {
	add_shortcode( 'pencilang', 'penci_language' );
	function penci_language( $langs ) {
		$current_lang = get_locale();
		$current_lang = strtolower( $current_lang );
		if ( array_key_exists( $current_lang, $langs ) && isset( $langs[ $current_lang ] ) ) {
			return $langs[ $current_lang ];
		} elseif ( array_key_exists( 'default', $langs ) ) {
			return $langs['default'];
		}

		return;
	}
}

if ( ! function_exists( 'penci_lazy_toolbar_link' ) ) {
	function penci_lazy_toolbar_link( $wp_admin_bar ) {

		if ( current_user_can( 'manage_options' ) && ( get_theme_mod( 'penci_speed_remove_css' ) || get_theme_mod( 'penci_speed_optimize_css' ) ) ) {
			$btn_title = 'Clear Critical CSS Cache';
			if ( is_multisite() ) {
				$btn_title = 'Clear Critical CSS Cache';

				$args_single = array(
					'id'     => 'pencilazy-clearsinglecache',
					'title'  => 'Clear Critical CSS Cache for This Site',
					'href'   => '?clear_pencilazy_css_single=true',
					'meta'   => array(
						'class' => 'pencilazy-clear-button ' . esc_html( wp_create_nonce( 'penci_speed_delete_cache' ) ),
						'title' => 'Clear Critical CSS Cache for This Site',
					)
				);
				$wp_admin_bar->add_node( $args_single );
			}

			$args = array(
				'id'    => 'pencilazy-clearcache',
				'title' => $btn_title,
				'href'  => '?clear_pencilazy_css=true',
				'meta'  => array(
					'class' => 'pencilazy-clear-button ' . esc_html( wp_create_nonce( 'penci_speed_delete_cache' ) ),
					'title' => $btn_title,
				)
			);
			$wp_admin_bar->add_node( $args );

		}
	}

	add_action( 'admin_bar_menu', 'penci_lazy_toolbar_link', 999 );
}

// Add filter
add_filter( 'hpp_disallow_lazyload', function ( $ok, $tag ) {

	$excludes           = Soledad\PageSpeed\Util\option_to_array( get_theme_mod( 'penci_disable_lazyload_extra' ) );
	$exclude_lazy_array = array(
		'pc-hdbanner3',
		'penci-mainlogo',
		'pc-singlep-img',
		'pc-igrlazy',
		'jetpack_remote_comment',
		'widgets.wp.com',
	);

	if ( get_theme_mod( 'penci_disable_lazyload_iframe' ) ) {
		$exclude_lazy_array[] = '<iframe';
	}

	$exclude_lazy_array = array_merge( $exclude_lazy_array, $excludes );

	if ( get_theme_mod( 'penci_speed_disable_first_screen' ) ) {
		$exclude_lazy_array = array(
			'penci-mainlogo',
			'jetpack_remote_comment',
			'widgets.wp.com',
		);
	}

	//class,src,srcset,.. ->attributes
	foreach ( $exclude_lazy_array as $val1 ) {
		if ( strpos( $tag, $val1 ) !== false ) {
			return 1;
		}
	}

	return $ok;
}, 10, 2 );

add_filter( 'hpp_disallow_lazyload_attr', function ( $ok, $tag ) {
	$excludes           = Soledad\PageSpeed\Util\option_to_array( get_theme_mod( 'penci_disable_lazyload_extra' ) );
	$exclude_lazy_array = array(
		'pc-hdbanner3',
		'penci-mainlogo',
		'pc-singlep-img',
		'pc-igrlazy',
		'jetpack_remote_comment',
		'widgets.wp.com',
	);
	$exclude_lazy_array = array_merge( $exclude_lazy_array, $excludes );
	if ( get_theme_mod( 'penci_speed_disable_first_screen' ) ) {
		$exclude_lazy_array = array(
			'penci-mainlogo',
			'jetpack_remote_comment',
			'widgets.wp.com'
		);
	}

	foreach ( $exclude_lazy_array as $val2 ) {
		if ( strpos( $tag['class'], $val2 ) !== false ) {
			return 1;
		}
	}

	return $ok;
}, 10, 2 );

add_filter( 'hpp_disallow_lazyload', function ( $ok, $tag ) {
	if ( strpos( $tag, 'lazy' ) !== false ) {
		return 1;
	}

	return $ok;
}, 10, 2 );

add_filter( 'penci_disable_youtube_lazy', function ( $ok, $tag ) {
	if ( strpos( $tag, 'autoplay=1' ) !== false ) {
		return 0;
	}

	return $ok;
}, 10, 2 );

add_filter( 'hpp_disallow_lazyload_attr', function ( $ok, $tag ) {
	if ( strpos( $tag['loading'], 'lazy' ) !== false ) {
		return 1;
	}

	return $ok;
}, 10, 2 );


add_filter( 'hpp_allow_lazy_video', function ( $ok, $tag ) {
	if ( strpos( $tag, 'penci-lazy' ) !== false ) {
		return 1;
	}

	return $ok;
}, 10, 2 );

add_action( 'plugins_loaded', function () {
	add_filter( 'customize_loaded_components', function ( $comp ) {
		unset( $comp['widgets'] );
		unset( $comp['nav_menus'] );

		return $comp;
	}, 90 );
	add_filter( 'wp_lazy_loading_enabled', '__return_false' );
}, 90 );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'penci-play-js', plugin_dir_url( __FILE__ ) . 'assets/play.js', [ 'jquery' ], PENCI_SOLEDAD_SHORTCODE_PERFORMANCE, true );
} );
