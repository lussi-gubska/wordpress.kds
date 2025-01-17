<?php
$options = [];
/* Advanced */
$options[] = array(
	'sanitize'    => 'sanitize_text_field',
	'label'       => esc_html__( 'Custom portfolio item URL prefix', 'soledad' ),
	'id'          => 'penci_pfl_custom_slug',
	'type'        => 'soledad-fw-text',
	'description' => sprintf( __( 'When you change this setting you need to Save Changes again the  %s permalinks rules here. %s', 'soledad' ), '<a class="wp-customizer-link" href="' . admin_url( 'options-permalink.php' ) . '" target="_bank">', '</a>' ),
	'input_attrs' => array(
		'placeholder' => __( 'Current portfolio slug: portfolio', 'soledad' ),
	),
);
$options[] = array(
	'sanitize'    => 'sanitize_text_field',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Custom portfolio category URL prefix', 'soledad' ),
	'id'          => 'penci_pfl_custom_catslug',
	'description' => sprintf( __( 'When you change this setting you need to Save Changes again the  %s permalinks rules here. %s', 'soledad' ), '<a class="wp-customizer-link" href="' . admin_url( 'options-permalink.php' ) . '" target="_bank">', '</a>' ),
	'input_attrs' => array(
		'placeholder' => __( 'Current category slug: portfolio-category', 'soledad' ),
	),
);
$options[] = array(
	'sanitize'    => 'sanitize_text_field',
	'label'       => esc_html__( 'Custom "Portfolio" name', 'soledad' ),
	'id'          => 'penci_pfl_custom_name',
	'type'        => 'soledad-fw-text',
);
return $options;
