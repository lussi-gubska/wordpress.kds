<?php
/*
Plugin Name: Penci Google Analytics Views
Plugin URI: https://pencidesign.net/
Description: Sync pageview data from Google Analytics to your WordPress Database, enabling you to sort posts, view pageview data in the WordPress Dashboard, and output pageviews to your visitors.
Version: 1.1
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-frontend-submission
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciGaViewsCustomizer::getInstance();
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['pencidesign_general_gviews_section'] = array(
			'priority'                                   => 30,
			'path'                                       => plugin_dir_path( __FILE__ ) . '/customizer/',
			'pencidesign_general_gviews_section'  => array( 'title' => esc_html__( 'Google Analytics Page Views', 'soledad' ),'icon'        => 'far fa-eye', ),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-ga-views', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

require_once 'api.php';