<?php

class PenciPWT_Stats {

	/**
	 * Removes excluded posts from WP_Query request.
	 *
	 * Hooks to PenciPWT_Generate_Stats::get_requested_posts() - ppc_get_requested_posts_args.
	 *
	 * @access  public
	 *
	 * @param    $args array arguments of the WP_Query call in get_requested_posts
	 *
	 * @return    array arguments
	 * @since   1.1
	 */

	static function remove_excluded_posts( $args ) {
		global $pencipwt_global_settings;

		//Get excluded posts
		$exclude_args = array(
			'fields'         => 'ids',
			'posts_per_page' => - 1,
			'post_type'      => 'any',
			'meta_key'       => $pencipwt_global_settings['option_exclude_post']
		);
		$posts        = new WP_Query( $exclude_args );

		//Add their ids to the query args
		if ( $posts->post_count != 0 ) {
			$args['post__not_in'] = $posts->posts;
		}

		return $args;
	}

	/**
	 * Modifies default get_requested_posts behaviour, allows selection by array of post ids
	 *
	 * Hooks to PenciPWT_Generate_Stats::get_requested_posts().
	 *
	 * @access  public
	 *
	 * @param    $args array WP_Query args
	 *
	 * @return    array WP_Query args
	 * @since   1.0
	 */
	static function get_requested_posts_by_id( $args ) {
		global $pencipwt_global_settings;

		$args = array(
			'post__in'            => $pencipwt_global_settings['temp']['get_requested_posts_post_ids'],
			'post_type'           => 'any',
			'post_status'         => 'any',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => - 1,
			'suppress_filters'    => false
		);

		return $args;
	}

