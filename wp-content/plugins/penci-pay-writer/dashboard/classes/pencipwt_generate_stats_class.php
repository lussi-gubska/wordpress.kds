<?php

class PenciPWT_Generate_Stats {

	/**
	 * @var    array $grp_args holds get_requested_posts WP_Query args.
	 * @since    2.49
	 */
	public static $grp_args;

	/**
	 * Produces stats by calling all needed methods.
	 *
	 * This is the highest-level method.
	 *
	 * @access  public
	 *
	 * @param   $time_start int the start time range timestamp
	 * @param   $time_end int the end time range timestamp
	 * @param   $author array optional an array of users for detailed stats
	 * @param    $format bool whether output stats should include formatted stats
	 *
	 * @return  array raw stats + formatted for output stats
	 * @since   2.0.2
	 */
	static function produce_stats( $time_start, $time_end, $author = null, $format = true ) {
		global $current_user, $pencipwt_global_settings;

		$return = array();
		$perm   = new PenciPWT_Permissions();

		if ( $cached_stats = PenciPWT_Cache_Functions::get_stats_snapshot( $time_start, $time_end, $author ) ) {
			return $cached_stats['stats'];
		}

		//If general stats & CU can't see others' general, behave as if detailed for him
		if ( ! is_array( $author ) and ! $perm->can_see_others_general_stats() ) {
			$requested_posts = PenciPWT_Generate_Stats::get_requested_posts( $time_start, $time_end, array( $current_user->ID ) );
		} else {
			$requested_posts = PenciPWT_Generate_Stats::get_requested_posts( $time_start, $time_end, $author );
		}

		if ( is_wp_error( $requested_posts ) ) {
			return $requested_posts;
		}

		$stats = PenciPWT_Generate_Stats::group_stats_by_author( $requested_posts );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$stats = PenciPWT_Counting_Stuff::data2cash( $stats, $author );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$stats = PenciPWT_Generate_Stats::calculate_total_stats( $stats );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$return['raw_stats'] = $stats;

		if ( $format ) {
			$formatted_stats = PenciPWT_Generate_Stats::format_stats_for_output( $stats, $author );
			if ( is_wp_error( $formatted_stats ) ) {
				return $formatted_stats;
			}

			$return['formatted_stats'] = $formatted_stats;
		}

		return $return;
	}

	/**
	 * Builds an array of posts to be counted given the timeframe, complete with their data.
	 *
	 * @access  public
	 *
	 * @param   $time_start int the start time range timestamp
	 * @param   $time_end int the end time range timestamp
	 * @param   $author array optional an array of users for detailed stats
	 *
	 * @return  array the array of WP posts object to be counted
	 * @since   2.0
	 */
	static function get_requested_posts( $time_start, $time_end, $author = null ) {
		self::$grp_args = array(
			'post_type'              => pencipwt_get_setting( 'counting_allowed_post_types' ),
			'post_status'            => array( 'publish', 'pending', 'future', 'private' ),
			// allowed post status can vary per user, so we allow them all in query and filter in data2cash
			'date_query'             => array(
				'after'     => date( 'Y-m-d H:i:s', $time_start ),
				'before'    => date( 'Y-m-d H:i:s', $time_end ),
				'inclusive' => true
			),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'posts_per_page'         => - 1,
			'ignore_sticky_posts'    => 1,
			'suppress_filters'       => false,
			'ppc_filter_user_roles'  => 1,
			'ppc_allowed_user_roles' => pencipwt_get_setting( 'counting_allowed_user_roles' )
		);

		//If a user_id is provided, and is valid, posts only by that author are selected
		if ( is_array( $author ) ) {
			self::$grp_args['author__in'] = $author;
		}

		self::$grp_args = apply_filters( 'ppc_get_requested_posts_args', self::$grp_args );

		//Filter for allowed user roles if needed
		if ( isset( self::$grp_args['ppc_filter_user_roles'] ) and self::$grp_args['ppc_filter_user_roles'] ) {
			add_filter( 'posts_join', array( 'PenciPWT_Generate_Stats', 'grp_filter_user_roles' ), 10, 2 );
		}

		$requested_posts = new WP_Query( self::$grp_args );

		//Remove custom filters
		remove_filter( 'posts_join', array( 'PenciPWT_Generate_Stats', 'grp_filter_user_roles' ) );

		do_action( 'ppc_got_requested_posts', $requested_posts );


		return $requested_posts->posts;
	}

