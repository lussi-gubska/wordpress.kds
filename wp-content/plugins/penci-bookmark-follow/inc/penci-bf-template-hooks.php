<?php
/**
 * Template Hooks
 *
 * Handles to add all hooks of template
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/********************** Follow Post Hooks **************************/

//add_action to load follow post - 5
add_action( 'penci_bl_follow_post', 'penci_bl_follow_post', 5 );

//add_action to load follow post content - 5
add_action( 'penci_bl_follow_post_content', 'penci_bl_follow_post_content', 5 );

/********************** Follow Author Hooks **************************/

//add_action to load follow author - 5
add_action( 'penci_bl_follow_author', 'penci_bl_follow_author', 5 );
add_action( 'penci_below_author_name', 'penci_bl_follow_author', 5 );

//add_action to load follow author content - 5
add_action( 'penci_bl_follow_author_content', 'penci_bl_follow_author_content', 5 );

/********************** Follow Term Hooks **************************/

//add_action to load follow term - 5
add_action( 'penci_bl_follow_term', 'penci_bl_follow_term', 5 );
add_action( 'penci_archive_follow_button', 'penci_bl_follow_term', 5 );

//add_action to load follow term content - 5
add_action( 'penci_bl_follow_term_content', 'penci_bl_follow_term_content', 5 );

//add_action to load follow term count box - 10
add_action( 'penci_bl_follow_term_count_box', 'penci_bl_follow_term_count_box', 10, 2 );

/********************** Subscription Manage Hooks **************************/

//add_action to load subscribe manage content - 5
add_action( 'penci_bl_subscribe_manage_content', 'penci_bl_subscribe_manage_content', 5 );

//add_action to manage follow posts - 5
add_action( 'penci_bl_manage_follow_posts', 'penci_bl_manage_follow_posts', 5 );
add_action( 'penci_bl_manage_follow_posts_ajax', 'penci_bl_manage_follow_posts', 5 );

//add_action to show follow posts listing table
add_action( 'penci_bl_follow_posts_table', 'penci_bl_follow_posts_listing_content', 5, 2 );

//add_action to manage follow author - 5
add_action( 'penci_bl_manage_follow_authors', 'penci_bl_manage_follow_authors', 5 );

//add_action to show follow authors listing table
add_action( 'penci_bl_follow_authors_table', 'penci_bl_follow_authors_listing_content', 5, 3 );

//add_action to author's followers - 5
add_action( 'penci_bl_author_followers', 'penci_bl_author_followers', 5, 1 );

//add_action to show author's followers listing table
add_action( 'penci_bl_author_followers_table', 'penci_bl_author_followers_listing_content', 5, 2 );

//add_action to manage follow terms - 5
add_action( 'penci_bl_manage_follow_terms', 'penci_bl_manage_follow_terms', 5 );

//add_action to show follow terms listing table
add_action( 'penci_bl_follow_terms_table'	, 'penci_bl_terms_listing_content', 5, 2 );

/********************** Unsubscribe Hooks **************************/

//add_action to load unsubscribe content - 5
add_action( 'penci_bl_unsubscribe_content', 'penci_bl_unsubscribe_content', 5 );

/********************** Email Template Hooks **************************/

//add_action to load html email template content - 10
add_action( 'penci_bl_default_email_template', 'penci_bl_default_email_template', 10, 4 );

add_action( 'penci_bl_send_notification_form', 'penci_bl_send_notification_form_template' );