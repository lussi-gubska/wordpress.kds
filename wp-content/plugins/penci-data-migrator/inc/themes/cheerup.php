<?php
$mess =  array();

// Layout style

$post_layout = get_post_meta( $post_id, '_bunyad_layout_style', true );
$post_layout_pre = 'full' == $post_layout ? 'no' : 'right';

$single_sidebar_pos =  update_post_meta( $post_id, 'penci_post_sidebar_display', $post_layout_pre );
if( $single_sidebar_pos ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'post_layout' );
}

// Post format
$featured_video =  get_post_meta( $post_id, '_bunyad_featured_video', true );

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

// Primary Category
$cat_label = get_post_meta( $post_id, '_bunyad_cat_label', true );

if ( $cat_label ) {
	$meta_prefix_seo = class_exists( 'WPSEO_Meta' ) ? WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
	$primary_term    = update_post_meta( $post_id, $meta_prefix_seo . 'primary_category', true );
	if( $primary_term ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'primary_term' );
	}
}


$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id,'success', timer_stop() );


return ( $mess ? implode( '<br>', $mess ) : '' );
