<?php
$options   = [];
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_text',
	'default'  => esc_html__( 'Top Posts', 'soledad' ),
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-text',
	'label'    => esc_html__( 'Custom "Top Posts" Text', 'soledad' ),
	'desc'     => esc_html__( 'If you want hide Top Posts text, let empty this', 'soledad' ),
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_style',
	'default'  => '',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-select',
	'label'    => __( 'Style for "Top Posts" Text', 'soledad' ),
	'priority' => 10,
	'choices'  => array(
		''                => __('Default Style','soledad' ),
		'nticker-style-2' => __('Style 2','soledad' ),
		'nticker-style-3' => __('Style 3','soledad' ),
		'nticker-style-4' => __('Style 4','soledad' ),
	)
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_animation',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-select',
	'label'    => __( '"Top Posts" Transition Animation', 'soledad' ),
	'default'  => '',
	'priority' => 10,
	'choices'  => array(
		''             => __('Slide In Up','soledad' ),
		'slideInRight' => __('Fade In Right','soledad' ),
		'fadeIn'       => __('Fade In','soledad' ),
		'marquee'      => __('Marquee','soledad' ),
	)
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_by',
	'default'  => '',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-select',
	'label'    => esc_html__( 'Display Top Posts By', 'soledad' ),
	'choices'  => array_merge(penci_jetpack_option(),array(
		''        => __('Recent Posts','soledad' ),
		'all'     => __('Popular Posts All Time','soledad' ),
		'week'    => __('Popular Posts Once Weekly','soledad' ),
		'month'   => __('Popular Posts Once Month','soledad' ),
	))
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_filter_by',
	'default'  => '',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-radio',
	'label'    => esc_html__( 'Filter Topbar By', 'soledad' ),
	'priority' => 10,
	'choices'  => array(
		'category' => __('Category','soledad' ),
		'tags'     => __('Tags','soledad' ),
	)
);
$options[] = array(
	'id'          => 'penci_header_pb_news_ticker_tags',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field',
	'type'        => 'soledad-fw-textarea',
	'label'       => esc_html__( 'Fill List Tags for Filter by Tags on "Top Post"', 'soledad' ),
	'description' => __('This option just apply when you select "Filter Topbar by" Tags above. And please fill list featured tags slug here, check <a rel="nofollow" href="https://soledad.pencidesign.net/soledad-document/images/tags.png" target="_blank">this image</a> to know what is tags slug. Example for multiple tags slug, fill:  tag-1, tag-2, tag-3','soledad' ),
	'priority'    => 10,
);
$options[] = array(
	'id'          => 'penci_header_pb_news_ticker_cats',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field',
	'type'        => 'soledad-fw-textarea',
	'label'       => esc_html__( 'Fill List Categories for Filter by Category on "Top Post"', 'soledad' ),
	'description' => __('This option just apply when you select "Filter Topbar by" Category above. And please fill list featured category slug here, check <a rel="nofollow" href="https://soledad.pencidesign.net/soledad-document/images/tags.png" target="_blank">this image</a> to know what is tags slug. Example for multiple category slug, fill:  cat-1, cat-2, cat-3','soledad' ),
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_post_titles',
	'default'  => '',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-size',
	'label'    => esc_html__( 'Words Length for Post Titles on Top Posts', 'soledad' ),
	'ids'      => array(
		'desktop' => 'penci_header_pb_news_ticker_post_titles',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'ms',
		),
	),
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_disable_autoplay',
	'default'  => 'disable',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => esc_html__( 'Disable Auto Play', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'choices'  => [
		'disable' => __('No','soledad' ),
		'enable'  => __('Yes','soledad' ),
	]
);
$options[] = array(
	'id'          => 'penci_header_pb_news_ticker_autoplay_timeout',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field',
	'type'        => 'soledad-fw-size',
	'description' => '1000 = 1 second',
	'label'       => esc_html__( 'Autoplay Timeout', 'soledad' ),
	'priority'    => 10,
	'ids'         => array(
		'desktop' => 'penci_header_pb_news_ticker_autoplay_timeout',
	),
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'ms',
		),
	),
);
$options[] = array(
	'id'          => 'penci_header_pb_news_ticker_autoplay_speed',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field',
	'type'        => 'soledad-fw-size',
	'description' => '1000 = 1 second',
	'label'       => esc_html__( 'Autoplay Speed', 'soledad' ),
	'ids'         => array(
		'desktop' => 'penci_header_pb_news_ticker_autoplay_speed',
	),
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'ms',
		),
	),
);
$options[] = array(
	'id'       => 'penci_header_pb_news_ticker_total_posts',
	'default'  => '',
	'sanitize' => 'penci_sanitize_choices_field',
	'type'     => 'soledad-fw-size',
	'label'    => esc_html__( 'Amount of Posts Display on Top Posts', 'soledad' ),
	'ids'      => array(
		'desktop' => 'penci_header_pb_news_ticker_total_posts',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'ms',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_width',
	'default'   => '420',
	'transport' => 'postMessage',
	'sanitize'  => 'absint',
	'type'      => 'soledad-fw-size',
	'label'     => __( 'Maxium Width for Ticker Text', 'soledad' ),
	'ids'       => array(
		'desktop' => 'penci_header_pb_news_ticker_width',
	),
	'choices'   => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_disable_uppercase',
	'default'   => 'disable',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __( 'Disable Uppercase for "Top Posts" text', 'soledad' ),
	'type'      => 'soledad-fw-select',
	'choices'   => [
		'disable' => __('No','soledad' ),
		'enable'  => __('Yes','soledad' ),
	]
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_post_titles_uppercase',
	'default'   => 'disable',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => esc_html__( 'Turn Off Uppercase Post Titles', 'soledad' ),
	'type'      => 'soledad-fw-select',
	'choices'   => [
		'disable' => __('No','soledad' ),
		'enable'  => __('Yes','soledad' ),
	]
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_headline_color',
	'default'   => '',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Color for "Top Posts" Text','soledad' ),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_headline_bg',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'type'      => 'soledad-fw-color',
	'label'     => __('Background Color for "Top Posts Text"','soledad' ),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_headline_bg_style3',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Color for Right Arrow on Style 3','soledad' ),
	'type'      => 'soledad-fw-color',
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_color',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Color for Post Titles','soledad' ),
	'type'      => 'soledad-fw-color',
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_hv_color',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Hover Color for Post Titles','soledad' ),
	'type'      => 'soledad-fw-color',
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_arr_color',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Color for Next/Prev Buttons','soledad' ),
	'type'      => 'soledad-fw-color',
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_arr_hv_color',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Hover Color for Next/Prev Buttons','soledad' ),
	'type'      => 'soledad-fw-color',
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_font',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'label'     => __('Custom Text Font','soledad' ),
	'type'      => 'soledad-fw-select',
	'choices'   => penci_all_fonts( 'select' )
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_fs',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'absint',
	'type'      => 'soledad-fw-size',
	'label'     => __( 'Font Size for Post Titles', 'soledad' ),
	'ids'       => array(
		'desktop' => 'penci_header_pb_news_ticker_fs',
	),
	'choices'   => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_arr_fs',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'absint',
	'type'      => 'soledad-fw-size',
	'label'     => __( 'Font Size for Next/Prev Buttons', 'soledad' ),
	'ids'       => array(
		'desktop' => 'penci_header_pb_news_ticker_arr_fs',
	),
	'choices'   => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_headline_fs',
	'default'   => '',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-size',
	'sanitize'  => 'absint',
	'label'     => __( 'Font Size for "Top Posts" Text', 'soledad' ),
	'ids'       => array(
		'desktop' => 'penci_header_pb_news_ticker_headline_fs',
	),
	'choices'   => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_spacing',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_choices_field',
	'type'      => 'soledad-fw-box-model',
	'label'     => __( 'Element Spacing', 'soledad' ),
	'choices'   => array(
		'margin'  => array(
			'margin-top'    => '',
			'margin-right'  => '',
			'margin-bottom' => '',
			'margin-left'   => '',
		),
		'padding' => array(
			'padding-top'    => '',
			'padding-right'  => '',
			'padding-bottom' => '',
			'padding-left'   => '',
		),
	),
);
$options[] = array(
	'id'        => 'penci_header_pb_news_ticker_class',
	'default'   => '',
	'transport' => 'postMessage',
	'sanitize'  => 'penci_sanitize_textarea_field',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Custom CSS Class', 'soledad' ),
);

return $options;
