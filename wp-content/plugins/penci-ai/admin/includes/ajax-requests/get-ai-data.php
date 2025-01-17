<?php

namespace PenciAIContentGenerator\AjaxRequests;

class getAIData {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( 'wp_ajax_penci_ai_ai_data', array( $this, 'ajax' ) );
	}

	public function ajax() {
		\penciai_checkNonce();

		@ini_set( 'zlib.output_compression', 0 );
		@ini_set( 'implicit_flush', 1 );
		@ob_end_clean();

		header( 'Content-Type: text/event-stream' );

		require PENCI_AI_DIR_PATH . 'admin/includes/content-switches.php';

		$switch = new \PENCI_AI_Content_Switches();

		if ( ! empty( get_theme_mod( 'penci_ai_api_key' ) ) ) {
			$ai = new \OpenAIAPI( get_theme_mod( 'penci_ai_api_key' ) );
			$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
			$ai->setModel( $default_model );

			$prompt       = isset( $_POST['prompt'] ) ? sanitize_text_field( $_POST['prompt'] ) : '';
			$type         = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
			$first_prompt = isset( $_POST['first_prompt'] ) ? sanitize_text_field( $_POST['first_prompt'] ) : '';

			$middle_prompt = $prompt;
			if ( $type == 'call_to_action' && ! empty( $first_prompt ) ) {
				$middle_prompt = $first_prompt;
			}
			if ( $type == 'what_language' ) {
				$middle_prompt = '"' . $middle_prompt . '"';
			}

			$temperature       = isset( $_POST['temperature'] ) ? sanitize_text_field( $_POST['temperature'] ) : 0.8;
			$max_tokens        = isset( $_POST['max-tokens'] ) ? sanitize_text_field( $_POST['max-tokens'] ) : 2000;
			$top_p             = isset( $_POST['top-p'] ) ? sanitize_text_field( $_POST['top-p'] ) : 1;
			$best_of           = isset( $_POST['best-of'] ) ? sanitize_text_field( $_POST['best-of'] ) : 1;
			$frequency_penalty = isset( $_POST['frequency-penalty'] ) ? sanitize_text_field( $_POST['frequency-penalty'] ) : 0;
			$presence_penalty  = isset( $_POST['presence-penalty'] ) ? sanitize_text_field( $_POST['presence-penalty'] ) : 0;

			$data = array(
				'prompt'            => $switch->startingprompt() . $middle_prompt . $switch->endingprompt(),
				'temperature'       => floatval( $temperature ),
				'max_tokens'        => (int) $max_tokens,
				'top_p'             => (int) $top_p,
				'best_of'           => (int) $best_of,
				'frequency_penalty' => (int) $frequency_penalty,
				'presence_penalty'  => (int) $presence_penalty,
				'stream'            => false,
			);

			$e = $ai->complete( $data );

			if ( penciai_is_json( $e ) ) {
					$obj = json_decode( $e );
				if ( isset( $obj->choices ) ) {
					$text = $obj->choices[0]->text;
					$text = str_replace( "\n", '<br>', $text );
					echo esc_html( $text );
				}
				if ( isset( $obj->error ) ) {
					if ( $obj->error->code == 'invalid_api_key' ) {
						echo '__invalid_api_key__';
					} elseif ( $obj->error->type == 'insufficient_quota' ) {
						echo '__insufficient_quota__';
					} elseif ( $obj->error->type == 'server_error' ) {
						echo '__server_error__';
					} else {
						wp_send_json_error( $obj );
					}
				}
			}
		} else {
			echo '__api-empty__';
		}

		wp_die();
	}
}
