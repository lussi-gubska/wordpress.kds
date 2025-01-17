<?php
/*
Plugin Name: Penci Paywall
Plugin URI: https://pencidesign.net/
Description: Member subscription for reading posts in Soledad Theme - WooCommerce plugin required.
Version: 1.7
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-paywall
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PENCI_PAYWALL', '1.7' );
define( 'PENCI_PAYWALL_URL', plugin_dir_url( __FILE__ ) );
define( 'PENCI_PAYWALL_PATH', plugin_dir_path( __FILE__ ) );

require_once 'inc/helper.php';
require_once 'inc/metabox.php';
require_once 'inc/init.php';
require_once 'inc/ajax-handle.php';
require_once 'inc/getpaid.php';

require_once 'inc/content_filter/content-tag.php';
require_once 'inc/content_filter/html-tree-node.php';
require_once 'inc/content_filter/content-filter.php';

require_once 'account/init.php';

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciPaywallCustomizer::getInstance();
		}
		if ( defined( 'WPB_VC_VERSION' ) ) {
			add_action(
				'vc_before_init',
				function () {
					require_once 'builder/jscomposer.php';
				},
				5
			);
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_paywall_panel'] = array(
			'priority'                           => 30,
			'path'                               => plugin_dir_path( __FILE__ ) . '/customizer/',
			'panel'                              => array(
				'title' => esc_html__( 'Content Paywall', 'soledad' ),
				'icon'  => 'fas fa-user-lock',
			),
			'penci_paywall_general_section'      => array( 'title' => esc_html__( 'General', 'soledad' ) ),
			'penci_paywall_advanced_section'     => array( 'title' => esc_html__( 'Advanced Settings', 'soledad' ) ),
			'penci_paywall_translations_section' => array( 'title' => esc_html__( 'Texts Translation', 'soledad' ) ),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-paywall', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

/**
 * Initialize Plugin
 */
PenciPaywall\Init::instance();
