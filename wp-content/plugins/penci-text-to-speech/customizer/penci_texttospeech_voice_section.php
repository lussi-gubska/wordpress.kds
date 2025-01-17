<?php
$options   = [];
$options[] = array(
	'default'  => 'before-content',
	'label'    => __( 'Player Position', 'soledad' ),
	'id'       => 'penci_texttospeech_position',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'choices'  => array(
		"before-content" => esc_html__( 'Before Post Content', 'soledad' ),
		"after-content"  => esc_html__( 'After Post Content', 'soledad' ),
		"before-title"   => esc_html__( 'Before Post Categories', 'soledad' ),
		"after-title"    => esc_html__( 'After Post Meta', 'soledad' ),
		"top-fixed"      => esc_html__( 'Top Fixed', 'soledad' ),
		"bottom-fixed"   => esc_html__( 'Bottom Fixed', 'soledad' ),
		"shortcode"      => esc_html__( 'Shortcode [penci-texttospeech]', 'soledad' )
	)
);

$options[] = array(
	'default'  => 'style-4',
	'label'    => __( 'Player Style', 'soledad' ),
	'id'       => 'penci_texttospeech_style',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'choices'  => array(
		'style-1' => esc_html__( 'Round Player', 'soledad' ),
		'style-2' => esc_html__( 'Rounded Player', 'soledad' ),
		'style-3' => esc_html__( 'Squared Player', 'soledad' ),
		'style-4' => esc_html__( 'WordPress Default Player', 'soledad' ),
		'style-5' => esc_html__( 'Chrome Style Player', 'soledad' ),
		'style-6' => esc_html__( 'Browser Default Player', 'soledad' )
	)
);

$options[] = array(
	'default'  => 'none',
	'label'    => __( 'Download Link', 'soledad' ),
	'id'       => 'penci_texttospeech_link',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'choices'  => array(
		'none'                 => esc_html__( 'Do not show', 'soledad' ),
		'backend'              => esc_html__( 'Backend Only', 'soledad' ),
		'frontend'             => esc_html__( 'Frontend Only', 'soledad' ),
		'backend-and-frontend' => esc_html__( 'Backend and Frontend', 'soledad' )
	)
);

$options[] = array(
	'label'       => __( 'Description Before Audio Player', 'soledad' ),
	'description' => __( 'Add a text or HTML markup before the player', 'soledad' ),
	'id'          => 'penci_texttospeech_before_player_switcher',
	'type'        => 'soledad-fw-textarea',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field'
);

$options[] = array(
	'label'       => __( 'Description After Audio Player', 'soledad' ),
	'description' => __( 'Add a text or HTML markup after the player', 'soledad' ),
	'id'          => 'penci_texttospeech_after_player_switcher',
	'type'        => 'soledad-fw-textarea',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_choices_field'
);

$options[] = array(
	'label'       => __( 'Autoplay', 'soledad' ),
	'description' => __( 'Autoplay the audio after page load. May not work in some browsers due to Browser Autoplay Policy. More details for <a href="https://developers.google.com/web/updates/2017/09/autoplay-policy-changes" target="_blank" rel="noreferrer">WebKit Browsers</a> and <a href="https://hacks.mozilla.org/2019/02/firefox-66-to-block-automatically-playing-audible-video-and-audio/" target="_blank" rel="noreferrer">Firefox</a>', 'soledad' ),
	'id'          => 'penci_texttospeech_autoplay',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Loop', 'soledad' ),
	'description' => __( 'Loop the audio playback', 'soledad' ),
	'id'          => 'penci_texttospeech_loop',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Speed Controls', 'soledad' ),
	'description' => __( 'Speed controls for the audio player the audio after page load', 'soledad' ),
	'id'          => 'penci_texttospeech_speed_controls',
	'type'        => 'soledad-fw-toggle',
	'default'     => true,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Speed Block Title', 'soledad' ),
	'description' => __( 'Specify the title for speeds section', 'soledad' ),
	'id'          => 'penci_texttospeech_speed_title',
	'type'        => 'soledad-fw-text',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'default'     => '0.25, 0.5, 0.75, 1.25, 1.5, 1.75',
	'id'          => 'penci_texttospeech_speed',
	'label'       => __( 'Available Speed', 'soledad' ),
	'description' => 'Specify speeds separated by commas. Speed must be in range from 0.1 to 16. Use period for decimal numbers, for example: 1.2, 1.5, 1.75',
	'type'        => 'soledad-fw-text',
	'priority'    => 1,
);

