<?php

class Penci_FTE_Front {

	private static $instance;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'load_style' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
		add_action( 'soledad_theme/custom_css', [ $this, 'front_css' ] );
	}

	public function load_style() {

		wp_register_style( 'penci-fte-chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css', [], PENCI_FTE_VERSION );
		wp_register_script( 'penci-fte-chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', [], PENCI_FTE_VERSION );
		wp_register_script( 'penci-post-filter-widget', PENCI_FTE_URL . '/assets/post-filter-widget.js', ( [
			'jquery',
			'penci-fte-chosen',
			'jquery.pjax'
		] ), PENCI_FTE_VERSION, true );

		wp_enqueue_style( 'penci-fte-front', PENCI_FTE_URL . 'assets/pfe.css', [ 'penci-fte-chosen' ], PENCI_FTE_VERSION );
	}

	public function register_widget( $widgets_manager ) {
		$_elements = [
			'penci-filter',
		];

		foreach ( $_elements as $aelement ) {
			require_once( PENCI_FTE_DIR . "/elementor/{$aelement}.php" );
			$class     = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $aelement ) ) );
			$classname = '\\' . $class . 'Element';
			$widgets_manager->register( new $classname() );
		}
	}

	public function front_css() {
		$ruls = [
			'penci_fte_heading_size'        => '.penci-fte-title{font-size:$value$px}',
			'penci_fte_text_size'           => '.penci-fte-groups{font-size:$value$px}',
			'penci_fte_counter_size'        => '.pcptf-mt .count{font-size:$value$px}',
			'penci_fte_check_size'          => '.pcptf-mt .pmfa:after{--size:$value$px}',
			'penci_fte_btn_size'          	=> '.pcft-buttons .pcft-button{font-size:$value$px}',
			'penci_fte_text_color'          => '.penci-fte-groups a{color:$value$}',
			'penci_fte_text_selected_color' => '.penci-fte-groups a:hover{color:$value$}',
			'penci_fte_check_color'         => '.pcptf-mt .pmfa:after{border-color:$value$}',
			'penci_fte_checked_color'       => '.pcptf-mt .added{--pcaccent-cl:$value$}',
			'penci_fte_filter_btn_bgcolor'  => '.pcft-buttons .pcft-button.pcft-filter-btn{background:$value$}',
			'penci_fte_filter_btn_tcolor'   => '.pcft-buttons .pcft-button.pcft-filter-btn{color:$value$}',
			'penci_fte_reset_btn_bgcolor'   => '.pcft-buttons .pcft-button.pcft-reset-btn{background:$value$}',
			'penci_fte_reset_btn_tcolor'    => '.pcft-buttons .pcft-button.pcft-reset-btn{color:$value$}',
		];

		$css = '';

		foreach ( $ruls as $id => $css_rule ) {
			$val = get_theme_mod( $id );
			if ( $val ) {
				$css .= str_replace( '$value$', $val, $css_rule );
			}
		}

		echo $css;
	}
}