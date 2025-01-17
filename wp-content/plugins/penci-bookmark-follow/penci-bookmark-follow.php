<?php
/*
Plugin Name: Penci Bookmark & Follow
Plugin URI: https://pencidesign.net/
Description: Penci Bookmark & Follow plugin allows your visitors to follow changes on your site for particular post, page, authors etc.
Version: 1.7
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-bookmark-follow
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Basic plugin definitions
 *
 * @package Penci Bookmark Follow
 * @since 1.1
 */
global $wpdb;
define( 'PENCI_BL_VERSION', '1.7' ); // version of plugin
define( 'PENCI_BL_DIR', __DIR__ ); // plugin dir
define( 'PENCI_BL_URL', plugin_dir_url( __FILE__ ) ); // plugin url
define( 'PENCI_BL_ADMIN_DIR', PENCI_BL_DIR . '/inc/admin' ); // plugin admin dir
define( 'PENCI_BL_IMG_URL', PENCI_BL_URL . 'inc/images' ); // plugin images url
define( 'PENCI_BL_POST_TYPE', 'pencibfpost' ); // follow post custom post type's slug
define( 'PENCI_BL_LOGS_POST_TYPE', 'pencibfpostlogs' ); // follow post logs custom post type's slug
define( 'PENCI_BL_TERM_POST_TYPE', 'pencibfterm' ); // follow term custom post type's slug
define( 'PENCI_BL_TERM_LOGS_POST_TYPE', 'pencibftermlogs' ); // follow term custom post type's slug
define( 'PENCI_BL_AUTHOR_POST_TYPE', 'pencibfauthor' ); // follow author custom post type's slug
define( 'PENCI_BL_AUTHOR_LOGS_POST_TYPE', 'pencibfauthorlogs' ); // follow author custom post type's slug
define( 'PENCI_BL_BASENAME', basename( PENCI_BL_DIR ) ); // base name
define( 'PENCI_BL_META_PREFIX', '_penci_bl_' );

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'penci_bl_install' );

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'penci_bl_uninstall' );

