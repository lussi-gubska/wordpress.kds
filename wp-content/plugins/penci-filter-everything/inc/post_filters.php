<?php
function penci_merge_meta_fields( $meta_query_args ) {
	$merged_values = array();

	foreach ( $meta_query_args as $arg ) {
		if ( is_array( $arg ) ) {
			$key = $arg['key'];
			// Group values by their keys
			if ( ! isset( $merged_values[ $key ] ) ) {
				$merged_values[ $key ] = array();
			}
			$merged_values[ $key ][] = $arg['value'];
		}
	}

	$merged_meta_query_args = array();
	foreach ( $merged_values as $key => $values ) {
		$merged_meta_query_args[] = array(
			'key'     => $key,
			'value'   => $values,
			'compare' => 'IN',
		);
	}

	return $merged_meta_query_args;
}

function penci_is_filter_request() {
	$is_f_request = false;

	$is_pjax = isset( $_SERVER['HTTP_X_PJAX'] ) && $_SERVER['HTTP_X_PJAX'] === 'true';

	if ( isset( $_GET['pcmtf'] ) && $_GET['pcmtf'] && $is_pjax ) {
		$is_f_request = true;
	}

	return $is_f_request;
}

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( is_archive() || is_front_page() || is_front_page() ) ) {

		// post meta
		$filters = isset( $_GET['pcmtf'] ) && $_GET['pcmtf'] ? $_GET['pcmtf'] : '';
		if ( $filters ) {
			$meta_fields = $filters ? explode( '|', $filters ) : [];
			$meta_query  = [];

			if ( ! empty( $meta_fields ) ) {

				foreach ( $meta_fields as $field ) {
					$field        = explode( ':', $field );
					$meta_query[] = [
						'key'   => isset( $field[0] ) ? $field[0] : '',
						'value' => isset( $field[1] ) ? htmlspecialchars( $field[1], ENT_QUOTES, 'UTF-8' ) : '',
					];
				}
			}

			if ( ! empty( $meta_query ) ) {
				$meta_query             = penci_merge_meta_fields( $meta_query );
				$meta_query['relation'] = 'OR';
				$query->set( 'meta_query', $meta_query );
			}
		}

		// handle the taxonomy

		$tax_filters = isset( $_GET['pcmtt'] ) && $_GET['pcmtt'] ? sanitize_text_field( $_GET['pcmtt'] ) : '';
		if ( $tax_filters ) {
			$tax_fields = $tax_filters ? explode( '|', $tax_filters ) : [];
			$tax_query  = [];

			if ( ! empty( $tax_fields ) ) {

				foreach ( $tax_fields as $term ) {
					$term        = explode( ':', $term );
					$tax_query[] = [
						'taxonomy' => isset( $term[0] ) ? $term[0] : '',
						'terms'    => isset( $term[1] ) ? $term[1] : '',
					];
				}
			}

			if ( ! empty( $tax_query ) ) {
				$tax_query['relation'] = 'OR';
				$query->set( 'tax_query', $tax_query );
			}

			if ( get_theme_mod( '' ) ) {
				if ( $query->is_category() ) {
					$query->set( 'cat', '' );
				} elseif ( $query->is_tag() ) {
					$query->set( 'tag_id', '' );
				}
			}

			// handle post type

			$post_types = isset( $_GET['pcptype'] ) && $_GET['pcptype'] ? sanitize_text_field( $_GET['pcptype'] ) : 'post';
			$query->set( 'post_type', explode( '.', $post_types ) );

		}
	}
} );

function penci_fe_elementor_query( $query_args ) {
	// post meta
	$filters     = isset( $_GET['pcmtf'] ) && $_GET['pcmtf'] ? $_GET['pcmtf'] : '';
	$meta_fields = $filters ? explode( '|', $filters ) : [];
	$meta_query  = [];

	if ( ! empty( $meta_fields ) ) {

		foreach ( $meta_fields as $field ) {
			$field        = explode( ':', $field );
			$meta_query[] = [
				'key'   => isset( $field[0] ) ? $field[0] : '',
				'value' => isset( $field[1] ) ? htmlspecialchars( $field[1], ENT_QUOTES, 'UTF-8' ) : '',
			];
		}
	}

	if ( ! empty( $meta_query ) ) {
		$meta_query               = penci_merge_meta_fields( $meta_query );
		$meta_query['relation']   = 'OR';
		$query_args['meta_query'] = $meta_query;
	}

	// handle the taxonomy

	$tax_filters = isset( $_GET['pcmtt'] ) && $_GET['pcmtt'] ? sanitize_text_field( $_GET['pcmtt'] ) : '';
	$tax_fields  = $tax_filters ? explode( '|', $tax_filters ) : [];
	$tax_query   = [];

	if ( ! empty( $tax_fields ) ) {

		foreach ( $tax_fields as $term ) {
			$term        = explode( ':', $term );
			$tax_query[] = [
				'taxonomy' => isset( $term[0] ) ? $term[0] : '',
				'terms'    => isset( $term[1] ) ? $term[1] : '',
			];
		}
	}

	if ( ! empty( $tax_query ) ) {
		$tax_query['relation']   = 'OR';
		$query_args['tax_query'] = $tax_query;
	}

	$post_types              = isset( $_GET['pcptype'] ) && $_GET['pcptype'] ? sanitize_text_field( $_GET['pcptype'] ) : 'post';
	$query_args['post_type'] = explode( '.', $post_types );

	return $query_args;
}