<?php


namespace PenciDesign;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use PenciDesign\PenciTextToSpeechUtilities;

/**
 * SINGLETON: Class used to implement shortcodes.
 *
 * @since 2.0.0
 *
 **/
final class Shortcodes {

	/**
	 * The one true Shortcodes.
	 *
	 * @var Shortcodes
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Shortcodes instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Initializes shortcodes. */
		$this->shortcodes_init();

	}

	/**
	 * Initializes shortcodes.
	 *
	 * @return void
	 **@since 2.0.0
	 * @access public
	 */
	public function shortcodes_init() {

		/** Add player by shortcode [penci-tts] or [penci-tts id=POST_ID] */
		add_shortcode( 'penci-tts', [ $this, 'penci_tts_shortcode' ] );
		add_shortcode( 'penci-texttospeech', [ $this, 'penci_tts_shortcode' ] );

		/** Text To Speech Mute Shortcode. [penci-tts-mute]...[/penci-tts-mute] */
		add_shortcode( 'penci-tts-mute', [ $this, 'penci_tts_mute_shortcode' ] );

		/** Text To Speech Break Shortcode. [penci-tts-break time="2s"] */
		add_shortcode( 'penci-tts-break', [ $this, 'penci_tts_break_shortcode' ] );

		/** Initializes additions shortcodes from SpeechCaster. */
		/** @noinspection ClassConstantCanBeUsedInspection */
		if ( class_exists( '\PenciTextToSpeechUtilities' ) ) {

			PenciTextToSpeechUtilities::get_instance()->shortcodes_init(); // Do not change Qualifier.

		}

	}

	/**
	 * Add Text To Speech break by shortcode [penci-tts-break time="300ms"].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @return string
	 * @since 2.0.0
	 * @access public
	 **/
	public function penci_tts_break_shortcode( $atts ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'time'     => '500ms',
			'strength' => 'medium'
		], $atts );

		/** Extra protection from the fools */
		$atts['time']     = trim( strip_tags( $atts['time'] ) );
		$atts['strength'] = trim( strip_tags( $atts['strength'] ) );

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<break time="' . esc_attr( $atts['time'] ) . '" strength="' . esc_attr( $atts['strength'] ) . '" />';

		}

		return '';

	}

	/**
	 * Add Text To Speech mute by shortcode [penci-tts-mute]...[/penci-tts-mute].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 * @param $content - Shortcode content when using the closing shortcode construct: [foo] shortcode text [/ foo].
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function penci_tts_mute_shortcode( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'tag' => 'div',
		], $atts );

		$tag = $atts['tag'];

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<' . $tag . ' speaker-mute="">' . do_shortcode( $content ) . '</' . $tag . '>';

		}

		return do_shortcode( $content );

	}

	/**
	 * Add player by shortcode [penci-tts].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @return bool|false|string
	 * @since 2.0.0
	 * @access public
	 **/
	public function penci_tts_shortcode( $atts ) {

		/**
		 * If selected other settings, but we found shortcode.
		 * Show short code, but don't read it.
		 **/
		if ( 'shortcode' !== get_theme_mod( 'penci_texttospeech_position' ) && ! is_array( $atts ) ) {
			return false;
		}

		$params = shortcode_atts( [ 'id' => '0' ], $atts );

		$id = (int) $params['id'];

		return SpeechCaster::get_instance()->get_player( $id );

	}

	/**
	 * Main Shortcodes Instance.
	 *
	 * Insures that only one instance of Shortcodes exists in memory at any one time.
	 *
	 * @static
	 * @return Shortcodes
	 * @since 2.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class Shortcodes.