/**
 * Plugin Setup (On Activation)
 *
 * Does the initial setup,
 * stest default values for the plugin options.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_install() {

	global $wpdb, $user_ID, $penci_bl_options;
	// register post type
	penci_bl_register_post_types();

	// IMP Call of Function
	// Need to call when custom post type is being used in plugin
	flush_rewrite_rules();

	// get all options of settings
	$penci_bl_set_pages = get_option( 'penci_bl_set_pages' );

	// check if option is empty
	if ( empty( $penci_bl_set_pages ) ) {

		$subscribe_manage_page = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_title'     => esc_html__( 'Bookmark Subscription Management', 'penci-bookmark-follow' ),
			'post_content'   => '[pencibf_follow_post_list]' . "\n\r" . '[pencibf_follow_term_list]' . "\n\r" . '[pencibf_follow_author_list]',
			'post_author'    => $user_ID,
			'menu_order'     => 0,
			'comment_status' => 'closed',
		);

		// create subscribe manage page
		$subscribe_manage_page_id = wp_insert_post( $subscribe_manage_page );

		$unsubscribe_page = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_parent'    => $subscribe_manage_page_id,
			'post_title'     => esc_html__( 'Unsubscribe Email Bookmark Notifications', 'penci-bookmark-follow' ),
			'post_content'   => '[pencibf_unsubscribe]',
			'post_author'    => $user_ID,
			'menu_order'     => 0,
			'comment_status' => 'closed',
		);

		// create unsubscribe page
		$unsubscribe_page_id = wp_insert_post( $unsubscribe_page );

		// this option contains all page ID(s) to just pass it to ww_fp_default_settings function
		update_option(
			'penci_bl_set_pages',
			array(
				'subscribe_manage_page' => $subscribe_manage_page_id,
				'unsubscribe_page'      => $unsubscribe_page_id,
			)
		);

	} //check fp options empty or not
}


add_filter( 'display_post_states', 'penci_bf_add_post_state', 10, 2 );
function penci_bf_add_post_state( $post_states, $post ) {

	$pages = get_option( 'penci_bl_set_pages' );

	if ( isset( $pages['subscribe_manage_page'] ) && $pages['subscribe_manage_page'] && $post->ID == $pages['subscribe_manage_page'] ) {
		$post_states[] = 'Bookmark Page';
	}

	if ( isset( $pages['unsubscribe_page'] ) && $pages['unsubscribe_page'] && $post->ID == $pages['unsubscribe_page'] ) {
		$post_states[] = 'Unsubscribe from Email Bookmark Notifications';
	}

	return $post_states;
}

add_action(
	'init',
	function () {
		$pages         = get_option( 'penci_bl_set_pages' );
		$spage         = get_theme_mod( 'pencibf_custom_subscribe_page' );
		$upage         = get_theme_mod( 'pencibf_custom_unsubscribe_page' );
		$should_update = false;

		$subscribe_manage_page = isset( $pages['subscribe_manage_page'] ) && $pages['subscribe_manage_page'] ? $pages['subscribe_manage_page'] : '';
		$unsubscribe_page      = isset( $pages['unsubscribe_page'] ) && $pages['unsubscribe_page'] ? $pages['unsubscribe_page'] : '';

		if ( $spage && $subscribe_manage_page != $spage ) {
			$subscribe_manage_page = $spage;
			$should_update         = true;
		}
		if ( $upage && $unsubscribe_page != $upage ) {
			$unsubscribe_page = $upage;
			$should_update    = true;
		}
		if ( $should_update ) {
			update_option(
				'penci_bl_set_pages',
				array(
					'subscribe_manage_page' => $subscribe_manage_page,
					'unsubscribe_page'      => $unsubscribe_page,
				)
			);
		}
	}
);

/**
 * Plugin Setup (On Deactivation)
 *
 * Delete  plugin options.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_uninstall() {

	global $wpdb;

	// IMP Call of Function
	// Need to call when custom post type is being used in plugin
	flush_rewrite_rules();

	// get all options of settings
	$penci_bl_options = get_option( 'penci_bl_options' );

	if ( isset( $penci_bl_options['del_all_options'] ) && ! empty( $penci_bl_options['del_all_options'] ) && $penci_bl_options['del_all_options'] == '1' ) {

		// get all page ID(s) which are created when plugin is activating first time
		$pages = get_option( 'penci_bl_set_pages' );
		wp_delete_post( $pages['subscribe_manage_page'], true );// delete subscribe manage page
		wp_delete_post( $pages['unsubscribe_page'], true );// delete unsubscribe page

		delete_option( 'penci_bl_options' );
		delete_option( 'penci_bl_set_pages' );
		delete_option( 'penci_bl_set_option' );

		$post_types = array(
			'pencibfpost',
			'pencibfpostlogs',
			'pencibfterm',
			'pencibftermlogs',
			'pencibfauthor',
			'pencibfauthorlogs',
		);

		foreach ( $post_types as $post_type ) {
			$args      = array(
				'post_type'   => $post_type,
				'post_status' => 'any',
				'numberposts' => '-1',
			);
			$all_posts = get_posts( $args );
			foreach ( $all_posts as $post ) {
				wp_delete_post( $post->ID, true );
			}
		}
	}
}

/**
 * Get Settings From Option Page
 *
 * Handles to return all settings value
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_settings() {

	$settings = is_array( get_option( 'penci_bl_options' ) ) ? get_option( 'penci_bl_options' ) : array();

	return $settings;
}

// Add action to read plugin default option to Make it WPML Compatible
add_action( 'plugins_loaded', 'penci_bl_read_default_options', 999 );

/**
 * Re-read all options to make it wpml compatible
 *
 * @package Penci Bookmark Follow
 * @since 1.6.4
 */
