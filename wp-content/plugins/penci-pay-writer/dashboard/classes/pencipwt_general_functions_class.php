<?php

require_once( 'pencipwt_permissions_class.php' );

class PenciPWT_General_Functions {

	/**
	 * Gets the cumulative settings for the requested user. Takes care to integrate general with current user/author ones.
	 *
	 * IF GENERAL SETTINGS ARE REQUESTED: if class var has settings, return that, otherwise get_site_option (so if network ones are available, those are got, otherwise blog-specific) - THIS is to
	 * make sure that we have some settings to base our further checks on. THEN check whether network settings should be got or not.
	 * IF USER SETTINGS ARE REQUESTED: if a valid user + a user who has specific settings + (current_user can see spcial settings) is requested, get its user_meta. Store user settings in global var as caching.
	 * IF NOTHING OF THE PREVIOUS IS MATCHED: return general settings.
	 *
	 * @access  public
	 *
	 * @param int the desired user id
	 * @param bool whether can-see-other-users-personalized-settings permission should be checked
	 * @param bool whether user specific settings should be completed with general ones for options which are not cusomized for that user
	 *
	 * @return  array the requested settings
	 * @since   2.0
	 */
	static function get_settings( $userid, $check_current_user_cap_special = false, $complete_with_general = true ) {
		global $pencipwt_global_settings;

		//GENERAL SETTINGS
		if ( $userid == 'general' ) {

			//Retrieve from cache if available
			$cache = wp_cache_get( 'ppc_settings_' . $userid );
			if ( $cache !== false ) {
				$return = $cache;
			} else {
				/* MULTISITE stuff
				$temp_settings = get_site_option( $pencipwt_global_settings['general_options_name'] );
				if( ! $temp_settings ) {
					$temp_settings = get_option( $pencipwt_global_settings['general_options_name'] );
				}

				if( $temp_settings['multisite_settings_rule'] == 1 ) {
					$general_settings = get_site_option( $pencipwt_global_settings['general_options_name'] );;
				} else {*/
				/*$general_settings = array();
				foreach( $general_settings_options as $single ) {
					$general_settings = array_merge( $general_settings, get_option( $single ) );
				}*/

				//Fetch them from database if first request
				$return = get_option( $pencipwt_global_settings['option_name'] );
				wp_cache_set( 'ppc_settings_' . $userid, $return );
				//}
			}

			//If a valid userid is given
		} else if ( (int) $userid != 0 ) {
			global $current_user;
			$general_settings = self::get_settings( 'general' );
			$perm             = new PenciPWT_Permissions();

			//If user shouldn't see other users personalized settings, set the userid to their own
			if ( isset( $current_user ) and $current_user->ID != 0 and
			                                $check_current_user_cap_special == true and $current_user->ID != $userid and ( ! $perm->can_see_countings_special_settings() ) ) {
				$userid = $current_user->ID;
			}

			//Retrieve cached settings if available or from database if not
			$cache = wp_cache_get( 'ppc_settings_' . $userid );

			if ( $cache != false and $complete_with_general ) {
				$user_settings = $cache;
			} else {
				if ( $cache === 0 ) { //cache equal to 0 means user has no special settings
					$user_settings = $general_settings;
				} else {
					$user_settings = get_user_option( $pencipwt_global_settings['option_name'], $userid );

					//If no special settings for this user are available, get general ones
					if ( $user_settings == false ) {
						$user_settings = $general_settings;
						wp_cache_set( 'ppc_settings_' . $userid, 0 );

						//If user has special settings, complete user settings with general ones if needed (i.e. add only-general settings to the return array of special user's settings)
					} else if ( $complete_with_general ) {
						$user_settings = array_merge( $general_settings, $user_settings );
						wp_cache_set( 'ppc_settings_' . $userid, $user_settings );
					}
				}
			}

			$return = $user_settings;

		} else {
			$return = self::get_settings( 'general' );
		}

		/**
		 * Filters retrieved settings before returning them.
		 *
		 * @param    $return array to be returned settings array
		 * @param    $userid string user id whose settings are being requested
		 * @param bool whether can-see-other-users-personalized-settings permission should be checked
		 * @param bool whether user specific settings should be completed with general ones for options which are not cusomized for that user
		 *
		 * @since    2.518
		 */
		$return = apply_filters( 'ppc_settings', $return );
		$return = apply_filters( 'ppc_get_settings', $return, $userid, $check_current_user_cap_special, $complete_with_general );

		return $return;
	}

