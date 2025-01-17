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
final class AdminStyles {

	/**
	 * The one true AdminStyles.
	 *
	 * @var AdminStyles
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new AdminStyles instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	public function admin_styles() {


		/** Styles for selected post types edit screen. */
		$this->edit_post_styles();

		/** Plugins page. Styles for "View version details" popup. */
		$this->plugin_update_styles();

	}

	/**
	 * Styles for selected post types edit screen.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	private function edit_post_styles() {

		/** Edit Post/Page. */
		$screen = get_current_screen();

		/** Get supported post types from plugin settings. */
		$cpt_support = get_theme_mod( 'penci_texttospeech_enabled_post_types' );

		if (
			$cpt_support &&
			null !== $screen &&
			$screen->base !== 'edit' &&
			in_array( $screen->post_type, $cpt_support, false )
		) {


			wp_enqueue_style( 'penci-texttospeech-admin-post', PenciTextToSpeech::$url . 'css/admin-post' . PenciTextToSpeech::$suffix . '.css', [], PenciTextToSpeech::$version );

		}

	}

	/**
	 * Styles for plugins page. "View version details" popup.
	 *
	 * @return void
	 **@since   3.0.0
	 */
	private function plugin_update_styles() {

		/** Plugin install page, for style "View version details" popup. */
		$screen = get_current_screen();
		if ( null === $screen || $screen->base !== 'plugin-install' ) {
			return;
		}

		/** Styles only for our plugin. */
		if ( isset( $_GET['plugin'] ) && $_GET['plugin'] === 'penci-text-to-speech' ) {

			wp_enqueue_style( 'penci-texttospeech-plugin-install', PenciTextToSpeech::$url . 'css/plugin-install' . PenciTextToSpeech::$suffix . '.css', [], PenciTextToSpeech::$version );

		}

	}

	/**
	 * Main AdminStyles Instance.
	 *
	 * Insures that only one instance of AdminStyles exists in memory at any one time.
	 *
	 * @static
	 * @return AdminStyles
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		/** @noinspection SelfClassReferencingInspection */
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AdminStyles ) ) {

			self::$instance = new AdminStyles;

		}

		return self::$instance;

	}

} // End Class AdminStyles.
