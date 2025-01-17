<?php
$group_color = 'Typo & Color';
$group_image = 'Image';

if ( ! class_exists( 'Penci_Vc_Params_Helper') ) {
	return;
}

if ( ! function_exists( 'pencifbs_get_pr_list' ) ) {
	function pencifbs_get_pr_list() {
		$result   = array();
		$packages = null;
		if ( class_exists( 'WooCommerce' ) ) {
			$packages = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => - 1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => array( 'post_package' ),
						),
					),
					'orderby'        => 'menu_order title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
				)
			);
		}

		if ( function_exists( 'getpaid' ) ) {
			$packages = get_posts(
				array(
					'post_type'      => 'wpi_item',
					'orderby'        => 'title',
					'order'          => 'ASC',
					'posts_per_page' => - 1,
					'post_status'    => array( 'publish' ),
					'meta_query'     => array(
						array(
							'key'     => '_wpinv_type',
							'compare' => 'IN',
							'value'   => [ 'post_package' ]
						)
					)
				)
			);
		}

		if ( $packages ) {
			foreach ( $packages as $value ) {
				$result[ $value->post_title ] = $value->ID;
			}
		} else {
			$result[''] = __( 'No Post Package Found', 'penci-frontend-submission' );
		}

		return $result;
	}
}

$theme_prefix_text = 'Soledad';
$cat_prefix_text = 'Penci';
if( function_exists('penci_get_theme_name')) {
	$theme_prefix_text = penci_get_theme_name( 'Soledad' );
	$cat_prefix_text = penci_get_theme_name( 'Penci' );
}