	/**
	 * Clears cache of all settings-dependent objects.
	 * This is DEPRECATED and is now a wrapper of PenciPWT_Cache_Functions::clear_settings_cache( $userid ).
	 *
	 * @access  public
	 *
	 * @param   $userid string userid whose settings cache needs to be flushed
	 *
	 * @return    void
	 * @since   2.601
	 */
	static function clear_settings_cache( $userid = 'general' ) {
		PenciPWT_Cache_Functions::clear_settings( $userid );
	}

	/**
	 * Gets non capitalized input.
	 *
	 * Grants compatibility with PHP < 5.3.
	 *
	 * @access  public
	 *
	 * @param   $string string to lowercase
	 *
	 * @return  string lowercased
	 * @since   2.0.9
	 */

	static function lcfirst( $string ) {
		if ( function_exists( 'lcfirst' ) ) {
			return lcfirst( $string );
		} else {
			return (string) ( strtolower( substr( $string, 0, 1 ) ) . substr( $string, 1 ) );
		}
	}

	/**
	 * Gets the link to the stats page of the requested author with the proper start and end time
	 *
	 * @access  public
	 *
	 * @param   $author_id int the author id
	 *
	 * @return  string the link to their stats
	 * @since   2.0
	 */

	static function get_the_author_link( $author_id ) {
		global $pencipwt_global_settings;

		$link = admin_url( $pencipwt_global_settings['stats_menu_link'] . '&amp;author=' . $author_id . '&amp;tstart=' . $pencipwt_global_settings['stats_tstart'] . '&amp;tend=' . $pencipwt_global_settings['stats_tend'] );

		if ( isset( $_REQUEST['ppc-time-range'] ) and ! empty( $_REQUEST['ppc-time-range'] ) ) {
			$link .= '&amp;ppc-time-range=' . sanitize_text_field( $_REQUEST['ppc-time-range'] );
		}

		if ( isset( $_REQUEST['paged'] ) and ! empty( $_REQUEST['paged'] ) ) {
			$link .= '&amp;paged=' . sanitize_text_field( $_REQUEST['paged'] );
		}

		return apply_filters( 'ppc_get_author_link', $link );
	}

	/**
	 * Makes sure each user role has or has not the requested capability to see options and stats pages.
	 *
	 * Called when updating settings and updating/installing.
	 *
	 * @access  public
	 *
	 * @param   $allowed_user_roles_options_page array user roles allowed to see plugin options
	 * @param   $allowed_user_roles_stats_page array user roles allowed to see plugin stats
	 *
	 * @since   2.0.4
	 */

