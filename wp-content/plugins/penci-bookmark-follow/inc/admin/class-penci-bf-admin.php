<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class
 *
 * Handles all admin functionalities of plugin
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
class Penci_Bf_Admin {

	var $model, $render, $scripts, $message, $public;

	function __construct() {

		global $penci_bl_model, $penci_bl_render, $penci_bl_script, $penci_bl_message, $penci_bl_public;
		$this->model   = $penci_bl_model;
		$this->render  = $penci_bl_render;
		$this->scripts = $penci_bl_script;
		$this->message = $penci_bl_message;
		$this->public  = $penci_bl_public;
	}

	/**
	 * Register All need admin menu page
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_add_admin_menu() {

		global $current_user;

		// get current user role
		$user_role = $current_user->roles;

		// Add menu for author role user for author follow list
		if ( in_array( 'author', $user_role ) ) {

			add_menu_page( esc_html__( 'Penci Bookmark & Follow ', 'penci-bookmark-follow' ), esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ), 'publish_posts', 'penci-bf-author', array(
				$this,
				'penci_bl_list_authors',
			) );
		}

		// follow blog post page
		add_menu_page( esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ), esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ), 'manage_options', 'penci-bf-post', array(
			$this,
			'penci_bl_list_users'
		), 'dashicons-paperclip' );

		add_submenu_page( 'penci-bf-post', esc_html__( 'Penci Bookmark & Follow - Followed Posts', 'penci-bookmark-follow' ), esc_html__( 'Followed Posts', 'penci-bookmark-follow' ), 'manage_options', 'penci-bf-post', array(
			$this,
			'penci_bl_list_users'
		) );

		// Followed Authors page
		add_submenu_page( 'penci-bf-post', esc_html__( 'Penci Bookmark & Follow - Followed Authors', 'penci-bookmark-follow' ), esc_html__( 'Followed Authors', 'penci-bookmark-follow' ), 'manage_options', 'penci-bf-author', array(
			$this,
			'penci_bl_list_authors'
		) );

		// Send Emails To Followers
		$send_email_page = add_submenu_page( 'penci-bf-post', esc_html__( 'Penci Bookmark & Follow - Send Emails', 'penci-bookmark-follow' ), apply_filters( 'wpwp_fp_admin_send_email_menu_text', esc_html__( 'Send Emails', 'penci-bookmark-follow' ) ), 'manage_options', 'penci-bf-send-email', array(
			$this,
			'penci_bl_send_email_page'
		) );

		// Add Followers
		$add_followers_page = add_submenu_page( 'penci-bf-post', esc_html__( 'Penci Bookmark & Follow - Add Followers', 'penci-bookmark-follow' ), esc_html__( 'Add Followers', 'penci-bookmark-follow' ), 'manage_options', 'penci-bf-add-follower', array(
			$this,
			'penci_bl_add_follower_page'
		) );

		add_action( "admin_head-$send_email_page", array( $this->scripts, 'penci_bl_send_email_page_load_scripts' ) );

		add_action( "admin_head-$add_followers_page", array(
			$this->scripts,
			'penci_bl_send_email_page_load_scripts'
		) );
	}

	/**
	 * Admin Options Page
	 *
	 * Handles to send emails to followers
	 *
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	function penci_bl_send_email_page() {

		//admin options page
		include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-send-email.php' );
	}

	/**
	 * Add follower page
	 *
	 * Handles to add followers from backend
	 *
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	function penci_bl_add_follower_page() {

		// Add followers page
		include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-add-follower.php' );
	}

	/**
	 * List Users Page
	 *
	 * List of all following user
	 * display
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	function penci_bl_list_users() {

		if ( isset( $_GET['postid'] ) && isset( $_GET['logid'] ) ) {

			//display following post user logs list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-logs-list.php' );

		} else if ( isset( $_GET['postid'] ) ) {

			//display following post users list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-list.php' );

		} else {

			//display following posts list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-list.php' );

		}
	}

	/**
	 * List Terms Page
	 *
	 * List of all following terms
	 * display
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	function penci_bl_list_terms() {

		if ( isset( $_GET['termid'] ) && isset( $_GET['taxonomy'] ) && isset( $_GET['logid'] ) ) {

			//display following term user logs list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-logs-list-terms.php' );

		} else if ( isset( $_GET['termid'] ) && isset( $_GET['taxonomy'] ) ) {

			//display following term users list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-list-terms.php' );

		} else {

			//display following terms list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-list-terms.php' );

		}
	}

	/**
	 * List author Page
	 *
	 * List of all following authors
	 * display
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	function penci_bl_list_authors() {

		if ( isset( $_GET['authorid'] ) && isset( $_GET['logid'] ) ) {

			//display following author user logs list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-logs-list-authors.php' );

		} else if ( isset( $_GET['authorid'] ) ) {

			//display following author users list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-users-list-authors.php' );

		} else {
			//display following authors list page
			include_once( PENCI_BL_ADMIN_DIR . '/forms/penci-bf-list-authors.php' );

		}
	}

	/**
	 * Register Settings
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 *
	 */
	public function penci_bl_admin_register_settings() {

		register_setting( 'penci_bl_plugin_options', 'penci_bl_options', array( $this, 'penci_bl_validate_options' ) );
	}

