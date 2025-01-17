<?php

namespace PenciAIContentGenerator\AjaxRequests;
class Replicate_API {
	private $api_url = 'https://api.replicate.com/v1';
	private $image_token;

	public function __construct() {
		$this->image_token = get_theme_mod( 'penci_ai_img_api_key' );
	}

	public function version_api( $engine ) {
		$verison_api = [
			'open_journey'           => '9936c2001faa2194a261c01381f90e65261879985476014a0a37a334593a05eb',
			'stable_diffusion'       => 'db21e45d3f7023abc2a46ee38a23973f6dce16bb082a930b0c49861f96d1e5bf',
			'text-to-pokemon'        => '3554d9e699e09693d3fa334a79c58be9a405dd021d3e11281256d53185868912',
			'anything-v3-better-vae' => '09a5805203f4c12da649ec1923bb7729517ca25fcac790e640eaa9ed66573b65',
			'anything-v4.0'          => '42a996d39a96aedc57b2e0aa8105dea39c9c89d9d266caf6bb4327a1c191b061',
			'text2image'             => '5c347a4bfa1d4523a58ae614c2194e15f2ae682b57e3797a5bb468920aa70ebf',
		];

		return $verison_api[ $engine ];
	}

	public function get_images( $engine, $prompt, $numberOfImages, $width, $height ) {
		$headers = [
			'Authorization' => 'Token ' . $this->image_token,
			'Content-Type'  => 'application/json',
		];

		$stableDiffusionResponse = wp_remote_post(
			$this->api_url . '/predictions',
			[
				'headers' => $headers,
				'body'    => json_encode( [
					'version' => self::version_api( $engine ),
					'input'   => [
						'prompt'      => $prompt,
						'num_outputs' => (int) $numberOfImages,
						'width'       => $width,
						'height'      => $height,
					]
				] ),
			],

		);
		$first_data              = json_decode( wp_remote_retrieve_body( $stableDiffusionResponse ), true );

		if ( isset( $first_data['id'] ) && $first_data['id'] ) {

			$go = true;
			do {
				$stableDiffusionResponse_img = wp_remote_get(
					$this->api_url . '/predictions/' . $first_data['id'],
					[
						'headers' => $headers,
					],
				);
				$data                        = json_decode( wp_remote_retrieve_body( $stableDiffusionResponse_img ), true );

				if ( isset( $data['status'] ) && $data['status'] == 'succeeded' ) {

					$img_lists = [];

					foreach ( $data['output'] as $out_img ) {
						$img_lists[] = [ 'url' => $out_img ];
					}

					wp_send_json_success( $img_lists, wp_remote_retrieve_response_code( $stableDiffusionResponse_img ) );

					$go = false;

				}
			} while ( $go );
		}
	}

	public function get_replicate_model_version( $model ) {
		$headers = [
			'Authorization' => 'Token ' . $this->image_token,
			'Content-Type'  => 'application/json',
		];

		$stableDiffusionResponse = wp_remote_post(
			$this->api_url . '/models/' . $model . '/versions',
			[
				'headers' => $headers,
			],

		);
		$data                    = json_decode( wp_remote_retrieve_body( $stableDiffusionResponse ), true );

		return $data['results'][0]['id'];

	}
}