	static function manage_cap_allowed_user_roles_plugin_pages( $allowed_user_roles_options_page, $allowed_user_roles_stats_page ) {
		global $wp_roles, $pencipwt_global_settings;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$wp_roles_to_use = array();
		foreach ( $wp_roles->role_names as $key => $value ) {
			$wp_roles_to_use[] = $key;
		}

		$allowed_user_roles_stats_page_add_cap      = array_intersect( $allowed_user_roles_stats_page, $wp_roles_to_use );
		$allowed_user_roles_stats_page_remove_cap   = array_diff( $wp_roles_to_use, $allowed_user_roles_stats_page );
		$allowed_user_roles_options_page_add_cap    = array_intersect( $allowed_user_roles_options_page, $wp_roles_to_use );
		$allowed_user_roles_options_page_remove_cap = array_diff( $wp_roles_to_use, $allowed_user_roles_options_page );

		foreach ( $allowed_user_roles_options_page_add_cap as $single ) {
			$current_role = get_role( self::lcfirst( $single ) );

			if ( is_object( $current_role ) and ! $current_role->has_cap( $pencipwt_global_settings['cap_manage_options'] ) ) {
				$current_role->add_cap( $pencipwt_global_settings['cap_manage_options'] );
			}
		}

		foreach ( $allowed_user_roles_options_page_remove_cap as $single ) {
			$current_role = get_role( self::lcfirst( $single ) );

			if ( is_object( $current_role ) and $current_role->has_cap( $pencipwt_global_settings['cap_manage_options'] ) ) {
				$current_role->remove_cap( $pencipwt_global_settings['cap_manage_options'] );
			}
		}

		foreach ( $allowed_user_roles_stats_page_add_cap as $single ) {
			$current_role = get_role( self::lcfirst( $single ) );

			if ( is_object( $current_role ) and ! $current_role->has_cap( $pencipwt_global_settings['cap_access_stats'] ) ) {
				$current_role->add_cap( $pencipwt_global_settings['cap_access_stats'] );
			}
		}

		foreach ( $allowed_user_roles_stats_page_remove_cap as $single ) {
			$current_role = get_role( self::lcfirst( $single ) );

			if ( is_object( $current_role ) and $current_role->has_cap( $pencipwt_global_settings['cap_access_stats'] ) ) {
				$current_role->remove_cap( $pencipwt_global_settings['cap_access_stats'] );
			}
		}
	}

	/**
	 * Defines default stats time range depending on chosen settings.
	 *
	 * Stores settings in plugin's global var.
	 *
	 * @access  public
	 *
	 * @param   $settings array plugin settings
	 *
	 * @since   2.1
	 */
	static function get_default_stats_time_range( $settings ) {
		global $pencipwt_global_settings;

		//Default time range already done
		if ( isset( $pencipwt_global_settings['stats_tstart'] ) ) {
			return;
		}

		//First available post time
		$args                 = array(
			'post_type'      => pencipwt_get_setting( 'counting_allowed_post_types' ),
			'posts_per_page' => 1,
			'orderby'        => 'post_date',
			'order'          => 'ASC'
		);
		$first_available_post = new WP_Query( $args );

		if ( $first_available_post->no_found_rows === false ) {
			$first_available_post_time = current_time( 'timestamp' );
		} else {
			$first_available_post_time = strtotime( $first_available_post->posts[0]->post_date );
		}

		$pencipwt_global_settings['first_available_post_time'] = $first_available_post_time;

		//Last available post time
		$args                = array(
			'post_type'      => pencipwt_get_setting( 'counting_allowed_post_types' ),
			'posts_per_page' => 1,
			'orderby'        => 'post_date',
			'order'          => 'DESC'
		);
		$last_available_post = new WP_Query( $args ); //for future scheduled posts

		if ( $last_available_post->no_found_rows === false ) {
			$last_available_post_time = strtotime( $last_available_post->posts[0]->post_date );
		}

		if ( ! isset( $last_available_post_time ) or $last_available_post_time < current_time( 'timestamp' ) ) {
			$last_available_post_time = strtotime( '23:59:59' );
		} //Pub Bonus needs to select even days without posts in the future, maybe there are publishings

		$pencipwt_global_settings['last_available_post_time'] = $last_available_post_time;

		//Define default time range
		if ( pencipwt_get_setting( 'default_stats_time_range_week' ) ) {
			$pencipwt_global_settings['stats_tstart'] = strtotime( '00:00:00' ) - ( ( date( 'N' ) - 1 ) * 24 * 60 * 60 );
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' );
		} else if ( pencipwt_get_setting( 'default_stats_time_range_month' ) ) {
			$pencipwt_global_settings['stats_tstart'] = strtotime( '00:00:00' ) - ( ( date( 'j' ) - 1 ) * 24 * 60 * 60 ); //starts from timestamp of current day and subtracts seconds for enough days (depending on what day is today)
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' );
		} else if ( pencipwt_get_setting( 'default_stats_time_range_this_year' ) ) {
			$pencipwt_global_settings['stats_tstart'] = strtotime( '00:00:00' ) - ( ( date( 'z' ) ) * 24 * 60 * 60 ); //starts from timestamp of current day and subtracts seconds for enough days (depending on what day of the year is today)
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' );
		} else if ( pencipwt_get_setting( 'default_stats_time_range_last_month' ) ) {
			$prev_month                               = ( date( 'm' ) > 1 ) ? date( 'm' ) - 1 : 12;
			$pencipwt_global_settings['stats_tstart'] = strtotime( '00:00:00' ) - ( ( date( 'j' ) - 1 + cal_days_in_month( CAL_GREGORIAN, $prev_month, date( 'Y' ) ) ) * 24 * 60 * 60 );
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' ) - ( date( 'j' ) * 24 * 60 * 60 );
		} else if ( pencipwt_get_setting( 'default_stats_time_range_all_time' ) ) {
			$pencipwt_global_settings['stats_tstart'] = $pencipwt_global_settings['first_available_post_time'];
			$pencipwt_global_settings['stats_tend']   = $pencipwt_global_settings['last_available_post_time'];
		} else if ( pencipwt_get_setting( 'default_stats_time_range_custom' ) ) {
			$pencipwt_global_settings['stats_tstart'] = strtotime( '00:00:00' ) - ( pencipwt_get_setting( 'default_stats_time_range_custom_value' ) * 24 * 60 * 60 );
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' );
		} else if ( pencipwt_get_setting( 'default_stats_time_range_start_day' ) ) {
			$pencipwt_global_settings['stats_tstart'] = strtotime( pencipwt_get_setting( 'default_stats_time_range_start_day_value' ) . ' 00:00:00' );
			$pencipwt_global_settings['stats_tend']   = strtotime( '23:59:59' );
		}
	}

