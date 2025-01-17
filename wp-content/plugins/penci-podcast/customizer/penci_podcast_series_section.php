<?php

$options = array();

$options[] = array(
	'id'    => 'pencipodcast_series_podcast_element_settings',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Podcast Elements', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_series_show_featured',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Featured Image', 'penci-podcast' ),
	'description' => esc_html__( 'Show featured image single podcast.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_series_show_podcast_author',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Podcast Author', 'penci-podcast' ),
	'description' => esc_html__( 'Show podcast author on podcast meta container.', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_series_show_podcast_total_episode',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Episode Counter', 'penci-podcast' ),
	'description' => wp_kses( __( 'Show or hide episode counter', 'penci-podcast' ), wp_kses_allowed_html() ),
);

$options[] = array(
	'id'          => 'pencipodcast_series_show_subscribe',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Subscribe Button', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_series_enable_post_excerpt',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Post Excerpt', 'penci-podcast' ),
	'description' => esc_html__( 'Show post excerpt on this block.', 'penci-podcast' ),
);

return $options;
