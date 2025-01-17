<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Developed based on https://wordpress.org/plugins/wp-html-compression/ plugin

$penci_html_compression_run = false;


function penci_speed_do_minify_html(){

	if( get_theme_mod( 'penci_speed_html_minify' ) && ! current_user_can( 'manage_options' ) ){

		// Don't run lib for AMP pages
		if( ( function_exists( 'is_penci_amp' ) && is_penci_amp() ) || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
			return;
		}

		// Include libs
		if( ! class_exists( 'HTML_Minify' ) ){
			require_once( 'libs/html-minify.php' );
		}

		global $penci_html_compression_run;

		if ( ! $penci_html_compression_run ){
			$penci_html_compression_run = true;

			// "Humans TXT" plugin support
			$is_humans = ( !function_exists('is_humans') ) ? false : is_humans();

			if ( ! $is_humans && ! is_feed() && ! is_robots() ){
				ob_start('html_minify_buffer');
			}
		}
	}
}

// Hook - don't running hooks when wp_html_compression_start included: https://gist.github.com/sethbergman/d07e879200bef6862131
if ( ! is_admin() && ! function_exists( 'wp_html_compression_start' ) ){
	add_action('template_redirect', 'penci_speed_do_minify_html', -1 );
	add_action('get_header', 'penci_speed_do_minify_html');
}