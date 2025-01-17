<?php

use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class PenciPayWriterElementor extends \Elementor\Widget_Base {

	public function get_title() {
		return esc_html__( 'Penci - Pay Writer Button', 'penci-pay-writer' );
	}

	public function get_icon() {
		return 'eicon-paypal-button';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'pay', 'writer', 'button' ];
	}

	protected function get_html_wrapper_class() {
		return 'pcpw-el elementor-widget-' . $this->get_name();
	}

	public function get_name() {
		return 'penci-pay-writer';
	}

	public function get_script_depends() {
		return [ 'penci-pay-writer' ];
	}

	public function get_style_depends() {
		return [ 'penci-pay-writer' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => esc_html__( 'General', 'penci-pay-writer' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'pay_writer_button', [
			'label'       => esc_html__( 'Button Text', 'penci-pay-writer' ),
			'default'     => esc_html__( 'Donate', 'penci-pay-writer' ),
			'description' => esc_html__( 'Change the subscribe button text.', 'penci-pay-writer' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
		] );

		$this->add_control( 'pay_writer_button_icon', [
			'label' => esc_html__( 'Button Icons', 'penci-pay-writer' ),
			'type'  => \Elementor\Controls_Manager::ICONS,
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'paypal_section', [
			'label' => esc_html__( 'Paypal Settings', 'penci-pay-writer' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'email', [
			'label' => esc_html__( 'Paypal Email Address', 'penci-pay-writer' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );

		$this->add_control( 'currency', [
			'label'   => esc_html__( 'Currency', 'penci-pay-writer' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => get_theme_mod( 'penci_paywriter_currency', 'USD' ),
			'options' => array(
				'AUD' => __( 'Australian dollar - AUD', 'penci-pay-writer' ),
				'BRL' => __( 'Brazilian real - BRL', 'penci-pay-writer' ),
				'CAD' => __( 'Canadian dollar - CAD', 'penci-pay-writer' ),
				'CNY' => __( 'Chinese Renmenbi - CNY', 'penci-pay-writer' ),
				'CZK' => __( 'Czech koruna - CZK', 'penci-pay-writer' ),
				'DKK' => __( 'Danish krone - DKK', 'penci-pay-writer' ),
				'EUR' => __( 'Euro - EUR', 'penci-pay-writer' ),
				'HKD' => __( 'Hong Kong dollar - HKD', 'penci-pay-writer' ),
				'HUF' => __( 'Hungarian forint - HUF', 'penci-pay-writer' ),
				'ILS' => __( 'Israeli new shekel - ILS', 'penci-pay-writer' ),
				'JPY' => __( 'Japanese yen - JPY', 'penci-pay-writer' ),
				'MYR' => __( 'Malaysian ringgit - MYR', 'penci-pay-writer' ),
				'MXN' => __( 'Mexican peso - MXN', 'penci-pay-writer' ),
				'TWD' => __( 'New Taiwan dollar - TWD', 'penci-pay-writer' ),
				'NZD' => __( 'New Zealand dollar - NZD', 'penci-pay-writer' ),
				'NOK' => __( 'Norwegian krone - NOK', 'penci-pay-writer' ),
				'PHP' => __( 'Philippine peso - PHP', 'penci-pay-writer' ),
				'PLN' => __( 'Polish złoty - PLN', 'penci-pay-writer' ),
				'GBP' => __( 'Pound sterling - GBP', 'penci-pay-writer' ),
				'RUB' => __( 'Russian ruble - RUB', 'penci-pay-writer' ),
				'SGD' => __( 'Singapore dollar - SGD', 'penci-pay-writer' ),
				'SEK' => __( 'Swedish krona - SEK', 'penci-pay-writer' ),
				'CHF' => __( 'Swiss franc - CHF', 'penci-pay-writer' ),
				'THB' => __( 'Thai baht - THB', 'penci-pay-writer' ),
				'USD' => __( 'United States dollar - USD', 'penci-pay-writer' ),
			),
		] );

		$this->add_control( 'description', [
			'label'   => esc_html__( 'Description', 'penci-pay-writer' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => get_theme_mod( 'penci_paywriter_checkout_description', 'Buy author a coffee' ),
		] );

		$this->add_control( 'return', [
			'label' => esc_html__( 'Return URL', 'penci-pay-writer' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );

		$this->add_control( 'cancel_url', [
			'label' => esc_html__( 'Cancel URL', 'penci-pay-writer' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );

		$this->add_control( 'fix_amount', [
			'label' => esc_html__( 'Fix Amount', 'penci-pay-writer' ),
			'type'  => \Elementor\Controls_Manager::SWITCHER,
		] );

		$this->add_control( 'amount', [
			'label'     => esc_html__( 'Amount', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'condition' => [ 'fix_amount' => 'yes' ],
			'default'   => get_theme_mod( 'penci_paywriter_fix_amount', '5.00' ),
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Typo & Colors', 'penci-pay-writer' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'btn_title_typo',
				'label'    => __( 'Button Typography', 'soledad' ),
				'selector' => '{{WRAPPER}} .pencipwt-donation-submit',
			)
		);

		$this->add_control( 'btn_title_cl', [
			'label'     => esc_html__( 'Button Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'color:{{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_hcl', [
			'label'     => esc_html__( 'Button Hover Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit:hover' => 'color:{{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_bgcl', [
			'label'     => esc_html__( 'Button Background Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'background-color:{{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_hbgcl', [
			'label'     => esc_html__( 'Button Background Hover Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit:hover' => 'background-color:{{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_bdcl', [
			'label'     => esc_html__( 'Button Border Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'border:1px solid {{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_hbdcl', [
			'label'     => esc_html__( 'Button Border Hover Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit:hover' => 'border:1px solid {{VALUE}}' ]
		] );

		$this->add_control( 'btn_title_padding', [
			'label'      => esc_html__( 'Button Padding', 'penci-pay-writer' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors'  => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ]
		] );

		$this->add_control( 'btn_title_bdradius', [
			'label'      => esc_html__( 'Button Border Radius', 'penci-pay-writer' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors'  => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ]
		] );

		$this->add_control( 'btn_title_bdwidth', [
			'label'      => esc_html__( 'Button Border Width', 'penci-pay-writer' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors'  => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ]
		] );

		$this->add_control( 'btn_title_bdstyle', [
			'label'     => esc_html__( 'Button Border Style', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'options'   => array(
				'solid'    => 'Solid',
				'dotted'   => 'Dotted',
				'dashed'   => 'Dashed',
				'double'   => 'Double',
				'groove'   => 'Groove',
				'ridge'    => 'Ridge',
				'inset'    => 'Inset',
				'outset'   => 'Outset',
				'gradient' => 'Gradient',
			),
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit' => 'border-style: {{VALUE}};' ]
		] );

		$this->add_control( 'btn_icon_title_bdcl', [
			'label'     => esc_html__( 'Button Icon Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit .pcbtn-ico' => 'color:{{VALUE}}' ]
		] );

		$this->add_control( 'btn_icon_title_hbdcl', [
			'label'     => esc_html__( 'Button Icon Hover Color', 'penci-pay-writer' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .pencipwt-donation-submit:hover .pcbtn-ico' => 'color:{{VALUE}}' ]
		] );

		$this->end_controls_section();

	}

	protected function render() {
		$settings    = $this->get_settings();
		$button_text = $settings['pay_writer_button'];
		$form_id     = 'pencipwt_custom_form_' . $this->get_id();
		$custom_icon = $settings['pay_writer_button_icon'];

		$icon = '';

		if ( ! empty( $custom_icon ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $custom_icon );
			$icon = ob_get_clean();
			$icon = '<span class="pcbtn-ico">' . $icon . '</span>';
		}


		echo "<a class='pencipwt-donation-submit el' data-id='{$form_id}' href='#' aria-label='{$button_text}' target='_blank'>{$icon}<span>{$button_text}</span></a>";
		echo \PenciPayWriter\Init::paypal_form( [
			'form_id'     => $form_id,
			'currency'    => $settings['currency'],
			'description' => $settings['description'],
			'return'      => $settings['return'],
			'cancel_url'  => $settings['cancel_url'],
			'fix_amount'  => $settings['fix_amount'],
			'amount'      => $settings['amount'],
			'email'       => $settings['email'],
		] );
	}
}
