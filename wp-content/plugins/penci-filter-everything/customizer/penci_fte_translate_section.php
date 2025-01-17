<?php
$options = [];

$options[] = array(
	'id'        => 'penci_fte_nodata',
	'transport' => 'postMessage',
	'default'   => __( 'No data found', 'penci-filter-everything' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Text: No data found', 'penci-filter-everything' ),
);

$options[] = array(
	'id'        => 'penci_fte_reset',
	'transport' => 'postMessage',
	'default'   => __( 'Reset', 'penci-filter-everything' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Text: Reset', 'penci-filter-everything' ),
);

$options[] = array(
	'id'        => 'penci_fte_apply',
	'transport' => 'postMessage',
	'default'   => __( 'Filter', 'penci-filter-everything' ),
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Text: Filter', 'penci-filter-everything' ),
);

return $options;