<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model Class
 *
 * Handles generic plugin functionality.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
class Penci_Bf_Model {

	public function __construct() {

	}

	/**
	 * Escape Tags & Slashes
	 *
	 * Handles escapping the slashes and tags
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */

	public function penci_bl_escape_attr( $data ) {
		return esc_attr( stripslashes( $data ) );
	}

	/**
	 * Strip Slashes From Array
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */

	public function penci_bl_escape_slashes_deep( $data = array(), $flag = false, $limited = false ) {

		if ( $flag != true ) {
			$data = $this->penci_bl_nohtml_kses( $data );
		} else {

			if ( $limited == true ) {
				$data = wp_kses_post( $data );
			}

		}
		$data = stripslashes_deep( $data );

		return $data;
	}

	/**
	 * Strip Html Tags
	 *
	 * It will sanitize text input (strip html tags, and escape characters)
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_nohtml_kses( $data = array() ) {

		if ( is_array( $data ) ) {

			$data = array_map( array( $this, 'penci_bl_nohtml_kses' ), $data );

		} elseif ( is_string( $data ) ) {

			$data = wp_filter_nohtml_kses( $data );
		}

		return $data;
	}

	/**
	 * Get Post data
	 *
	 * Handles to get post data for
	 * followpost post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_post_data( $args = array() ) {

		// Check if post type selected from filter
		if ( isset( $args['post_type'] ) && ! empty( $args['post_type'] ) ) {
			$post_types = array( $args['post_type'] );
		} else {
			// get all custom post types
			$post_types = get_post_types( array( 'public' => true ), 'names' );
		}

		//check if its custom post types created or not
		$checkpostargs = array(
			'post_type'      => PENCI_BL_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => '-1'
		);


		//fire query in to table for retriving data
		$result = new WP_Query( $checkpostargs );

		if ( ! empty( $result->posts ) ) {
			foreach ( $result->posts as $key => $value ) {

				//if custom post type is created for that post, than store its id
				$postids[] = $value->post_parent;
			}
		}

		//if we dont get any id that take it empty
		if ( empty( $postids ) ) {
			$postids[] = 0;
		}

		$followpostargs = array(
			'post_type' => $post_types,
			'post__in'  => $postids,

		);
		// added since 1.8.6
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = ( array ) $user->roles;
			if ( ! in_array( 'administrator', $role ) ) { // if login user is not admin
				$args['author'] = get_current_user_id();
			}
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {
			$followpostargs['s'] = $args['search'];
		}

		$followpostargs = wp_parse_args( $followpostargs, $args );

		//fire query in to table for retriving data
		$result = new WP_Query( $followpostargs );

		//retrived data is in object format so assign that data to array for listing
		$followpostslist = $this->penci_bl_object_to_array( $result->posts );

		// Check if post type counter from filter
		if ( isset( $args['count'] ) && ! empty( $args['count'] ) ) {
			return count( $followpostslist );
		}

		// if get list for data list then return data with data and total array
		if ( isset( $args['penci_bl_list_data'] ) && ! empty( $args['penci_bl_list_data'] ) ) {

			$data_res['data'] = $followpostslist;

			//To get total count of post using "found_posts" and for users "total_users" parameter
			$data_res['total'] = isset( $result->found_posts ) ? $result->found_posts : '';

			return $data_res;
		}


		return $followpostslist;
	}

	/**
	 * Get Post Users data
	 *
	 * Handles to get users data for
	 * post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_post_users_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followpostusersargs = array(
			'post_type'   => PENCI_BL_POST_TYPE,
			'post_status' => 'publish'
		);

		$followpostusersargs = wp_parse_args( $args, $followpostusersargs );


		$followpostusersargs['posts_per_page'] = '-1';

		//if search using post parent
		if ( isset( $args['postid'] ) ) {
			$followpostusersargs['post_parent'] = $args['postid'];
		}

		//if search using user
		if ( isset( $args['author'] ) ) {
			$followpostusersargs['author'] = $args['author'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {
			$metaargs[] = array(
				'key'     => $prefix . 'post_user_email',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		$followpostusersargs['meta_key']   = $prefix . 'follow_status';
		$followpostusersargs['meta_value'] = '1';

		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$metaargs[] = array(
				'key'   => $prefix . 'post_user_email',
				'value' => $args['penci_bl_email']
			);
		}

		if ( ! empty( $metaargs ) ) {
			//$followpostusersargs['meta_query'] = $metaargs;
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followpostusersargs['fields'] = $args['fields'];
		}

		//get order by records
		$followpostusersargs['order']   = 'DESC';
		$followpostusersargs['orderby'] = 'date';

		$guest = false;

		if ( isset( $args['post__in'] ) && ! empty( $args['post__in'] ) ) {
			$followpostusersargs             = [];
			$followpostusersargs['post__in'] = $args['post__in'];
			$guest                           = true;
		}

		//fire query in to table for retriving data
		$result = new WP_Query( $followpostusersargs );

		$followpostuserslist = $this->penci_bl_object_to_array( $result->posts );

		if ( is_array( $followpostuserslist ) && ! $guest ) {

			foreach ( $followpostuserslist as $post_id => $post_data ) {
				if ( isset( $post_data['post_parent'] ) && $post_data['post_parent'] ) {
					if ( empty( get_post( $post_data['post_parent'] ) ) ) {
						unset( $followpostuserslist[ $post_id ] );
					}
				} else {
					unset( $followpostuserslist[ $post_id ] );
				}
			}
		}

		if ( isset( $args['count'] ) && $args['count'] == '1' ) {
			$followpostuserslist = count( $followpostuserslist );
		} else if ( isset( $args['penci_bl_user_list_data'] ) && ! empty( $args['penci_bl_user_list_data'] ) ) {

			$data_res['data'] = $followpostuserslist;

			//To get total count of post using "found_posts" and for users "total_users" parameter
			$data_res['total'] = isset( $result->found_posts ) ? $result->found_posts : '';

			return $data_res;
		}

		if ( is_array( $followpostuserslist ) ) {

			$page  = isset( $args['paged'] ) && $args['paged'] ? $args['paged'] : 1;
			$ppp   = isset( $args['posts_per_page'] ) && $args['posts_per_page'] ? $args['posts_per_page'] : 10;
			$total = count( $followpostuserslist );

			if ( $total > $ppp ) {
				$followpostuserslist = array_slice( $followpostuserslist, ( $page - 1 ) * $ppp, $ppp );
			}
		}


		return $followpostuserslist;
	}

	/**
	 * Get Post User Logs data
	 *
	 * Handles to get user logs data for
	 * post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_post_user_logs_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followeduserlogssargs = array(
			'post_type'      => PENCI_BL_LOGS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => '-1'
		);

		//if search using post parent
		if ( isset( $args['logid'] ) ) {
			$followeduserlogssargs['post_parent'] = $args['logid'];
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followeduserlogssargs['fields'] = $args['fields'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {
			//$followeduserlogssargs['s'] = $args['search'];
			$metaargs[] = array(
				'key'     => $prefix . 'log_email_data',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		if ( ! empty( $metaargs ) ) {
			$followeduserlogssargs['meta_query'] = $metaargs;
		}

		//fire query in to table for retriving data
		$result = new WP_Query( $followeduserlogssargs );

		//retrived data is in object format so assign that data to array for listing
		$followeduserlogslist = $this->penci_bl_object_to_array( $result->posts );

		return $followeduserlogslist;
	}

	/**
	 * Bulk Follow Post Delete Action
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_follow_post_delete( $args = array() ) {

		if ( isset( $args['parent_id'] ) && ! empty( $args['parent_id'] ) ) {
			foreach ( $args['parent_id'] as $parent_id ) {
				$ids = $this->penci_bl_get_follow_post_users_data( array( 'postid' => $parent_id, 'fields' => 'ids' ) );
				foreach ( $ids as $id ) {
					$log_ids = $this->penci_bl_get_follow_post_user_logs_data( array(
						'logid'  => $id,
						'fields' => 'ids'
					) );
					foreach ( $log_ids as $log_id ) {
						wp_delete_post( $log_id, true );
					}
					wp_delete_post( $id, true );
				}
			}
		}
	}

	/**
	 * Bulk Follow Author Delete Action
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_follow_author_delete( $args = array() ) {

		if ( isset( $args['authorid'] ) && ! empty( $args['authorid'] ) ) {
			foreach ( $args['authorid'] as $authorid ) {
				$ids = $this->penci_bl_get_follow_author_users_data( array(
					'authorid' => $authorid,
					'fields'   => 'ids'
				) );
				foreach ( $ids as $id ) {
					$log_ids = $this->penci_bl_get_follow_author_user_logs_data( array(
						'logid'  => $id,
						'fields' => 'ids'
					) );
					foreach ( $log_ids as $log_id ) {
						wp_delete_post( $log_id, true );
					}
					wp_delete_post( $id, true );
				}
			}
		}
	}

	/**
	 * Bulk Follow Term Delete Action
	 *
	 * @package Follow My Blog Post
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_follow_term_delete( $args = array() ) {

		if( isset( $args['termid'] ) && !empty( $args['termid'] ) ) {
			foreach ( $args['termid'] as $termid ) {
				$ids = $this->penci_bl_get_follow_term_users_data( array( 'termid' => $termid, 'fields' => 'ids' ) );
				foreach ( $ids as $id ) {
					$log_ids = $this->penci_bl_get_follow_term_user_logs_data( array( 'logid' => $id, 'fields' => 'ids' ) );
					foreach ( $log_ids as $log_id ) {
						wp_delete_post( $log_id, true );
					}
					wp_delete_post( $id, true );
				}
			}
		}
	}

	/**
	 * Bulk Delete Action
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_delete( $args = array() ) {

		if ( isset( $args['id'] ) && ! empty( $args['id'] ) ) {

			wp_delete_post( $args['id'], true );
		}
	}

	/**
	 * Bulk Subscribe Action
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_subscribe( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		if ( isset( $args['id'] ) && ! empty( $args['id'] ) ) {

			update_post_meta( $args['id'], $prefix . 'follow_status', '1' );
		}
	}

	/**
	 * Bulk Unsubscribe Action
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_bulk_unsubscribe( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		if ( isset( $args['id'] ) && ! empty( $args['id'] ) ) {

			update_post_meta( $args['id'], $prefix . 'follow_status', '0' );
		}
	}

	/**
	 * Convert Object To Array
	 *
	 * Converting Object Type Data To Array Type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */

