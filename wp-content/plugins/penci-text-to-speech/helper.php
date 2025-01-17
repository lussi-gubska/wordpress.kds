<?php

namespace PenciDesign;

use DOMDocument;
use DOMException;
use DOMXPath;
use PenciDesign\MetaBox;
use PenciDesign\SpeechCaster;
use PenciDesign\XMLHelper;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Core class used to implement a PenciTextToSpeechUtilities class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 2.0.0
 **/
final class PenciTextToSpeechUtilities {

	/**
	 * The one true PenciTextToSpeechUtilities.
	 *
	 * @var PenciTextToSpeechUtilities
	 * @since 2.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new class instance.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function __construct() {

		/** We  can't work without api key. */
		if ( ! get_theme_mod( 'penci_texttospeech_api_key' ) ) {
			return;
		}

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

		/** Add Penci Text To Speech bulk action for each selected post type. */
		$cpt_posts = get_theme_mod( 'penci_texttospeech_enabled_post_types' );

		if ( $cpt_posts ) {
			foreach ( $cpt_posts as $post_type ) {

				/** Add PenciTextToSpeechUtilities Column to selected post types. */
				add_filter( "manage_{$post_type}_posts_columns", [ $this, 'add_head_column' ], 10 );
				add_filter( "manage_edit-{$post_type}_columns", [ $this, 'add_head_column' ], 10 );
				add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'add_content_column' ], 10, 2 );

				/** Add Penci Text To Speech bulk action to dropdown. */
				add_filter( "bulk_actions-edit-{$post_type}", [ $this, 'bulk_actions' ] );

