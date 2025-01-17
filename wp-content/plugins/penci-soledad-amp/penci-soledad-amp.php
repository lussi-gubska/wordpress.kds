<?php
/**
 * Plugin Name: Penci Soledad AMP
 * Description: Add AMP support to your WordPress site.
 * Plugin URI: http://pencidesign.com/
 * Author: PenciDesign
 * Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
 * Version: 5.3
 * Text Domain: penci-amp
 * Domain Path: /languages/
 */

/**
 * Check is amp with is_penci_amp()
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PENCI_AMP_FILE', __FILE__ );
define( 'PENCI_AMP_DIR', dirname( __FILE__ ) );
define( 'PENCI_AMP_VERSION', '5.3' );
define( 'PENCI_STARTPOINT', 'amp' );

if( ! defined( 'AMP__FILE__' ) ){
	define( 'AMP__FILE__', __FILE__ );
}
if( ! defined( 'AMP__DIR__' ) ){
	define( 'AMP__DIR__', dirname( __FILE__ ) );
}

if( ! defined( 'AMP__VERSION' ) ){
	define( 'AMP__VERSION', '5.3' );
}

/* Create a cookie for detect view desktop or not */
$destop_view = isset( $_GET['desktop_view'] ) ? $_GET['desktop_view'] : '';
if( 'show' == $destop_view ){
	setcookie( 'amp_desktop_view' , 'yes', time() + (86400 * 1) ); /* Cookie available for one day */
} else if( 'no' == $destop_view ) {
	setcookie( 'amp_desktop_view' , '', time() - 3600 );  /* Unset cookie */
}

// Load file
require_once( PENCI_AMP_DIR . '/back-compat/back-compat.php' );
require_once( PENCI_AMP_DIR . '/includes/amp-helper-functions.php' );
require_once( PENCI_AMP_DIR . '/includes/admin/functions.php' );
require_once( PENCI_AMP_DIR . '/includes/class-amp-product.php' );
require_once( PENCI_AMP_DIR . '/includes/class-amp-menu-walker.php' );
require_once( PENCI_AMP_DIR . '/includes/class-amp-custom_rewrite-rules.php' );
require_once( PENCI_AMP_DIR . '/includes/class-amp-compatibility.php' );
require_once( PENCI_AMP_DIR . '/includes/amp-metabox.php' );
require_once( PENCI_AMP_DIR . '/includes/class-yoastseo-amp-frontend.php' );
require_once( PENCI_AMP_DIR . '/includes/json-schema.php' );

require_once( PENCI_AMP_DIR . '/includes/sanitizers/class-amp-link-sanitizer.php' );
require_once( PENCI_AMP_DIR . '/includes/settings/default-setting.php' );
require_once( PENCI_AMP_DIR . '/includes/settings/class-amp-customizer-settings.php' );
require_once( PENCI_AMP_DIR . '/includes/settings/class-amp-customizer-design-settings.php' );

register_activation_hook( __FILE__, 'penci_amp_activate' );
if ( ! function_exists( 'penci_amp_activate' ) ) {
	function penci_amp_activate() {
		if ( ! did_action( 'penci_amp_init' ) ) {
			penci_amp_init();
		}
		flush_rewrite_rules();
	}
}

register_deactivation_hook( __FILE__, 'penci_amp_deactivate' );

if ( ! function_exists( 'penci_amp_deactivate' ) ) {
	function penci_amp_deactivate() {
		global $wp_rewrite;
		foreach ( $wp_rewrite->endpoints as $index => $endpoint ) {
			if ( PENCI_AMP_QUERY_VAR === $endpoint[1] ) {
				unset( $wp_rewrite->endpoints[ $index ] );
				break;
			}
		}

		flush_rewrite_rules();
	}
}

add_action( 'init', 'penci_amp_init' );

