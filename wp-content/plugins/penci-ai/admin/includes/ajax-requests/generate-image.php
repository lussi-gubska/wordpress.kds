<?php

namespace PenciAIContentGenerator\AjaxRequests;

use Ferranfg\MidjourneyPhp\Midjourney;

class GenerateImage {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_penciai_generate_image", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();
		$prompt      = isset( $_POST['prompt'] ) ? sanitize_text_field( $_POST['prompt'] ) : "";
		$image_size  = isset( $_POST['image-size'] ) ? sanitize_text_field( $_POST['image-size'] ) : "medium";
		$return_both = isset( $_POST['return_both'] ) ? sanitize_key( $_POST['return_both'] ) : false;
		$engine      = get_theme_mod( 'penci_ai_img_type', 'dall_e' );

		$image_size_ = '512x512';
		if ( $image_size == 'thumbnail' ) {
			$image_size_ = '256x256';
		} elseif ( $image_size == 'medium' ) {
			$image_size_ = '512x512';
		} elseif ( $image_size == 'large' ) {
			$image_size_ = '1024x1024';
		}

		if ( ! empty( get_theme_mod( 'penci_ai_api_key' ) ) && 'dall_e' == $engine ) {
			$ai = new \OpenAIAPI( get_theme_mod( 'penci_ai_api_key' ) );
			$default_model = get_theme_mod( 'penci_ai_model', 'gpt-3.5-turbo-instruct' );
			$ai->setModel( $default_model );
			$image_experiments = isset( $_POST['image_experiments'] ) && is_array( $_POST['image_experiments'] ) ? array_keys( $_POST['image_experiments'] ) : array();
			$image_experiments = array_map( 'sanitize_text_field', $image_experiments );

			if ( empty( $image_experiments ) ) {
				$image_experiments = get_theme_mod( 'image_experiments', array() );
			}

			$image_styles = '';
			if ( ! empty( $image_experiments ) ) {
				$image_styles = implode( ', ', $image_experiments );
				$image_styles = ' | ' . rtrim( $image_styles, ',' ) . '.';
			}

			$image_styles = str_replace( array( 'four_k', 'eight_k', '_' ), array( "4K", "8K", " " ), $image_styles );

			$data     = array(
				'prompt' => $prompt . $image_styles,
				'n'      => 1,
				'size'   => $image_size_,
			);
			$response = $ai->image( $data );

			$url = $media_url = "";
			if ( penciai_is_json( $response ) ) {
				$json = json_decode( $response );
				if ( isset( $json->data ) && isset( $json->data[0] ) ) {
					$url = $json->data[0]->url;

					$media_url = penciai_upload_image_to_media_gallery( $url, $return_both );
				}
			}


			wp_send_json_success( $media_url );

		} elseif ( 'midjourney' == $engine ) {
			$img_lists          = [];
			$discord_channel_id = get_theme_mod( 'penci_ai_discord_channel_id' );
			$discord_user_token = get_theme_mod( 'penci_ai_discord_user_token' );

			if ( ! $discord_channel_id || ! $discord_user_token ) {
				wp_send_json_error( 'API key is empty, please enter the API key on the settings panel first.' );
			}
			$midjourney = new Midjourney( $discord_channel_id, $discord_user_token );
			$message    = $midjourney->generate( $prompt );

			if ( isset( $message->upscaled_photo_url ) ) {
				$url       = $message->upscaled_photo_url;
				$media_url = penciai_upload_image_to_media_gallery( $url, $return_both );
				wp_send_json_success( $media_url );
			} else {
				wp_send_json_error( 'Can\'t get data from Discord channel.', 'soledad' );
			}
		} elseif ( get_theme_mod( 'penci_ai_img_api_key' ) ) {

			$verison_api = [
				'open_journey'           => '9936c2001faa2194a261c01381f90e65261879985476014a0a37a334593a05eb',
				'stable_diffusion'       => 'db21e45d3f7023abc2a46ee38a23973f6dce16bb082a930b0c49861f96d1e5bf',
				'text-to-pokemon'        => '3554d9e699e09693d3fa334a79c58be9a405dd021d3e11281256d53185868912',
				'anything-v3-better-vae' => '09a5805203f4c12da649ec1923bb7729517ca25fcac790e640eaa9ed66573b65',
				'anything-v4.0'          => '42a996d39a96aedc57b2e0aa8105dea39c9c89d9d266caf6bb4327a1c191b061',
				'text2image'             => '5c347a4bfa1d4523a58ae614c2194e15f2ae682b57e3797a5bb468920aa70ebf',
			];

			$headers = [
				'Authorization' => 'Token ' . get_theme_mod( 'penci_ai_img_api_key' ),
				'Content-Type'  => 'application/json',
			];

			$imgsize = explode( 'x', $image_size_ );

			$stableDiffusionResponse = wp_remote_post(
				'https://api.replicate.com/v1/predictions',
				[
					'headers' => $headers,
					'body'    => json_encode( [
						'version' => $verison_api[ $engine ],
						'input'   => [
							'prompt'      => $prompt,
							'num_outputs' => 1,
							'width'       => $imgsize[0],
							'height'      => $imgsize[1],
						]
					] ),
				],

			);
			$first_data              = json_decode( wp_remote_retrieve_body( $stableDiffusionResponse ), true );
			if ( isset( $first_data['id'] ) && $first_data['id'] ) {

				$go = true;
				do {
					$stableDiffusionResponse_img = wp_remote_get(
						'https://api.replicate.com/v1/predictions/' . $first_data['id'],
						[
							'headers' => $headers,
						],
					);
					$data                        = json_decode( wp_remote_retrieve_body( $stableDiffusionResponse_img ), true );

					if ( isset( $data['status'] ) && $data['status'] == 'succeeded' ) {

						foreach ( $data['output'] as $out_img ) {
							$media_url = penciai_upload_image_to_media_gallery( $out_img, $return_both );
							wp_send_json_success( $media_url );
						}

						$go = false;

					}
				} while ( $go );
			}

		} else {
			wp_send_json_error( 'Please enter the API key on the settings panel first.' );
		}

		wp_die();


	}
}

