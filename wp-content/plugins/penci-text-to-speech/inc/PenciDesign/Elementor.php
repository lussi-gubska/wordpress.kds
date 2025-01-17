<?php

namespace PenciDesign;

use PenciDesign\PenciTextToSpeech;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class to implement Text To Speech Elementor Widget.
 *
 * @since 3.0.0
 *
 **/
final class Elementor {

	/**
	 * The one true Elementor.
	 *
	 * @var Elementor
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Elementor instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function __construct() {

		/** Check for basic requirements. */
		$this->initialization();

	}


	/**
	 * The init process check for basic requirements and then then run the plugin logic.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function initialization() {

		/** Check if Elementor installed and activated. */
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		/** Register custom widgets. */
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

	}

	/**
	 * Register new Elementor widgets.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function register_widgets() {

		/** Load and register Elementor widgets. */
		$path = PenciTextToSpeech::$path . 'inc/PenciDesign/Elementor/';
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $filename ) {

			if ( substr( $filename, - 14 ) === '.elementor.php' ) {

				/** @noinspection PhpIncludeInspection */
				require_once $filename;

				/** Prepare class name from file. */
				$widget_class = $filename->getBasename( '.php' );
				$widget_class = '\\' . str_replace( '.', '_', $widget_class );

				/** @noinspection PhpFullyQualifiedNameUsageInspection */
				\Elementor\Plugin::instance()->widgets_manager->register( new $widget_class() );

			}

		}

	}

	/**
	 * Main Elementor Instance.
	 *
	 * Insures that only one instance of Elementor exists in memory at any one time.
	 *
	 * @static
	 * @return Elementor
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class Elementor.