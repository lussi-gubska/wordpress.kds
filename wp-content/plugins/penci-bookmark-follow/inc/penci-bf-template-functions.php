<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Templates Functions
 *
 * Handles to manage templates of plugin
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 *
 */


/**
 * Returns the path to the Penci Bookmark & Follow templates directory
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
function penci_bl_get_templates_dir() {

	return apply_filters( 'penci_bl_template_dir', PENCI_BL_DIR . '/inc/templates/' );

}

function penci_bl_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	if ( ! $template_path ) {
		$template_path = PENCI_BL_BASENAME . '/';
	}
	if ( ! $default_path ) {
		$default_path = penci_bl_get_templates_dir();
	}

	// Look within passed path within the theme - this is priority

	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'penci_bl_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. follow my blog post attributes) passing attributes and including the file.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 *
 */

function penci_bl_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = penci_bl_locate_template( $template_name, $template_path, $default_path );

	do_action( 'penci_bl_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'penci_bl_after_template_part', $template_name, $template_path, $located, $args );
}

/************************************ Call Follow Post Functions ***************************/

if ( ! function_exists( 'penci_bl_follow_post' ) ) {

	/**
	 * Load Follow Post Template
	 *
	 * Handles to load follow post template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_post( $args ) {

		$args['display_type'] = 'icon';

		if ( is_single() && is_main_query() ) {
			$args['display_type'] = 'text';
		}

		//follow post template
		penci_bl_get_template( 'follow-post/follow-post.php', array( 'args' => $args ) );

	}
}

if ( ! function_exists( 'penci_bl_follow_post_content' ) ) {

	/**
	 * Load Follow Term Content Template
	 *
	 * Handles to load follow term content template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_post_content( $args ) {

		global $wpdb, $user_ID, $user_email, $penci_bl_options, $penci_bl_model;

		$prefix = PENCI_BL_META_PREFIX;

		// post id
		$post_id   = isset( $args['post_id'] ) && ! empty( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
		$shortcode = isset( $args['shortcode'] ) ? $args['shortcode'] : false;

		// check follow is enabled for post
		if ( $penci_bl_model->penci_bl_check_enable_follow( $post_id, $shortcode ) ) {

			// follow class
			$follow_pos_class = isset( $args['follow_pos_class'] ) ? $args['follow_pos_class'] : '';

			// current post id
			$current_post_id = isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ? $args['current_post_id'] : '';

			// follow text
			$follow_text = isset( $args['follow_buttons']['follow'] ) && ! empty( $args['follow_buttons']['follow'] ) ? $args['follow_buttons']['follow'] : $penci_bl_options['follow_buttons']['follow'];

			// following text
			$following_text = isset( $args['follow_buttons']['following'] ) && ! empty( $args['follow_buttons']['following'] ) ? $args['follow_buttons']['following'] : $penci_bl_options['follow_buttons']['following'];

			// unfollow text
			$unfollow_text = isset( $args['follow_buttons']['unfollow'] ) && ! empty( $args['follow_buttons']['unfollow'] ) ? $args['follow_buttons']['unfollow'] : $penci_bl_options['follow_buttons']['unfollow'];

			// follow message
			$follow_message = isset( $args['follow_message'] ) ? $args['follow_message'] : $penci_bl_options['follow_message'];

			$html = '';

			$follow_status = '0';

			// Check Disable Guest Followes from settings and followsemail is not empty
			if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) )
			     && isset( $_POST['followsemail'] ) && ! empty( $_POST['followsemail'] ) ) {

				$follow_email = $_POST['followsemail'];
			} else {
				$follow_email = $user_email;
			}

			// args to check user is following this post?
			$post_args = array(
				'post_status' => 'publish',
				'post_parent' => $post_id,
				'post_type'   => PENCI_BL_POST_TYPE,
				'meta_key'    => $prefix . 'post_user_email',
				'meta_value'  => $follow_email
			);

			// get results from args		
			$result = get_posts( $post_args );

			// if we get result then user is following this post
			if ( count( $result ) > 0 ) {
				$follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );
			}

			// get post type for post id
			$post_type = get_post_type( $current_post_id );

			// show follow me form is not on home page
			// OR enable follow me checked in meta for post

			if ( $follow_status == '1' ) {
				$follow_status = '0';
				$follow_class  = 'penci-bf-following-button';
				$follow_label  = $following_text;
			} else {
				$follow_status = '1';
				$follow_class  = 'penci-bf-follow-button';
				$follow_label  = $follow_text;
			}

			// Check user is logged in
			if ( is_user_logged_in() ) {

				$user_args = array(
					'follow_message'   => $follow_message,
					'follow_status'    => $follow_status,
					'follow_label'     => $follow_label,
					'follow_class'     => $follow_class,
					'follow_pos_class' => $follow_pos_class,
					'post_id'          => $post_id,
					'current_post_id'  => $current_post_id,
					'follow_text'      => $follow_text,
					'following_text'   => $following_text,
					'unfollow_text'    => $unfollow_text,
				);

				//follow term template for register user
				penci_bl_get_template( 'follow-post/user.php', $user_args );

			} else if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) ) ) {

				//Added extra class for disable reload
				$extra_classes = isset( $args['disable_reload'] ) && $args['disable_reload'] == true ? 'disable-reload' : '';

				// follow post privacy
				$privacy_page = ( isset( $penci_bl_options['gdpr_privacy_page'] ) && ! empty( $penci_bl_options['gdpr_privacy_page'] ) ) ? $penci_bl_options['gdpr_privacy_page'] : '';

				$privacy_message = ( isset( $penci_bl_options['gdpr_privacy_message'] ) && ! empty( $penci_bl_options['gdpr_privacy_message'] ) ) ? $penci_bl_options['gdpr_privacy_message'] : '';

				$gdpr_enable = ( isset( $penci_bl_options['gdpr_enable'] ) && ! empty( $penci_bl_options['gdpr_enable'] ) ) ? $penci_bl_options['gdpr_enable'] : '';

				if ( ! empty( $privacy_message ) && ! empty( $gdpr_enable ) ) {

					$privacy_url  = ! empty( $privacy_page ) ? get_permalink( $privacy_page ) : '';
					$privacy_link = '';

					if ( ! empty( $privacy_url ) && ! empty( $privacy_page ) ) {
						$privacy_link = '<a href="' . $privacy_url . '" target="_blank">' . get_the_title( $privacy_page ) . '</a>';
					}

					$privacy_message = str_replace( '[privacy_policy]', $privacy_link, $privacy_message );
				} else {
					$privacy_message = '';
				}

				$guest_args = array(
					'follow_message'   => $follow_message,
					'follow_status'    => $follow_status,
					'follow_label'     => $follow_label,
					'follow_class'     => $follow_class,
					'follow_pos_class' => $follow_pos_class,
					'post_id'          => $post_id,
					'current_post_id'  => $current_post_id,
					'follow_text'      => $follow_text,
					'following_text'   => $following_text,
					'unfollow_text'    => $unfollow_text,
					'extra_classes'    => $extra_classes,
					'privacy_message'  => $privacy_message,
					'gdpr_enable'      => $gdpr_enable
				);

				//follow term template for guest user
				penci_bl_get_template( 'follow-post/guest.php', $guest_args );

			}
		}
	}
}

if ( ! function_exists( 'penci_bl_follow_post_count_box' ) ) {

	/**
	 * Load Follow Post Count Box Template
	 *
	 * Handles to load follow post count box tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_post_count_box( $follow_message, $post_id ) {

		// get followers counts
		$numn = penci_bl_get_post_followers_count( $post_id );

		$follow_message = str_replace( '{followers_count}', '<span class="penci_bl_followers_count">' . $numn . '</span>', $follow_message );

		//follow count box template
		penci_bl_get_template( 'follow-post/follow-count-box.php', array( 'follow_message' => $follow_message ) );

	}
}

/************************************ Call Follow Author Functions ***************************/

if ( ! function_exists( 'penci_bl_follow_author' ) ) {

	/**
	 * Load Follow Author Template
	 *
	 * Handles to load follow author template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_author( $args ) {

		//follow author template
		penci_bl_get_template( 'follow-author/follow-author.php', array( 'args' => $args ) );

	}
}
if ( ! function_exists( 'penci_bl_follow_author_content' ) ) {

	/**
	 * Load Follow Author Content Template
	 *
	 * Handles to load follow author content template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_author_content( $args ) {

		global $wpdb, $user_ID, $user_email, $penci_bl_options, $penci_bl_model, $post;

		$prefix = PENCI_BL_META_PREFIX;

		// current post id
		$current_post_id = isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ? $args['current_post_id'] : get_the_ID();

		// current author id
		$author_id = isset( $args['author_id'] ) && ! empty( $args['author_id'] ) ? $args['author_id'] : $post->post_author;

		// follow text
		$follow_text = isset( $args['follow_buttons']['follow'] ) && ! empty( $args['follow_buttons']['follow'] ) ? $args['follow_buttons']['follow'] : $penci_bl_options['authors_follow_buttons']['follow'];

		// following text
		$following_text = isset( $args['follow_buttons']['following'] ) && ! empty( $args['follow_buttons']['following'] ) ? $args['follow_buttons']['following'] : $penci_bl_options['authors_follow_buttons']['following'];

		// unfollow text
		$unfollow_text = isset( $args['follow_buttons']['unfollow'] ) && ! empty( $args['follow_buttons']['unfollow'] ) ? $args['follow_buttons']['unfollow'] : $penci_bl_options['authors_follow_buttons']['unfollow'];

		// follow message
		$follow_message = isset( $args['follow_message'] ) ? $args['follow_message'] : $penci_bl_options['authors_follow_message'];

		$html = '';

		$follow_status = '0';

		// show follow me form is not on home page
		// Check authorid are not empty
		if ( ! empty( $author_id ) ) {

			$author_data = get_user_by( 'id', $author_id );

			if ( ! empty( $author_data ) ) { // Check author data is not empty

				$author_label = '';
				if ( isset( $author_data->display_name ) && ! empty( $author_data->display_name ) ) {
					$author_label = $author_data->display_name;
				}
				$follow_text    = str_replace( '{author_name}', $author_label, $follow_text );
				$following_text = str_replace( '{author_name}', $author_label, $following_text );
				$unfollow_text  = str_replace( '{author_name}', $author_label, $unfollow_text );

				// Check Disable Guest Followes from settings and followsemail is not empty
				if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) )
				     && isset( $_POST['followsemail'] ) && ! empty( $_POST['followsemail'] ) ) {

					$follow_email = $_POST['followsemail'];
				} else {
					$follow_email = $user_email;
				}

				// args to check user is following this post?
				$author_args = array(
					'post_status' => 'publish',
					'post_type'   => PENCI_BL_AUTHOR_POST_TYPE,
					'post_parent' => $author_id,
					'meta_key'    => $prefix . 'author_user_email',
					'meta_value'  => $follow_email
				);

				// get results from args		
				$result = get_posts( $author_args );

				// if we get result then user is following this post
				if ( count( $result ) > 0 ) {
					$follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );
				}

				// get post type for post id
				$post_type = get_post_type( $current_post_id );

				if ( $follow_status == '1' ) {
					$follow_status = '0';
					$follow_class  = 'penci-bf-following-button';
					$follow_label  = $following_text;
				} else {
					$follow_status = '1';
					$follow_class  = 'penci-bf-follow-button';
					$follow_label  = $follow_text;
				}

				// Check user is logged in
				if ( is_user_logged_in() ) {

					$user_args = array(
						'follow_message'  => $follow_message,
						'follow_status'   => $follow_status,
						'follow_label'    => $follow_label,
						'follow_class'    => $follow_class,
						'author_id'       => $author_id,
						'current_post_id' => $current_post_id,
						'follow_text'     => $follow_text,
						'following_text'  => $following_text,
						'unfollow_text'   => $unfollow_text,
					);

					//follow author template for register user
					penci_bl_get_template( 'follow-author/user.php', $user_args );

				} else if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) ) ) {

					//Added extra class for disable reload
					$extra_classes = isset( $args['disable_reload'] ) && $args['disable_reload'] == true ? 'disable-reload' : '';

					$gdpr_enable = ( isset( $penci_bl_options['gdpr_enable'] ) && ! empty( $penci_bl_options['gdpr_enable'] ) ) ? $penci_bl_options['gdpr_enable'] : '';

					// follow author privacy
					$privacy_page = ( isset( $penci_bl_options['gdpr_privacy_page'] ) && ! empty( $penci_bl_options['gdpr_privacy_page'] ) ) ? $penci_bl_options['gdpr_privacy_page'] : '';

					$privacy_message = ( isset( $penci_bl_options['gdpr_privacy_message'] ) && ! empty( $penci_bl_options['gdpr_privacy_message'] ) ) ? $penci_bl_options['gdpr_privacy_message'] : '';

					if ( ! empty( $privacy_message ) && ! empty( $gdpr_enable ) ) {

						$privacy_url  = ! empty( $privacy_page ) ? get_permalink( $privacy_page ) : '';
						$privacy_link = '';

						if ( ! empty( $privacy_url ) && ! empty( $privacy_page ) ) {
							$privacy_link = '<a href="' . $privacy_url . '" target="_blank">' . get_the_title( $privacy_page ) . '</a>';
						}

						$privacy_message = str_replace( '[privacy_policy]', $privacy_link, $privacy_message );
					} else {
						$privacy_message = '';
					}


					$guest_args = array(
						'follow_message'  => $follow_message,
						'follow_status'   => $follow_status,
						'follow_label'    => $follow_label,
						'follow_class'    => $follow_class,
						'author_id'       => $author_id,
						'current_post_id' => $current_post_id,
						'follow_text'     => $follow_text,
						'following_text'  => $following_text,
						'unfollow_text'   => $unfollow_text,
						'extra_classes'   => $extra_classes,
						'privacy_message' => $privacy_message,
						'gdpr_enable'     => $gdpr_enable
					);

					//follow author template for guest user
					penci_bl_get_template( 'follow-author/guest.php', $guest_args );

				}
			}
		}
	}
}

/************************************ Call Subscription Manage Page Functions ***************************/

if ( ! function_exists( 'penci_bl_subscribe_manage_content' ) ) {

	/**
	 * Load Subscription Manage Page Content Template
	 *
	 * Handles to load subscription manage page content tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_subscribe_manage_content() {

		// manage follow posts
		do_action( 'penci_bl_manage_follow_posts' );

		// manage follow author
		do_action( 'penci_bl_manage_follow_authors' );

		// manage follow terms
		do_action( 'penci_bl_manage_follow_terms' );
	}
}


if ( ! function_exists( 'penci_bl_manage_follow_terms' ) ) {

	/**
	 * Manage Follow Terms Template
	 *
	 */
	function penci_bl_manage_follow_terms( $args = array() ) {

		//manage follow terms template
		penci_bl_get_template( 'subscribe-manage/follow-terms.php', $args );
	}
}

if ( ! function_exists( 'penci_bl_manage_follow_posts' ) ) {

	/**
	 * Manage Follow Posts Template
	 *
	 * Handles to manage follow posts tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_manage_follow_posts( $args = array() ) {

		//manage follow posts template
		penci_bl_get_template( 'subscribe-manage/follow-posts.php', $args );
	}
}


if ( ! function_exists( 'penci_bl_follow_posts_listing_content' ) ) {

	/**
	 * Load Follow Posts Listing Table Template
	 *
	 * Handles to load follow posts listing table template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_posts_listing_content( $followposts, $paging ) {

		//follow posts template
		penci_bl_get_template(
			'subscribe-manage/follow-posts-listing/follow-posts-listing.php',
			array(
				'followposts' => $followposts,
				'paging'      => $paging
			)
		);
	}
}

if ( ! function_exists( 'penci_bl_manage_follow_authors' ) ) {

	/**
	 * Manage Follow authors Template
	 *
	 * Handles to manage follow authors tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_manage_follow_authors( $args = array() ) {

		//manage follow authors template
		penci_bl_get_template( 'subscribe-manage/follow-authors.php', $args );
	}
}

if ( ! function_exists( 'penci_bl_follow_authors_listing_content' ) ) {

	/**
	 * Load Follow authors Listing Table Template
	 *
	 * Handles to load follow authors listing table template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_authors_listing_content( $followauthors, $paging, $disabled_actions = false ) {

		//follow authors template
		penci_bl_get_template( 'subscribe-manage/follow-authors-listing/follow-authors-listing.php', array(
			'followauthors'    => $followauthors,
			'paging'           => $paging,
			'disabled_actions' => $disabled_actions,
		) );
	}
}

/************************************ Call Author's follower list Functions ***************************/

if ( ! function_exists( 'penci_bl_author_followers' ) ) {

	/**
	 * User's Followers Template
	 *
	 * Handles to manage User's Followers tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	function penci_bl_author_followers( $args = array() ) {

		//manage author's followers template
		penci_bl_get_template( 'author-followers/author-followers.php', $args );
	}
}

if ( ! function_exists( 'penci_bl_author_followers_listing_content' ) ) {

	/**
	 * Load author's Followers Listing Table Template
	 *
	 * Handles to load author's Followers listing table template
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	function penci_bl_author_followers_listing_content( $followers, $paging ) {

		//author's Followers template
		penci_bl_get_template( 'author-followers/author-followers-listing.php', array(
				'followers' => $followers,
				'paging'    => $paging
			)
		);
	}
}

/************************************ Call Unsubscribe Page Functions ***************************/

if ( ! function_exists( 'penci_bl_unsubscribe_content' ) ) {

	/**
	 * Load Unsubscribe Page Content Template
	 *
	 * Handles to load unsubscribe page content tempate
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_unsubscribe_content() {

		//follow count box template
		penci_bl_get_template( 'unsubscribe/content.php' );

	}
}

/************************************ Call Email Template Functions ***************************/

if ( ! function_exists( 'penci_bl_default_email_template' ) ) {
	function penci_bl_default_email_template( $message, $unsubscribe = false, $email = '', $unsubscribedata = array() ) {

		global $penci_bl_options;

		$unsubscribe_message = '';

		// site name with url 
		$site_name = get_bloginfo( 'name' );

		// Check Append Unsubscribe URL is enable
		if ( $unsubscribe ) {

			// get unsubscibe message
			$unsubscribe_message = penci_bl_get_unsubscribe_message();

			// Check disable unsubscribe confirmation option checked
			if ( isset( $penci_bl_options['unsubscribe_confirmation'] ) && ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) {

				// get direct unsubscibe message with unsubscibe link
				$unsubscribe_message = penci_bl_get_unsubscribe_link_message( $email, $unsubscribedata );
			}
		}

		$html_email_args = array(
			'site_name'           => $site_name,
			'message'             => $message,
			'unsubscribe_message' => $unsubscribe_message,
		);

		//html email template
		penci_bl_get_template( 'emailtemplate/htmlemail.php', $html_email_args );

	}
}

/************************************ Call Follow Term Functions ***************************/

if ( ! function_exists( 'penci_bl_follow_term' ) ) {

	/**
	 * Load Follow Term Template
	 *
	 */
	function penci_bl_follow_term( $args ) {

		//follow term template
		penci_bl_get_template( 'follow-term/follow-term.php', array( 'args' => $args ) );

	}
}
if ( ! function_exists( 'penci_bl_follow_term_content' ) ) {

	function penci_bl_follow_term_content( $args ) {

		global $wpdb, $user_ID, $user_email, $penci_bl_options, $penci_bl_model;

		$default_taxonomy = '';
		$default_term_id  = '';

		$prefix = PENCI_BL_META_PREFIX;

		if ( is_archive() ) {
			$term_default_data = get_queried_object();
			$default_taxonomy  = $term_default_data->taxonomy;
			$default_term_id   = $term_default_data->term_id;
		}

		// current post id
		$current_post_id = isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ? $args['current_post_id'] : get_the_ID();

		// follow post type
		$follow_posttype = isset( $args['follow_posttype'] ) && ! empty( $args['follow_posttype'] ) ? $args['follow_posttype'] : '';

		// follow taxonomy slug
		$follow_taxonomy_slug = isset( $args['follow_taxonomy'] ) && ! empty( $args['follow_taxonomy'] ) ? $args['follow_taxonomy'] : $default_taxonomy;

		// follow term slug
		$follow_term_id = isset( $args['follow_term_id'] ) && ! empty( $args['follow_term_id'] ) ? $args['follow_term_id'] : $default_term_id;

		// follow message
		$follow_message = isset( $args['follow_message'] ) ? $args['follow_message'] : $penci_bl_options['term_follow_message'];

		// follow text
		$follow_text = isset( $args['follow_buttons']['follow'] ) && ! empty( $args['follow_buttons']['follow'] ) ? $args['follow_buttons']['follow'] : $penci_bl_options['follow_buttons']['follow'];

		// following text
		$following_text = isset( $args['follow_buttons']['following'] ) && ! empty( $args['follow_buttons']['following'] ) ? $args['follow_buttons']['following'] : $penci_bl_options['follow_buttons']['following'];

		// unfollow text
		$unfollow_text = isset( $args['follow_buttons']['unfollow'] ) && ! empty( $args['follow_buttons']['unfollow'] ) ? $args['follow_buttons']['unfollow'] : $penci_bl_options['follow_buttons']['unfollow'];


		$html = '';

		$follow_status = '0';

		// show follow me form is not on home page
		// Check texonomy and termid are not empty
		if ( ! empty( $follow_taxonomy_slug ) && ! empty( $follow_term_id ) ) {

			$term_data = get_term_by( 'id', $follow_term_id, $follow_taxonomy_slug );

			if ( ! empty( $term_data ) ) { // Check term data is not empty

				$term_label = '';
				if ( isset( $term_data->name ) && ! empty( $term_data->name ) ) {
					$term_label = $term_data->name;
				}


				// Check Disable Guest Followes from settings and followsemail is not empty
				if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) )
				     && isset( $_POST['followsemail'] ) && ! empty( $_POST['followsemail'] ) ) {

					$follow_email = $_POST['followsemail'];
				} else {
					$follow_email = $user_email;
				}

				// args to check user is following this post?
				$term_args = array(
					'post_status' => 'publish',
					'post_type'   => PENCI_BL_TERM_POST_TYPE,
					'post_parent' => $follow_term_id,
					'meta_key'    => $prefix . 'term_user_email',
					'meta_value'  => $follow_email
				);

				// get results from args
				$result = get_posts( $term_args );

				// if we get result then user is following this post
				if ( count( $result ) > 0 ) {
					$follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );
				}

				// get post type for post id
				$post_type = get_post_type( $current_post_id );

				if ( $follow_status == '1' ) {
					$follow_status = '0';
					$follow_class  = 'penci-bf-following-button';
					$follow_label  = $following_text;

				} else {
					$follow_status = '1';
					$follow_class  = 'penci-bf-follow-button';
					$follow_label  = $follow_text;

				}

				// Check user is logged in
				if ( is_user_logged_in() ) {

					$user_args = array(
						'follow_message' => $follow_message,
						'follow_status'  => $follow_status,

						'follow_class'         => $follow_class,
						'follow_posttype'      => $follow_posttype,
						'follow_taxonomy_slug' => $follow_taxonomy_slug,
						'follow_term_id'       => $follow_term_id,
						'follow_term_name'     => $term_label,
						'current_post_id'      => $current_post_id,

						'follow_text'      => $follow_text,
						'following_text'   => $following_text,
						'unfollow_text'    => $unfollow_text,

					);

					//follow term template for register user
					penci_bl_get_template( 'follow-term/user.php', $user_args );

				} else if ( ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) ) ) {

					//Added extra class for disable reload
					$extra_classes = isset( $args['disable_reload'] ) && $args['disable_reload'] == true ? 'disable-reload' : '';

					// follow term privacy
					$privacy_page = ( isset( $penci_bl_options['gdpr_privacy_page'] ) && ! empty( $penci_bl_options['gdpr_privacy_page'] ) ) ? $penci_bl_options['gdpr_privacy_page'] : '';

					$gdpr_enable = ( isset( $penci_bl_options['gdpr_enable'] ) && ! empty( $penci_bl_options['gdpr_enable'] ) ) ? $penci_bl_options['gdpr_enable'] : '';

					$privacy_message = ( isset( $penci_bl_options['gdpr_privacy_message'] ) && ! empty( $penci_bl_options['gdpr_privacy_message'] ) ) ? $penci_bl_options['gdpr_privacy_message'] : '';

					if ( ! empty( $privacy_message ) && ! empty( $gdpr_enable ) ) {

						$privacy_url  = ! empty( $privacy_page ) ? get_permalink( $privacy_page ) : '';
						$privacy_link = '';

						if ( ! empty( $privacy_url ) && ! empty( $privacy_page ) ) {
							$privacy_link = '<a href="' . $privacy_url . '" target="_blank">' . get_the_title( $privacy_page ) . '</a>';
						}

						$privacy_message = str_replace( '[privacy_policy]', $privacy_link, $privacy_message );
					} else {
						$privacy_message = '';
					}

					$guest_args = array(
						'follow_message'   => $follow_message,
						'follow_status'    => $follow_status,
						'follow_term_name' => $term_label,

						'follow_class'         => $follow_class,
						'follow_posttype'      => $follow_posttype,
						'follow_taxonomy_slug' => $follow_taxonomy_slug,
						'follow_term_id'       => $follow_term_id,
						'current_post_id'      => $current_post_id,

						'follow_text'      => $follow_text,
						'following_text'   => $following_text,
						'unfollow_text'    => $unfollow_text,

						'extra_classes'   => $extra_classes,
						'privacy_message' => $privacy_message,
						'gdpr_enable'     => $gdpr_enable
					);

					//follow term template for guest user
					penci_bl_get_template( 'follow-term/guest.php', $guest_args );

				}
			}
		}
	}
}

