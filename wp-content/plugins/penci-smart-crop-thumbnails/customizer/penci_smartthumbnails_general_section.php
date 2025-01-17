<?php
$options   = [];
$options[] = array(
	'label'    => __( 'Show Grid on Preview Image', 'penci-smart-crop-thumbnails' ),
	'id'       => 'penci_smart_thumbnail_grid',
	'type'     => 'soledad-fw-toggle',
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Force Clear Cache', 'penci-smart-crop-thumbnails' ),
	'description' => __( 'Force browser to reload images, after focus point has changed', 'penci-smart-crop-thumbnails' ),
	'id'          => 'penci_smart_thumbnail_force_clear_cache',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'    => __( 'Default Focus Point', 'penci-smart-crop-thumbnails' ),
	'id'       => 'penci_smart_thumbnail_default_focus_point',
	'type'     => 'soledad-fw-select',
	'default'  => 'center-center',
	'choices'  => array(
		'top-left'      => __( 'Top Left', 'penci-smart-crop-thumbnails' ),
		'top-center'    => __( 'Top Center', 'penci-smart-crop-thumbnails' ),
		'top-right'     => __( 'Top Right', 'penci-smart-crop-thumbnails' ),
		'center-left'   => __( 'Center Left', 'penci-smart-crop-thumbnails' ),
		'center-center' => __( 'Center Center', 'penci-smart-crop-thumbnails' ),
		'center-right'  => __( 'Center Right', 'penci-smart-crop-thumbnails' ),
		'bottom-left'   => __( 'Bottom Left', 'penci-smart-crop-thumbnails' ),
		'bottom-center' => __( 'Bottom Center', 'penci-smart-crop-thumbnails' ),
		'bottom-right'  => __( 'Bottom Right', 'penci-smart-crop-thumbnails' ),
	),
	'sanitize' => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Place the focus point on the upper side for portrait images', 'penci-smart-crop-thumbnails' ),
	'description' => __( 'This will override `default focus point` for portrait images.', 'penci-smart-crop-thumbnails' ),
	'id'          => 'penci_smart_thumbnail_portrait_default_top',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'    => __( 'Enlarge small images', 'penci-smart-crop-thumbnails' ),
	'id'       => 'penci_smart_thumbnail_enlarge_smaller',
	'type'     => 'soledad-fw-toggle',
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'    => __( 'Delete Useless Thumbnail Images', 'penci-smart-crop-thumbnails' ),
	'id'       => 'penci_smart_thumbnail_delete_unused_thumbnail',
	'type'     => 'soledad-fw-toggle',
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field'
);

return $options;