<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public Class
 *
 * Handles all public functionalities of plugin
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
class Penci_Bf_Public {

	public $model, $message;

	public function __construct() {

		global $penci_bl_model, $penci_bl_message;
		$this->model   = $penci_bl_model;
		$this->message = $penci_bl_message;

		$this->message->init();
	}

	/**
	 * Process Functionality Of Post Comment
	 *
	 * Handles to process functionality when
	 * comment post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_comment_insert( $comment_ID, $comment_data ) {

		global $wpdb, $user_ID, $user_email, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$follow_status = '1';

		// get commented post id
		$comment_post_ID = $comment_data->comment_post_ID;

		//Get post type
		$post_type = get_post_type( $comment_post_ID );

		//Get all public post types
		$custom_post_types = get_post_types( array( 'public' => true ) );

		//If post type public truw
		if ( ! array_key_exists( $post_type, $custom_post_types ) ) {
			return;
		}

		// if user not logged in, then take input email as user_email
		if ( ! is_user_logged_in() ) {

			$user_email = $comment_data->comment_author_email;

			// store this email to session for later use
			set_transient( 'penci_bl_post_email', $user_email );
		}

		// If not set or enable auto follow when comment added
		if ( ! isset( $penci_bl_options['disable_auto_follow_add_comment'] ) ) {

			// args to check if this user_email is subscribed on this commented post
			$args = array(
				'post_status'    => 'publish',
				'post_parent'    => $comment_post_ID,
				'posts_per_page' => '-1',
				'post_type'      => PENCI_BL_POST_TYPE,
				'meta_key'       => $prefix . 'post_user_email',
				'meta_value'     => $user_email
			);

			$data = get_posts( $args );

			// if not then create new post with subscribe this user email
			if ( count( $data ) <= 0 ) {

				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_POST_TYPE,
					'post_parent'  => $comment_post_ID
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );
					// update post user email
					update_post_meta( $followed_post_id, $prefix . 'post_user_email', $user_email );
				}

			} else { // if get data then update its meta fields
				update_post_meta( $data[0]->ID, $prefix . 'follow_status', $follow_status );
				// update post user email
				update_post_meta( $data[0]->ID, $prefix . 'post_user_email', $user_email );

			}
		}

		if ( isset( $comment_data->comment_approved ) && $comment_data->comment_approved == '1' ) {

			// if status is approved, then send email and create log
			$this->model->penci_bl_create_comments( $comment_data );
		}
	}

	/**
	 * Send comment subscription email when comment approved by admin
	 *
	 * Handles to send comment subscription email when comment approved by admin
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_comment_unapproved_to_approved( $comment_data ) {

		// if status is approved, then send email and create log
		$this->model->penci_bl_create_comments( $comment_data );

	}

	/**
	 * Check If Clicked On Unsubscribe URL
	 *
	 * Handles to unsubscribe users to the post
	 * by clicking on unsubscribe url
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_email_unsubscribe() {

		// To prevent global $post object notice/warnings in admin post listing page
		if ( is_admin() ) {
			return;
		}

		global $penci_bl_options, $post;

		$unsub_type = '';
		$unsub_id   = '';

		$prefix = PENCI_BL_META_PREFIX;

		if ( isset( $_GET['penci_bl_action'] ) && ! empty( $_GET['penci_bl_action'] )
		     && base64_decode( $_GET['penci_bl_action'] ) == 'unsubscribe'
		     && isset( $_GET['penci_bl_email'] ) && ! empty( $_GET['penci_bl_email'] ) ) {

			if ( isset( $_GET['type'] ) && isset( $_GET['id'] ) ) {
				// get type of unsubscribtion
				$unsub_type = base64_decode( $_GET['type'] );
				$unsub_id   = $_GET['id'];
			}

			$email = base64_decode( $_GET['penci_bl_email'] );
			$email = rawurldecode( $email );

			$all_follows = $this->model->penci_bl_check_follow_email( $email );

			// unsubscribe type and id is set
			if ( ! empty( $unsub_type ) && ! empty( $unsub_id ) && ! empty( $all_follows ) ) {
				//set session to unsubscribe message
				$this->message->add_session( 'penci-bf-unsubscribe', esc_html__( 'Your email is unsubscribed successfully.', 'penci-bookmark-follow' ), 'success' );

				// Unsubscribe user from specific post, term or author define in misc-functions
				penci_bl_unsubscribe_user( $unsub_type, $unsub_id );
			}

			// if not define unsubscription type
			if ( ! empty( $all_follows ) ) { // Check email is exist or not

				// Check email exist in follow posts
				if ( isset( $all_follows['follow_posts'] ) && ! empty( $all_follows['follow_posts'] ) ) {

					foreach ( $all_follows['follow_posts'] as $follow_post_id ) {

						// unsubscribe email from followers list
						update_post_meta( $follow_post_id, $prefix . 'follow_status', '0' );

					}
				}


				// Check email exist in follow authors
				if ( isset( $all_follows['follow_authors'] ) && ! empty( $all_follows['follow_authors'] ) ) {

					foreach ( $all_follows['follow_authors'] as $follow_author_id ) {

						// unsubscribe email from followers list
						update_post_meta( $follow_author_id, $prefix . 'follow_status', '0' );

					}
				}

				//set session to unsubscribe message
				$this->message->add_session( 'penci-bf-unsubscribe', esc_html__( 'Your email is unsubscribed successfully.', 'penci-bookmark-follow' ), 'success' );

			} else {

				//set message to unsubscribe message
				$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Sorry, This email id does not exist in our system.', 'penci-bookmark-follow' ) );

			}
			$unsubscribe_page_id = isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] ) ? $penci_bl_options['unsubscribe_page'] : $post->ID;
			$url                 = get_permalink( $unsubscribe_page_id );
			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * Subscribe email by confirmation link
	 *
	 * Handles to subscribe email by confirmation link
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_email_subscribe() {

		// To prevent global $post object notice/warnings in admin post listing page
		if ( is_admin() ) {
			return;
		}

		global $wpdb, $user_ID, $user_email, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		// get current post id
		$current_post_ID = get_the_ID();

		// Check Confirmation email from user
		if ( isset( $_GET['penci_bl_action'] ) && ! empty( $_GET['penci_bl_action'] )
		     && base64_decode( $_GET['penci_bl_action'] ) == 'subscribe'
		     && isset( $_GET['penci_bl_email'] ) && ! empty( $_GET['penci_bl_email'] )
		     && isset( $_GET['penci_bl_post_id'] ) && ! empty( $_GET['penci_bl_post_id'] ) ) {

			// get post id
			$post_id = base64_decode( $_GET['penci_bl_post_id'] );

			$follow_status     = '1';
			$follow_user_email = base64_decode( $_GET['penci_bl_email'] );
			$follow_user_email = rawurldecode( $follow_user_email );

			// args to check user is following this post?
			$args = array(
				'post_status'    => 'publish',
				'post_parent'    => $post_id,
				'posts_per_page' => '-1',
				'post_type'      => PENCI_BL_POST_TYPE,
				'meta_key'       => $prefix . 'post_user_email',
				'meta_value'     => $follow_user_email
			);

			// get results from args		
			$result = get_posts( $args );

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_POST_TYPE,
					'post_parent'  => $post_id,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update post user email
					update_post_meta( $followed_post_id, $prefix . 'post_user_email', $follow_user_email );

					if ( ! empty( $follow_user_email ) ) {

						//set session to subscribe message
						$this->message->add_session( 'penci-bf-email-subscribe-' . $post_id, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update post user email
				update_post_meta( $result[0]->ID, $prefix . 'post_user_email', $follow_user_email );

				//set session to subscribe message
				$this->message->add_session( 'penci-bf-email-subscribe-' . $post_id, esc_html__( 'Your email is already subscribed for this post.', 'penci-bookmark-follow' ) );

				$follow_args = array(
					'ID'          => $result[0]->ID,
					'post_title'  => $user_ID,
					'post_author' => $user_ID
				);
				wp_update_post( $follow_args );

			}

			$followed_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

			do_action( 'penci_bl_follow_post_email_subscribe', $followed_post_id );

			//Subscribe post URL
			$post_redirect_url = apply_filters( 'penci_bl_subscribed_post_url', get_permalink( $current_post_ID ), $post_id );

			wp_redirect( $post_redirect_url );
			exit;
		}

		// Check Confirmation email from user
		if ( isset( $_GET['penci_bl_action'] ) && ! empty( $_GET['penci_bl_action'] )
		     && base64_decode( $_GET['penci_bl_action'] ) == 'subscribeterm'
		     && isset( $_GET['penci_bl_email'] ) && ! empty( $_GET['penci_bl_email'] )
		     && isset( $_GET['penci_bl_term_id'] ) && ! empty( $_GET['penci_bl_term_id'] )
		     && isset( $_GET['penci_bl_taxonomy'] ) && ! empty( $_GET['penci_bl_taxonomy'] ) ) {

			// get posttype
			$posttype = base64_decode( $_GET['penci_bl_posttype'] );

			// get taxonomy
			$taxonomy = base64_decode( $_GET['penci_bl_taxonomy'] );

			// get term id
			$term_id = base64_decode( $_GET['penci_bl_term_id'] );

			$follow_status     = '1';
			$follow_user_email = base64_decode( $_GET['penci_bl_email'] );
			$follow_user_email = rawurldecode( $follow_user_email );

			// args to check user is following this post?
			$args = array(
				'post_status'    => 'publish',
				'post_type'      => PENCI_BL_TERM_POST_TYPE,
				'post_parent'    => $term_id,
				'posts_per_page' => '-1',
				'meta_key'       => $prefix . 'term_user_email',
				'meta_value'     => $follow_user_email
			);

			// get results from args		
			$result = get_posts( $args );

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_TERM_POST_TYPE,
					'post_parent'  => $term_id,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update category user email
					update_post_meta( $followed_post_id, $prefix . 'term_user_email', $follow_user_email );

					// update post type
					update_post_meta( $followed_post_id, $prefix . 'post_type', $posttype );

					// update taxonomy
					update_post_meta( $followed_post_id, $prefix . 'taxonomy_slug', $taxonomy );

					if ( ! empty( $follow_user_email ) ) {

						//set session to subscribe message
						$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update category user email
				update_post_meta( $result[0]->ID, $prefix . 'term_user_email', $follow_user_email );

				// update post type
				update_post_meta( $result[0]->ID, $prefix . 'post_type', $posttype );

				// update taxonomy
				update_post_meta( $result[0]->ID, $prefix . 'taxonomy_slug', $taxonomy );

				//set session to subscribe message
				$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is already subscribed for this term.', 'penci-bookmark-follow' ) );

				$follow_args = array(
					'ID'          => $result[0]->ID,
					'post_title'  => $user_ID,
					'post_author' => $user_ID
				);
				wp_update_post( $follow_args );

			}

			$followed_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

			//Subscribe taxonomy URL
			$taxonomy_redirect_url = apply_filters( 'penci_bl_subscribed_taxonomy_url', get_permalink( $current_post_ID ), $term_id );

			wp_redirect( $taxonomy_redirect_url );
			exit;
		}

		// Check Confirmation email from user
		if ( isset( $_GET['penci_bl_action'] ) && ! empty( $_GET['penci_bl_action'] )
		     && base64_decode( $_GET['penci_bl_action'] ) == 'subscribeauthor'
		     && isset( $_GET['penci_bl_email'] ) && ! empty( $_GET['penci_bl_email'] )
		     && isset( $_GET['penci_bl_author_id'] ) && ! empty( $_GET['penci_bl_author_id'] ) ) {

			// get author id
			$author_id = base64_decode( $_GET['penci_bl_author_id'] );

			$follow_status     = '1';
			$follow_user_email = base64_decode( $_GET['penci_bl_email'] );
			$follow_user_email = rawurldecode( $follow_user_email );

			// args to check user is following this post?
			$args = array(
				'post_status'    => 'publish',
				'post_type'      => PENCI_BL_AUTHOR_POST_TYPE,
				'post_parent'    => $author_id,
				'posts_per_page' => '-1',
				'meta_key'       => $prefix . 'author_user_email',
				'meta_value'     => $follow_user_email
			);

			// get results from args		
			$result = get_posts( $args );

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_AUTHOR_POST_TYPE,
					'post_parent'  => $author_id,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update category user email
					update_post_meta( $followed_post_id, $prefix . 'author_user_email', $follow_user_email );

					if ( ! empty( $follow_user_email ) ) {

						//set session to subscribe message
						$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update category user email
				update_post_meta( $result[0]->ID, $prefix . 'author_user_email', $follow_user_email );

				//set session to subscribe message
				$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is already subscribed for this author.', 'penci-bookmark-follow' ) );

				$follow_args = array(
					'ID'          => $result[0]->ID,
					'post_title'  => $user_ID,
					'post_author' => $user_ID
				);
				wp_update_post( $follow_args );

			}

			$followed_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

			do_action( 'penci_bl_follow_author_email_subscribe', $followed_post_id );

			//Subscribe author URL
			$author_redirect_url = apply_filters( 'penci_bl_subscribed_author_url', get_permalink( $current_post_ID ), $author_id );

			wp_redirect( $author_redirect_url );
			exit;
		}

	}

	/**
	 * Display Follow Button with Content
	 *
	 * Handles to display follow button with content
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_content_filter( $post_id = '', $class = 'normal' ) {

		global $penci_bl_options, $post;

		$html = '';

		ob_start();
		do_action( 'penci_bl_follow_post', array( 'follow_pos_class' => $class ) );
		$html .= ob_get_clean();

		echo $html;
	}

	/**
	 * Display Follow Message
	 *
	 * Handle to display follow message
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_display_message( $content ) {

		global $penci_bl_options, $post;

		$html = '';

		$follow_post_id = apply_filters( 'penci_bl_follow_display_message_post_id', $post->ID );

		if ( is_user_logged_in() || ( ! isset( $penci_bl_options['disable_follow_guest'] ) || ( isset( $penci_bl_options['disable_follow_guest'] ) && empty( $penci_bl_options['disable_follow_guest'] ) ) ) ) {

			if ( $this->message->size( 'penci-bf-email-subscribe-' . $follow_post_id ) > 0 ) { //make success message
				$html .= '<div class="penci_bl_follow_message">';
				$html .= $this->message->output( 'penci-bf-email-subscribe-' . $follow_post_id );
				$html .= '</div><!--penci_bl_follow_message-->';
			}
		}

		return $html . $content;
	}

	/**
	 * Initial Loaded
	 *
	 * Handle to add functionality when page loaded
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_loaded() {


		add_action( 'penci_bookmark_post', array( $this, 'penci_bl_follow_content_filter' ), 10, 2 );

	}

	public function penci_bl_follow_single_loaded() {


		if ( ! ( $this->model->penci_bl_has_shortcode( 'pencibf_follow_me' ) ) && is_single() ) {
			//change the content using filter
			add_action( 'penci_single_meta_content', array( $this, 'penci_bl_follow_content_filter' ) );
		}


	}

	/**
	 * Follow Post
	 *
	 * Handle to follow post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_post() {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'penci_bookmark_follow' ) ) {
			wp_send_json_error( esc_html__( 'Nonce verification fails.', 'penci-bookmark-follow' ) );
		}

		global $user_ID, $user_email, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$user_ID            = isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : $user_ID;
		$current_post_ID    = isset( $_POST['currentpostid'] ) && ! empty( $_POST['currentpostid'] ) ? $_POST['currentpostid'] : '';
		$post_ID            = isset( $_POST['postid'] ) && ! empty( $_POST['postid'] ) ? $_POST['postid'] : '';
		$follow_status      = isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ? $_POST['status'] : '0';
		$follow_user_email  = isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ? $_POST['email'] : $user_email;
		$only_following     = isset( $_POST['onlyfollowing'] ) && ! empty( $_POST['onlyfollowing'] ) ? $_POST['onlyfollowing'] : '';
		$disable_reload     = isset( $_POST['disable_reload'] ) && $_POST['disable_reload'] ? true : false;
		$email_confirmation = '';

		// args to check user is following this post?
		$args = array(
			'post_status'    => 'publish',
			'post_parent'    => $post_ID,
			'posts_per_page' => '-1',
			'post_type'      => PENCI_BL_POST_TYPE,
			'meta_key'       => $prefix . 'post_user_email',
			'meta_value'     => $follow_user_email
		);

		// get results from args		
		$result = get_posts( $args );

		// Check Require Email confirmation from settings and user is not logged in
		if ( empty( $result ) && ! empty( $follow_user_email ) && $follow_status == '1'
		     && ( ( isset( $penci_bl_options['double_opt_in'] ) && $penci_bl_options['double_opt_in'] == '1' && is_user_logged_in() )
		          || ( is_super_admin() && ( ! empty( $_POST['page'] ) && $_POST['page'] == 'penci-bf-add-follower' ) )
		          || ( ! is_user_logged_in() && ! isset( $penci_bl_options['disable_follow_guest'] ) && isset( $penci_bl_options['double_opt_in_guest'] ) && $penci_bl_options['double_opt_in_guest'] == '1' ) ) ) {

			$email_args = array(
				'penci_bl_email'  => $follow_user_email,
				'post_id'         => $post_ID,
				'current_post_id' => $current_post_ID,
			);
			$this->model->penci_bl_confirmation_email( $email_args );

			if ( isset( $_POST['page'] ) ) {
				echo esc_html__( 'Please check your email inbox to confirm subscription.', 'penci-bookmark-follow' );
			} else {
				echo 'confirm';
			}
			exit;
		}

		if ( is_user_logged_in() && $follow_status == '0' && $current_post_ID ) {
			$log_ids = $this->model->penci_bl_get_follow_post_user_logs_data( array(
				'logid'  => $current_post_ID,
				'fields' => 'ids'
			) );
			foreach ( $log_ids as $log_id ) {
				wp_delete_post( $log_id, true );
			}
			wp_delete_post( $current_post_ID, true );
		}

		if ( empty( $email_confirmation ) ) {

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_POST_TYPE,
					'post_parent'  => $post_ID,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author']      = $user_ID;
					$follow_post_args['post_author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update post user email
					update_post_meta( $followed_post_id, $prefix . 'post_user_email', $follow_user_email );

					if ( ! is_user_logged_in() && ! $disable_reload ) {
						//set session to subscribe message
						$this->message->add_session( 'penci-bf-email-subscribe-' . $post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				$exist_follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update post user email
				update_post_meta( $result[0]->ID, $prefix . 'post_user_email', $follow_user_email );

				if ( ! is_user_logged_in() || ( is_super_admin() && isset( $_POST['page'] ) && $_POST['page'] == 'penci-bf-add-follower' ) ) {

					if ( $exist_follow_status == '1' ) {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci-bf-email-subscribe-' . $post_ID, esc_html__( 'Your email is already subscribed for this post.', 'penci-bookmark-follow' ) );
						} else {
							$guest_error = esc_html__( 'Your email is already subscribed for this post.', 'penci-bookmark-follow' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo sprintf(
								esc_html__( '%s This Email is already subscribed for this post. %s', 'penci-bookmark-follow' ),
								'<span class="penci_bl_add_follower_error">',
								'</span>'
							);
							exit;
						}

					} else {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci-bf-email-subscribe-' . $post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo esc_html__( 'Email is subscribed successfully.', 'penci-bookmark-follow' );
							exit;
						}
					}
				} else {
					$follow_args = array(
						'ID'          => $result[0]->ID,
						'post_title'  => $user_ID,
						'post_author' => $user_ID
					);
					wp_update_post( $follow_args );
				}
			}
		}

		$inserted_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

		do_action( 'penci_bl_follow_post_action', $inserted_post_id );

		// get user counts
		$numn = penci_bl_get_post_followers_count( $post_ID );

		if ( $only_following ) {
			ob_start();
			//do action to load follow posts html via ajax

			$atts['only_following'] = $only_following;
			do_action( 'penci_bl_manage_follow_posts', $atts );
			echo ob_get_clean();

		} else {

			//Pass error message instead of number when guest error
			if ( ! empty( $guest_error ) ) {
				$numn = $guest_error;
			}

			echo $numn;
		}
		exit;
	}

	/**
	 * Follow Author
	 *
	 * Handle to follow Author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_author() {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'penci_bookmark_follow' ) ) {
			wp_send_json_error( esc_html__( 'Nonce verification fails.', 'penci-bookmark-follow' ) );
		}

		global $user_ID, $user_email, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$user_ID            = isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : $user_ID;
		$current_post_ID    = isset( $_POST['currentpostid'] ) && ! empty( $_POST['currentpostid'] ) ? $_POST['currentpostid'] : '';
		$follow_authorid    = isset( $_POST['authorid'] ) && ! empty( $_POST['authorid'] ) ? $_POST['authorid'] : '';
		$follow_status      = isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ? $_POST['status'] : '0';
		$follow_user_email  = isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ? $_POST['email'] : $user_email;
		$only_following     = isset( $_POST['onlyfollowing'] ) && ! empty( $_POST['onlyfollowing'] ) ? $_POST['onlyfollowing'] : '';
		$disable_reload     = isset( $_POST['disable_reload'] ) && $_POST['disable_reload'] ? true : false;
		$email_confirmation = '';

		// args to check user is following this post?
		$args = array(
			'post_status'    => 'publish',
			'post_type'      => PENCI_BL_AUTHOR_POST_TYPE,
			'post_parent'    => $follow_authorid,
			'posts_per_page' => '-1',
			'meta_key'       => $prefix . 'author_user_email',
			'meta_value'     => $follow_user_email
		);

		// get results from args		
		$result = get_posts( $args );

		// Check Require Email confirmation from settings and user is not logged in
		if ( empty( $result ) && ! empty( $follow_user_email ) && $follow_status == '1'
		     && ( ( isset( $penci_bl_options['double_opt_in'] ) && $penci_bl_options['double_opt_in'] == '1' && is_user_logged_in() )
		          || ( is_super_admin() && ( isset( $_POST['page'] ) && $_POST['page'] == 'penci-bf-add-follower' ) )
		          || ( ! is_user_logged_in() && ! isset( $penci_bl_options['disable_follow_guest'] ) && isset( $penci_bl_options['double_opt_in_guest'] ) && $penci_bl_options['double_opt_in_guest'] == '1' )
		     ) ) {

			$email_args = array(
				'penci_bl_email'  => $follow_user_email,
				'author_id'       => $follow_authorid,
				'current_post_id' => $current_post_ID,
				'page'            => $_POST['page']
			);
			$this->model->penci_bl_author_confirmation_email( $email_args );

			//set session to subscribe message
			if ( ! is_user_logged_in() && ! $disable_reload ) { //if user is not logged in then show message after sending email for confirmation
				//$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Please check your email inbox to confirm subscription.', 'penci-bookmark-follow' ), 'success' );
			}
			$email_confirmation = 'true';

			if ( isset( $_POST['page'] ) ) {
				echo esc_html__( 'Please check your email inbox to confirm subscription.', 'penci-bookmark-follow' );
			} else {
				echo 'confirm';
			}
			exit;
		}

		/*if ( is_user_logged_in() && $follow_status == '0' ) {
			$log_ids = $this->model->penci_bl_get_follow_author_user_logs_data( array(
				'logid'  => $current_post_ID,
				'fields' => 'ids'
			) );
			foreach ( $log_ids as $log_id ) {
				wp_delete_post( $log_id, true );
			}
			wp_delete_post( $current_post_ID, true );
		}*/

