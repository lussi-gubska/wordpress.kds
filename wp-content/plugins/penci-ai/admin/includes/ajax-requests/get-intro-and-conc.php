<?php

namespace PenciAIContentGenerator\AjaxRequests;

class GetIntroAndConc {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_get_intro_and_conc", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();
		$ai = new \OpenAIAPI( get_theme_mod( 'penci_ai_api_key' ) );
		$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
		$ai->setModel( $default_model );

		$lang      = isset( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ) : "en";
		$lang_name = isset( $_POST['lang_name'] ) ? sanitize_text_field( $_POST['lang_name'] ) : "English";

		if ( ! empty( get_theme_mod( 'lang_' . $lang ) ) ) {
			wp_send_json_success( esc_html( get_theme_mod( 'lang_' . $lang ) ) );
			wp_die();
		}
		$data = array(
			'prompt' => 'in the ' . $lang . ' language, what word is most used for "Introduction" and "Conclusion", output in js array.',
		);

		$response = $ai->complete( $data );
		if ( penciai_is_json( $response ) ) {
			$json = json_decode( $response );
			if ( isset( $json->choices ) ) {
				$text    = $json->choices[0]->text;
				$text    = str_replace( "\n", '<br>', $text );
				$content = $text;
				//$ai->setResponse(esc_html($content));
				wp_send_json_success( esc_html( $content ) );
			} else {
				$obj      = json_decode( $response );
				$hasError = $this->ajax->is_response_has_error( $obj );
				if ( $hasError !== false ) {
					wp_send_json_error( $hasError );
				} else {
					wp_send_json_error( "__something_went_wrong__" );
				}
			}

		} else {
			wp_send_json_error( '__something_went_wrong__' );
		}
		var_dump( $response );
		wp_die();

	}
}
