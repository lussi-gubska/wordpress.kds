<?php
$options = [];
$texts   = penci_ftsub_text();
foreach ( $texts as $ops => $text ) {
	$options[] = array(
		'id'      => 'pcfsub_' . $ops,
		'default' => esc_html( $text ),
		'type'    => 'soledad-fw-text',
		'label'   => esc_html__( 'Text: ', 'penci-frontend-submission' ) . '"' . esc_html( $text ) . '"',
	);
}

return $options;