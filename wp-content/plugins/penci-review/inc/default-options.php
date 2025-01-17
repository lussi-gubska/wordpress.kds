<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'penci_review_tran_default_setting' ) ):
	function penci_review_tran_default_setting( $name ) {
		$defaults = array(
			'penci_review_price_text' => esc_html__( 'Price:', 'penci' ),

			'penci_review_text_thing'         => esc_html__( 'Thing', 'penci' ),
			'penci_review_text_none'          => esc_html__( 'None', 'penci' ),
			'penci_review_text_book'          => esc_html__( 'Book', 'penci' ),
			'penci_review_text_game'          => esc_html__( 'Game', 'penci' ),
			'penci_review_text_movie'         => esc_html__( 'Movie', 'penci' ),
			'penci_review_text_musicreco'     => esc_html__( 'MusicRecording', 'penci' ),
			'penci_review_text_painting'      => esc_html__( 'Painting', 'penci' ),
			'penci_review_text_place'         => esc_html__( 'Place', 'penci' ),
			'penci_review_text_product'       => esc_html__( 'Product', 'penci' ),
			'penci_review_text_restaurant'    => esc_html__( 'Restaurant', 'penci' ),
			'penci_review_text_sfapp'         => esc_html__( 'SoftwareApplication', 'penci' ),
			'penci_review_text_store'         => esc_html__( 'Store', 'penci' ),
			'penci_review_text_tvseries'      => esc_html__( 'TVSeries', 'penci' ),
			'penci_review_text_webSite'       => esc_html__( 'WebSite', 'penci' ),
			'penci_review_text_course'        => esc_html__( 'Course', 'penci' ),
			'penci_review_text_event'         => esc_html__( 'Event', 'penci' ),
			'penci_review_text_howto'         => esc_html__( 'How to', 'penci' ),

			// Booking
			'penci_reviewt_btitle'            => esc_html__( 'Book Title', 'penci' ),
			'penci_reviewt_burl'              => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_bauthor'           => esc_html__( 'Book Author', 'penci' ),
			'penci_reviewt_bedition'          => esc_html__( 'Book Edition', 'penci' ),
			'penci_reviewt_bformat'           => esc_html__( 'Book Format', 'penci' ),
			'penci_reviewt_bdate'             => esc_html__( 'Date published', 'penci' ),
			'penci_reviewt_billustrator'      => esc_html__( 'Illustrator', 'penci' ),
			'penci_reviewt_bISBN'             => esc_html__( 'ISBN', 'penci' ),
			'penci_reviewt_bnumberofpage'     => esc_html__( 'Number Of Pages', 'penci' ),
			'penci_reviewt_bdesc'             => esc_html__( 'Book Description', 'penci' ),

			// Game
			'penci_reviewt_game_title'        => esc_html__( 'Game title', 'penci' ),
			'penci_reviewt_game_url'          => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_game_desc'         => esc_html__( 'Game description', 'penci' ),

			// Movie
			'penci_reviewt_mv_title'          => esc_html__( 'Movie title', 'penci' ),
			'penci_reviewt_mv_url'            => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_mv_date'           => esc_html__( 'Date published', 'penci' ),
			'penci_reviewt_mv_desc'           => esc_html__( 'Movie description', 'penci' ),
			'penci_reviewt_mv_dir'            => esc_html__( 'Director(s)', 'penci' ),
			'penci_reviewt_mv_actor'          => esc_html__( 'Actor(s)', 'penci' ),
			'penci_reviewt_mv_genre'          => esc_html__( 'Genre', 'penci' ),

			// Music recording
			'penci_reviewt_music_name'        => esc_html__( 'Track name', 'penci' ),
			'penci_reviewt_music_url'         => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_music_author'      => esc_html__( 'Author', 'penci' ),
			'penci_reviewt_music_dur'         => esc_html__( 'Track Duration', 'penci' ),
			'penci_reviewt_music_album'       => esc_html__( 'Album name', 'penci' ),
			'penci_reviewt_music_genre'       => esc_html__( 'Genre', 'penci' ),

			// Painting
			'penci_reviewt_painting_name'     => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_painting_author'   => esc_html__( 'Author', 'penci' ),
			'penci_reviewt_painting_url'      => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_painting_date_pub' => esc_html__( 'Date published', 'penci' ),
			'penci_reviewt_painting_genre'    => esc_html__( 'Genre', 'penci' ),

			// Place
			'penci_reviewt_place_name'        => esc_html__( 'Place Name', 'penci' ),
			'penci_reviewt_place_url'         => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_place_desc'        => esc_html__( 'Place Description', 'penci' ),

			// Product
			'penci_reviewt_prod_name'         => esc_html__( 'Product Name', 'penci' ),
			'penci_reviewt_prod_url'          => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_prod_price'        => esc_html__( 'Price', 'penci' ),
			'penci_reviewt_prod_currency'     => esc_html__( 'Currency', 'penci' ),
			'penci_reviewt_prod_avai'         => esc_html__( 'Availability', 'penci' ),
			'penci_reviewt_prod_band'         => esc_html__( 'Brand', 'penci' ),
			'penci_reviewt_prod_suk'          => esc_html__( 'SKU', 'penci' ),
			'penci_reviewt_prod_desc'         => esc_html__( 'Product Description', 'penci' ),
			'penci_reviewt_prod_pricevali'    => esc_html__( 'Price Valid Until', 'penci' ),
			'penci_reviewt_prod_mpn'          => esc_html__( 'Product MPN', 'penci' ),

			// Restaurant
			'penci_reviewt_restau_name'       => esc_html__( 'Restaurant Name', 'penci' ),
			'penci_reviewt_restau_url'        => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_restau_address'    => esc_html__( 'Address', 'penci' ),
			'penci_reviewt_restau_price'      => esc_html__( 'Price range', 'penci' ),
			'penci_reviewt_restau_telephone'  => esc_html__( 'Telephone', 'penci' ),
			'penci_reviewt_restau_serves'     => esc_html__( 'Serves cuisine', 'penci' ),
			'penci_reviewt_restau_ophours'    => esc_html__( 'Opening hours', 'penci' ),
			'penci_reviewt_restau_desc'       => esc_html__( 'Restaurant Description', 'penci' ),

			// Software application
			'penci_reviewt_app_name'          => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_app_url'           => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_app_price'         => esc_html__( 'Price', 'penci' ),
			'penci_reviewt_app_currency'      => esc_html__( 'Currency', 'penci' ),
			'penci_reviewt_app_opsystem'      => esc_html__( 'Operating System', 'penci' ),
			'penci_reviewt_app_app_cat'       => esc_html__( 'Application Category', 'penci' ),
			'penci_reviewt_app_desc'          => esc_html__( 'Description', 'penci' ),

			// Store
			'penci_reviewt_store_name'        => esc_html__( 'Store Name', 'penci' ),
			'penci_reviewt_store_url'         => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_store_address'     => esc_html__( 'Address', 'penci' ),
			'penci_reviewt_store_price'       => esc_html__( 'Price range', 'penci' ),
			'penci_reviewt_store_telephone'   => esc_html__( 'Telephone', 'penci' ),
			'penci_reviewt_store_desc'        => esc_html__( 'Store Description', 'penci' ),

			// TVSeries
			'penci_reviewt_tv_name'           => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_tv_url'            => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_tv_desc'           => esc_html__( 'Description', 'penci' ),

			// WebSite
			'penci_reviewt_web_name'          => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_web_url'           => esc_html__( 'URL', 'penci' ),
			'penci_reviewt_web_desc'          => esc_html__( 'Description', 'penci' ),

			// Course
			'penci_reviewt_coursetitle'       => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_coursedesc'        => esc_html__( 'Description', 'penci' ),

			// Event
			'penci_reviewt_eventtitle'        => esc_html__( 'Name', 'penci' ),
			'penci_reviewt_eventdesc'         => esc_html__( 'Description', 'penci' ),
			'penci_reviewt_eventurl'          => esc_html__( 'Url', 'penci' ),
			'penci_reviewt_eventsdate'        => esc_html__( 'startDate', 'penci' ),
			'penci_reviewt_eventedate'        => esc_html__( 'endDate', 'penci' ),
			'penci_reviewt_eventlname'        => esc_html__( 'Location Name', 'penci' ),
			'penci_reviewt_eventladdress'     => esc_html__( 'Location Address', 'penci' ),
			'penci_reviewt_eventprice'        => esc_html__( 'Price', 'penci' ),
			'penci_reviewt_eventpricec'       => esc_html__( 'Price Currency', 'penci' ),
			'penci_reviewt_eventvalidFrom'    => esc_html__( 'Valid From Date', 'penci' ),

			// How to
		);


		return isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
	}
endif;

/**
 * Get theme settings.
 *
 * @param string $name
 *
 * @return mixed
 */
if ( ! function_exists( 'penci_review_tran_setting' ) ):
	function penci_review_tran_setting( $name ) {
		$value = get_theme_mod( $name, penci_review_tran_default_setting( $name ) );

		return do_shortcode( $value );
	}
endif;