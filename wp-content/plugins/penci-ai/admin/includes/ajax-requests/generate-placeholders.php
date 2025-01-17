<?php

namespace PenciAIContentGenerator\AjaxRequests;

class GeneratePlaceholders {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_generate_placeholders", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();

		if ( ! empty( get_bloginfo() ) ) {

			if ( ! empty( get_theme_mod( 'penci_ai_api_key' ) ) ) {
				$ai = new \OpenAIAPI( get_theme_mod( 'penci_ai_api_key' ) );
				$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
				$ai->setModel( $default_model );

				$lang    = get_locale();
				$in_lang = '';
				if ( ! empty( $lang ) && $lang != 'en_US' ) {
					if ( $lang == 'as' ) {
						$lang = 'Assamese';
					}
					$in_lang = ' in the "' . $lang . '" language.';
				}
				$data = array(
					'prompt'            => 'Write some related topic of "' . get_bloginfo() . '"' . $in_lang,
					'temperature'       => 0.3,
					'max_tokens'        => 2000, //short: 128 , medium: 128, long: 1000 (for topic detailes)
					'frequency_penalty' => 0,
					'presence_penalty'  => 0,
				);

				$response = $ai->complete( $data );


				$str = "";
				if ( isset( $response ) && ! empty( $response ) && penciai_is_json( $response ) ) {
					$str = penciai_remove_first_br( json_decode( $response )->choices[0]->text );
					$str = explode( "\n", $str );
					//$str = array_pop($str);
					$str = implode( ',', $str );
					$str = rtrim( $str, ',' );

					update_option( 'penciai-placeholders', $str );
				}

				wp_send_json_success( $str );

			} else {
				wp_send_json_error( 'API key is empty, please enter the API key on the settings panel first.' );
			}
		}

		wp_die();

	}
}
