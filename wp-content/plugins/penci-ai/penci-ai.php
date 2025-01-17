<?php
/*
Plugin Name: Penci AI SmartContent Creator
Plugin URI: http://pencidesign.net/
Description: Penci AI SmartContent Creator is a WordPress plugin that uses AI to generate high-quality content for your website. This plugin can assist you in creating articles and blog posts, as well as generating AI-based images. Additionally, it can suggest topic ideas and optimize keywords to save you time and improve the quality of your content. It is a must-have tool for content creation.
Version: 1.6
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define the version and name of the plugin
define( 'PENCI_AI_VERSION', '1.6' );
define( 'PENCI_AI_NAME', 'penci_ai' );

// Define the directory path and URL for the plugin
define( 'PENCI_AI_DIR_PATH', __DIR__ . '/' );
define( 'PENCI_AI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PENCI_AI_DIR_URL', plugin_dir_url( __FILE__ ) );


// Include the file containing the plugin class
require 'vendor/autoload.php';
require 'includes/class-PenciAIContentGenerator.php';

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-ai', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);


/**
 * Function that runs when the plugin is loaded.
 *
 * @since 1.0.0
 */
function penci_ai() {
	// Create an instance of the plugin class and call its run method
	$plugin = new PenciAIContentGenerator\PenciAIContentGenerator();
	$plugin->run();
}

// Run the plugin
penci_ai();

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciAICustomizer::getInstance();
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_ai_panel'] = array(
			'priority'                 => 30,
			'path'                     => PENCI_AI_DIR_PATH . 'customizer/',
			'panel'                    => array(
				'icon'  => 'fas fa-pencil-alt',
				'title' => esc_html__( 'Penci AI SmartContent Creator', 'soledad' ),
			),
			'penci_ai_api_section'     => array( 'title' => esc_html__( 'API Settings', 'soledad' ) ),
			'penci_ai_content_section' => array( 'title' => esc_html__( 'Content', 'soledad' ) ),
			'penci_ai_image_section'   => array( 'title' => esc_html__( 'Images', 'soledad' ) ),
			'penci_ai_general_section' => array( 'title' => esc_html__( 'General', 'soledad' ) ),
		);
		return $options;
	}
);