function penci_bl_read_default_options() {

	global $penci_bl_options;

	// get values for created pages
	$pages = get_option( 'penci_bl_set_pages' );

	// default for all created pages
	$unsubscribe = $subscribemanage = '';

	// get all post type
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	foreach ( $post_types as $key => $post_type ) {
		if ( $key == 'attachment' ) {
			unset( $post_types[ $key ] );
		}
	}

	// check pages are created or not
	if ( ! empty( $pages ) ) {

		// check if subscribe manage page is created then set to default
		if ( isset( $pages['subscribe_manage_page'] ) ) {
			$subscribemanage = $pages['subscribe_manage_page'];
		}
		if ( isset( $pages['unsubscribe_page'] ) ) {
			$unsubscribe = $pages['unsubscribe_page'];
		}
	}

	$from_email = get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>';

	$penci_bl_options = array(
		'disable_follow_guest'              => get_theme_mod( 'pencibf_disable_follow_guest' ),
		'enable_notify_followers'           => get_theme_mod( 'pencibf_enable_notify_followers' ),
		'disable_auto_follow_add_comment'   => get_theme_mod( 'pencibf_disable_auto_follow_add_comment' ),
		'double_opt_in'                     => get_theme_mod( 'pencibf_double_opt_in' ),
		'double_opt_in_guest'               => false,
		'subscribe_manage_page'             => $subscribemanage,
		'unsubscribe_page'                  => $unsubscribe,
		'follow_buttons'                    => array(
			'follow'    => get_theme_mod( 'pencibf_followtext', esc_html__( 'Bookmark', 'penci-bookmark-follow' ) ),
			'following' => get_theme_mod( 'pencibf_followingtext', esc_html__( 'Bookmarked', 'penci-bookmark-follow' ) ),
			'unfollow'  => get_theme_mod( 'pencibf_unfollowtext', esc_html__( 'Un-Bookmark', 'penci-bookmark-follow' ) ),
		),
		'follow_message'                    => get_theme_mod( 'pencibf_follow_message', '( {followers_count} ' . esc_html__( 'Followers', 'penci-bookmark-follow' ) . ' )' ),
		'prevent_type'                      => get_theme_mod( 'pencibf_prevent_type', $post_types ),
		'term_follow_message'               => get_theme_mod( 'pencibf_tax_follow_message', '( {followers_count} ' . esc_html__( 'Followers', 'penci-bookmark-follow' ) . ' )' ),
		'authors_follow_buttons'            => array(
			'follow'    => get_theme_mod( 'pencibf_author_followtext', esc_html__( 'Follow Author', 'penci-bookmark-follow' ) ),
			'following' => get_theme_mod( 'pencibf_author_followingtext', esc_html__( 'Following Author', 'penci-bookmark-follow' ) ),
			'unfollow'  => get_theme_mod( 'pencibf_author_unfollowtext', esc_html__( 'Unfollow Author', 'penci-bookmark-follow' ) ),
		),
		'authors_follow_message'            => get_theme_mod( 'pencibf_author_follow_message', '( {followers_count} ' . esc_html__( 'Followers', 'penci-bookmark-follow' ) . ' )' ),
		'notification_type'                 => $post_types,
		'recipient_per_email'               => '0',
		'post_trigger_notification'         => array(
			'post_update' => get_theme_mod( 'pencibf_post_trigger_notification_post_update' ),
			'new_comment' => get_theme_mod( 'pencibf_post_trigger_notification_new_comment' ),
		),
		'term_trigger_notification'         => array(
			'new_post'    => get_theme_mod( 'pencibf_term_trigger_notification_post_published' ),
			'post_update' => get_theme_mod( 'pencibf_term_trigger_notification_post_update' ),
			'new_comment' => get_theme_mod( 'pencibf_term_trigger_notification_new_comment' ),
		),
		'author_trigger_notification'       => array(
			'new_post'    => get_theme_mod( 'pencibf_author_trigger_notification_post_published' ),
			'post_update' => get_theme_mod( 'pencibf_author_trigger_notification_post_update' ),
			'new_comment' => get_theme_mod( 'pencibf_author_trigger_notification_new_comment' ),
		),
		'email_template'                    => 'plain',
		'from_email'                        => $from_email,
		'enable_unsubscribe_url'            => '1',
		'unsubscribe_message'               => get_theme_mod( 'pencibf_unsubscribe_message', esc_html__( 'If you want to unsubscribe, click on', 'penci-bookmark-follow' ) . ' {unsubscribe_url}' ),
		'email_subject'                     => get_theme_mod( 'pencibf_email_subject', sprintf( esc_html__( 'Post %1$s updated at %2$s', 'penci-bookmark-follow' ), '{post_name}', '{site_name}' ) ),
		'email_body'                        => get_theme_mod( 'pencibf_email_body', sprintf( esc_html__( 'Post %s updated', 'penci-bookmark-follow' ), '{post_name}' ) . "\n\n" . esc_html__( 'If you want to see page click below link', 'penci-bookmark-follow' ) . "\n\n" . '{post_link} ' . esc_html__( 'for', 'penci-bookmark-follow' ) . ' {site_link}' ),
		'confirm_email_subject'             => get_theme_mod( 'pencibf_confirm_email_subject', esc_html__( 'Follow', 'penci-bookmark-follow' ) . ' {post_name} - {site_name}' ),
		'confirm_email_body'                => get_theme_mod( 'pencibf_confirm_email_body', esc_html__( 'Hello', 'penci-bookmark-follow' ) . "\n\n" . esc_html__( 'You recently followed below blog post. This means you will receive an email when post is updated.', 'penci-bookmark-follow' ) . "\n\n" . esc_html__( 'Blog Post URL', 'penci-bookmark-follow' ) . ': {post_link}' . "\n\n" . esc_html__( 'To activate, click confirm below. If you did not request this, please feel free to disregard this notice!', 'penci-bookmark-follow' ) . "\n\n" . '{subscribe_url}' . "\n\n" . esc_html__( 'Thanks', 'penci-bookmark-follow' ) ),
		'comment_email_subject'             => get_theme_mod( 'pencibf_comment_email_subject', sprintf( esc_html__( 'New comment on %1$s by %2$s', 'penci-bookmark-follow' ), '"{post_name}"', '{user_name}' ) ),
		'comment_email_body'                => get_theme_mod( 'pencibf_comment_email_body', sprintf( esc_html__( 'New comment added on the post %1$s by %2$s, see below :', 'penci-bookmark-follow' ), '"{post_name}"', '{user_name}' ) . "\n\n" . '{comment_text}' ),
		'term_email_subject'                => get_theme_mod( 'pencibf_term_email_subject', esc_html__( '[New Post]', 'penci-bookmark-follow' ) . ' {post_name}' ),
		'term_email_body'                   => get_theme_mod( 'pencibf_term_email_body', esc_html__( 'New post added under the', 'penci-bookmark-follow' ) . ' {taxonomy_name} "{term_name}":' . "\n\n" . '{post_name}' . "\n\n" . '{post_description}' . "\n\n" . esc_html__( 'If you want to see page click below link', 'penci-bookmark-follow' ) . "\n\n" . '{post_link} ' . esc_html__( 'for', 'penci-bookmark-follow' ) . ' {site_link}' ),
		'author_email_subject'              => get_theme_mod( 'pencibf_author_email_subject', esc_html__( '[New Post]', 'penci-bookmark-follow' ) . ' {post_name}' ),
		'author_email_body'                 => get_theme_mod( 'pencibf_author_email_body', esc_html__( 'New post added by the author', 'penci-bookmark-follow' ) . ' "{author_name}":' . "\n\n" . '{post_name}' . "\n\n" . '{post_description}' . "\n\n" . esc_html__( 'If you want to see page click below link', 'penci-bookmark-follow' ) . "\n\n" . '{post_link} ' . esc_html__( 'for', 'penci-bookmark-follow' ) . ' {site_link}' ),
		'term_confirm_email_subject'        => get_theme_mod( 'pencibf_term_confirm_email_subject', esc_html__( 'Follow', 'penci-bookmark-follow' ) . ' {term_name} - {site_name}' ),
		'term_confirm_email_body'           => get_theme_mod( 'pencibf_term_confirm_email_body', esc_html__( 'Hello', 'penci-bookmark-follow' ) . "\n\n" . esc_html__( 'You recently followed the', 'penci-bookmark-follow' ) . ' {taxonomy_name} "{term_name}". ' . esc_html__( 'This means you will receive an email when any new post is published under the', 'penci-bookmark-follow' ) . ' {taxonomy_name} "{term_name}".' . "\n\n" . esc_html__( 'To activate, click confirm below. If you did not request this, please feel free to disregard this notice!', 'penci-bookmark-follow' ) . "\n\n" . '{subscribe_url}' . "\n\n" . esc_html__( 'Thanks', 'penci-bookmark-follow' ) ),
		'author_confirm_email_subject'      => get_theme_mod( 'pencibf_author_confirm_email_subject', esc_html__( 'Follow', 'penci-bookmark-follow' ) . ' {author_name} - {site_name}' ),
		'author_confirm_email_body'         => get_theme_mod( 'pencibf_author_confirm_email_body', esc_html__( 'Hello', 'penci-bookmark-follow' ) . "\n\n" . esc_html__( 'You recently followed the author', 'penci-bookmark-follow' ) . ' "{author_name}". ' . esc_html__( 'This means you will receive an email when any new post is published by the author', 'penci-bookmark-follow' ) . ' "{author_name}".' . "\n\n" . esc_html__( 'To activate, click confirm below. If you did not request this, please feel free to disregard this notice!', 'penci-bookmark-follow' ) . "\n\n" . '{subscribe_url}' . "\n\n" . esc_html__( 'Thanks', 'penci-bookmark-follow' ) ),
		'unsubscribe_confirm_email_subject' => get_theme_mod( 'pencibf_unsubscribe_confirm_email_subject', '[{site_name}] ' . esc_html__( 'Please confirm your unsubscription request', 'penci-bookmark-follow' ) ),
		'unsubscribe_confirm_email_body'    => get_theme_mod( 'pencibf_unsubscribe_confirm_email_body', sprintf( esc_html__( '%s has received a request to unsubscribe for this email address. To complete your request please click on the link below:', 'penci-bookmark-follow' ), '{site_name}' ) . "\n\n" . '{confirm_url}' . "\n\n" . esc_html__( 'If you did not request this, please feel free to disregard this notice!', 'penci-bookmark-follow' ) ),
	);
}