	/**
	 * Checks if there is a saved ordering for stats and redirects to the apt ordered stats page in case.
	 * Stores current ordering, if any.
	 *
	 * @since    2.725
	 *
	 */
	static function default_stats_order() {
		global $pencipwt_global_settings;

		//Exit if disabled
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		if ( ! pencipwt_get_setting( 'save_stats_order' ) ) {
			return;
		}

		//If there is a saved sorting, use it
		if ( ! isset( $_GET['orderby'] ) and isset( $_COOKIE[ 'ppc_' . $pencipwt_global_settings['current_page'] . '_orderby' ] ) ) {
			$redirect_url = admin_url( 'admin.php' ) . '?' . $_SERVER['QUERY_STRING'] . '&orderby=' . $_COOKIE[ 'ppc_' . $pencipwt_global_settings['current_page'] . '_orderby' ];

			if ( isset( $_COOKIE[ 'ppc_' . $pencipwt_global_settings['current_page'] . '_order' ] ) ) {
				$redirect_url .= '&order=' . $_COOKIE[ 'ppc_' . $pencipwt_global_settings['current_page'] . '_order' ];
			}

			wp_safe_redirect( $redirect_url );

		}

		//Store stats sorting settings, cookies expire in 6 months
		if ( isset( $_GET['orderby'] ) ) {
			setcookie( 'ppc_' . $pencipwt_global_settings['current_page'] . '_orderby', htmlentities( $_GET['orderby'] ), time() + ( 86400 * 180 ) );

			if ( isset( $_GET['order'] ) ) {
				setcookie( 'ppc_' . $pencipwt_global_settings['current_page'] . '_order', htmlentities( $_GET['order'] ), time() + ( 86400 * 180 ) );
			}
		}

	}

