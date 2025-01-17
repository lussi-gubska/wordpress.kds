<?php
$mess =  array();

// Count views
$views_all = (int) get_post_meta( $post_id, 'better-views-count', true );
if( ! empty( $views_all ) ){
	$views_all_updated = update_post_meta( $post_id, 'penci_post_views_count',$views_all  );

	if( $views_all_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'count_views' );
	}
}

// Primary Category

$primary_category = get_post_meta( $post_id, '_bs_primary_category', true );
$mess = Penci_Soledad_MG_Helper::update_category_primary( $post_id, $primary_category, $mess );

// Post format
$featured_video =  get_post_meta( $post_id, '_featured_embed_code', true );

if( $featured_video ) {
	$post_format =  get_post_format( $post_id );
	$video_url_updated = '';
	if( 'audio' == $post_format ){
		$video_url_updated = update_post_meta( $post_id, '_format_audio_embed', $featured_video );
	}else {
		$video_url_updated = update_post_meta( $post_id, '_format_video_embed', $featured_video );
	}

	if( $video_url_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'video_url' );
	}
}

// Review
$review_rating_type = get_post_meta( $post_id, '_bs_review_rating_type', true );
$review_criteria    = get_post_meta( $post_id, '_bs_review_criteria', true );
$review_pros  = get_post_meta( $post_id, '_pros', true );
$review_cons  = get_post_meta( $post_id, '_cons', true );

$desc = get_post_meta( $post_id, '_bs_review_verdict_summary', true );
$desc .= get_post_meta( $post_id, '_bs_review_extra_desc', true );

$penci_review = array(
	'penci_review_style' => 'style_1',
	'penci_review_title' => get_post_meta( $post_id, '_bs_review_heading', true ),
	'penci_review_des'   => $desc,
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

if ( 'points' == $review_rating_type ) {
	$penci_review['penci_review_style'] = 'style_1';
} elseif ( 'percentage' == $review_rating_type ) {
	$penci_review['penci_review_style'] = 'style_2';

} elseif ( 'stars' == $review_rating_type ) {
	$penci_review['penci_review_style'] = 'style_3';
}

if ( ! empty( $review_criteria ) ) {
	$points_count = 1;
	foreach ($review_criteria as $section) {
		$desc = isset( $section['label'] ) ? $section['label'] : '';
		$rate = isset( $section['rate'] ) ? $section['rate'] : '';

		if ( $desc || $rate ) {
			$penci_review[ 'penci_review_' . $points_count ]          = $desc;
			$penci_review[ 'penci_review_' . $points_count . '_num' ] = $rate;
		}

		$points_count ++;
	}
}

if ( ! empty( $review_pros ) ) {

	$review_good = '';
	foreach ( $review_pros as $review_pro ) {
		$review_good .= $review_pro['label'] . "\n";
	}
	$penci_review['penci_review_good'] = $review_good;
}
if ( ! empty( $review_cons ) ) {

	$review_bad = '';
	foreach ( $review_cons as $review_con ) {
		$review_good .= $review_con['label'] . "\n";
	}
	$penci_review['penci_review_bad'] = $review_bad;
}

foreach ( $penci_review as $penci_review_item => $penci_review_value ) {
	$penci_review_updated = update_post_meta( $post_id, $penci_review_item, $penci_review_value );
}

if( $penci_review_updated ){
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'review' );
}


$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'success', timer_stop() );

return implode( '<br>', $mess );
