<?php
$options    = [];
$count_type = array(
	'standard_payment' => esc_attr__( 'Standard Payout', 'penci-pay-writer' ),
	'view_payment'     => esc_attr__( 'View Payout', 'penci-pay-writer' ),
	'word_payment'     => esc_attr__( 'Word Payout', 'penci-pay-writer' ),
);

$options[] = array(
	'id'    => 'penci_paywriter_setting_header_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'PayPal Settings', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_paypal_sandbox',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Sandbox Mode', 'penci-pay-writer' ),
	'description' => esc_html__( 'Check this option if you are using Sandbox APP credentials', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_paypal_forward_ipn_response_urls',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Forward IPN Url (Options)', 'penci-pay-writer' ),
	'description' => esc_html__( 'Please provide the valid IPN request URL', 'penci-pay-writer' ),
);

$options[] = array(
	'id'    => 'penci_paywriter_general_setting_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'General Settings', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_currency',
	'default'     => 'USD',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Payout Currency', 'penci-pay-writer' ),
	'description' => esc_html__( 'Choose the currency of the payout', 'penci-pay-writer' ),
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
	'id'        => 'penci_paywriter_cur_character',
	'default'   => '$',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Currency Character', 'penci-pay-writer' ),
);

$options[] = array(
	'id'        => 'penci_paywriter_cur_character_pos',
	'default'   => 'after',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-select',
	'label'     => esc_html__( 'Payout Currency Position', 'penci-pay-writer' ),
	'choices'   => array(
		'after'  => __( 'After Number', 'penci-pay-writer' ),
		'before' => __( 'Before Number', 'penci-pay-writer' ),
	),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_max_amount',
	'default'     => '0',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Payout Limit', 'penci-pay-writer' ),
	'description' => esc_html__( 'Set the maximum payout amount for the receiver (0 means unlimited)', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '0',
		'step' => '1',
	),
);

// standard payment option
$options[] = array(
	'id'    => 'penci_paywriter_payment_standard_payment_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Standard Payout Option', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_standard_amount',
	'default'     => '1.5',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Minimum Post Payout', 'penci-pay-writer' ),
	'description' => esc_html__( 'Adjust minimum payout amount for each post. Each post will cost at least the value configured.', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '0',
		'step' => '1',
	),
);

// view payment option
$options[] = array(
	'id'    => 'penci_paywriter_payment_view_payment_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'View Count Payout Option', 'penci-pay-writer' ),
);

$options[] = array(
	'id'        => 'penci_paywriter_payment_payment_view_enable',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'default'   => true,
	'label'     => esc_html__( 'Enable View Payout', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_payment_view_rate',
	'default'     => '0.001',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'View Payout', 'penci-pay-writer' ),
	'description' => esc_html__( 'Adjust the payout amount for each view', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '0.001',
		'step' => '0.001',
	),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_payment_min_view',
	'default'     => '5',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Minimum View Count', 'penci-pay-writer' ),
	'description' => esc_html__( 'Set the minimum views needed to be eligible for view payout', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '1',
		'step' => '1',
	),
);

// image payment option
$options[] = array(
	'id'    => 'penci_paywriter_payment_img_payment_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Image Count Payout Option', 'penci-pay-writer' ),
);

$options[] = array(
	'id'        => 'penci_paywriter_img_payment_view_enable',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'default'   => false,
	'label'     => esc_html__( 'Enable Images Payout', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_img_payment_view_rate',
	'default'     => '0.001',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Image Payout', 'penci-pay-writer' ),
	'description' => esc_html__( 'Adjust the payout amount for each image', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '0.001',
		'step' => '0.001',
	),
);

$options[] = array(
	'id'        => 'penci_paywriter_img_min_rate',
	'default'   => '2',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-number',
	'label'     => esc_html__( 'Minimum Images Per Posts', 'penci-pay-writer' ),
	'choices'   => array(
		'min'  => '2',
		'step' => '9',
	),
);


$options[] = array(
	'id'    => 'penci_paywriter_payment_payment_word_payment_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Word Payout Option', 'penci-pay-writer' ),
);

$options[] = array(
	'id'          => 'penci_paywriter_counting_words',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Word Payout', 'penci-pay-writer' ),
);


$options[] = array(
	'id'          => 'penci_paywriter_payment_payment_word_rate',
	'default'     => '0.001',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Word Payout Rate', 'penci-pay-writer' ),
	'description' => esc_html__( 'Set the word rate. The rate would be applied if the if the minimum word requirement is achieved.', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '0.01',
		'step' => '0.01',
	),
);

$options[] = array(
	'id'          => 'penci_paywriter_payment_payment_min_word',
	'default'     => '5',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Minimum Word Count', 'penci-pay-writer' ),
	'description' => esc_html__( 'Set the minimum word count that need to be achieved before the rate can be applied', 'penci-pay-writer' ),
	'choices'     => array(
		'min'  => '1',
		'step' => '1',
	),
);

$options[] = array(
	'id'          => 'penci_paywriter_counting_words_legacy',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Use Legacy Word Counter', 'penci-pay-writer' ),
);

return $options;

