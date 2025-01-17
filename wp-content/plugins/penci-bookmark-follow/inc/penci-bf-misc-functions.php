<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Misc Functions
 *
 * All misc functions handles to
 * different functions
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

/**
 * Get Followers Count for Post / Page / Custom Post Types
 *
 * Handles to get followers count of
 * post / page / custom post type by post id
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_post_followers_count( $post_id ) {

	//check if post id empty then return zero
	if ( empty( $post_id ) ) {
		return 0;
	}

	$prefix = PENCI_BL_META_PREFIX;

	//arguments to collect followers data by post
	$args = array(
		'post_status'    => 'publish',
		'post_parent'    => $post_id,
		'posts_per_page' => '-1',
		'post_type'      => PENCI_BL_POST_TYPE,
		'meta_key'       => $prefix . 'follow_status',
		'meta_value'     => '1'
	);

	//get data for post followed by users
	$data = get_posts( $args );

	//get followers count
	$counts = count( $data );

	//return followers count
	return apply_filters( 'penci_bl_get_post_followers_count', $counts, $post_id );
}

/**
 * Get Taxonomy Terms Followers Count
 *
 * Handles to get followers count of term by term id
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_term_followers_count( $term_id ) {

	//check if term id empty then return zero
	if ( empty( $term_id ) ) {
		return 0;
	}

	$prefix = PENCI_BL_META_PREFIX;

	//arguments to collect followers data by term
	$args = array(
		'post_status'    => 'publish',
		'post_type'      => PENCI_BL_TERM_POST_TYPE,
		'post_parent'    => $term_id,
		'posts_per_page' => '-1',
		'meta_key'       => $prefix . 'follow_status',
		'meta_value'     => '1'
	);

	//get data for term followed by users
	$data = get_posts( $args );

	//get followers count
	$counts = count( $data );

	return apply_filters( 'penci_bl_get_term_followers_count', $counts, $term_id );
}

/**
 * Get Authors Followers Count
 *
 * Handles to get followers count of author by author id
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_author_followers_count( $author_id ) {

	//check if author id empty then return zero
	if ( empty( $author_id ) ) {
		return 0;
	}

	$prefix = PENCI_BL_META_PREFIX;

	//arguments to collect followers data by author
	$args = array(
		'post_status'    => 'publish',
		'post_type'      => PENCI_BL_AUTHOR_POST_TYPE,
		'post_parent'    => $author_id,
		'posts_per_page' => '-1',
		'meta_key'       => $prefix . 'follow_status',
		'meta_value'     => '1'
	);

	//get data for author followed by users
	$data = get_posts( $args );

	//get followers count
	$counts = count( $data );

	return apply_filters( 'penci_bl_get_author_followers_count', $counts, $author_id );
}

/**
 * Get Unsubscribe message
 *
 * Handles to get get unsubscibe message and return
 * unsubscibe message html
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_unsubscribe_message() {

	global $penci_bl_options;

	$unsubscribe_message = ''; // initialize its with blank

	// Check enable unsubscribe url & unsubscribe page is exist & unsubscribe message is not empty
	if ( isset( $penci_bl_options['enable_unsubscribe_url'] ) && $penci_bl_options['enable_unsubscribe_url'] == '1'
	     && isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] )
	     && isset( $penci_bl_options['unsubscribe_message'] ) && ! empty( $penci_bl_options['unsubscribe_message'] ) ) {

		// get unsubscibe message
		$unsubscribe_message = $penci_bl_options['unsubscribe_message'];

		// get url of unsubscribe page
		$url = get_permalink( $penci_bl_options['unsubscribe_page'] );

		// make unsubscibe url
		$unsubscribe_url = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Unsubscribe', 'penci-bookmark-follow' ) . '</a>';

		// replace {unsubscribe url} with unsubscibe message
		$unsubscribe_message = str_replace( '{unsubscribe_url}', $unsubscribe_url, $unsubscribe_message );
	}

	return apply_filters( 'penci_bl_get_unsubscribe_message', $unsubscribe_message );
}


/**
 * Get Unsubscribe link message directly
 *
 * Handles to get unsubscibe message with link and return
 * unsubscibe message link html
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_unsubscribe_link_message( $follow_user_email, $unsubscribedata = array() ) {

	global $penci_bl_options, $post;

	$unsubscribe_message = ''; // initialize its with blank

	// get unsubscibe message
	$unsubscribe_message       = $penci_bl_options['unsubscribe_message'];
	$is_individual_unsubscribe = ! empty( $penci_bl_options['is_individual_unsubscribe'] ) ? $penci_bl_options['is_individual_unsubscribe'] : 0; // Get option whether to send unsubcscribe mail for single post, term, author or multiple

	// get url of unsubscribe page
	$unsubscribe_page_id = isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] ) ? $penci_bl_options['unsubscribe_page'] : $post->ID;

	$url = get_permalink( $unsubscribe_page_id );
	$url = add_query_arg( array(
		'penci_bl_action' => base64_encode( 'unsubscribe' ),
		'penci_bl_email'  => base64_encode( rawurlencode( $follow_user_email ) )
	), $url );

	// add query param to unsubscribe url for get what to unsubscibe and for what id
	if ( ! empty( $unsubscribedata ) && ! empty( $is_individual_unsubscribe ) && $is_individual_unsubscribe == 1 ) {
		$url = add_query_arg( array(
			'type' => base64_encode( $unsubscribedata['type'] ),
			'id'   => $unsubscribedata['id']
		), $url );
	}

	// make unsubscibe url
	$unsubscribe_url = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Unsubscribe', 'penci-bookmark-follow' ) . '</a>';

	// replace {unsubscribe url} with unsubscibe message
	$unsubscribe_message = str_replace( '{unsubscribe_url}', $unsubscribe_url, $unsubscribe_message );

	return apply_filters( 'penci_bl_get_unsubscribe_message', $unsubscribe_message );
}

/**
 * unsubscribe user from specific post, term or author
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_unsubscribe_user( $unsub_type, $unsub_id ) {
	global $penci_bl_options, $post;
	$prefix = PENCI_BL_META_PREFIX;

	// check from what to unsubscribe
	switch ( $unsub_type ) {
		case 'post':
			update_post_meta( $unsub_id, $prefix . 'follow_status', '0' );
			break;
		case 'term':
			update_post_meta( $unsub_id, $prefix . 'follow_status', '0' );
			break;
		case 'author':
			update_post_meta( $unsub_id, $prefix . 'follow_status', '0' );
			break;
		default:
			break;
	}

	$unsubscribe_page_id = isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] ) ? $penci_bl_options['unsubscribe_page'] : $post->ID;
	$url                 = get_permalink( $unsubscribe_page_id );
	wp_redirect( $url );
	exit;
}