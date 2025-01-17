<?php
/**
 * Single Product Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$info_shop_single = penci_amp_get_image_sizes( 'shop_single' );
$info_shop_single = penci_amp_get_image_sizes( 'shop_single' );

$attachment_ids = $product->get_gallery_image_ids();
if( get_post_thumbnail_id() || ( $attachment_ids && has_post_thumbnail() )  ) {
	echo '<div class="product-thumbnails">';
}

if ( $thumb_id = get_post_thumbnail_id() ) {

	$img = wp_get_attachment_image_src( $thumb_id, 'shop_single' );

	$thumb_src = isset( $img[0] ) ? esc_attr( $img[0] ) : '';
	if( ! empty( $thumb_src ) ) {
		$srcset = wp_get_attachment_image_srcset( $thumb_id );

		echo '<div class="product-thumbnail">';
		printf( '<amp-img src="%s" alt="%s" height="%s" width="%s" %s role="button" tabindex="0" layout="responsive"></amp-img>',
			esc_url( $thumb_src ),
			esc_attr( get_post_field( 'post_excerpt', $thumb_id ) ),
			isset( $img[1] ) ? esc_attr( $img[1] ) : '300',
			isset( $img[2] ) ? esc_attr( $img[2] ) : '300',
			! empty( $srcset ) ? 'srcset="' . esc_attr( $srcset ) . '"' : ''
		);
		echo '</div>';
	}


}

if ( $attachment_ids && has_post_thumbnail() ) {

	echo '<div class="product-gallery clearfix">';
	foreach ( $attachment_ids as $attachment_id ) {
		$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
		$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );

		$img_src = isset( $thumbnail[0] ) ? esc_attr( $thumbnail[0] ) : '';
		if( empty( $img_src ) ) {
			continue;
		}

		echo '<div class="penci-amp-product-gallery__image">';
		printf( '<amp-img src="%s" alt="%s" height="%s" width="%s" %s role="button" tabindex="0" layout="responsive"></amp-img>',
			$img_src,
			get_post_field( 'post_title', $attachment_id ),
			isset( $thumbnail[1] ) ? esc_attr( $thumbnail[1] ) : '150',
			isset( $thumbnail[2] ) ? esc_attr( $thumbnail[2] ) : '150',
			! empty( $full_size_image[0] ) ? 'srcset="' . esc_attr( $full_size_image[0] ) . '"' : ''
		);
		echo '</div>';

	}

	echo '</div>';
}

if( get_post_thumbnail_id() || ( $attachment_ids && has_post_thumbnail() )  ) {
	echo '</div><!-- Product thumbs -->';
}
