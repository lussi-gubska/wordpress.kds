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
final class FrontStyles {

	/**
	 * The one true FrontStyles.
	 *
	 * @var FrontStyles
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new FrontStyles instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add plugin styles. */
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		add_action( 'soledad_theme/custom_css', [ $this, 'front_custom_style' ] );

		/** Remove WP mediaElement if set Default Browser Player */
		if ( 'style-6' == get_theme_mod( 'penci_texttospeech_style' ) ) {

			/** Remove media element styles and scripts. */
			add_filter( 'wp_audio_shortcode_library', '__return_empty_string', 11 );

		}

	}

	public function front_custom_style() {
		$bgcolor = get_theme_mod( 'penci_texttospeech_bgcolor' );
		if ( $bgcolor ) {
			echo '.penci-texttospeech-wrapper{--pcaccent-cl:' . $bgcolor . '}';
		}

		$spacings = [
			'penci_texttospeech_mb'  => '.penci-texttospeech-wrapper.customizer{margin-bottom:{{VALUE}}px !important}',
			'penci_texttospeech_mt'  => '.penci-texttospeech-wrapper.customizer{margin-top:{{VALUE}}px  !important}',
			'penci_texttospeech_mbm' => '@media only screen and (max-width: 767px){.penci-texttospeech-wrapper.customizer{margin-bottom:{{VALUE}}px  !important}}',
			'penci_texttospeech_mtm' => '@media only screen and (max-width: 767px){.penci-texttospeech-wrapper.customizer{margin-top:{{VALUE}}px  !important}}',
		];

		foreach ( $spacings as $spacing => $css ) {
			$value = get_theme_mod( $spacing );
			if ( $value ) {
				echo str_replace( '{{VALUE}}', $value, $css );
			}
		}

	}

	/**
	 * Add plugin styles.
	 *
	 * @return void
	 **@since 3.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'penci-texttospeech', PenciTextToSpeech::$url . 'css/text-to-speech' . PenciTextToSpeech::$suffix . '.css', [], PenciTextToSpeech::$version );

	}

	/**
	 * Main FrontStyles Instance.
	 *
	 * Insures that only one instance of FrontStyles exists in memory at any one time.
	 *
	 * @static
	 * @return FrontStyles
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class FrontStyles.