	/**
	 * Validate Settings Options
	 *
	 * Handle settings page values
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_validate_options( $input ) {

		global $penci_bl_options;

		// sanitize text input (strip html tags, and escape characters)
		$input['follow_buttons']                    = $this->model->penci_bl_escape_slashes_deep( $input['follow_buttons'] );
		$input['follow_buttons']['follow']          = isset( $input['follow_buttons']['follow'] ) && ! empty( $input['follow_buttons']['follow'] ) && trim( $input['follow_buttons']['follow'] != '' ) ? $input['follow_buttons']['follow'] : esc_html__( 'Follow', 'penci-bookmark-follow' );
		$input['follow_buttons']['following']       = isset( $input['follow_buttons']['following'] ) && ! empty( $input['follow_buttons']['following'] ) && trim( $input['follow_buttons']['following'] != '' ) ? $input['follow_buttons']['following'] : esc_html__( 'Following', 'penci-bookmark-follow' );
		$input['follow_buttons']['unfollow']        = isset( $input['follow_buttons']['unfollow'] ) && ! empty( $input['follow_buttons']['unfollow'] ) && trim( $input['follow_buttons']['unfollow'] != '' ) ? $input['follow_buttons']['unfollow'] : esc_html__( 'Unfollow', 'penci-bookmark-follow' );
		$input['follow_message']                    = $this->model->penci_bl_escape_slashes_deep( $input['follow_message'] );
		$input['term_follow_buttons']               = $this->model->penci_bl_escape_slashes_deep( $input['term_follow_buttons'] );
		$input['term_follow_buttons']['follow']     = isset( $input['term_follow_buttons']['follow'] ) && ! empty( $input['term_follow_buttons']['follow'] ) && trim( $input['term_follow_buttons']['follow'] != '' ) ? $input['term_follow_buttons']['follow'] : esc_html__( 'Follow {term_name}', 'penci-bookmark-follow' );
		$input['term_follow_buttons']['following']  = isset( $input['term_follow_buttons']['following'] ) && ! empty( $input['term_follow_buttons']['following'] ) && trim( $input['term_follow_buttons']['following'] != '' ) ? $input['term_follow_buttons']['following'] : esc_html__( 'Following {term_name}', 'penci-bookmark-follow' );
		$input['term_follow_buttons']['unfollow']   = isset( $input['term_follow_buttons']['unfollow'] ) && ! empty( $input['term_follow_buttons']['unfollow'] ) && trim( $input['term_follow_buttons']['unfollow'] != '' ) ? $input['term_follow_buttons']['unfollow'] : esc_html__( 'Unfollow {term_name}', 'penci-bookmark-follow' );
		$input['term_follow_message']               = $this->model->penci_bl_escape_slashes_deep( $input['term_follow_message'] );
		$input['recipient_per_email']               = $this->model->penci_bl_escape_slashes_deep( $input['recipient_per_email'], true );
		$input['from_email']                        = $this->model->penci_bl_escape_slashes_deep( $input['from_email'], true );
		$input['unsubscribe_message']               = $this->model->penci_bl_escape_slashes_deep( $input['unsubscribe_message'], true );
		$input['email_subject']                     = $this->model->penci_bl_escape_slashes_deep( $input['email_subject'] );
		$input['email_body']                        = $this->model->penci_bl_escape_slashes_deep( $input['email_body'], true );
		$input['term_email_subject']                = $this->model->penci_bl_escape_slashes_deep( $input['term_email_subject'] );
		$input['term_email_body']                   = $this->model->penci_bl_escape_slashes_deep( $input['term_email_body'], true );
		$input['comment_email_subject']             = $this->model->penci_bl_escape_slashes_deep( $input['comment_email_subject'] );
		$input['comment_email_body']                = $this->model->penci_bl_escape_slashes_deep( $input['comment_email_body'], true );
		$input['confirm_email_subject']             = $this->model->penci_bl_escape_slashes_deep( $input['confirm_email_subject'] );
		$input['confirm_email_body']                = $this->model->penci_bl_escape_slashes_deep( $input['confirm_email_body'], true );
		$input['term_confirm_email_subject']        = $this->model->penci_bl_escape_slashes_deep( $input['term_confirm_email_subject'] );
		$input['term_confirm_email_body']           = $this->model->penci_bl_escape_slashes_deep( $input['term_confirm_email_body'], true );
		$input['unsubscribe_confirm_email_subject'] = $this->model->penci_bl_escape_slashes_deep( $input['unsubscribe_confirm_email_subject'] );
		$input['unsubscribe_confirm_email_body']    = $this->model->penci_bl_escape_slashes_deep( $input['unsubscribe_confirm_email_body'], true );
		$input['double_opt_in_guest']               = isset( $input['double_opt_in_guest'] ) && ! empty( $input['double_opt_in_guest'] ) ? '1' : '';
		$input['custom_css']                        = ! empty( $input['custom_css'] ) ? $this->model->penci_bl_escape_slashes_deep( $input['custom_css'] ) : '';

		//set session to set tab selected in settings page
		$selectedtab = isset( $input['selected_tab'] ) ? $input['selected_tab'] : '';
		set_transient( get_current_user_id() . 'penci_bl_selected_tab', strtolower( $selectedtab ) );

		// apply filters for validate settings
		$input = apply_filters( 'penci_bl_validate_settings', $input, $penci_bl_options );

		//filter to save all settings to database
		return $input;
	}

	/**
	 * Bulk actions
	 *
	 * Handles bulk action functinalities
	 * for follow post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_process_bulk_actions() {

		// Code for followed post
		if ( ( isset( $_GET['action'] ) || isset( $_GET['action2'] ) )
		     && ( isset( $_GET['page'] ) && $_GET['page'] == 'penci-bf-post' )
		     && ( isset( $_GET['post'] ) || isset( $_GET['user'] ) || isset( $_GET['userlog'] ) ) ) {

			// check if we get user OR get userlogs 
			if ( isset( $_GET['post'] ) ) {
				$action_on_id = $_GET['post'];
			} else if ( isset( $_GET['user'] ) ) {
				$action_on_id = $_GET['user'];
			} else {
				$action_on_id = $_GET['userlog'];
			}

			// check if we dont get array of IDs
			if ( ! is_array( $action_on_id ) ) {
				$action_on_id = array( $action_on_id );
			}

			// redirect string for userlist page
			$newstr = add_query_arg( array(
					'userlog' => false,
					'post'    => false,
					'user'    => false,
					'action'  => false,
					'action2' => false
				)
			);

			//if there is multiple checkboxes are checked then call delete in loop
			foreach ( $action_on_id as $id ) {

				//parameters for delete function
				$args = array( 'id' => $id );

				if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) ||
				     ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) ) {

					if ( isset( $_GET['post'] ) ) {
						$args['parent_id'] = $_GET['post'];
						//delete record from database
						$this->model->penci_bl_bulk_follow_post_delete( $args );
					} else {
						//delete record from database
						$this->model->penci_bl_bulk_delete( $args );
					}
					$newstr = add_query_arg( array( 'message' => '3' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'subscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'subscribe' ) ) {

					//subscribe users
					$this->model->penci_bl_bulk_subscribe( $args );

					$newstr = add_query_arg( array( 'message' => '1' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'unsubscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'unsubscribe' ) ) {

					//unsubscribe users
					$this->model->penci_bl_bulk_unsubscribe( $args );

					$newstr = add_query_arg( array( 'message' => '2' ), $newstr );
				}
			}

			wp_redirect( $newstr );
			exit;
		}

		// Code for followed term
		if ( ( isset( $_GET['action'] ) || isset( $_GET['action2'] ) )
		     && ( isset( $_GET['page'] ) && $_GET['page'] == 'penci-bf-term' )
		     && ( isset( $_GET['term'] ) || isset( $_GET['user'] ) || isset( $_GET['userlog'] ) ) ) {

			// check if we get user OR get userlogs 
			if ( isset( $_GET['term'] ) ) {
				$action_on_id = $_GET['term'];
			} else if ( isset( $_GET['user'] ) ) {
				$action_on_id = $_GET['user'];
			} else {
				$action_on_id = $_GET['userlog'];
			}

			// check if we dont get array of IDs
			if ( ! is_array( $action_on_id ) ) {
				$action_on_id = array( $action_on_id );
			}

			// redirect string for userlist page
			$newstr = add_query_arg( array(
					'userlog' => false,
					'term'    => false,
					'user'    => false,
					'action'  => false,
					'action2' => false
				)
			);

			//if there is multiple checkboxes are checked then call delete in loop
			foreach ( $action_on_id as $id ) {

				//parameters for delete function
				$args = array( 'id' => $id );

				if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) ||
				     ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) ) {

					$this->model->penci_bl_bulk_delete( $args );
					$newstr = add_query_arg( array( 'message' => '3' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'subscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'subscribe' ) ) {

					//subscribe users
					$this->model->penci_bl_bulk_subscribe( $args );

					$newstr = add_query_arg( array( 'message' => '1' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'unsubscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'unsubscribe' ) ) {

					//unsubscribe users
					$this->model->penci_bl_bulk_unsubscribe( $args );

					$newstr = add_query_arg( array( 'message' => '2' ), $newstr );
				}
			}

			wp_redirect( $newstr );
			exit;
		}

		// Code for followed author
		if ( ( isset( $_GET['action'] ) || isset( $_GET['action2'] ) )
		     && ( isset( $_GET['page'] ) && $_GET['page'] == 'penci-bf-author' )
		     && ( isset( $_GET['author'] ) || isset( $_GET['user'] ) || isset( $_GET['userlog'] ) ) ) {

			// check if we get user OR get userlogs 
			if ( isset( $_GET['author'] ) ) {
				$action_on_id = $_GET['author'];
			} else if ( isset( $_GET['user'] ) ) {
				$action_on_id = $_GET['user'];
			} else {
				$action_on_id = $_GET['userlog'];
			}

			// check if we dont get array of IDs
			if ( ! is_array( $action_on_id ) ) {
				$action_on_id = array( $action_on_id );
			}

			// redirect string for userlist page
			$newstr = add_query_arg( array(
					'userlog' => false,
					'author'  => false,
					'user'    => false,
					'action'  => false,
					'action2' => false
				)
			);

			//if there is multiple checkboxes are checked then call delete in loop
			foreach ( $action_on_id as $id ) {

				//parameters for delete function
				$args = array( 'id' => $id );

				if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) ||
				     ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) ) {

					if ( isset( $_GET['author'] ) ) {
						$args['authorid'] = $_GET['author'];
						//delete record from database
						$this->model->penci_bl_bulk_follow_author_delete( $args );
					} else {
						//delete record from database
						$this->model->penci_bl_bulk_delete( $args );
					}
					$newstr = add_query_arg( array( 'message' => '3' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'subscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'subscribe' ) ) {

					//subscribe users
					$this->model->penci_bl_bulk_subscribe( $args );

					$newstr = add_query_arg( array( 'message' => '1' ), $newstr );

				} else if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'unsubscribe' ) ||
				            ( isset( $_GET['action2'] ) && $_GET['action2'] == 'unsubscribe' ) ) {

					//unsubscribe users
					$this->model->penci_bl_bulk_unsubscribe( $args );

					$newstr = add_query_arg( array( 'message' => '2' ), $newstr );
				}
			}

			wp_redirect( $newstr );
			exit;
		}

	}

	/**
	 * Save Post
	 *
	 * Handle to check post after save post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_save_post( $post_id, $post ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		/**
		 * For Elementor plugin compatibility.
		 *
		 * Added checkbox "Notify followers for this update" in frontend editor
		 */
		if ( defined( 'ELEMENTOR_VERSION' ) && isset( $_POST['actions'] ) ) {

			static $avoid_duplicate_emails = 1;
			// As elementor call ajax multiple times, so prevent more then 1 email notifications
			if ( $avoid_duplicate_emails > 1 ) {
				return;
			}
			$avoid_duplicate_emails ++;

			$elementor_data    = json_decode( stripslashes( $_POST['actions'] ), true );
			$post_notificaiton = isset( $elementor_data['save_builder']['data']['settings'][ $prefix . 'email_notification' ] ) && $elementor_data['save_builder']['data']['settings'][ $prefix . 'email_notification' ] ? $elementor_data['save_builder']['data']['settings'][ $prefix . 'email_notification' ] : null;
			if ( $post_notificaiton == "yes" ) {
				$_POST[ $prefix . 'email_notification' ] = 1;
			}
		}