// global variables
global $penci_bl_model, $penci_bl_public, $penci_bl_admin,
		$penci_bl_script, $penci_bl_options,
		$penci_bl_message, $penci_bl_shortcode;


// Misc Functions File
require_once PENCI_BL_DIR . '/inc/penci-bf-misc-functions.php';

$penci_bl_options = penci_bl_get_settings();

require_once PENCI_BL_DIR . '/inc/class-penci-bf-message-stack.php'; // message class, handles the messages after review submission
$penci_bl_message = new Penci_Bf_Message_Stack();

// Script Class to add styles and scripts to admin and public side
require_once PENCI_BL_DIR . '/inc/class-penci-bf-scripts.php';
$penci_bl_script = new Penci_Bf_Scripts();
$penci_bl_script->add_hooks();

// Register Post Types
require_once PENCI_BL_DIR . '/inc/penci-bf-post-types.php';

// Pagination Class
require_once PENCI_BL_DIR . '/inc/class-penci-bf-pagination-public.php'; // front end pagination class
require_once PENCI_BL_DIR . '/inc/class-penci-bf-model.php';
$penci_bl_model = new Penci_Bf_Model();

// Shortcodes class for handling shortcodes
require_once PENCI_BL_DIR . '/inc/class-penci-bf-shortcodes.php';
$penci_bl_shortcode = new Penci_Bf_Shortcodes();
$penci_bl_shortcode->add_hooks();

