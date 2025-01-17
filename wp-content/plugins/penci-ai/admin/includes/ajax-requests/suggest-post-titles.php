<?php

namespace PenciAIContentGenerator\AjaxRequests;

class SuggestPostTitles {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_penciai_suggest_post_titles", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();
		$main_title = isset( $_POST['main_title'] ) && ! empty( $_POST['main_title'] ) ? sanitize_text_field( $_POST['main_title'] ) : '';
		if ( ! empty( get_theme_mod( 'penci_ai_api_key' ) ) ) {
			$ai = new \OpenAIAPI( get_theme_mod( 'penci_ai_api_key' ) );
			$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
			$ai->setModel( $default_model );


			$data = array(
				'prompt'            => 'Transform the following title into 5 unique SEO-optimized title : "' . $main_title . '".',
				'temperature'       => intval( get_theme_mod( 'penci_ai_temperature' ) ),
				'max_tokens'        => 2000,
				'frequency_penalty' => 0,
				'presence_penalty'  => 0,
			);

			$response = $ai->complete( $data );


			$str = "";
			if ( isset( $response ) && ! empty( $response ) && penciai_is_json( $response ) ) {
				$json = json_decode( $response );

				if ( isset( $json->choices ) ) {
					$str = penciai_remove_first_br( $json->choices[0]->text );
				} else {
					$hasError = $this->ajax->is_response_has_error( $json );
					if ( $hasError !== false ) {
						wp_send_json_error( $hasError );
					} else {
						wp_send_json_error( "__something_went_wrong__" );
					}
				}

			}

			wp_send_json_success( $str );

		}

		wp_die();

	}
}