		$post_type_object = get_post_type_object( $post->post_type );

		//If post type is followlog then return auto posting
		if ( $post->post_type == PENCI_BL_LOGS_POST_TYPE ) {
			return $post_id;
		}

		//Check to enable frontend submissioon
		$check_support_fes = $this->penci_bl_check_support_fes( $post_id, $post );

		if ( ! $check_support_fes ) { //If enabled then not to check backend code

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) // Check Autosave
			     || ( $post_type_object->cap && ! is_null( $post_type_object->cap->edit_post ) && ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) // Check permission
			     || ( $post->post_status != 'publish' ) ) {
				return $post_id;
			}
		}

		do_action( 'penci_bl_before_send_email_on_save_post', $this );

		// Get post published meta
		$post_published = get_post_meta( $post_id, $prefix . 'post_published', true );

		if ( ( empty( $post_published ) )
		     || ( isset( $_POST[ $prefix . 'email_notification' ] ) && $_POST[ $prefix . 'email_notification' ] == '1' ) ) {

			// check send email notification is enabled for $post_id
			if ( ! $this->model->penci_bl_check_send_email_notifications( $post_id ) ) {
				return false;
			}

			// apply filters for verify send email after post create/update
			$has_send_email = apply_filters( 'penci_bl_verify_send_email', true, $post_id, $penci_bl_options, $_POST );

			if ( $has_send_email ) { // Verified for send email

				// Check first time publish
				// Check disable email notification is checked
				if ( empty( $post_published ) ) {

					// if data changed then send email and create term log
					$success_mail        = $this->model->penci_bl_term_create_logs( $post_id );
					$success_author_mail = $this->model->penci_bl_author_create_logs( $post_id );
					if ( $success_mail || $success_author_mail ) {

						//redirect to custom url after saving post
						add_filter( 'redirect_post_location', array( $this, 'penci_bl_redirect_save_post' ) );
						update_post_meta( $post_id, $prefix . 'post_published', '1' );
					}
				}

				// Check email notification from publish meta box
				if ( isset( $_POST[ $prefix . 'email_notification' ] ) && $_POST[ $prefix . 'email_notification' ] == '1' ) {

					// if data changed then send email and create log
					$success_mail = $this->model->penci_bl_create_logs( $post_id );
					if ( $success_mail ) {

						//redirect to custom url after saving post
						add_filter( 'redirect_post_location', array( $this, 'penci_bl_redirect_save_post' ) );
					}
				}
			}
		}
	}

	/**
	 * If elementor plugin is activated, then unset email_notification saved meta.
	 * Elementor by default save this meta and we don't want to save.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.9.9
	 */
	public function penci_bl_save_post_for_elementor( $post_id, $post ) {

		// Check if elementor is activated
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$elementor_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
			$prefix             = PENCI_BL_META_PREFIX;
			if ( isset( $elementor_settings[ $prefix . 'email_notification' ] ) ) {
				unset( $elementor_settings[ $prefix . 'email_notification' ] );
				update_post_meta( $post_id, '_elementor_page_settings', $elementor_settings );
			}
		}
	}

	/**
	 * Check To Enable Frontend Submission
	 *
	 * Handle to check frontend submission
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.0
	 */
	public function penci_bl_check_support_fes( $post_id, $post ) {

		if ( class_exists( 'EDD_Front_End_Submissions' ) ) { //if edd frontend submission plugin is activated

			//Do code for edd frontend submission plugin
			$action           = isset( $_POST['action'] ) ? $_POST['action'] : false;
			$approve_download = isset( $_REQUEST['approve_download'] ) ? $_REQUEST['approve_download'] : false;
			if ( ( $action == 'fes_submit_post' || ! empty( $approve_download ) ) && ( $post->post_status == 'publish' ) ) {
				return true;
			}
		}

		return apply_filters( 'penci_bl_check_support_fes', false );
	}

	/**
	 * Add perameter in url after save post
	 *
	 * Handle to add perameter in url after save post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_redirect_save_post( $loc ) {

		return add_query_arg( 'penci-bf-successmail', '1', $loc );

	}

	/**
	 * Display Success Message
	 *
	 * Handle to display success message for followers email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_admin_notices() {

		if ( ! isset( $_GET['penci-bf-successmail'] ) ) {
			return false;
		}

		if ( isset( $_GET['penci-bf-successmail'] ) && ! empty( $_GET['penci-bf-successmail'] ) ) {

			echo '<div class="updated"><p>' . esc_html__( 'Email successfully sent to all followers.', 'penci-bookmark-follow' ) . '</p></div>';
		}

	}

	/**
	 * Get all terms by taxonomy
	 *
	 * Handle to get all terms by taxonomy
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_terms() {

		$html = '';
		if ( isset( $_POST['posttype'] ) && ! empty( $_POST['posttype'] )
		     && isset( $_POST['taxonomy'] ) && ! empty( $_POST['taxonomy'] ) ) {

			$posttype = $_POST['posttype'];
			$taxonomy = $_POST['taxonomy'];

			$catargs    = array(
				'type'       => $posttype,
				'taxonomy'   => $taxonomy,
				'order'      => 'DESC',
				'hide_empty' => '0',
			);
			$categories = get_categories( $catargs );
			$html       .= '<option value="">-- Select --</option>';
			foreach ( $categories as $cat ) {
				$html .= '<option value="' . $cat->term_id . '">' . $cat->name . '</option>';
			}
		}
		echo $html;
		exit;
	}

	/**
	 * Get all post name for send email
	 *
	 * Handle to get all post name
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.0
	 */
	public function penci_bl_post_name() {

		$args = array();
		$html = '';
		if ( isset( $_POST['posttype'] ) && ! empty( $_POST['posttype'] ) ) {

			$posttype = $_POST['posttype'];

			$args['post_type'] = $posttype;
			$data              = $this->model->penci_bl_get_follow_post_data( $args );

			foreach ( $data as $key => $value ) {

				$html .= '<option value="' . $data[ $key ]['ID'] . '">' . $data[ $key ]['post_title'] . '</option>';

			}
		}
		echo $html;
		exit;
	}

	/**
	 * Delete all follow post when delete main post / page
	 *
	 * Handle to delete all follow post when delete main post / page
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_delete_main_post( $pid ) {

		$args = array(
			'parent_id' => array( $pid )
		);
		$this->model->penci_bl_bulk_follow_post_delete( $args );

		return true;
	}

	/**
	 * Add Enable Email Notification Meta in publish meta box
	 *
	 * Handle to add meta in publish box
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_publish_meta() {

		global $post, $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		if ( isset( $post->post_status ) && $post->post_status == 'publish' && $this->model->penci_bl_check_post_update_notification() ) {

			//Post Update notify field
			$update_notify_field = apply_filters( 'penci_bl_post_update_email_notify_field', array(
				'title'   => esc_html__( 'Notify followers for this update:', 'penci-bookmark-follow' ),
				'default' => isset( $penci_bl_options['enable_notify_followers'] ) ? true : false,
			), $post );

			echo '<div class="misc-pub-section misc-pub-section-last">
			         <span id="timestamp">
			         	<label for="penci_bl_enable_email_notify">' . $update_notify_field['title'] . '<input type="checkbox" id="penci_bl_enable_email_notify" value="1" name="' . $prefix . 'email_notification" ' . checked( $update_notify_field['default'], true, false ) . '/></label>
			         </span>
		         </div>';

			do_action( 'penci_bl_after_notify_followers', $post );

		}
	}

	/**
	 * Add Enable Email Notification Meta in publish meta box
	 *
	 * Handle to add meta in publish box
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_notification_meta_box( $post_type, $post ) {

		$prefix = PENCI_BL_META_PREFIX;

		if ( function_exists( 'get_current_screen' ) ) {

			$current_screen = get_current_screen();

			if ( isset( $current_screen->is_block_editor ) && $current_screen->is_block_editor == '1' && isset( $post->post_status ) && $post->post_status == 'publish' && $this->model->penci_bl_check_post_update_notification() ) {

				$pages = get_post_types( array( 'public' => true ), 'names' );

				foreach ( $pages as $page ) {

					//don't add metabox to media post type
					if ( $page == 'attachment' ) {
						continue;
					}

					//add metabox
					add_meta_box(
						'penci_bl_notification_meta',
						esc_html__( 'Penci Bookmark & Follow - Notification' ),
						array( $this, 'penci_bl_publish_meta' ),
						$page,
						'side',
						'high'
					);
				}
			}
		}
	}


	/**
	 * Change template design for default email template
	 *
	 * Handles to change template design for default email template
	 *
	 * @param email
	 * @param email get follower email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 *
	 * Added @since 1.7.6
	 */
	public function penci_bl_email_template_default( $html, $message, $unsubscribe = false, $email = '', $unsubscribedata = array() ) {

		ob_start();
		do_action( 'penci_bl_default_email_template', $message, $unsubscribe, $email, $unsubscribedata );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Change template design for plain email template
	 *
	 * Handles to change template design for plain email template
	 *
	 * @param email
	 * @param email get follower email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 *
	 * Added @since 1.7.6
	 */
	public function penci_bl_email_template_plain( $html, $message, $unsubscribe = false, $email = '', $unsubscribedata = array() ) {

		global $penci_bl_options;

		// site name with url 
		$site_name = get_bloginfo( 'name' );

		// Check Append Unsubscribe URL is enable & unsubscribe page is exist & unsubscribe message is not empty
		if ( $unsubscribe && isset( $penci_bl_options['enable_unsubscribe_url'] ) && $penci_bl_options['enable_unsubscribe_url'] == '1'
		     && isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] )
		     && isset( $penci_bl_options['unsubscribe_message'] ) && ! empty( $penci_bl_options['unsubscribe_message'] ) ) {

			$unsubscribe_message       = $penci_bl_options['unsubscribe_message']; // Unsubscribe Message
			$is_individual_unsubscribe = ! empty( $penci_bl_options['is_individual_unsubscribe'] ) ? $penci_bl_options['is_individual_unsubscribe'] : 0; // Get option whether to send unsubcscribe mail for single post, term, author or multiple

			// Check disable unsubscribe confirmation option is checked
			if ( isset( $penci_bl_options['unsubscribe_confirmation'] ) && ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) {

				$url = get_permalink( $penci_bl_options['unsubscribe_page'] );
				// Generate query param
				$url = add_query_arg( array(
					'penci_bl_action' => base64_encode( 'unsubscribe' ),
					'penci_bl_email'  => base64_encode( rawurlencode( $email ) )
				), $url );

				// add query param to unsubscription url for get what to unsubscibe and for what id
				if ( ! empty( $unsubscribedata ) && ! empty( $is_individual_unsubscribe ) && $is_individual_unsubscribe == 1 ) {
					$url = add_query_arg( array(
						'type' => base64_encode( $unsubscribedata['type'] ),
						'id'   => $unsubscribedata['id']
					), $url );
				}

			} else {
				$url = get_permalink( $penci_bl_options['unsubscribe_page'] );
			}
			$unsubscribe_url = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Unsubscribe', 'penci-bookmark-follow' ) . '</a>';

			$unsubscribe_message = str_replace( '{unsubscribe_url}', $unsubscribe_url, $unsubscribe_message );

			$unsubscribe_message = "\n\r" . "\n\r" . $unsubscribe_message;

			$message .= nl2br( $unsubscribe_message );

		}

		$html .= $message;

		return $html;
	}

	/**
	 * Search for Authors and return json
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_search_authors() {

		header( 'Content-Type: application/json; charset=utf-8' );

		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( empty( $term ) ) {
			die();
		}

		$authors_query = new WP_User_Query( array(
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) );

		$authors = $authors_query->get_results();

		if ( $authors ) {
			foreach ( $authors as $author ) {
				$found_authors[ $author->ID ] = $author->display_name . ' (#' . $author->ID . ' &ndash; ' . sanitize_email( $author->user_email ) . ')';
			}
		}

		echo json_encode( $found_authors );
		die();
	}

	public function penci_bl_load_more_notification() {

		$postType = $_POST['posttype'];
		$paged    = $_POST['page'];
		global $penci_bl_options;
		// echo "<pre>";
		// print_r($postType);die();
		//$paged  = $_POST['page'];
		$args     = array(
			'post_type'      => $postType,
			'status'         => 'published',
			'paged'          => $paged,
			'posts_per_page' => 10,
			'post__not_in'   => $penci_bl_options[ 'notification_item_' . $postType ]
		);
		$wp_query = null;
		$wp_query = new WP_Query;
		$wp_query->query( $args );


		if ( $wp_query->have_posts() ) {

			while ( $wp_query->have_posts() ) {

				$wp_query->the_post();

				$checked   = ( in_array( $wp_query->post->ID, $penci_bl_options[ 'notification_item_' . $postType . '' ] ) ) ? 'checked="checked"' : '';
				$posttitle = ! empty( $wp_query->post->post_title ) ? $wp_query->post->post_title : esc_html__( '(no title)', 'penci-bookmark-follow' );
				if ( strlen( $posttitle ) > 50 ) {
					$posttitle = substr( $posttitle, 0, 50 );
					$posttitle = $posttitle . '...';
				} else {
					$posttitle = $posttitle;
				}


				echo '<li>
					<input type="checkbox" id="penci_bl_notification_' . $wp_query->post->ID . '" name="penci_bl_options[notification_item_' . $postType . '][]" value="' . $wp_query->post->ID . '" ' . $checked . '/>																	
					<label for="penci_bl_notification' . $wp_query->post->ID . '">' . $posttitle . '</label>
				</li>';

			}
		}
		wp_reset_query();
		die();
	}

	/**
	 * AJAX Call
	 *
	 * Handles to ajax call to post load more show
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 */
	public function penci_bl_load_more() {
		$postType = $_REQUEST['postType'];
		$paged    = $_POST['page'];

		global $penci_bl_options;


		$args = array(
			'post_type'      => $postType,
			'status'         => 'published',
			'paged'          => $paged,
			'posts_per_page' => 10,
			'post__not_in'   => $penci_bl_options[ 'prevent_item_' . $postType ]
		);

		$wp_query = null;
		$wp_query = new WP_Query;
		$wp_query->query( $args );


		if ( $wp_query->have_posts() ) {

			while ( $wp_query->have_posts() ) {

				$wp_query->the_post();

				$checked = ""; //( in_array( $wp_query->post->ID, $penci_bl_options['prevent_item_'.$postType] ) ) ? 'checked="checked"' : '';				

				$posttitle = ! empty( $wp_query->post->post_title ) ? $wp_query->post->post_title : esc_html__( '(no title)', 'penci-bookmark-follow' );

				if ( strlen( $posttitle ) > 50 ) {
					$posttitle = substr( $posttitle, 0, 50 );
					$posttitle = $posttitle . '...';
				} else {
					$posttitle = $posttitle;
				}

				echo '<li><input type="checkbox" id="penci_bl_prevent_' . $wp_query->post->ID . '" name="penci_bl_options[prevent_item_' . $postType . '][]" value="' . $wp_query->post->ID . '" ' . $checked . ' /><label for="penci_bl_prevent_' . $wp_query->post->ID . '">' . $posttitle . '</label></li>';

			}

		}

		wp_reset_query();
		wp_die();
	}

	/**
	 * AJAX Call
	 *
	 * Handles to ajax call to store social count to the database
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 */
	public function penci_bl_send_test_email() {

		global $penci_bl_options, $current_user;

		$email_template = isset( $_POST['template'] ) && ! empty( $_POST['template'] ) ? $_POST['template'] : 'default';
		$email          = isset( $current_user->user_email ) ? $current_user->user_email : get_option( 'admin_email' );

		//get user email template value from settings page
		$subject = isset( $penci_bl_options['email_subject'] ) ? $penci_bl_options['email_subject'] : '';

		//get user email template value from settings page
		$message = isset( $penci_bl_options['email_body'] ) ? $penci_bl_options['email_body'] : '';

		// replace email shortcodes with content
		$subject = $this->model->penci_bl_replace_shortcodes( '1', $subject );

		// replace email shortcodes with content
		$message = $this->model->penci_bl_replace_shortcodes( '1', $message );

		$this->model->penci_bl_send_email( $email, $subject, $message, '', $email_template );

		echo 'success';
		exit;
	}

	/**
	 * call function to send an email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.0
	 */
	public function penci_bl_admin_send_email() {

		$prefix = PENCI_BL_META_PREFIX;

		if ( isset( $_POST['penci_bl_send_email_submit'] ) && ! empty( $_POST['followed_type'] ) ) {// check if not empty followed_type

			$followed_type           = $_POST['followed_type'];
			$followed_type_post_name = isset( $_POST['followed_type_post_name'] ) ? $_POST['followed_type_post_name'] : '';
			$followed_type_terms     = isset( $_POST['followed_type_terms'] ) ? $_POST['followed_type_terms'] : '';
			$penci_bl_term_id        = isset( $_POST['penci_bl_term_id'] ) ? $_POST['penci_bl_term_id'] : '';
			$followed_type_author    = isset( $_POST['followed_type_author'] ) ? $_POST['followed_type_author'] : '';
			$email_subject           = isset( $_POST['followed_email_subject'] ) ? $_POST['followed_email_subject'] : '';
			$email_body              = isset( $_POST['followed_email_body'] ) ? $_POST['followed_email_body'] : '';
			$email_subject           = $this->model->penci_bl_escape_slashes_deep( $email_subject );
			$email_body              = $this->model->penci_bl_escape_slashes_deep( $email_body, true, true );// limited html allowd

			$args            = array();
			$followers_count = '';
			if ( $followed_type == "followed_post" ) {//check followed type is post

				$args['postid']          = $followed_type_post_name;
				$args['penci_bl_status'] = 'subscribe';

				$data            = $this->model->penci_bl_get_follow_post_users_data( $args );
				$followers_count = count( $data );

				foreach ( $data as $key => $value ) {

					$user_email = get_post_meta( $value['ID'], $prefix . 'post_user_email', true );

					if ( ! empty( $value['post_author'] ) ) {
						$user_email = $this->model->penci_bl_get_user_email_from_id( $value['post_author'] );
					}

					$sentemail = $this->model->penci_bl_send_email( $user_email, $email_subject, $email_body );

				}
			} elseif ( $followed_type == "followed_authors" ) {//check followed type is authors

				$args['authorid']        = $followed_type_author;
				$args['penci_bl_status'] = 'subscribe';
				$data                    = $this->model->penci_bl_get_follow_author_users_data( $args );

				$followers_count = count( $data );

				foreach ( $data as $key => $value ) {

					$user_email = get_post_meta( $value['ID'], $prefix . 'author_user_email', true );

					if ( ! empty( $value['post_author'] ) ) {
						$user_email = $this->model->penci_bl_get_user_email_from_id( $value['post_author'] );
					}

					$sentemail = $this->model->penci_bl_send_email( $user_email, $email_subject, $email_body );
				}
			}

			// add action after send instant email notfication since 1.8.6
			do_action( 'penci_bl_after_send_instant_mail_notification', $data );

			if ( ! empty( $sentemail ) ) {

				//set session to set message for sent email
				set_transient( get_current_user_id() . '_penci_bl_sent_mail_message',
					sprintf( esc_html__( 'Mail sent successfully to %s followers.', 'penci-bookmark-follow' ), $followers_count ) );

				wp_redirect( add_query_arg( array( 'penci-bf-sent-notification' => '1' ) ) );
				exit;
			}
		}
	}

	/**
	 * function to change wp query when there is post author is guest
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.1
	 */
	public function penci_bl_follow_user_list_posts_where( $where ) {

		global $wpdb;

		if ( ( ( ! empty( $_GET['page'] ) && $_GET['page'] == 'penci-bf-post' && isset( $_GET['postid'] ) ) ||
		       ( ! empty( $_GET['page'] ) && $_GET['page'] == 'penci-bf-term' && isset( $_GET['termid'] ) && isset( $_GET['taxonomy'] ) ) ||
		       ( ! empty( $_GET['page'] ) && $_GET['page'] == 'penci-bf-author' && isset( $_GET['authorid'] ) ) ) &&
		     isset( $_GET['user'] ) && $_GET['user'] == 0 ) {

			$where .= ' AND ' . $wpdb->posts . '.post_author=' . $_GET['user'];
		}

		return $where;
	}

	/**
	 * Validate current author.
	 *
	 * Current author should only able to see his list. If try to access other author list
	 * then redirect to author list page
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.7.6
	 */
	public function penci_bl_author_list_validate_author() {

		global $current_user;

		// Get current user roles
		$user_role = $current_user->roles;

		if ( isset( $_GET['authorid'] ) ) {

			if ( in_array( 'author', $user_role ) && $_GET['authorid'] != get_current_user_id() ) {
				$redirect_url = remove_query_arg( 'authorid' );
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}

	/**
	 * Get all post name
	 *
	 * Handle to get all post name
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.0
	 */
	public function penci_bl_get_posts() {

		$args = array();
		$html = '';
		if ( isset( $_POST['posttype'] ) && ! empty( $_POST['posttype'] ) ) {

			$posttype = $_POST['posttype'];
			$data     = new WP_Query( array(
				'post_type'      => $_POST['posttype'],
				'posts_per_page' => - 1
			) );

			$html .= '<option value="">-- Select --</option>';
			foreach ( $data->posts as $post ) {

				$html .= '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
			}
		}
		echo $html;
		exit;
	}

	/**
	 * Get all user name
	 *
	 * Handle to get all user name
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.0
	 */
	public function penci_bl_get_users() {

		if ( isset( $_POST['post_id'] ) ) {

			$args = array(
				'posts_per_page'          => - 1,
				'postid'                  => $_POST['post_id'],
				'penci_bl_user_list_data' => true,
			);

			$result_data = $this->model->penci_bl_get_follow_post_users_data( $args );
		}

		if ( isset( $_POST['author_id'] ) ) {

			$args = array(
				'posts_per_page'                   => - 1,
				'authorid'                         => $_POST['author_id'],
				'penci_bl_users_list_authors_data' => true,
			);

			$result_data = $this->model->penci_bl_get_follow_author_users_data( $args );
		}

		foreach ( $result_data['data'] as $data ) {

			$users[] = $data['post_author'];
		}

		$args = array(
			'fields' => 'all_with_meta',
		);

		if ( isset( $users ) && $users ) {
			$args['exclude'] = $users;
		}

		$user_list = get_users( $args );

		$html = '';
		foreach ( $user_list as $user ) {

			$html .= '<option value="' . $user->ID . '" data-email="' . $user->user_email . '">' . $user->first_name . ' ' . $user->last_name . '(' . $user->user_email . ')' . '</option>';
		}

		echo $html;
		exit;
	}

	/**
	 * Save follow data
	 *
	 * Handle to save follow data
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.0
	 */
	public function penci_bl_save() {

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'penci_bl_save' ) {

			if ( isset( $_POST['postid'] ) ) {

				$this->public->penci_bl_follow_post();

			} else if ( isset( $_POST['authorid'] ) ) {

				$this->public->penci_bl_follow_author();

			}

		}
		exit;
	}

	/**
	 * Export Posts Followers
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_export_all_posts( $args = array() ) {

		if ( isset( $_GET['export_posts_followers'] ) ) {

			global $penci_bl_model;

			$this->model = $penci_bl_model;

			$prefix = PENCI_BL_META_PREFIX;

			// Taking parameter
			$orderby = isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date';
			$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
			$search  = isset( $_GET['s'] ) ? sanitize_text_field( trim( $_GET['s'] ) ) : null;

			//Check if passed post id
			if ( ! empty( $_GET['postid'] ) ) {
				$followpostslist = array( get_post( $_GET['postid'], 'ARRAY_A' ) );
			} else {

				$args = array(
					'orderby'        => $orderby,
					'order'          => $order,
					'posts_per_page' => - 1
				);

				//in case of search make parameter for retriving search data
				if ( isset( $search ) && ! empty( $search ) ) {
					$args['search'] = $search;
				}

				if ( isset( $_GET['penci_bl_post_type'] ) && ! empty( $_GET['penci_bl_post_type'] ) ) {
					$args['post_type'] = $_GET['penci_bl_post_type'];
				}

				$followpostslist = $this->model->penci_bl_get_follow_post_data( $args );
			}

			if ( ! empty( $followpostslist ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="followed_posts.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				$file = fopen( 'php://output', 'w' );

				fputcsv( $file, array( 'Post Name', 'User Email', 'User Type', 'Subscribed', 'User', 'Post Type' ) );

				foreach ( $followpostslist as $key => $value ) {

					//Arguments for get followers
					$users_args = array( 'postid' => $value['ID'] );

					//Passed other args
					if ( isset( $_REQUEST['penci_bl_status'] ) && ! empty( $_REQUEST['penci_bl_status'] ) ) {
						$users_args['penci_bl_status'] = $_REQUEST['penci_bl_status'];
					}

					$result_data = $this->model->penci_bl_get_follow_post_users_data( $users_args );

					foreach ( $result_data as $user_key => $user_value ) {

						// get user email from meta field
						$user_email = get_post_meta( $user_value['ID'], $prefix . 'post_user_email', true );

						// get user is subscribed or not
						$status = get_post_meta( $user_value['ID'], $prefix . 'follow_status', true );

						$userdata = get_user_by( 'id', $user_value['post_author'] );

						$user_email_html = '';
						$user            = '';
						if ( ! empty( $userdata ) ) {    // to display user display name

							$user_email   = isset( $userdata->user_email ) ? $userdata->user_email : '';
							$display_name = $userdata->display_name;

							if ( ! empty( $user_email ) ) {
								$user = $display_name;
							}
							$user_type = esc_html__( 'Registered User', 'penci-bookmark-follow' );
						} else {
							$user      = esc_html__( 'guest', 'penci-bookmark-follow' );
							$user_type = esc_html__( 'Guest', 'penci-bookmark-follow' );
						}

						$subscribed = $status == '1' ? esc_html__( 'Yes', 'penci-bookmark-follow' ) : esc_html__( 'No', 'penci-bookmark-follow' );

						fputcsv( $file, array(
							$value['post_title'],
							$user_email,
							$user_type,
							$subscribed,
							$user,
							$value['post_type']
						) );
					}
				}

				exit();
			}
		}
	}

	/**
	 * Export Authors Followers
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_export_all_authors( $args = array() ) {

		if ( isset( $_GET['export_authors_followers'] ) ) {

			global $penci_bl_model;

			$this->model = $penci_bl_model;

			$prefix = PENCI_BL_META_PREFIX;

			// Taking parameter
			$orderby = isset( $_GET['orderby'] ) ? urldecode( $_GET['orderby'] ) : 'date';
			$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
			$search  = isset( $_GET['s'] ) ? sanitize_text_field( trim( $_GET['s'] ) ) : null;

			$args = array(
				'orderby'        => $orderby,
				'order'          => $order,
				'posts_per_page' => - 1
			);

			//in case of search make parameter for retriving search data
			if ( isset( $search ) && ! empty( $search ) ) {
				$args['search'] = $search;
			}

			//Check if followers page
			if ( ! empty( $_GET['authorid'] ) ) {
				$args['post_parent'] = $_GET['authorid'];
			}

			//Get follow data
			$followauthorslist = $this->model->penci_bl_get_follow_author_data( $args );

			if ( ! empty( $followauthorslist ) ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="followed_authors.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				$file = fopen( 'php://output', 'w' );

				fputcsv( $file, array( 'Author Name', 'User Email', 'User Type', 'User', 'Subscribed' ) );

				foreach ( $followauthorslist as $key => $value ) {

					//Get arguments for followers
					$users_args = array( 'authorid' => $value['post_parent'] );

					$result_data = $this->model->penci_bl_get_follow_author_users_data( $users_args );

					foreach ( $result_data as $user_key => $user_value ) {

						// get user email from meta field
						$user_email = get_post_meta( $user_value['ID'], $prefix . 'post_user_email', true );

						// get user is subscribed or not
						$status = get_post_meta( $user_value['ID'], $prefix . 'follow_status', true );

						$userdata   = get_user_by( 'id', $user_value['post_author'] );
						$authordata = get_user_by( 'id', $user_value['post_parent'] );

						$user = '';
						if ( ! empty( $userdata ) ) {    // to display user display name

							$user_email   = isset( $userdata->user_email ) ? $userdata->user_email : '';
							$display_name = $userdata->display_name;

							if ( ! empty( $user_email ) ) {
								$user = $display_name;
							}
							$user_type = esc_html__( 'Registered User', 'penci-bookmark-follow' );
						} else {
							$user      = esc_html__( 'guest', 'penci-bookmark-follow' );
							$user_type = esc_html__( 'Guest', 'penci-bookmark-follow' );
						}

						$subscribed = $status == '1' ? esc_html__( 'Yes', 'penci-bookmark-follow' ) : esc_html__( 'No', 'penci-bookmark-follow' );

						fputcsv( $file, array(
							$authordata->display_name,
							$user_email,
							$user_type,
							$user,
							$subscribed
						) );
					}
				}

				exit();
			}
		}
	}

	/**
	 * Delete from following list when delete Author.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_delete_following_authors( $user_id = '' ) {

		if ( ! empty( $user_id ) ) { // check if user id is not empty

			$args = array(
				'authorid' => array( $user_id )
			);

			//delete record from database
			$this->model->penci_bl_bulk_follow_author_delete( $args );
		}
	}

	/**
	 * Adding Hooks
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function add_hooks() {

		//add admin menu pages
		add_action( 'admin_menu', array( $this, 'penci_bl_add_admin_menu' ) );

		//register settings in init
		add_action( 'admin_init', array( $this, 'penci_bl_admin_register_settings' ) );

		//save post action
		add_action( 'save_post', array( $this, 'penci_bl_save_post' ), 10, 2 );

		// For elementor, keep execution last as we need to clear saved meta
		add_action( 'save_post', array( $this, 'penci_bl_save_post_for_elementor' ), 9999, 2 );

		//process bulk subscribe in admin init
		add_action( 'admin_init', array( $this, 'penci_bl_process_bulk_actions' ) );

		//show admin notices
		add_action( 'admin_notices', array( $this, 'penci_bl_admin_notices' ) );

		//AJAX call for post name
		add_action( 'wp_ajax_penci_bl_post_name', array( $this, 'penci_bl_post_name' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_post_name', array( $this, 'penci_bl_post_name' ) );

		//AJAX call for follow category
		add_action( 'wp_ajax_penci_bl_terms', array( $this, 'penci_bl_terms' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_terms', array( $this, 'penci_bl_terms' ) );

		//AJAX call for custom term name
		add_action( 'wp_ajax_penci_bl_custom_terms', array( $this, 'penci_bl_custom_terms' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_custom_terms', array( $this, 'penci_bl_custom_terms' ) );

		// AJAX call to get all posts
		add_action( 'wp_ajax_penci_bl_get_posts', array( $this, 'penci_bl_get_posts' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_get_posts', array( $this, 'penci_bl_get_posts' ) );

		// AJAX call to get all users
		add_action( 'wp_ajax_penci_bl_get_users', array( $this, 'penci_bl_get_users' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_get_users', array( $this, 'penci_bl_get_users' ) );

		// AJAX call to save follow data
		add_action( 'wp_ajax_penci_bl_save', array( $this, 'penci_bl_save' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_save', array( $this, 'penci_bl_save' ) );

		//AJAX call to get all terms
		add_action( 'wp_ajax_penci_bl_get_terms', array( $this, 'penci_bl_terms' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_get_terms', array( $this, 'penci_bl_terms' ) );

		//delete all follow post when delete main post / page
		add_action( 'delete_post', array( $this, 'penci_bl_delete_main_post' ) );

		//add meta in publish box
		add_action( 'post_submitbox_misc_actions', array( $this, 'penci_bl_publish_meta' ) );

		// Add meta in publish box
		add_action( 'add_meta_boxes', array( $this, 'penci_bl_notification_meta_box' ), 10, 2 );

		// add filter to change template design for default email template
		add_filter( 'penci_bl_email_template_default', array( $this, 'penci_bl_email_template_default' ), 10, 5 );

		// add filter to change template design for plain email template
		add_filter( 'penci_bl_email_template_plain', array( $this, 'penci_bl_email_template_plain' ), 10, 5 );

		//ajax call to send test email
		add_action( 'wp_ajax_penci_bl_test_email', array( $this, 'penci_bl_send_test_email' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_test_email', array( $this, 'penci_bl_send_test_email' ) );

		//ajax call to search Authors
		add_action( 'wp_ajax_penci_bl_search_authors', array( $this, 'penci_bl_search_authors' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_search_authors', array( $this, 'penci_bl_search_authors' ) );

		// ajax call to post load more
		add_action( 'wp_ajax_penci_bl_load_more', array( $this, 'penci_bl_load_more' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_load_more', array( $this, 'penci_bl_load_more' ) );

		// ajax call to post load more
		add_action( 'wp_ajax_penci_bl_load_more_notification', array( $this, 'penci_bl_load_more_notification' ) );
		add_action( 'wp_ajax_nopriv_penci_bl_load_more_notification', array(
			$this,
			'penci_bl_load_more_notification'
		) );


		//send email
		add_action( 'init', array( $this, 'penci_bl_admin_send_email' ) );

		// add filter to change query when to get guest user list
		add_filter( 'posts_where', array( $this, 'penci_bl_follow_user_list_posts_where' ), 10 );

		// add action to redirect to author to his page, if trying to access other author page
		add_action( 'admin_init', array( $this, 'penci_bl_author_list_validate_author' ) );

		// Add action to export followers posts/terms/authors
		add_action( 'admin_init', array( $this, 'penci_bl_export_all_posts' ) );
		add_action( 'admin_init', array( $this, 'penci_bl_export_all_authors' ) );

		// Delete following log item when delete user
		add_action( 'delete_user', array( $this, 'penci_bl_delete_following_authors' ) );
	}
}