if ( ! function_exists( 'penci_bl_terms_listing_content' ) ) {

	/**
	 * Load Follow Terms Listing Table Template
	 *
	 * Handles to load follow terms listing table template
	 *
	 * @package Follow My Blog Post
	 * @since 1.1.0
	 */
	function penci_bl_terms_listing_content( $followterms, $paging ) {

		//follow terms template
		penci_bl_get_template( 'subscribe-manage/follow-terms-listing/follow-terms-listing.php', array(
			'followterms' => $followterms,
			'paging'      => $paging
		) );
	}
}

if ( ! function_exists( 'penci_bl_follow_term_count_box' ) ) {

	function penci_bl_follow_term_count_box( $follow_message, $follow_term_id ) {

		// get user counts
		$numn = penci_bl_get_term_followers_count( $follow_term_id );

		$follow_message = str_replace( '{followers_count}', '<span class="penci_bl_followers_count">' . $numn . '</span>', $follow_message );

		//follow count box template
		penci_bl_get_template( 'follow-term/follow-count-box.php', array( 'follow_message' => $follow_message ) );

	}
}

if ( ! function_exists( 'penci_bl_get_term_thumb_url' ) ) {

	function penci_bl_get_term_thumb_url( $term_id, $size = 'thumbnail' ) {

		$thumb_url = get_template_directory_uri() . '/images/no-thumb.jpg';
		$cat_data  = get_option( "category_$term_id" );
		$thumb_id  = isset( $cat_data['thumbnail_id'] ) ? $cat_data['thumbnail_id'] : '';


		if ( $thumb_id ) {
			$thumb_url = wp_get_attachment_url( $thumb_id, $size );
		}

		return $thumb_url;
	}
}