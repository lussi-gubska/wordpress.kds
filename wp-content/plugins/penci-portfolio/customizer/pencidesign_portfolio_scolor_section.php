<?php
$options   = [];
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Overlay Hover Color', 'soledad' ),
	'id'       => 'penci_portfolio_overlay_color',
);
$options[] = array(
	'default'  => '0.85',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Portfolio Overlay Hover Opacity', 'soledad' ),
	'id'       => 'penci_portfolio_overlay_opacity',
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'0.05' => '0.05',
		'0.1'  => '0.1',
		'0.15' => '0.15',
		'0.2'  => '0.2',
		'0.25' => '0.25',
		'0.3'  => '0.3',
		'0.35' => '0.35',
		'0.4'  => '0.4',
		'0.45' => '0.45',
		'0.5'  => '0.5',
		'0.55' => '0.55',
		'0.6'  => '0.6',
		'0.65' => '0.65',
		'0.7'  => '0.7',
		'0.75' => '0.75',
		'0.8'  => '0.8',
		'0.85' => '0.85',
		'0.9'  => '0.9',
		'0.95' => '0.95',
		'1'    => '1',
	)
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Post Title Color', 'soledad' ),
	'id'       => 'penci_portfolio_title_color',
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Post Title Hover Color', 'soledad' ),
	'id'       => 'penci_portfolio_title_hcolor',
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Post Categories Color', 'soledad' ),
	'id'       => 'penci_portfolio_cate_color',
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Post Categories Hover Color', 'soledad' ),
	'id'       => 'penci_portfolio_cate_hcolor',
);
$options[] = array(
	'sanitize' => 'sanitize_text_field',
	'label'    => __( 'Single Portfolio Color', 'soledad' ),
	'id'       => 'penci_portfolio_single_color',
	'type'     => 'soledad-fw-header',
);
$options[] = array(
	'id'       => 'penci_portfolio_single_title_color',
	'default'  => '',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Title Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_text_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio General Text Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_text_link_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Text Link Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_text_link_hover_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Portfolio Text Link Hover Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_meta_label_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Meta Text: Label Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_meta_value_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Meta Text: Value Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_border_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'General Border Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_relate_title_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Title Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_relate_title_hover_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Title Hover Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_relate_cat_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Category Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_relate_cat_hover_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Category Hover Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_carousel_background_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Carousel Background Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_carousel_border_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Carousel Border Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_carousel_active_background_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Carousel Active Background Color', 'soledad' ),
);
$options[] = array(
	'default'  => '',
	'id'       => 'penci_portfolio_single_carousel_active_border_color',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => __( 'Relate Project Carousel Active Border Color', 'soledad' ),
);

return $options;
