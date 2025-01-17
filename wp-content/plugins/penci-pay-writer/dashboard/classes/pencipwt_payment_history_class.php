<?php

class PenciPWT_Payment_History {

	/**
	 * Holds current-stats-view post payment histories. Fetched at page load.
	 */
	public static $post_payment_histories = array();

	/**
	 * Holds current-stats-view author payment histories. Fetched at page load.
	 */
	public static $author_payment_histories = array();

	/**
	 * Holds general payment history option. Fetched at page load.
	 */
	public static $general_payment_history = array();

	/**
	 * Gets transaction. Mixes posts history, author history and transaction details.
	 *
	 * Outputs an array in the form
	 *    'transaction' => transaction details
	 *    'authors_info' => array
	 *        author_ID => 'author_name'
	 *    'posts_ids' => paid posts ids (array)
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @return  array transaction + posts_ids + authors_info
	 * @since   1.5
	 */
	static function get_transaction( $tracking_id ) {
		//POSTS HISTORIES
		$posts_by_tracking_id = self::get_posts_payment_histories_by_tracking_id( $tracking_id );
		$posts_ids            = array();
		//$posts_payment_histories = array();

		//Build array of unserialized records to be summed up as well as array of posts ids
		foreach ( $posts_by_tracking_id as $single ) {
			//$single_payment_history = maybe_unserialize( $single->meta_value );
			//$posts_payment_histories[$tracking_id][] = $single_payment_history[$tracking_id];
			$posts_ids[] = $single->post_id;
		}

		//$posts_overall_history = self::sum_up_posts_payment_history( $posts_payment_histories );

		//AUTHOR HISTORIES
		$authors_by_tracking_id = self::get_authors_payment_histories_by_tracking_id( $tracking_id );
		$authors_info           = array();
		//$authors_payment_histories = array();

		//Build array of unserialized records to be summed up as well as array of authors ids => array( names )
		foreach ( $authors_by_tracking_id as $single ) {
			//$single_payment_history = maybe_unserialize( $single->meta_value );
			//$authors_payment_histories[$tracking_id][] = $single_payment_history[$tracking_id];
			$authors_info[ $single->user_id ] = array( 'name' => $single->display_name );
		}

		//$authors_overall_history = self::sum_up_posts_payment_history( $authors_payment_histories );

		//TRANSACTION DETAILS
		$transaction_details = self::get_transaction_details( $tracking_id );

		//FINAL MERGING
		//$transaction = array_merge_recursive( $posts_overall_history, $transaction_details );
		//$transaction = self::sort_payment_history_by_time( $transaction );

		//if( count( $authors_overall_history ) > 0 )
		//  $transaction = array_merge_recursive( $transaction, $authors_payment_histories );

		return apply_filters( 'ppcp_get_transaction_result', array(
			'transaction'  => $transaction_details,
			'posts_ids'    => $posts_ids,
			'authors_info' => $authors_info
		) );
	}

	/**
	 * Gets requested posts payment histories.
	 * Runs after WP_Post array is selected.
	 *
	 * Hooks to PenciPWT_Generate_Stats::get_requested_posts - ppc_got_requested_posts
	 *
	 * @access  public
	 *
	 * @param    $requested_posts array requested posts
	 *
	 * @since   1.5
	 */

	static function get_all_requested_posts_payment_histories( $requested_posts ) {
		global $pencipwt_global_settings, $wpdb;

		$post_ids = array();
		foreach ( $requested_posts->posts as $single ) {
			$post_ids[] = $single->ID;
		}

		$query = 'SELECT ' . $wpdb->postmeta . '.post_id, ' . $wpdb->postmeta . '.meta_value FROM ' . $wpdb->postmeta . ' WHERE ' . $wpdb->postmeta . '.meta_key = "' . $pencipwt_global_settings['payment_history_field'] . '" AND ' . $wpdb->postmeta . '.post_id IN(' . implode( ',', $post_ids ) . ')';

		self::$post_payment_histories = $wpdb->get_results( $query, OBJECT_K );
	}