				/** Save Custom Post Template on post save. */
				add_action( "save_post_{$post_type}", [ $this, 'save_post' ] );

			}
		}

		/** Automatic synthesis. */
		add_action( 'post_updated', [ $this, 'auto_generation_on_update' ], 10, 3 );

		add_filter( 'mejs_settings', function ( $settings ) {
			$settings['features'] = [
				'playpause',
				'current',
				'progress',
				'duration',
				'tracks',
				'volume',
				'fullscreen',
			];
			if ( get_theme_mod( 'penci_texttospeech_speed_controls', true ) ) {

				$settings['features'][] = 'speed';
				$settings['speedText']  = get_theme_mod( 'penci_texttospeech_speed_title', 'Speed' );

				$speeds = preg_split( "/[\s,]+/", get_theme_mod( 'penci_texttospeech_speed', '0.25, 0.5, 0.75, 1.25, 1.5, 1.75' ) );
				$speeds = array_filter( $speeds );

				$settings['speeds']    = $speeds;
				$settings['speedChar'] = '';
			}

			return $settings;
		} );

	}

	/**
	 * Save Custom Post Template on post save.
	 *
	 * @param $post_id
	 *
	 * @return void
	 **@since  3.0.0
	 * @access public
	 *
	 */
	public function save_post( $post_id ) {

		/** Get new custom template. */
		$new_custom_st = filter_input( INPUT_POST, 'penci_texttospeech_speech_templates_template' );

		$meta_key = 'penci_texttospeech_custom_speech_template';

		/** Clear post custom template for 'content' */
		if ( 'content' === $new_custom_st ) {

			update_post_meta( $post_id, $meta_key, '' );

			return;

		}

		/** Remember custom template for current post type. */
		update_post_meta( $post_id, $meta_key, $new_custom_st );

	}

	/**
	 * Generate audio version on every post update.
	 *
	 * @param $post_ID
	 * @param $post_after
	 * @param $post_before
	 *
	 * @return void
	 **@since  3.0.0
	 * @access public
	 *
	 */
	public function auto_generation_on_update( $post_ID, $post_after, $post_before ) {

		/** Work only if Automatic synthesis is enabled. */
		if ( ! get_theme_mod( 'penci_texttospeech_auto_generation' ) ) {
			return;
		}

		/** Get supported post types from plugin settings. */
		$cpt_support = get_theme_mod( 'penci_texttospeech_enabled_post_types' );

		/** Work only with supported post types. */
		if ( $cpt_support && ! in_array( $post_before->post_type, $cpt_support, false ) ) {
			return;
		}

		/** Work only if content was changed. */
		if ( $post_before->post_content === $post_after->post_content ) {
			return;
		}

		/** Generate Audi version for current post. */
		SpeechCaster::get_instance()->voice_acting( $post_ID );

	}

	/**
	 * Add Penci Text To Speech sub by shortcode [penci-tts-sub alias="Second World War"]WW2[/penci-tts-sub].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content - Shortcode content when using the closing shortcode construct: [foo] shortcode text [/ foo].
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_sub_shortcode( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'alias' => ''
		], $atts );

		/** If we don't have alias, exit. */
		if ( ! $atts['alias'] ) {

			return do_shortcode( $content );

		}

		/** Extra protection from the fools */
		$atts['alias'] = trim( strip_tags( $atts['alias'] ) );

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<sub alias="' . esc_attr( $atts['alias'] ) . '">' . do_shortcode( $content ) . '</sub>';

		}

		return do_shortcode( $content );

	}

	/**
	 * Initializes shortcodes.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 */
	public function shortcodes_init() {

		/** Penci Text To Speech say‑as Shortcode. [penci-tts-say‑as interpret-as="cardinal"][/penci-tts-say‑as] */
		add_shortcode( 'penci-tts-say‑as', [ $this, 'penci_tts_say_as_shortcode' ] );

		/** Penci Text To Speech sub Shortcode. [penci-tts-sub alias="World Wide Web Consortium"]W3C[/penci-tts-sub] */
		add_shortcode( 'penci-tts-sub', [ $this, 'penci_tts_sub_shortcode' ] );

		/** Penci Text To Speech emphasis Shortcode. [penci-tts-emphasis level="moderate"]This is an important announcement.[/penci-tts-emphasis] */
		add_shortcode( 'penci-tts-emphasis', [ $this, 'penci_tts_emphasis_shortcode' ] );

		/** Penci Text To Speech voice Shortcode. [penci-tts-voice name="en-GB-Wavenet-C"]I am not a real human.[/penci-tts-voice] */
		add_shortcode( 'penci-tts-voice', [ $this, 'penci_tts_voice_shortcode' ] );

		/** Link to audio file Shortcode [penci-tts-file]. */
		add_shortcode( 'penci-tts-file', [ $this, 'penci_tts_file' ] );

		/** Shortcode to say but not show content [penci-tts-say]Some Content.[/penci-tts-say]. */
		add_shortcode( 'penci-tts-say', [ $this, 'penci_tts_say' ] );

		/** Shortcode used to customize the pitch, speaking rate, and volume of text contained by the element. [penci-tts-prosody rate="" pitch="" volume=""]Some Content.[/penci-tts-prosody]. */
		add_shortcode( 'penci-tts-prosody', [ $this, 'penci_tts_prosody' ] );

	}

	/**
	 * Link to audio file Shortcode [penci-tts-file] or [penci-tts-file id="123"].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @return bool|false|string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_file( $atts ) {

		$params = shortcode_atts( [ 'id' => '0' ], $atts );

		$id = (int) $params['id'];

		/** URL to post audio file. */
		return SpeechCaster::get_instance()->get_audio_url( $id );

	}

	/**
	 * Add Penci Text To Speech voice by shortcode [penci-tts-voice name="en-GB-Wavenet-B"]My new voice.[/penci-tts-voice].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_voice_shortcode( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'name' => ''
		], $atts );

		/** If we don't have name, exit. */
		if ( ! $atts['name'] ) {
			return do_shortcode( $content );
		}

		/** Extra protection from the fools */
		$atts['name'] = trim( strip_tags( $atts['name'] ) );

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<voice name="' . esc_attr( $atts['name'] ) . '">' . do_shortcode( $content ) . '</voice>';

		}

		return do_shortcode( $content );

	}

	/**
	 * Add Penci Text To Speech emphasis by shortcode [penci-tts-emphasis level="reduced"]Some information[/penci-tts-emphasis].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content - Shortcode content when using the closing shortcode construct: [foo] shortcode text [/ foo].
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_emphasis_shortcode( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'level' => 'none'
		], $atts );

		/** Extra protection from the fools */
		$atts['level'] = trim( strip_tags( $atts['level'] ) );

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<emphasis level="' . esc_attr( $atts['level'] ) . '">' . do_shortcode( $content ) . '</emphasis>';

		}

		return do_shortcode( $content );

	}

	/**
	 * Shortcode to say but not show content [penci-tts-say]Some Content.[/penci-tts-say].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content - Shortcode content when using the closing shortcode construct: [foo] shortcode text [/ foo].
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_say( $atts, $content ) {

		shortcode_atts( [], $atts ); // To hide unused param warning.

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return '<span speaker-say="">' . do_shortcode( $content ) . '</span>';

		}

		return '';

	}

	/**
	 * Shortcode used to customize the pitch, speaking rate, and volume of text contained by the element.
	 * [penci-tts-prosody rate="" pitch="" volume=""]Some Content.[/penci-tts-prosody].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_prosody( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'rate'   => '',
			'pitch'  => '',
			'volume' => ''
		], $atts );

		/** If we don't have any params then exit. */
		if ( ! $atts['rate'] && ! $atts['pitch'] && ! $atts['volume'] ) {
			return do_shortcode( $content );
		}

		/** Extra protection from the fools */
		$atts['rate']   = trim( strip_tags( $atts['rate'] ) );
		$atts['pitch']  = trim( strip_tags( $atts['pitch'] ) );
		$atts['volume'] = trim( strip_tags( $atts['volume'] ) );

		$res = '<prosody ';

		if ( $atts['rate'] ) {
			$res .= 'rate="' . $atts['rate'] . '" ';
		}

		if ( $atts['pitch'] ) {
			$res .= 'pitch="' . $atts['pitch'] . '" ';
		}

		if ( $atts['volume'] ) {
			$res .= 'volume="' . $atts['volume'] . '" ';
		}

		$res .= '>' . do_shortcode( $content ) . '</prosody>';

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return $res;

		}

		return do_shortcode( $content );

	}

	/**
	 * Add Penci Text To Speech say‑as by shortcode [penci-tts-say‑as interpret-as="ordinal"][/penci-tts-say‑as].
	 *
	 * @param $atts - An associative array of attributes specified in the shortcode.
	 *
	 * @param $content
	 *
	 * @return string
	 * @since 3.0.0
	 * @access public
	 **/
	public function penci_tts_say_as_shortcode( $atts, $content ) {

		/** White list of options with default values. */
		$atts = shortcode_atts( [
			'interpret-as' => '',
			'format'       => '',
			'detail'       => ''
		], $atts );

		/** If we don't have interpret-as, exit. */
		if ( ! $atts['interpret-as'] ) {
			return do_shortcode( $content );
		}

		/** Extra protection from the fools */
		$atts['interpret-as'] = trim( strip_tags( $atts['interpret-as'] ) );
		$atts['format']       = trim( strip_tags( $atts['format'] ) );
		$atts['detail']       = trim( strip_tags( $atts['detail'] ) );

		$res = '<say-as interpret-as="' . $atts['interpret-as'] . '" ';

		if ( $atts['format'] ) {
			$res .= 'format="' . $atts['format'] . '" ';
		}

		if ( $atts['detail'] ) {
			$res .= 'detail="' . $atts['detail'] . '" ';
		}

		$res .= '>' . do_shortcode( $content ) . '</say-as>';

		/** Show shortcodes only for our parser. Hide on frontend. */
		if ( isset( $_GET['penci-tts'] ) && $_GET['penci-tts'] ) {

			return $res;

		}

		return do_shortcode( $content );

	}

	/**
	 * @throws DOMException
	 */
	public function apply_ssml_attributes( $post_content ) {

		/** Hide DOM parsing errors. */
		libxml_use_internal_errors( true );
		libxml_clear_errors();

		/** Load the possibly malformed HTML into a DOMDocument. */
		$dom          = new DOMDocument();
		$dom->recover = true;
		$dom->loadHTML( '<?xml encoding="UTF-8"><body id="repair">' . $post_content . '</body>' ); // input UTF-8.

		$selector = new DOMXPath( $dom );

		/** Say as */
		foreach ( $selector->query( '//*[@penci-tts-say-as]' ) as $e ) {

			// Create SSML node
			$ssmlNode = $dom->createElement( 'say-as', $e->nodeValue );
			$ssmlNode->setAttribute( 'interpret-as', $e->getAttribute( 'penci-tts-say-as' ) );
			$ssmlNode->setAttribute( 'format', $e->getAttribute( 'format' ) );
			$ssmlNode->setAttribute( 'detail', $e->getAttribute( 'detail' ) );

			// Replace HTML node by SSML node
			$e->parentNode->replaceChild( $ssmlNode, $e );

		}

		/** Prosody */
		foreach ( $selector->query( '//*[@penci-tts-prosody]' ) as $e ) {

			// Create SSML node
			$ssmlNode = $dom->createElement( 'prosody', $e->nodeValue );
			$ssmlNode->setAttribute( 'rate', $e->getAttribute( 'rate' ) );
			$ssmlNode->setAttribute( 'pitch', $e->getAttribute( 'pitch' ) );
			$ssmlNode->setAttribute( 'volume', $e->getAttribute( 'volume' ) );

			// Replace HTML node by SSML node
			$e->parentNode->replaceChild( $ssmlNode, $e );

		}

		/** Substitution or alias */
		$dom = $this->inject_ssml_markup( $dom, $selector, 'penci-tts-sub', 'sub', 'alias' );

		/** Emphasis */
		$dom = $this->inject_ssml_markup( $dom, $selector, 'penci-tts-emphasis', 'emphasis', 'level' );

		/** Voice name */
		$dom = $this->inject_ssml_markup( $dom, $selector, 'penci-tts-voice', 'voice', 'name' );

		/** HTML without muted tags. */
		$body = $dom->documentElement->lastChild;

		return trim( XMLHelper::get_instance()->get_inner_html( $body ) );

	}

	/**
	 * Inject SSML tag instead HTML with data attributes
	 *
	 * @param $dom
	 * @param $selector
	 * @param $speaker_attribute
	 * @param $ssml_tag
	 * @param $ssml_attribute
	 *
	 * @return mixed
	 */
	private function inject_ssml_markup( $dom, $selector, $speaker_attribute, $ssml_tag, $ssml_attribute ) {

		foreach ( $selector->query( '//*[@' . $speaker_attribute . ']' ) as $e ) {

			// Create SSML node
			$ssmlNode = $dom->createElement( $ssml_tag, $e->nodeValue );
			$ssmlNode->setAttribute( $ssml_attribute, $e->getAttribute( $speaker_attribute ) );

			// Replace HTML node by SSML node
			$e->parentNode->replaceChild( $ssmlNode, $e );

		}

		return $dom;

	}

	/**
	 * Add Penci Text To Speech bulk action to dropdown.
	 *
	 * @param $actions
	 *
	 * @return mixed
	 * @since 2.0.0
	 * @access public
	 **/
	public function bulk_actions( $actions ) {

		/** Work only with PUBLISHED posts or for ALL. */
		$post_status = filter_input( INPUT_GET, 'post_status' );

		if ( $post_status === 'publish' || $post_status === null ) {

			$actions['penci-text-to-speech'] = esc_html__( 'Create Audio', 'penci-text-to-speech' );

		}

		return $actions;

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
	 * @since 2.0.0
	 * @access public
	 **/
	public function enqueue_backend() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Add JS for admin area.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function admin_scripts() {

		/** Add styles only on Pages and Posts list page. */
		$cpt_posts = get_theme_mod( 'penci_texttospeech_enabled_post_types' );
		$screen    = get_current_screen();
		if ( null === $screen ) {
			return;
		}


		/** Get URL to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_baseurl = $upload_dir['baseurl'];

		/** URL to audio folder. */
		$audio_url = $upload_baseurl . '/penci-text-to-speech/';
		if ( $cpt_posts ) {
			foreach ( $cpt_posts as $post_type ) {

				if ( $screen->id === "edit-{$post_type}" ) {

					wp_enqueue_script( 'penci-texttospeech-admin-edit', PenciTextToSpeech::$url . 'js/admin-edit' . PenciTextToSpeech::$suffix . '.js', [ 'jquery' ], PenciTextToSpeech::$version, true );
					wp_localize_script( 'penci-texttospeech-admin-edit', 'PenciTextToSpeech', [
						'nonce'     => wp_create_nonce( 'penci-tts-nonce' ), // Set Nonce.
						'audio_url' => $audio_url,
					] );

					break;

				}

			}
		}

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function admin_styles() {

		$screen = get_current_screen();

		/** Add Penci Text To Speech bulk action for each selected post type. */
		$cpt_posts = get_theme_mod( 'penci_texttospeech_enabled_post_types' );
		if ( $cpt_posts ) {
			foreach ( $cpt_posts as $post_type ) {

				if ( null !== $screen && $screen->id === "edit-{$post_type}" ) {

					wp_enqueue_style( 'penci-texttospeech-admin-edit', PenciTextToSpeech::$url . 'css/admin-edit' . PenciTextToSpeech::$suffix . '.css', [], PenciTextToSpeech::$version );
					break;

				}

			}
		}

	}

	/**
	 * Add HEAD for Penci Text To Speech column with results.
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 2.0.0
	 * @access public
	 **/
	public function add_head_column( $columns ) {

		/** Work only with PUBLISHED posts or for ALL. */
		$post_status = filter_input( INPUT_GET, 'post_status' );
		if ( ! ( $post_status === 'publish' || $post_status === null ) ) {
			return $columns;
		}

		/** If we have title, comments column or author columns add after it. */
		$add_after = 'cb';
		if ( isset( $columns['title'] ) ) {
			$add_after = 'title';
		} elseif ( isset( $columns['comments'] ) ) {
			$add_after = 'comments';
		} elseif ( isset( $columns['author'] ) ) {
			$add_after = 'author';
		} elseif ( isset( $columns['date'] ) ) {
			$add_after = 'date';
		}

		/** Add new column to the existing columns. */
		$new = [];
		foreach ( $columns as $key => $col ) {

			$new[ $key ] = $col;

			/** Add after comments column. */
			if ( $key === $add_after ) {

				$new['penci-text-to-speech'] = '<span class="dashicons dashicons-controls-play" title="' . esc_attr__( 'Penci Text To Speech', 'penci-text-to-speech' ) . '"><span class="screen-reader-text">' . esc_attr__( 'Penci Text To Speech', 'penci-text-to-speech' ) . '</span></span>';

			}

		}

		/** Return a new column array to WordPress. */
		return $new;

	}

	/**
	 * Add CONTENT for Penci Text To Speech column with results.
	 *
	 * @param string $column_name
	 * @param $post_ID
	 *
	 * @since 2.0.0
	 * @access public
	 **/
	public function add_content_column( $column_name, $post_ID ) {

		if ( $column_name !== 'penci-text-to-speech' ) {
			return;
		}

		/** Show Generate Button if we haven't audio. */
		if ( ! SpeechCaster::get_instance()->audio_exists( $post_ID ) ) {

			/** Render generate audio button for bulk processing. */
			$this->render_generate_btn( $post_ID );

		} else {

			/** Render download audio button for bulk processing. */
			$this->render_download_btn( $post_ID );

		}

	}

	/**
	 * Render download audio button for bulk processing.
	 *
	 * @param $post_id
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function render_download_btn( $post_id ) {

		/** URL to download audio file. */
		$audio_url = SpeechCaster::get_instance()->get_audio_url( $post_id );

		?>
        <a href="<?php echo esc_url( $audio_url ); ?>"
           class="dashicons dashicons-format-audio penci-texttospeech-download" download=""
           title="<?php esc_html_e( 'Download Audio', 'penci-text-to-speech' ); ?>"></a>
        <a href="#"
           class="penci-texttospeech-gen"
           data-post-id="<?php echo esc_attr( $post_id ); ?>"
           data-stid="<?php esc_attr_e( $this->get_post_st( $post_id ) ); ?>"
           style="display: none;"></a>

		<?php $this->render_post_custom_st_name( $post_id ); // Print custom speech template name.

	}

	/**
	 * Render generate audio button for bulk processing.
	 *
	 * @param $post_id
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	private function render_generate_btn( $post_id ) {

		?>
        <a href="#"
           class="penci-texttospeech-gen"
           data-post-id="<?php echo esc_attr( $post_id ); ?>"
           data-stid="<?php esc_attr_e( $this->get_post_st( $post_id ) ); ?>"
           title="<?php esc_html_e( 'Create audio', 'penci-text-to-speech' ); ?>">
            <i class="dashicons dashicons-controls-play"></i>
        </a>

		<?php $this->render_post_custom_st_name( $post_id ); // Print custom speech template name.

	}

	/**
	 * Return Speech Template by post id.
	 *
	 * @param $post_id
	 *
	 * @return string
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function get_post_st( $post_id ) {

		/** If current post have custom Speech Template, show it. */
		$stid = get_post_meta( $post_id, 'penci_texttospeech_custom_speech_template', true );
		if ( $stid ) {
			return $stid;
		}

		/** Default Speech Template. */
		return 'content';

	}

	/**
	 * Print custom speech template name.
	 *
	 * @param $post_id
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function render_post_custom_st_name( $post_id ) {

		/** If current post have custom Speech Template, show it. */
		$STID = get_post_meta( $post_id, 'penci_texttospeech_custom_speech_template', true );
		if ( ! $STID ) {
			return;
		}

		/** Return Speech Template Name by ID. */
		$st_name = $this->get_speech_template_name( $STID );

		/** Show custom Speech Template name. */
		if ( $st_name ) {

			?><a href="#" class="mdp-custom-template"><img
                    src="<?php echo esc_attr( PenciTextToSpeech::$url . 'images/custom-template.svg' ); ?>"
                    alt="<?php esc_html_e( $st_name ); ?>" title="<?php esc_html_e( $st_name ); ?>"></a><?php

		}

	}

	/**
	 * Return Speech Template Name by ID.
	 *
	 * @param $stid
	 *
	 * @return string
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function get_speech_template_name( $stid ) {

		/** Read all ST from settings. */
		/** In this option we store all Speech Templates. */
		$st_opt_name = 'penci_texttospeech_speech_templates';

		/** Get all Speech Templates. */
		$st = get_option( $st_opt_name, false );

		/** Return if no ST. */
		if ( ! $st ) {
			return '';
		}

		/** If We have any ST. */
		if ( count( $st ) ) {

			/** Add add ST to list. */
			foreach ( $st as $template ) {

				if ( $template['id'] === $stid ) {
					return $template['name'];
				}

			}

		}

		return '';

	}

	/**
	 * Render speech template controls.
	 *
	 * @param $default
	 *
	 * @return void
	 **@since  3.0.0
	 * @access public
	 *
	 */
	public function render_speech_template_controls( $default ) {
		?>
        <button id="penci-texttospeech-add-speech-template-btn"
                data-post-url="<?php echo esc_url( $this->get_post_url() ); ?>"
                class="penci-texttospeech-add mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                title="<?php esc_attr_e( 'Add Speech Template', 'penci-text-to-speech' ); ?>"
                aria-label="<?php esc_attr_e( 'Add Speech Template', 'penci-text-to-speech' ); ?>">add
        </button>

        <button class="penci-texttospeech-edit mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                title="<?php esc_attr_e( 'Edit Speech Template', 'penci-text-to-speech' ); ?>"
                aria-label="<?php esc_attr_e( 'Edit Speech Template', 'penci-text-to-speech' ); ?>">create
        </button>

        <button class="penci-texttospeech-make-default mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                data-post-type="<?php esc_attr_e( get_post_type() ); ?>"
                data-default-for-post-type="<?php esc_attr_e( $default ); ?>"
                title="<?php esc_attr_e( 'Save this Speech Template as Default for this Post Type', 'penci-text-to-speech' ); ?>"
                aria-label="<?php esc_attr_e( 'Save this Speech Template as Default for this Post Type', 'penci-text-to-speech' ); ?>">
			<?php if ( $default ) : ?>
                flag
			<?php else: ?>
                outlined_flag
			<?php endif; ?>
        </button>

        <button class="penci-texttospeech-delete mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                title="<?php esc_attr_e( 'Delete Speech Template', 'penci-text-to-speech' ); ?>"
                aria-label="<?php esc_attr_e( 'Delete Speech Template', 'penci-text-to-speech' ); ?>">delete
        </button>

		<?php

	}

	/**
	 * Return frontend post url for Speech Template Editor.
	 *
	 * @return string
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function get_post_url() {

		/** Current post id. */
		$post_id = get_the_ID();

		/** Get full permalink for the current post. */
		$url = get_permalink( $post_id );

		/** Returns a string if the URL has parameters or NULL if not. */
		$query = parse_url( $url, PHP_URL_QUERY );

		/** Add penci_tts_template param to URL. */
		if ( $query ) {
			$url .= '&penci_tts_template=1';
		} else {
			$url .= '?penci_tts_template=1';
		}

		return $url;

	}

	/**
	 * Main PenciTextToSpeechUtilities Instance.
	 *
	 * Insures that only one instance of PenciTextToSpeechUtilities exists in memory at any one time.
	 *
	 * @static
	 * @return PenciTextToSpeechUtilities
	 * @since 2.0.0
	 **/
	public static function get_instance() {

		/** @noinspection SelfClassReferencingInspection */
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PenciTextToSpeechUtilities ) ) {

			self::$instance = new PenciTextToSpeechUtilities;

		}

		return self::$instance;

	}

} // End Class PenciTextToSpeechUtilities.