if ( ! function_exists( 'penci_amp_init' ) ) {
	function penci_amp_init() {
		if ( false === apply_filters( 'penci_amp_is_enabled', true ) ) {
			return;
		}

		define( 'PENCI_AMP_QUERY_VAR', apply_filters( 'penci_amp_query_var', PENCI_STARTPOINT ) );
		define( 'AMP_QUERY_VAR', apply_filters( 'penci_amp_query_var', PENCI_STARTPOINT ) );

		do_action( 'penci_amp_init' );
		do_action( 'amp_init' );

		load_plugin_textdomain( 'penci-amp', false, plugin_basename( PENCI_AMP_DIR ) . '/languages' );
		register_nav_menu( 'penci-amp-sidebar-nav', esc_html__( 'AMP Navigation', 'penci-amp' ) );

		// Home page
		add_rewrite_rule( PENCI_AMP_QUERY_VAR . '/?$', "index.php?amp=index", 'top' );

		$GLOBALS['Penci_AMP_Custom_Rewrite_Rules'] = new Penci_AMP_Custom_Rewrite_Rules();
		global $Penci_AMP_Custom_Rewrite_Rules;
		$Penci_AMP_Custom_Rewrite_Rules->add_startpoint( PENCI_STARTPOINT, EP_ALL, $query_var = true );
		$Penci_AMP_Custom_Rewrite_Rules->add_endpoint( PENCI_STARTPOINT, EP_ALL, $query_var = true );

		add_rewrite_endpoint( PENCI_AMP_QUERY_VAR, EP_PERMALINK );
		add_post_type_support( 'post', PENCI_AMP_QUERY_VAR );
		add_post_type_support( 'page', PENCI_AMP_QUERY_VAR );
		add_post_type_support( 'product', PENCI_AMP_QUERY_VAR );
		add_post_type_support( 'attachment', PENCI_AMP_QUERY_VAR );

		add_rewrite_rule( PENCI_AMP_QUERY_VAR . '/?$', "index.php?amp=index", 'top' );

		add_filter( 'request', 'penci_amp_force_query_var_value' );
		add_action( 'wp', 'penci_amp_maybe_add_actions' );

		add_filter( 'old_slug_redirect_url', 'penci_amp_redirect_old_slug_to_new_url' );

		if ( class_exists( 'Jetpack' ) && ! ( defined( 'IS_WPCOM' ) && IS_WPCOM ) ) {
			require_once( PENCI_AMP_DIR . '/jetpack-helper.php' );
		}

		add_action( 'template_redirect', 'penci_amp_auto_redirect_to_amp', 1 );
	}
}

function penci_amp_auto_redirect_to_amp() {
	$is_penci_amp = is_penci_amp();

	if (  $is_penci_amp ) {
		return;
	}

	$auto_redirect = get_theme_mod( 'penci_amp_mobile_version' );
	if( ! $auto_redirect || isset( $_COOKIE['amp_desktop_view'] ) ) {
		return;
	}

	if ( ! empty( $_GET['penciamp-skip-redirect'] ) || ! empty( $_COOKIE['penciamp-skip-redirect'] ) ) {
		if ( ! isset( $_COOKIE['penciamp-skip-redirect'] ) ) {
			setcookie( 'penciamp-skip-redirect', true, time() + DAY_IN_SECONDS, '/' );
		}

		return;
	} else {
		if ( isset( $_COOKIE['penciamp-skip-redirect'] ) ) {
			unset( $_COOKIE['penciamp-skip-redirect'] );
		}
	}
	if ( wp_is_mobile() ) {
		$requested_url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$requested_url = is_ssl() ? 'https://' : 'http://';
			$requested_url .= $_SERVER['HTTP_HOST'];
			$requested_url .= $_SERVER['REQUEST_URI'];
		}
		$amp_permalink = Penci_AMP_Link_Sanitizer::__pre_url( $requested_url );
		if ( $requested_url && $amp_permalink && $amp_permalink !== $requested_url ) {
			wp_redirect( $amp_permalink );
			exit;
		}
	}
}

add_action( 'request', 'penci_fix_search' );

if ( ! function_exists( 'penci_fix_search' ) ) {
	function penci_fix_search( $query ) {
		if ( ! empty( $query['s'] ) && ! empty( $query['amp'] ) ) {
			$query['post_type'] = array( 'post' );
		}

		return $query;
	}
}

if ( ! function_exists( 'penci_amp_force_query_var_value' ) ) {
	function penci_amp_force_query_var_value( $query_vars ) {
		if ( isset( $query_vars[ PENCI_AMP_QUERY_VAR ] ) && '' === $query_vars[ PENCI_AMP_QUERY_VAR ] ) {
			$query_vars[ PENCI_AMP_QUERY_VAR ] = 1;
		}

		return $query_vars;
	}
}

