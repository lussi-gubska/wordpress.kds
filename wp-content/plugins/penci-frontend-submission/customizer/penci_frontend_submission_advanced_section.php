<?php
$options   = array();
$options[] = array(
	'id'    => 'penci_frontend_submit_link_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Page URL Settings', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'          => 'penci_frontend_submit_my_post_slug',
	'transport'   => 'postMessage',
	'default'     => 'my_post',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Post Listing URL Slug', 'penci-frontend-submission' ),
	'description' => esc_html__( 'Default: ' . home_url( '/' ) . 'account/my_post/', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'          => 'penci_frontend_submit_editor_slug',
	'transport'   => 'postMessage',
	'default'     => 'editor',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Post Submit URL Slug', 'penci-frontend-submission' ),
	'description' => esc_html__( 'Default: ' . home_url( '/' ) . 'editor/', 'penci-frontend-submission' ),
);

return $options;