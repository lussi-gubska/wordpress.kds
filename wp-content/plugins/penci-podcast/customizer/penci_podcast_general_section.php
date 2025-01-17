<?php

$options = array();

$options[] = array(
	'id'    => 'pencipodcast_general_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'General Setting', 'penci-podcast' ),
);

$options[] = array(
	'id'          => 'pencipodcast_podcast_enable_player',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Podcast Player', 'penci-podcast' ),
	'description' => esc_html__( 'Enable this feature will show podcast player.', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_podcast_hide_button',
	'transport' => 'postMessage',
	'default'   => false,
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Show Hide Podcast Player Button', 'penci-podcast' ),
);

$options[] = array(
	'id'              => 'pencipodcast_podcast_hide_pos',
	'transport'       => 'postMessage',
	'default'         => 'left',
	'type'            => 'soledad-fw-toggle',
	'label'           => esc_html__( 'Hide Podcast Position', 'penci-podcast' ),
	'options'         => [
		'left'  => esc_html__( 'Left', 'penci-podcast' ),
		'right' => esc_html__( 'Right', 'penci-podcast' ),
	],
	'active_callback' => array(
		array(
			'setting'  => 'pencipodcast_podcast_hide_button',
			'operator' => '==',
			'value'    => true,
		),
	),
);

$options[] = array(
	'id'              => 'pencipodcast_podcast_global_player',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'soledad-fw-toggle',
	'label'           => esc_html__( 'Enable Global Player', 'penci-podcast' ),
	'description'     => esc_html__( 'Enable this feature will show podcast player globaly.', 'penci-podcast' ),
	'active_callback' => array(
		array(
			'setting'  => 'pencipodcast_podcast_enable_player',
			'operator' => '==',
			'value'    => true,
		),
	),
);

return $options;