if ( ! function_exists( 'penci_amp_maybe_add_actions' ) ) {
	function penci_amp_maybe_add_actions() {

		$is_penci_amp_endpoint = is_penci_amp_endpoint();

		if ( is_singular() && is_page() && is_attachment() && ! is_feed() ) {

			global $wp_query;
			$post = $wp_query->post;

			$supports = penci_post_supports_amp( $post );

			if ( ! $supports ) {
				if ( $is_penci_amp_endpoint ) {
					wp_safe_redirect( get_permalink( $post->ID ) );
					exit;
				}

				return;
			}
		}

		if ( $is_penci_amp_endpoint ) {
			penci_amp_prepare_render();
		} else {
			penci_amp_add_frontend_actions();
		}
	}
}


if ( ! function_exists( 'penci_amp_load_classes' ) ) {
	function penci_amp_load_classes() {
		require_once( PENCI_AMP_DIR . '/includes/class-amp-post-template.php' );
	}
}

if ( ! function_exists( 'penci_amp_add_frontend_actions' ) ) {
	function penci_amp_add_frontend_actions() {
		require_once( PENCI_AMP_DIR . '/includes/amp-frontend-actions.php' );
	}
}


if ( ! function_exists( 'penci_amp_add_post_template_actions' ) ) {
	function penci_amp_add_post_template_actions() {
		require_once( PENCI_AMP_DIR . '/includes/amp-post-template-actions.php' );
		require_once( PENCI_AMP_DIR . '/includes/amp-post-template-functions.php' );
		penci_amp_post_template_init_hooks();
	}
}


if ( ! function_exists( 'penci_amp_prepare_render' ) ) {
	function penci_amp_prepare_render() {
		add_action( 'template_redirect', 'penci_amp_render' );
	}
}

if ( ! function_exists( 'penci_amp_render' ) ) {
	function penci_amp_render() {
		
		if( function_exists( 'is_woocommerce' ) && is_woocommerce() ){
			return;
		}
		
		if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
			return;
		}
		
		if( function_exists( 'is_buddypress' ) && is_buddypress() ){
			return;
		}
		
		if( get_theme_mod( 'penciamp_dison_home' ) && ( is_home() || is_front_page() ) ){
			return;
		}
		if( get_theme_mod( 'penciamp_dison_page' ) && is_page() ){
			return;
		}
		if( get_theme_mod( 'penciamp_dison_post' ) && is_single() ){
			return;
		}
		if( get_theme_mod( 'penciamp_dison_cat' ) && is_category() ){
			return;
		}
		if( get_theme_mod( 'penciamp_dison_tag' ) && is_tag() ){
			return;
		}
		if( get_theme_mod( 'penciamp_dison_arch' ) && is_archive() ){
			return;
		}
		
		$post_id = get_queried_object_id();

		$dis_amp_onpost = get_post_meta( $post_id, 'penci_dis_amp_onpost', true );

		if ( is_archive() ) {
			$dis_amp_onpost = get_term_meta( $post_id, 'penci_dis_amp_onpost', true );
		}

		if ( $dis_amp_onpost ) {
			return;
		}
		
		
		penci_amp_render_post( $post_id );
		exit;
	}
}

if( ! function_exists( 'penci_is_disable_amp_option' ) ) {
	function penci_is_disable_amp_option() {
		$return = false;
		if( function_exists( 'is_woocommerce' ) && is_woocommerce() ){
			$return = true;
		}
		
		if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
			$return = true;
		}
		
		if( function_exists( 'is_buddypress' ) && is_buddypress() ){
			$return = true;
		}
		
		if( get_theme_mod( 'penciamp_dison_home' ) && ( is_home() || is_front_page() ) ){
			$return = true;
		}
		if( get_theme_mod( 'penciamp_dison_page' ) && is_page() ){
			$return = true;
		}
		if( get_theme_mod( 'penciamp_dison_post' ) && is_single() ){
			$return = true;
		}
		if( get_theme_mod( 'penciamp_dison_cat' ) && is_category() ){
			$return = true;
		}
		if( get_theme_mod( 'penciamp_dison_tag' ) && is_tag() ){
			$return = true;
		}
		if( get_theme_mod( 'penciamp_dison_arch' ) && is_archive() ){
			$return = true;
		}
		
		$post_id = get_queried_object_id();
		if( $post_id ){
			$disable_amp = get_post_meta( $post_id, 'penci_dis_amp_onpost', true );

			if ( is_archive() ) {
				$disable_amp = get_term_meta( $post_id, 'penci_dis_amp_onpost', true );
			}
			
			if( $disable_amp ){
				$return = true;
			}
		}
		
		return $return;
	}
}

