<?php
if ( ! class_exists( 'Penci_Soledad_Gutenberg' ) ):
	class Penci_Soledad_Gutenberg {
		private static $instance;

		public static function get_instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		private function __construct() {
			add_action( 'init', array( $this, 'register_block' ) );
			/* add_action( 'enqueue_block_editor_assets',  array( $this, 'enqueue_editor_assets' ) ); */

			global $wp_version;
			if( function_exists('vp_pfui_post_admin_setup') && ( 5 <= $wp_version ) ) {
				require_once dirname( __FILE__ ) . '/metaboxes.php';
				add_filter( 'admin_body_class', array( $this,'custom_admin_body_class' ) );
			}
		}

		function register_block() {
			if( is_admin() ){
				wp_enqueue_style( 'penci-gutenberg', vp_pfui_base_url() . '/gutenberg/style.css', array( 'wp-edit-blocks' ), PENCI_SOLEDAD_VERSION );
			}
		}

		function custom_admin_body_class( $classes ) {
			$classes .= ' penci-gutenberg-vp-pfui';

			return $classes;
		}
	}

	Penci_Soledad_Gutenberg::get_instance();
endif;
