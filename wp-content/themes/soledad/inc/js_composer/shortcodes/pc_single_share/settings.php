<?php
$group_icon  = 'Icon';
$group_color = 'Typo & Color';

vc_map( array(
	'base'          => "pc_single_share",
	'icon'          => PENCI_SOLEDAD_URL . '/images/vc-icon.png',
	'category'      => penci_get_theme_name( 'Post Builder' ),
	'html_template' => PENCI_SOLEDAD_DIR . '/inc/js_composer/shortcodes/pc_single_share/frontend.php',
	'weight'        => 910,
	'name'          => penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'Post Builder - Social Share', 'soledad' ),
	'description'   => 'Post Builder - Social Share',
	'controls'      => 'full',
	'params'        => array_merge( array(
		array(
			'type'             => 'dropdown',
			'heading'          => esc_html__( 'Share Style', 'soledad' ),
			'param_name'       => 'penci_single_style_cscount',
			'value'            => array(
				'Style 1'  => 's1',
				'Style 2'  => 's2',
				'Style 3'  => 's3',
				'Style 4'  => 'n1',
				'Style 5'  => 'n2',
				'Style 6'  => 'n3',
				'Style 7'  => 'n4',
				'Style 8'  => 'n5',
				'Style 9'  => 'n6',
				'Style 10' => 'n7',
				'Style 11' => 'n8',
				'Style 12' => 'n9',
				'Style 13' => 'n10',
				'Style 14' => 'n11',
				'Style 15' => 'n12',
				'Style 16' => 'n13',
				'Style 17' => 'n14',
				'Style 18' => 'n15',
				'Style 19' => 'n16',
				'Style 20' => 'n17',
				'Style 21' => 'n18',
				'Style 22' => 'n19',
				'Style 23' => 'n20',
			),
			'edit_field_class' => 'vc_col-sm-6'
		),
		array(
			'type'             => 'penci_switch',
			'heading'          => esc_html__( 'Hide Comment?', 'soledad' ),
			'param_name'       => 'penci_single_meta_comment',
			'edit_field_class' => 'vc_col-sm-6'
		),
		array(
			'type'             => 'penci_switch',
			'heading'          => esc_html__( 'Hide Label?', 'soledad' ),
			'param_name'       => 'penci_single_share_label',
			'edit_field_class' => 'vc_col-sm-6'
		),
		array(
			'type'             => 'penci_switch',
			'heading'          => esc_html__( 'Hide Post Like?', 'soledad' ),
			'param_name'       => 'penci__hide_share_plike',
			'edit_field_class' => 'vc_col-sm-6'
		),
		array(
			'type'             => 'dropdown',
			'heading'          => esc_html__( 'Social Align', 'soledad' ),
			'param_name'       => 'meta_align',
			'value'            => array(
				esc_html__( 'Center', 'soledad' ) => 'center',
				esc_html__( 'Left', 'soledad' )   => 'left',
				esc_html__( 'Right', 'soledad' )  => 'right',
			),
			'edit_field_class' => 'vc_col-sm-6'
		),
	), array(
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Comment Text Color', 'soledad' ),
			'param_name'       => 'comment_text_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Label Color', 'soledad' ),
			'param_name'       => 'label_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Label Icon Color', 'soledad' ),
			'param_name'       => 'label_icolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Label Borders Color', 'soledad' ),
			'param_name'       => 'label_bdcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Label Label Background Color', 'soledad' ),
			'param_name'       => 'label_bgcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Like Button Color', 'soledad' ),
			'param_name'       => 'likebtn_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Like Button Borders Color', 'soledad' ),
			'param_name'       => 'likebtn_bcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Background Color', 'soledad' ),
			'param_name'       => 'social_bgcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Hover Background Color', 'soledad' ),
			'param_name'       => 'social_bghcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Color', 'soledad' ),
			'param_name'       => 'social_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Hover Color', 'soledad' ),
			'param_name'       => 'social_hcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Borders Color', 'soledad' ),
			'param_name'       => 'social_bcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Social Hover Borders Color', 'soledad' ),
			'param_name'       => 'social_bhcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Plus Button Color', 'soledad' ),
			'param_name'       => 'plus_btn_color',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Plus Button Hover Color', 'soledad' ),
			'param_name'       => 'plus_btn_hcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Plus Button Background Color', 'soledad' ),
			'param_name'       => 'plus_btn_bgcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'type'             => 'colorpicker',
			'heading'          => esc_html__( 'Plus Button Background Hover Color', 'soledad' ),
			'param_name'       => 'plus_btn_bghcolor',
			'group'            => $group_color,
			'edit_field_class' => 'vc_col-sm-6',
		),
	), Penci_Vc_Params_Helper::extra_params() )
) );
