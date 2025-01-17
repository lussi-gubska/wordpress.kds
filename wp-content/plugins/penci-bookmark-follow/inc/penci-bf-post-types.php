<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Type Functions
 *
 * Handles all custom post types
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

/**
 * Setup Follow Post PostTypes
 *
 * Registers the follow post posttypes
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

function penci_bl_register_post_types() {

	//follow post - post type
	$follow_post_labels = array(
		'name'               => esc_html__( 'Follow Post', 'penci-bookmark-follow' ),
		'singular_name'      => esc_html__( 'Follow Post', 'penci-bookmark-follow' ),
		'add_new'            => esc_html__( 'Add New', 'penci-bookmark-follow' ),
		'add_new_item'       => esc_html__( 'Add New Follow Post', 'penci-bookmark-follow' ),
		'edit_item'          => esc_html__( 'Edit Follow Post', 'penci-bookmark-follow' ),
		'new_item'           => esc_html__( 'New Follow Post', 'penci-bookmark-follow' ),
		'all_items'          => esc_html__( 'All Follow Posts', 'penci-bookmark-follow' ),
		'view_item'          => esc_html__( 'View Follow Post', 'penci-bookmark-follow' ),
		'search_items'       => esc_html__( 'Search Follow Post', 'penci-bookmark-follow' ),
		'not_found'          => esc_html__( 'No follow posts found', 'penci-bookmark-follow' ),
		'not_found_in_trash' => esc_html__( 'No follow posts found in Trash', 'penci-bookmark-follow' ),
		'parent_item_colon'  => '',
		'menu_name'          => esc_html__( 'Follow Posts', 'penci-bookmark-follow' ),
	);
	$follow_post_args   = array(
		'labels'          => $follow_post_labels,
		'public'          => false,
		'query_var'       => false,
		'rewrite'         => false,
		'capability_type' => PENCI_BL_POST_TYPE,
		'hierarchical'    => false,
		'supports'        => array( 'title' ),
	);

	//register follow posts post type
	register_post_type( PENCI_BL_POST_TYPE, $follow_post_args );

	//follow post logs - post type
	$follow_post_logs_labels = array(
		'name'               => esc_html__( 'Follow Post Logs', 'penci-bookmark-follow' ),
		'singular_name'      => esc_html__( 'Follow Post Log', 'penci-bookmark-follow' ),
		'add_new'            => esc_html__( 'Add New', 'penci-bookmark-follow' ),
		'add_new_item'       => esc_html__( 'Add New Follow Post Log', 'penci-bookmark-follow' ),
		'edit_item'          => esc_html__( 'Edit Follow Post Log', 'penci-bookmark-follow' ),
		'new_item'           => esc_html__( 'New Follow Post Log', 'penci-bookmark-follow' ),
		'all_items'          => esc_html__( 'All Follow Post Logs', 'penci-bookmark-follow' ),
		'view_item'          => esc_html__( 'View Follow Post Log', 'penci-bookmark-follow' ),
		'search_items'       => esc_html__( 'Search Follow Post Log', 'penci-bookmark-follow' ),
		'not_found'          => esc_html__( 'No follow post logs found', 'penci-bookmark-follow' ),
		'not_found_in_trash' => esc_html__( 'No follow post logs found in Trash', 'penci-bookmark-follow' ),
		'parent_item_colon'  => '',
		'menu_name'          => esc_html__( 'Follow Post Logs', 'penci-bookmark-follow' ),
	);
	$follow_post_logs_args   = array(
		'labels'          => $follow_post_logs_labels,
		'public'          => false,
		'query_var'       => false,
		'rewrite'         => false,
		'capability_type' => PENCI_BL_LOGS_POST_TYPE,
		'hierarchical'    => false,
		'supports'        => array( 'title' )
	);

	//register follow posts logs post type
	register_post_type( PENCI_BL_LOGS_POST_TYPE, $follow_post_logs_args );

	//follow author - post type
	$follow_author_labels = array(
		'name'               => esc_html__( 'Follow Author', 'penci-bookmark-follow' ),
		'singular_name'      => esc_html__( 'Follow Author', 'penci-bookmark-follow' ),
		'add_new'            => esc_html__( 'Add New', 'penci-bookmark-follow' ),
		'add_new_item'       => esc_html__( 'Add New Follow Author', 'penci-bookmark-follow' ),
		'edit_item'          => esc_html__( 'Edit Follow Author', 'penci-bookmark-follow' ),
		'new_item'           => esc_html__( 'New Follow Author', 'penci-bookmark-follow' ),
		'all_items'          => esc_html__( 'All Follow Authors', 'penci-bookmark-follow' ),
		'view_item'          => esc_html__( 'View Follow Author', 'penci-bookmark-follow' ),
		'search_items'       => esc_html__( 'Search Follow Author', 'penci-bookmark-follow' ),
		'not_found'          => esc_html__( 'No follow authors found', 'penci-bookmark-follow' ),
		'not_found_in_trash' => esc_html__( 'No follow authors found in Trash', 'penci-bookmark-follow' ),
		'parent_item_colon'  => '',
		'menu_name'          => esc_html__( 'Follow Authors', 'penci-bookmark-follow' ),
	);
	$follow_author_args   = array(
		'labels'          => $follow_author_labels,
		'public'          => false,
		'query_var'       => false,
		'rewrite'         => false,
		'capability_type' => PENCI_BL_AUTHOR_POST_TYPE,
		'hierarchical'    => false,
		'supports'        => array( 'title' )
	);

	//register follow author post type
	register_post_type( PENCI_BL_AUTHOR_POST_TYPE, $follow_author_args );

	//follow Author logs - post type
	$follow_author_logs_labels = array(
		'name'               => esc_html__( 'Follow Author Logs', 'penci-bookmark-follow' ),
		'singular_name'      => esc_html__( 'Follow Author Log', 'penci-bookmark-follow' ),
		'add_new'            => esc_html__( 'Add New', 'penci-bookmark-follow' ),
		'add_new_item'       => esc_html__( 'Add New Follow Author Log', 'penci-bookmark-follow' ),
		'edit_item'          => esc_html__( 'Edit Follow Author Log', 'penci-bookmark-follow' ),
		'new_item'           => esc_html__( 'New Follow Author Log', 'penci-bookmark-follow' ),
		'all_items'          => esc_html__( 'All Follow Author Logs', 'penci-bookmark-follow' ),
		'view_item'          => esc_html__( 'View Follow Author Log', 'penci-bookmark-follow' ),
		'search_items'       => esc_html__( 'Search Follow Author Log', 'penci-bookmark-follow' ),
		'not_found'          => esc_html__( 'No follow author logs found', 'penci-bookmark-follow' ),
		'not_found_in_trash' => esc_html__( 'No follow author logs found in Trash', 'penci-bookmark-follow' ),
		'parent_item_colon'  => '',
		'menu_name'          => esc_html__( 'Follow Author Logs', 'penci-bookmark-follow' ),
	);
	$follow_author_logs_args   = array(
		'labels'          => $follow_author_logs_labels,
		'public'          => false,
		'query_var'       => false,
		'rewrite'         => false,
		'capability_type' => PENCI_BL_AUTHOR_LOGS_POST_TYPE,
		'hierarchical'    => false,
		'supports'        => array( 'title' )
	);

	//register follow authors logs post type
	register_post_type( PENCI_BL_AUTHOR_LOGS_POST_TYPE, $follow_author_logs_args );

}

//register custom post type

add_action( 'init', 'penci_bl_register_post_types', 100 ); // we need to keep priority 100, because we need to execute this init action after all other init action called.
