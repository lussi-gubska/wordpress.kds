<?php
/*
Plugin Name: Penci Frontend Submission
Plugin URI: https://pencidesign.net/
Description: Frontend submit article for Soledad WordPress Theme
Version: 1.6
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-frontend-submission
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PENCI_FRONTEND_SUBMISSION', '1.6' );
define( 'PENCI_FRONTEND_SUBMISSION_URL', plugin_dir_url( __FILE__ ) );
define( 'PENCI_FRONTEND_SUBMISSION_PATH', plugin_dir_path( __FILE__ ) );

require_once 'inc/init.php';
require_once 'inc/section.php';
require_once 'inc/getpaid.php';

add_action(
	'plugins_loaded',
	function () {
		require_once 'inc/post_package.php';
		require_once 'inc/helper.php';

		if ( class_exists( 'WooCommerce' ) ) {
			require_once 'inc/woocommerce.php';
		}

		\PenciFrontendSubmission\AccountPage::getInstance();
		\PenciFrontendSubmission\Session::getInstance();
	}
);

add_action(
	'after_setup_theme',
	function () {
		if ( class_exists( 'WooCommerce' ) ) {
			\PenciFrontendSubmission\WooCommercePackage::getInstance();
		}
	}
);

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciFrontEndSubmissionCustomizer::getInstance();
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

		$options['penci_frontend_submission_panel'] = array(
			'priority'                                   => 30,
			'path'                                       => PENCI_FRONTEND_SUBMISSION_PATH . '/customizer/',
			'panel'                                      => array(
				'icon'        => 'far fa-newspaper',
				'title'       => esc_html__( 'Front End Submission', 'soledad' ),
				'description' => __( 'Please check <a target="_blank" href="https://soledad.pencidesign.net/soledad-document/#text-to-speech">this video tutorial</a> to know how to setup this feature.', 'soledad' ),
			),
			'penci_frontend_submission_general_section'  => array( 'title' => esc_html__( 'General', 'soledad' ) ),
			'penci_frontend_submission_advanced_section' => array( 'title' => esc_html__( 'Advanced Settings', 'soledad' ) ),
			'penci_frontend_submission_translations_section' => array( 'title' => esc_html__( 'Text Translations', 'soledad' ) ),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-frontend-submission', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);
