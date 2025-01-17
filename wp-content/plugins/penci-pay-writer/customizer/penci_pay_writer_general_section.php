<?php

$options = [];

$options[] = array(
	'id'          => 'penci_paywriter_enable_all_post',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'label'       => esc_html__( 'Donation on All Posts', 'penci-pay-writer' ),
	'description' => esc_html__( 'Enable donation on all posts', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_currency',
	'transport'   => 'postMessage',
	'default'     => 'USD',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Donation Currency', 'penci-pay-writer' ),
	'description' => esc_html__( 'Currency allowed for post donation', 'penci-pay-writer' ),
	'choices'     => array(
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
);

$options[] = array(
	'id'        => 'penci_paywriter_donation_custom_email',
	'transport' => 'postMessage',
	'default'   => '',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Donate to one Paypal email instead of for Post Author', 'penci-pay-writer' ),
	'description' => esc_html__( 'By default, the donation will be donated to the Paypal email of the Author of posts. This option will make the donation to only one email address assigned.', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_button_text',
	'transport'   => 'postMessage',
	'default'     => 'Donate',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Donation Element Text', 'penci-pay-writer' ),
	'description' => esc_html__( 'Configure text displayed in the donation element', 'penci-pay-writer' ),
);


$options[] = array(
	'id'          => 'penci_paywriter_element_type',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-select',
	'default'     => 'button',
	'label'       => esc_html__( 'Donation Button Position', 'penci-pay-writer' ),
	'description' => esc_html__( 'Choose how the donation display on the post', 'penci-pay-writer' ),
	'choices'     => array(
		'button' => 'Post Meta',
		'widget' => 'End of Post Content',
		'both'   => 'Post Meta & End of Post Content',
	),
);

$options[] = array(
	'id'            => 'penci_paywriter_donation_icon_color',
	'transport'     => 'postMessage',
	'default'       => '1eb277',
	'type'          => 'soledad-fw-color',
	'disable_color' => true,
	'label'         => esc_html__( 'Donation Icon Color', 'penci-pay-writer' ),
	'description'   => esc_html__( 'Choose color for donation icon color', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_widget_title',
	'transport'   => 'postMessage',
	'default'     => 'Donation',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Donation Title', 'penci-pay-writer' ),
	'description' => esc_html__( 'Configure title displayed in the donation widget', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_widget_description',
	'transport'   => 'postMessage',
	'default'     => 'Buy author a coffee',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Donation Description', 'penci-pay-writer' ),
	'description' => esc_html__( 'Displays description text at the donation widget', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_checkout_description',
	'transport'   => 'postMessage',
	'default'     => 'Buy author a coffee',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Checkout Description', 'penci-pay-writer' ),
	'description' => esc_html__( 'Displays description text at the donation checkout page', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_enable_fix_amount',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'label'       => esc_html__( 'Fixed Donation', 'penci-pay-writer' ),
	'description' => esc_html__( 'Enabling this option will strict donors to donate the predetermined amount', 'penci-pay-writer' ),
);

$options[] = array(
	'id'        => 'penci_paywriter_fix_amount',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-number',
	'label'     => esc_html__( 'Fixed Donation amount', 'penci-pay-writer' ),
	'default'   => 5,
	'choices'   => array(
		'max'  => 0,
		'min'  => 1,
		'step' => 1,
	),
);

$options[] = array(
	'id'        => 'penci_paywriter_pp_heading',
	'transport' => 'postMessage',
	'default'   => '',
	'type'      => 'soledad-fw-header',
	'label'     => esc_html__( 'Paypal URL Settings', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_return_url',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Return URL from PayPal', 'penci-pay-writer' ),
	'description' => esc_html__( 'Enter a return URL (could be a Thank You page). PayPal will redirect visitors to this page after Payment.', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_cancel_url',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Cancel URL from PayPal', 'penci-pay-writer' ),
	'description' => esc_html__( 'Enter a cancel URL. PayPal will redirect visitors to this page if they click on the cancel link.', 'penci-pay-writer' ),
);

return $options;


