<?php
/*
Plugin Name: Penci Sport
Plugin URI: https://pencidesign.net/
Description: Displays scores and rankings for popular sports/leagues
Version: 1.1
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-sport
*/
define( 'PENCI_SPORT_VERSION', '1.0' );
define( 'PENCI_SPORT_URL', plugin_dir_url( __FILE__ ) );


class Penci_Sport {

	private static $instance;

	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
		require_once 'widgets/fixture.php';
		require_once 'widgets/standing.php';
	}

	public function init() {
		if ( ! self::is_soledad() ) {
			wp_admin_notice( __( 'Penci Sport plugin only working with the Soledad theme.', 'penci-sport' ), [ 'type' => 'error' ] );
			return;
		}
		require_once 'helper.php';
		require_once 'football_api.php';
		

		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'penci_load_scripts' ] );
	}

    public static function penci_load_scripts() {
		wp_enqueue_style( 'penci-sport', PENCI_SPORT_URL . 'assets/penci-sport.css', array(), PENCI_SPORT_VERSION );
	}

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public static function is_soledad() {
		$theme = wp_get_theme();

		if ( is_child_theme() ) {
			$parent_theme = wp_get_theme( $theme->template );
			$name         = $parent_theme->get( 'Name' );
		} else {
			$name = $theme->get( 'Name' );
		}

		return $name == 'soledad';
	}

    public function register_widget( $widgets_manager ) {

		$_elements = [
			'standing',
			'fixture',
		];

		foreach ( $_elements as $aelement ) {
			require_once( __DIR__ . "/elementor/{$aelement}.php" );
			$classname = '\\PenciSportElementor' . ucwords( $aelement );
			$widgets_manager->register( new $classname() );
		}

	}
}


Penci_Sport::getInstance();