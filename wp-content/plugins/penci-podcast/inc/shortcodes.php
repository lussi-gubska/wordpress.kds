<?php

class PenciPodCast_Shortcode {
	/**
	 * Instance of Player
	 *
	 * @var PenciPodCast_Shortcode
	 */
	private static $instance;

	/**
	 * Singleton page of Player class
	 *
	 * @return PenciPodCast_Shortcode
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		add_shortcode( 'podcast', [ $this, 'podcast_shortcode' ] );
	}

	public static function podcast_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id'      => '',
			'size'    => '',
			'num'     => 5,
			'author'  => '',
			'sub'     => '',
			'episode' => '',
			'desc'    => '',
			'img_pos' => '',
			'class'   => '',
		), $atts, 'podcast' );
		ob_start();
		load_template( PENCI_PODCAST_DIR . 'templates/playlist.php', true, $atts );

		return ob_get_clean();
	}
}