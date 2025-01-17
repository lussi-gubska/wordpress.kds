<?php
$options   = [];
$options[] = array(
	'default'     => false,
	'sanitize'    => 'penci_sanitize_checkbox_field',
	'label'       => __( 'Use Classic Editor for Edit Portfolio Projects?', 'soledad' ),
	'description' => __( 'Edit the portfolio projects using WP Block Editor by default, if you want to use Classic Editor to edit it, check this option.', 'soledad' ),
	'id'          => 'penci_portfolio_classic_editor',
	'type'        => 'soledad-fw-toggle',
);
$options[] = array(
	'id'          => 'penci_portfolio_cspage',
	'default'     => '',
	'sanitize'    => 'penci_sanitize_number_field',
	'label'       => __( 'Custom Portfolio Main Page', 'soledad' ),
	'description' => __( 'Fill the ID of Page you want to display as portoflio main page here (Eg: 10). You can check <a class="wp-customizer-link" href="https://www.competethemes.com/blog/find-page-id/" target="_blank">this guide</a> to know how to find ID of a Page.', 'soledad' ),
	'type'        => 'soledad-fw-number',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Sidebar On Portfolio Categories', 'soledad' ),
	'id'       => 'penci_portfolio_cat_enable_sidebar',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Sidebar On Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_single_enable_sidebar',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'id'       => 'penci_portfolio_single_enable_left_sidebar',
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Left Sidebar On Single Portfolio', 'soledad' ),
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Two Sidebars On Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_single_enable_2sidebar',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'     => 'main-sidebar',
	'sanitize'    => 'penci_sanitize_choices_field',
	'label'       => __( 'Custom Sidebar for Single Portfolio', 'soledad' ),
	'id'          => 'penci_sidebar_single_portfolio',
	'description'=>__('If sidebar your choice is empty, will display Main Sidebar','soledad'),
	'type'        => 'soledad-fw-select',
	'choices'     => get_list_custom_sidebar_option()
);
$options[] = array(
	'default'  => 'style-1',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Single Portfolio Style', 'soledad' ),
	'id'       => 'penci_single_portfolio_style',
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'style-1' => __( 'Style 1', 'soledad' ),
		'style-2' => __( 'Style 2', 'soledad' ),
		'style-3' => __( 'Style 3', 'soledad' ),
	)
);
$options[] = array(
	'default'  => 'style-1',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Single Portfolio Social Share Style', 'soledad' ),
	'id'       => 'penci_single_portfolio_social_share_style',
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'style-1' => __( 'Style 1', 'soledad' ),
		'style-2' => __( 'Style 2', 'soledad' ),
		'style-3' => __( 'Style 3', 'soledad' ),
		'style-4' => __( 'Style 4', 'soledad' ),
	)
);
$options[] = array(
	'default'     => 'main-sidebar-left',
	'sanitize'    => 'penci_sanitize_choices_field',
	'label'       => __( 'Custom Sidebar Left for Single Portfolio', 'soledad' ),
	'id'          => 'penci_sidebar_left_single_portfolio',
	'description'=>__('If sidebar your choice is empty, will display Main Sidebar Left. This option just use when you enable 2 sidebars for Single Portfolio','soledad'),
	'type'        => 'soledad-fw-select',
	'choices'     => get_list_custom_sidebar_option()
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Hide Featured Image on Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_hide_featured_image_single',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Hide Share Box on Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_share_box',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Next/Prev Project on Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_next_prev_project',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Related Project on Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_related_project',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => 'Next Project',
	'sanitize' => 'sanitize_text_field',
	'label'    => __( 'Custom Text for Next Project Button', 'soledad' ),
	'id'       => 'penci_portfolio_next_text',
	'type'     => 'soledad-fw-text',
);
$options[] = array(
	'default'  => 'Previous Project',
	'sanitize' => 'sanitize_text_field',
	'label'    => __( 'Custom Text for Previous Project Button', 'soledad' ),
	'id'       => 'penci_portfolio_prev_text',
	'type'     => 'soledad-fw-text',
);
$options[] = array(
	'default'  => __( 'Related Projects', 'soledad' ),
	'sanitize' => 'sanitize_text_field',
	'label'    => __( 'Custom Text for Related Projects Text', 'soledad' ),
	'id'       => 'penci_portfolio_related_text',
	'type'     => 'soledad-fw-text',
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_sanitize_checkbox_field',
	'label'    => __( 'Enable Comment on Single Portfolio', 'soledad' ),
	'id'       => 'penci_portfolio_enable_comment',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'  => 'masonry',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Portfolio Category Layout', 'soledad' ),
	'id'       => 'penci_portfolio_layout',
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'masonry' => __('Masonry Layout','soledad' ),
		'grid'    => __('Grid Layout','soledad' ),
	)
);
$options[] = array(
	'default'  => 'text_overlay',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Portfolio Category Layout', 'soledad' ),
	'id'       => 'penci_portfolio_item_style',
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'text_overlay' => __('Text Overlay','soledad' ),
		'below_img'    => __('Text Below Image','soledad' ),
	)
);
$options[] = array(
	'id'       => 'penci_portfolio_layout',
	'default'  => 'carousel',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Portfolio Category Layout', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'grid'     => __('Grid','soledad' ),
		'carousel' => __('Carousel','soledad' ),
	)
);
$options[] = array(
	'default'  => '3',
	'id'       => 'penci_single_portfolio_related_col',
	'sanitize' => 'penci_sanitize_choices_field',
	'label'    => __( 'Single Portfolio Related Projects Columns', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'choices'  => array(
		'2' => __( '2 Columns', 'soledad' ),
		'3' => __( '3 Columns', 'soledad' ),
		'4' => __( '4 Columns', 'soledad' ),
	)
);
$options[] = array(
	'default'  => '3',
	'type'     => 'soledad-fw-size',
	'sanitize' => 'absint',
	'label'    => __( 'Single Portfolio Related Projects Item', 'soledad' ),
	'id'       => 'penci_single_portfolio_related_num',
	'ids'      => array(
		'desktop' => 'penci_single_portfolio_related_num',
	),
	'choices'  => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 100,
			'step' => 1,
			'edit' => true,
			'unit' => '',
		),
	),
);

return $options;
