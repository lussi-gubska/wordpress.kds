<?php
$options = array();

$options[] = array(
	'id'    => 'pencifte_header_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Font Sizes', 'penci-filter-everything' ),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'absint',
	'type'     => 'soledad-fw-size',
	'label'    => __( 'Font Size Heading Text', 'penci-filter-everything' ),
	'id'       => 'penci_fte_heading_size',
	'ids'      => array(
		'desktop' => 'penci_fte_heading_size',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 300,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'absint',
	'type'     => 'soledad-fw-size',
	'label'    => __( 'Font Size General Text', 'penci-filter-everything' ),
	'id'       => 'penci_fte_text_size',
	'ids'      => array(
		'desktop' => 'penci_fte_text_size',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 300,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'absint',
	'type'     => 'soledad-fw-size',
	'label'    => __( 'Font Size Counter Text', 'penci-filter-everything' ),
	'id'       => 'penci_fte_counter_size',
	'ids'      => array(
		'desktop' => 'penci_fte_counter_size',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 300,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'absint',
	'type'     => 'soledad-fw-size',
	'label'    => __( 'Check Size', 'penci-filter-everything' ),
	'id'       => 'penci_fte_check_size',
	'ids'      => array(
		'desktop' => 'penci_fte_check_size',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 300,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'absint',
	'type'     => 'soledad-fw-size',
	'label'    => __( 'Buttons Font Size', 'penci-filter-everything' ),
	'id'       => 'penci_fte_btn_size',
	'ids'      => array(
		'desktop' => 'penci_fte_btn_size',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 300,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

$options[] = array(
	'id'    => 'pencifte_header_02',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Colors', 'penci-filter-everything' ),
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Text Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_text_color',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Selected Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_text_selected_color',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Check Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_check_color',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Checked Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_checked_color',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Filter Button Background Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_filter_btn_bgcolor',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Filter Button Text Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_filter_btn_tcolor',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Reset Button Background Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_reset_btn_bgcolor',
);

$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => esc_html__( 'Reset Button Text Color', 'penci-filter-everything' ),
	'id'       => 'penci_fte_reset_btn_tcolor',
);

return $options;