		if ( empty( $email_confirmation ) && ! empty( $follow_authorid ) ) {

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_AUTHOR_POST_TYPE,
					'post_parent'  => $follow_authorid,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author']      = $user_ID;
					$follow_post_args['post_author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update category user email
					update_post_meta( $followed_post_id, $prefix . 'author_user_email', $follow_user_email );

					if ( ! is_user_logged_in() && ! $disable_reload ) {
						//set session to subscribe message
						$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				$exist_follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update category user email
				update_post_meta( $result[0]->ID, $prefix . 'author_user_email', $follow_user_email );

				if ( ! is_user_logged_in() || ( is_super_admin() && isset( $_POST['page'] ) && $_POST['page'] == 'penci-bf-add-follower' ) ) {

					if ( $exist_follow_status == '1' ) {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is already subscribed for this author.', 'penci-bookmark-follow' ) );
						} else {
							$guest_error = esc_html__( 'Your email is already subscribed for this author.', 'penci-bookmark-follow' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo sprintf( esc_html__( '%s Your email is already subscribed for this author. %s', 'penci-bookmark-follow' ),
								'<span class="penci_bl_add_follower_error">',
								'</span>'
							);
							exit;
						}
					} else {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci-bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo esc_html__( 'Email is subscribed successfully.', 'penci-bookmark-follow' );
							exit;
						}
					}
				} else {
					$follow_args = array(
						'ID'          => $result[0]->ID,
						'post_title'  => $user_ID,
						'post_author' => $user_ID
					);
					wp_update_post( $follow_args );
				}
			}
		}

