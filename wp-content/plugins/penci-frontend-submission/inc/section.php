<?php

namespace PenciFrontendSubmission;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Session {

	private static $instance;

	public static function getInstance() {

		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {

		$user_ip         = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;
		$transient_name  = 'penci_frontend_submit_' . $user_ip . '_name';
		$transient_class = 'penci_frontend_submit_' . $user_ip . '_class';

		if ( empty( get_transient( $transient_name ) ) || empty( get_transient( $transient_class ) ) ) {
			set_transient( $transient_name, '', HOUR_IN_SECONDS );
			set_transient( $transient_class, '', HOUR_IN_SECONDS );
		}

		add_filter( 'penci_get_message', array( $this, 'get_flash_message' ) );

	}

	public static function flash_message( $name = '', $message = '', $class = 'success' ) {

		$user_ip         = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;
		$transient_name  = 'penci_frontend_submit_' . $user_ip . '_name';
		$transient_class = 'penci_frontend_submit_' . $user_ip . '_class';

		if ( ! empty( $name ) ) {
			// No message, create it
			if ( ! empty( $message ) && empty( get_transient( $transient_name ) ) ) {
				if ( ! empty( get_transient( $transient_name ) ) ) {
					delete_transient( $transient_name );
				}

				if ( ! empty( get_transient( $transient_class ) ) ) {
					delete_transient( $transient_class );
				}

				set_transient( $transient_name, $message, HOUR_IN_SECONDS );
				set_transient( $transient_class, $class, HOUR_IN_SECONDS );
			} // Message exists, display it
			elseif ( ! empty( get_transient( $transient_name ) ) && empty( $message ) ) {
				$class      = ! empty( get_transient( $transient_class ) ) ? get_transient( $transient_class ) : 'success';
				$flash_msg  = get_transient( $transient_name );
				$flash_html = '<div class="' . $class . ' pencifts-alert alert alert-dismissible fade in" role="alert">' . $flash_msg . '</div>';

				delete_transient( $transient_name );
				delete_transient( $transient_class );

				return apply_filters( 'penci_flash_message', $flash_html, $name, $class );
			}
		}

	}

	public function get_flash_message() {
		return self::flash_message( 'message' );
	}
}
