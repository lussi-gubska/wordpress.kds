<?php
$options = [];
$texts   = pencipw_text_translation_list();
foreach ( $texts as $ops => $text ) {
	$options[] = array(
		'id'      => 'pencipw_text_' . $ops,
		'default' => esc_html( $text ),
		'type'    => 'soledad-fw-text',
		'label'   => esc_html__( 'Text: ', 'penci-paywall' ) . '"' . esc_html( $text ) . '"',
	);
}

return $options;