$options[] = array(
	'label'       => __( 'Synthesize Audio on Save', 'soledad' ),
	'description' => __( 'Auto re-generate the audio file when you updating post content', 'soledad' ),
	'id'          => 'penci_texttospeech_auto_generation',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Add Custom Fields', 'soledad' ),
	'description' => __( 'Add audio meta-data to the post Custom Fields', 'soledad' ),
	'id'          => 'penci_texttospeech_post_meta',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);

$options[] = array(
	'label'       => __( 'Visible in the Media Library', 'soledad' ),
	'description' => __( 'Make the audio visible and available in the Media Library', 'soledad' ),
	'id'          => 'penci_texttospeech_media_library',
	'type'        => 'soledad-fw-toggle',
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field'
);


$options[] = array(
	'label'       => __( 'Audio Preload', 'soledad' ),
	'description' => __( 'The preload attribute specifies if and how the audio file should be loaded when the page loads.', 'soledad' ),
	'id'          => 'penci_texttospeech_preload',
	'type'        => 'soledad-fw-select',
	'default'     => 'none',
	'choices'     => [
		'none'     => esc_html__( 'None', 'penci-text-to-speech' ),
		'metadata' => esc_html__( 'Metadata', 'penci-text-to-speech' ),
		'auto'     => esc_html__( 'Auto', 'penci-text-to-speech' ),
		'backend'  => esc_html__( 'Backend', 'penci-text-to-speech' ),
	],
);

$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Custom Background Color for Media Player',
	'id'       => 'penci_texttospeech_bgcolor',
	'priority' => 2
);

$options[] = array(
	'label'    => '',
	'id'       => 'penci_texttospeech_mtm',
	'type'     => 'soledad-fw-hidden',
	'sanitize' => 'absint',
	'default'  => '14',
);
$options[] = array(
	'label'    => __( 'Custom Top Spacing for Media Player', 'soledad' ),
	'id'       => 'penci_texttospeech_mt',
	'type'     => 'soledad-fw-size',
	'default'  => '',
	'sanitize' => 'absint',
	'ids'      => array(
		'desktop' => 'penci_texttospeech_mt',
		'mobile'  => 'penci_texttospeech_mtm',
	),
	'choices'  => array(
		'desktop' => array(
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'edit'    => true,
			'unit'    => 'px',
			'default' => '',
		),
		'mobile'  => array(
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'edit'    => true,
			'unit'    => 'px',
			'default' => '',
		),
	),
);

$options[] = array(
	'label'    => '',
	'id'       => 'penci_texttospeech_mbm',
	'type'     => 'soledad-fw-hidden',
	'sanitize' => 'absint',
	'default'  => '14',
);
$options[] = array(
	'label'    => __( 'Custom Bottom Spacing for Media Player', 'soledad' ),
	'id'       => 'penci_texttospeech_mb',
	'type'     => 'soledad-fw-size',
	'default'  => '',
	'sanitize' => 'absint',
	'ids'      => array(
		'desktop' => 'penci_texttospeech_mb',
		'mobile'  => 'penci_texttospeech_mbm',
	),
	'choices'  => array(
		'desktop' => array(
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'edit'    => true,
			'unit'    => 'px',
			'default' => '',
		),
		'mobile'  => array(
			'min'     => 1,
			'max'     => 500,
			'step'    => 1,
			'edit'    => true,
			'unit'    => 'px',
			'default' => '',
		),
	),
);

return $options;