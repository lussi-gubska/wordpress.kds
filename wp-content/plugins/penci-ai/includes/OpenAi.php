<?php

class OpenAIAPI {
	private $api_key;
	private $model;
	private $stream_method;
	public $response;

	public function __construct( $api_key ) {
		$this->api_key = $api_key;

		add_action( 'http_api_curl', array( $this, 'filterCurlForStream' ) );
	}

	public function setModel( $model ) {
		$this->model = $model;
	}

	public function setResponse( $content = '' ) {
		$this->response = $content;
	}

	public function filterCurlForStream( $handle ) {
		if ( $this->stream_method !== null ) {
			curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt(
				$handle,
				CURLOPT_WRITEFUNCTION,
				function ( $curl_info, $data ) {
					return call_user_func( $this->stream_method, $data, $this );
				}
			);
		}
	}

	private function sendRequest( $endpoint, $data ) {
		$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
		if ( $endpoint == 'completions' ) {
			$url           = 'https://api.openai.com/v1/completions';
			$data['model'] = $default_model;
		} elseif ( $endpoint == 'image' ) {
			$url = 'https://api.openai.com/v1/images/generations';
		}

		$stream = false;
		if ( isset( $data['stream'] ) && $data['stream'] == true ) {
			$stream = true;
		}

		$options = array(
			'method'    => 'POST',
			'timeout'   => 1200,
			'headers'   => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->api_key,
			),
			'body'      => wp_json_encode( $data ),
			'sslverify' => false,
			'stream'    => $stream,
		);

		$response = wp_remote_request( $url, $options );

		if ( $stream ) {
			return $this->response;
		} else {
			return wp_remote_retrieve_body( $response );
		}
	}

	public function listModels() {
		$response = $this->sendRequest( 'models', array() );

		return $response;
	}

	public function retrieveModel( $model_id ) {
		$response = $this->sendRequest( 'models/' . $model_id, array() );

		return $response;
	}

	public function complete( $data, $stream = null ) {
		if ( isset( $data['stream'] ) && $data['stream'] == true ) {
			$this->stream_method = $stream;
		}

		$response = $this->sendRequest( 'completions', $data, $stream );

		return $response;
	}

	public function image( $data, $stream = null ) {
		$this->stream_method = $stream;
		$response            = $this->sendRequest( 'image', $data );

		return $response;
	}
}