	/**
	 * Formats payments for output.
	 *
	 * @access    public
	 *
	 * @param    $payment string payment to be formatted
	 *
	 * @return    string formatted payment
	 * @since    2.40
	 */

	static function format_payment( $payment ) {

		return apply_filters( 'ppc_format_payment', sprintf( '%.' . pencipwt_get_setting( 'payment_display_round_digits' ) . 'f', $payment ) );
	}

	/**
	 * Parses visits callback function.
	 *
	 * @access    public
	 *
	 * @param callback to be parsed
	 *
	 * @return    mixed (string/array) visits count PHP callable
	 * @since    2.770
	 */
	static function parse_visits_callback_function( $callback = '' ) {
		$explode = explode( ',', $callback );

		$return = $callback;
		if ( count( $explode ) == 2 ) //if callback is in the form classname, methodname
		{
			$return = array( trim( $explode[0] ), trim( $explode[1] ) );
		}

		return $return;
	}

	/**
	 * Computes the total paid by summing up all payment history items ppc_count/ppc_payment values
	 *
	 * @access  public
	 *
	 * @param    $payment_history array payment history
	 *
	 * @return    array paid total
	 * @since   1.0
	 */
	static function get_paid_total( $payment_history ) {
		global $ppc_global_settings;

		//$payment_history_count = count( $payment_history );
		$paid_total = array(
			'ppc_count'   => array(),
			'ppc_payment' => array(),
			'ppc_misc'    => array()
		);

		if ( empty( $payment_history ) ) {
			return $paid_total;
		}

		foreach ( $payment_history as $single ) {

			if ( ! empty( $single['ppc_count'] ) ) {
				foreach ( $single['ppc_count'] as $key => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $paid_total['ppc_count'][ $key ] ) ) {
						$paid_total['ppc_count'][ $key ] = $value;
					} else {
						$paid_total['ppc_count'][ $key ] += $value;
					}
				}
			}

