<?php

class PenciPWT_Ajax_Functions {

	/**
	 * Checks whether the AJAX request is legitimate, if not displays an error that the requesting JS will display.
	 *
	 * @access  public
	 *
	 * @param   $nonce string the WP nonce
	 *
	 * @since   2.0
	 */

	static function ppc_check_ajax_referer( $nonce ) {
		if ( ! check_ajax_referer( $nonce, false, false ) ) {
			die( __( 'Error: Seems like AJAX request was not recognised as coming from the right page. Maybe hacking around..?', 'penci-pay-writer' ) );
		}
	}

	/**
	 * Fetches users to be personalized basing on the requested user role.
	 *
	 * @access  public
	 * @since   2.0
	 */
	static function personalize_fetch_users_by_roles() {
		global $pencipwt_global_settings;
		self::ppc_check_ajax_referer( 'ppc_personalize_fetch_users_by_roles' );

		echo 'ok';
		$user_role = trim( $_REQUEST['user_role'] );

		$args = array(
			'orderby'     => 'display_name',
			'order'       => 'ASC',
			'role'        => $user_role,
			'count_total' => true,
			'fields'      => array(
				'ID',
				'display_name'
			)
		);

		/**
		 * Filters user fetching (by role) for Personalize settings box.
		 *
		 * This fetches the users list that is shown in the Options Personalize settings box when a user role is clicked.
		 *
		 * @param array $args WP_User_query args
		 *
		 * @since    2.0
		 */

		$args = apply_filters( 'ppc_personalize_fetch_users_args', $args );

		$users_to_show = new WP_User_Query( $args );

		if ( $users_to_show->get_total() == 0 ) {
			_e( 'No users found.', 'penci-pay-writer' );

		} else {
			$n    = 0;
			$html = '';
			echo '<table>';

			foreach ( $users_to_show->results as $single ) {
				if ( $n % 3 == 0 ) {
					$html .= '<tr>';
				}

				$html .= '<td><a href="' . admin_url( $pencipwt_global_settings['options_menu_link'] . '&amp;userid=' . $single->ID ) . '" title="' . $single->display_name . '">' . $single->display_name . '</a></td>';

				if ( $n % 3 == 2 ) {
					$html .= '</tr>';
				}

				/**
				 * Filters user display in Personalize settings box.
				 *
				 * This fires for every user that is displayed for the selected role.
				 *
				 * @param string $html html code for the user list up to the current one
				 * @param object $single WP_User current user data
				 *
				 * @since    2.0
				 */

				echo apply_filters( 'ppc_html_personalize_list_print_user', $html, $single );

				$html = '';
				$n ++;
			}

			echo '</table>';
		}

		/**
		 * Allows to display html after the list of users from a user-role in the personalize settings box.
		 *
		 * @param string $user_role user role selected
		 *
		 * @since    2.518
		 */

		do_action( 'ppc_personalize_users_role_list_end', $user_role );

		exit;
	}

	/**
	 * Fetches users to be personalized basing on the requested user role.
	 *
	 * @access  public
	 * @since   2.710
	 */
	static function stats_get_users_by_role() {
		global $pencipwt_global_settings;
		self::ppc_check_ajax_referer( 'ppc_stats_get_users_by_role' );

		$user_role = trim( $_REQUEST['user_role'] );

		$args = array(
			'orderby'     => 'display_name',
			'order'       => 'ASC',
			'role'        => $user_role,
			'count_total' => true,
			'fields'      => array(
				'ID',
				'display_name'
			)
		);

		/**
		 * Filters user fetching (by role) for stats select.
		 *
		 * This fetches the users list that is shown in the Stats User dropdown when a user role is selected.
		 *
		 * @param array $args WP_User_query args
		 *
		 * @since    2.710
		 */

		$args = apply_filters( 'ppc_stats_get_users_args', $args );

		$users_to_show = new WP_User_Query( $args );
		$html          = '<option value="ppc_any">' . __( 'Any', 'penci-pay-writer' ) . '</option>';

		if ( $users_to_show->get_total() != 0 ) {

			foreach ( $users_to_show->results as $single ) {
				$html .= '<option value="' . $single->ID . '">' . $single->display_name . '</option>';
			}

		}

		wp_send_json_success( array(
			'html' => $html
		) );
	}