// Public Class to handle most of the functionalities of public side
require_once PENCI_BL_DIR . '/inc/class-penci-bf-public.php';
$penci_bl_public = new Penci_Bf_Public();
$penci_bl_public->add_hooks();

// Admin Pages Class for admin side
require_once PENCI_BL_ADMIN_DIR . '/class-penci-bf-admin.php';
$penci_bl_admin = new Penci_Bf_Admin();
$penci_bl_admin->add_hooks();

// loads the Templates Functions file
require_once PENCI_BL_DIR . '/inc/penci-bf-template-functions.php';

// Load Template Hook File
require_once PENCI_BL_DIR . '/inc/penci-bf-template-hooks.php';

// Load Settings
add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciBookmarkFollowCustomizer::getInstance();
		}
	}
);

add_action(
	'penci_top_search',
	function () {
		if ( get_theme_mod( 'pencibf_disable_header_icon' ) ) {
			return;
		}
		$pages = get_option( 'penci_bl_set_pages' );
		if ( isset( $pages['subscribe_manage_page'] ) && $pages['subscribe_manage_page'] && function_exists( 'penci_get_setting' ) ) {
			echo '<div id="penci-header-bookmark" class="top-search-classes"><a title="' . penci_get_setting( 'penci_trans_bookmark' ) . '" href="' . get_page_link( $pages['subscribe_manage_page'] ) . '">' . penci_icon_by_ver( 'fa fa-bookmark-o' ) . '</a></div>';
		}
	},
	9999
);
if ( ! function_exists( 'pencibg_default_text' ) ) {
	function pencibg_default_text() {
		return array(
			'postIfollow'    => __( 'Posts Being Followed', 'penci-bookmark-follow' ),
			'authorIfollow'  => __( 'Authors Being Followed', 'penci-bookmark-follow' ),
			'catIfollow'     => __( 'Categories Being Followed', 'penci-bookmark-follow' ),
			'deletedpost'    => __( 'This post has been deleted.', 'penci-bookmark-follow' ),
			'deletedauthor'  => __( 'This user has been deleted.', 'penci-bookmark-follow' ),
			'notitle'        => __( '(no title)', 'penci-bookmark-follow' ),
			'avatar'         => __( 'Avatar', 'penci-bookmark-follow' ),
			'authorname'     => __( 'Author Name', 'penci-bookmark-follow' ),
			'followeddate'   => __( 'Followed Date', 'penci-bookmark-follow' ),
			'actions'        => __( 'Action', 'penci-bookmark-follow' ),
			'moreauthors'    => __( 'Load More Authors', 'penci-bookmark-follow' ),
			'smoreauthors'   => __( 'Sorry, No More Authors', 'penci-bookmark-follow' ),
			'moreterms'      => __( 'Load More Items', 'penci-bookmark-follow' ),
			'smoreterms'     => __( 'Sorry, No More Items', 'penci-bookmark-follow' ),
			'nopostfollow'   => __( 'You have not follow any posts yet.', 'penci-bookmark-follow' ),
			'noauthorfollow' => __( 'You have not follow any authors yet.', 'penci-bookmark-follow' ),
			'term_follow'    => __( 'You can find more categories to follow by clicking on <a target="_blank" href="%s">this link</a>', 'penci-bookmark-follow' ),
		);
	}
}
if ( ! function_exists( 'pencibf_get_text' ) ) {
	function pencibf_get_text( $text ) {
		$texts = pencibg_default_text();

		return get_theme_mod( 'pencibf_text_' . $text ) ? get_theme_mod( 'pencibf_text_' . $text ) : $texts[ $text ];
	}
}

