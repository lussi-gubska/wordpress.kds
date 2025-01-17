<?php
$options = array();

$texts = pencipdc_translate();
foreach ( $texts as $mod => $translate ) {
	$options[] = array(
		'id'        => "pencipdc_translate_{$mod}",
		'transport' => 'postMessage',
		'default'   => $translate,
		'type'      => 'soledad-fw-text',
		'label'     => esc_html__( "Text: {$translate}", 'penci-podcast' ),
	);
}

return $options;