	/**
	 * Filters get_requested_posts query for allowed user roles.
	 *
	 * @access  public
	 *
	 * @param   $join string the sql join
	 *
	 * @return  string the sql join
	 * @since   2.24
	 */
	static function grp_filter_user_roles( $join ) {
		global $wpdb;

		$join .= "INNER JOIN " . $wpdb->usermeta . "
                    ON " . $wpdb->usermeta . ".user_id = " . $wpdb->posts . ".post_author
                    AND " . $wpdb->usermeta . ".meta_key = '" . $wpdb->get_blog_prefix() . "capabilities'
                    AND " . $wpdb->usermeta . ".meta_value REGEXP ('" . implode( '|', self::$grp_args['ppc_allowed_user_roles'] ) . "')";

		return $join;
	}

	/**
	 * Groups posts array by their authors.
	 *
	 * @access  public
	 *
	 * @param   $data array the counting data
	 *
	 * @return  array the counting data, grouped by author id
	 * @since   2.519
	 */
	static function group_stats_by_author( $data ) {
		$sorted_array = array();
		foreach ( $data as $post_id => $single ) {
			$sorted_array[ $single->post_author ][ $post_id ] = $single;
		}

		return apply_filters( 'ppc_grouped_by_author_stats', $sorted_array );
	}

	/**
	 * Computes authors total (count+payment)
	 *
	 * @access  public
	 *
	 * @param   $data array the counting data grouped by author
	 *
	 * @return  array the counting data, with totals
	 * @since   2.519
	 */
	static function calculate_total_stats( $data ) {
		global $pencipwt_global_settings, $current_user;

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );

		foreach ( $data as $author_id => $author_stats ) {
			$user_settings = PenciPWT_General_Functions::get_settings( $author_id, true );

			//Make sure stats arrays always exist in a complete form, even though empty
			//if( ! isset( $author_stats['total']['ppc_payment']['normal_payment'] ) )
			$data[ $author_id ]['total']['ppc_payment']['normal_payment'] = array();

			//if( ! isset( $author_stats['total']['ppc_count']['normal_count'] ) )
			$data[ $author_id ]['total']['ppc_count']['normal_count'] = array();

			foreach ( $author_stats as $post_id => $single ) {

				//Written posts count
				if ( ! isset( $data[ $author_id ]['total']['ppc_misc']['posts'] ) ) {
					$data[ $author_id ]['total']['ppc_misc']['posts'] = 1;
				} else {
					$data[ $author_id ]['total']['ppc_misc']['posts'] ++;
				}

				//Don't include in general stats count posts below threshold
				if ( pencipwt_get_setting( 'counting_payment_only_when_total_threshold' ) ) {
					if ( $single->ppc_misc['exceed_threshold'] == false ) {
						continue;
					}
				}

				//Compute total countings
				foreach ( $single->ppc_count['normal_count'] as $what => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $data[ $author_id ]['total']['ppc_count']['normal_count'][ $what ] ) ) {
						$data[ $author_id ]['total']['ppc_count']['normal_count'][ $what ]['real']     = $single->ppc_count['normal_count'][ $what ]['real'];
						$data[ $author_id ]['total']['ppc_count']['normal_count'][ $what ]['to_count'] = $single->ppc_count['normal_count'][ $what ]['to_count'];
					} else {
						$data[ $author_id ]['total']['ppc_count']['normal_count'][ $what ]['real']     += $single->ppc_count['normal_count'][ $what ]['real'];
						$data[ $author_id ]['total']['ppc_count']['normal_count'][ $what ]['to_count'] += $single->ppc_count['normal_count'][ $what ]['to_count'];
					}
				}