	/**
	 * Returns the payment history for the given post, taking it from class var if available.
	 *
	 * @access  public
	 *
	 * @param    $post_id int post id
	 *
	 * @return    array payment history or empty if unavailable
	 * @since   1.5
	 */

	static function get_post_payment_history( $post_id ) {
		global $pencipwt_global_settings, $wpdb;

		//See if class var has this history ready
		if ( isset( self::$post_payment_histories[ $post_id ] ) ) {
			return array_reverse( maybe_unserialize( self::$post_payment_histories[ $post_id ]->meta_value ) );

		} else {
			$history = get_post_meta( $post_id, $pencipwt_global_settings['payment_history_field'], true );


			//If no history available, return empty array
			if ( $history === false or empty( $history ) ) {
				return array();

				//Merge post history with general transaction details
			} else {
				$history = array_reverse( maybe_unserialize( $history ) ); //sort by date desc. Need current() due to pay history form a:1:{i:0;a:4:{...

				foreach ( $history as $tracking_id => $single ) {
					$general_history = self::get_transaction_details( $tracking_id );
					if ( is_wp_error( $general_history ) ) {
						return $general_history;
					}

					$history[ $tracking_id ] = array_merge( $single, $general_history );
				}

				return $history;
			}
		}
	}

	/**
	 * Gets all posts payment histories by tracking id (i.e. gets posts paid in the same transaction).
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @return  array payment histories
	 * @since   1.5
	 */

	static function get_posts_payment_histories_by_tracking_id( $tracking_id ) {
		global $pencipwt_global_settings, $wpdb;

		$query = 'SELECT ' . $wpdb->postmeta . '.meta_value, ' . $wpdb->postmeta . '.post_id, ' . $wpdb->posts . '.post_author
					FROM ' . $wpdb->postmeta . ', ' . $wpdb->posts . '
					WHERE ' . $wpdb->postmeta . '.post_id = ' . $wpdb->posts . '.ID
					AND ' . $wpdb->postmeta . '.meta_key = "' . $pencipwt_global_settings['payment_history_field'] . '"
					AND ' . $wpdb->postmeta . '.meta_value LIKE "%' . $tracking_id . '%"
					ORDER BY ' . $wpdb->postmeta . '.meta_id';

		return $wpdb->get_results( $query );
	}

	/**
	 * For each post, the payment history is taken and a new record with the current payment data is added.
	 *
	 * @access  public
	 *
	 * @param   $payment_data array a PenciPWT_Payment::prepare_payment['posts_for_payment_history_data'] result
	 *
	 * @since   1.5
	 */

	static function post_payment_history_add_new_record( $payment_data ) {
		global $pencipwt_global_settings;

		if ( empty( $payment_data ) ) {
			return;
		}

		foreach ( $payment_data as $post_id => $single ) {
			$payment_history = self::get_post_payment_history( $post_id ); //get current history
			if ( is_wp_error( $payment_history ) ) {
				return $payment_history;
			}

			$tracking_id                        = array_keys( $single );
			$payment_history[ $tracking_id[0] ] = $single[ $tracking_id[0] ]; //append new record

			$update = self::update_post_payment_history( $post_id, $payment_history );
			if ( is_wp_error( $update ) ) {
				return $update;
			}

			self::$post_payment_histories[ $post_id ] = $payment_history; //update class var
		}

		do_action( 'ppcp_updated_post_payment_history', $post_id );
	}

	/**
	 * Updates postmeta holding post payment history.
	 *
	 * @access  public
	 *
	 * @param   $post_id int post id
	 * @param   $payment_history array new payment history
	 *
	 * @since   1.5
	 */

