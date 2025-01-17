<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class  Penci_AMP_Product {

	public function __construct() {

		add_action( 'pre_get_posts', array( $this,'pre_shop' ) );
	}

	public function pre_shop( $amp_query ) {

		$is_penci_amp = is_penci_amp();

		if ( ! $is_penci_amp || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! $amp_query->is_main_query() ) {
			return;
		}

		if ( ! isset($amp_query->queried_object->ID ) ) {
			return;
		}

		$shop_id = wc_get_page_id( 'shop' );
		if ( $amp_query->queried_object->ID !== $shop_id ) {
			return;
		}


		$posts_per_page = 6;
		$amp_query->set( 'posts_per_page', absint( $posts_per_page ) );
		$amp_query->set( 'pagename', '' );
		$amp_query->set( 'post_type', 'product' );

		$amp_query->is_post_type_archive = true;
		$amp_query->is_page              = true;
		$amp_query->is_archive           = true;
	}
}

new Penci_AMP_Product;