vc_map( array(
	'base'          => 'penci_package',
	'icon'          => get_template_directory_uri() . '/images/vc-icon.png',
	'category'      => $theme_prefix_text,
	'html_template' => PENCI_FRONTEND_SUBMISSION_PATH . '/builder/template.php',
	'weight'        => 700,
	'name'          => $cat_prefix_text . ' ' . esc_html__( 'Package', 'penci-frontend-submission' ),
	'description'   => __( 'Penci Front End Submission Package', 'penci-frontend-submission' ),
	'controls'      => 'full',
	'params'        => array_merge( array(
		array(
			'type'       => 'dropdown',
			'heading'    => __( 'Select Package Item', 'penci-frontend-submission' ),
			'param_name' => 'list_package',
			'value'      => pencifbs_get_pr_list(),
		),
		array(
			'type'        => 'penci_switch',
			'heading'     => __( 'Use image', 'penci-frontend-submission' ),
			'param_name'  => '_use_img',
			'true_state'  => 'yes',
			'false_state' => 'no',
			'default'     => 'no',
			'std'         => 'no',
		),
		array(
			'type'       => 'attach_image',
			'heading'    => esc_html__( 'Image', 'penci-frontend-submission' ),
			'param_name' => '_image',
			'dependency' => array( 'element' => '_use_img', 'value' => 'true', ),
			'group'      => $group_image,
		),
		array(
			'type'       => 'penci_responsive_sizes',
			'param_name' => 'image_width',
			'heading'    => __( 'Image width', 'penci-frontend-submission' ),
			'value'      => '',
			'std'        => '',
			'suffix'     => 'px',
			'min'        => 1,
			'dependency' => array( 'element' => '_use_img', 'value' => 'true', ),
			'group'      => $group_image,
		),
		array(
			'type'       => 'penci_responsive_sizes',
			'param_name' => 'image_height',
			'heading'    => __( 'Image height', 'penci-frontend-submission' ),
			'value'      => '',
			'std'        => '',
			'suffix'     => 'px',
			'min'        => 1,
			'dependency' => array( 'element' => '_use_img', 'value' => 'true', ),
			'group'      => $group_image,
		),
		array(
			'type'       => 'penci_responsive_sizes',
			'param_name' => 'image_mar_top',
			'heading'    => __( 'Image margin top', 'penci-frontend-submission' ),
			'value'      => '',
			'std'        => '',
			'suffix'     => 'px',
			'min'        => 1,
			'dependency' => array( 'element' => '_use_img', 'value' => 'true', ),
			'group'      => $group_image,
		),
		array(
			'type'       => 'penci_responsive_sizes',
			'param_name' => 'image_mar_bottom',
			'heading'    => __( 'Image margin bottom', 'penci-frontend-submission' ),
			'value'      => '',
			'std'        => '',
			'suffix'     => 'px',
			'min'        => 1,
			'dependency' => array( 'element' => '_use_img', 'value' => 'true', ),
			'group'      => $group_image,
		),
		array(
			'type'        => 'textarea_html',
			'class'       => '',
			'heading'     => __( 'Features', 'ultimate_vc' ),
			'param_name'  => 'content',
			'value'       => '',
			'description' => __( 'Create the features list using un-ordered list elements.', 'penci-frontend-submission' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => __( 'Button Text', 'penci-frontend-submission' ),
			'param_name'  => '_btn_text',
			'description' => __( 'Enter call to action button text', 'penci-frontend-submission' ),
		),
		array(
			'type'       => 'penci_switch',
			'heading'    => esc_html__( 'Make this pricing box as featured', 'penci-frontend-submission' ),
			'param_name' => '_featured',
			'value'      => 'no',
		),
		array(
			'type'       => 'penci_only_number',
			'param_name' => 'min_height',
			'heading'    => __( 'Minimum height for pricing item', 'penci-frontend-submission' ),
			'value'      => '',
			'std'        => '',
			'suffix'     => 'px',
			'min'        => 1,
			'max'        => 1000,
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_heading_mar_bottom',
			'heading'          => __( 'Pricing title margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_subheading_mar_b',
			'heading'          => __( 'Pricing subtitle margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_price_mar_bottom',
			'heading'          => __( 'Pricing margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_unit_mar_bottom',
			'heading'          => __( 'Price unit margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_features_martop',
			'heading'          => __( 'Features margin top', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_features_bottom',
			'heading'          => __( 'Features margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => 'item_fea_bottom',
			'heading'          => __( 'Item of list features margin bottom', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => 'btn_mar_top',
			'heading'          => __( 'Button margin top', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => 'btn_width',
			'heading'          => __( 'Button width', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => 'btn_height',
			'heading'          => __( 'Button Height', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'edit_field_class' => 'vc_col-sm-6',
		),

		// Title
		array(
			'type'             => 'textfield',
			'param_name'       => '_heading_settings',
			'heading'          => esc_html__( 'Title', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Title color', 'penci-frontend-submission' ),
			'param_name'       => '_heading_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_heading_fsize',
			'heading'          => __( 'Font Size for Pricing Title', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => '_heading_typo',
			'value'      => '',
		),

		// Typo & Color
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Background color for pricing item', 'penci-frontend-submission' ),
			'param_name'       => 'bg_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Border color for pricing item', 'penci-frontend-submission' ),
			'param_name'       => 'border_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),


		// Sub title
		array(
			'type'             => 'textfield',
			'param_name'       => '_heading_settings',
			'heading'          => esc_html__( 'Pricing Subtitle', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Pricing Subtitle', 'penci-frontend-submission' ),
			'param_name'       => '_subheading_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_subheading_fsize',
			'heading'          => __( 'Font Size for Pricing Title', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => '_subheading_typo',
			'value'      => '',
		),

		// Pricing table
		array(
			'type'             => 'textfield',
			'param_name'       => '_price_settings',
			'heading'          => esc_html__( 'Price', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Price color', 'penci-frontend-submission' ),
			'param_name'       => '_price_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_price_fsize',
			'heading'          => __( 'Font Size for Pricing', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => '_price_typo',
			'value'      => '',
		),

		// Unit table
		array(
			'type'             => 'textfield',
			'param_name'       => '_price_settings',
			'heading'          => esc_html__( 'Price Unit', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Price Unit color', 'penci-frontend-submission' ),
			'param_name'       => '_unit_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_unit_fsize',
			'heading'          => __( 'Font Size for Price Unit', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => '_unit_typo',
			'value'      => '',
		),
		// Features
		array(
			'type'             => 'textfield',
			'param_name'       => '_features_settings',
			'heading'          => esc_html__( 'Features', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Features color', 'penci-frontend-submission' ),
			'param_name'       => 'features_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => 'features_fsize',
			'heading'          => __( 'Font Size for Pricing', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => 'features_typo',
			'value'      => '',
		),

		// Button
		array(
			'type'             => 'textfield',
			'param_name'       => '_btn_settings',
			'heading'          => esc_html__( 'Button', 'penci-frontend-submission' ),
			'value'            => '',
			'group'            => $group_color,
			'edit_field_class' => 'penci-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button background color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_bgcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button border color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_borcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button text color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_text_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button background hover color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_hbgcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button border hover color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_hborcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Button text hover color', 'penci-frontend-submission' ),
			'param_name'       => 'btn_text_hcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'penci_responsive_sizes',
			'param_name'       => '_btn_fsize',
			'heading'          => __( 'Font Size for Pricing', 'penci-frontend-submission' ),
			'value'            => '',
			'std'              => '',
			'suffix'           => 'px',
			'min'              => 1,
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'       => 'google_fonts',
			'group'      => $group_color,
			'param_name' => '_btn_typo',
			'value'      => '',
		),
	), Penci_Vc_Params_Helper::extra_params() )
) );
