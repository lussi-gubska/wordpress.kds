<?php
$options        = [];
$options[]      = array(
	'default'  => false,
	'sanitize' => 'penci_review_sanitize_checkbox_field',
	'label'    => 'Hide "Average Score" text',
	'id'       => 'penci_review_hide_average',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$list_checkbox1 = array(
	'penci_rv_enable_star_review' => esc_html__( 'Enable Stars Review Style', 'penci' ),
	'penci_rv_hide_featured_img'  => esc_html__( 'Hide Featured Image', 'penci' ),
	'penci_review_hide_address'   => esc_html__( 'Hide Adress', 'penci' ),
	'penci_review_hide_phone'     => esc_html__( 'Hide Phone', 'penci' ),
	'penci_review_hide_website'   => esc_html__( 'Hide Website', 'penci' ),
	'penci_review_hide_price'     => esc_html__( 'Hide Product Price', 'penci' ),
	'penci_review_hide_btnbuy'    => esc_html__( 'Hide Button Buy Now', 'penci' ),
	'penci_review_hide_schema'    => esc_html__( 'Hide Reviewed Schema Info', 'penci' ),
);
foreach ( $list_checkbox1 as $id1 => $title1 ) {
	$options[] = array(
		'default'  => '',
		'sanitize' => 'penci_review_sanitize_checkbox_field',
		'label'    => $title1,
		'id'       => $id1,
		'type'     => 'soledad-fw-toggle',
		'priority' => 2
	);
}
$options[] = array(
	'default'  => '#dedede',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Box Border Color',
	'id'       => 'penci_review_border_color',
	'priority' => 2
);
$options[] = array(
	'default'  => '#313131',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Title Color',
	'id'       => 'penci_review_title_color',
	'priority' => 3
);
$options[] = array(
	'default'  => '#313131',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Description Text Color',
	'id'       => 'penci_review_desc_color',
	'priority' => 3
);
$options[] = array(
	'default'  => '#313131',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Point Title & Score Color',
	'id'       => 'penci_review_point_title_color',
	'priority' => 4
);
$options[] = array(
	'default'  => '#e6e6e6',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Process Main Background Color',
	'id'       => 'penci_review_process_main_color',
	'priority' => 4
);
$options[] = array(
	'default'  => '#6eb48c',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Process Running Background Color',
	'id'       => 'penci_review_process_run_color',
	'priority' => 4
);
$options[] = array(
	'default'  => '#313131',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'The Goods & The Bads Title Color',
	'id'       => 'penci_review_title_good_color',
	'priority' => 5
);
$options[] = array(
	'default'  => '#22b162',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review The Goods Icon Color',
	'id'       => 'penci_review_good_icon',
	'priority' => 5
);
$options[] = array(
	'default'  => '#e03030',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review The Bads Icon Color',
	'id'       => 'penci_review_bad_icon',
	'priority' => 5
);
$options[] = array(
	'default'  => '#6eb48c',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Average Total Background',
	'id'       => 'penci_review_average_total_bg',
	'priority' => 6
);
$options[] = array(
	'default'  => '#ffffff',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Average Total Score Color',
	'id'       => 'penci_review_average_total_color',
	'priority' => 6
);
$options[] = array(
	'default'  => '#ffffff',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review "Average Score" Text Color',
	'id'       => 'penci_review_average_text_color',
	'priority' => 6
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_review_sanitize_checkbox_field',
	'label'    => 'Hide Review Pie Chart on Featured Image',
	'id'       => 'penci_review_hide_piechart',
	'type'     => 'soledad-fw-toggle',
	'priority' => 6
);
$options[] = array(
	'default'  => '#6eb48c',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Pie Chart Border Color',
	'id'       => 'penci_review_piechart_border',
	'priority' => 6
);
$options[] = array(
	'default'  => '#ffffff',
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Review Pie Chart Score Text Color',
	'id'       => 'penci_review_piechart_text',
	'priority' => 6
);
$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Stars Rating Background Color',
	'id'       => 'penci_review_star_bgcl',
	'priority' => 6
);
$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Stars Rating Color',
	'id'       => 'penci_review_star_cl',
	'priority' => 6
);
$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Stars Rating Background Color on Review Box',
	'id'       => 'penci_review_star_bgclb',
	'priority' => 6
);
$options[] = array(
	'sanitize' => 'sanitize_hex_color',
	'type'     => 'soledad-fw-color',
	'label'    => 'Stars Rating Color on Review Box',
	'id'       => 'penci_review_star_clb',
	'priority' => 6
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "The Goods" text',
	'id'       => 'penci_review_good_text',
	'type'     => 'soledad-fw-text',
	'priority' => 7
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "The Bads" text',
	'id'       => 'penci_review_bad_text',
	'type'     => 'soledad-fw-text',
	'priority' => 8
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Average Score" text',
	'id'       => 'penci_review_average_text',
	'type'     => 'soledad-fw-text',
	'priority' => 9
);
$trans     = array(
	'penci_review_price_text'         => esc_html__( 'Custom "Price" text', 'penci' ),
	'penci_review_text_thing'         => esc_html__( 'Custom "Thing" text', 'penci' ),
	'penci_review_text_none'          => esc_html__( 'Custom "None" text', 'penci' ),
	'penci_review_text_book'          => esc_html__( 'Custom "Book" text', 'penci' ),
	'penci_review_text_game'          => esc_html__( 'Custom "Game" text', 'penci' ),
	'penci_review_text_movie'         => esc_html__( 'Custom "Movie" text', 'penci' ),
	'penci_review_text_musicreco'     => esc_html__( 'Custom "MusicRecording" text', 'penci' ),
	'penci_review_text_painting'      => esc_html__( 'Custom "Painting" text', 'penci' ),
	'penci_review_text_place'         => esc_html__( 'Custom "Place" text', 'penci' ),
	'penci_review_text_product'       => esc_html__( 'Custom "Product" text', 'penci' ),
	'penci_review_text_restaurant'    => esc_html__( 'Custom "Restaurant" text', 'penci' ),
	'penci_review_text_sfapp'         => esc_html__( 'Custom "SoftwareApplication" text', 'penci' ),
	'penci_review_text_store'         => esc_html__( 'Custom "Store" text', 'penci' ),
	'penci_review_text_tvseries'      => esc_html__( 'Custom "TVSeries" text', 'penci' ),
	'penci_review_text_webSite'       => esc_html__( 'Custom "WebSite" text', 'penci' ),
	// Booking
	'penci_reviewt_btitle'            => esc_html__( 'Custom "Book Title" text', 'penci' ),
	'penci_reviewt_burl'              => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_bauthor'           => esc_html__( 'Custom "Book Author" text', 'penci' ),
	'penci_reviewt_bedition'          => esc_html__( 'Custom "Book Edition" text', 'penci' ),
	'penci_reviewt_bformat'           => esc_html__( 'Custom "Book Format" text', 'penci' ),
	'penci_reviewt_bdate'             => esc_html__( 'Custom "Date published" text', 'penci' ),
	'penci_reviewt_billustrator'      => esc_html__( 'Custom "Illustrator" text', 'penci' ),
	'penci_reviewt_bISBN'             => esc_html__( 'Custom "ISBN" text', 'penci' ),
	'penci_reviewt_bnumberofpage'     => esc_html__( 'Custom "Number Of Pages" text', 'penci' ),
	'penci_reviewt_bdesc'             => esc_html__( 'Custom "Book Description" text', 'penci' ),
	// Game
	'penci_reviewt_game_title'        => esc_html__( 'Custom "Game title" text', 'penci' ),
	'penci_reviewt_game_url'          => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_game_desc'         => esc_html__( 'Custom "Game description" text', 'penci' ),
	// Movie
	'penci_reviewt_mv_title'          => esc_html__( 'Custom "Movie title" text', 'penci' ),
	'penci_reviewt_mv_url'            => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_mv_date'           => esc_html__( 'Custom "Date published" text', 'penci' ),
	'penci_reviewt_mv_desc'           => esc_html__( 'Custom "Movie description" text', 'penci' ),
	'penci_reviewt_mv_dir'            => esc_html__( 'Custom "Director(s)" text', 'penci' ),
	'penci_reviewt_mv_actor'          => esc_html__( 'Custom "Actor(s)" text', 'penci' ),
	'penci_reviewt_mv_genre'          => esc_html__( 'Custom "Genre" text', 'penci' ),
	// Music recording
	'penci_reviewt_music_name'        => esc_html__( 'Custom "Track name" text', 'penci' ),
	'penci_reviewt_music_url'         => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_music_author'      => esc_html__( 'Custom "Author" text', 'penci' ),
	'penci_reviewt_music_dur'         => esc_html__( 'Custom "Track Duration" text', 'penci' ),
	'penci_reviewt_music_album'       => esc_html__( 'Custom "Album name" text', 'penci' ),
	'penci_reviewt_music_genre'       => esc_html__( 'Custom "Genre" text', 'penci' ),
	// Painting
	'penci_reviewt_painting_name'     => esc_html__( 'Custom "Name" text', 'penci' ),
	'penci_reviewt_painting_author'   => esc_html__( 'Custom "Author" text', 'penci' ),
	'penci_reviewt_painting_url'      => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_painting_date_pub' => esc_html__( 'Custom "Date published" text', 'penci' ),
	'penci_reviewt_painting_genre'    => esc_html__( 'Custom "Genre" text', 'penci' ),
	// Place
	'penci_reviewt_place_name'        => esc_html__( 'Custom "Place Name" text', 'penci' ),
	'penci_reviewt_place_url'         => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_place_desc'        => esc_html__( 'Custom "Place Description" text', 'penci' ),
	// Product
	'penci_reviewt_prod_name'         => esc_html__( 'Custom "Product Name" text', 'penci' ),
	'penci_reviewt_prod_url'          => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_prod_price'        => esc_html__( 'Custom "Price" text', 'penci' ),
	'penci_reviewt_prod_currency'     => esc_html__( 'Custom "Currency" text', 'penci' ),
	'penci_reviewt_prod_avai'         => esc_html__( 'Custom "Availability" text', 'penci' ),
	'penci_reviewt_prod_band'         => esc_html__( 'Custom "Brand" text', 'penci' ),
	'penci_reviewt_prod_suk'          => esc_html__( 'Custom "SKU" text', 'penci' ),
	'penci_reviewt_prod_desc'         => esc_html__( 'Custom "Product Description', 'penci' ),
	// Restaurant
	'penci_reviewt_restau_name'       => esc_html__( 'Custom "Restaurant Name" text', 'penci' ),
	'penci_reviewt_restau_url'        => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_restau_address'    => esc_html__( 'Custom "Address" text', 'penci' ),
	'penci_reviewt_restau_price'      => esc_html__( 'Custom "Price range" text', 'penci' ),
	'penci_reviewt_restau_telephone'  => esc_html__( 'Custom "Telephone" text', 'penci' ),
	'penci_reviewt_restau_serves'     => esc_html__( 'Custom "Serves cuisine" text', 'penci' ),
	'penci_reviewt_restau_ophours'    => esc_html__( 'Custom "Opening hours" text', 'penci' ),
	'penci_reviewt_restau_desc'       => esc_html__( 'Custom "Restaurant Description" text', 'penci' ),
	// Software application
	'penci_reviewt_app_name'          => esc_html__( 'Custom "Name" text', 'penci' ),
	'penci_reviewt_app_url'           => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_app_price'         => esc_html__( 'Custom "Price" text', 'penci' ),
	'penci_reviewt_app_currency'      => esc_html__( 'Custom "Currency" text', 'penci' ),
	'penci_reviewt_app_opsystem'      => esc_html__( 'Custom "Operating System" text', 'penci' ),
	'penci_reviewt_app_app_cat'       => esc_html__( 'Custom "Application Category" text', 'penci' ),
	'penci_reviewt_app_desc'          => esc_html__( 'Custom "Description" text', 'penci' ),
	// Store
	'penci_reviewt_store_name'        => esc_html__( 'Custom "Store Name" text', 'penci' ),
	'penci_reviewt_store_url'         => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_store_address'     => esc_html__( 'Custom "Address" text', 'penci' ),
	'penci_reviewt_store_price'       => esc_html__( 'Custom "Price range" text', 'penci' ),
	'penci_reviewt_store_telephone'   => esc_html__( 'Custom "Telephone" text', 'penci' ),
	'penci_reviewt_store_desc'        => esc_html__( 'Custom "Store Description" text', 'penci' ),
	// TVSeries
	'penci_reviewt_tv_name'           => esc_html__( 'Custom "Name" text', 'penci' ),
	'penci_reviewt_tv_url'            => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_tv_desc'           => esc_html__( 'Custom "Description" text', 'penci' ),
	// WebSite
	'penci_reviewt_web_name'          => esc_html__( 'Custom "Name" text', 'penci' ),
	'penci_reviewt_web_url'           => esc_html__( 'Custom "URL" text', 'penci' ),
	'penci_reviewt_web_desc'          => esc_html__( 'Custom "Description" text', 'penci' ),
);
foreach ( $trans as $key => $tran ) {
	$options[] = array(
		'default'  => penci_review_tran_default_setting( $key ),
		'sanitize' => 'sanitize_text_field',
		'label'    => $tran,
		'id'       => $key,
		'type'     => 'soledad-fw-text'
	);
}

return $options;
