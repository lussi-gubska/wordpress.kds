<?php
$options = [];
$texts   = penci_livesub_text();
foreach ( $texts as $ops => $text ) {
	$options[] = array(
		'id'      => 'pclb_trans_' . $ops,
		'default' => esc_html( $text ),
		'type'    => 'soledad-fw-text',
		'label'   => esc_html__( 'Text: ', 'penci-liveblog' ) . '"' . esc_html( $text ) . '"',
	);
}

return $options;