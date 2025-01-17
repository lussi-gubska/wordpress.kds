<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Penci_Soledad_Migrator_Ajax' ) ) {
	class Penci_Soledad_Migrator_Ajax {

		public function __construct() {
			add_action( 'wp_ajax_nopriv_penci_migrator_ajax', array( $this, 'nopriv_ajax_callback' ) );
			add_action( 'wp_ajax_penci_migrator_ajax', array( $this, 'ajax_callback' ) );
		}

		public function nopriv_ajax_callback() {
			wp_send_json_error( __( 'Unauthorized request.', 'penci-data-migrator' ) );
		}

		public function ajax_callback() {
			check_ajax_referer( 'ajax-nonce', 'nonce' );

			if ( ! isset( $_POST['data'] ) || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}

			$data           = array();
			$data_serialize = wp_unslash( $_POST['data'] );

			parse_str( $data_serialize, $data );

			$theme   = isset( $data['theme'] ) ? htmlspecialchars( $data['theme'], ENT_QUOTES ) : '';
			$force   = isset( $data['force_switch_posts'] ) ? (bool) $data['force_switch_posts'] : false;
			$post_id = isset( $_POST['post_id'] ) ? filter_var( wp_unslash( $_POST['post_id'] ), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : 0;

			$theme_lists = array(
				'bimber',
				'cheerup',
				'jannah',
				'jnews',
				'newsmag',
				'newspaper',
				'pennews',
				'publisher',
				'sahifa',
				'smartmag',
				'sproutspoon'
			);

			if ( ! in_array( $theme, $theme_lists ) ) {
				wp_send_json_error( array( 'mess' => esc_html__( 'Please select a valid theme.', 'penci-data-migrator' ) ) );
			}

			if ( ! $theme ) {
				wp_send_json_error( array( 'mess' => esc_html__( 'Please choose a theme first.', 'penci-data-migrator' ) ) );
			}
			if ( ! $post_id ) {
				wp_send_json_error( array( 'mess' => esc_html__( 'Not found Post', 'penci-data-migrator' ) ) );
			}

			// No timeout limit.
			set_time_limit( 0 );

			$skip = 'false';

			$migrator = get_post_meta( $post_id, 'soledad_migrator_' . $theme, true );

			if ( 1 === $migrator && ! $force ) {
				$skip = 'true';
				$mess = 'Skip to migrate Post "' . get_the_title( $post_id ) . '"';
			} else {
				$mess = include PENCI_MIGRATOR_DIR . "inc/themes/{$theme}.php";
				update_post_meta( $post_id, 'soledad_migrator_' . $theme, 1 );
			}

			wp_send_json_success(
				array(
					'skip' => $skip,
					'mess' => $mess,
				)
			);
		}
	}
}

new Penci_Soledad_Migrator_Ajax();
