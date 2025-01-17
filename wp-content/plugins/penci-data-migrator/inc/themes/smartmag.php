<?php
$mess = array();

// Layout style

$post_layout     = get_post_meta( $post_id, '_bunyad_layout_style', true );
$post_layout_pre = 'full' == $post_layout ? 'no-sidebar-wide' : 'sidebar-right';

$single_sidebar_pos = update_post_meta( $post_id, 'penci_post_sidebar_display', $post_layout_pre );
if ( $single_sidebar_pos ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'post_layout' );
}

// Sub Title
$has_post_title = false;
$subtitle       = get_post_meta( $post_id, '_bunyad_sub_title', true );
if ( ! empty( $subtitle ) ) {
	$has_post_title = update_post_meta( $post_id, 'penci_post_sub_title', $subtitle );
}

if ( $has_post_title ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'sub_title' );
}

// Post format
$featured_video = get_post_meta( $post_id, '_bunyad_featured_video', true );

if ( $featured_video ) {
	$post_format       = get_post_format( $post_id );
	$video_url_updated = '';
	if ( 'audio' == $post_format ) {
		$video_url_updated = update_post_meta( $post_id, '_format_audio_embed', $featured_video );
	} else {
		$video_url_updated = update_post_meta( $post_id, '_format_video_embed', $featured_video );
	}

	if ( $video_url_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'video_url' );
	}
}

// Primary Category
$cat_label = get_post_meta( $post_id, '_bunyad_cat_label', true );

if ( $cat_label ) {
	$meta_prefix_seo = class_exists( 'WPSEO_Meta' ) ? WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
	$primary_term    = update_post_meta( $post_id, $meta_prefix_seo . 'primary_category', true );
	if ( $primary_term ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'primary_term' );
	}
}

// Review
$review_type = get_post_meta( $post_id, '_bunyad_review_type', true );
if ( $review_type ) {

	$review_style = 'style_1';
	if ( 'percent' == $score_type ) {
		$review_style = 'style_2';
	} elseif ( 'stars' == $score_type ) {
		$review_style = 'style_3';
	}

	$penci_review = array(
		'penci_review_style' => $review_style,
		'penci_review_title' => get_post_meta( $post_id, '_bunyad_review_heading', true ),
		'penci_review_des'   => get_post_meta( $post_id, '_bunyad_review_heading', true ),
		'penci_review_1'     => '',
		'penci_review_1_num' => '',
		'penci_review_2'     => '',
		'penci_review_2_num' => '',
		'penci_review_3'     => '',
		'penci_review_3_num' => '',
		'penci_review_4'     => '',
		'penci_review_4_num' => '',
		'penci_review_5'     => '',
		'penci_review_5_num' => '',
		'penci_review_good'  => '',
		'penci_review_bad'   => '',
	);

	for ( $i = 1; $i < 6; $i ++ ) {
		$desc = get_post_meta( $post_id, '_bunyad_criteria_label_' . $i, true );
		$rate = get_post_meta( $post_id, '_bunyad_criteria_rating_' . $i, true );

		if ( $desc || $rate ) {
			$penci_review[ 'penci_review_' . $i ]          = $desc;
			$penci_review[ 'penci_review_' . $i . '_num' ] = $rate;
		}
	}

	foreach ( $penci_review as $penci_review_item => $penci_review_value ) {
		$penci_review_updated = update_post_meta( $post_id, $penci_review_item, $penci_review_value );
	}

	if ( $penci_review_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'review' );
	}
}


$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );


return ( $mess ? implode( '<br>', $mess ) : '' );