if ( ! function_exists( 'penci_amp_render_post' ) ) {
	function penci_amp_render_post( $post_id ) {

		$post = get_post( $post_id );
		if ( ! $post && is_singular() ) {
			return;
		}

		penci_amp_load_classes();

		do_action( 'pre_penci_amp_render_post', $post_id );

		penci_amp_add_post_template_actions();
		$template = new Penci_AMP_Post_Template( $post_id );
		$include  = penci_amp_template_loader();

		$template->load( $include );
	}
}

if ( ! function_exists( 'penci_amp_template_loader' ) ) {
	function penci_amp_template_loader() {

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) :
			if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ):
				$template = 'woocommerce';
			elseif ( is_singular( 'product' ) ):
				$template = 'single-product';
			endif;
		elseif ( is_404() ) :
			$template = 'page404';
		elseif ( is_search() ) :
			$template = 'search';
		elseif ( is_front_page() || is_home() || penci_amp_polylang_front_page() ) :
			$template = 'index';
		elseif ( is_post_type_archive() || is_tax() || is_category() || is_tag() || is_author() || is_date() || is_archive() || is_paged() ) :
			$template = 'archive';

			if( is_page_template( 'template-custom-all-blog-posts.php' ) ):
				$template = 'template-custom-all-blog-posts';
			endif;
		elseif ( is_attachment() || is_singular() || is_single() ) :
			$template = 'single';

			if( is_page_template( 'template-custom-all-blog-posts.php' ) ):
				$template = 'template-custom-all-blog-posts';
			endif;
		elseif ( is_page() ) :
			$template = 'page';

			if( is_page_template( 'template-custom-all-blog-posts.php' ) ):
				$template = 'template-custom-all-blog-posts';
			endif;

		else :
			$template = 'index';
		endif;

		return $template;

	}
}

// check polylang frontpage
if ( ! function_exists( 'penci_amp_polylang_front_page' ) ) {
	function penci_amp_polylang_front_page() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {

			global $polylang;
			$front_page_id = get_option( 'page_on_front' );
			$page_id       = $polylang->curlang->page_on_front;

			if ( function_exists( 'pll_get_post' ) && $page_id == pll_get_post( $front_page_id ) && ! is_page() && ! is_single() && ! is_archive() && ! is_search() ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( '_penci_amp_bootstrap_customizer' ) ) {
	function _penci_amp_bootstrap_customizer() {
		$penci_amp_customizer_enabled = apply_filters( 'penci_amp_customizer_is_enabled', true );

		if ( true === $penci_amp_customizer_enabled ) {
			penci_amp_init_customizer();
		}
	}
}

add_action( 'plugins_loaded', '_penci_amp_bootstrap_customizer', 9 );

if ( ! function_exists( 'penci_amp_redirect_old_slug_to_new_url' ) ) {
	function penci_amp_redirect_old_slug_to_new_url( $link ) {

		if ( is_penci_amp_endpoint() ) {
			$link = trailingslashit( trailingslashit( $link ) . PENCI_AMP_QUERY_VAR );
		}

		return $link;
	}
}

/* Filter to remove the canonical tag auto added from Rank Math plugin */
add_filter( 'rank_math/frontend/canonical', 'penci_remove_canonical_from_rank_math' );
function penci_remove_canonical_from_rank_math( $canonical ){
	if( is_penci_amp() ){
		return '';
	}
	return $canonical;
}

/* Filter to remove canonical from Yoast SEO plugin */
add_filter( 'wpseo_canonical', 'penci_remove_canonical_from_yoast_seo' );
function penci_remove_canonical_from_yoast_seo( $canonical ){
	if( is_penci_amp() ){
		return false;
	}
	return $canonical;
}

/* Filter to remove canonical from All in One SEO Pack plugin */
add_filter('aioseo_canonical_url','penci_remove_canonical_from_aioseop', 10, 1);
function penci_remove_canonical_from_aioseop( $url ){
	if( is_penci_amp() ){
		return false;
	}
	return $url;
}

/* Disable optimize from WP Rocket for AMP */
if( is_penci_amp() ){
	define( 'DONOTROCKETOPTIMIZE', true );
}