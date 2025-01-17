<?php

namespace PenciPodcast\Helper;

class FeedForm {

	public static function get_for_render( $post_id = null ): array {
		$field_definitions = self::field_definitions();
		$response          = [];

		foreach ( $field_definitions as $key => $field_definition ) {
			if ( ! isset( $field_definition['storage'] ) ) {
				continue;
			}

			if ( isset( $field_definition['views'] ) ) {
				if ( ! in_array( 'add', $field_definition['views'] ) && ! is_numeric( $post_id ) ) {
					continue;
				}

				if ( ! in_array( 'edit', $field_definition['views'] ) && is_numeric( $post_id ) ) {
					continue;
				}

				unset( $field_definition['views'] );
			}

			$storage = $field_definition['storage'];

			unset( $field_definition['storage'] );

			if ( is_numeric( $post_id ) ) {
				$field_definition['value'] = null;

				if ( $storage['type'] === 'meta' ) {
					$field_definition['value'] = get_post_meta( $post_id, $storage['meta'], ( $storage['meta_is_single'] ?? true ) );
				}

				if ( isset( $field_definition['options'] )
				     && ! is_array( $field_definition['value'] )
				     && ! isset( $field_definition['options'][ $field_definition['value'] ] )
				     && ! empty( $field_definition['value'] )
				) {
					$field_definition['options'] = [ $field_definition['value'] => $field_definition['value'] ] + $field_definition['options'];
				}
			}

			if ( ( ! isset( $field_definition['value'] ) || $field_definition['value'] === null ) && isset( $field_definition['default'] ) ) {
				$field_definition['value'] = $field_definition['default'];
			}

			unset( $field_definition['default'] );

			$response[ $key ] = $field_definition;
		}

		return apply_filters( PENCI_PODCAST_IMPORTER_ALIAS . '_feed_form_for_render', $response, $field_definitions );
	}

	public static function field_definitions(): array {
		$response = [];

		$response['feed_url'] = [
			'label'       => __( 'Podcast Feed URL', 'penci-podcast' ),
			'name'        => 'feed_url',
			'type'        => 'url',
			'required'    => 1,
			'placeholder' => 'https://anchor.fm/s/98e888/podcast/rss',
			'storage'     => [
				'type' => 'meta',
				'meta' => 'pencipdc_rss_feed'
			]
		];

		$post_type_options = [];

		foreach ( pencipdc_importer_supported_post_types() as $post_type ) {
			$post_type_options[ $post_type ] = get_post_type_object( $post_type )->labels->singular_name;
		}

		$response['post_status'] = [
			'label'   => __( 'Podcast Status', 'penci-podcast' ),
			'name'    => 'post_status',
			'type'    => 'select',
			'options' => [
				'publish' => __( 'Publish', 'penci-podcast' ),
				'draft'   => __( 'Save as Draft', 'penci-podcast' )
			],
			'storage' => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_publish'
			]
		];

