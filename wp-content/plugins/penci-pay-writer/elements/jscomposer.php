<?php

class PenciPayWriterWPBakery {

	/**
	 * Main constructor
	 */
	public function __construct() {

		// Registers the shortcode in WordPress
		add_shortcode( 'penci_pay_writer_shortcode', __CLASS__ . '::output' );

		// Map shortcode to WPBakery so you can access it in the builder
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'penci_pay_writer_shortcode', __CLASS__ . '::map' );
		}

	}

	/**
	 * Shortcode output
	 */
	public static function output( $atts, $content = null ) {

		$settings    = vc_map_get_attributes( 'penci_pay_writer_shortcode', $atts );
		$button_text = $settings['pay_writer_button'];
		$form_id     = 'pencipwt_custom_form_' . rand();

		wp_enqueue_script( 'penci-pay-writer' );
		wp_enqueue_style( 'penci-pay-writer' );

		$out = "<a class='pencipwt-donation-submit el' data-id='{$form_id}' href='#' aria-label='{$button_text}' target='_blank'><span>{$button_text}</span></a>";
		$out .= \PenciPayWriter\Init::paypal_form( [
			'form_id'     => $form_id,
			'currency'    => $settings['currency'],
			'description' => $settings['description'],
			'return'      => $settings['return'],
			'cancel_url'  => $settings['cancel_url'],
			'fix_amount'  => $settings['fix_amount'],
			'amount'      => $settings['amount'],
			'email'       => $settings['email'],
		] );

		return $out;

	}

	/**
	 * Map shortcode to WPBakery
	 *
	 * This is an array of all your settings which become the shortcode attributes ($atts)
	 * for the output. See the link below for a description of all available parameters.
	 *
	 * @since 1.0.0
	 * @link  https://kb.wpbakery.com/docs/inner-api/vc_map/
	 */
	public static function map() {

		if ( ! class_exists('Penci_Vc_Params_Helper') ) {
			return;
		}

		$theme_prefix_text = 'Soledad';
		$cat_prefix_text = 'Penci';
		if( function_exists('penci_get_theme_name')) {
			$theme_prefix_text = penci_get_theme_name( 'Soledad' );
			$cat_prefix_text = penci_get_theme_name( 'Penci' );
		}
		return array(
			'base'        => 'penci_pay_writer_shortcode',
			'icon'        => get_template_directory_uri() . '/images/vc-icon.png',
			'category'    => $theme_prefix_text,
			'weight'      => 700,
			'name'        => $cat_prefix_text . ' ' . esc_html__( 'Pay Writer', 'soledad' ),
			'description' => __( 'Penci Pay Writer element', 'soledad' ),
			'controls'    => 'full',
			'params'      => array_merge(
				array(
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Button Text', 'soledad' ),
						'param_name' => 'pay_writer_button',
						'std'        => '',
					),
					array(
						'type'       => 'textfield',
						'heading'    => __( 'Paypal Email Address', 'soledad' ),
						'param_name' => 'email',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Currency', 'soledad' ),
						'param_name' => 'currency',
						'value'      => array(
							__( 'Australian dollar - AUD', 'penci-pay-writer' )    => 'AUD',
							__( 'Brazilian real - BRL', 'penci-pay-writer' )       => 'BRL',
							__( 'Canadian dollar - CAD', 'penci-pay-writer' )      => 'CAD',
							__( 'Chinese Renmenbi - CNY', 'penci-pay-writer' )     => 'CNY',
							__( 'Czech koruna - CZK', 'penci-pay-writer' )         => 'CZK',
							__( 'Danish krone - DKK', 'penci-pay-writer' )         => 'DKK',
							__( 'Euro - EUR', 'penci-pay-writer' )                 => 'EUR',
							__( 'Hong Kong dollar - HKD', 'penci-pay-writer' )     => 'HKD',
							__( 'Hungarian forint - HUF', 'penci-pay-writer' )     => 'HUF',
							__( 'Israeli new shekel - ILS', 'penci-pay-writer' )   => 'ILS',
							__( 'Japanese yen - JPY', 'penci-pay-writer' )         => 'JPY',
							__( 'Malaysian ringgit - MYR', 'penci-pay-writer' )    => 'MYR',
							__( 'Mexican peso - MXN', 'penci-pay-writer' )         => 'MXN',
							__( 'New Taiwan dollar - TWD', 'penci-pay-writer' )    => 'TWD',
							__( 'New Zealand dollar - NZD', 'penci-pay-writer' )   => 'NZD',
							__( 'Norwegian krone - NOK', 'penci-pay-writer' )      => 'NOK',
							__( 'Philippine peso - PHP', 'penci-pay-writer' )      => 'PHP',
							__( 'Polish złoty - PLN', 'penci-pay-writer' )         => 'PLN',
							__( 'Pound sterling - GBP', 'penci-pay-writer' )       => 'GBP',
							__( 'Russian ruble - RUB', 'penci-pay-writer' )        => 'RUB',
							__( 'Singapore dollar - SGD', 'penci-pay-writer' )     => 'SGD',
							__( 'Swedish krona - SEK', 'penci-pay-writer' )        => 'SEK',
							__( 'Swiss franc - CHF', 'penci-pay-writer' )          => 'CHF',
							__( 'Thai baht - THB', 'penci-pay-writer' )            => 'THB',
							__( 'United States dollar - USD', 'penci-pay-writer' ) => 'USD',
						),
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => esc_html__( 'Description', 'soledad' ),
						'param_name' => 'description',
					),
					array(
						'type'       => 'textfield',
						'param_name' => 'return',
						'heading'    => esc_html__( 'Return URL', 'soledad' ),
						'value'      => '',
					),
					array(
						'type'       => 'textfield',
						'param_name' => 'cancel_url',
						'heading'    => esc_html__( 'Cancel URL', 'soledad' ),
						'value'      => '',
					),
					array(
						'type'        => 'penci_switch',
						'param_name'  => 'fix_amount',
						'heading'     => esc_html__( 'Fix Amount', 'soledad' ),
						'true_state'  => 'yes',
						'false_state' => 'no',
						'default'     => 'no',
						'std'         => 'no',
					),
					array(
						'type'       => 'textfield',
						'param_name' => 'amount',
						'heading'    => esc_html__( 'Amount', 'soledad' ),
						'value'      => '',
					),
				),
				Penci_Vc_Params_Helper::extra_params(),
			)
		);
	}

}

new PenciPayWriterWPBakery;