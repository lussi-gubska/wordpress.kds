<?php


namespace PenciDesign;

use PenciDesign\PenciTextToSpeech;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class adds admin styles.
 *
 * @since 3.0.0
 *
 **/
final class FrontScripts {

	/**
	 * The one true FrontScripts.
	 *
	 * @var FrontScripts
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new FrontScripts instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add plugin scripts. */
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

	}

	/**
	 * Add plugin scripts.
	 *
	 * @return void
	 * @since   1.0.0
	 **/
	public function enqueue_scripts() {
		wp_register_script( 'penci-texttospeech', PenciTextToSpeech::$url . 'js/text-to-speech' . PenciTextToSpeech::$suffix . '.js', [ 'wp-mediaelement' ], PenciTextToSpeech::$version, true );
		wp_register_script( 'penci-texttospeech-fixed', PenciTextToSpeech::$url . 'js/fixed' . PenciTextToSpeech::$suffix . '.js', [ 'jquery' ], PenciTextToSpeech::$version, true );
		wp_register_script( 'penci-texttospeech-el', PenciTextToSpeech::$url . 'js/elementor' . PenciTextToSpeech::$suffix . '.js', [
			'jquery',
			'wp-mediaelement'
		], PenciTextToSpeech::$version, true );

		if ( in_array( get_theme_mod( 'penci_texttospeech_position' ), [ 'top-fixed', 'bottom-fixed' ] ) ) {
			wp_enqueue_script( 'penci-texttospeech-fixed' );
		}

		/** Remove WP mediaElement if set Default Browser Player. */
		if ( 'style-6' == get_theme_mod( 'penci_texttospeech_style' ) ) {
			return;
		}

		/** Prepare conditions for script enqueue */
		$hide_download    = 'style-6' == get_theme_mod( 'penci_texttospeech_style' ) && in_array( get_theme_mod( 'penci_texttospeech_link', 'none' ), [
				'none',
				'backend'
			] );
		$enable_speeds    = get_theme_mod( 'penci_texttospeech_speed_controls', true );
		$player_is_chrome = 'style-5' == get_theme_mod( 'penci_texttospeech_style' );
		$backend_preload  = 'backend' == get_theme_mod( 'penci_texttospeech_preload' );

		/** Add JS script, for hide download button in the default webkit player */
		if ( $enable_speeds || $hide_download || $player_is_chrome || $backend_preload ) {
			wp_enqueue_script( 'penci-texttospeech' );
		}

		wp_enqueue_script( 'jquery' );

	}

	/**
	 * Main FrontScripts Instance.
	 *
	 * Insures that only one instance of FrontScripts exists in memory at any one time.
	 *
	 * @static
	 * @return FrontScripts
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof FrontScripts ) ) {

			self::$instance = new FrontScripts;

		}

		return self::$instance;

	}

} // End Class FrontScripts.
