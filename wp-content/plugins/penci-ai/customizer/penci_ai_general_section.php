<?php
$options = [];

$options[] = array(
	'id'          => 'penci_ai_user_roles',
	'default'     => [ 'administrator' ],
	'sanitize'    => 'penci_sanitize_multiple_checkbox',
	'label'       => __( 'User roles can access', 'penci-ai' ),
	'description' => __( 'Select user roles to access the "Penci AI SmartContent Creator" option.', 'penci-ai' ),
	'type'        => 'soledad-fw-select',
	'multiple'    => 999,
	'choices'     => call_user_func( function () {
		$roles = [];

		$wp_roles = new \WP_Roles();
		if ( ! empty( $wp_roles ) ) {
			foreach ( $wp_roles->roles as $role_name => $role_info ) {
				$roles[ $role_name ] = $role_info['name'];
			}
		}

		return $roles;
	} ),
);

$options[] = array(
	'id'       => 'penci_ai_enabled_post_types',
	'default'  => [ 'post', 'page' ],
	'sanitize' => 'penci_sanitize_multiple_checkbox',
	'label'    => __( 'Enable Support in Post Types', 'soledad' ),
	'type'     => 'soledad-fw-select',
	'multiple' => 999,
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

return $options;