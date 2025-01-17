<?php

if ( ! class_exists( 'Penci_Soledad_Demo_Importer_Helper' ) ):
	class Penci_Soledad_Demo_Importer_Helper {

	}
endif;

if ( ! function_exists( 'penci_get_page_by_title' ) ) {
	function penci_get_page_by_title( $pages ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'page',
				'title'                  => esc_attr( $pages ),
				'post_status'            => 'all',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'post_date ID',
				'order'                  => 'ASC',
			)
		);

		if ( ! empty( $query->post ) ) {
			$page_got_by_title = $query->post;
		} else {
			$page_got_by_title = null;
		}

		return $page_got_by_title;
	}
}