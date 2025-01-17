<?php

namespace Soledad\PageSpeed;

use Soledad\PageSpeed\Admin\Cache;

/**
 * Admin initialization.
 *
 * @author  asadkn
 * @modified pencidesign
 * @since   1.0.0
 */
class Admin {
	/**
	 * @var Soledad\PageSpeed\Admin\Cache
	 */
	protected $cache;

	/**
	 * Setup hooks
	 */
	public function init() {
		$this->cache = new Cache;
		$this->cache->init();
		add_action( 'wp_ajax_penci_speed_delete_cache', [ $this, 'delete_cache' ] );
		add_action( 'init', [ $this, 'delete_page_cache' ] );
	}

	/**
	 * Delete Cache short page.
	 */
	public function delete_page_cache() {
		if ( current_user_can( 'manage_options' ) && isset( $_GET['clear_pencilazy_css'] ) ) {
			$this->_delete_cache();
			$this->increase_cache_version();
		}

		if ( current_user_can( 'manage_options' ) && isset( $_GET['clear_pencilazy_css_single'] ) ) {
			$this->_delete_single_cache();
			$this->increase_cache_version();
		}

		return false;
	}

	public function increase_cache_version() {
		$current_version = get_option( 'penci_speed_file_version' );
		$current_version = $current_version ? $current_version : 1.0;

		$file_version = $current_version + 0.1;

		update_option( 'penci_speed_file_version', $file_version );
	}

	public function _delete_single_cache() {
		$this->cache->empty_single_site();

		/**
		 * Hook after deleting cache.
		 */
		do_action( 'soledad_pagespeed/after_delete_cache' );
	}

	/**
	 * Callback: Delete the cache.
	 *
	 * @access private
	 */
	public function _delete_cache() {
		$this->cache->empty();

		/**
		 * Hook after deleting cache.
		 */
		do_action( 'soledad_pagespeed/after_delete_cache' );
	}

	/**
	 * Delete Cache page.
	 */
	public function delete_cache() {
		check_ajax_referer( 'penci_speed_delete_cache', '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'User Permission Error', 'soledad' ) );
		}

		$this->_delete_cache();

		wp_send_json_success();

		die();
	}
}