		$response['post_author'] = [
			'label'   => __( 'Podcast Author', 'penci-podcast' ),
			'name'    => 'post_author',
			'type'    => 'wp_dropdown_users',
			'storage' => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_author'
			]
		];

		$response['post_taxonomies'] = [
			'label'   => __( 'Categories', 'penci-podcast' ),
			'name'    => 'post_taxonomies',
			'type'    => 'multiple_select',
			'options' => pencipdc_importer_get_taxonomies_select_definition( array_keys( $post_type_options ), true ),
			'storage' => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_category'
			]
		];

		$response['post_series'] = [
			'label'   => __( 'Series', 'penci-podcast' ),
			'name'    => 'post_series',
			'type'    => 'multiple_select',
			'options' => pencipdc_importer_get_taxonomies_select_definition( array_keys( $post_type_options ), false ),
			'storage' => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_series'
			]
		];

		if ( ! pencipdc_importer_feed_limit_reached() ) {

			$response['import_continuous'] = [
				'label'           => __( 'Ongoing Import (Enable to continuously import future episodes)', 'penci-podcast' ),
				'name'            => 'import_continuous',
				'type'            => 'checkbox',
				'value_unchecked' => 'off',
				'value_checked'   => 'on',
				'storage'         => [
					'type' => 'meta',
					'meta' => 'pencipdc_import_continuous'
				],
				'views'           => [ 'add' ]
			];

		}

		$response['import_images'] = [
			'label'           => __( 'Import Episode Featured Images', 'penci-podcast' ),
			'name'            => 'import_images',
			'type'            => 'checkbox',
			'value_unchecked' => 'off',
			'value_checked'   => 'on',
			'storage'         => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_images'
			]
		];

		$response['import_embed_player'] = [
			'label'           => __( 'Use an embed audio player instead of the default Penci Podcast Player (depending on your podcast host)', 'penci-podcast' ),
			'name'            => 'import_embed_player',
			'type'            => 'checkbox',
			'value_unchecked' => 'off',
			'value_checked'   => 'on',
			'storage'         => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_embed_player'
			]
		];

		$response['import_date_from'] = [
			'label'       => __( 'Date Limit', 'penci-podcast' ),
			'name'        => 'import_date_from',
			'type'        => 'date',
			'placeholder' => __( '01-01-2019', 'penci-podcast' ),
			'description' => __( 'Optional: only import episodes after a certain date.', 'penci-podcast' ),
			'storage'     => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_date_from'
			]
		];

		$response['import_content_tag'] = [
			'label'   => __( 'Imported Content Tag', 'penci-podcast' ),
			'name'    => 'import_content_tag',
			'type'    => 'select',
			'options' => [
				'content:encoded' => 'content:encoded',
				'description'     => 'description',
				'itunes:summary'  => 'itunes:summary'
			],
			'storage' => [
				'type' => 'meta',
				'meta' => 'pencipdc_content_tag'
			]
		];

		$response['import_truncate_post'] = [
			'label'       => __( 'Truncate Post Content', 'penci-podcast' ),
			'name'        => 'import_truncate_post',
			'type'        => 'number',
			'description' => __( 'Optional: Will trim the post content when imported to the character amount below.', 'penci-podcast' ) .
			                 __( 'Leave empty to skip trimming, set to 0 to skip content import.', 'penci-podcast' ),
			'storage'     => [
				'type' => 'meta',
				'meta' => 'pencipdc_truncate_post'
			]
		];

		$response['import_episode_number'] = [
			'label'           => __( 'Append episode number to post title', 'penci-podcast' ),
			'name'            => 'import_episode_number',
			'type'            => 'checkbox',
			'value_unchecked' => 'off',
			'value_checked'   => 'on',
			'storage'         => [
				'type' => 'meta',
				'meta' => 'pencipdc_import_episode_number'
			]
		];

		$response['import_prepend_title'] = [
			'label'           => __( 'Append custom text to post title', 'penci-podcast' ),
			'name'            => 'import_prepend_title',
			'type'            => 'text',
			'placeholder'     => __( 'Ex: My Podcast', 'penci-podcast' ),
			'description'     => sprintf( __( 'Optional: Add %s to display the show name.', 'penci-podcast' ), '<code>[podcast_title]</code>' ),
			'value_unchecked' => 'off',
			'value_checked'   => 'on',
			'storage'         => [
				'type' => 'meta',
				'meta' => 'pencipdc_prepend_title'
			]
		];

		return apply_filters( PENCI_PODCAST_IMPORTER_ALIAS . '_feed_form_definitions', $response );
	}

	public static function request_data_to_meta_map( $request_data ): array {
		$field_definitions = self::field_definitions();
		$response          = [];

		foreach ( $field_definitions as $field_definition ) {
			if ( ! isset( $field_definition['storage']['meta'] ) ) {
				continue;
			}

			if ( isset( $request_data[ $field_definition['name'] ] ) ) {
				if ( is_array( $request_data[ $field_definition['name'] ] ) ) {
					$response[ $field_definition['storage']['meta'] ] = array_map( 'intval', $request_data[ $field_definition['name'] ] );
					continue;
				}

				$response[ $field_definition['storage']['meta'] ] = sanitize_text_field( $request_data[ $field_definition['name'] ] );
				continue;
			}

			if ( isset( $field_definition['default'] ) ) {
				$request_data[ $field_definition['storage']['meta'] ] = $field_definition['default'];
			} else if ( isset( $field_definition['value_unchecked'] ) ) {
				$request_data[ $field_definition['storage']['meta'] ] = $field_definition['value_unchecked'];
			}
		}

		return apply_filters( PENCI_PODCAST_IMPORTER_ALIAS . '_feed_form_request_data_to_meta_map', $response, $request_data, $field_definitions );
	}

}