<?php

$options   = array();
$options[] = array(
	'id'    => 'penci_liveblog_header_1',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'General Option', 'penci-liveblog' ),
);

$options[] = array(
	'id'          => 'penci_liveblog_timeout',
	'transport'   => 'postMessage',
	'default'     => '60',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Auto Get New Content After:', 'penci-liveblog' ),
	'description' => esc_html__( 'Enter the timeout in the seconds.', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_time_format',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Custom Data/Time Format', 'penci-liveblog' ),
	'description'     => __( 'Please fill in the datetime format string for this field - you can find more information <a target="_blank" href="https://wordpress.org/documentation/article/customize-date-and-time-format/">here</a>', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_share',
	'transport' => 'postMessage',
	'default'   => false,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Disable Event Social Share', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_notice',
	'transport' => 'postMessage',
	'default'   => false,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Hide Event Message Notice', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_change_page_title',
	'transport' => 'postMessage',
	'default'   => false,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Changing Browser Tab Title after Receiving a New Update?', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_post_prefix',
	'transport' => 'postMessage',
	'default'   => true,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Add "Live" Label Highlight to Post Title', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_post_prefix_position',
	'transport' => 'postMessage',
	'default'   => 'before',
	'type'      => 'soledad-fw-select',
	'choices'   => [
		'before' => __('Before Post Title','penci-liveblog'),
		'after'  => __('After Post Title','penci-liveblog'),
	],
	'label'     => esc_html__( 'Live Label Position', 'penci-liveblog' ),
);

$options[] = array(
	'id'        => 'penci_liveblog_hide_btn',
	'transport' => 'postMessage',
	'default'   => false,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Hide Update Now Section', 'penci-liveblog' ),
);

$options[] = array(
	'label'    => esc_html__( 'In-Feed Ads', 'soledad' ),
	'id'       => 'penci_liveblog_header_2',
	'type'     => 'soledad-fw-header',
	'sanitize' => 'sanitize_text_field'
);
$options[] = array(
	'label'    => __( 'Insert In-feed Ads Code After Every How Many Events?', 'soledad' ),
	'id'       => 'penci_liveblog_infeedads_num',
	'type'     => 'soledad-fw-size',
	'default'  => '3',
	'sanitize' => 'absint',
	'ids'      => array(
		'desktop' => 'penci_liveblog_infeedads_num',
	),
	'choices'  => array(
		'desktop' => array(
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
			'edit'    => true,
			'unit'    => '',
			'default' => '3',
		),
	),
);
$options[] = array(
	'label'       => __( 'In-feed Ads Code/HTML', 'soledad' ),
	'description' => __( 'Please use normal responsive ads here to get the best results. In-feed ads cannot work with auto-ads because auto-ads will randomly place your ads in random places on the pages.', 'soledad' ),
	'id'          => 'penci_liveblog_infeedads_code',
	'type'        => 'soledad-fw-textarea',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_textarea_field'
);

return $options;