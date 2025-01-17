<?php
$options   = [];
$options[] = array(
	'id'    => 'pencilb_style_header_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Colors', 'penci-paywall' ),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Date/Time Color', 'penci-liveblog' ),
	'id'       => 'pencilb_date_color',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Title Color', 'penci-liveblog' ),
	'id'       => 'pencilb_title_color',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Content Color', 'penci-liveblog' ),
	'id'       => 'pencilb_content_color',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Content Link Color', 'penci-liveblog' ),
	'id'       => 'pencilb_content_link_color',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Content Link Hover Color', 'penci-liveblog' ),
	'id'       => 'pencilb_content_link_hcolor',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Social Share Color', 'penci-liveblog' ),
	'id'       => 'pencilb_share_color',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Social Share Hover Color', 'penci-liveblog' ),
	'id'       => 'pencilb_share_hcolor',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Event Border Color', 'penci-liveblog' ),
	'id'       => 'pencilb_event_bcolor',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Top Status Background Color', 'penci-liveblog' ),
	'id'       => 'pencilb_top_status_bgcolor',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Top Status Text Color', 'penci-liveblog' ),
	'id'       => 'pencilb_top_status_txtcolor',
);

$options[] = array(
	'id'    => 'pencilb_style_header_02',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Font Sizes', 'penci-paywall' ),
);

$options[] = array(
	'sanitize' => 'absint',
	'label'    => '',
	'id'       => 'penci_liveblog_date_mfsize',
	'type'     => 'soledad-fw-hidden',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Font Size for Date/Time',
	'id'       => 'penci_liveblog_date_fsize',
	'type'     => 'soledad-fw-size',
	'ids'      => [
		'desktop' => 'penci_liveblog_date_fsize',
		'mobile'  => 'penci_liveblog_date_mfsize',
	],
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'sanitize' => 'absint',
	'label'    => '',
	'id'       => 'penci_liveblog_title_mfsize',
	'type'     => 'soledad-fw-hidden',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Font Size for Title',
	'id'       => 'penci_liveblog_title_fsize',
	'type'     => 'soledad-fw-size',
	'ids'      => [
		'desktop' => 'penci_liveblog_title_fsize',
		'mobile'  => 'penci_liveblog_title_mfsize',
	],
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'sanitize' => 'absint',
	'label'    => '',
	'id'       => 'penci_liveblog_content_mfsize',
	'type'     => 'soledad-fw-hidden',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Font Size for Content',
	'id'       => 'penci_liveblog_content_fsize',
	'type'     => 'soledad-fw-size',
	'ids'      => [
		'desktop' => 'penci_liveblog_content_fsize',
		'mobile'  => 'penci_liveblog_content_mfsize',
	],
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'sanitize' => 'absint',
	'label'    => '',
	'id'       => 'penci_liveblog_share_mfsize',
	'type'     => 'soledad-fw-hidden',
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Font Size for Social Share',
	'id'       => 'penci_liveblog_share_fsize',
	'type'     => 'soledad-fw-size',
	'ids'      => [
		'desktop' => 'penci_liveblog_share_fsize',
		'mobile'  => 'penci_liveblog_share_mfsize',
	],
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

return $options;