if ( ! function_exists( 'pencibf_soledad_time_link' ) ) :
	/**
	 * Gets a nicely formatted string for the published date.
	 */
	function pencibf_soledad_time_link( $id = null, $dformat = null ) {
		$get_the_date  = get_the_date( DATE_W3C, $id );
		$get_the_time  = get_the_time( get_option( 'date_format' ), $id );
		$get_the_datem = get_the_modified_date( DATE_W3C, $id );
		$get_the_timem = get_the_modified_date( get_option( 'date_format' ), $id );
		$classes       = 'published';
		$format        = get_theme_mod( 'penci_date_format' );
		if ( 'timeago' == $dformat ) {
			$format = 'timeago';
		} elseif ( 'normal' == $dformat ) {
			$format = 'normal';
		}

		if ( $id ) {
			if ( get_theme_mod( 'penci_show_modified_date' ) ) {
				$get_the_date = $get_the_datem;
				$get_the_time = $get_the_timem;
			}

			if ( 'timeago' == $format ) {
				$current_time = current_time( 'timestamp' );
				$post_time    = get_the_time( 'U', $id );
				if ( get_theme_mod( 'penci_show_modified_date' ) ) {
					$post_time = get_the_modified_time( 'U', $id );
				}
				if ( $current_time > $post_time ) {
					$get_the_time = penci_get_setting( 'penci_trans_beforeago' ) . ' ' . human_time_diff( $post_time, $current_time ) . ' ' . penci_get_setting( 'penci_trans_tago' );
				}
			}

			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U', $id ) !== get_the_modified_time( 'U', $id ) ) {
				if ( get_theme_mod( 'penci_show_modified_date' ) ) {
					$classes = 'updated';
				}
				$time_string = '<time class="entry-date ' . $classes . '" datetime="%1$s">%2$s</time>';
			}

			printf(
				$time_string,
				$get_the_date,
				$get_the_time
			);
		}
	}