	public function penci_bl_object_to_array( $result ) {
		$array = array();
		foreach ( $result as $key => $value ) {
			if ( is_object( $value ) ) {
				$array[ $key ] = $this->penci_bl_object_to_array( $value );
			} else {
				$array[ $key ] = $value;
			}
		}

		return $array;
	}

	/**
	 * Check Enable Follow
	 *
	 * Handles to check enable follow me
	 * checkbox check or not for
	 * particular post and post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_check_enable_follow( $post_id, $shortcode = false ) {

		global $penci_bl_options;

		if ( $shortcode ) {
			return true;
		}

		$prefix = PENCI_BL_META_PREFIX;

		$post_type = get_post_type( $post_id );

		// get disable follow value from post meta
		$disable_follow = get_post_meta( $post_id, $prefix . 'disable_follow_me', true );

		// get post ids in which follow me will display
		$selected_posts = isset( $penci_bl_options[ 'prevent_item_' . $post_type ] ) ? $penci_bl_options[ 'prevent_item_' . $post_type ] : array();

		// get post types in which follow me will display
		$selected_post_types = isset( $penci_bl_options['prevent_type'] ) ? $penci_bl_options['prevent_type'] : array();

		// check if not set disable from metabox and set filter on from setting page

		if ( $disable_follow != '1' && ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Shortcode Replace
	 *
	 * Handles to replace entered shortcodes
	 * with corresponding values
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_replace_shortcodes( $post_id, $text ) {

		global $current_user;

		$post_title = $post_content = '';

		// get user details
		$user_info = get_user_by( 'id', $current_user->ID );

		// get user name
		$user_name = isset( $user_info->user_login ) ? $user_info->user_login : '';

		if ( ! empty( $post_id ) ) {

			// get post data using post id
			$post_data = get_post( $post_id );

			$post_title   = isset( $post_data->post_title ) && ! empty( $post_data->post_title ) ? $post_data->post_title : esc_html__( 'Hello world!', 'penci-bookmark-follow' );
			$post_content = isset( $post_data->post_content ) ? strip_shortcodes( $post_data->post_content ) : '';
		}

		// post name
		$post_name = isset( $_POST['post_title'] ) && ! empty( $_POST['post_title'] ) ? $_POST['post_title'] : $post_title;
		$post_name = $this->penci_bl_escape_slashes_deep( $post_name );

		// post description with 260 characters
		$post_description = $this->penci_bl_short_content( $post_content, 260 );

		// post link
		$post_link = '<a href="' . get_permalink( $post_id ) . '" >' . $post_name . '</a>';

		// site name with url
		$site_name = get_bloginfo( 'name' );

		// site name with url
		$site_link = '<a href="' . site_url() . '" >' . $site_name . '</a>';

		// replace of shortcodes
		$text = str_replace( '{post_name}', $post_name, $text );
		$text = str_replace( '{post_description}', $post_description, $text );
		$text = str_replace( '{post_link}', $post_link, $text );
		$text = str_replace( '{site_name}', $site_name, $text );
		$text = str_replace( '{site_link}', $site_link, $text );
		$text = str_replace( '{user_name}', $user_name, $text );

		//return replaced values
		return apply_filters( 'penci_bl_unsubscribe_shortcode', $text, $post_id );
	}

	/**
	 * Send Emails With BCC for follow post
	 *
	 * Handle to send email with bcc for follow post
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 *
	 * When disable unsubscribe confirmation checked send email individually
	 * @since 1.7.6
	 */
	public function penci_bl_post_send_mail_with_bcc( $followers_data, $subject, $message, $post_id = '', $type = '', $comment_data = '' ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$setmail    = false;
		$recipients = '';

		// Check if diable unsubscribe confirmation checked then send email individually
		if ( $penci_bl_options['recipient_per_email'] == 1 || ( isset( $penci_bl_options['unsubscribe_confirmation'] ) &&
		                                                        ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'post_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// unsubscribe config array send post id and type to unsubscription email
				$unsub_data = array(
					'type' => 'post',
					'id'   => $value['ID']
				);

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user                     = get_user_by( 'email', $email );
					$penci_bl_diff_lang_email = array();

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty( $penci_bl_icl_admin_lang ) ) {
						// If user's admin language set then take it as user language
						$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
					} else {
						// If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

					$this->penci_bl_post_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_emailid', $post_id, $unsub_data, $type, $comment_data );
				} else {

					// send email to each user individually
					$setmail = $this->penci_bl_send_email( $email, $subject, $message, '', '', true, $unsub_data );
				}
			}
		} else if ( empty( $penci_bl_options['recipient_per_email'] ) ) {

			$penci_bl_diff_langs = $penci_bl_diff_lang_email = array();

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'post_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {

					if ( ! empty( $email ) ) {

						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
			}
			if ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) { // If our language array is not empty

				$unsub_data = '';
				$this->penci_bl_post_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $type, $comment_data );
			}
		} else {
			// we're using recipient_per_email
			$count = 1;
			$batch = $penci_bl_diff_langs = $penci_bl_diff_lang_email = array();
			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'post_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {
					if ( ! empty( $email ) ) {
						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
				if ( $penci_bl_options['recipient_per_email'] == $count ) {
					$count = 0;
					if ( ! empty( $recipients ) ) {
						$batch[]    = $recipients;
						$recipients = '';
					} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

						$unsub_data = '';
						$this->penci_bl_post_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $type, $comment_data );
						$penci_bl_diff_lang_email = array();
					}
				}
				$count ++;
			}
			// add any partially completed batches to our batch array
			if ( ! empty( $recipients ) ) {
				$batch[] = $recipients;
			} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

				$unsub_data = '';
				$this->penci_bl_post_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $type, $comment_data );
				$penci_bl_diff_lang_email = array();
			}
		}

		// actually send mail
		if ( isset( $batch ) && ! empty( $batch ) ) {
			foreach ( $batch as $recipients ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		} else {
			if ( ! empty( $recipients ) ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		}

		return apply_filters( 'penci_bl_post_send_mail_with_bcc', $setmail, $followers_data );
	}

	/**
	 * Send Emails And Create Logs
	 *
	 * Handle to send email to subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_create_logs( $post_id ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$flag = false;

		$post_type = get_post_type( $post_id );

		// get post ids in which follow me will display
		$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

		// get post types in which follow me will display
		$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

		// check if post have permission to display follow me form & checkbox
		if ( ! ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
			return false;
		}

		// Get Post subject from meta
		$post_email_subject = get_post_meta( $post_id, $prefix . 'email_subject', true );

		// Get Post message from meta
		$post_email_body = get_post_meta( $post_id, $prefix . 'email_body', true );

		if ( isset( $post_email_subject ) && ! empty( $post_email_subject ) ) {
			$subject = $post_email_subject;
		} else {
			$subject = $penci_bl_options['email_subject'];
		}

		if ( isset( $post_email_body ) && ! empty( $post_email_body ) ) {
			$message = $post_email_body;
		} else {
			$message = $penci_bl_options['email_body'];
		}

		// check if post have allow notification from Post / Page Notification Events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['post_trigger_notification']['post_update'] ) && $penci_bl_options['post_trigger_notification']['post_update'] == '1' ) {

			$post_id = apply_filters( 'pencibf_change_send_mail_post_followers', $post_id );

			$flag = $this->penci_bl_post_send_mail( $subject, $message, $post_id, 'post', '' );
		}

		// check if post have allow notification from Category / Tags Notification events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['term_trigger_notification']['post_update'] ) && $penci_bl_options['term_trigger_notification']['post_update'] == '1' ) {

			$flag = $this->penci_bl_all_term_send_mail( $subject, $message, $post_id, 'term_update', '' );
		}

		// check if post have allow notification from author Notification events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['author_trigger_notification']['post_update'] ) && $penci_bl_options['author_trigger_notification']['post_update'] == '1' ) {
			$post        = get_post( $post_id );
			$post_author = $post->post_author;
			$flag        = $this->penci_bl_all_author_send_mail( $subject, $message, $post_id, $post_author, 'author_update', '' );
		}

		if ( $flag ) {
			return true;
		}
	}

	/**
	 * Send Emails And Create Logs
	 *
	 * Handle to send email to subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_post_send_mail( $subject, $message, $post_id, $type, $comment_data ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$ids = array();

		// get the list of all users who are following this post
		$args = array(
			'postid'          => $post_id,
			'penci_bl_status' => 'subscribe'
		);

		$followers_data = $this->penci_bl_get_follow_post_users_data( $args );

		// check followers are not exists
		if ( empty( $followers_data ) ) {
			return false;
		}

		$subject = $this->penci_bl_replace_shortcodes( $post_id, $subject );

		// replace email shortcodes with content
		$message = $this->penci_bl_replace_shortcodes( $post_id, $message );

		$flag = $this->penci_bl_post_send_mail_with_bcc( $followers_data, $subject, $message, $post_id, $type, $comment_data );
		if ( ! $flag ) {
			return false;
		}

		// if mail is successfully send then create log based on enable_log from settings
		if ( isset( $penci_bl_options['enable_log'] ) && $penci_bl_options['enable_log'] == '1' ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				// get mail data
				$mail_data = $subject . "%$%$%" . $message;

				$args = array(
					'post_title'   => $value['post_author'],
					'post_content' => '',
					'post_type'    => PENCI_BL_LOGS_POST_TYPE,
					'post_status'  => 'publish',
					'post_parent'  => $value['ID'],
					'post_author'  => $value['post_author']
				);

				$follow_post_log_id = wp_insert_post( $args );

				if ( $follow_post_log_id ) {

					// update email data meta
					update_post_meta( $follow_post_log_id, $prefix . 'log_email_data', nl2br( $mail_data ) );
				}
			}
		}

		// add action to use 3rd party plugin when mail is sent
		do_action( 'penci_bl_post_send_mail', $followers_data, $post_id );

		return true;
	}

	/**
	 * Send Emails With BCC for follow term
	 *
	 * Handle to send email with bcc for follow term
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 *
	 * When disable unsubscribe confirmation checked send email individually
	 * @since 1.7.6
	 */
	public function penci_bl_term_send_mail_with_bcc( $followers_data, $subject, $message, $term_id = '', $taxonomy = '', $post_id = '', $type = '', $comment_data = '' ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$setmail    = false;
		$recipients = '';
		// Check if diable unsubscribe confirmation checked then send email individually
		if ( $penci_bl_options['recipient_per_email'] == 1 || ( isset( $penci_bl_options['unsubscribe_confirmation'] ) &&
		                                                        ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'term_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// unsubscribe config array send post id and type to unsubscription email
				$unsub_data = array(
					'type' => 'term',
					'id'   => $value['ID']
				);

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user                     = get_user_by( 'email', $email );
					$penci_bl_diff_lang_email = array();

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty( $penci_bl_icl_admin_lang ) ) {
						// If user's admin language set then take it as user language
						$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
					} else {
						// If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

					$setmail = $this->penci_bl_term_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_emailid', $term_id, $taxonomy, $post_id, $unsub_data, $type, $comment_data );
				} else {

					// send email
					$setmail = $this->penci_bl_send_email( $email, $subject, $message, '', '', true, $unsub_data );
				}
			}

		} else if ( empty( $penci_bl_options['recipient_per_email'] ) ) {

			$penci_bl_diff_langs = $penci_bl_diff_lang_email = array();

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'term_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {

					if ( ! empty( $email ) ) {
						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
			}
			if ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) { // If our language array is not empty

				$unsub_data = '';
				$setmail    = $this->penci_bl_term_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $term_id, $taxonomy, $post_id, $unsub_data, $type, $comment_data );
			}
		} else {
			// we're using recipient_per_email
			$count = 1;
			$batch = $penci_bl_diff_langs = $penci_bl_diff_lang_email = array();
			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'term_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {

					if ( ! empty( $email ) ) {
						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
				if ( $penci_bl_options['recipient_per_email'] == $count ) {
					$count = 0;
					if ( ! empty( $recipients ) ) {
						$batch[]    = $recipients;
						$recipients = '';
					} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

						$unsub_data               = '';
						$setmail                  = $this->penci_bl_term_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $term_id, $taxonomy, $post_id, $unsub_data, $type, $comment_data );
						$penci_bl_diff_lang_email = array();
					}
				}
				$count ++;
			}
			// add any partially completed batches to our batch array
			if ( ! empty( $recipients ) ) {
				$batch[] = $recipients;
			} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

				$unsub_data               = '';
				$setmail                  = $this->penci_bl_term_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $term_id, $taxonomy, $post_id, $unsub_data, $type, $comment_data );
				$penci_bl_diff_lang_email = array();
			}
		}

		// actually send mail
		if ( isset( $batch ) && ! empty( $batch ) ) {
			foreach ( $batch as $recipients ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		} else {
			if ( ! empty( $recipients ) ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		}

		return apply_filters( 'penci_bl_term_send_mail_with_bcc', $setmail, $followers_data );
	}

	/**
	 * Send Emails With BCC for follow author
	 *
	 * Handle to send email with bcc for follow author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 *
	 * When disable unsubscribe confirmation checked send email individually
	 * @since 1.7.6
	 */
	public function penci_bl_author_send_mail_with_bcc( $followers_data, $subject, $message, $authorid = '', $post_id = '', $type = '', $comment_data = '' ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$setmail    = false;
		$recipients = '';
		// Check if diable unsubscribe confirmation checked then send email individually
		if ( $penci_bl_options['recipient_per_email'] == 1 || ( isset( $penci_bl_options['unsubscribe_confirmation'] ) &&
		                                                        ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'author_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// unsubscribe config array send post id and type to unsubscription email
				$unsub_data = array(
					'type' => 'author',
					'id'   => $value['ID']
				);

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user                     = get_user_by( 'email', $email );
					$penci_bl_diff_lang_email = array();

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty( $penci_bl_icl_admin_lang ) ) {
						// If user's admin language set then take it as user language
						$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
					} else {
						// If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

					$setmail = $this->penci_bl_auth_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_emailid', $post_id, $unsub_data, $authorid, $type, $comment_data );

				} else {

					// send email
					$setmail = $this->penci_bl_send_email( $email, $subject, $message, '', '', true, $unsub_data );
				}
			}

		} else if ( empty( $penci_bl_options['recipient_per_email'] ) ) {

			$penci_bl_diff_langs = $penci_bl_diff_lang_email = array();

			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'author_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {

					if ( ! empty( $email ) ) {
						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
			}
			if ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) { // If our language array is not empty

				$unsub_data = '';
				$setmail    = $this->penci_bl_auth_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $authorid, $type, $comment_data );
			}
		} else {
			// we're using recipient_per_email
			$count = 1;
			$batch = $penci_bl_diff_langs = $penci_bl_diff_lang_email = array();
			// foreach loop for send email to every user, then create log
			foreach ( $followers_data as $value ) {

				$email = get_post_meta( $value['ID'], $prefix . 'author_user_email', true );

				if ( ! empty( $value['post_author'] ) ) {
					$email = $this->penci_bl_get_user_email_from_id( $value['post_author'] );
				}

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $email ) || empty( $email ) ) {
					continue;
				}

				// If WPML plugin is active
				if ( function_exists( 'icl_object_id' ) ) {

					// Get user data from user_email
					$user = get_user_by( 'email', $email );

					if ( ! empty ( $user ) ) {
						// Get Admin Language for user, if $user is not empty
						$penci_bl_icl_admin_lang = get_user_meta( $user->data->ID, 'icl_admin_language', true );
					}

					if ( ! empty ( $penci_bl_icl_admin_lang ) ) { // If user's admin language is set
						if ( ! in_array( $penci_bl_icl_admin_lang, $penci_bl_diff_langs ) ) { // If user's admin language is not present in our array

							$penci_bl_diff_langs[]                                  = $penci_bl_icl_admin_lang;
							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						} else { // Else

							$penci_bl_diff_lang_email[ $penci_bl_icl_admin_lang ][] = $email;
						}
					} else { // If user's admin language is not set then take current admin language as user language
						$penci_bl_diff_lang_email[ ICL_LANGUAGE_CODE ][] = $email;
					}

				} else {

					if ( ! empty( $email ) ) {
						empty( $recipients ) ? $recipients = "$email" : $recipients .= ", $email";
						// Bcc Headers now constructed by phpmailer class
					}
				}
				if ( $penci_bl_options['recipient_per_email'] == $count ) {
					$count = 0;
					if ( ! empty( $recipients ) ) {
						$batch[]    = $recipients;
						$recipients = '';
					} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

						$unsub_data               = '';
						$setmail                  = $this->penci_bl_auth_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $authorid, $type, $comment_data );
						$penci_bl_diff_lang_email = array();
					}
				}
				$count ++;
			}
			// add any partially completed batches to our batch array
			if ( ! empty( $recipients ) ) {
				$batch[] = $recipients;
			} elseif ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {

				$unsub_data               = '';
				$setmail                  = $this->penci_bl_auth_send_email_per_lang( $penci_bl_diff_lang_email, 'pencibf_bcc', $post_id, $unsub_data, $authorid, $type, $comment_data );
				$penci_bl_diff_lang_email = array();
			}
		}

		// actually send mail
		if ( isset( $batch ) && ! empty( $batch ) ) {
			foreach ( $batch as $recipients ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		} else {
			if ( ! empty( $recipients ) ) {
				$newheaders = "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $newheaders, '', true );
			}
		}

		return apply_filters( 'penci_bl_author_send_mail_with_bcc', $setmail, $followers_data );
	}

	/**
	 * Send Emails And Create Logs for Term
	 *
	 * Handle to send email to term subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_term_create_logs( $post_id ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$flag = false;

		$post_type = get_post_type( $post_id );

		// get post ids in which follow me will display
		$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

		// get post types in which follow me will display
		$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

		// check if post have permission to display follow me form & checkbox
		if ( ! ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
			return false;
		}

		// Get Term subject from meta
		$term_email_subject = get_post_meta( $post_id, $prefix . 'term_email_subject', true );

		// Get Term subject from meta
		$term_email_body = get_post_meta( $post_id, $prefix . 'term_email_body', true );

		if ( isset( $term_email_subject ) && ! empty( $term_email_subject ) ) {
			$subject = $term_email_subject;
		} else {
			$subject = $penci_bl_options['term_email_subject'];
		}

		if ( isset( $term_email_body ) && ! empty( $term_email_body ) ) {
			$message = $term_email_body;
		} else {
			$message = $penci_bl_options['term_email_body'];
		}

		// check if post have allow notification from Category / Tags Notification events > Trigger Emails > When new post published
		if ( isset( $penci_bl_options['term_trigger_notification']['new_post'] ) && $penci_bl_options['term_trigger_notification']['new_post'] == '1' ) {

			$flag = $this->penci_bl_all_term_send_mail( $subject, $message, $post_id, 'term_new', '' );
		}

		if ( $flag ) {
			return true;
		}

		return false;
	}

	/**
	 * Send Emails And Create Logs for author
	 *
	 * Handle to send email to author subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_author_create_logs( $post_id ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$flag = false;

		$post_type = get_post_type( $post_id );

		// get post ids in which follow me will display
		$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

		// get post types in which follow me will display
		$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

		// check if post have permission to display follow me form & checkbox
		if ( ! ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
			return false;
		}

		// Get author subject from meta
		$author_email_subject = get_post_meta( $post_id, $prefix . 'author_email_subject', true );

		// Get author subject from meta
		$author_email_body = get_post_meta( $post_id, $prefix . 'author_email_body', true );

		if ( isset( $author_email_subject ) && ! empty( $author_email_subject ) ) {
			$subject = $author_email_subject;
		} else {
			$subject = $penci_bl_options['author_email_subject'];
		}

		if ( isset( $author_email_body ) && ! empty( $author_email_body ) ) {
			$message = $author_email_body;
		} else {
			$message = $penci_bl_options['author_email_body'];
		}

		// check if post have allow notification from Authors Notification events > Trigger Emails > When new post published
		if ( isset( $penci_bl_options['author_trigger_notification']['new_post'] ) && $penci_bl_options['author_trigger_notification']['new_post'] == '1' ) {
			$post        = get_post( $post_id );
			$post_author = $post->post_author;

			$flag = $this->penci_bl_all_author_send_mail( $subject, $message, $post_id, $post_author, 'author_new', '' );
		}

		if ( $flag ) {
			return true;
		}

		return false;
	}

	/**
	 * Send Emails And Create Logs for Term
	 *
	 * Handle to send email to term subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_all_term_send_mail( $subject, $message, $post_id, $type = '', $comment_data = '' ) {

		$flag = false;

		$post_type = get_post_type( $post_id );

		// All taxonomy for current post type
		$all_taxonomy = get_object_taxonomies( $post_type );
		if ( ! empty( $all_taxonomy ) ) { // Check taxonomy is not empty

			foreach ( $all_taxonomy as $taxonomy_slug ) {

				// Get selected term for particular taxonomy 
				$terms = get_the_terms( $post_id, $taxonomy_slug );

				// check not generate error
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$send_mail = $this->penci_bl_term_send_mail( $term->term_id, $term->taxonomy, $subject, $message, $post_id, $type, $comment_data );
						if ( $send_mail ) {
							$flag = true;
						}
					}
				}
			}
		}

		if ( $flag ) {
			return true;
		}

		return false;
	}

	/**
	 * Send Emails And Create Logs for author
	 *
	 * Handle to send email to author subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_all_author_send_mail( $subject, $message, $post_id, $post_author, $type = '', $comment_data = '' ) {

		$flag = false;

		$send_mail = $this->penci_bl_author_send_mail( $post_author, $subject, $message, $post_id, $type, $comment_data );
		if ( $send_mail ) {
			$flag = true;
		}

		if ( $flag ) {
			return true;
		}

		return false;
	}

	/**
	 * Send Emails And Create Logs for author
	 *
	 * Handle to send email to author subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_author_send_mail( $authorid, $subject, $message, $post_id, $type = '', $comment_data = '' ) {

		global $penci_bl_options;

		if ( ! empty( $authorid ) && ! empty( $post_id ) ) {

			$ids = array();

			$prefix = PENCI_BL_META_PREFIX;

			// get the list of all users who are following this post
			$args = array(
				'authorid'        => $authorid,
				'penci_bl_status' => 'subscribe',
			);

			$followers_data = $this->penci_bl_get_follow_author_users_data( $args );

			// check followers are not exists
			if ( empty( $followers_data ) ) {
				return false;
			}

			$author_name = '';

			// author name & author link
			$author_data = get_user_by( 'id', $authorid );
			if ( ! empty( $author_data ) && isset( $author_data->display_name ) ) {
				$author_name = $author_data->display_name;
			}

			$message = str_replace( '{author_name}', $author_name, $message );

			// replace email shortcodes with content
			$message = $this->penci_bl_replace_shortcodes( $post_id, $message );

			$subject = $this->penci_bl_replace_shortcodes( $post_id, $subject );

			$flag = $this->penci_bl_author_send_mail_with_bcc( $followers_data, $subject, $message, $authorid, $post_id, $type, $comment_data );
			if ( ! $flag ) {
				return false;
			}

			// if mail is successfully send then create log based on enable_log from settings
			if ( isset( $penci_bl_options['enable_log'] ) && $penci_bl_options['enable_log'] == '1' ) {

				// foreach loop for send email to every user, then create log
				foreach ( $followers_data as $value ) {

					// get mail data
					$mail_data = $subject . "%$%$%" . $message;

					$args = array(
						'post_title'   => $value['post_author'],
						'post_content' => '',
						'post_type'    => PENCI_BL_AUTHOR_LOGS_POST_TYPE,
						'post_status'  => 'publish',
						'post_parent'  => $value['ID'],
						'post_author'  => $value['post_author']
					);

					$follow_post_log_id = wp_insert_post( $args );

					if ( $follow_post_log_id ) {

						// update email data meta
						update_post_meta( $follow_post_log_id, $prefix . 'log_email_data', nl2br( $mail_data ) );
					}
				}
			}
		}

		// add action to use 3rd party plugin when mail is sent
		do_action( 'penci_bl_author_send_mail', $followers_data, $post_id );

		return true;
	}

	/**
	 * Send Emails And Create Logs
	 *
	 * Handle to send email to subscriber
	 * and create its logs
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_create_comments( $comment_data ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$flag = false;

		$comment_text = $comment_author = '';
		if ( ! empty( $comment_data ) ) {

			// Get the post id
			$post_id   = $comment_data->comment_post_ID;
			$post_name = get_the_title( $post_id );

			// check send email notification is enabled for $post_id
			if ( ! $this->penci_bl_check_send_email_notifications( $post_id ) ) {
				return false;
			}


			$is_digeest_email_schedule = apply_filters( 'penci_bl_verify_digest_comments_send_email', true, $post_id, $penci_bl_options, $comment_data );

			if ( $is_digeest_email_schedule ) {

				$comment_text   = isset( $comment_data->comment_content ) ? $comment_data->comment_content : '';
				$comment_author = isset( $comment_data->comment_author ) ? $comment_data->comment_author : '';

				// Get Comment subject from meta
				$comment_email_subject = get_post_meta( $post_id, $prefix . 'comment_email_subject', true );

				// Get Comment message from meta
				$comment_email_body = get_post_meta( $post_id, $prefix . 'comment_email_body', true );

				if ( isset( $comment_email_subject ) && ! empty( $comment_email_subject ) ) {
					$subject = $comment_email_subject;
				} else {
					$subject = $penci_bl_options['comment_email_subject'];
				}

				if ( isset( $comment_email_body ) && ! empty( $comment_email_body ) ) {
					$message = $comment_email_body;
				} else {
					$message = $penci_bl_options['comment_email_body'];
				}

				$subject = str_replace( '{user_name}', $comment_author, $subject );

				$message = str_replace( '{comment_text}', $comment_text, $message );
				$message = str_replace( '{user_name}', $comment_author, $message );


				// check if post have allow notification from Post / Page Notification Events > Trigger Emails > When new comment added
				if ( isset( $penci_bl_options['post_trigger_notification']['new_comment'] ) && $penci_bl_options['post_trigger_notification']['new_comment'] == '1' ) {

					$flag = $this->penci_bl_post_send_mail( $subject, $message, $post_id, 'comment', $comment_data );
				}

				// check if post have allow notification from Category / Tags Notification events > Trigger Emails > When new comment added
				if ( isset( $penci_bl_options['term_trigger_notification']['new_comment'] ) && $penci_bl_options['term_trigger_notification']['new_comment'] == '1' ) {

					$flag = $this->penci_bl_all_term_send_mail( $subject, $message, $post_id, 'comment', $comment_data );
				}

				// check if post have allow notification from author Notification events > Trigger Emails > When new comment added
				if ( isset( $penci_bl_options['author_trigger_notification']['new_comment'] ) && $penci_bl_options['author_trigger_notification']['new_comment'] == '1' ) {
					$post        = get_post( $post_id );
					$post_author = $post->post_author;
					$flag        = $this->penci_bl_all_author_send_mail( $subject, $message, $post_id, $post_author, 'comment', $comment_data );
				}
			}

		}
	}

	/**
	 * Check the current post if th shortcode has been added
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_has_shortcode( $shortcode = '' ) {

		$post_to_check = get_post( get_the_ID() );


		// false because we have to search through the post content first
		$found = false;
		// if no shortcode was provided, return false
		if ( ! $shortcode ) {
			return $found;
		}

		if ( empty( $post_to_check ) ) {
			return $found;
		}
		// check the post content for the short code
		if ( stripos( $post_to_check->post_content, '[' . $shortcode ) !== false ) {
			// we have found the short code
			$found = true;
		}

		// return our final results
		return $found;
	}

	/**
	 * Send Confirmation email
	 *
	 * Handles to send confirmation email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_confirmation_email( $args = array() ) {

		global $penci_bl_options;

		$follow_user_email = $confirm_email_link = $post_id = $current_post_id = '';
		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$follow_user_email = $args['penci_bl_email'];
		}
		if ( isset( $args['post_id'] ) && ! empty( $args['post_id'] ) ) {
			$post_id = $args['post_id'];
		}
		if ( isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ) {
			$current_post_id = $args['current_post_id'];
		} else { //if current post id empty
			$current_post_id = ! empty( $penci_bl_options['subscribe_manage_page'] ) ? $penci_bl_options['subscribe_manage_page'] : '';
		}

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {

			// Switch language context�
			do_action( 'wpml_switch_language_for_email', $follow_user_email );


			$penci_bl_options = penci_bl_get_settings();
		}

		// subscribe url
		$url            = get_permalink( $current_post_id );
		$url_parameters = apply_filters( 'penci_bl_confirmation_email_url',
			array(
				'penci_bl_post_id' => base64_encode( $post_id ),
				'penci_bl_email'   => base64_encode( rawurlencode( $follow_user_email ) ),
				'penci_bl_action'  => base64_encode( 'subscribe' )
			)
		);
		$url            = add_query_arg( $url_parameters, $url );
		$subscribe_url  = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Confirm Follow', 'penci-bookmark-follow' ) . '</a>';

		$subject = isset( $penci_bl_options['confirm_email_subject'] ) ? $penci_bl_options['confirm_email_subject'] : '';
		$message = isset( $penci_bl_options['confirm_email_body'] ) ? $penci_bl_options['confirm_email_body'] : '';

		if ( ! empty( $post_id ) ) {
			$subject = $this->penci_bl_replace_shortcodes( $post_id, $subject );
			$message = $this->penci_bl_replace_shortcodes( $post_id, $message );
		}
		$message = str_replace( '{subscribe_url}', $subscribe_url, $message );

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {
			// switch language back
			do_action( 'wpml_restore_language_from_email' );
		}

		// Check message and email id are not empty
		if ( ! empty( $message ) && ! empty( $follow_user_email ) && is_email( $follow_user_email ) ) {

			$setmail = $this->penci_bl_send_email( $follow_user_email, $subject, $message );
		}
	}

	/**
	 * Send Confirmation email for follow term
	 *
	 * Handles to send confirmation email for follow term
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_term_confirmation_email( $args = array() ) {

		global $penci_bl_options;

		$follow_user_email = $confirm_email_link = $post_id = $current_post_id = $term_name = $taxonomy_name = '';

		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$follow_user_email = $args['penci_bl_email'];
		}
		if ( isset( $args['posttype'] ) && ! empty( $args['posttype'] ) ) {
			$posttype = $args['posttype'];
		}
		if ( isset( $args['taxonomy'] ) && ! empty( $args['taxonomy'] ) ) {
			$taxonomy = $args['taxonomy'];
		}
		if ( isset( $args['term_id'] ) && ! empty( $args['term_id'] ) ) {
			$term_id = $args['term_id'];
		}
		if ( isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ) {
			$current_post_id = $args['current_post_id'];
		} else { //if current post id empty
			$current_post_id = ! empty( $penci_bl_options['subscribe_manage_page'] ) ? $penci_bl_options['subscribe_manage_page'] : '';
		}

		// subscribe url
		$subscrib_args = array(
			'penci_bl_posttype' => base64_encode( $posttype ),
			'penci_bl_taxonomy' => base64_encode( $taxonomy ),
			'penci_bl_term_id'  => base64_encode( $term_id ),
			'penci_bl_email'    => base64_encode( rawurlencode( $follow_user_email ) ),
			'penci_bl_action'   => base64_encode( 'subscribeterm' )
		);

		if ( isset( $args['page'] ) && $args['page'] == 'penci-bf-add-follower' ) {
			$url = site_url();
		} else {
			$url = get_permalink( $current_post_id );
		}

		$url           = add_query_arg( $subscrib_args, $url );
		$subscribe_url = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Confirm Follow', 'penci-bookmark-follow' ) . '</a>';

		// term name & term link
		$term_data = get_term_by( 'id', $term_id, $taxonomy );
		if ( ! empty( $term_data ) && isset( $term_data->name ) ) {
			$term_name = $term_data->name;
		}

		// taxonomy name
		$taxonomy_data = get_taxonomy( $taxonomy );
		if ( ! empty( $taxonomy_data ) && isset( $taxonomy_data->labels ) && isset( $taxonomy_data->labels->singular_name ) ) {
			$taxonomy_name = $taxonomy_data->labels->singular_name;
		}
		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {

			// Switch language context�
			do_action( 'wpml_switch_language_for_email', $follow_user_email );


			$penci_bl_options = penci_bl_get_settings();
		}

		$subject = isset( $penci_bl_options['term_confirm_email_subject'] ) ? $penci_bl_options['term_confirm_email_subject'] : '';
		$message = isset( $penci_bl_options['term_confirm_email_body'] ) ? $penci_bl_options['term_confirm_email_body'] : '';

		if ( ! empty( $current_post_id ) ) {

			$subject = $this->penci_bl_replace_shortcodes( $current_post_id, $subject );
			$message = $this->penci_bl_replace_shortcodes( $current_post_id, $message );
		} else {

			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		}

		$subject = str_replace( '{term_name}', $term_name, $subject );

		$message = str_replace( '{term_name}', $term_name, $message );
		$message = str_replace( '{taxonomy_name}', $taxonomy_name, $message );
		$message = str_replace( '{subscribe_url}', $subscribe_url, $message );

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {
			// switch language back
			do_action( 'wpml_restore_language_from_email' );
		}

		// Check message and email id are not empty
		if ( ! empty( $message ) && ! empty( $follow_user_email ) && is_email( $follow_user_email ) ) {

			$setmail = $this->penci_bl_send_email( $follow_user_email, $subject, $message );
		}
	}

	/**
	 * Send Confirmation email for follow author
	 *
	 * Handles to send confirmation email for follow author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_author_confirmation_email( $args = array() ) {

		global $penci_bl_options;

		$follow_user_email = $confirm_email_link = $post_id = $current_post_id = '';

		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$follow_user_email = $args['penci_bl_email'];
		}

		if ( isset( $args['author_id'] ) && ! empty( $args['author_id'] ) ) {
			$author_id = $args['author_id'];
		}
		if ( isset( $args['current_post_id'] ) && ! empty( $args['current_post_id'] ) ) {
			$current_post_id = $args['current_post_id'];
		} else { //if current post id empty
			$current_post_id = ! empty( $penci_bl_options['subscribe_manage_page'] ) ? $penci_bl_options['subscribe_manage_page'] : '';
		}

		// subscribe url
		$subscrib_args = array(
			'penci_bl_author_id' => base64_encode( $author_id ),
			'penci_bl_email'     => base64_encode( rawurlencode( $follow_user_email ) ),
			'penci_bl_action'    => base64_encode( 'subscribeauthor' )
		);

		if ( isset( $args['page'] ) && $args['page'] == 'penci-bf-add-follower' ) {
			$url = site_url();
		} else {
			$url = get_permalink( $current_post_id );
		}

		$url           = add_query_arg( $subscrib_args, $url );
		$subscribe_url = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Confirm Follow', 'penci-bookmark-follow' ) . '</a>';

		// author name & author link
		$author_data = get_user_by( 'id', $author_id );
		if ( ! empty( $author_data ) && isset( $author_data->display_name ) ) {
			$author_name = $author_data->display_name;
		}

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {

			// Switch language context�
			do_action( 'wpml_switch_language_for_email', $follow_user_email );


			$penci_bl_options = penci_bl_get_settings();
		}

		$subject = isset( $penci_bl_options['author_confirm_email_subject'] ) ? $penci_bl_options['author_confirm_email_subject'] : '';
		$message = isset( $penci_bl_options['author_confirm_email_body'] ) ? $penci_bl_options['author_confirm_email_body'] : '';

		if ( ! empty( $current_post_id ) ) {

			$subject = $this->penci_bl_replace_shortcodes( $current_post_id, $subject );
			$message = $this->penci_bl_replace_shortcodes( $current_post_id, $message );

		} else {
			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		}

		$subject = str_replace( '{author_name}', $author_name, $subject );

		$message = str_replace( '{author_name}', $author_name, $message );
		$message = str_replace( '{subscribe_url}', $subscribe_url, $message );

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {
			// switch language back
			do_action( 'wpml_restore_language_from_email' );
		}

		// Check message and email id are not empty
		if ( ! empty( $message ) && ! empty( $follow_user_email ) && is_email( $follow_user_email ) ) {

			$setmail = $this->penci_bl_send_email( $follow_user_email, $subject, $message );
		}
	}

	/**
	 * Send Confirmation for unsubscribe email
	 *
	 * Handles to send confirmation for unsubscribe email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_confirmation_unsubscribe_email( $args = array() ) {

		global $penci_bl_options, $post;

		$follow_user_email = $confirm_email_link = '';
		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$follow_user_email = $args['penci_bl_email'];
		}

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {

			// Switch language context�
			do_action( 'wpml_switch_language_for_email', $follow_user_email );


			$penci_bl_options = penci_bl_get_settings();
		}

		$subject = isset( $penci_bl_options['unsubscribe_confirm_email_subject'] ) ? $penci_bl_options['unsubscribe_confirm_email_subject'] : '';
		$message = isset( $penci_bl_options['unsubscribe_confirm_email_body'] ) ? $penci_bl_options['unsubscribe_confirm_email_body'] : '';

		$unsubscribe_page_id = isset( $penci_bl_options['unsubscribe_page'] ) && ! empty( $penci_bl_options['unsubscribe_page'] ) ? $penci_bl_options['unsubscribe_page'] : $post->ID;
		$url                 = get_permalink( $unsubscribe_page_id );
		$url                 = add_query_arg( array(
			'penci_bl_action' => base64_encode( 'unsubscribe' ),
			'penci_bl_email'  => base64_encode( rawurlencode( $follow_user_email ) )
		), $url );
		$confirm_email_link  = '<a target="_blank" href="' . esc_url( $url ) . '" >' . esc_html__( 'Confirm Unsubscription', 'penci-bookmark-follow' ) . '</a>';

		$subject = str_replace( '{email}', $follow_user_email, $subject );

		$message = str_replace( '{email}', $follow_user_email, $message );
		$message = str_replace( '{confirm_url}', $confirm_email_link, $message );

		/*if ( ! empty( $post->ID ) ) {
			$subject = $this->penci_bl_replace_shortcodes( $post->ID, $subject );
			$message = $this->penci_bl_replace_shortcodes( $post->ID, $message );
		}*/

		// If WPML plugin is active
		if ( function_exists( 'icl_object_id' ) ) {
			// switch language back
			do_action( 'wpml_restore_language_from_email' );
		}


		return $this->penci_bl_send_email( $follow_user_email, $subject, $message );
	}

	/**
	 * Filter for getting follow author data with grouped
	 *
	 * Handles to get follow author data with grouped
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	function penci_bl_follow_author_groupby( $groupby ) {

		global $wpdb;

		$groupby = "{$wpdb->posts}.post_parent";

		return apply_filters( 'penci_bl_follow_author_groupby', $groupby );
	}

	/**
	 * Get author data
	 *
	 * Handles to get all author list
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_author_list( $args = array() ) {

		//check if its custom post types created or not
		$args = array(
			'role'   => 'author',
			'fields' => array( 'id', 'display_name' )
		);

		$users = get_users( $args );

		return $users;
	}

	/**
	 * Get author data
	 *
	 * Handles to get author data for
	 * followauthor post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_author_data( $args = array() ) {

		global $current_user;

		$prefix = PENCI_BL_META_PREFIX;

		$followauthorlist = array();

		$metaquery = array();

		//check if its custom post types created or not
		$checkauthorargs = array(
			'post_status' => 'publish',
			'post_type'   => PENCI_BL_AUTHOR_POST_TYPE,
		);
		/**
		 * If current user Author
		 * View only current login author followers
		 */
		$user_role = $current_user->roles;

		if ( in_array( 'author', $user_role ) ) {
			$checkauthorargs['post_parent'] = get_current_user_id();
		}

		$checkauthorargs = wp_parse_args( $checkauthorargs, $args );

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {

			global $wpdb;

			$search = $args['search'];
			// prepare query
			$querystr = "
    			SELECT id 
    			FROM $wpdb->users
    			WHERE display_name LIKE '%$search%'";

			// get result from database
			$users_ids = $wpdb->get_results( $querystr, ARRAY_A );
			$users     = array();
			// preare terms id array from result
			foreach ( $users_ids as $key => $users_id ) {
				$users[] = $users_id['id'];
			}

			if ( empty( $users ) ) {
				// if no result found then set post parent to something that never exist
				$checkauthorargs['post_parent'] = 9999999999;
			} else {
				// assign terms id in post parent id for searching purpose
				$checkauthorargs['post_parent__in'] = $users;
			}
		}

		if ( ! empty( $metaquery ) ) {
			$checkauthorargs['meta_query'] = $metaquery;
		}

		// add filter for getting follow author data with grouped
		add_filter( 'posts_groupby', array( $this, 'penci_bl_follow_author_groupby' ) );

		//fire query in to table for retriving data
		$result = new WP_Query( $checkauthorargs );

		// remove filter for groupby remove to post query
		remove_filter( 'posts_groupby', array( $this, 'penci_bl_follow_author_groupby' ) );

		//retrived data is in object format so assign that data to array for listing
		$followauthorlist = $this->penci_bl_object_to_array( $result->posts );

		// Check if post type counter from filter
		if ( isset( $args['count'] ) && ! empty( $args['count'] ) ) {
			return count( $followauthorlist );
		}

		// if get list for author list then return data with data and total array
		if ( isset( $args['penci_bl_list_author_data'] ) && ! empty( $args['penci_bl_list_author_data'] ) ) {

			$data_res['data'] = $followauthorlist;

			//To get total count of post using "found_posts" and for users "total_users" parameter
			$data_res['total'] = isset( $result->found_posts ) ? $result->found_posts : '';

			return $data_res;
		}

		return $followauthorlist;
	}

	/**
	 * Get author Users data
	 *
	 * Handles to get author users data for
	 * post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_author_users_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followauthorusersargs = array(
			'post_type'   => PENCI_BL_AUTHOR_POST_TYPE,
			'post_status' => 'publish'
		);

		$followauthorusersargs = wp_parse_args( $followauthorusersargs, $args );

		//show how many per page records
		if ( isset( $args['posts_per_page'] ) && ! empty( $args['posts_per_page'] ) ) {
			$followauthorusersargs['posts_per_page'] = $args['posts_per_page'];
		} else {
			$followauthorusersargs['posts_per_page'] = '-1';
		}

		//show per page records
		if ( isset( $args['paged'] ) && ! empty( $args['paged'] ) ) {
			$followauthorusersargs['paged'] = $args['paged'];
		}

		//if search using post parent
		if ( isset( $args['authorid'] ) ) {
			$followauthorusersargs['post_parent'] = $args['authorid'];
		}

		//if search using author
		if ( isset( $args['author'] ) ) {
			$followauthorusersargs['author'] = $args['author'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {

			$metaargs[] = array(
				'key'     => $prefix . 'author_user_email',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		if ( isset( $args['penci_bl_status'] ) && ! empty( $args['penci_bl_status'] ) ) {
			$status     = $args['penci_bl_status'] == 'subscribe' ? '1' : '0';
			$metaargs[] = array(
				'key'   => $prefix . 'follow_status',
				'value' => $status
			);
		}

		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$metaargs[] = array(
				'key'   => $prefix . 'author_user_email',
				'value' => $args['penci_bl_email']
			);
		}

		if ( ! empty( $metaargs ) ) {
			$followauthorusersargs['meta_query'] = $metaargs;
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followauthorusersargs['fields'] = $args['fields'];
		}

		//get order by records
		$followauthorusersargs['order']   = 'DESC';
		$followauthorusersargs['orderby'] = 'date';

		//fire query in to table for retriving data
		$result = new WP_Query( $followauthorusersargs );

		if ( isset( $args['count'] ) && $args['count'] == '1' ) {
			$followauthoruserslist = $result->post_count;
		} else {
			//retrived data is in object format so assign that data to array for listing
			$followauthoruserslist = $this->penci_bl_object_to_array( $result->posts );

			// if get list for follow author user list then return data with data and total array
			if ( isset( $args['penci_bl_users_list_authors_data'] ) && ! empty( $args['penci_bl_users_list_authors_data'] ) ) {

				$data_res['data'] = $followauthoruserslist;

				//To get total count of post using "found_posts" and for users "total_users" parameter
				$data_res['total'] = isset( $result->found_posts ) ? $result->found_posts : '';

				return $data_res;
			}
		}

		return $followauthoruserslist;
	}

	/**
	 * Get author User Logs data
	 *
	 * Handles to get author user logs data for
	 * post type
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_follow_author_user_logs_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followeduserlogssargs = array(
			'post_type'      => PENCI_BL_AUTHOR_LOGS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => '-1'
		);

		//if search using post parent
		if ( isset( $args['logid'] ) ) {
			$followeduserlogssargs['post_parent'] = $args['logid'];
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followeduserlogssargs['fields'] = $args['fields'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {

			$metaargs[] = array(
				'key'     => $prefix . 'log_email_data',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		if ( ! empty( $metaargs ) ) {
			$followeduserlogssargs['meta_query'] = $metaargs;
		}

		//fire query in to table for retriving data
		$result = new WP_Query( $followeduserlogssargs );

		//retrived data is in object format so assign that data to array for listing
		$followeduserlogslist = $this->penci_bl_object_to_array( $result->posts );

		return $followeduserlogslist;
	}

	/**
	 * Check Follow Email is exist
	 *
	 * Handles to check follow email is exist
	 * into follow posts, terms and author
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_check_follow_email( $email ) {

		$all_follows = array();

		// args to check if this user_email is exist for follow post
		$args = array(
			'penci_bl_email'  => $email,
			'penci_bl_status' => 'subscribe',
			'fields'          => 'ids'
		);

		$follow_posts   = $this->penci_bl_get_follow_post_users_data( $args );
		$follow_authors = $this->penci_bl_get_follow_author_users_data( $args );

		if ( ! empty( $follow_posts ) || ! empty( $follow_authors ) ) {
			$all_follows = array(
				'follow_posts'   => $follow_posts,
				'follow_authors' => $follow_authors
			);
		}

		return apply_filters( 'penci_bl_check_follow_email', $all_follows, $email );
	}

	/**
	 * Get Date Format
	 *
	 * Handles to return formatted date which format is set in backend
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_get_date_format( $date, $time = false ) {

		$format = $time ? get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) : get_option( 'date_format' );
		$date   = date_i18n( $format, strtotime( $date ) );

		return apply_filters( 'penci_bl_get_date_format', $date, $time );
	}

	/**
	 * Get Short Content From Long Content
	 *
	 * Handles to return content with specific
	 * string length
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_short_content( $content, $charlength = 30 ) {

		//check content is large then characterlenght
		if ( strlen( $content ) > $charlength ) {

			$subex   = substr( $content, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut   = - ( strlen( $exwords[ count( $exwords ) - 1 ] ) );

			if ( $excut < 0 ) {
				$content = substr( $subex, 0, $excut );
			} else {
				$content = $subex;
			}
			$content = trim( $content ) . '...';
		}

		//return short content if long passed then length otherwise original content will be return
		return apply_filters( 'penci_bl_short_content', $content, $charlength );
	}

	/**
	 * Check Post Update Notification for post
	 *
	 * Handles to check post update notification for
	 * current post when post is going to update
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 **/
	public function penci_bl_check_post_update_notification() {

		global $post, $penci_bl_options;

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		$post_type = get_post_type( $post->ID );

		$filter_name = 'penci_bl_check_post_update_notification';

		// get post ids in which follow me will display
		$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

		// get post types in which follow me will display
		$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

		// check if post have permission to display follow me form & checkbox
		if ( ! ( in_array( $post->ID, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
			return apply_filters( $filter_name, false );
		}

		// check if post have allow notification from Posts Notification Events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['post_trigger_notification']['post_update'] ) && $penci_bl_options['post_trigger_notification']['post_update'] == '1' ) {
			return apply_filters( $filter_name, true );
		}

		// check if post have allow notification from Terms Notification Events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['term_trigger_notification']['post_update'] ) && $penci_bl_options['term_trigger_notification']['post_update'] == '1' ) {
			return apply_filters( $filter_name, true );
		}

		// check if post have allow notification from Authors Notification Events > Trigger Emails > When post / page updated
		if ( isset( $penci_bl_options['author_trigger_notification']['post_update'] ) && $penci_bl_options['author_trigger_notification']['post_update'] == '1' ) {
			return apply_filters( $filter_name, true );
		}
	}

	/**
	 * Send Global Email
	 *
	 * Handles to send global email
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 *
	 */
	public function penci_bl_send_email( $email, $subject, $message, $appendheader = '', $email_template = '', $unsubscribe = false, $unsub_data = array() ) {

		global $penci_bl_options;
		$admin_email = get_option( 'admin_email' );

		$fromEmail = ( isset( $penci_bl_options['from_email'] ) && ! empty( $penci_bl_options['from_email'] ) ) ? $penci_bl_options['from_email'] : $admin_email;

		$is_email = preg_match( '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/si', $fromEmail );

		// check if from email not contain email
		if ( ! empty( $fromEmail ) && ! $is_email ) {
			$fromEmail .= ' <' . $admin_email . '>';
		}

		$headers = 'From: ' . $fromEmail . "\r\n";
		$headers .= "Reply-To: " . $fromEmail . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= $appendheader;

		if ( ! empty( $email_template ) ) {
			$email_template_option = $email_template;
		} else if ( isset( $penci_bl_options['email_template'] ) && ! empty( $penci_bl_options['email_template'] ) ) {
			$email_template_option = $penci_bl_options['email_template'];
		} else {
			$email_template_option = 'default';
		}

		$message = do_shortcode( $message );
		$message = wpautop( $message );

		$html = '';
		$html .= '<html>
					<head></head>
					<body>';
		// @param email added to generate direct unsubscribe link 
		$html = apply_filters( 'penci_bl_email_template_' . $email_template_option, $html, $message, $unsubscribe, $email, $unsub_data );
		$html .= '	</body>
				</html>';

		$html = apply_filters( 'penci_bl_email_html', $html, $message );

		$externally_send_mail = array( 'send_mail_externally' => false );

		// apply filter to send email externally
		$externally_send_mail = apply_filters( 'penci_bl_send_email_externaly', $externally_send_mail, $email, $subject, $headers, $message );

		if ( $externally_send_mail['send_mail_externally'] == true ) { // check if email is sent from here or externally

			// assign return value from 3rd party plugin (true or false)
			$setmail = isset( $externally_send_mail['success'] ) ? $externally_send_mail['success'] : false;
		} else {

			// Filter when mail to param is blank (Note : This is for some domains on which mail will not work without 'to')
			if ( empty( $email ) ) {
				$email = apply_filters( 'penci_bl_blank_email_to', $email, $fromEmail );
			}

			$setmail = wp_mail( $email, $subject, $html, $headers );
		}

		return $setmail;
	}

	/**
	 * Get Email Templates
	 *
	 * Handles to get all email templates
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.2.0
	 */
	public function penci_bl_email_get_templates() {

		$templates = array(
			''      => esc_html__( 'HTML Template', 'penci-bookmark-follow' ),
			'plain' => esc_html__( 'No template, plain text only', 'penci-bookmark-follow' )
		);

		return apply_filters( 'penci_bl_email_get_templates', $templates );
	}

	/**
	 * Send Emails With BCC for followers
	 *
	 * Handle to send email with bcc for followers
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.5.1
	 *
	 * When disable unsubscribe confirmation checked send email individually
	 * @since 1.7.6
	 */
	public function penci_bl_send_mail_with_bcc( $followers, $subject, $message, $appendheader = '', $email_template = '', $unsubscribe = false ) {

		global $penci_bl_options;

		$prefix = PENCI_BL_META_PREFIX;

		$setmail    = false;
		$recipients = '';
		$followers  = ( ! is_array( $followers ) ) ? (array) $followers : $followers;

		// Check if diable unsubscribe confirmation checked then send email individually
		if ( $penci_bl_options['recipient_per_email'] == 1 || ( isset( $penci_bl_options['unsubscribe_confirmation'] ) &&
		                                                        ! empty( $penci_bl_options['unsubscribe_confirmation'] ) && $penci_bl_options['recipient_per_email'] == 1 ) ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers as $follower_email ) {

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $follower_email ) || empty( $follower_email ) ) {
					continue;
				}

				// send email to each user individually
				$setmail = $this->penci_bl_send_email( $follower_email, $subject, $message, $appendheader, $email_template, $unsubscribe );
			}

		} else if ( empty( $penci_bl_options['recipient_per_email'] ) ) {

			// foreach loop for send email to every user, then create log
			foreach ( $followers as $follower_email ) {

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $follower_email ) || empty( $follower_email ) ) {
					continue;
				}

				if ( ! empty( $follower_email ) ) {

					empty( $recipients ) ? $recipients = "$follower_email" : $recipients .= ", $follower_email";
					// Bcc Headers now constructed by phpmailer class
				}
			}
		} else {
			// we're using recipient_per_email
			$count = 1;
			$batch = array();
			// foreach loop for send email to every user, then create log
			foreach ( $followers as $follower_email ) {

				// sanity check -- make sure we have a valid email
				if ( ! is_email( $follower_email ) || empty( $follower_email ) ) {
					continue;
				}

				if ( ! empty( $follower_email ) ) {
					empty( $recipients ) ? $recipients = "$follower_email" : $recipients .= ", $follower_email";
					// Bcc Headers now constructed by phpmailer class
				}
				if ( $penci_bl_options['recipient_per_email'] == $count ) {
					$count      = 0;
					$batch[]    = $recipients;
					$recipients = '';
				}
				$count ++;
			}
			// add any partially completed batches to our batch array
			if ( ! empty( $recipients ) ) {
				$batch[] = $recipients;
			}
		}

		// actually send mail
		if ( isset( $batch ) && ! empty( $batch ) ) {
			foreach ( $batch as $recipients ) {
				$appendheader .= "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $appendheader, $email_template, $unsubscribe );
			}
		} else {
			if ( ! empty( $recipients ) ) {
				$appendheader .= "Bcc: $recipients\n";
				// send email
				$setmail = $this->penci_bl_send_email( '', $subject, $message, $appendheader, $email_template, $unsubscribe );
			}
		}

		return $setmail;
	}

	/**
	 * Check for send email notifications
	 *
	 * Check whether email should send or not for particular post_id
	 *
	 * Handle to send email with bcc for followers
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.0
	 */
	public function penci_bl_check_send_email_notifications( $post_id ) {


		return true;
	}

	/**
	 * Get user email from user id
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.1
	 */
	public function penci_bl_get_user_email_from_id( $userid ) {

		$user_email = '';

		$userdata = get_user_by( 'id', $userid );

		if ( ! empty( $userdata ) && ! empty( $userdata->user_email ) ) {
			$user_email = $userdata->user_email;
		}

		return apply_filters( 'penci_bl_get_user_email_from_id', $user_email, $userid );
	}

	/**
	 * Send email from an array depending on languages
	 * for post update or comment on post
	 * while WPML plugin is active
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.1
	 */
	public function penci_bl_post_send_email_per_lang( $penci_bl_diff_lang_email, $header_type, $post_id, $unsub_data, $type, $comment_data ) {

		$prefix = PENCI_BL_META_PREFIX;

		if ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {
			foreach ( $penci_bl_diff_lang_email as $penci_bl_diff_lang_email_id => $penci_bl_diff_lang_email_val ) {

				// Switch language context�
				do_action( 'wpml_switch_language_for_email', $penci_bl_diff_lang_email_val[0] );


				$penci_bl_options = penci_bl_get_settings();

				if ( $type == 'comment' ) {

					$comment_text = $comment_author = '';
					if ( ! empty( $comment_data ) ) {

						// Get the post id
						$post_id   = $comment_data->comment_post_ID;
						$post_name = get_the_title( $post_id );

						// check send email notification is enabled for $post_id
						if ( ! $this->penci_bl_check_send_email_notifications( $post_id ) ) {
							return false;
						}

						// Get disable email notification meta
						$disable_email_notification = get_post_meta( $post_id, $prefix . 'disable_email_notification', true );

						// Check disable enail notification is checked
						if ( $disable_email_notification != '1' ) {

							$comment_text   = isset( $comment_data->comment_content ) ? $comment_data->comment_content : '';
							$comment_author = isset( $comment_data->comment_author ) ? $comment_data->comment_author : '';

							// Get Comment subject from meta
							$comment_email_subject = get_post_meta( $post_id, $prefix . 'comment_email_subject', true );

							// Get Comment message from meta
							$comment_email_body = get_post_meta( $post_id, $prefix . 'comment_email_body', true );

							if ( isset( $comment_email_subject ) && ! empty( $comment_email_subject ) ) {
								$subject = $comment_email_subject;
							} else {
								$subject = $penci_bl_options['comment_email_subject'];
							}

							if ( isset( $comment_email_body ) && ! empty( $comment_email_body ) ) {
								$message = $comment_email_body;
							} else {
								$message = $penci_bl_options['comment_email_body'];
							}

							$subject = str_replace( '{user_name}', $comment_author, $subject );
							$subject = str_replace( '{post_name}', $post_name, $subject );

							$message = str_replace( '{comment_text}', $comment_text, $message );
							$message = str_replace( '{user_name}', $comment_author, $message );
							$message = str_replace( '{post_name}', $post_name, $message );
						}
					}
				} elseif ( $type == 'post' ) {
					// Get Email subject from meta
					$post_email_subject = get_post_meta( $post_id, $prefix . 'email_subject', true );

					// Get Post message from meta
					$post_email_body = get_post_meta( $post_id, $prefix . 'email_body', true );

					if ( isset( $post_email_subject ) && ! empty( $post_email_subject ) ) { // If $post_email_subject is empty then assign that to $subject
						$subject = $post_email_subject;
					} else { // Else get subject from global settings
						$subject = $penci_bl_options['email_subject'];
					}

					if ( isset( $post_email_body ) && ! empty( $post_email_body ) ) { // If $post_email_body is empty then assign that to $message
						$message = $post_email_body;
					} else { // Else get subject from global settings
						$message = $penci_bl_options['email_body'];
					}

					// replace email shortcodes with content
					$subject = $this->penci_bl_replace_shortcodes( $post_id, $subject );

					// replace email shortcodes with content
					$message = $this->penci_bl_replace_shortcodes( $post_id, $message );
				}

				if ( $header_type == 'pencibf_bcc' ) {
					$newheaders = $email = '';
					foreach ( $penci_bl_diff_lang_email_val as $penci_bl_emails ) { // Generate BCC now
						empty( $newheaders ) ? $newheaders = "Bcc: $penci_bl_emails" : $newheaders .= ", $penci_bl_emails";
					}
				} else {
					$newheaders = $email = '';
					$email      = $penci_bl_diff_lang_email_val;
				}

				// send email to user's with email set under BCC
				$setmail = $this->penci_bl_send_email( $email, $subject, $message, $newheaders, '', true, $unsub_data );

				// switch language back
				do_action( 'wpml_restore_language_from_email' );
			}
		}
	}

	/**
	 * Send email from an array depending on languages
	 * for author new post, author post update or comment on  author's post
	 * while WPML plugin is active
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.6.1
	 */
	public function penci_bl_auth_send_email_per_lang( $penci_bl_diff_lang_email, $header_type, $post_id, $unsub_data, $authorid, $type = '', $comment_data = '' ) {

		$prefix = PENCI_BL_META_PREFIX;

		if ( ! empty ( $penci_bl_diff_lang_email ) && is_array( $penci_bl_diff_lang_email ) ) {
			foreach ( $penci_bl_diff_lang_email as $penci_bl_diff_lang_email_id => $penci_bl_diff_lang_email_val ) {

				// Switch language context�
				do_action( 'wpml_switch_language_for_email', $penci_bl_diff_lang_email_val[0] );


				$penci_bl_options = penci_bl_get_settings();

				if ( $type == 'comment' ) {

					$comment_text = $comment_author = '';
					if ( ! empty( $comment_data ) ) {

						// Get the post id
						$post_id   = $comment_data->comment_post_ID;
						$post_name = get_the_title( $post_id );

						// check send email notification is enabled for $post_id
						if ( ! $this->penci_bl_check_send_email_notifications( $post_id ) ) {
							return false;
						}

						// Get disable email notification meta 
						$disable_email_notification = get_post_meta( $post_id, $prefix . 'disable_email_notification', true );

						// Check disable enail notification is checked
						if ( $disable_email_notification != '1' ) {

							$comment_text   = isset( $comment_data->comment_content ) ? $comment_data->comment_content : '';
							$comment_author = isset( $comment_data->comment_author ) ? $comment_data->comment_author : '';

							// Get Comment subject from meta
							$comment_email_subject = get_post_meta( $post_id, $prefix . 'comment_email_subject', true );

							// Get Comment message from meta
							$comment_email_body = get_post_meta( $post_id, $prefix . 'comment_email_body', true );

							if ( isset( $comment_email_subject ) && ! empty( $comment_email_subject ) ) {
								$subject = $comment_email_subject;
							} else {
								$subject = $penci_bl_options['comment_email_subject'];
							}

							if ( isset( $comment_email_body ) && ! empty( $comment_email_body ) ) {
								$message = $comment_email_body;
							} else {
								$message = $penci_bl_options['comment_email_body'];
							}

							$subject = str_replace( '{user_name}', $comment_author, $subject );
							$subject = str_replace( '{post_name}', $post_name, $subject );

							$message = str_replace( '{comment_text}', $comment_text, $message );
							$message = str_replace( '{user_name}', $comment_author, $message );
							$message = str_replace( '{post_name}', $post_name, $message );
						}
					}
				} elseif ( $type == 'author_new' ) {

					$post_type = get_post_type( $post_id );

					// get post ids in which follow me will display
					$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

					// get post types in which follow me will display
					$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

					// check if post have permission to display follow me form & checkbox
					if ( ! ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
						return false;
					}

					// Get author subject from meta
					$author_email_subject = get_post_meta( $post_id, $prefix . 'author_email_subject', true );

					// Get author subject from meta
					$author_email_body = get_post_meta( $post_id, $prefix . 'author_email_body', true );

					if ( isset( $author_email_subject ) && ! empty( $author_email_subject ) ) {
						$subject = $author_email_subject;
					} else {
						$subject = $penci_bl_options['author_email_subject'];
					}

					if ( isset( $author_email_body ) && ! empty( $author_email_body ) ) {
						$message = $author_email_body;
					} else {
						$message = $penci_bl_options['author_email_body'];
					}

					// get the list of all users who are following this post
					$args = array(
						'authorid'        => $authorid,
						'penci_bl_status' => 'subscribe',
					);

					$followers_data = $this->penci_bl_get_follow_author_users_data( $args );

					// check followers are not exists
					if ( empty( $followers_data ) ) {
						return false;
					}

					$author_name = '';

					// author name & author link
					$author_data = get_user_by( 'id', $authorid );
					if ( ! empty( $author_data ) && isset( $author_data->display_name ) ) {
						$author_name = $author_data->display_name;
					}

					$message = str_replace( '{author_name}', $author_name, $message );

				} elseif ( $type == 'author_update' ) {

					$post_type = get_post_type( $post_id );

					// get post ids in which follow me will display
					$selected_posts = isset( $penci_bl_options[ 'notification_item_' . $post_type ] ) ? $penci_bl_options[ 'notification_item_' . $post_type ] : array();

					// get post types in which follow me will display
					$selected_post_types = isset( $penci_bl_options['notification_type'] ) ? $penci_bl_options['notification_type'] : array();

					// check if post have permission to display follow me form & checkbox
					if ( ! ( in_array( $post_id, $selected_posts ) || in_array( $post_type, $selected_post_types ) ) ) {
						return false;
					}

					// Get Post subject from meta
					$post_email_subject = get_post_meta( $post_id, $prefix . 'email_subject', true );

					// Get Post message from meta
					$post_email_body = get_post_meta( $post_id, $prefix . 'email_body', true );

					if ( isset( $post_email_subject ) && ! empty( $post_email_subject ) ) {
						$subject = $post_email_subject;
					} else {
						$subject = $penci_bl_options['email_subject'];
					}

					if ( isset( $post_email_body ) && ! empty( $post_email_body ) ) {
						$message = $post_email_body;
					} else {
						$message = $penci_bl_options['email_body'];
					}
				}
				// replace email shortcodes with content
				$message = $this->penci_bl_replace_shortcodes( $post_id, $message );

				$subject = $this->penci_bl_replace_shortcodes( $post_id, $subject );

				if ( $header_type == 'pencibf_bcc' ) {
					$newheaders = $email = '';
					foreach ( $penci_bl_diff_lang_email_val as $penci_bl_emails ) { // Generate BCC now
						empty( $newheaders ) ? $newheaders = "Bcc: $penci_bl_emails" : $newheaders .= ", $penci_bl_emails";
					}
				} else {
					$newheaders = $email = '';
					$email      = $penci_bl_diff_lang_email_val;
				}

				// send email to user's with email set under BCC
				$setmail = $this->penci_bl_send_email( $email, $subject, $message, $newheaders, '', true, $unsub_data );

				if ( ! $setmail ) {
					return false;
				}

				// switch language back
				do_action( 'wpml_restore_language_from_email' );
			}
		}

		return true;
	}

	/**
	 * Get Term Users data
	 *
	 */
	public function penci_bl_get_follow_term_users_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followtermusersargs = array(
			'post_type'   => PENCI_BL_TERM_POST_TYPE,
			'post_status' => 'publish'
		);

		$followtermusersargs = wp_parse_args( $followtermusersargs, $args );

		//show how many per page records
		if ( isset( $args['posts_per_page'] ) && ! empty( $args['posts_per_page'] ) ) {
			$followtermusersargs['posts_per_page'] = $args['posts_per_page'];
		} else {
			$followtermusersargs['posts_per_page'] = '-1';
		}

		//show per page records
		if ( isset( $args['paged'] ) && ! empty( $args['paged'] ) ) {
			$followtermusersargs['paged'] = $args['paged'];
		}

		//if search using post parent
		if ( isset( $args['termid'] ) ) {
			$followtermusersargs['post_parent'] = $args['termid'];
		}

		//if search using author
		if ( isset( $args['author'] ) ) {
			$followtermusersargs['author'] = $args['author'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {

			$metaargs[] = array(
				'key'     => $prefix . 'term_user_email',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		if ( isset( $args['penci_bl_status'] ) && ! empty( $args['penci_bl_status'] ) ) {
			$status     = $args['penci_bl_status'] == 'subscribe' ? '1' : '0';
			$metaargs[] = array(
				'key'   => $prefix . 'follow_status',
				'value' => $status
			);
		}

		if ( isset( $args['penci_bl_taxonomy'] ) && ! empty( $args['penci_bl_taxonomy'] ) ) {
			$metaargs[] = array(
				'key'   => $prefix . 'taxonomy_slug',
				'value' => $args['penci_bl_taxonomy']
			);
		}

		if ( isset( $args['penci_bl_email'] ) && ! empty( $args['penci_bl_email'] ) ) {
			$metaargs[] = array(
				'key'   => $prefix . 'term_user_email',
				'value' => $args['penci_bl_email']
			);
		}

		if ( ! empty( $metaargs ) ) {
			$followtermusersargs['meta_query'] = $metaargs;
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followtermusersargs['fields'] = $args['fields'];
		}

		//get order by records
		$followtermusersargs['order']   = 'DESC';
		$followtermusersargs['orderby'] = 'date';

		//fire query in to table for retriving data
		$result = new WP_Query( $followtermusersargs );

		if ( isset( $args['count'] ) && $args['count'] == '1' ) {
			$followtermuserslist = $result->post_count;
		} else {
			//retrived data is in object format so assign that data to array for listing
			$followtermuserslist = $this->penci_bl_object_to_array( $result->posts );

			// if get list for follow term user list then return data with data and total array
			if ( isset( $args['penci_bl_user_list_term_data'] ) && ! empty( $args['penci_bl_user_list_term_data'] ) ) {

				$data_res['data'] = $followtermuserslist;

				//To get total count of post using "found_posts" and for users "total_users" parameter
				$data_res['total'] = isset( $result->found_posts ) ? $result->found_posts : '';

				return $data_res;
			}
		}

		return $followtermuserslist;
	}

	/**
	 * Get Term User Logs data
	 */
	public function penci_bl_get_follow_term_user_logs_data( $args = array() ) {

		$prefix = PENCI_BL_META_PREFIX;

		$followeduserlogssargs = array(
			'post_type'      => PENCI_BL_TERM_LOGS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => '-1'
		);

		//if search using post parent
		if ( isset( $args['logid'] ) ) {
			$followeduserlogssargs['post_parent'] = $args['logid'];
		}

		//if returns only id
		if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
			$followeduserlogssargs['fields'] = $args['fields'];
		}

		//if search is called then retrive searching data
		if ( isset( $args['search'] ) ) {

			$metaargs[] = array(
				'key'     => $prefix . 'log_email_data',
				'value'   => $args['search'],
				'compare' => 'LIKE'
			);
		}

		if ( ! empty( $metaargs ) ) {
			$followeduserlogssargs['meta_query'] = $metaargs;
		}

		//fire query in to table for retriving data
		$result = new WP_Query( $followeduserlogssargs );

		//retrived data is in object format so assign that data to array for listing
		$followeduserlogslist = $this->penci_bl_object_to_array( $result->posts );

		return $followeduserlogslist;
	}
}