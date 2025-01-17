<?php
function penci_fe_get_published_post_types() {
	$post_types           = get_post_types( [ 'public' => true ], 'objects' ); // Get all public post types
	$published_post_types = [];

	foreach ( $post_types as $post_type => $post_type_object ) {
		// Check if there are published posts of this post type
		$published_post_types[ $post_type ] = $post_type_object->labels->name; // Add to array
	}

	return $published_post_types;
}

function penci_fe_get_tax() {
	$post_types  = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ), 'object' );
	$tax_options = [];
	foreach ( $post_types as $post_type => $type ) {
		foreach ( get_object_taxonomies( $type->name, 'object' ) as $tax_name => $tax_info ) {
			if ( ! in_array( $tax_name, [ 'post_format', 'elementor_library_type', 'penci_block_category' ] ) ) {
				$tax_options[ $tax_name ] = $type->label . ' - ' . $tax_info->label;
			}
		}
	}

	return $tax_options;
}

function penci_fe_meta_values( $meta_key ) {
	global $wpdb;

	// Query to count total posts for each meta key value
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT meta_value, COUNT(*) as total 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = %s 
             GROUP BY meta_value",
			$meta_key
		),
		ARRAY_A
	);

	return $results;
}

function penci_fe_should_render( $id ) {
	$settings = get_post_meta( $id, '_penci_filter_options', true );

	// Extract filter conditions and fallback values
	$cod      = $settings['filter_conditions'] ?? 'homepage';
	$page_ids = $settings['filter_conditions_page_ids'] ?? [];
	$post_ids = $settings['filter_conditions_post_ids'] ?? [];

	// Early exits for performance
	if ( $cod === 'homepage' && ( is_home() || is_front_page() ) ) {
		return true;
	}

	if ( $cod === 'archive' && is_archive() ) {
		return true;
	}

	if ( $cod === 'pages' && is_page() ) {
		return empty( $page_ids ) || in_array( get_the_ID(), (array) $page_ids, true );
	}

	if ( $cod === 'posts' && is_single() ) {
		return empty( $post_ids ) || in_array( get_the_ID(), (array) $post_ids, true );
	}

	return false;
}