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
 * Class to implement Text To Speech WPBakery Element.
 *
 * @since 3.0.0
 *
 **/
final class WPBakery {

	/**
	 * The one true Text To Speech WPBakery.
	 *
	 * @var Helper
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Text To Speech WPBakery instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function __construct() {

		/** Check if WPBakery VC is installed */
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		/** Load WPBakery VC elements. */
		add_action( 'vc_before_init', [ $this, 'load_elements' ] );

	}


	/**
	 * Load all available VC Elements.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function load_elements() {

		/** Load VC Elements, file must ends by ".WPBakery.php" */
		$path = PenciTextToSpeech::$path . 'inc/PenciDesign/WPBakery/';
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $filename ) {

			if ( substr( $filename, - 13 ) === ".wpbakery.php" ) {

				/** @noinspection PhpIncludeInspection */
				require_once $filename;

			}

		}

	}

	/**
	 * Main Text To Speech WPBakery Instance.
	 *
	 * Insures that only one instance ofText To Speech WPBakery exists in memory at any one time.
	 *
	 * @static
	 * @return Helper
	 **@since 3.0.0
	 *
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class Text To Speech WPBakery.
