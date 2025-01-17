<?php

namespace PenciAIContentGenerator\AjaxRequests;

class SaveMediaImageToMedia {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_penciai_save_image_to_media_library", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();
		$title            = isset( $_POST['title'] ) && ! empty( $_POST['title'] ) ? sanitize_textarea_field( $_POST['title'] ) : '';
		$alternative_text = isset( $_POST['alternative_text'] ) && ! empty( $_POST['alternative_text'] ) ? sanitize_textarea_field( $_POST['alternative_text'] ) : '';
		$caption          = isset( $_POST['caption'] ) && ! empty( $_POST['caption'] ) ? sanitize_textarea_field( $_POST['caption'] ) : '';
		$description      = isset( $_POST['description'] ) && ! empty( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
		$file_name        = isset( $_POST['file_name'] ) && ! empty( $_POST['file_name'] ) ? sanitize_text_field( $_POST['file_name'] ) : 'image.png';
		$img_url          = isset( $_POST['img_url'] ) && ! empty( $_POST['img_url'] ) ? sanitize_url( $_POST['img_url'] ) : '';

		$media = $this->upload_image_to_media_gallery( $img_url, $title, $alternative_text, $caption, $description, $file_name );

		wp_send_json_success( $media );
		wp_die();

	}

	private function upload_image_to_media_gallery( $url, $title, $alt_text, $caption, $description, $file_name ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$tmp = download_url( $url );

		$file_array = array(
			'name'     => $file_name,
			'tmp_name' => $tmp
		);

		// Create the attachment
		$id = media_handle_sideload( $file_array, 0 );

		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );

			return $id;
		}

		$data = array(
			'ID'           => $id,
			'post_title'   => $title,
			'post_content' => $description,
			'post_excerpt' => $caption,
		);

		wp_update_post( $data );

		// Set the alternative text for the attachment
		update_post_meta( $id, '_wp_attachment_image_alt', $alt_text );

		// Get the attachment information
		$attachment        = array();
		$attachment['id']  = $id;
		$attachment['url'] = wp_get_attachment_url( $id );

		return $attachment;
	}
}
