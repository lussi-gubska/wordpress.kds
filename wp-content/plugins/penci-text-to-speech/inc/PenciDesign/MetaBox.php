<?php


namespace PenciDesign;

use Google\ApiCore\ApiException;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class adds Text To Speech Metabox for selected Post types.
 *
 * @since 3.0.0
 *
 **/
final class MetaBox {

	/**
	 * The one true MetaBox.
	 *
	 * @var MetaBox
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new MetaBox instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		if ( $this->check_gutenberg_editor() ) {
			add_action( 'add_meta_boxes', [ $this, 'meta_box' ] );
		} else {
			add_action( 'edit_form_after_title', [ $this, 'meta_box_html' ] );
		}
	}

	function check_gutenberg_editor() {

		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		$use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

		return $use_block_editor;
	}

	public function meta_box() {

		/** Get selected post types. */
		$screens = get_theme_mod( 'penci_texttospeech_enabled_post_types' );
		if ( $screens ) {
			foreach ( $screens as $screen ) {

				/** Add Text To Speech Metabox */
				add_meta_box(
					'penci_text_to_speech_settings_box',
					'Penci Text To Speech',
					[ $this, 'meta_box_html' ],
					$screen,
					'side',
					'core'
				);

			}
		}

	}

	/**
	 * Render Meta Box.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function meta_box_html() {

		global $post;
		$type    = '';
		$screens = [];

		$screens_opts = get_theme_mod( 'penci_texttospeech_enabled_post_types' );

		if ( $screens_opts ) {
			$screens = $screens_opts;
		}

		if ( $post && $post->post_type ) {
			$type = $post->post_type;
		}

		if ( $type && in_array( $type, $screens ) ) {


			/** Show audio player if audio exist. */
			SpeechCaster::get_instance()->the_player();

			/** Show "Generate Audio" button if Post already saved and published. */
			$status = get_post_status();
			if ( 'publish' !== $status ) :

				/** Show warning for unpublished posts. */
				$this->meta_box_html_status();

            elseif ( post_password_required() ) :

				/** Show warning for password protected posts. */
				$this->meta_box_html_password();

            elseif ( get_theme_mod( 'penci_texttospeech_api_key' ) ) :

				/** Show generate button. */
				$this->meta_box_html_generate();

			endif;
		}
	}

	/**
	 * Show warning for unpublished posts.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function meta_box_html_status() {
		?>
        <div class="mdp-warning">
			<?php esc_html_e( 'Publish a post before you can generate an audio version.', 'penci-text-to-speech' ); ?>
        </div>
		<?php
	}

	/**
	 * Show warning for password protected posts.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function meta_box_html_password() {
		?>
        <div class="mdp-warning">
			<?php esc_html_e( 'Penci Text To Speech reads only publicly available posts.', 'penci-text-to-speech' ); ?><br>
			<?php esc_html_e( 'Remove the password from the post, create an audio version, then close the post again with a password.', 'penci-text-to-speech' ); ?>
            <br>
			<?php esc_html_e( 'This is a necessary safety measure.', 'penci-text-to-speech' ); ?>
        </div>
		<?php
	}

	/**
	 * Show generate button.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function meta_box_html_generate() {

		/** Checks if there is audio for the current post. */
		$audio_exists = SpeechCaster::get_instance()->audio_exists();
		?>
        <div class="penci-texttospeech-meta-box-controls">


            <div>
                <button id="penci_texttospeech_generate" type="button"
                        class="button-large components-button is-button is-primary is-large">
					<?php if ( $audio_exists ) : ?>
                        <i class="dashicons dashicons-format-audio"></i>
						<?php esc_html_e( 'Re-create Audio', 'penci-text-to-speech' ); ?>
					<?php else : ?>
                        <i class="dashicons dashicons-format-audio"></i>
						<?php esc_html_e( 'Create Audio', 'penci-text-to-speech' ); ?>
					<?php endif; ?>
                </button>

				<?php if ( $audio_exists ) : ?>
                    <button id="penci_texttospeech_remove" type="button"
                            class="button-large components-button button-link-delete is-button is-default is-large">
						<?php esc_html_e( 'Remove', 'penci-text-to-speech' ); ?>
                    </button>
				<?php endif; ?>
            </div>

        </div>
		<?php

	}

	/**
	 * Main MetaBox Instance.
	 *
	 * Insures that only one instance of MetaBox exists in memory at any one time.
	 *
	 * @static
	 * @return MetaBox
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class MetaBox.