			if ( ! empty( $single['ppc_payment'] ) ) {

				foreach ( $single['ppc_payment'] as $key => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $paid_total['ppc_payment'][ $key ] ) ) {
						$paid_total['ppc_payment'][ $key ] = $value;
					} else {
						$paid_total['ppc_payment'][ $key ] += $value;
					}
				}
			}

			if ( ! empty( $single['ppc_misc'] ) ) {
				foreach ( $single['ppc_misc'] as $key => $value ) {
					if ( ! isset( $paid_total['ppc_misc'][ $key ] ) ) {
						$paid_total['ppc_misc'][ $key ] = $value;
					} else if ( is_array( $value ) ) {
						$paid_total['ppc_misc'][ $key ] = array_merge( $paid_total['ppc_misc'][ $key ], $value );
					} else {
						$paid_total['ppc_misc'][ $key ][] = $value;
					}
				}
			}
		}

		return apply_filters( 'ppcp_get_paid_total', $paid_total, $payment_history );
	}

	/**
	 * Computes due payment amount for a given post.
	 * Subtracts payment_history counts from paid_total ones, for each counting type.
	 * Then retrieves payment through get_countings_payment.
	 * Finally adds possible payment_only counting types amounts.
	 *
	 * @access  public
	 *
	 * @param    $paid_total array paid total countings
	 * @param    $post object WP post object
	 *
	 * @return    array due payment
	 * @since   1.0
	 */
	static function compute_post_due_payment( $paid_total, $post ) {
		global $ppc_global_settings;

		$due_payment = array(
			'ppc_count'   => array(),
			'ppc_payment' => array(),
			'ppc_misc'    => array()
		);

		//Return 0 if post is paid and should be excluded from stats
		if ( pencipwt_get_setting( 'exclude_from_stats_after_payment' ) and ! empty( $paid_total['ppc_payment'] ) ) {
			$due_payment['ppc_payment']['total'] = 0;

			return $due_payment;
		}

		foreach ( $post->ppc_count['normal_count'] as $key => $value ) {
			//If this counting type was already paid for
			if ( isset( $paid_total['ppc_count'][ $key ] ) ) {
				$due_payment['ppc_count'][ $key ]['to_count'] = $value['to_count'] - $paid_total['ppc_count'][ $key ];
			} else {
				$due_payment['ppc_count'][ $key ]['to_count'] = $value['to_count'];
			}
		}

		$due_payment['ppc_payment'] = PenciPWT_Counting_Stuff::get_countings_payment( $due_payment['ppc_count'], $post->post_author );

		//Add payment_only counting types amounts
		$counting_types = PenciPWT_Counting_Stuff::$current_active_counting_types_post;
		foreach ( $counting_types as $id => $value ) {
			if ( isset( $value['payment_only'] ) and $value['payment_only'] == true ) {
				$counting_type_payment = call_user_func( $value['payment_callback'], $value, $post->ID );

				if ( isset( $paid_total['ppc_payment'][ $id ] ) ) {
					$due_payment['ppc_payment'][ $id ] = $counting_type_payment - $paid_total['ppc_payment'][ $id ];
				} else {
					$due_payment['ppc_payment'][ $id ] = $counting_type_payment;
				}
			}
		}

		$due_payment['ppc_payment']['total'] = array_sum( $due_payment['ppc_payment'] );

		return $due_payment;
	}

	/**
	 * Computes the author due payment amount.
	 *
	 * For post counting types, the payment is built summing up all post individual payments,
	 * while for author counting types by subtracting paid_total values from count values.
	 * Then a get_countings_payment is called, and the data from post and author counting
	 * types are merged together.
	 *
	 * Be careful never to share the same ID for post and author counting types.
	 *
	 * @access  public
	 *
	 * @param    $paid_total array paid total countings
	 * @param    $author_stats object WP post object
	 * @param   $author_id
	 *
	 * @return    array due payment
	 * @since   1.0
	 */
	static function compute_author_due_payment( $paid_total, $author_stats, $author_id ) {
		global $ppc_global_settings;

		$due_payment = array(
			'ppc_count'   => array(),
			'ppc_payment' => array( 'total' => 0 ), //hack to allow computing due payment of author with NO posts
			'ppc_misc'    => array()
		);

		//Post counting types
		foreach ( $author_stats as $post_id => $post_stats ) {
			if ( $post_id == 'total' ) {
				continue;
			}

			foreach ( $post_stats->ppc_count['due_payment'] as $key => $value ) {
				if ( ! isset( $due_payment['ppc_count'][ $key ] ) ) {
					$due_payment['ppc_count'][ $key ]['to_count'] = $value['to_count']; //due payment only holds the to_count value
				} else {
					$due_payment['ppc_count'][ $key ]['to_count'] += $value['to_count'];
				}
			}

			foreach ( $post_stats->ppc_payment['due_payment'] as $key => $value ) {
				if ( ! isset( $due_payment['ppc_payment'][ $key ] ) ) {
					$due_payment['ppc_payment'][ $key ] = $value;
				} else {
					$due_payment['ppc_payment'][ $key ] += $value;
				}
			}
		}
		$author_counting_types_class = new PenciPWT_Counting_Types();

		$author_counting_types = $author_counting_types_class->get_active_counting_types( 'author', $author_id );
		$due_payment_author    = array(
			'ppc_count'   => array(),
			'ppc_payment' => array(),
			'ppc_misc'    => array()
		);

		//Author wide only counting types
		if ( ! empty( $author_counting_types ) and isset( $author_stats['total']['ppc_count'] ) and isset( $author_stats['total']['ppc_count']['normal_count'] ) ) {
			foreach ( $author_stats['total']['ppc_count']['normal_count'] as $key => $value ) {
				if ( isset( $author_counting_types[ $key ] ) ) {

					//If this payment type was already paid for - else new
					if ( isset( $paid_total['ppc_count'][ $key ] ) ) {
						$due_payment_author['ppc_count'][ $key ]['to_count'] = $value['to_count'] - $paid_total['ppc_count'][ $key ]['to_count'];
					} else {
						$due_payment_author['ppc_count'][ $key ]['to_count'] = $value['to_count'];
						$due_payment_author['ppc_count'][ $key ]['aux']      = $value['aux'];
					}

				}
			}
		}

		PenciPWT_Counting_Stuff::$current_active_counting_types_author = $author_counting_types; //NEEDED because get_counting_payment relies on that variable, and we would always be using the last-author-in-stats settings otherwise

		$due_payment_author = apply_filters( 'ppcp_due_payment_author_before_payment', $due_payment_author, $paid_total, $author_stats, $author_id );

		$due_payment_author['ppc_payment']          = PenciPWT_Counting_Stuff::get_countings_payment( $due_payment_author['ppc_count'], $author_id );
		$due_payment_author['ppc_payment']['total'] = array_sum( $due_payment_author['ppc_payment'] );

		//Put together posts and author data
		$due_payment['ppc_payment']['total'] = $due_payment_author['ppc_payment']['total'] + $due_payment['ppc_payment']['total'];
		unset( $due_payment_author['ppc_payment']['total'] );
		$due_payment = array_merge_recursive_distinct( $due_payment, $due_payment_author ); //avoid duplicating indexes

		return apply_filters( 'ppcp_author_due_payment', $due_payment, $paid_total, $author_stats );
	}

	/**
	 * Adds draft status to allowed post statuses in settings.
	 *
	 * @param    $settings array
	 * @param    $userid int
	 * @param    $check_current_user_cap_special bool
	 * @param    $complete_with_general bool
	 *
	 * @return    array settings
	 * @since    1.8.5
	 */
	static function get_settings_allow_all_statuses( $settings, $userid, $check_current_user_cap_special, $complete_with_general ) {
		foreach ( get_post_stati() as $status ) {
			$settings['counting_allowed_post_statuses'][ $status ] = 1;
		}

		return $settings;
	}
}

