<?php

class Penci_AMP_Template {

	private $template_dir;

	public function __construct(  ) {
		$this->template_dir = apply_filters( 'penci_amp_post_template_dir', PENCI_AMP_DIR. '/templates' );
	}

	public function load() {
		$this->load_parts( array( 'index' ) );
	}

	public function load_parts( $templates ) {
		foreach ( $templates as $template ) {
			$file = $this->get_template_path( $template );
			$this->verify_and_include( $file, $template );
		}
	}

	private function get_template_path( $template ) {
		return sprintf( '%s/%s.php', $this->template_dir, $template );
	}

	private function verify_and_include( $file, $template_type ) {
		$located_file = $this->locate_template( $file );
		if ( $located_file ) {
			$file = $located_file;
		}

		$file = apply_filters( 'penci_amp_post_template_file', $file, $template_type, $this->post );
		if ( ! $this->is_valid_template( $file ) ) {
			_doing_it_wrong( __METHOD__, sprintf( esc_html__( 'Path validation for template (%s) failed. Path cannot traverse and must be located in `%s`.', 'penci-amp' ), esc_html( $file ), 'WP_CONTENT_DIR' ), '0.1' );
			return;
		}

		do_action( 'penci_amp_post_template_include_' . $template_type, $this );
		include( $file );
	}


	private function locate_template( $file ) {
		$search_file = sprintf( 'amp/%s', basename( $file ) );
		return locate_template( array( $search_file ), false );
	}

	private function is_valid_template( $template ) {
		if ( false !== strpos( $template, '..' ) ) {
			return false;
		}

		if ( false !== strpos( $template, './' ) ) {
			return false;
		}

		if ( ! file_exists( $template ) ) {
			return false;
		}

		return true;
	}
}