	static function update_post_payment_history( $post_id, $payment_history ) {
		global $pencipwt_global_settings;

		//If payment history is empty array, delete postmeta
		if ( empty( $payment_history ) ) {
			if ( false === delete_post_meta( $post_id, $pencipwt_global_settings['payment_history_field'] ) ) { //delete; throw error is unsuccessful
				return false;
			}
		}

		if ( false === update_post_meta( $post_id, $pencipwt_global_settings['payment_history_field'], $payment_history ) ) { //update; throw error is unsuccessful
			return false;
		}

		self::$post_payment_histories[ $post_id ] = $payment_history; //update class var

		if ( method_exists( 'PenciPWT_Cache_Functions', 'clear_post_stats' ) ) {
			PenciPWT_Cache_Functions::clear_post_stats( $post_id );
		}

		do_action( 'ppcp_updated_post_payment_history', $post_id );
	}

	/**
	 * Deletes post payment history record.
	 *
	 * @access  public
	 *
	 * @param   $post_id int post id
	 * @param   $tracking_id string tracking id
	 *
	 * @since   1.5
	 */

	static function delete_post_payment_history_item( $post_id, $tracking_id ) {
		$payment_history = self::get_post_payment_history( $post_id );

		if ( ! isset( $payment_history[ $tracking_id ] ) ) {
			return;
		}

		unset( $payment_history[ $tracking_id ] );

		$update = self::update_post_payment_history( $post_id, $payment_history );
		if ( is_wp_error( $update ) ) {
			return $update;
		}

		//Update class var
		if ( isset( self::$post_payment_histories[ $post_id ][ $tracking_id ] ) ) {
			unset( self::$post_payment_histories[ $post_id ][ $tracking_id ] );
		}

		do_action( 'ppcp_post_payment_history_deleted_item' );
	}

	/**
	 * Retrives posts belonging to the given author in the given tracking id.
	 *
	 * @param    $author_id int
	 * @param    $tracking_id string
	 *
	 * @return    array posts belonging to $author and paid in $tracking_id
	 * @since    1.6.9
	 */
	static function get_author_transaction_paid_posts( $author_id, $tracking_id ) {
		global $wpdb;

		$query = 'SELECT ' . $wpdb->postmeta . '.post_id
					FROM ' . $wpdb->postmeta . ', ' . $wpdb->posts . '
					WHERE ' . $wpdb->postmeta . '.post_id = ' . $wpdb->posts . '.ID
					AND ' . $wpdb->posts . '.post_author = ' . $author_id . '
					AND ' . $wpdb->postmeta . '.meta_value LIKE "%' . $tracking_id . '%"
					ORDER BY ' . $wpdb->postmeta . '.meta_id';

		$result = $wpdb->get_results( $query );

		$posts_ids = array();
		foreach ( $result as $single ) {
			$posts_ids[] = $single->post_id;
		}

		return $posts_ids;
	}

	/**
	 * Returns the payment history for the given author by summing up all author's posts payment histories.
	 *
	 * @access  public
	 *
	 * @param    $author_id int author id
	 * @param   $time_restrict bool optional whether only posts in current stats time range should be selected
	 * @param   $post_restrict array optional whether only given posts should be selected
	 *
	 * @return    array payment history or empty if unavailable
	 * @since   1.5
	 */

	static function sum_up_author_posts_payment_history( $author, $time_restrict = false, $post_restrict = false ) {
		$author_payment_history = array();

		$all_user_postmeta = self::get_author_all_posts_payment_histories( $author, $time_restrict, $post_restrict );

		//Build array of unserialized records to be summed up
		foreach ( $all_user_postmeta as $post_payment_history ) { //post payment history
			$post_payment_history_content = maybe_unserialize( $post_payment_history->meta_value );
			if ( empty( $post_payment_history_content ) ) {
				continue;
			} //in unlikely case of empty record

			$tracking_ids = array_keys( $post_payment_history_content );
			foreach ( $tracking_ids as $tracking_id ) {
				$author_payment_history[ $tracking_id ][ $post_payment_history->post_id ] = $post_payment_history_content[ $tracking_id ];
			}
		}

		$sum_up_payment_history = self::sum_up_posts_payment_history( $author_payment_history );

		return $sum_up_payment_history;
	}