				//Compute total payment
				foreach ( $single->ppc_payment['normal_payment'] as $what => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $data[ $author_id ]['total']['ppc_payment']['normal_payment'][ $what ] ) ) {
						$data[ $author_id ]['total']['ppc_payment']['normal_payment'][ $what ] = $value;
					} else {
						$data[ $author_id ]['total']['ppc_payment']['normal_payment'][ $what ] += $value;
					}
				}

				$data[ $author_id ] = apply_filters( 'ppc_sort_stats_by_author_foreach_post', $data[ $author_id ], $single );
			}
		}

		//Add all users to stats so that author payment criteria may be applied even with no written posts
		$perm = new PenciPWT_Permissions();

		if ( $pencipwt_global_settings['current_page'] == 'stats_general' and pencipwt_get_setting( 'stats_show_all_users' ) ) {
			$args = array( 'fields' => array( 'ID' ), 'number' => - 1 );
			if ( ! $perm->can_see_others_general_stats() ) //only maybe add current user if user can't see others' stats
			{
				$args['include'] = $current_user->ID;
			}

			$all_users = get_users( $args );

			foreach ( $all_users as $user ) {
				$ID = $user->ID;

				if ( isset( $data[ $ID ] ) ) {
					continue;
				} //already in stats, don't override!

				//Set up empty total record
				$data[ $ID ]['total'] = array(
					'ppc_count'   => array(
						'normal_count' => array()
					),
					'ppc_payment' => array(
						'normal_payment' => array( 'total' => 0 )
					),
					'ppc_misc'    => array( 'posts' => 0 ),
				);

				$data[ $ID ]['total']['ppc_misc']['posts'] = 0;
			}
		}

		//AUTHOR COUNTING TYPES
		foreach ( $data as $author => &$stats ) {
			$user_settings                     = PenciPWT_General_Functions::get_settings( $author, true );
			PenciPWT_Counting_Stuff::$settings = $user_settings;

			$author_counting_types = $pencipwt_global_settings['counting_types_object']->get_active_counting_types( 'author', $author );
			foreach ( $author_counting_types as $id => $single_counting ) {
				//Counting
				$counting_type_count = 0;
				if ( ! isset( $single_counting['payment_only'] ) or $single_counting['payment_only'] == false ) {
					$counting_type_count = call_user_func( $single_counting['count_callback'], $stats, $author, $data );

					//The 'aux' index was added later to author counting types to allow them to store more complex counting data.
					//For example, Publisher Bonus stores here visits/words data so that it can calculate a bonus for them with its class payment method.
					if ( ! isset( $counting_type_count['aux'] ) ) {
						$counting_type_count['aux'] = array();
					}

					$stats['total']['ppc_count']['normal_count'][ $id ] = $counting_type_count;
				}

				//Payment
				$counting_type_payment                                  = call_user_func( $single_counting['payment_callback'], $counting_type_count, $author );
				$stats['total']['ppc_payment']['normal_payment'][ $id ] = $counting_type_payment;

				if ( isset( $stats['total']['ppc_payment']['normal_payment']['total'] ) ) {
					$stats['total']['ppc_payment']['normal_payment']['total'] += $counting_type_payment;
				} else {
					$stats['total']['ppc_payment']['normal_payment']['total'] = $counting_type_payment;
				}
			}

			//Check total threshold
			if ( pencipwt_get_setting( 'counting_payment_total_threshold' ) != 0 and isset( $stats['total']['ppc_payment']['normal_payment']['total'] ) && $stats['total']['ppc_payment']['normal_payment']['total'] ) {
				if ( $stats['total']['ppc_payment']['normal_payment']['total'] > $stats['total']['ppc_misc']['posts'] * $user_settings['counting_payment_total_threshold'] ) {
					$stats['total']['ppc_payment']['normal_payment']['total'] = $stats['total']['ppc_misc']['posts'] * $user_settings['counting_payment_total_threshold'];
				}
			}

			//Build payment tooltips
			if ( isset( $stats['total']['ppc_payment']['normal_payment'] ) and isset( $stats['total']['ppc_count']['normal_count'] ) and ! empty( $stats['total']['ppc_payment']['normal_payment'] ) and ! empty( $stats['total']['ppc_count']['normal_count'] ) ) {
				$active_counting_types_merge                          = array_merge( $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' ), $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' ) );
				$stats['total']['ppc_misc']['tooltip_normal_payment'] = PenciPWT_Counting_Stuff::build_payment_details_tooltip( $stats['total']['ppc_count']['normal_count'], $stats['total']['ppc_payment']['normal_payment'], $active_counting_types_merge );
				$stats['total']['ppc_misc']                           = apply_filters( 'ppc_stats_author_misc', $stats['total']['ppc_misc'], $author, $stats );
			}

			$stats = apply_filters( 'ppc_sort_stats_by_author_foreach_author', $stats, $author );
			//print_r($stats['total']);
		}

		return apply_filters( 'ppc_generated_raw_stats', $data );
	}

	/**
	 * Makes stats ready for output.
	 *
	 * An array is setup containing the heading columns and the rows data. These will be shown on output of any format: html, csv, pdf...
	 *
	 * @access  public
	 *
	 * @param   $data array a group_stats_by_author result
	 * @param   $author array optional whether detailed stats
	 *
	 * @return  array the formatted stats
	 * @since   2.0
	 */

	static function format_stats_for_output( $data, $author = null ) {
		global $pencipwt_global_settings;

		$formatted_stats = array(
			'cols'  => array(),
			'stats' => array()
		);

		if ( is_array( $author ) and ! empty( $author ) ) {

			foreach ( $data as $author_id_foreach => $author_stats_foreach ) {
				$author_id    = $author_id_foreach;
				$author_stats = $author_stats_foreach;
			} //list alternative
			$user_settings = PenciPWT_General_Functions::get_settings( $author_id, true );

			//if( empty( $author_stats ) ) return;
			$post_stats = current( $author_stats );

			$counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );

			$formatted_stats['cols']['post_id']               = __( 'ID', 'penci-pay-writer' );
			$formatted_stats['cols']['post_title']            = __( 'Title', 'penci-pay-writer' );
			$formatted_stats['cols']['post_type']             = __( 'Type', 'penci-pay-writer' );
			$formatted_stats['cols']['post_status']           = __( 'Status', 'penci-pay-writer' );
			$formatted_stats['cols']['post_publication_date'] = __( 'Pub. Date', 'penci-pay-writer' );

			$data_merge = array_merge( $post_stats->ppc_count['normal_count'], $post_stats->ppc_payment['normal_payment'] ); //get counting types from a random post

			/*
            // BUG: if random post has different counting types (for example because of category custom settings, then the whole thing is screwed up)
            // It doesnt work even if in the whole page there is just on different post, because then on line 357 we use this var to foreach cnt types
            */

			unset( $data_merge['total'] );

			//Add column labels for counting types
			self::get_detailed_stats_columns( $formatted_stats['cols'], $data_merge );

			foreach ( $author_stats as $key => $post ) {
				if ( $key === 'total' ) {
					continue;
				} //Skip author's total

				$post_date = explode( ' ', $post->post_date );

				$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_id']     = $post->ID;
				$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_title']  = $post->post_title;
				$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_type']   = $post->post_type;
				$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_status'] = $post->post_status;

				$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_publication_date'] = $post_date[0];

				$data_merge = array_merge( $post->ppc_count['normal_count'], $post->ppc_payment['normal_payment'] ); //get counting types for this post

				//Add column labels for counting types, if new ones are there
				self::get_detailed_stats_columns( $formatted_stats['cols'], $data_merge );

				foreach ( $data_merge as $id => $value ) { //foreach counting types in $post->ppc_* vars
					if ( isset( $counting_types[ $id ] ) ) {

						if ( isset( $counting_types[ $id ]['display_status_index'] ) and isset( $user_settings[ $counting_types[ $id ]['display_status_index'] ] ) ) //check display setting per user
						{
							$display = $user_settings[ $counting_types[ $id ]['display_status_index'] ];
						} else {
							$display = $counting_types[ $id ]['display'];
						}

						switch ( $display ) {
							case 'both':
								$formatted_stats['stats'][ $author_id ][ $post->ID ][ 'post_' . $id ] = $post->ppc_count['normal_count'][ $id ]['to_count'] . ' (' . PenciPWT_General_Functions::format_payment( sprintf( '%.2f', $post->ppc_payment['normal_payment'][ $id ] ) ) . ')';
								break;

							case 'count':
								$formatted_stats['stats'][ $author_id ][ $post->ID ][ 'post_' . $id ] = $post->ppc_count['normal_count'][ $id ]['to_count'];
								break;

							case 'payment':
								$formatted_stats['stats'][ $author_id ][ $post->ID ][ 'post_' . $id ] = PenciPWT_General_Functions::format_payment( $post->ppc_payment['normal_payment'][ $id ] );
								break;

							case 'none':
							case 'tooltip':
								//nothing to display here
								break;
						}
					}
				}

				if ( ! pencipwt_get_setting( 'hide_column_total_payment' ) ) {
					$formatted_stats['stats'][ $author_id ][ $post->ID ]['post_total_payment'] = PenciPWT_General_Functions::format_payment( $post->ppc_payment['normal_payment']['total'] - $post->ppc_payment['due_payment']['total'] );
				}

				$formatted_stats['stats'][ $author_id ][ $post->ID ] = apply_filters( 'ppc_author_stats_format_stats_after_each_default', $formatted_stats['stats'][ $author_id ][ $post->ID ], $author_id, $post );
			}

			//Cols bottom, so that Payment cols are always at the end
			if ( ! pencipwt_get_setting( 'hide_column_total_payment' ) ) {
				$formatted_stats['cols']['post_total_payment'] = __( 'Paid', 'penci-pay-writer' );
			}

			$formatted_stats['cols'] = apply_filters( 'ppc_author_stats_format_stats_after_cols_default', $formatted_stats['cols'] );

		} else {
			$cols_info = array(
				'counting_types' => array()
			); //holds info about columns. We build cols list after stats taking all unique cnt types enabled across all users. A user may have some counting types unabled, so we can't know before the end all the possible cols we may need

			foreach ( $data as $author_id => $posts ) {
				if ( ! isset( $posts['total']['ppc_payment']['normal_payment'] ) or empty( $posts['total']['ppc_payment']['normal_payment'] ) ) {
					continue;
				} //user with no counting types enabled

				$author_data           = get_userdata( $author_id );
				$user_settings         = PenciPWT_General_Functions::get_settings( $author_id, true );
				$post_counting_types   = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );
				$author_counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' );
				$counting_types        = array_merge( $post_counting_types, $author_counting_types );

				$formatted_stats['stats'][ $author_id ]['author_id'] = $author_id;
				if ( isset( $author_data->display_name ) ) {
					$formatted_stats['stats'][ $author_id ]['author_name'] = $author_data->display_name;
				}
				$formatted_stats['stats'][ $author_id ]['author_written_posts'] = (int) $posts['total']['ppc_misc']['posts'];

				$data_merge = array_merge( $posts['total']['ppc_count']['normal_count'], $posts['total']['ppc_payment']['normal_payment'] );

				foreach ( $data_merge as $id => $value ) {
					if ( isset( $counting_types[ $id ] ) ) {

						if ( isset( $counting_types[ $id ]['display_status_index'] ) and isset( $user_settings[ $counting_types[ $id ]['display_status_index'] ] ) ) //check display setting per user
						{
							$display = $user_settings[ $counting_types[ $id ]['display_status_index'] ];
						} else {
							$display = $counting_types[ $id ]['display'];
						}

						switch ( $display ) {
							case 'both':
								$formatted_stats['stats'][ $author_id ][ 'author_' . $id ] = $posts['total']['ppc_count']['normal_count'][ $id ]['to_count'] . ' (' . PenciPWT_General_Functions::format_payment( $posts['total']['ppc_payment']['normal_payment'][ $id ] ) . ')';
								break;

							case 'count':
								$formatted_stats['stats'][ $author_id ][ 'author_' . $id ] = $posts['total']['ppc_count']['normal_count'][ $id ]['to_count'];
								break;

							case 'payment':
								$formatted_stats['stats'][ $author_id ][ 'author_' . $id ] = PenciPWT_General_Functions::format_payment( $posts['total']['ppc_payment']['normal_payment'][ $id ] );
								break;

							case 'none':
							case 'tooltip':
								//nothing to display here
								break;
						}

						if ( ! isset( $cols['counting_types'][ $id ] ) ) {
							$cols_info['counting_types'][ $id ] = $counting_types[ $id ];
						}
					}
				}

				if ( ! pencipwt_get_setting( 'hide_column_total_payment' ) ) {
					$formatted_stats['stats'][ $author_id ]['author_total_payment'] = PenciPWT_General_Functions::format_payment( (int) $posts['total']['ppc_payment']['normal_payment']['total'] - (int) $posts['total']['ppc_payment']['due_payment']['total'] );
				}

				$formatted_stats['stats'][ $author_id ] = apply_filters( 'ppc_general_stats_format_stats_after_each_default', $formatted_stats['stats'][ $author_id ], $author_id, $posts );
			}


			//COLUMNS
			$formatted_stats['cols']['author_id']            = __( 'Author ID', 'penci-pay-writer' );
			$formatted_stats['cols']['author_name']          = __( 'Author Name', 'penci-pay-writer' );
			$formatted_stats['cols']['author_written_posts'] = __( 'Total Posts', 'penci-pay-writer' );

			foreach ( $cols_info['counting_types'] as $id => $cnt_type ) {
				switch ( $cnt_type['display'] ) {
					case 'none':
					case 'tooltip':
						//nothing to display here
						break;

					default:
						$formatted_stats['cols'][ 'author_' . $id ] = $cnt_type['label'];
						break;
				}
			}

			if ( ! pencipwt_get_setting( 'hide_column_total_payment' ) ) {
				$formatted_stats['cols']['author_total_payment'] = __( 'Paid', 'penci-pay-writer' );
			}

			$formatted_stats['cols'] = apply_filters( 'ppc_general_stats_format_stats_after_cols_default', $formatted_stats['cols'] );

		}

		return apply_filters( 'ppc_formatted_stats', $formatted_stats );
	}

	/**
	 * Builds detailed stats columns array incrementally.
	 *
	 * Since each post can have different cnt types enabled (for example
	 * because of Category custom settings), every post must be able to
	 * contribute to the table columns.
	 *
	 * @param    &$columns array current columns
	 * @param    $maybe_add array columns current post contributes
	 *
	 * @return    void
	 * @since    2.710
	 */
	static function get_detailed_stats_columns( &$columns, $maybe_add ) {
		global $pencipwt_global_settings;

		$cols           = array();
		$counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );

		foreach ( $maybe_add as $id => $value ) {
			if ( isset( $counting_types[ $id ] ) ) {
				switch ( $counting_types[ $id ]['display'] ) {
					case 'none':
					case 'tooltip':
						//nothing to display here
						break;

					default:
						$cols[ 'post_' . $id ] = $counting_types[ $id ]['label'];
						break;
				}
			}
		}

		$columns = array_merge( $columns, $cols );
	}

	/**
	 * Computes overall stats.
	 *
	 * @access  public
	 *
	 * @param   $data array a group_stats_by_author result
	 *
	 * @return  array the overall stats
	 * @since   2.0
	 */

	static function get_overall_stats( $stats ) {
		$overall_stats = array(
			'posts'         => 0,
			'total_payment' => 0,
			'payment'       => array(),
			'count'         => array()
		);

		$due_payment = 0;

		foreach ( $stats as $single ) {
			//Posts total count
			$overall_stats['posts'] += $single['total']['ppc_misc']['posts'];

			//Total payment
			$overall_stats['total_payment'] += $single['total']['ppc_payment']['normal_payment']['total'];

			//Total counts
			if ( isset( $single['total'] ) and isset( $single['total']['ppc_count'] ) and isset( $single['total']['ppc_count']['normal_count'] ) ) {
				foreach ( $single['total']['ppc_count']['normal_count'] as $key => $data ) {
					if ( ! isset( $overall_stats['count'][ $key ] ) ) {
						$overall_stats['count'][ $key ] = $data['to_count'];
					} else {
						$overall_stats['count'][ $key ] += $data['to_count'];
					}
				}
			}

			//Total payments
			if ( isset( $single['total'] ) and isset( $single['total']['ppc_payment'] ) and isset( $single['total']['ppc_payment']['normal_payment'] ) ) {
				foreach ( $single['total']['ppc_payment']['normal_payment'] as $key => $data ) {
					if ( $key == 'total' ) {
						continue;
					} //skip total payment

					if ( ! isset( $overall_stats['payment'][ $key ] ) ) {
						$overall_stats['payment'][ $key ] = $data;
					} else {
						$overall_stats['payment'][ $key ] += $data;
					}
				}
			}

			// Total Paid
			if ( isset( $single['total']['ppc_payment']['due_payment']['total'] ) ) {
				$due_payment += $single['total']['ppc_payment']['due_payment']['total'];
			}
		}

		$overall_stats['total_paid'] = $overall_stats['total_payment'] - $due_payment;

		return apply_filters( 'ppc_overall_stats', $overall_stats, $stats );
	}
}
