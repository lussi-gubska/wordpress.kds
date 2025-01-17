<?php
if ( ! function_exists( 'pencipdc_cache' ) ) {
	/**
	 * Podcast Cache
	 *
	 * @param $query_hash
	 * @param bool $value
	 *
	 * @return bool|false|mixed
	 */
	function pencipdc_cache( $query_hash, $value = false ) {
		if ( ! $value ) {
			return wp_cache_get( $query_hash, 'penci-podcast' );
		}
		wp_cache_set( $query_hash, $value, 'penci-podcast' );

		return $value;
	}
}
if ( ! function_exists( 'pencipdc_get_term_translate_id' ) ) {
	/**
	 * @param int $term_id Term ID .
	 *
	 * @return mixed
	 */
	function pencipdc_get_term_translate_id( $term_id ) {
		if ( function_exists( 'pll_get_term' ) ) {
			$result_id = pll_get_term( $term_id, pll_current_language() );

			if ( $result_id ) {
				$term_id = $result_id;
			}
		}

		return $term_id;
	}
}
if ( ! function_exists( 'pencipdc_get_podcast_by_category' ) ) {
	function pencipdc_get_podcast_by_category( $id, $tax = 'podcast-series' ) {

		if ( is_array( $id ) ) {
			return false;
		}

		$query_hash = 'query_hash_podcast_by_category_' . md5( serialize( $id ) );
		if ( ! $result = pencipdc_cache( $query_hash ) ) {
			global $wpdb;
			$post_with_category   = "SELECT DISTINCT(p.ID) FROM {$wpdb->prefix}posts AS p
									LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON(p.ID = tr.object_id)
									WHERE tr.term_taxonomy_id IN({$id})
									OR tr.term_taxonomy_id IN(
										SELECT DISTINCT(tt.term_taxonomy_id) FROM {$wpdb->prefix}term_taxonomy AS tt
										WHERE tt.parent IN({$id})
									)
									AND p.post_type = 'podcast'
									AND(p.post_status = 'publish')";
			$category_with_series = "SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = '{$tax}'";
			$query                = "SELECT DISTINCT(term_taxonomy_id) FROM {$wpdb->prefix}term_relationships WHERE object_id IN({$post_with_category}) AND term_taxonomy_id IN({$category_with_series})";
			$query                = $wpdb->get_results( $query );

			$result_ids = array();

			if ( ! empty( $query ) ) {
				foreach ( $query as $result ) {
					$result_ids[] = pencipdc_get_term_translate_id( $result->term_taxonomy_id );
				}
			}

			$result = pencipdc_cache( $query_hash, $result_ids );
		}

		return $result;
	}
}
if ( ! function_exists( 'pencipdc_get_podcast_author' ) ) {
	function pencipdc_get_podcast_author( $podcast_id ) {
		$episodes = pencipdc_podcast_posts( $podcast_id );
		$is_empty = empty( $episodes ) ? true : false;
		$user     = false;
		if ( ! $is_empty ) {
			$authors = array();
			foreach ( $episodes as $episode_obj ) {
				$authors[] = $episode_obj->post_author;
			}
			if ( ! empty( $authors ) ) {
				$values = array_count_values( $authors );
				arsort( $values );
				$most_users = array_slice( array_keys( $values ), 0, 1, true );
				foreach ( $most_users as $index => $users ) {
					$user = $users;
				}
			}
		}

		return $user;
	}
}

if ( ! function_exists( 'pencipdc_podcast_posts' ) ) {
	/**
	 * @param $podcast_id
	 * @param array $args
	 *
	 * @return array|false|int[]|mixed|WP_Post[]
	 */
	function pencipdc_podcast_posts( $podcast_id, $args = array() ) {
		$defaults   = array(
			'orderby'   => 'rand',
			'post_type' => 'podcast',
			'tax_query' => array(
				array(
					'taxonomy'         => 'podcast-series',
					'field'            => 'term_id',
					'terms'            => $podcast_id, // Where term_id of selected $podcast_id.
					'include_children' => false,
				),
			),
		);
		$_args      = wp_parse_args( $args, $defaults );
		$query_hash = 'query_hash_' . md5( serialize( $_args ) );
		if ( ! $episodes = pencipdc_cache( $query_hash ) ) {
			$episodes = pencipdc_cache(
				$query_hash,
				call_user_func(
					static function () use ( $_args ) {
						return get_posts( $_args );
					}
				)
			);
		}
		if ( ! is_wp_error( $episodes ) ) {
			return $episodes;
		}

		return array();
	}
}