	/**
	 * Returns the overall payment history by summing up all given ones.
	 *
	 * @access  public
	 *
	 * @param    $payment_histories array payment histories
	 *
	 * @return    array overall payment history
	 * @since   1.5
	 */

	static function sum_up_posts_payment_history( $payment_histories ) {
		$payment_history = array();

		foreach ( $payment_histories as $tracking_id => $single_group ) { //all records belonging to same tracking i

			foreach ( $single_group as $post_id => $single ) { //single post payment history record

				//Sum up counts and amounts
				foreach ( $single['ppc_count'] as $key => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $payment_history[ $tracking_id ]['ppc_count'][ $key ] ) ) {
						$payment_history[ $tracking_id ]['ppc_count'][ $key ] = $value;
					} else {
						$payment_history[ $tracking_id ]['ppc_count'][ $key ] += $value;
					}
				}

				foreach ( $single['ppc_payment'] as $key => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $payment_history[ $tracking_id ]['ppc_payment'][ $key ] ) ) {
						$payment_history[ $tracking_id ]['ppc_payment'][ $key ] = $value;
					} else {
						$payment_history[ $tracking_id ]['ppc_payment'][ $key ] += $value;
					}
				}

				//Retro-caomptibiliry
				if ( isset( $single['time'] ) ) {
					$payment_history[ $tracking_id ]['time'] = $single['time'];
				}
				if ( isset( $single['verified'] ) ) {
					$payment_history[ $tracking_id ]['verified'] = $single['verified'];
				}
			}

		}

		return $payment_history;
	}

	/**
	 * Returns all author's posts payment histories.
	 *
	 * @access  public
	 *
	 * @param    $author int author id
	 * @param   $time_restrict bool optional whether only posts in current stats time range should be selected
	 * @param   $post_restrict array optional whether only given posts should be selected
	 *
	 * @return    array post payment histories
	 * @since   1.0
	 */

	static function get_author_all_posts_payment_histories( $author, $time_restrict = false, $post_restrict = false ) {
		global $wpdb, $pencipwt_global_settings, $wpdb;

		//Maybe restrict query selection time to stats current view time range
		$where = '';
		if ( $time_restrict and isset( $pencipwt_global_settings['stats_tstart'] ) and isset( $pencipwt_global_settings['stats_tend'] ) ) {
			$where .= ' AND (' . $wpdb->posts . '.post_date BETWEEN "' . date( 'Y-m-d H:i:s', $pencipwt_global_settings['stats_tstart'] ) . '" AND "' . date( 'Y-m-d H:i:s', $pencipwt_global_settings['stats_tend'] ) . '") ';
		}

		//Maybe restrict query selection to given post ids
		if ( is_array( $post_restrict ) ) {
			$total_index = array_keys( $post_restrict, 'total' );
			if ( in_array( 'total', $post_restrict ) ) {
				unset( $post_restrict[ $total_index[0] ] );
			}

			if ( count( $post_restrict ) > 0 ) {
				$where .= ' AND ' . $wpdb->postmeta . '.post_id IN(' . implode( ',', $post_restrict ) . ') ';
			}
		}

		$query = 'SELECT ' . $wpdb->postmeta . '.meta_value, ' . $wpdb->postmeta . '.post_id
					FROM ' . $wpdb->postmeta . ', ' . $wpdb->posts . '
					WHERE ' . $wpdb->postmeta . '.post_id = ' . $wpdb->posts . '.ID
					AND ' . $wpdb->postmeta . '.meta_key = "' . $pencipwt_global_settings['payment_history_field'] . '"
					AND ' . $wpdb->posts . '.post_author = ' . $author . ' '
		         . $where .
		         'ORDER BY ' . $wpdb->postmeta . '.meta_id';

		return $wpdb->get_results( $query );
	}

	/**
	 * Gets all authors payment histories by tracking id (i.e. gets authors paid in the same transaction).
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @return  array payment histories
	 * @since   1.5
	 */

	static function get_authors_payment_histories_by_tracking_id( $tracking_id ) {
		global $pencipwt_global_settings, $wpdb;

		$query = 'SELECT ' . $wpdb->usermeta . '.meta_value, ' . $wpdb->usermeta . '.user_id, ' . $wpdb->users . '.display_name
					FROM ' . $wpdb->usermeta . ', ' . $wpdb->users . '
					WHERE ' . $wpdb->usermeta . '.user_id = ' . $wpdb->users . '.ID
					AND ' . $wpdb->usermeta . '.meta_key = "' . $pencipwt_global_settings['payment_history_field'] . '"
					AND ' . $wpdb->usermeta . '.meta_value LIKE "%' . $tracking_id . '%"
					ORDER BY ' . $wpdb->users . '.ID';

		return $wpdb->get_results( $query );
	}

	/**
	 * Returns given author payment history by merging his posts payment histories and his own author history one.
	 *
	 * @access  public
	 *
	 * @param    $author int author id
	 * @param   $time_restrict bool optional whether only posts in current stats time range should be selected
	 * @param   $post_restrict array optional whether only given posts should be selected
	 *
	 * @return    array payment history
	 * @since   1.5
	 */

	static function get_author_payment_history( $author, $time_restrict = false, $post_restrict = false ) {
		global $pencipwt_global_settings;

		if ( empty( $author ) ) {
			return new WP_Error( 'ppcp_get_author_payment_history_invalid_args', '$author cannot be empty' );
		}

		//Maybe restrict query selection time to stats current view time range OR post ids
		$posts_history = self::sum_up_author_posts_payment_history( $author, $time_restrict, $post_restrict );

		//Get author (usermeta) payment history
		$author_history = self::get_author_strict_payment_history( $author );
		if ( is_wp_error( $author_history ) ) {
			return $author_history;
		}

		$posts_tracking_ids  = array_keys( $posts_history );
		$author_tracking_ids = array_keys( $author_history );
		$tracking_ids        = array();

		foreach ( $posts_tracking_ids as $single ) {
			if ( ! in_array( $single, $tracking_ids ) ) {
				$tracking_ids[] = $single;
			}
		}

		foreach ( $author_tracking_ids as $single ) {
			if ( ! in_array( $single, $tracking_ids ) ) {
				$tracking_ids[] = $single;
			}
		}

		$payment_history = $posts_history;
		foreach ( $tracking_ids as $single ) {
			$transaction_details = self::get_transaction_details( $single );
			if ( is_wp_error( $transaction_details ) ) {
				return $transaction_details;
			}

			//Merge with author's history, if anything and if tehre's not only the total payment field
			if ( isset( $author_history[ $single ] ) and count( $author_history[ $single ]['ppc_payment'] ) > 1 ) {

				//Sum up counts and amounts
				if ( isset( $author_history[ $single ]['ppc_count'] ) ) {
					foreach ( $author_history[ $single ]['ppc_count'] as $key => $value ) {
						//Avoid notices of non isset index
						if ( ! isset( $payment_history[ $single ]['ppc_count'][ $key ] ) ) {
							$payment_history[ $single ]['ppc_count'][ $key ] = $value;
						} else {
							$payment_history[ $single ]['ppc_count'][ $key ] += $value;
						}
					}
				}

				foreach ( $author_history[ $single ]['ppc_payment'] as $key => $value ) {
					//Avoid notices of non isset index
					if ( ! isset( $payment_history[ $single ]['ppc_payment'][ $key ] ) ) {
						$payment_history[ $single ]['ppc_payment'][ $key ] = $value;
					} else {
						$payment_history[ $single ]['ppc_payment'][ $key ] += $value;
					}
				}
			}

			//Merge with transaction details
			if ( isset( $payment_history[ $single ] ) ) {
				$payment_history[ $single ] = array_replace_recursive( $payment_history[ $single ], $transaction_details );

				if ( isset( $author_history[ $single ]['ppc_misc'] ) ) {
					$payment_history[ $single ]['ppc_misc'] = $author_history[ $single ]['ppc_misc'];
				}
			}
		}

		return $payment_history;
	}

	/**
	 * Returns given author payment history (ie the usermeta).
	 *
	 * @access  public
	 *
	 * @param    $author int author id
	 *
	 * @return    array payment history
	 * @since   1.5
	 */

	static function get_author_strict_payment_history( $author ) {
		global $pencipwt_global_settings;

		//See if class var has this history ready
		if ( isset( self::$author_payment_histories[ $author ] ) ) {
			return array_reverse( maybe_unserialize( self::$author_payment_histories[ $author ]->meta_value ) );

		} else {
			$history = get_user_meta( $author, $pencipwt_global_settings['payment_history_field'] );


			//If no history available, return empty array
			if ( $history === false or count( $history ) == 0 ) {
				return array();

				//Merge post history with general transaction details
			} else {
				$history = array_reverse( current( $history ) ); //sort by date desc. Need current() due to pay history form a:1:{i:0;a:4:{...

				foreach ( $history as $tracking_id => $single ) {
					$general_history         = self::get_transaction_details( $tracking_id );
					$history[ $tracking_id ] = array_merge( $single, $general_history );
				}

				return $history;
			}
		}
	}

	/**
	 * For each author, the payment history is taken and a new record with the current payment data is added.
	 *
	 * @access  public
	 *
	 * @param   $payment_data array a prepare_payment['authors_for_payment_history_data'] result
	 *
	 * @since   1.5
	 */

	static function author_payment_history_add_new_record( $payment_data ) {
		if ( empty( $payment_data ) ) {
			return;
		}

		foreach ( $payment_data as $author_id => $single ) {
			$payment_history = self::get_author_strict_payment_history( $author_id ); //get current history
			if ( is_wp_error( $payment_history ) ) {
				return $payment_history;
			}

			$payment_history = array_merge( $payment_history, $single ); //append new record

			$update = self::update_author_payment_history( $author_id, $payment_history );
			if ( is_wp_error( $update ) ) {
				return $update;
			}
		}
	}

	/**
	 * Deletes author payment history record and all related posts records.
	 *
	 * @access  public
	 *
	 * @param   $author_id int author id
	 * @param   $tracking_id string tracking id
	 *
	 * @since   1.5
	 */

	static function delete_author_payment_history_item( $author_id, $tracking_id ) {
		$posts_payment_histories = self::get_author_all_posts_payment_histories( $author_id );
		if ( is_wp_error( $posts_payment_histories ) ) {
			return $posts_payment_histories;
		}

		$author_payment_history = self::get_author_strict_payment_history( $author_id );
		if ( is_wp_error( $author_payment_history ) ) {
			return $author_payment_history;
		}

		//Author postmeta
		if ( array_key_exists( $tracking_id, $author_payment_history ) ) {
			unset( $author_payment_history[ $tracking_id ] );

			$update_author = self::update_author_payment_history( $author_id, $author_payment_history );
			if ( is_wp_error( $update_author ) ) {
				return $update_author;
			}

			//Update class var
			if ( isset( self::$author_payment_histories[ $author_id ][ $tracking_id ] ) ) {
				unset( self::$author_payment_histories[ $author_id ][ $tracking_id ] );
			}
		}

		do_action( 'ppcp_author_payment_history_deleted_item' );
	}

	/**
	 * Updates author usermeta holding payment history.
	 *
	 * @access  public
	 *
	 * @param   $payment_history array new payment history
	 *
	 * @since   1.5
	 */

	static function update_author_payment_history( $author_id, $payment_history ) {
		global $pencipwt_global_settings;

		//If empty payment history is given and usermeta exists, delete usermeta
		if ( empty( $payment_history ) and get_user_meta( $author_id, $pencipwt_global_settings['payment_history_field'] ) != false ) {


			unset( self::$author_payment_histories[ $author_id ] ); //update class var
		}

		if ( false === update_user_meta( $author_id, $pencipwt_global_settings['payment_history_field'], $payment_history ) ) { //update usermeta; throw error is unsuccessful


			self::$author_payment_histories[ $author_id ] = $payment_history; //update class var
		}

		do_action( 'ppcp_updated_author_payment_history', $author_id );
	}

	/**
	 * Returns general transaction history (method, note, time, verified and other related stuff).
	 *
	 * @access  public
	 * @return    array general payment history
	 * @since   1.5
	 */

	static function get_transaction_history() {
		global $pencipwt_global_settings;

		//See if class var has history ready
		if ( isset( self::$general_payment_history ) and ! empty( self::$general_payment_history ) ) {
			return self::$general_payment_history;

		} else {
			$general_history = get_option( $pencipwt_global_settings['payment_history_field'] );

			//If no history, return empty array
			if ( empty( $general_history ) or ! is_array( $general_history ) ) {
				$general_history = array();
			}

			self::$general_payment_history = $general_history; //update class var

			return $general_history;
		}
	}

	/**
	 * Returns given tracking id general transaction details (method, note, time, verified and other related stuff).
	 *
	 * @access  public
	 *
	 * @param    $tracking_id string transaction plugin tracking id
	 *
	 * @return    array general payment history
	 * @since   1.5
	 */

	static function get_transaction_details( $tracking_id ) {
		$general_history = self::get_transaction_history();
		if ( is_wp_error( $general_history ) ) {
			return $general_history;
		}

		if ( isset( $general_history[ $tracking_id ] ) ) {
			return $general_history[ $tracking_id ];
		} else {
			return array();
		}
	}

	/**
	 * Stores current payment in general payment history option (=transaction).
	 * Keeps time, payment method, payment note, verified status.
	 *
	 * @access  public
	 *
	 * @param   $payment_data array a prepare_payment['transaction_details'] result
	 *
	 * @since   1.5
	 */

	static function update_transaction_details( $payment_data ) {
		global $pencipwt_global_settings;

		if ( empty( $payment_data ) ) {
			return;
		}

		$payment_history = self::get_transaction_history(); //get current history
		if ( is_wp_error( $payment_history ) ) {
			return $payment_history;
		}

		$payment_history[ $payment_data['tracking_id'] ] = $payment_data; //append new record

		$update = self::update_transactions_option( $payment_history );
		if ( is_wp_error( $update ) ) {
			return $update;
		}
	}

	/**
	 * Deletes transaction details.
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @since   1.5
	 */

	static function delete_transaction_details( $tracking_id ) {
		global $pencipwt_global_settings;

		$payment_history = self::get_transaction_history(); //get current history
		if ( is_wp_error( $payment_history ) ) {
			return $payment_history;
		}

		if ( isset( $payment_history[ $tracking_id ] ) ) //delete record
		{
			unset( $payment_history[ $tracking_id ] );
		}

		$update = self::update_transactions_option( $payment_history );
		if ( is_wp_error( $update ) ) {
			return $update;
		}
	}

	/**
	 * Deletes transaction details if no more posts/authors are attached to it.
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @since   1.5
	 */

	static function maybe_delete_transaction_details( $tracking_id ) {
		$transaction = self::get_transaction( $tracking_id ); //get current history
		if ( is_wp_error( $transaction ) ) {
			return $transaction;
		}

		if ( count( $transaction['posts_ids'] ) == 0 and count( $transaction['authors_info'] ) == 0 ) {
			$delete = self::delete_transaction_details( $tracking_id );
			if ( is_wp_error( $delete ) ) {
				return $delete;
			}
		}
	}

	/**
	 * Deletes a transaction (details, posts history, authors history, everything!)
	 *
	 * If something fails for whatever reason, we rollback and add everything back.
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string tracking id
	 *
	 * @since   1.5.9.3
	 */

	static function delete_transaction( $tracking_id ) {
		$transaction = self::get_transaction( $tracking_id );

		if ( empty( $transaction['transaction'] ) and empty( $transaction['posts_ids'] ) and empty( $transaction['authors_info'] ) ) {
			return;
		}

		$posts_history = array();
		foreach ( $transaction['posts_ids'] as $post_id ) {
			$posts_history[ $post_id ] = self::get_post_payment_history( $post_id );
			if ( is_wp_error( self::delete_post_payment_history_item( $post_id, $tracking_id ) ) ) {
				$error_delete_post = true;
			}
		}

		$authors_history = array();
		foreach ( $transaction['authors_info'] as $user_id => $user_data ) {
			$authors_history[ $user_id ] = self::get_author_strict_payment_history( $user_id );
			if ( is_wp_error( self::delete_author_payment_history_item( $user_id, $tracking_id ) ) ) {
				$error_delete_author = true;
			}
		}

		$transactions = self::get_transaction_history();
		if ( is_wp_error( self::delete_transaction_details( $tracking_id ) ) ) {
			$error_delete_transaction = true;
		}

		//Rollback data on error
		if ( $error_delete_post or $error_delete_author or $error_delete_transaction ) {
			if ( is_wp_error( $rollback = self::delete_transaction_rollback( $transactions, $posts_history, $authors_history ) ) ) {
				return $rollback;
			}

			return new WP_Error( 'ppcp_delete_transaction_error_rolled_back', __( 'There was an issue deleting the transaction. Data hasn\'t been damaged though.', 'penci-pay-writer' ) );
		}
	}

	/**
	 * If something goes wrong while deleting a transaction, we put all the data back where it was.
	 *
	 * @access  public
	 *
	 * @param   $transactions array
	 * @param    $posts_history array
	 * @param    $authors_history array
	 *
	 * @since   1.5.9.3
	 */

	static function delete_transaction_rollback( $transactions, $posts_history, $authors_history ) {


	}

	/**
	 * Updates transactions option.
	 *
	 * @access  public
	 *
	 * @param   $transactions array transactions
	 *
	 * @since   1.5
	 */
	static function update_transactions_option( $transactions ) {
		global $pencipwt_global_settings;

		if ( false === update_option( $pencipwt_global_settings['payment_history_field'], $transactions ) ) { //update option; throw error if unsuccessful
			return false;
		}

		self::$general_payment_history = $transactions; //update class var

		do_action( 'ppcp_updated_general_payment_history' );
	}

	/**
	 * Sorts payment history records by time DESC.
	 *
	 * @access  public
	 *
	 * @param   $payment_history array payment history
	 *
	 * @return  array the same payment history, but sorted
	 * @since   1.5
	 */
	static function sort_payment_history_by_time( $payment_history ) {

		if ( is_array( $payment_history ) ) {
			uasort( $payment_history, array( 'PenciPWT_Payment_History', 'compare_elements_time' ) );
		}

		return $payment_history;
	}

	/**
	 * Compares elements time, newer is greater.
	 *
	 * @access  public
	 *
	 * @param   $first array first payment history record
	 * @param   $second array second payment history record
	 *
	 * @return  int -1 if first > second, 0 if equal, 1 if first < second
	 * @since   1.5
	 */
	static function compare_elements_time( $first, $second ) {
		if ( $first['time'] > $second['time'] ) {
			return - 1;
		} else if ( $first['time'] == $second['time'] ) {
			return 0;
		} else if ( $first['time'] < $second['time'] ) {
			return 1;
		}
	}
}
