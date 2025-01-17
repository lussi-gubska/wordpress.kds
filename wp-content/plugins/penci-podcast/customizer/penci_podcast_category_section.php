<?php

$options   = array();
$options[] = array(
	'id'    => 'pencipodcast_single_podcast',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Template & Layout Settings', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_items_position',
	'transport' => 'postMessage',
	'default'   => 'top',
	'type'      => 'soledad-fw-select',
	'label'     => esc_html__( 'Featured Image Position', 'penci-podcast' ),
	'choices'   => array(
		'top'  => 'Top',
		'left' => 'Left',
	),
);

$options[] = array(
	'id'          => 'pencipodcast_items_col',
	'transport'   => 'postMessage',
	'default'     => '2',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Podcast Template', 'penci-podcast' ),
	'description' => esc_html__( 'Choose your single podcast template.', 'penci-podcast' ),
	'choices'     => array(
		'2' => '2 Columns',
		'3' => '3 Columns',
		'4' => '4 Columns',
		'5' => '5 Columns',
	),
);

$options[] = array(
	'id'          => 'pencipodcast_single_layout',
	'transport'   => 'postMessage',
	'default'     => 'left-sidebar',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Podcast Layout', 'penci-podcast' ),
	'description' => esc_html__( 'Choose your single podcast layout', 'penci-podcast' ),
	'choices'     => array(
		'left-sidebar'  => 'Left Sidebar',
		'right-sidebar' => 'Right Sidebar',
		'two-sidebar'   => 'Two Sidebar',
		'no-sidebar'    => 'No Sidebar',
	),
);


$options[] = array(
	'id'    => 'pencipodcast_single_podcast_element_settings',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Podcast Elements', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_show_featured',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Featured Image', 'penci-podcast' ),
	'description' => esc_html__( 'Show featured image single podcast.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_show_podcast_author',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Podcast Author', 'penci-podcast' ),
	'description' => esc_html__( 'Show podcast author on podcast meta container.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_show_podcast_total_episode',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Episode Counter', 'penci-podcast' ),
	'description' => wp_kses( __( 'Show or hide episode counter', 'penci-podcast' ), wp_kses_allowed_html() ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_show_post_option',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Podcast Meta Option', 'penci-podcast' ),
	'description' => esc_html__( 'Show Podcast meta option on podcast aside.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_show_subscribe',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Subscribe Button', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_enable_post_excerpt',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Post Excerpt', 'penci-podcast' ),
	'description' => esc_html__( 'Show post excerpt on this block.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_single_excerpt_length',
	'transport'   => 'postMessage',
	'default'     => 20,
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Excerpt Length', 'penci-podcast' ),
	'description' => esc_html__( 'Set the word length of excerpt on post.', 'penci-podcast' ),
	'choices'     => array(
		'min'  => '0',
		'max'  => '200',
		'step' => '1',
	),
);

$options[] = array(
	'id'          => 'pencipodcast_single_excerpt_ellipsis',
	'transport'   => 'postMessage',
	'default'     => '...',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Excerpt Ellipsis', 'penci-podcast' ),
	'description' => esc_html__( 'Define excerpt ellipsis', 'penci-podcast' ),
);

return $options;
