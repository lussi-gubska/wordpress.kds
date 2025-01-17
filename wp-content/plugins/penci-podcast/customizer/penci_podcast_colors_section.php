<?php
$options = array();

$options[] = array(
	'id'        => 'pencipodcast_player_bgcolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Player Background Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_textcolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Player Text Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_activecolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Player Text Active Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_boderscolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Player Borders Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_trackbgcolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Audio Tracking Background Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_closetxtcolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Close Text Color', 'penci-podcast' ),
);

$options[] = array(
	'id'        => 'pencipodcast_player_closebgcolor',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Close Background Color', 'penci-podcast' ),
);

return $options;