	/**
	 * Generates stats for given post ids.
	 *
	 * @access  public
	 *
	 * @param   $post_ids array post ids to get stats of
	 *
	 * @return  array stats
	 * @since   1.5
	 */
	static function get_stats_by_post_ids( $posts_ids ) {
		global $pencipwt_global_settings;

		if ( empty( $posts_ids ) ) {
			return;
		}

		$args = array(
			'post__in'            => $posts_ids,
			'post_type'           => 'any',
			'post_status'         => array( 'publish', 'pending', 'future', 'private' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => - 1,
			'suppress_filters'    => false
		);

		$requested_posts = new WP_Query( $args );

		do_action( 'ppc_got_requested_posts', $requested_posts );


		$stats = PenciPWT_Generate_Stats::group_stats_by_author( $requested_posts->posts );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$stats = PenciPWT_Counting_Stuff::data2cash( $stats, array() );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$stats = PenciPWT_Generate_Stats::calculate_total_stats( $stats );
		if ( is_wp_error( $stats ) ) {
			return $stats;
		}

		$return['raw_stats'] = $stats;

		$formatted_stats = PenciPWT_Generate_Stats::format_stats_for_output( $stats, array() );
		if ( is_wp_error( $formatted_stats ) ) {
			return $formatted_stats;
		}

		$return['formatted_stats'] = $formatted_stats;

		return $return;
	}

	/**
	 * Adds due payment to author total stats.
	 *
	 * Hooks to PenciPWT_Generate_Stats::group_stats_by_author() - ppc_sort_stats_by_author_foreach_author.
	 *
	 * @access  public
	 *
	 * @param    $stats array current author stats
	 * @param   $author int current author id
	 * @param    $user_settings array currently-in-use user settings
	 *
	 * @return    array stats
	 * @since   1.0
	 */
	static function get_author_due_payment( $stats, $author ) {
		global $pencipwt_global_settings;

		//Don't run in detailed stats
		//if( $pencipwt_global_settings['current_page'] == 'stats_detailed' ) return $stats;

		$payment_history = PenciPWT_Payment_History::get_author_payment_history( $author, true, array_keys( $stats ) );
		$paid_total      = PenciPWT_General_Functions::get_paid_total( $payment_history );

		//Due payment basing on total paid, if available
		if ( ! is_wp_error( $payment_history ) && count( $payment_history ) != 0 ) {
			$due_payment                                  = PenciPWT_General_Functions::compute_author_due_payment( $paid_total, $stats, $author );
			$stats['total']['ppc_count']['due_payment']   = $due_payment['ppc_count'];
			$stats['total']['ppc_payment']['due_payment'] = $due_payment['ppc_payment'];

			//Never had any payment
		} else {
			$counting_data = array();
			if ( isset( $stats['total']['ppc_count'] ) ) {
				foreach ( $stats['total']['ppc_count']['normal_count'] as $counting_type => $data ) {
					$counting_data[ $counting_type ]['to_count'] = $data['to_count'];
					$counting_data[ $counting_type ]['real']     = $data['to_count'];

					if ( isset( $stats['total']['ppc_count']['normal_count'][ $counting_type ]['aux'] ) ) //add maybe aux data
					{
						$counting_data[ $counting_type ]['aux'] = $data['aux'];
					}
				}

				$stats['total']['ppc_count']['due_payment'] = $counting_data;

			}

			if ( isset( $stats['total']['ppc_payment'] ) ) {
				$stats['total']['ppc_payment']['due_payment'] = $stats['total']['ppc_payment']['normal_payment'];
			}

		}

		return $stats;
	}

	/**
	 * Adds details payment history, paid total and due payment to post payment.
	 *
	 * Hooks to PenciPWT_Counting_Stuff::data2cash() - ppc_post_counting_payment_data.
	 *
	 * @access  public
	 *
	 * @param    $single object WP post object (with PPC data)
	 * @param    $author array request author ids
	 *
	 * @return    object input+data
	 * @since   1.0
	 */
	static function get_post_payment_details( $single, $author ) {
		global $pencipwt_global_settings;

		//Pay history
		$payment_history = PenciPWT_Payment_History::get_post_payment_history( $single->ID );

		//Total paid basing on pay history
		$total_paid = PenciPWT_General_Functions::get_paid_total( $payment_history );

		//Due payment basing on total paid, if available
		if ( count( $total_paid['ppc_payment'] ) != 0 ) {
			$due_payment                        = PenciPWT_General_Functions::compute_post_due_payment( $total_paid, $single );
			$single->ppc_count['due_payment']   = $due_payment['ppc_count'];
			$single->ppc_payment['due_payment'] = $due_payment['ppc_payment'];
		} else {
			$counting_data = array();
			foreach ( $single->ppc_count['normal_count'] as $counting_type => $data ) {
				$counting_data[ $counting_type ]['to_count'] = $data['to_count'];
				$counting_data[ $counting_type ]['real']     = $data['to_count'];
			}

			$single->ppc_count['due_payment']   = $counting_data;
			$single->ppc_payment['due_payment'] = $single->ppc_payment['normal_payment'];
		}

		return $single;
	}

	/**
	 * Adds PRO sortable cols to author stats table (due payment)
	 *
	 * @param $sortable_cols array sortable cols
	 *
	 * @return $sortable_cols
	 * @since 1.6.8
	 */
	static function implement_author_sortable_columns( $sortable_cols ) {
		$sortable_cols['post_due_payment'] = array( 'post_due_payment', false );

		return $sortable_cols;
	}


	/**
	 * Adds PRO sortable cols to general stats table (due payment)
	 *
	 * @param $sortable_cols array sortable cols
	 *
	 * @return $sortable_cols
	 * @since 1.6.8
	 */
	static function implement_general_sortable_columns( $sortable_cols ) {
		$sortable_cols['author_due_payment'] = array( 'author_due_payment', false );

		return $sortable_cols;
	}

	/**
	 * Adds PRO cols to general stats output cols.
	 *
	 * Hooks to PenciPWT_Generate_Stats::format_stats_for_output() - ppc_general_stats_format_stats_after_cols_default.
	 *
	 * @access  public
	 *
	 * @param    $cols array already cols
	 *
	 * @return    array cols
	 * @since   1.0
	 */

	static function format_stats_for_output_implement_general_payment_data_cols( $cols ) {
		$settings          = PenciPWT_Counting_Stuff::$settings;
		$cols_before_total = array();

		/**
		 * Breaks the cols array before the total payment index to push some cols in the middle.
		 */

		//Get total payment col and remove it from array. Some cols need to be added before
		$total_payment_col = array_slice( $cols, ( ( count( $cols ) - 1 ) ), 1 );
		array_splice( $cols, ( ( count( $cols ) - 1 ) ), 1 );

		//Push the total payment col back, with new cols in the middle
		$cols = array_merge( $cols, $cols_before_total, $total_payment_col );

		/**
		 * These cols are added after the total payment one.
		 */

		$cols['author_due_payment'] = __( 'Unpaid', 'penci-pay-writer' );

		return $cols;
	}

	/**
	 * Adds PRO cols to author stats output cols.
	 *
	 * Hooks to PenciPWT_Generate_Stats::format_stats_for_output() - ppc_author_stats_format_stats_after_cols_default.
	 *
	 * @access  public
	 *
	 * @param    $cols array already cols
	 *
	 * @return    array cols
	 * @since   1.0
	 */

	static function format_stats_for_output_implement_author_payment_data_cols( $cols ) {
		$settings          = PenciPWT_Counting_Stuff::$settings;
		$cols_before_total = array();

		/**
		 * Breaks the cols array before the total payment index to push some cols in the middle.
		 */

		//Get total payment col and remove it from array. Some cols need to be added before
		$total_payment_col = array_slice( $cols, ( ( count( $cols ) - 1 ) ), 1 );
		array_splice( $cols, ( ( count( $cols ) - 1 ) ), 1 );

		//Push the total payment col back, with new cols in the middle
		$cols = array_merge( $cols, $cols_before_total, $total_payment_col );

		/**
		 * These cols are added after the total payment one.
		 */

		$cols['post_due_payment'] = __( 'Unpaid', 'penci-pay-writer' );

		return $cols;
	}

	/**
	 * Adds PRO data to general stats output data.
	 *
	 * Hooks to PenciPWT_Generate_Stats::format_stats_for_output() - ppc_general_stats_format_stats_after_each_default.
	 *
	 * @access  public
	 *
	 * @param    $formatted_stats array formatted stats for current author
	 * @param    $author int author id
	 * @param    $posts sorted_stats[author]
	 *
	 * @return    array formatted stats
	 * @since   1.0
	 */

	static function format_stats_for_output_implement_general_payment_data( $formatted_stats, $author, $posts ) {
		$fields_before_total = array();

		/**
		 * Breaks the fields array before the total payment index to push some cols in the middle.
		 */

		//Get total payment field and remove it from array. Some cols need to be added before
		$total_payment_col = array_slice( $formatted_stats, ( ( count( $formatted_stats ) - 1 ) ), 1 );
		array_splice( $formatted_stats, ( ( count( $formatted_stats ) - 1 ) ), 1 );

		//Push the total payment col back, with new cols in the middle
		$formatted_stats = array_merge( $formatted_stats, $fields_before_total, $total_payment_col );

		/**
		 * These cols are added after the total payment one.
		 */

		$formatted_stats['author_due_payment'] = $posts['total']['ppc_payment']['due_payment']['total'];

		return $formatted_stats;
	}

	/**
	 * Adds PRO data to author stats output data.
	 *
	 * Hooks to PenciPWT_Generate_Stats::format_stats_for_output() - ppc_author_stats_format_stats_after_each_default.
	 *
	 * @access  public
	 *
	 * @param    $formatted_stats array formatted stats
	 * @param    $author int author id
	 * @param    $post object WP post object (with PPC data)
	 *
	 * @return    array formatted stats
	 * @since   1.0
	 */

	static function format_stats_for_output_implement_author_payment_data( $formatted_stats, $author, $post ) {
		$fields_before_total = array();

		/**
		 * Breaks the fields array before the total payment index to push some cols in the middle.
		 */

		//Get total payment field and remove it from array. Some cols need to be added before
		$total_payment_col = array_slice( $formatted_stats, ( ( count( $formatted_stats ) - 1 ) ), 1 );
		array_splice( $formatted_stats, ( ( count( $formatted_stats ) - 1 ) ), 1 );

		//Push the total payment col back, with new cols in the middle
		$formatted_stats = array_merge( $formatted_stats, $fields_before_total, $total_payment_col );

		/**
		 * These cols are added after the total payment one.
		 */

		$formatted_stats['post_due_payment'] = $post->ppc_payment['due_payment']['total'];

		return $formatted_stats;
	}

	/**
	 * Adds currency symbol to overall stats total payment.
	 *
	 * Hooks to PenciPWT_Generate_Stats::get_overall_stats() - ppc_overall_stats.
	 *
	 * @access  public
	 *
	 * @param    $overall_stats array generated overall stats
	 * @param   $stats array generated stats (shown in stats page)
	 *
	 * @return    array overall stats
	 * @since   1.0
	 */

	static function get_overall_stats_implement( $overall_stats, $stats = null ) {
		$overall_stats['due_payment'] = 0;

		foreach ( $stats as $single ) {
			//Due payment total count
			if ( isset( $single['total']['ppc_payment']['due_payment']['total'] ) ) {
				$overall_stats['due_payment'] += $single['total']['ppc_payment']['due_payment']['total'];
			}
		}

		return $overall_stats;
	}
}
