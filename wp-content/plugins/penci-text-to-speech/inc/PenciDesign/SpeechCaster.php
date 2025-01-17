<?php


namespace PenciDesign;

use DOMXPath;
use DOMDocument;
use PenciDesign\PenciTextToSpeech;
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class contain all Text To Speech logic.
 *
 * @since 3.0.0
 *
 **/
final class SpeechCaster {

	/**
	 * The one true SpeechCaster.
	 *
	 * @var SpeechCaster
	 * @since 3.0.0
	 **/
	private static $instance;

	/**
	 * Add Ajax handlers and before_delete_post action.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function add_actions() {

		/** Ajax Create Audio on Backend. */
		add_action( 'wp_ajax_gspeak', [ $this, 'gspeak' ] );

		/** Ajax Remove Audio on Backend. */
		add_action( 'wp_ajax_remove_audio', [ $this, 'remove_audio' ] );

		/** Remove audio file on remove post record. */
		add_action( 'before_delete_post', [ $this, 'before_delete_post' ] );

	}

	/**
	 * Combine multiple audio files to one .mp3.
	 *
	 * @param $files - Audio files for gluing into one big.
	 * @param $post_id - ID of the Post/Page.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function glue_audio( $files, $post_id ) {

		/** Get path to upload folder. */
		$upload_dir = wp_get_upload_dir();
		if ( ! is_array( $upload_dir ) ) {
			return;
		}

		/** Path to post audio file. */
		$audio_file = $upload_dir['basedir'] . '/penci-text-to-speech/post-' . $post_id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		/** Just in case, if it exists. */
		wp_delete_file( $audio_file );
		foreach ( $files as $audio ) {

			/** Add new audio part to file. */
			file_put_contents( $audio_file, file_get_contents( $audio ), FILE_APPEND );

			/** Remove temporary audio files. */
			wp_delete_file( $audio );

		}

		/** Store file meta to the post meta */
		if ( get_theme_mod( 'penci_texttospeech_post_meta' ) ) {

			$this->create_post_meta( $upload_dir, $post_id, get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' ) );

		}

		/** Create Media Library record */
		if ( get_theme_mod( 'penci_texttospeech_media_library' ) ) {

			Attachment::get_instance()->create_attachment( $upload_dir, $post_id, get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' ) );

		}

	}

	/**
	 * Get audio meta and store it in the post meta
	 *
	 * @param $upload_dir
	 * @param $post_id
	 * @param $format
	 *
	 * @return void
	 */
	private function create_post_meta( $upload_dir, $post_id, $format ) {

		$audio_file = $upload_dir['basedir'] . '/penci-text-to-speech/post-' . $post_id . '.' . $format;
		$audio_url  = $upload_dir['baseurl'] . '/penci-text-to-speech/post-' . $post_id . '.' . $format;

		/** Store file meta to the post meta */
		if ( ! is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}
		$audio_meta = wp_read_audio_metadata( $audio_file );
		if ( ! is_array( $audio_meta ) ) {
			return;
		}

		/** Store file props to post the post meta */
		update_post_meta( $post_id, 'penci-tts-url', $audio_url );
		update_post_meta( $post_id, 'penci-tts-timestamp', current_time( 'r' ) );
		update_post_meta( $post_id, 'penci-tts-duration', $audio_meta['length_formatted'] );
		update_post_meta( $post_id, 'penci-tts-filesize', $audio_meta['filesize'] );

	}

	/**
	 * Convert HTML to temporary audio file.
	 *
	 * @param $html - Content to be voiced.
	 * @param $post_id - ID of the Post/Page.
	 *
	 * @return string
	 * @throws ApiException
	 * @since 3.0.0
	 * @access public
	 **/
	public function part_speak( $html, $post_id ) {

		/**
		 * Filters html part before speak it.
		 *
		 * @param string $html Post content part.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$html = apply_filters( 'penci_tts_before_part_speak', $html, $post_id );

		/** Instantiates a client. */
		$client = new TextToSpeechClient();

		/** Strip all html tags, except SSML tags.  */
		$html = strip_tags( $html, '<p><break><say-as><sub><emphasis><prosody><voice>' );

		/** Remove the white spaces from the left and right sides.  */
		$html = trim( $html );

		/** Convert HTML entities to their corresponding characters: &quot; => " */
		$html = html_entity_decode( $html );

		/**
		 * Replace special characters with HTML Ampersand Character Codes.
		 * These codes prevent the API from confusing text with SSML tags.
		 * '&' --> '&amp;'
		 **/
		$html = str_replace( '&', '&amp;', $html );

		/** Get language code and name from <voice> tag, or use default. */
		list( $lang_code, $lang_name ) = XMLHelper::get_instance()->get_lang_params_from_tag( $html );

		/** We don’t need <voice> tag anymore. */
		$html = strip_tags( $html, '<p><break><say-as><sub><emphasis><prosody>' );

		/** Force to SSML. */
		$ssml = "<speak>";
		$ssml .= $html;
		$ssml .= "</speak>";

		/**
		 * Filters $ssml content before Google Synthesis it.
		 *
		 * @param string $ssml Post content part.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$ssml = apply_filters( 'penci_tts_before_synthesis', $ssml, $post_id );

		/** Sets text to be synthesised. */
		$synthesisInputText = ( new SynthesisInput() )->setSsml( $ssml );

		/** Build the voice request, select the language. */
		$voice = ( new VoiceSelectionParams() )
			->setLanguageCode( $lang_code )
			->setName( $lang_name );

		/** Configure audio output */
		$audioConfig = ( new AudioConfig() )
			->setAudioEncoding( AudioEncoding::MP3 )
			->setEffectsProfileId( [ get_theme_mod( 'penci_texttospeech_audio_profile', 'wearable-class-device' ) ] )
			->setSpeakingRate( get_theme_mod( 'penci_texttospeech_speaking_rate', 0 ) )
			->setPitch( get_theme_mod( 'penci_texttospeech_pitch', 0 ) )
			->setSampleRateHertz( 24000 );
		//->setVolumeGainDb( $options['volume'] );

		/** Perform text-to-speech request on the text input with selected voice. */
		$response = $client->synthesizeSpeech( $synthesisInputText, $voice, $audioConfig );

		/** The response's audioContent is binary. */
		$audioContent = $response->getAudioContent();

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_basedir = $upload_dir['basedir'];

		/** Path to audio file. */
		$audio_file = $upload_basedir . '/penci-text-to-speech/tmp-' . uniqid( '', false ) . '-post-' . $post_id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );
		file_put_contents( $audio_file, $audioContent );

		return $audio_file;

	}

	/**
	 * Prepare HTML for Google TTS.
	 *
	 * @param $html - Post/Page content to split.
	 * @param int $max
	 *
	 * @return array() HTML parts to speech.
	 * @since 3.0.0
	 * @access public
	 **/
	public function great_divider( $html, $max = 4500 ) {

		/** Get voice wrapper for whole content */
		$voice_tag        = (object) array();
		$is_voice_wrapper = false;
		if ( preg_match( '/^(<voice)/', $html ) === 1 && preg_match( '/(<\/voice>)$/', $html ) === 1 ) {

			/** Get open and close voice tags */
			preg_match( '/^(<voice)\s\S+>/', $html, $voice_tag_start );
			preg_match( '/(<\/voice>)$/', $html, $voice_tag_end );

			/** Remove voice tag for all content */
			$html = preg_replace( '/^(<voice)\s\S+>/', '', $html, 1 );
			$html = preg_replace( '/(<\/voice>)$/', '', $html, 1 );

			/** Store voice tags in variable */
			if ( is_array( $voice_tag_start ) && is_array( $voice_tag_end ) ) {

				$voice_tag = [
					'open'  => $voice_tag_start[0],
					'close' => $voice_tag_end[0],
				];

				$is_voice_wrapper = true;

			}

		}

		$parts = [];

		/** Divide HTML by closing tags '</' */
		$html_array = preg_split( '/(<\/)/', $html );
		$html_array = array_filter( $html_array );

		/** Fix broken tags, add '</' to all except first element. */
		$count = 0;
		foreach ( $html_array as $i => $el ) {
			$count ++;
			if ( $count === 1 ) {
				continue;
			} // Skip first element.

			$html_array[ $i ] = '</' . $el;
		}

		/** Fix broken html. */
		foreach ( $html_array as $i => $el ) {
			$html_array[ $i ] = XMLHelper::get_instance()->repair_html( $el );
		}

		/** Remove empty elements. */
		$html_array = array_filter( $html_array );

		/** Divide into parts. */
		$current = "";
		foreach ( $html_array as $i => $el ) {
			$previous = $current;
			$current  .= $el;
			if ( strlen( $current ) >= $max ) {
				$parts[] = $previous;
				$current = $el;
			}
		}
		$parts[] = $current;

		/** Add voice wrapper for whole content, which was added for whole content */
		if ( $is_voice_wrapper ) {

			array_walk( $parts, [ $this, 'voice_tag_wrap' ], $voice_tag );

		}

		return $parts;

	}

	/**
	 * Wrap content in the voice tag
	 *
	 * @param $item1
	 * @param $key
	 * @param $voice_tag
	 */
	private function voice_tag_wrap( &$html, $key, $tag ) {

		$html = $tag['open'] . $html . $tag['close'];

	}

	/**
	 * Add custom text before/after audio.
	 *
	 * @param $parts - Content splitted to parts about 4000. Google have limits Total characters per request.
	 *
	 * @return array With text parts to speech.
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function add_watermark( $parts ) {

		/** Before Audio. */
		if ( get_theme_mod( 'penci_texttospeech_before_audio' ) ) {
			array_unshift( $parts, do_shortcode( get_theme_mod( 'penci_texttospeech_before_audio' ) ) );
		}

		/** After Audio. */
		if ( get_theme_mod( 'penci_texttospeech_after_audio' ) ) {
			$parts[] = do_shortcode( get_theme_mod( 'penci_texttospeech_after_audio' ) );
		}

		return $parts;
	}

	/**
	 * Divide parts by voice. One part voiced by one voice.
	 *
	 * @param array $parts HTML parts to be voiced.
	 *
	 * @return array() HTML parts to be voiced.
	 * @since 2.0.0
	 * @access public
	 */
	public function voice_divider( $parts ) {

		/** Array with parts splitted by voice. */
		$result = [];
		foreach ( $parts as $part ) {

			/** Mark location of the cut. */
			$part = str_replace( [ "<voice", "</voice>" ], [ "{|mdp|}<voice", "</voice>{|mdp|}" ], $part );

			/** Cut by marks. */
			$arr = explode( "{|mdp|}", $part );

			/** Clean the array. */
			$arr = array_filter( $arr );

			/** Combine results. */
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$result = array_merge( $result, $arr );

		}

		/** Fix broken html of each part. */
		foreach ( $result as &$el ) {
			$el = XMLHelper::get_instance()->repair_html( $el );
		}
		unset( $el );

		/** Remove empty elements. */
		$result = array_filter( $result );

		return $result;

	}

	/**
	 * Return post/page content by ID with executed shortcodes.
	 *
	 * @param $post_id - ID of the Post/Page content from which we will parse.
	 * @param string $template
	 *
	 * @return array|mixed|object
	 **@since 3.0.0
	 * @access public
	 *
	 */
	private function parse_post_content( $post_id, $template = null ) {

		$url = $this->get_frontend_url( $post_id, $template );

		/** Get page content */
		$response = wp_remote_get(
			$url,
			array(
				'sslverify' => false,
				'timeout'   => 30,
			)
		);

		/** Throw error message */
		if ( is_wp_error( $response ) ) {

			$return = [
				'success' => false,
				'message' => esc_html__( 'Error connecting to', 'penci-text-to-speech' ) . ' ' . $url . ' ' . $response->get_error_message() . ' (' . $response->get_error_code() . ')',
			];
			wp_send_json( $return );

		}

		/** Get post content ot throw an error */
		$html = wp_remote_retrieve_body( $response );
		if ( $html === '' ) {

			$response_code = wp_remote_retrieve_response_code( $response );
			$return        = [
				'success' => false,
				'message' => esc_html__( 'Failed to get content due to an error:', 'penci-text-to-speech' ) . 'HTTP: ' . $response_code . ' URL: ' . $url
			];
			wp_send_json( $return );

		}

		/** Remove figcaption */
		if ( ! get_theme_mod( 'penci_texttospeech_read_figcaption' ) ) {

			$figcaption_pattern = '/(<figcaption)+.+(<\/figcaption>)/';
			$html               = preg_replace( $figcaption_pattern, '', $html );

		}

		return apply_filters( 'penci_tts_parse_post_content', $html );

	}

	/**
	 * Return frontend url with post content to parse.
	 *
	 * @param int $post_id - ID of the Post/Page content from which we will parse.
	 * @param string $template
	 *
	 * @return string
	 **@since  3.0.0
	 * @access public
	 *
	 */
	private function get_frontend_url( $post_id, $template ) {

		/** Get full permalink for the current post. */
		$url = get_permalink( $post_id );

		/** Returns a string if the URL has parameters or NULL if not. */
		$query = parse_url( $url, PHP_URL_QUERY );

		/** Add penci-tts param to URL. */
		if ( $query ) {

			$url .= '&penci-tts=1';

		} else {

			$url .= '?penci-tts=1';

		}

		/** Add template param to url. */
		if ( $template ) {

			$url .= '&penci_tts_template=' . $template;

		}

		return $url;

	}

	/**
	 * Remove muted elements by class "penci-tts-mute" or attribute penci-tts-mute="".
	 *
	 * @param $post_content - Post/Page content.
	 *
	 * @return string
	 **@since 2.0.0
	 * @access public
	 *
	 */
	public function remove_muted_html( $post_content ) {

		/** Hide DOM parsing errors. */
		libxml_use_internal_errors( true );
		libxml_clear_errors();

		/** Load the possibly malformed HTML into a DOMDocument. */
		$dom          = new DOMDocument();
		$dom->recover = true;
		$dom->loadHTML( '<?xml encoding="UTF-8"><body id="repair">' . $post_content . '</body>' ); // input UTF-8.

		$selector = new DOMXPath( $dom );

		/** Remove all elements with penci-tts-mute="" attribute. */
		foreach ( $selector->query( '//*[@penci-tts-mute]' ) as $e ) {
			$e->parentNode->removeChild( $e );
		}

		/** Remove all elements with class="penci-tts-mute". */
		foreach ( $selector->query( '//*[contains(attribute::class, "penci-tts-mute")]' ) as $e ) {
			$e->parentNode->removeChild( $e );
		}

		/* Exclude HTML Class */
		$exclude_tags = [
			'.penci-ilrelated-posts',
			'.penci-ilrltpost-beaf',
			'.post-tags',
			'.penci-single-link-pages',
			'.d-none'
		];
		if ( get_theme_mod( 'penci_texttospeech_excluded_html' ) ) {
			$exclude_tags = array_merge( $exclude_tags, explode( ',', get_theme_mod( 'penci_texttospeech_excluded_html' ) ) );
		}
		foreach ( $exclude_tags as $tag ) {
			$tag_name = substr( $tag, 1 );
			if ( '.' === substr( $tag, 0, 1 ) ) {
				$found = $selector->query( '//*[contains(attribute::class, "' . $tag_name . '")]' );
			} else {
				$found = $selector->query( '//*[contains(attribute::id, "' . $tag_name . '")]' );
			}
			foreach ( $found as $e ) {
				$e->parentNode->removeChild( $e );
			}
		}

		/** HTML without muted tags. */
		$body = $dom->documentElement->lastChild;

		return trim( XMLHelper::get_instance()->get_inner_html( $body ) );

	}

	/**
	 * Return Player code for Frontend.
	 *
	 * @param int $id - Post/Page id.
	 *
	 * @return false|string
	 **@since 1.0.0
	 * @access public
	 *
	 */
	public function get_player( $id = 0, $preview = false, $class = 'customizer' ) {

		/** Show player if we have audio. */
		if ( ! $this->audio_exists( $id ) && ! $preview ) {
			return false;
		}

		/** Don't show player if we parse content. */
		if ( isset( $_GET['penci-tts'] ) ) {
			return false;
		}

		/** Don't show player if in Speech Template Editor. */
		if ( isset( $_GET['penci_tts_template'] ) && 'penci-text-to-speech' === $_GET['penci_tts_template'] ) {
			return false;
		}

		/** Prepare variables for render */
		$audio_url = $this->get_audio_url( $id );

		if ( $preview ) {
			$audio_url = 'https://file-examples.com/wp-content/uploads/2017/11/file_example_MP3_700KB.mp3';
		}

		$loop     = get_theme_mod( 'penci_texttospeech_loop' ) ? 'loop="on" ' : ' ';
		$autoplay = get_theme_mod( 'penci_texttospeech_autoplay' ) ? 'autoplay="on" ' : ' ';
		$download = ! in_array( get_theme_mod( 'penci_texttospeech_link' ), [ 'none', 'backend' ] );
		$preload  = get_theme_mod( 'penci_texttospeech_preload' ) !== 'backend' ? ' preload="' . get_theme_mod( 'penci_texttospeech_preload' ) . '"' : '';

		$speeds = preg_split( "/[\s,]+/", get_theme_mod( 'penci_texttospeech_speed', '0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75' ) );
		$speeds = array_filter( $speeds );

		// Get audio duration from backend
		$length_formatted = 0;
		if ( get_theme_mod( 'penci_texttospeech_preload' ) === 'backend' ) {

			$length_formatted = $this->get_audio_meta( $id, 'length_formatted' );

		}

		$classes = ' ' . get_theme_mod( 'penci_texttospeech_position', 'before-content' ) . ' ';
		$classes .= ' ' . get_theme_mod( 'penci_texttospeech_style', 'style-4' ) . ' ';
		$classes .= ' ' . get_theme_mod( 'penci_texttospeech_bgcolor' ) ? 'custombg' : 'default-bg' . ' ';
		$classes = trim( $classes );

		$out = '<div class="penci-texttospeech-wrapper ' . $class . ' pc-ttp-s-' . get_theme_mod( 'penci_texttospeech_style', 'style-4' ) . ' pc-ttp-' . get_theme_mod( 'penci_texttospeech_position', 'before-content' ) . '">';
		if ( get_theme_mod( 'penci_texttospeech_before_player_switcher' ) ) {
			$out .= wp_kses_post( get_theme_mod( 'penci_texttospeech_before_player_switcher' ) );
		}
		$download_class = '';
		if ( get_theme_mod( 'penci_texttospeech_preload' ) === 'backend' ) {
			$download_class = wp_sprintf(
				' data-length-formatted="%s"',
				esc_attr( $length_formatted )
			);
		}
		$out .= '<div class="penci-texttospeech-box ' . esc_attr( $classes ) . '">';

		$out .= '<div data-download="' . esc_attr( $download ) . '" ' . $download_class . '>';
		$out .= do_shortcode( '[audio features="speed" src="' . $audio_url . '" ' . $autoplay . $loop . $preload . ']' );
		$out .= '</div>';
		$out .= '</div>';

		if ( in_array( get_theme_mod( 'penci_texttospeech_link' ), [
			'frontend',
			'backend-and-frontend'
		] ) ) :

			$out .= wp_sprintf(
				'<p class="penci-texttospeech-download-box">' . __( 'Download:', 'soledad' ) . ' <a href="%1$s" download="" title="Download: %2$s">%2$s</a></p>',
				esc_url( $audio_url ),
				get_the_title( $id )
			);

		endif;

		if ( get_theme_mod( 'penci_texttospeech_after_player_switcher' ) ) {
			$out .= wp_kses_post( get_theme_mod( 'penci_texttospeech_after_player_switcher' ) );
		}

		$out .= '</div>';

		return $out;

	}

	/**
	 * Get audio meta
	 *
	 * @param $id
	 * @param $key
	 *
	 * @return false|void
	 */
	public function get_audio_meta( $id, $key ) {

		if ( ! is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$audio_path = $this->get_audio_path( $id );
		$audio_meta = wp_read_audio_metadata( $audio_path );

		if ( ! is_array( $audio_meta ) ) {
			return false;
		}

		return $audio_meta[ $key ] ?? false;

	}

	/**
	 * Return URL to audio version of the post.
	 *
	 * @param int $id - Post/Page id.
	 *
	 * @return bool|string
	 * @since 3.0.0
	 * @access public
	 *
	 */
	public function get_audio_url( int $id = 0 ) {

		/** If audio file not exist. */
		$f_time = $this->audio_exists( $id );
		if ( ! $f_time ) {
			return false;
		}

		/** Current post ID. */
		if ( ! $id ) {

			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$id = get_the_ID();

			if ( ! $id ) {
				return false;
			}

		}

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_baseurl = $upload_dir['baseurl'];

		/** URL to post audio file. */
		$audio_url = $upload_baseurl . '/penci-text-to-speech/post-' . $id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		/** Cache Busting. '.mp3' is needed. */
		$audio_url .= '?cb=' . $f_time . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		return $audio_url;
	}

	/**
	 * Return path to audio version of the post.
	 *
	 * @param int $id
	 *
	 * @return false|string
	 */
	public function get_audio_path( int $id = 0 ) {

		/** If audio file not exist. */
		$f_time = $this->audio_exists( $id );
		if ( ! $f_time ) {
			return false;
		}

		/** Current post ID. */
		if ( ! $id ) {

			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$id = get_the_ID();

			if ( ! $id ) {
				return false;
			}

		}

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_baseurl = $upload_dir['basedir'];

		/** URL to post audio file. */
		return $upload_baseurl . '/penci-text-to-speech/post-' . $id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

	}

	/**
	 * Checks if there is audio for the current post.
	 *
	 * @param int $id - Post/Page id.
	 *
	 * @return bool|false|int
	 * @since 3.0.0
	 * @access public
	 **/
	public function audio_exists( $id = 0 ) {


		/** Current post ID. */
		if ( ! $id ) {

			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$id = get_the_ID();

			if ( ! $id ) {
				return false;
			}

		}

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_basedir = $upload_dir['basedir'];

		/** Path to post audio file. */
		$audio_file = $upload_basedir . '/penci-text-to-speech/post-' . $id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		/** True if we have audio. */
		if ( file_exists( $audio_file ) ) {
			return filemtime( $audio_file );
		}

		return false;
	}

	/**
	 * Add player code to page.
	 *
	 * @return void
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function add_player() {

		/** Add player before/after Title. */
		$position = get_theme_mod( 'penci_texttospeech_position', 'before-content' );

		add_action( 'penci_before_main_post_title', function () {
			if ( 'before-title' == get_theme_mod( 'penci_texttospeech_position' ) ) {
				echo $this->get_player();
			}
		} );

		add_action( 'penci_after_main_post_title', function () {
			if ( 'after-title' == get_theme_mod( 'penci_texttospeech_position' ) ) {
				echo $this->get_player();
			}
		} );

		add_filter( 'the_content', [ $this, 'add_player_to_content' ], 9999 );

	}

	/**
	 * Add player before/after Content and Top/Bottom Fixed.
	 *
	 * @param $content - Post/Page content.
	 *
	 * @return string
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function add_player_to_content( $content ) {

		$position = get_theme_mod( 'penci_texttospeech_position' );

		if ( ! in_array( $position, [
			'before-content',
			'after-content',
			'top-fixed',
			'bottom-fixed'
		] ) ) {
			return $content;
		}

		/** Check if we are in the loop and work only with selected post types. */
		if ( in_the_loop() && ! ( is_singular( get_theme_mod( 'penci_texttospeech_enabled_post_types' ) ) ) ) {
			return $content;
		}


		/** Add player only Once. */
		if ( strpos( $content, 'class="penci-texttospeech-wrapper"' ) !== false ) {
			return $content;
		}

		$player = $this->get_player();

		if ( $position == 'before-content' ) {
			return $player . $content;
		} else {
			return $content . $player;
		}
	}

	/**
	 * Render Player code for MetaBox in the admin area.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function the_player() {

		/** Show player if we have audio. */
		$f_time = $this->audio_exists();
		if ( ! $f_time ) {
			return;
		}

		/** URL to post audio file. */
		$audio_url = $this->get_audio_url();

		$download = ! in_array( get_theme_mod( 'penci_texttospeech_link' ), [ 'none', 'backend' ] );

		?>
        <div class="penci-texttospeech-box <?php echo esc_attr( get_theme_mod( 'penci_texttospeech_style', 'style-4' ) ); ?>"
             style="background: <?php echo esc_attr( get_theme_mod( 'penci_texttospeech_bgcolor' ) ); ?>">
            <div data-download="<?php echo esc_attr( $download ); ?>">
				<?php echo do_shortcode( '[audio src="' . $audio_url . '" preload="metadata"]' ); ?>
            </div>
        </div>
        <div class="penci-texttospeech-audio-info">
			<?php if ( in_array( get_theme_mod( 'penci_texttospeech_link' ), [
				'backend',
				'backend-and-frontend'
			] ) ) : ?>
                <span class="dashicons dashicons-download"
                      title="<?php esc_html_e( 'Download Audio', 'penci-text-to-speech' ); ?>"></span>
                <a href="<?php echo esc_url( $audio_url ); ?>"
                   download=""><?php esc_html_e( 'Download Audio', 'penci-text-to-speech' ); ?></a><br>
			<?php endif; ?>
            <span class="dashicons dashicons-clock"
                  title="<?php esc_html__( 'Date of Creation', 'penci-text-to-speech' ) ?>"></span>
			<?php echo date( "F d Y H:i:s", $f_time ); ?>
        </div>
		<?php

	}

	/**
	 * Remove audio on remove post record.
	 *
	 * @param $post_id - The post id that is being deleted.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function before_delete_post( $post_id ) {

		/** If we don't have audio then nothing to delete. */
		if ( ! $this->audio_exists( $post_id ) ) {
			return;
		}

		$this->remove_audio_by_id( $post_id );

	}

	/**
	 * Remove Audio by ID.
	 *
	 * @param $id - The post id from which we delete audio.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_audio_by_id( $id ) {

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_basedir = $upload_dir['basedir'];

		/** Path to post audio file. */
		$audio_file = $upload_basedir . '/penci-text-to-speech/post-' . $id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		/** Remove audio file. */
		wp_delete_file( $audio_file );

	}

	/**
	 * Ajax Remove Audio action hook here.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_audio() {

		/** Get plugin settings. */

		/** Security Check. */
		check_ajax_referer( 'penci-tts-nonce', 'nonce' );

		/** Current post ID. */
		$post_id = (int) $_POST['post_id'];

		/** Get path to upload folder. */
		$upload_dir     = wp_get_upload_dir();
		$upload_basedir = $upload_dir['basedir'];

		/** Path to post audio file. */
		$audio_file = $upload_basedir . '/penci-text-to-speech/post-' . $post_id . '.' . get_theme_mod( 'penci_texttospeech_audio_format', 'mp3' );

		/** Remove audio file. */
		wp_delete_file( $audio_file );

		echo 'ok';

		wp_die();

	}

	/**
	 * Ajax Create Audio action hook here.
	 *
	 * @return void
	 **@since  3.0.0
	 * @access public
	 *
	 */
	public function gspeak() {

		/** Security Check. */
		check_ajax_referer( 'penci-tts-nonce', 'nonce' );

		/** Current post ID. */
		$post_id = (int) $_POST['post_id'];

		/** Get Speech Template ID. */
		$stid = filter_input( INPUT_POST, 'stid' );
		$stid = $stid ? $stid : 'content';

		/** Create audio version of post. */
		if ( $this->voice_acting( $post_id, $stid ) ) {

			$return = [
				'success' => true,
				'message' => esc_html__( 'Audio Generated Successfully', 'penci-text-to-speech' )
			];

		} else {

			$return = [
				'success' => false,
				'message' => esc_html__( 'An error occurred while generating the audio.', 'penci-text-to-speech' )
			];

		}


		wp_send_json( $return );

	}

	/**
	 * Let me speak. Create audio version of post.
	 *
	 * @param int $post_id
	 * @param string $stid
	 * @param array $html_parts
	 *
	 * @return boolean
	 **@since  3.0.0
	 * @access public
	 *
	 */
	public function voice_acting( $post_id = 0, $stid = 'content', $html_parts = array() ) {

		if ( 'content' === $stid ) {

			/** Prepare parts for generate audio for whole post content. */
			$parts = $this->content_based_generation( $post_id );

		} elseif ( 'custom-content' === $stid ) {

			/** @var array Custom content generation $parts */
			$parts = $html_parts;

		}

		/** Create audio file for each part. */
		$audio_parts = [];
		foreach ( $parts as $part ) {

			try {

				/** Convert HTML to temporary audio file. */
				$audio_parts[] = $this->part_speak( $part, $post_id );

			} catch ( ApiException $e ) {

				/** Show error message. */
				echo esc_html__( 'Caught exception: ' ) . $e->getMessage() . "\n";

			}

		}

		/** Combine multiple files to one. */
		$this->glue_audio( $audio_parts, $post_id );

		return true;

	}

	private function clean_content( $post_content ) {

		/** Remove <script>...</script>. */
		$post_content = preg_replace( '/<\s*script.+?<\s*\/\s*script.*?>/si', ' ', $post_content );

		/** Remove <style>...</style>. */
		$post_content = preg_replace( '/<\s*style.+?<\s*\/\s*style.*?>/si', ' ', $post_content );

		/** Trim, replace tabs and extra spaces with single space. */
		$post_content = preg_replace( '/[ ]{2,}|[\t]/', ' ', trim( $post_content ) );

		/** Remove muted elements by class "penci-tts-mute" or attribute penci-tts-mute="". */
		$post_content = $this->remove_muted_html( $post_content );

		/** Convert data attributes to the SSML markup */
		if ( class_exists( 'PenciTextToSpeechUtilities' ) ) {
			$post_content = PenciTextToSpeechUtilities::get_instance()->apply_ssml_attributes( $post_content );
		}

		/** Prepare HTML to splitting. */
		$post_content = XMLHelper::get_instance()->clean_html( $post_content );

		return $post_content;

	}

	/**
	 * Regex content replacement
	 *
	 * @param $post_content
	 *
	 * @return mixed|null
	 */
	private function regex_content_replace( $post_content ) {

		$regex = get_theme_mod( 'penci_texttospeech_regex' );

		/** Apply regex replacement */
		if ( $regex ) {

			$expressions = preg_split( "/\r\n|\n|\r/", $regex );

			foreach ( $expressions as $i => $exp ) {

				if ( ! ( $i % 2 == 0 ) ) {
					$post_content = preg_replace( $expressions[ $i - 1 ], $exp, $post_content );
				}

			}

		}

		return apply_filters( 'penci_tts_after_content_regex_replace', $post_content );

	}

	public function get_string_between( $string, $start, $end ) {

		$string = ' ' . $string;
		$ini    = strpos( $string, $start );
		if ( $ini === 0 ) {
			return '';
		}

		$ini += strlen( $start );
		$len = strpos( $string, $end, $ini ) - $ini;

		return substr( $string, $ini, $len );

	}

	/**
	 * Prepare parts for generate audio for whole post content.
	 *
	 * @param int $post_id
	 *
	 * @return array
	 **@since  3.0.0
	 * @access public
	 *
	 */
	private function content_based_generation( $post_id ) {

		/**
		 * Get Current post Content.
		 * Many shortcodes do not work in the admin area so we need this trick.
		 * We open frontend page in custom template and parse content.
		 **/
		$post_content = $this->parse_post_content( $post_id, 'penci-text-to-speech' );

		/** Get only content part from full page. */
		$post_content = $this->get_string_between( $post_content, '<div class="penci-texttospeech-content-start"></div>', '<div class="penci-texttospeech-content-end"></div>' );

		/**
		 * Filters the post content before any manipulation.
		 *
		 * @param string $post_content Post content.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$post_content = apply_filters( 'penci_tts_before_content_manipulations', $post_content, $post_id );

		$post_content = $this->regex_content_replace( $post_content );

		$post_content = $this->clean_content( $post_content );

		/**
		 * Filters the post content before split to parts by 4500 chars.
		 *
		 * @param string $post_content Post content.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$post_content = apply_filters( 'penci_tts_before_content_dividing', $post_content, $post_id );

		/** If all content is bigger than the quota. */
		$parts[] = $post_content;
		if ( strlen( $post_content ) > 4500 ) {

			/**
			 * Split to parts about 4500. Google have limits Total characters per request.
			 * See: https://cloud.google.com/text-to-speech/quotas
			 **/
			$parts = $this->great_divider( $post_content, 4500 );

		}

		/**
		 * Filters content parts before voice_divider.
		 *
		 * @param string $parts Post content parts.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$parts = apply_filters( 'penci_tts_before_voice_divider', $parts, $post_id );

		/** Divide parts by voice. One part voiced by one voice */
		$parts = $this->voice_divider( $parts );

		/**
		 * Filters content parts before adding watermarks.
		 *
		 * @param string $parts Post content parts.
		 * @param int $post_id Post ID.
		 **@since 3.0.0
		 *
		 */
		$parts = apply_filters( 'penci_tts_before_adding_watermarks', $parts, $post_id );

		/** Add custom text before/after audio. */
		$parts = $this->add_watermark( $parts );

		return $parts;

	}

	/**
	 * Display structured data
	 * @return void
	 */
	public function structured_data() {

		$post_id = get_the_ID();
		/** Exit on the Category and Tag pages */
		if ( is_archive() || is_404() || is_category() || is_tag() || is_attachment() ) {
			return;
		}

		/** Exit is post type not listed in settings */
		if ( ! is_singular( get_theme_mod( 'penci_texttospeech_enabled_post_types' ) ) ) {
			return;
		}

		/** Show ld+json Markup if it is enabled in settings. */
		if ( get_theme_mod( 'penci_texttospeech_schema' ) ) {
			return;
		}

		/** Return if post have no one audio file and schema for all posts is disabled */
		if ( ! $this->audio_exists( $post_id ) ) {
			return;
		}

		$json_ld = '{
                        "@context": "https://schema.org/",
                        "@type": "[type]",
                        "name": "[title]",
                        "speakable":
                            {
                                "@type": "SpeakableSpecification",
                                "cssSelector": [
                                    "main h1",
                                    ".entry-content > p:first-of-type"
                                ]
                            },
                        "url": "[permalink]"
                    }';


		/** Get post info */
		$type      = get_post_type( $post_id ) === 'page' ? 'WebPage' : 'Article';
		$title     = get_the_title( $post_id );
		$permalink = get_permalink( $post_id );

		/** Apply variables replacements. */
		$json_ld = str_replace( [ '[title]', '[type]', '[permalink]' ], [ $title, $type, $permalink ], $json_ld );

		/** Output ld+json Markup. */
		printf( '<script id="penci-texttospeech-speakable" type="application/ld+json">%s</script>', $json_ld );

	}

	public static function penci_tts_page_template( $template ) {

		/** Change template for correct parsing content. */
		if ( isset( $_GET['penci_tts_template'] ) && 'penci-text-to-speech' === $_GET['penci_tts_template'] ) {

			/** Disable admin bar. */
			show_admin_bar( false );

			$template = PenciTextToSpeech::$path . 'inc/PenciDesign/speaker-template.php';

		}

		return $template;

	}

	/**
	 * Main SpeechCaster Instance.
	 *
	 * Insures that only one instance of SpeechCaster exists in memory at any one time.
	 *
	 * @static
	 * @return SpeechCaster
	 * @since 3.0.0
	 **/
	public static function get_instance() {

		/** @noinspection SelfClassReferencingInspection */
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SpeechCaster ) ) {

			/** @noinspection SelfClassReferencingInspection */
			self::$instance = new SpeechCaster;

		}

		return self::$instance;

	}

} // End Class SpeechCaster.