//Compatibility for people who lack the PHP calendar plugin
if ( ! function_exists( 'cal_days_in_month' ) ) {
	function cal_days_in_month( $calendar, $month, $year ) {
		return date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
	}
}

//Ensuring compatibility with PHP < 5.5 (Requires 5.3, though)
if ( ! function_exists( "array_column" ) ) {
	function array_column( $array, $column_name ) {
		return array_map( function ( $element ) use ( $column_name ) {
			return $element[ $column_name ];
		}, $array );
	}
}

if ( ! function_exists( 'array_merge_recursive_distinct' ) ) {
	function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => &$value ) {
			if ( is_array( $value ) && isset ( $merged [ $key ] ) && is_array( $merged [ $key ] ) ) {
				$merged [ $key ] = array_merge_recursive_distinct( $merged [ $key ], $value );
			} else {
				$merged [ $key ] = $value;
			}
		}

		return $merged;
	}
}

//Callback for stats sorting
function ppc_uasort_stats_sort( $a, $b ) {
	$result = strnatcasecmp( $a[ $_REQUEST['orderby'] ], $b[ $_REQUEST['orderby'] ] ); //Determine sort order

	return ( $_REQUEST['order'] === 'asc' ) ? $result : - $result; //Send final sort direction to usort
}

//Get a list with how settings should be prioritized (for addons)
//Assumes that every action has a different priority
function ppc_get_settings_priority( $setting_type ) {
	global $wp_filter;

	$settings_priority = array();
	foreach ( $wp_filter['ppc_get_settings']->callbacks as $priority => $callbacks ) {
		#$settings_priority[key($callbacks)] = $priority;
		if ( strpos( key( $callbacks ), $setting_type ) !== false ) {
			return $priority;
		}
	}

	return false;
	#return $settings_priority;
}
