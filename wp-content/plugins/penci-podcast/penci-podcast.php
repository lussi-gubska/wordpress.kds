<?php
/*
Plugin Name: Penci Podcast
Plugin URI: https://pencidesign.net/
Description: This plugin enables you to develop a top-notch podcast website with a wide range of features.
Version: 1.4
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-podcast
*/

defined( 'PENCI_PODCAST' ) or define( 'PENCI_PODCAST', 'penci-podcast' );
defined( 'PENCI_PODCAST_VERSION' ) or define( 'PENCI_PODCAST_VERSION', '1.3' );
defined( 'PENCI_PODCAST_URL' ) or define( 'PENCI_PODCAST_URL', plugins_url( PENCI_PODCAST ) );
defined( 'PENCI_PODCAST_FILE' ) or define( 'PENCI_PODCAST_FILE', __FILE__ );
defined( 'PENCI_PODCAST_DIR' ) or define( 'PENCI_PODCAST_DIR', plugin_dir_path( PENCI_PODCAST_FILE ) );

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciPodcastCustomizer::getInstance();
		}
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-podcast', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_podcast_panel'] = array(
			'priority'                        => 30,
			'path'                            => plugin_dir_path( __FILE__ ) . '/customizer/',
			'panel'                           => array(
				'title' => esc_html__( 'Podcast', 'soledad' ),
				'icon'  => 'fas fa-podcast',
			),
			'penci_podcast_general_section'   => array( 'title' => esc_html__( 'General Settings', 'soledad' ) ),
			'penci_podcast_category_section'  => array( 'title' => esc_html__( 'Categories Layout', 'soledad' ) ),
			'penci_podcast_series_section'    => array( 'title' => esc_html__( 'Series Layout', 'soledad' ) ),
			'penci_podcast_colors_section'    => array( 'title' => esc_html__( 'Colors', 'soledad' ) ),
			'penci_podcast_translate_section' => array( 'title' => esc_html__( 'Text Translation', 'soledad' ) ),
		);
		return $options;
	}
);


require_once 'inc/importer/importer.php';
require_once 'inc/init.php';
require_once 'inc/queries.php';
require_once 'inc/helper.php';
require_once 'inc/player.php';
require_once 'inc/metabox.php';
require_once 'inc/shortcodes.php';
require_once 'inc/taxonomy-meta.php';
require_once 'inc/widget.php';

Penci_PodCast_Player::get_instance();
PenciPodCast_Shortcode::get_instance();