	/**
	 * Retrieves and shows post payment history
	 *
	 * @access  public
	 * @since   1.0
	 */

	static function show_post_payment_history() {
		global $pencipwt_global_settings;

		$post_id = (int) $_REQUEST['post_id'];

		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_show_post_payment_history_' . $post_id );

		$post_data       = get_post( $post_id );
		$settings        = PenciPWT_General_Functions::get_settings( $post_data->post_author, true );
		$payment_history = PenciPWT_Payment_History::get_post_payment_history( $post_id );

		echo '<h2>' . __( 'Payment history for post ID:', 'penci-pay-writer' ) . ' <span id="delete_from">' . $post_id . '</span></h2>';

		if ( $payment_history == false or count( $payment_history ) == 0 ) {
			die( __( 'No payment records are available.', 'penci-pay-writer' ) );
		}

		//Initializes counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();
		$counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );

		do_action( 'ppcp_html_post_payment_history_before_items' );
		PenciPWT_HTML_Functions::print_payment_history( $payment_history, $settings, $counting_types );
		do_action( 'ppcp_html_post_payment_history_after_items' );

		exit;
	}

	/**
	 * Retrieves and shows author payment history
	 *
	 * @access  public
	 * @since   1.0
	 */

	static function show_author_payment_history() {
		global $pencipwt_global_settings;

		$author_id = (int) $_REQUEST['author_id'];
		$author    = get_userdata( $author_id );

		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_show_author_payment_history_' . $author_id );

		$settings        = PenciPWT_General_Functions::get_settings( $author_id, true );
		$payment_history = PenciPWT_Payment_History::get_author_payment_history( $author_id );

		echo '<h2>' . __( 'Payment history for author ID', 'penci-pay-writer' ) . ': <span id="delete_from">' . $author_id . '</span> - ' . __( 'Name', 'penci-pay-writer' ) . ': ' . $author->display_name . '</h2>';

		if ( $payment_history == false or count( $payment_history ) == 0 ) {
			die( __( 'No payment records are available.', 'penci-pay-writer' ) );
		}

		//Initializes counting types, merging post and author ones
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();
		$post_counting_types   = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );
		$author_counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' );
		$counting_types        = array_merge( $post_counting_types, $author_counting_types );

		do_action( 'ppcp_html_author_payment_history_before_items' );
		PenciPWT_HTML_Functions::print_paid_total( PenciPWT_General_Functions::get_paid_total( $payment_history ), $settings, $counting_types, $author_id );
		PenciPWT_HTML_Functions::print_payment_history( $payment_history, $settings, $counting_types, $author_id );
		do_action( 'ppcp_html_author_payment_history_after_items' );

		exit;
	}

	/**
	 * Retrieves and shows a transaction.
	 *
	 * @access  public
	 * @since   1.5.9.3
	 */

	static function show_transaction() {
		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_show_transaction' );

		$tracking_id = $_REQUEST['tracking_id'];
		$transaction = PenciPWT_Payment_History::get_transaction( $tracking_id );

		echo '<h2>' . __( 'Transaction details for tracking id:', 'penci-pay-writer' ) . ' <span id="delete_from">' . $tracking_id . '</span></h2>';

		if ( empty( $transaction['posts_ids'] ) and empty( $transaction['authors_info'] ) ) {
			die( __( 'No payment records are available.', 'penci-pay-writer' ) );
		}

		do_action( 'ppcp_html_transaction_before_output' );
		PenciPWT_HTML_Functions::print_transaction( $transaction );
		do_action( 'ppcp_html_transaction_after_output' );

		exit;
	}

	/**
	 * Deletes an author payment history record -- which means deleting all author's posts payment histories.
	 *
	 * @access  public
	 * @since   1.0
	 */

	static function payment_history_author_delete() {
		global $pencipwt_global_settings;
		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_payment_history_delete' );

		$perm = new PenciPWT_Permissions();
		if ( ! $perm->can_delete_payment_history() ) {
			die( __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' ) );
		}

		$author_id   = (int) $_REQUEST['delete_from'];
		$tracking_id = (string) $_REQUEST['tracking_id'];

		//Author history
		$delete_author = PenciPWT_Payment_History::delete_author_payment_history_item( $author_id, $tracking_id );
		if ( is_wp_error( $delete_author ) ) {
			die( $delete_author->get_error_message() );
		}

		//Posts history
		$posts_payment_histories = PenciPWT_Payment_History::get_author_all_posts_payment_histories( $author_id );
		if ( is_wp_error( $posts_payment_histories ) ) {
			die( $posts_payment_histories->get_error_message() );
		}

		foreach ( $posts_payment_histories as $single ) {
			$post_payment_history_content = maybe_unserialize( $single->meta_value );

			$delete_post = PenciPWT_Payment_History::delete_post_payment_history_item( $single->post_id, $tracking_id );
			if ( is_wp_error( $delete_post ) ) {
				die( $delete_post->get_error_message() );
			}
		}

		$delete_transaction = PenciPWT_Payment_History::maybe_delete_transaction_details( $tracking_id );
		if ( is_wp_error( $delete_transaction ) ) {
			die( $delete_transaction->get_error_message() );
		}

		die( 'ok' );
	}

	/**
	 * Deletes a post payment history record
	 *
	 * @access  public
	 * @since   1.0
	 */

	static function payment_history_post_delete() {
		global $pencipwt_global_settings;
		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_payment_history_delete' );

		$perm = new PenciPWT_Permissions();
		if ( ! $perm->can_delete_payment_history() ) {
			die( __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' ) );
		}

		$post_id     = (int) $_REQUEST['delete_from'];
		$tracking_id = (string) $_REQUEST['tracking_id'];

		$delete = PenciPWT_Payment_History::delete_post_payment_history_item( $post_id, $tracking_id );
		if ( is_wp_error( $delete ) ) {
			die( $delete->get_error_message() );
		}

		die( 'ok' );
	}

	/**
	 * Deletes a transaction.
	 *
	 * @access  public
	 * @since   1.5.9.3
	 */

	static function delete_transaction() {
		global $pencipwt_global_settings;
		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_transaction_delete' );

		$perm = new PenciPWT_Permissions();
		if ( ! $perm->can_delete_payment_history() ) {
			die( __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' ) );
		}

		$tracking_id = (string) $_REQUEST['tracking_id'];

		$delete = PenciPWT_Payment_History::delete_transaction( $tracking_id );
		if ( is_wp_error( $delete ) ) {
			die( $delete->get_error_message() );
		}

		die( 'ok' );
	}

	/**
	 * Responsible for marking as paid.
	 *
	 * Extracts posts ids from form data and hands them to payment function.
	 *
	 * @access  public
	 * @since   1.3
	 */
	static function mark_as_paid() {
		global $pencipwt_global_settings;

		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_confirm_payment' );
		$perm = new PenciPWT_Permissions();

		if ( ! $perm->can_mark_as_paid() ) {
			wp_send_json_error( array(
				'message' => __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' )
			) );
		}

		parse_str( $_REQUEST['form_data'], $form_data );

		//Need to set global time range values or other addons may have issues
		$pencipwt_global_settings['stats_tstart'] = $form_data['ppcp_stats_tstart'];
		$pencipwt_global_settings['stats_tend']   = $form_data['ppcp_stats_tend'];

		//Initiliaze counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();

		$posts_ids = unserialize( base64_decode( $form_data['ppcp_payment_posts_ids'] ) );

		if ( ! is_array( $posts_ids ) or empty( $posts_ids ) ) {
			wp_send_json_error( array(
				'message' => 'No posts to pay.'
			) );
		}

		//Gets countings and amounts for given posts
		$stats = PenciPWT_Stats::get_stats_by_post_ids( $posts_ids );
		if ( is_wp_error( $stats ) ) {
			wp_send_json_error( array(
				'message' => $stats->get_error_message()
			) );
		}

		$prepare_payment = PenciPWT_Payment::prepare_payment( $stats );
		if ( is_wp_error( $prepare_payment ) ) {
			wp_send_json_error( array(
				'message' => $prepare_payment->get_error_message()
			) );
		}

		//Store transaction details - option (starting to be issued in PenciPWT_Payment::prepare_payment)
		//$payment_data['transaction_details']['payment_method'] = $_REQUEST['payment_method'];
		$prepare_payment['payment_data']['transaction_details']['payment_method'] = 'mark';
		$prepare_payment['payment_data']['transaction_details']['payment_note']   = trim( $form_data['ppcp_payment_note'] );

		$prepare_payment = apply_filters( 'ppcp_prepare_payment_data_before_payment', $prepare_payment, $form_data );
		$payment_data    = $prepare_payment['payment_data'];

		$general_payment_history_update = PenciPWT_Payment_History::update_transaction_details( $payment_data['transaction_details'] );
		if ( is_wp_error( $general_payment_history_update ) ) {
			wp_send_json_error( array(
				'message' => $general_payment_history_update->get_error_message()
			) );
		}

		//Posts marking as paid - postmeta
		$posts_payment_histories = PenciPWT_Payment_History::post_payment_history_add_new_record( $payment_data['posts_for_payment_history_data'] );
		if ( is_wp_error( $posts_payment_histories ) ) {
			wp_send_json_error( array(
				'message' => $posts_payment_histories->get_error_message()
			) );
		}

		//Author marking as paid - usermeta
		$author_payment_histories_update = PenciPWT_Payment_History::author_payment_history_add_new_record( $payment_data['authors_for_payment_history_data'] );
		if ( is_wp_error( $author_payment_histories_update ) ) {
			wp_send_json_error( array(
				'message' => $author_payment_histories_update->get_error_message()
			) );
		}

		//Send notification to users if needed
		//Delay emails. We only send it 10 minutes later so you have time to delete the transaction if needed and emails won't be sent
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		if ( pencipwt_get_setting( 'payment_notification_mark_as_paid' ) ) {
			wp_schedule_single_event( time() + 600, 'ppcp_send_email_notifications', array( $payment_data['transaction_details']['tracking_id'] ) );
		}

		do_action( 'ppcp_posts_mark_as_paid_successful', array(
			'tracking_id' => $payment_data['transaction_details']['tracking_id']
		) );

		//Make up return url. Author ID is taken, if only an author is being paid as first index of stats array
		$author = '';
		if ( count( $prepare_payment['stats']['raw_stats'] ) == 1 ) {
			reset( $prepare_payment['stats']['raw_stats'] );
			$author = '&author=' . key( $prepare_payment['stats']['raw_stats'] );
		}
		$current_page = admin_url( $pencipwt_global_settings['stats_menu_link'] . '&success=mark_paid&tstart=' . $form_data['ppcp_stats_tstart'] . '&tend=' . $form_data['ppcp_stats_tend'] . $author );

		wp_send_json_success( array(
			'redirect_url' => $current_page
		) );
	}

	/**
	 * Responsible for a paypal payment.
	 *
	 * Extracts posts ids from form data and hands them to payment function.
	 *
	 * @access  public
	 * @since   1.3
	 */
	static function paypal_payment() {
		global $pencipwt_global_settings;
		require_once( 'pencipwt_payment_class.php' );

		PenciPWT_Ajax_Functions::ppc_check_ajax_referer( 'ppcp_confirm_payment' );
		$perm = new PenciPWT_Permissions();

		//Enough to be able to pay with paypal to be able to mark as paid
		if ( ! $perm->can_see_paypal_functions() ) {
			wp_send_json_error( array(
				'message' => __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' )
			) );
		}

		parse_str( $_REQUEST['form_data'], $form_data );

		//Need to set global time range values or other addons may have issues
		$pencipwt_global_settings['stats_tstart'] = $form_data['ppcp_stats_tstart'];
		$pencipwt_global_settings['stats_tend']   = $form_data['ppcp_stats_tend'];

		//Initiliaze counting types
		$pencipwt_global_settings['counting_types_object'] = new PenciPWT_Counting_Types();
		$pencipwt_global_settings['counting_types_object']->register_built_in_counting_types();

		$posts_ids = unserialize( base64_decode( $form_data['ppcp_payment_posts_ids'] ) );

		//Gets countings and amounts for given posts
		$stats = PenciPWT_Stats::get_stats_by_post_ids( $posts_ids );
		if ( is_wp_error( $stats ) ) {
			wp_send_json_error( array(
				'message' => $stats->get_error_message()
			) );
		}

		$prepare_payment = PenciPWT_Payment::prepare_payment( $stats );
		if ( is_wp_error( $prepare_payment ) ) {
			wp_send_json_error( array(
				'message' => $prepare_payment->get_error_message()
			) );
		}

		//Store transaction details - option (starting to be issued in PenciPWT_Payment::prepare_payment)
		$prepare_payment['payment_data']['transaction_details']['payment_method'] = 'PayPal';
		$prepare_payment['payment_data']['transaction_details']['payment_note']   = trim( $form_data['ppcp_payment_note'] );

		$prepare_payment = apply_filters( 'ppcp_prepare_paypal_payment_data_before_payment', $prepare_payment, $form_data );
		$payment_data    = $prepare_payment['payment_data'];

		$PayPal = new PenciPWT_PayPal_Functions();
		if ( is_wp_error( $PayPal->error ) ) {
			wp_send_json_error( array(
				'message' => $PayPal->error->get_error_message()
			) );
		}

		//Make up return url. Author ID is taken, if only an author is being paid as first index of stats array
		$author = '';
		if ( count( $prepare_payment['stats']['raw_stats'] ) == 1 ) {
			reset( $prepare_payment['stats']['raw_stats'] );
			$author = '&author=' . key( $prepare_payment['stats']['raw_stats'] );
		}
		$current_page = admin_url( $pencipwt_global_settings['stats_menu_link'] . '&tstart=' . $form_data['ppcp_stats_tstart'] . '&tend=' . $form_data['ppcp_stats_tend'] . $author );

		//Make PayPal payment
		$paypal_payment = $PayPal->execute_payment( $payment_data, $current_page );
		if ( is_wp_error( $paypal_payment ) ) {
			wp_send_json_error( array(
				'message' => $paypal_payment->get_error_message()
			) );
		}

		/**
		 * MARK AS PAID
		 */

		$general_payment_history_update = PenciPWT_Payment_History::update_transaction_details( $payment_data['transaction_details'] );
		if ( is_wp_error( $general_payment_history_update ) ) {
			wp_send_json_error( array(
				'message' => $general_payment_history_update->get_error_message()
			) );
		}

		//Posts marking as paid - postmeta
		$posts_payment_histories = PenciPWT_Payment_History::post_payment_history_add_new_record( $payment_data['posts_for_payment_history_data'] );
		if ( is_wp_error( $posts_payment_histories ) ) {
			wp_send_json_error( array(
				'message' => $posts_payment_histories->get_error_message()
			) );
		}

		//Author marking as paid - usermeta
		$author_payment_histories_update = PenciPWT_Payment_History::author_payment_history_add_new_record( $payment_data['authors_for_payment_history_data'] );
		if ( is_wp_error( $author_payment_histories_update ) ) {
			wp_send_json_error( array(
				'message' => $author_payment_histories_update->get_error_message()
			) );
		}

		if ( pencipwt_get_setting('payment_notification_paypal') and ! pencipwt_get_setting('paypal_ipn') ) {
			wp_schedule_single_event( time() + 600, 'ppcp_send_email_notifications', array( $payment_data['transaction_details']['tracking_id'] ) );
		}

		do_action( 'ppcp_posts_paypal_payment_successful', array(
			'tracking_id' => $payment_data['transaction_details']['tracking_id']
		) );

		wp_send_json_success( array(
			'redirect_url' => $paypal_payment['return_url']
		) );
	}
}