endif;
add_action(
	'soledad_theme/custom_css',
	function () {
		$styles = array(
			'pencibf_bm_cl'     => '.penci-bf-follow-post-wrapper .pencibf-following-text:before{color:{{VALUE}}}',
			'pencibf_bm_bcl'    => '.penci-bf-follow-post-wrapper .pencibf-following-text:before{border-color:{{VALUE}}}',
			'pencibf_bm_bgcl'   => '.penci-bf-follow-post-wrapper .pencibf-following-text:before{background-color:{{VALUE}}}',
			'pencibf_bm_hcl'    => '.penci-bf-follow-post-wrapper .pencibf-following-text:hover:before{color:{{VALUE}}}',
			'pencibf_bm_hbcl'   => '.penci-bf-follow-post-wrapper .pencibf-following-text:hover:before{border-color:{{VALUE}}}',
			'pencibf_bm_hbgcl'  => '.penci-bf-follow-post-wrapper .pencibf-following-text:hover:before{background-color:{{VALUE}}}',
			'pencibf_bm_bmcl'   => '.penci-bf-follow-post-wrapper .penci-bf-following-button .pencibf-following-text:before{color:{{VALUE}}}',
			'pencibf_bm_bmbcl'  => '.penci-bf-follow-post-wrapper .penci-bf-following-button .pencibf-following-text:before{border-color:{{VALUE}}}',
			'pencibf_bm_bmbgcl' => '.penci-bf-follow-post-wrapper .penci-bf-following-button .pencibf-following-text:before{background-color:{{VALUE}}}',
		);

		$css = '';

		foreach ( $styles as $mod => $prop ) {
			$val = get_theme_mod( $mod );
			if ( $val ) {
				$css .= str_replace( '{{VALUE}}', $val, $prop );
			}
		}
		echo $css;
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_bookmark_follow_panel'] = array(
			'priority'                                => 30,
			'path'                                    => PENCI_BL_DIR . '/customizer/',
			'panel'                                   => array(
				'icon'  => 'fas fa-bookmark',
				'title' => esc_html__( 'Bookmark & Follow', 'soledad' ),
			),
			'penci_bookmark_follow_general_section'   => array( 'title' => esc_html__( 'General', 'penci-bookmark-follow' ) ),
			'penci_bookmark_follow_noti_section'      => array( 'title' => esc_html__( 'Notifications Settings', 'penci-bookmark-follow' ) ),
			'penci_bookmark_follow_email_section'     => array( 'title' => esc_html__( 'Email Settings', 'penci-bookmark-follow' ) ),
			'penci_bookmark_follow_translate_section' => array( 'title' => esc_html__( 'Quick Text Translation', 'penci-bookmark-follow' ) ),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-bookmark-follow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);
