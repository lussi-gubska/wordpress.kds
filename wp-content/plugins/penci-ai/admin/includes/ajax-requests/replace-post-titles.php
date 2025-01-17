<?php

namespace PenciAIContentGenerator\AjaxRequests;

class ReplacePostTitles {

	private $ajax;

	/**
	 * PreloadCaches constructor.
	 */
	public function __construct( $a ) {
		$this->ajax = $a;
		add_action( "wp_ajax_penciai_replace_with_suggested_title", [ $this, 'ajax' ] );
	}

	public function ajax() {
		\penciai_checkNonce();
		$title = isset( $_POST['title'] ) && ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$id    = isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '0';

		$my_post = array(
			'ID'         => intval( $id ),
			'post_title' => $title,
			//'post_name'  => sanitize_title_with_dashes( $title ),
		);

		// Update the post into the database
		wp_update_post( $my_post );

		wp_send_json_success();
		wp_die();

	}
}
