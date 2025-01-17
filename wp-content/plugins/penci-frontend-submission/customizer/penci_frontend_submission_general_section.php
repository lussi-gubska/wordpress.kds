<?php

$options   = array();
$options[] = array(
	'id'    => 'penci_frontend_submit_tab_header_1',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'General Options', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'       => 'penci_frontend_submit_enabled_post_types',
	'default'  => 'post',
	'sanitize' => 'penci_sanitize_multiple_checkbox',
	'label'    => __( 'Enable Support in Post Types', 'soledad' ),
	'type'     => 'soledad-fw-select',
	//'multiple' => 999,
	'choices'  => call_user_func( function () {
		$exclude    = array(
			'attachment',
			'revision',
			'product',
			'nav_menu_item',
			'safecss',
			'penci-block',
			'penci_builder',
			'custom-post-template',
			'archive-template',
		);
		$registered = get_post_types( [ 'show_in_nav_menus' => true ], 'objects' );
		$types      = array();


		foreach ( $registered as $post ) {

			if ( in_array( $post->name, $exclude ) ) {

				continue;
			}

			$types[ $post->name ] = $post->label;
		}

		return $types;
	} )
);

$options[] = array(
	'id'       => 'penci_frontend_submit_status',
	'default'  => 'pending',
	'sanitize' => 'penci_sanitize_multiple_checkbox',
	'label'    => __( 'Default Submit Status', 'penci-frontend-submission' ),
	'type'     => 'soledad-fw-select',
	'choices'  => [
		'draft'   => __( 'Draft', 'penci-frontend-submission' ),
		'pending' => __( 'Pending', 'penci-frontend-submission' ),
		'publish' => __( 'Publish', 'penci-frontend-submission' ),
	]
);


$options[] = array(
	'id'       => 'penci_frontend_submit_acf_groups',
	'sanitize' => 'penci_sanitize_multiple_checkbox',
	'label'    => __( 'ACF/SCF Groups', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'choices'  => call_user_func( function () {
		$types        = [ '' => 'None' ];
		if ( function_exists('acf_get_field_groups')){
		$field_groups = acf_get_field_groups();
		if ( ! empty( $field_groups ) ) {
			foreach ( $field_groups as $group ) {
				$types[ $group['ID'] ] = $group['title'];
			}
		}
	}

		return $types;
	} )
);

$options[] = array(
	'id'          => 'penci_frontend_submit_enable_add_media',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Add Media', 'penci-frontend-submission' ),
	'description' => esc_html__( 'Enable add media button on frontend post editor.', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'          => 'penci_frontend_submit_maxupload',
	'transport'   => 'postMessage',
	'default'     => '2',
	'type'        => 'soledad-fw-slider',
	'label'       => esc_html__( 'Maxupload Size', 'penci' ),
	'description' => esc_html__( 'Set maxupload file size.', 'penci' ),
	'choices'     => array(
		'min'  => '1',
		'max'  => '10',
		'step' => '1',
	),
);

$options[] = array(
	'id'    => 'penci_frontend_submit_tab_header_2',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Premium Subscription Option', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'          => 'penci_frontend_submit_enable_woocommerce',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Enable Premium Submission Mode', 'penci-frontend-submission' ),
	'description' => esc_html__( 'By enabling this option, the site user will need to buy post package before they can submit their post using frontend submit.', 'penci-frontend-submission' ),
);

$options[] = array(
	'id'        => 'penci_frontend_subpage',
	'transport' => 'postMessage',
	'default'   => 'none',
	'type'      => 'soledad-fw-select',
	'label'     => esc_html__( 'Subscribe Page', 'penci-paywall' ),
	'choices'   => call_user_func( function () {
		$pages       = get_pages( array( 'post_status' => 'publish' ) );
		$page_option = array( 'none' => esc_html__( '-Select Page-', 'penci-paywall' ) );

		foreach ( $pages as $page ) {
			$page_option[ $page->ID ] = esc_html( $page->post_title );
		}

		return $page_option;
	} ),
);

return $options;