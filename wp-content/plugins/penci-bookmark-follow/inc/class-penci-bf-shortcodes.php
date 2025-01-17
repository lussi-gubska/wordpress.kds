<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes Class
 *
 * Handles shortcodes functionality of plugin
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
class Penci_Bf_Shortcodes {

	public $model;

	public function __construct() {

		global $penci_bl_model;

		$this->model = $penci_bl_model;
	}

	/**
	 * Follow Post Shortcode
	 *
	 * Handles to replace the shortcode follow post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_shortcode( $atts, $content ) {

		global $post, $penci_bl_options;

		extract( shortcode_atts( array(
			'id'                => ! empty( $post->ID ) ? $post->ID : '',
			'disablecount'      => 'false',
			'followerscountmsg' => '',
			'followtext'        => '',
			'followingtext'     => '',
			'unfollowtext'      => '',
			'disable_reload'    => false,
		), $atts ) );
		if ( false === get_post_status( $id ) ) {
			$args    = array();
			$content = esc_html__( "Please enter post id.", "fmbpbjs" );

			return apply_filters( 'penci_bl_follow_shortcode_content', $content, $args );
		}
		$html = $followcountmsg = '';

		if ( $disablecount != 'true' ) { // Check not disable follow count
			// follow counter message
			$followcountmsg = ! empty( $followerscountmsg ) ? $followerscountmsg : $penci_bl_options['follow_message'];
		}

		$args = array(
			'post_id'        => $id,
			'follow_message' => $followcountmsg,
			'follow_buttons' => array(
				'follow'    => $followtext,
				'following' => $followingtext,
				'unfollow'  => $unfollowtext,
			),
			'shortcode'      => true,
			'disable_reload' => $disable_reload,
		);

		ob_start();
		do_action( 'penci_bl_follow_post', $args );
		$html .= ob_get_clean();

		$content = $content . $html;

		return apply_filters( 'penci_bl_follow_shortcode_content', $content, $args );
	}

	/**
	 * Follow Term Shortcode
	 *
	 * Handles to replace the shortcode follow author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_author_shortcode( $atts, $content ) {

		global $post, $penci_bl_options;

		$post_author = isset( $post->post_author ) ? $post->post_author : '';

		//Check when post author empty, consider as author page
		if ( empty( $post_author ) ) {

			//Get author id from author page query string
			$author      = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			$post_author = isset( $author->ID ) ? $author->ID : '';
		}

		extract( shortcode_atts( array(
			'author_id'         => $post_author,
			'disablecount'      => 'false',
			'followerscountmsg' => '',
			'followtext'        => '',
			'followingtext'     => '',
			'unfollowtext'      => '',
			'disable_reload'    => false,
		), $atts ) );

		$html = $followcountmsg = '';

		if ( ! empty( $author_id ) ) { // Check author id not empty

			if ( $disablecount != 'true' ) { // Check not disable follow count
				// follow counter message
				$followcountmsg = ! empty( $followerscountmsg ) ? $followerscountmsg : $penci_bl_options['authors_follow_message'];
			}

			if ( empty( $followtext ) ) {
				$followtext = $penci_bl_options['authors_follow_buttons']["follow"];
			}
			if ( empty( $followingtext ) ) {
				$followingtext = $penci_bl_options['authors_follow_buttons']["following"];
			}
			if ( empty( $unfollowtext ) ) {
				$unfollowtext = $penci_bl_options['authors_follow_buttons']["unfollow"];
			}

			$args = array(
				'author_id'      => $author_id,
				'follow_message' => $followcountmsg,
				'follow_buttons' => array(
					'follow'    => $followtext,
					'following' => $followingtext,
					'unfollow'  => $unfollowtext,
				),
				'disable_reload' => $disable_reload,
			);

			ob_start();
			do_action( 'penci_bl_follow_author', $args );
			$html .= ob_get_clean();

		}
		$content = $content . $html;

		return apply_filters( 'penci_bl_follow_author_shortcode_content', $content, $args );
	}

	/**
	 * Manage Follow Posts
	 *
	 * Handles to replace the shortcode for manage follow posts
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_post_list_shortcode( $atts, $content ) {

		ob_start();

		do_action( 'penci_bl_manage_follow_posts', $atts );
		$content .= ob_get_clean();

		return apply_filters( 'penci_bl_follow_post_list_shortcode_content', $content, $atts );
	}


	/**
	 * Manage Follow Authors
	 *
	 * Handles to replace the shortcode for manage follow author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_author_list_shortcode( $atts, $content ) {

		ob_start();
		do_action( 'penci_bl_manage_follow_authors', $atts );
		$content .= ob_get_clean();

		return apply_filters( 'penci_bl_follow_author_list_shortcode_content', $content, $atts );
	}

	/**
	 * User's Followers
	 *
	 * Handles to replace the shortcode for User's Followers
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_follow_follower_list_shortcode( $atts, $content ) {

		ob_start();
		do_action( 'penci_bl_author_followers', $atts );
		$content .= ob_get_clean();

		return apply_filters( 'penci_bl_follow_follower_list_shortcode_content', $content, $atts );
	}

	/**
	 * Manage Unsubscribe page
	 *
	 * Handles to replace the shortcode for manage follow author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_unsubscribe_shortcode( $atts, $content ) {

		ob_start();
		do_action( 'penci_bl_unsubscribe_content' );
		$content .= ob_get_clean();

		return apply_filters( 'penci_bl_unsubscribe_shortcode_content', $content, $atts );
	}


	/**
	 * Manage Follow Terms
	 *
	 */
	public function penci_bl_follow_term_list_shortcode( $atts, $content ) {

		ob_start();
		do_action( 'penci_bl_manage_follow_terms', $atts );
		$content .= ob_get_clean();

		return apply_filters( 'penci_bl_follow_term_list_shortcode_content', $content, $atts );
	}

	/**
	 * Adding Hooks
	 *
	 * Adding hooks for calling shortcodes.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function add_hooks() {

		//add filter to use shortcode for text widget
		add_filter( 'widget_text', 'do_shortcode' );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_me', array( $this, 'penci_bl_follow_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_author_me', array( $this, 'penci_bl_follow_author_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_post_list', array( $this, 'penci_bl_follow_post_list_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_author_list', array( $this, 'penci_bl_follow_author_list_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_follower_list', array( $this, 'penci_bl_follow_follower_list_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_unsubscribe', array( $this, 'penci_bl_unsubscribe_shortcode' ) );

		//change the content using shortcode
		add_shortcode( 'pencibf_follow_term_list', array( $this, 'penci_bl_follow_term_list_shortcode' ) );
	}
}