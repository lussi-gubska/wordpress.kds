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
 * SINGLETON: Class adds admin scripts.
 *
 * @since 3.0.0
 *
 **/
final class AdminScripts {

	/**
	 * The one true AdminScripts.
	 *
	 * @var AdminScripts
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new AdminScripts instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Add JavaScrips for admin area.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	public function admin_scripts() {

		/** Plugin Settings Page. */
		$this->settings_scripts();

		/** Scripts for selected post types on edit screen. */
		$this->edit_post_scripts();

	}

	/**
	 * Scripts for selected post types edit screen.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	private function edit_post_scripts() {

		/** Edit screen for selected post types. */
		$screen = get_current_screen();

		/** Get supported post types from plugin settings. */
		$cpt_support = get_theme_mod( 'penci_texttospeech_enabled_post_types' );

		if (
			$cpt_support &&
			null !== $screen &&
			$screen->base !== 'edit' &&
			in_array( $screen->post_type, $cpt_support, false )
		) {

			wp_enqueue_script( 'penci-tts-admin-post', PenciTextToSpeech::$url . 'js/admin-post' . PenciTextToSpeech::$suffix . '.js', [
				'jquery',
			], PenciTextToSpeech::$version, true );

			/** Get URL to upload folder. */
			$upload_dir     = wp_get_upload_dir();
			$upload_baseurl = $upload_dir['baseurl'];

			/** URL to audio folder. */
			$audio_url = $upload_baseurl . '/penci-text-to-speech/';

			$lang_code_name = get_theme_mod( 'penci_texttospeech_language_name', 'en-US-Standard-D' ) ? get_theme_mod( 'penci_texttospeech_language_name', 'en-US-Standard-D' ) : 'en-US-Standard-D';
			$lang_code      = explode( '-', $lang_code_name );
			$lang_code      = is_array( $lang_code ) ? $lang_code[0] . '-' . $lang_code[1] : 'en-US';

			/** Pass some vars to JS. */
			wp_localize_script( 'penci-tts-admin-post', 'PenciTextToSpeech', [
				'post_id'             => get_the_ID(),
				'nonce'               => wp_create_nonce( 'penci-tts-nonce' ),
				'audio_url'           => $audio_url,
				'voice'               => $lang_code,
				'speechTemplateCount' => 'content',
			] );

		}

	}

	/**
	 * Scripts for plugin setting page.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	private function settings_scripts() {

		/** Add scripts only on plugin settings page. */
		$screen = get_current_screen();
		if ( null === $screen || $screen->base !== PenciTextToSpeech::$menu_base ) {
			return;
		}

		wp_enqueue_script( 'merkulov-ui', PenciTextToSpeech::$url . 'js/merkulov-ui' . PenciTextToSpeech::$suffix . '.js', [], PenciTextToSpeech::$version, true );
		wp_enqueue_script( 'dataTables', PenciTextToSpeech::$url . 'js/jquery.dataTables' . PenciTextToSpeech::$suffix . '.js', [ 'jquery' ], PenciTextToSpeech::$version, true );

		wp_enqueue_script( 'penci-texttospeech-admin', PenciTextToSpeech::$url . 'js/admin' . PenciTextToSpeech::$suffix . '.js', [
			'jquery',
			'dataTables'
		], PenciTextToSpeech::$version, true );
		wp_localize_script( 'penci-texttospeech-admin', 'PenciTextToSpeech', [
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'penci-tts-nonce' ), // Nonce for security.
		] );

	}

	/**
	 * Main AdminScripts Instance.
	 *
	 * Insures that only one instance of AdminScripts exists in memory at any one time.
	 *
	 * @static
	 * @return AdminScripts
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		/** @noinspection SelfClassReferencingInspection */
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AdminScripts ) ) {

			self::$instance = new AdminScripts;

		}

		return self::$instance;

	}

} // End Class AdminScripts.