		$inserted_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

		do_action( 'penci_bl_follow_author_action', $inserted_post_id );

		// get user counts
		$numn = penci_bl_get_author_followers_count( $follow_authorid );

		if ( $only_following ) {
			ob_start();
			//do action to load follow posts html via ajax

			$atts['only_following'] = $only_following;
			do_action( 'penci_bl_manage_follow_authors', $atts );
			echo ob_get_clean();
		} else {

			//Pass error message instead of number when guest error
			if ( ! empty( $guest_error ) ) {
				$numn = $guest_error;
			}

			echo $numn;
		}
		exit;
	}

	/**
	 * After user registration
	 *
	 * Handles to save data after user registration
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_user_registration( $user_id ) {

		$user = get_user_by( 'id', $user_id );

		if ( ! empty( $user ) ) { // Check user is not exist

			$user_email = $user->user_email;

			// args to check if this user_email is exist for follow post
			$args = array(
				'penci_bl_email' => $user_email,
				'fields'         => 'ids'
			);

			$follow_posts = $this->model->penci_bl_get_follow_post_users_data( $args );

			foreach ( $follow_posts as $follow_post_id ) {

				$follow_args = array(
					'ID'          => $follow_post_id,
					'post_title'  => $user->ID,
					'post_author' => $user->ID
				);
				wp_update_post( $follow_args );

			}
		}
	}

	/**
	 * Send Unsubscribe Confirmation Email
	 *
	 * Handles to send unsubscribe confirmation email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_send_unsubscribe_conformation() {

		// Check unsubscribe click button
		if ( isset( $_POST['penci_bl_unsubscribe_submit'] ) && ! empty( $_POST['penci_bl_unsubscribe_submit'] ) ) {

			// Check unsubscribe email is not empty
			if ( isset( $_POST['penci_bl_unsubscribe_email'] ) && ! empty( $_POST['penci_bl_unsubscribe_email'] ) ) {

				$_POST['penci_bl_unsubscribe_email'] = trim( $_POST['penci_bl_unsubscribe_email'] );
				// Check unsubscribe email is valid
				if ( is_email( $_POST['penci_bl_unsubscribe_email'] ) ) {

					$email = $_POST['penci_bl_unsubscribe_email'];

					$all_follows = $this->model->penci_bl_check_follow_email( $email );
					if ( ! empty( $all_follows ) ) { // Check email is exist or not

						$send_mail = $this->model->penci_bl_confirmation_unsubscribe_email( array( 'penci_bl_email' => $email ) );
						if ( $send_mail ) {
							//set message to unsubscribe message
							$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Please check your email inbox to confirm unsubscription.', 'penci-bookmark-follow' ), 'success' );
						} else {
							$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Mail sent error.', 'penci-bookmark-follow' ), 'success' );
						}
					} else {

						//set message to unsubscribe message
						$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Sorry, This email id does not exist in our system.', 'penci-bookmark-follow' ) );

					}

				} else {

					//set message to unsubscribe message
					$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Please enter valid email.', 'penci-bookmark-follow' ) );

				}
			} else {

				//set message to unsubscribe message
				$this->message->add( 'penci-bf-unsubscribe', esc_html__( 'Please enter email.', 'penci-bookmark-follow' ) );

			}
		}
	}

	/**
	 * AJAX call
	 *
	 * Handles to show details of with ajax
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_posts_ajax() {

		if ( is_user_logged_in() ) {
			ob_start();
			//do action to load follow posts html via ajax
			do_action( 'penci_bl_manage_follow_posts_ajax' );
			echo ob_get_clean();
			exit;
		} else {
			return esc_html__( 'You have not follow any posts yet.', 'penci-bookmark-follow' );
		}
	}

	/**
	 * AJAX call
	 *
	 * Handles to show details of with ajax
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_follow_authors_ajax() {

		if ( is_user_logged_in() ) {
			ob_start();
			//do action to load follow authors html via ajax
			do_action( 'penci_bl_manage_follow_authors' );
			echo ob_get_clean();
			exit;
		} else {
			return esc_html__( 'You have not follow any authors yet.', 'penci-bookmark-follow' );
		}
	}

	/**
	 * AJAX call
	 *
	 * Handles to show details of with ajax
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_action_post() {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'penci_bookmark_follow' ) ) {
			wp_send_json_error( esc_html__( 'Nonce verification fails.', 'penci-bookmark-follow' ) );
		}

		if ( isset( $_POST['bulkaction'] ) && $_POST['bulkaction'] == 'delete'
		     && isset( $_POST['ids'] ) && ! empty( $_POST['ids'] ) && $_POST['ids'] != ',' ) {

			$ids = explode( ',', trim( $_POST['ids'], ',' ) );
			foreach ( $ids as $id ) {
				$log_ids = $this->model->penci_bl_get_follow_post_user_logs_data( array(
					'logid'  => $id,
					'fields' => 'ids'
				) );
				foreach ( $log_ids as $log_id ) {
					wp_delete_post( $log_id, true );
				}
				wp_delete_post( $id, true );
			}
			ob_start();
			//do action to load follow terms html via ajax
			do_action( 'penci_bl_manage_follow_posts' );
			echo ob_get_clean();
			exit;
		}
	}


	/**
	 * AJAX call
	 *
	 * Handles to show details of with ajax
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_action_author() {

		if ( ! wp_verify_nonce( $_POST['nonce'], 'penci_bookmark_follow' ) ) {
			wp_send_json_error( esc_html__( 'Nonce verification fails.', 'penci-bookmark-follow' ) );
		}

		if ( isset( $_POST['bulkaction'] ) && $_POST['bulkaction'] == 'delete'
		     && isset( $_POST['ids'] ) && ! empty( $_POST['ids'] ) && $_POST['ids'] != ',' ) {

			$ids = explode( ',', trim( $_POST['ids'], ',' ) );

			foreach ( $ids as $id ) {
				$log_ids = $this->model->penci_bl_get_follow_author_user_logs_data( array(
					'logid'  => $id,
					'fields' => 'ids'
				) );
				foreach ( $log_ids as $log_id ) {
					wp_delete_post( $log_id, true );
				}
				wp_delete_post( $id, true );
			}
			ob_start();
			//do action to load follow authors html via ajax
			do_action( 'penci_bl_manage_follow_authors' );
			echo ob_get_clean();
			exit;
		}
	}

	/**
	 * Adding Follow Author Button
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_buddypress_follow_button() {

		global $bp, $penci_bl_options;

		if ( ! empty( $bp->displayed_user->id ) ) {

			$args = apply_filters( 'penci_bl_buddypress_follow_button_args', array(
				'author_id'      => $bp->displayed_user->id,
				'follow_message' => ! empty( $penci_bl_options['authors_follow_message'] ) ? $penci_bl_options['authors_follow_message'] : '',
				'follow_buttons' => array(
					'follow'    => '',
					'following' => '',
					'unfollow'  => '',
				),
				'disable_reload' => true,
			) );

			do_action( 'penci_bl_follow_author', $args );
		}
	}

	/**
	 * Adding BuddyPress tabs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_buddypress_profile_tabs() {

		global $bp;

		//Get current profile member id
		$user_id   = get_current_user_id();
		$member_id = ! empty( $bp->displayed_user->id ) ? $bp->displayed_user->id : '';
		if ( ! empty( $member_id ) ) {

			//Get author's following count
			$flwg_count_args = array(
				'author' => $member_id,
				'count'  => '1',
			);

			//Check if visited profile is not from current user
			if ( $user_id != $member_id ) {
				$flwg_count_args['penci_bl_status'] = 'subscribe';
			}

			$following_count     = $this->model->penci_bl_get_follow_author_users_data( $flwg_count_args );
			$following_count_msg = ! empty( $following_count ) ? '<span class="count">' . $following_count . '</span>' : '<span class="no-count">0</span>';

			//Get author's followers count
			$flwr_count_args = array(
				'authorid'        => $member_id,
				'penci_bl_status' => 'subscribe',
				'count'           => '1',
				'author__not_in'  => array( - 0 )
			);

			$follower_count     = $this->model->penci_bl_get_follow_author_users_data( $flwr_count_args );
			$follower_count_msg = ! empty( $follower_count ) ? '<span class="count">' . $follower_count . '</span>' : '<span class="no-count">0</span>';

			//Create Followers tab
			bp_core_new_nav_item( array(
				'name'                    => sprintf( esc_html__( 'Followers %s', 'bdfollow' ), $follower_count_msg ),
				'slug'                    => 'followers',
				'screen_function'         => array( $this, 'penci_bl_buddypress_followers_callback' ),
				'position'                => 75,
				'show_for_displayed_user' => true,
				'parent_url'              => $bp->displayed_user->domain,
				'parent_slug'             => $bp->profile->slug,
				'default_subnav_slug'     => 'followers'
			) );

			//Create Following tab
			bp_core_new_nav_item( array(
				'name'                    => sprintf( esc_html__( 'Following %s', 'bdfollow' ), $following_count_msg ),
				'slug'                    => 'following',
				'screen_function'         => array( $this, 'penci_bl_buddypress_following_callback' ),
				'position'                => 75,
				'show_for_displayed_user' => true,
				'parent_url'              => $bp->displayed_user->domain,
				'parent_slug'             => $bp->profile->slug,
				'default_subnav_slug'     => 'following'
			) );
		}
	}

	/**
	 * BuddyPress Followers Callback
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_buddypress_followers_callback() {

		//add title and content here - last is to call the members plugin.php template
		add_action( 'bp_template_content', array( $this, 'bd_follow_followers_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * BuddyPress Followers Content
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function bd_follow_followers_content() {

		global $penci_bl_options, $bp;

		$member_id = ! empty( $bp->displayed_user->id ) ? $bp->displayed_user->id : '';
		if ( ! empty( $member_id ) ) {

			echo '<p class="pencibf_notice">' . sprintf( esc_html__( '%s NOTE: %s Guest users who have followed the @%s have not been listed here.', 'penci-bookmark-follow' ), '<strong>', '</strong>', get_the_author_meta( 'user_login', $member_id ) ) . '</p>';

			$args = apply_filters( 'penci_bl_buddypress_author_followers_args', array(
				'author_id'     => $member_id,
				'exclude_guest' => true
			) );
			do_action( 'penci_bl_author_followers', $args );
		}
	}

	/**
	 * BuddyPress Following Callback
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_buddypress_following_callback() {

		//add title and content here - last is to call the members plugin.php template
		add_action( 'bp_template_content', array( $this, 'bd_follow_following_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * BuddyPress Following Content
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function bd_follow_following_content() {

		global $bp;

		$user_id   = get_current_user_id();
		$member_id = ! empty( $bp->displayed_user->id ) ? $bp->displayed_user->id : '';
		if ( ! empty( $member_id ) ) {

			//Arguments
			$args = array(
				'author_id' => $member_id,
			);

			//Check if visited page is not current user profile
			if ( $user_id != $member_id ) {
				$args['disabled_actions'] = 1;
				$args['only_following']   = 1;
			}

			do_action( 'penci_bl_manage_follow_authors', apply_filters( 'penci_bl_buddypress_manage_follow_authors_args', $args ) );
		}
	}

	/**
	 * BuddyPress Following Menu
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_buddypress_admin_menu() {

		global $wp_admin_bar, $bp;

		$user_id = get_current_user_id();
		if ( ! bp_use_wp_admin_bar() || defined( 'DOING_AJAX' ) ) {
			return;
		}

		//Get urls
		$user_domain    = bp_loggedin_user_domain();
		$following_link = trailingslashit( $user_domain . 'following' );
		$followers_link = trailingslashit( $user_domain . 'followers' );

		//Get author's following count
		$flwg_count_args     = array(
			'author' => $user_id,
			'count'  => '1',
		);
		$following_count     = $this->model->penci_bl_get_follow_author_users_data( $flwg_count_args );
		$following_count_msg = ! empty( $following_count ) ? '<span class="count">' . $following_count . '</span>' : '';

		//Get author's followers count
		$flwr_count_args    = array(
			'authorid'        => $user_id,
			'penci_bl_status' => 'subscribe',
			'count'           => '1',
			'author__not_in'  => array( - 0 )
		);
		$follower_count     = $this->model->penci_bl_get_follow_author_users_data( $flwr_count_args );
		$follower_count_msg = ! empty( $follower_count ) ? '<span class="count">' . $follower_count . '</span>' : '';

		// add menu item
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account-xprofile',
			'id'     => 'my-account-xprofile-followers',
			'title'  => esc_html__( 'Followers ', 'penci-bookmark-follow' ) . $follower_count_msg,
			'href'   => trailingslashit( $followers_link ),
		) );

		// add menu item
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-account-xprofile',
			'id'     => 'my-account-xprofile-following',
			'title'  => esc_html__( 'Following ', 'penci-bookmark-follow' ) . $following_count_msg,
			'href'   => trailingslashit( $following_link ),
		) );
	}

	/**
	 * Add "Notify followers for this update" option in elementor frontend editor
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.9.9
	 */
	public function penci_bl_add_elementor_page_settings_controls( $page ) {

		if ( isset( $page ) && $page->get_id() > "" ) {

			$post = get_post( $page->get_id() );

			if ( isset( $post->post_status ) && $post->post_status == 'publish' && $this->model->penci_bl_check_post_update_notification() ) {

				$prefix = PENCI_BL_META_PREFIX;

				$page->add_control(
					$prefix . 'email_notification',
					[
						'label' => esc_html__( 'Notify followers for this update', 'penci-bookmark-follow' ),
						'type'  => \Elementor\Controls_Manager::SWITCHER,
					]
				);
			}
		}
	}

	/**
	 * AJAX call
	 *
	 * Handles to show details of with ajax
	 *
	 * @package Follow My Blog Post
	 * @since 1.1.0
	 */
	public function penci_bf_follow_terms_ajax() {

		if ( is_user_logged_in() ) {
			ob_start();
			//do action to load follow terms html via ajax
			do_action( 'penci_bl_manage_follow_terms' );
			echo ob_get_clean();
			exit;
		} else {
			return esc_html__( 'You have not follow any terms yet.', 'penci-bookmark-follow' );
		}
	}


	public function penci_bl_bulk_action_term() {

		if ( isset( $_POST['bulkaction'] ) && $_POST['bulkaction'] == 'delete'
		     && isset( $_POST['ids'] ) && ! empty( $_POST['ids'] ) && $_POST['ids'] != ',' ) {

			$ids = explode( ',', trim( $_POST['ids'], ',' ) );

			foreach ( $ids as $id ) {
				$log_ids = $this->model->penci_bl_get_follow_term_user_logs_data( array(
					'logid'  => $id,
					'fields' => 'ids'
				) );
				foreach ( $log_ids as $log_id ) {
					wp_delete_post( $log_id, true );
				}
				wp_delete_post( $id, true );
			}
			ob_start();
			//do action to load follow terms html via ajax
			do_action( 'wpw_fp_manage_follow_terms' );
			echo ob_get_clean();
			exit;
		}
	}

	/**
	 * Follow Category
	 */
	public function penci_bl_follow_terms() {

		global $user_ID, $user_email, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$user_ID            = isset( $_POST['user_id'] ) && ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : $user_ID;
		$current_post_ID    = isset( $_POST['currentpostid'] ) && ! empty( $_POST['currentpostid'] ) ? $_POST['currentpostid'] : '';
		$follow_posttype    = isset( $_POST['posttype'] ) && ! empty( $_POST['posttype'] ) ? $_POST['posttype'] : '';
		$follow_taxonomy    = isset( $_POST['taxonomyslug'] ) && ! empty( $_POST['taxonomyslug'] ) ? $_POST['taxonomyslug'] : '';
		$follow_termid      = isset( $_POST['termid'] ) && ! empty( $_POST['termid'] ) ? $_POST['termid'] : '';
		$follow_status      = isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ? $_POST['status'] : '0';
		$follow_user_email  = isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ? $_POST['email'] : $user_email;
		$only_following     = isset( $_POST['onlyfollowing'] ) && ! empty( $_POST['onlyfollowing'] ) ? $_POST['onlyfollowing'] : '';
		$disable_reload     = isset( $_POST['disable_reload'] ) && $_POST['disable_reload'] ? true : false;
		$email_confirmation = '';

		// args to check user is following this post?
		$args = array(
			'post_status'    => 'publish',
			'post_type'      => PENCI_BL_TERM_POST_TYPE,
			'post_parent'    => $follow_termid,
			'posts_per_page' => '-1',
			'meta_key'       => $prefix . 'term_user_email',
			'meta_value'     => $follow_user_email
		);

		// get results from args
		$result = get_posts( $args );

		// Check Require Email confirmation from settings and user is not logged in
		if ( empty( $result ) && ! empty( $follow_user_email ) && $follow_status == '1'
		     && ( ( isset( $penci_bl_options['double_opt_in'] ) && $penci_bl_options['double_opt_in'] == '1' && is_user_logged_in() )
		          || ( is_super_admin() && isset( $_POST['page'] ) && $_POST['page'] == 'penci_bf-add-follower' )
		          || ( ! is_user_logged_in() && ! isset( $penci_bl_options['disable_follow_guest'] ) && isset( $penci_bl_options['double_opt_in_guest'] ) && $penci_bl_options['double_opt_in_guest'] == '1' )
		     ) ) {

			$email_args = array(
				'penci_bl_email'  => $follow_user_email,
				'posttype'        => $follow_posttype,
				'taxonomy'        => $follow_taxonomy,
				'term_id'         => $follow_termid,
				'current_post_id' => $current_post_ID,
				'page'            => $_POST['page']
			);
			$this->model->penci_bl_term_confirmation_email( $email_args );

			//set session to subscribe message
			if ( ! is_user_logged_in() && ! $disable_reload ) { //if user is not logged in then show message after sending email for confirmation
				//$this->message->add_session( 'penci_bf-email-subscribe-' . $current_post_ID, esc_html__( 'Please check your email inbox to confirm subscription.', 'penci-bookmark-follow' ), 'success' );
			}
			$email_confirmation = 'true';

			if ( isset( $_POST['page'] ) ) {
				echo esc_html__( 'Please check your email inbox to confirm subscription.', 'penci-bookmark-follow' );
			} else {
				echo 'confirm';
			}
			exit;
		}

		if ( empty( $email_confirmation ) && ! empty( $follow_termid ) && ! empty( $follow_taxonomy ) ) {

			if ( empty( $result ) ) {

				// args for create custom post type for following user
				$follow_post_args = array(
					'post_title'   => $user_ID,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_type'    => PENCI_BL_TERM_POST_TYPE,
					'post_parent'  => $follow_termid,
				);
				if ( is_user_logged_in() ) {
					$follow_post_args['author']      = $user_ID;
					$follow_post_args['post_author'] = $user_ID;
				}
				$followed_post_id = wp_insert_post( $follow_post_args );

				// if post is created successfully
				if ( $followed_post_id ) {

					// update follow status
					update_post_meta( $followed_post_id, $prefix . 'follow_status', $follow_status );

					// update category user email
					update_post_meta( $followed_post_id, $prefix . 'term_user_email', $follow_user_email );

					// update post type
					update_post_meta( $followed_post_id, $prefix . 'post_type', $follow_posttype );

					// update taxonomy slug
					update_post_meta( $followed_post_id, $prefix . 'taxonomy_slug', $follow_taxonomy );

					if ( ! is_user_logged_in() && ! $disable_reload ) {
						//set session to subscribe message
						$this->message->add_session( 'penci_bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
					}
				}
			} else if ( count( $result ) > 0 ) {

				$exist_follow_status = get_post_meta( $result[0]->ID, $prefix . 'follow_status', true );

				// update follow status
				update_post_meta( $result[0]->ID, $prefix . 'follow_status', $follow_status );

				// update category user email
				update_post_meta( $result[0]->ID, $prefix . 'term_user_email', $follow_user_email );

				// update post type
				update_post_meta( $result[0]->ID, $prefix . 'post_type', $follow_posttype );

				// update taxonomy slug
				update_post_meta( $result[0]->ID, $prefix . 'taxonomy_slug', $follow_taxonomy );

				if ( ! is_user_logged_in() || ( is_super_admin() && isset( $_POST['page'] ) && $_POST['page'] == 'penci_bf-add-follower' ) ) {

					if ( $exist_follow_status == '1' ) {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci_bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is already subscribed for this term.', 'penci-bookmark-follow' ) );
						} else {
							$guest_error = esc_html__( 'Your email is already subscribed for this item.', 'penci-bookmark-follow' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo sprintf(
								esc_html__( '%s This Email is already subscribed for this term. %s', 'penci-bookmark-follow' ),
								'<span class="penci_bl_add_follower_error">',
								'</span>'
							);
							exit;
						}
					} else {

						//Check if not disable reload and message append
						if ( ! $disable_reload ) {

							//set session to subscribe message
							$this->message->add_session( 'penci_bf-email-subscribe-' . $current_post_ID, esc_html__( 'Your email is subscribed successfully.', 'penci-bookmark-follow' ), 'success' );
						}

						if ( isset( $_POST['page'] ) ) {
							echo esc_html__( 'Email is subscribed successfully.', 'penci-bookmark-follow' );
							exit;
						}
					}
				} else {
					$follow_args = array(
						'ID'          => $result[0]->ID,
						'post_title'  => $user_ID,
						'post_author' => $user_ID
					);
					wp_update_post( $follow_args );
				}
			}
		}

		$inserted_post_id = ! empty( $followed_post_id ) ? $followed_post_id : $result[0]->ID;

		do_action( 'penci_bl_term_action', $inserted_post_id );

		// get user counts
		$numn = penci_bl_get_term_followers_count( $follow_termid );

		if ( $only_following ) {
			ob_start();
			//do action to load follow posts html via ajax

			$atts['only_following'] = $only_following;
			do_action( 'penci_bl_manage_follow_terms', $atts );
			echo ob_get_clean();
		} else {

			//Pass error message instead of number when guest error
			if ( ! empty( $guest_error ) ) {
				$numn = $guest_error;
			}

			echo $numn;
		}
		exit;
	}

	/**
	 * Adding Hooks
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function add_hooks() {

		global $penci_bl_options;

		//add action to send comment subscription email when comment inserted and approved
		add_action( 'wp_insert_comment', array( $this, 'penci_bl_comment_insert' ), 99, 2 );

		//add action to send comment subscription email when comment approved by admin
		add_action( 'comment_unapproved_to_approved', array( $this, 'penci_bl_comment_unapproved_to_approved' ) );

		//wp call
		add_action( 'wp', array( $this, 'penci_bl_email_unsubscribe' ) );
		add_action( 'wp', array( $this, 'penci_bl_email_subscribe' ) );
		add_action( 'init', array( $this, 'penci_bl_follow_loaded' ) );
		add_action( 'wp', array( $this, 'penci_bl_follow_single_loaded' ) );

		//AJAX call for follow post
		add_action( 'wp_ajax_penci_bl_follow_post', array( $this, 'penci_bl_follow_post' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_post', array( $this, 'penci_bl_follow_post' ) );

		//AJAX call for follow author
		add_action( 'wp_ajax_penci_bl_follow_author', array( $this, 'penci_bl_follow_author' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_author', array( $this, 'penci_bl_follow_author' ) );

		//AJAX call for follow terms
		add_action( 'wp_ajax_penci_bl_follow_terms', array( $this, 'penci_bl_follow_terms' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_terms', array( $this, 'penci_bl_follow_terms' ) );

		//ajax pagination for follow posts
		add_action( 'wp_ajax_penci_bl_follow_post_next_page', array( $this, 'penci_bl_follow_posts_ajax' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_post_next_page', array( $this, 'penci_bl_follow_posts_ajax' ) );

		//ajax pagination for follow authors
		add_action( 'wp_ajax_penci_bl_follow_author_next_page', array( $this, 'penci_bl_follow_authors_ajax' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_author_next_page', array( $this, 'penci_bl_follow_authors_ajax' ) );

		//ajax pagination for follow authors
		add_action( 'wp_ajax_penci_bl_bulk_action_author', array( $this, 'penci_bl_bulk_action_author' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_bulk_action_author', array( $this, 'penci_bl_bulk_action_author' ) );

		//ajax pagination for follow terms
		add_action( 'wp_ajax_penci_bl_follow_term_next_page', array( $this, 'penci_bf_follow_terms_ajax' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_follow_term_next_page', array( $this, 'penci_bf_follow_terms_ajax' ) );

		//ajax pagination for follow terms
		add_action( 'wp_ajax_penci_bl_bulk_action_term', array( $this, 'penci_bl_bulk_action_term' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_bulk_action_term', array( $this, 'penci_bl_bulk_action_term' ) );


		add_action( 'wp_ajax_penci_bl_bulk_action_post', array( $this, 'penci_bl_bulk_action_post' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_bulk_action_post', array( $this, 'penci_bl_bulk_action_post' ) );

		//user registraion
		add_action( 'user_register', array( $this, 'penci_bl_user_registration' ) );

		//unsubscribe confirmation
		add_action( 'wp', array( $this, 'penci_bl_send_unsubscribe_conformation' ) );

		//Check if buddypress enabled
		if ( ! empty( $penci_bl_options['enabled_supports'] ) && is_array( $penci_bl_options['enabled_supports'] )
		     && in_array( 'buddypress', $penci_bl_options['enabled_supports'] ) ) {

			add_action( 'bp_before_member_header_meta', array( $this, 'penci_bl_buddypress_follow_button' ) );
			add_action( 'bp_setup_nav', array( $this, 'penci_bl_buddypress_profile_tabs' ), 100 );
			add_action( 'bp_setup_admin_bar', array( $this, 'penci_bl_buddypress_admin_menu' ), 300 );
		}

		// get all post type
		$enabledPosts = ! empty( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

		foreach ( $enabledPosts as $key => $postType ) {
			// add custom fields in elementor page settings
			add_action( 'elementor/element/wp-' . $postType . '/document_settings/before_section_end', array(
				$this,
				'penci_bl_add_elementor_page_settings_controls'
			) );
		}
	}
}