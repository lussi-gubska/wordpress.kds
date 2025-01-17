<?php

require_once( 'pencipwt_permissions_class.php' );

class PenciPWT_HTML_Functions {
	/*
	 * Hold currency symbol data (position and symbol).
	 */
	public static $currency_symbol;

	/**
	 * Adds pay field and payment history to HTML stats cols.
	 *
	 * Hooks to PenciPWT_HTML_Functions::get_html_stats() - ppc_general_stats_html_cols_after_default, ppc_author_stats_html_cols_after_default.
	 *
	 * @access  public
	 * @since   1.0
	 */
	static function get_html_stats_implement_payment_data_cols() {
		$perm = new PenciPWT_Permissions();

		if ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) {
			echo '<th scope="col"><input type="checkbox" class="ppcp_one_to_rule_them_all" /> &nbsp; ' . __( 'Pay', 'penci-pay-writer' ) . '</th>';
		}

		if ( $perm->can_see_payment_history() ) {
			echo '<th scope="col">' . __( 'Pay History', 'penci-pay-writer' ) . '</th>';
		}
	}

	/**
	 * Formats payment bonus, due payment and total payment fields with abbr tag and currency symbol in general stats.
	 *
	 * Hooks to PenciPWT_HTML_Functions::get_html_stats() - ppc_general_stats_html_each_field_value.
	 *
	 * @access  public
	 *
	 * @param    $field_value string field currently handling value
	 * @param    $field_name string field currently handling name
	 * @param    $author_raw_data array sorted stats
	 *
	 * @return    string field value
	 * @since   1.0
	 */
	static function get_html_stats_general_each_field( $field_value, $field_name, $author_raw_data ) {
		global $pencipwt_global_settings;

		switch ( $field_name ) {
			case 'author_due_payment':
				$tooltip = '';

				if ( pencipwt_get_setting( 'enable_stats_payments_tooltips' ) ) { //generate tooltip if requested
					$active_counting_types_merge = array_merge( $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' ), $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' ) );
					$tooltip                     = PenciPWT_Counting_Stuff::build_payment_details_tooltip( $author_raw_data['total']['ppc_count']['due_payment'], $author_raw_data['total']['ppc_payment']['due_payment'], $active_counting_types_merge );
				}

				$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . PenciPWT_General_Functions::format_payment( $field_value ) . '</abbr>';

				break;
		}

		return $field_value;
	}

	/**
	 * Formats payment bonus, due payment and total payment fields with abbr tag and currency symbol in author stats.
	 *
	 * Hooks to PenciPWT_HTML_Functions::get_html_stats() - ppc_author_stats_html_each_field_value.
	 *
	 * @access  public
	 *
	 * @param    $field_value string field currently handling value
	 * @param    $field_name string field currently handling name
	 * @param    $post object WP post object (with PPC data)
	 *
	 * @return    string field value
	 * @since   1.0
	 */
	static function get_html_stats_author_each_field( $field_value, $field_name, $post ) {
		global $pencipwt_global_settings;

		switch ( $field_name ) {
			case 'post_due_payment':
				$tooltip = '';

				if ( pencipwt_get_setting( 'enable_stats_payments_tooltips' ) ) { //generate tooltip if requested
					$tooltip = PenciPWT_Counting_Stuff::build_payment_details_tooltip( $post->ppc_count['due_payment'], $post->ppc_payment['due_payment'], $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' ) );
				}

				$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . PenciPWT_General_Functions::format_payment( $field_value ) . '</abbr>';

				break;
		}

		return $field_value;
	}

	/**
	 * Adds pay field and payment history to HTML general stats.
	 *
	 * Hooks to PenciPWT_HTML_Functions::get_html_stats() - ppc_general_stats_html_after_each_default.
	 *
	 * @access  public
	 *
	 * @param    $author int author id
	 * @param    $formatted_data array formatted stats
	 * @param    $raw_data array sorted stats
	 *
	 * @since   1.0
	 */
	static function get_html_stats_implement_general_payment_data( $author, $formatted_data, $raw_data ) {
		global $pencipwt_global_settings, $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		if ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) {

			$data_to_pass_along = apply_filters( 'ppcp_author_payment_data_to_pass_along', array(
				'author' => $author
			) );

			$disabled = '';
			if ( $raw_data[ $author ]['total']['ppc_payment']['due_payment']['total'] == 0 ) {
				$disabled = ' disabled="disabled"';
			}

			echo '<td><input type="checkbox" class="ppcp_paid_status_update" name="ppcp_paid_update_author_' . $author . '" value="' . base64_encode( serialize( $data_to_pass_along ) ) . '"' . $disabled . ' /></td>';
		}

		if ( $perm->can_see_payment_history() ) {
			$payment_history_ajax_url = add_query_arg(
				array(
					'action'      => 'ppcp_show_author_payment_history',
					'author_id'   => $author,
					'height'      => 500,
					'_ajax_nonce' => wp_create_nonce( 'ppcp_show_author_payment_history_' . $author )
				),
				'admin-ajax.php'
			);

			echo '<td class="author_payment_history"><a href="' . $payment_history_ajax_url . '" class="thickbox" title="' . __( 'See payment history', 'penci-pay-writer' ) . '">' . __( 'See payment history', 'penci-pay-writer' ) . '</a></td>';
		}
	}

	/**
	 * Adds pay field and payment history to HTML author stats.
	 *
	 * Hooks to PenciPWT_HTML_Functions::get_html_stats() - ppc_author_stats_html_after_each_default.
	 *
	 * @access  public
	 *
	 * @param    $author int author id
	 * @param    $formatted_data array formatted stats
	 * @param    $post object WP post object (with PPC data)
	 *
	 * @since   1.0
	 */
	static function get_html_stats_implement_author_payment_data( $author, $formatted_data, $post ) {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		$user_settings            = PenciPWT_General_Functions::get_settings( $author );
		$payment_history_ajax_url = add_query_arg(
			array(
				'action'      => 'ppcp_show_post_payment_history',
				'post_id'     => $post->ID,
				'height'      => 500,
				'_ajax_nonce' => wp_create_nonce( 'ppcp_show_post_payment_history_' . $post->ID )
			),
			'admin-ajax.php'
		);

		//Disable payment field if payment doesn't exceed threshold && pay only when does || due pay == 0
		if ( $perm->can_mark_as_paid() or $perm->can_see_paypal_functions() ) {

			$disabled = '';
			if ( ( pencipwt_get_setting( 'counting_payment_only_when_total_threshold' ) and $post->ppc_misc['exceed_threshold'] == false ) or $post->ppc_payment['due_payment']['total'] == 0 ) {
				$disabled = ' disabled="disabled"';
			}

			echo '<td><input type="checkbox" class="ppcp_paid_status_update" name="ppcp_paid_update_post_' . $post->ID . '" value=""' . $disabled . ' /></td>';
		}

		if ( $perm->can_see_payment_history() ) {
			echo '<td class="post_payment_history"><a href="' . $payment_history_ajax_url . '" class="thickbox" title="' . __( 'See payment history', 'penci-pay-writer' ) . '">' . __( 'See payment history', 'penci-pay-writer' ) . '</a></td>';
		}
	}

	/**
	 * Prints payment history.
	 *
	 * @access  public
	 *
	 * @param    $payment_history array payment history
	 * @param    $settings plugin general settings
	 * @param   $counting_types array
	 * @param    $author (optional) int needed to use csv export feature
	 *
	 * @since   1.0
	 */
	static function print_payment_history( $payment_history, $settings, $counting_types, $author = false ) {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		//Sort payment history
		$payment_history = PenciPWT_Payment_History::sort_payment_history_by_time( $payment_history );

		echo '<p>' . sprintf( __( 'Here you can see past payments. These records are used to compute due payments, so cast an eye on them sometimes. Moreover, %1$sremember%2$s that deleting an author\'s history record will delete all their posts history records that belong to the same transaction.', 'penci-pay-writer' ), '<strong>', '</strong>' ) . '</p>';

		echo '<div id="ppcp_payment_history_loading" class="ppc_ajax_loader"><img src="' . $pencipwt_global_settings['folder_path'] . 'style/images/ajax-loader.gif' . '" alt="' . __( 'Loading', 'penci-pay-writer' ) . '..." title="' . __( 'Loading', 'penci-pay-writer' ) . '..." /></div>';
		echo '<div id="ppcp_payment_history_error" class="ppc_error"></div>';

		foreach ( $payment_history as $key => $single ) {

			if ( ! $perm->can_see_others_detailed_stats() ) {
				$transaction_display = $key;

			} else { //link to transaction details if user can see others' pay history
				$transaction_ajax_url = add_query_arg(
					array(
						'action'      => 'ppcp_show_transaction',
						'tracking_id' => $key,
						'height'      => 500,
						'_ajax_nonce' => wp_create_nonce( 'ppcp_show_transaction' )
					),
					'admin-ajax.php'
				);

				$transaction_display = '<a href="' . $transaction_ajax_url . '" class="thickbox ppcp_transaction_link" title="' . __( "View transaction details", "ppcp" ) . '">' . $key . '</a>';
			}

			echo '<div class="ppcp_payment_history_element">';
			echo '<p><strong>' . date_i18n( get_option( 'date_format' ), $single['time'] ) . ' => ' . PenciPWT_General_Functions::format_payment( $single['ppc_payment']['total'] ) . ' - ID: ' . $transaction_display . '</strong>';

			//If paid with PayPal, show the verified/unverified badge - since v. 1.4
			//Since v 1.4.8 we check whether the status display is enabled
			if ( pencipwt_get_setting( 'paypal_display_payment_history_status' ) ) {
				if ( isset( $single['payment_method'] ) and ( $single['payment_method'] == 'paypal' or $single['payment_method'] == 'PayPal' ) ) {
					if ( isset( $single['verified'] ) and $single['verified'] ) {
						echo '<span class="ppcp_payment_history_record_verified">' . __( 'PayPal Verified', 'penci-pay-writer' ) . '</span>';
					} else {
						echo '<span class="ppcp_payment_history_record_unverified">' . __( 'PayPal Unverified', 'penci-pay-writer' ) . '</span>';
					}

					//Fallback for v. < 1.4: if transaction is marked as verified we *assume* it was made with PayPal and show the badge
				} else {
					if ( isset( $single['verified'] ) and $single['verified'] ) {
						echo '<span class="ppcp_payment_history_record_verified">' . __( 'PayPal Verified', 'penci-pay-writer' ) . '</span>';
					}
				}
			}

			//Maybe item deletion link
			if ( $perm->can_delete_payment_history() ) {

				if ( is_int( $author ) and $author > 0 ) {
					$delete_class = 'ppcp_payment_history_delete_author';
				} else {
					$delete_class = 'ppcp_payment_history_delete_post';
				}

				echo '<a href="#" class="' . $delete_class . '" title="' . __( 'Delete this record', 'penci-pay-writer' ) . '" accesskey="' . $key . '">' . __( 'Delete this record', 'penci-pay-writer' ) . '</a>';
			}

			//Maybe csv-export link
			if ( $perm->can_csv_export() and $author !== false ) {
				echo '<a href="' . wp_nonce_url( admin_url( $pencipwt_global_settings['stats_menu_link'] . '&amp;export=csv&amp;noheader=true&amp;tracking_id=' . $key . '&amp;author=' . $author ), 'ppcp_csv_export' ) . '" class="ppcp_payment_history_export" title="' . __( 'Export transaction details', 'penci-pay-writer' ) . '">' . __( 'Export transaction details', 'penci-pay-writer' ) . '</a>';
			}

			//Define and display payment method
			if ( ! isset( $single['payment_method'] ) or $single['payment_method'] == '' or $single['payment_method'] == 'mark' ) {
				$payment_method = __( 'Nothing special', 'penci-pay-writer' );
			}
			if ( ! isset( $payment_method ) ) {
				$payment_method = $single['payment_method'];
			}

			echo '<span class="ppcp_payment_history_element_method"><strong>' . __( 'Method:', 'penci-pay-writer' ) . '</strong> <em>' . $payment_method . '</em></span>';
			echo '</p>';

			echo '<div class="ppcp_payment_history_element_content">';

			//Maybe display payment note
			if ( isset( $single['payment_note'] ) and isset( $single['payment_note'][1] ) ) {
				echo '<p class="ppcp_payment_history_element_note"><abbr title="' . __( 'Payment note', 'penci-pay-writer' ) . '"><em>' . $single['payment_note'] . '</em></abbr></p>';
			}

			echo '<table>';
			echo '<thead>';
			echo '<tr>';
			echo '<th style="width: 40%">' . __( 'Type', 'penci-pay-writer' ) . '</th>';
			echo '<th style="width: 20%">' . __( 'Count', 'penci-pay-writer' ) . '</th>';
			echo '<th style="width: 20%">' . __( 'Payment', 'penci-pay-writer' ) . '</th>';
			echo '</tr>';
			echo '</thead>';

			if ( ! empty( $single['ppc_payment'] ) ) {
				foreach ( $single['ppc_payment'] as $id => $data ) {
					if ( $id == 'total' ) {
						continue;
					}
					if ( $data == 0.00 and ( ! isset( $single['ppc_count'][ $id ] ) or $single['ppc_count'][ $id ] == 0.000000 ) ) {
						continue;
					}

					echo '<tr>';
					if ( ! isset( $counting_types[ $id ] ) ) {
						echo '<td>' . $id . '</td>';

						if ( isset( $single['ppc_count'][ $id ] ) ) //possible payment_only counting types of disabled addons
						{
							echo '<td>' . $single['ppc_count'][ $id ] . '</td>';
						} else {
							echo '<td>-</td>';
						}

					} else {

						echo '<td>' . $counting_types[ $id ]['label'] . '</td>';
						if ( ! isset( $counting_types[ $id ]['payment_only'] ) or $counting_types[ $id ]['payment_only'] == false ) {
							echo '<td>' . $single['ppc_count'][ $id ] . '</td>';
						} else {
							echo '<td>-</td>';
						}

					}

					echo '<td>' . PenciPWT_General_Functions::format_payment( $data ) . '</td>';

					echo '</tr>';
				}
			}

			do_action( 'ppcp_payment_history_display_bottom', $single );

			echo '</table>';
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Prints paid total.
	 *
	 * @access  public
	 *
	 * @param    $payment_history array payment history
	 * @param    $settings plugin general settings
	 * @param   $counting_types array
	 * @param    $author (optional) int needed to use csv export feature
	 *
	 * @since   1.8
	 */
	static function print_paid_total( $paid_total, $settings, $counting_types, $author = false ) {
		global $pencipwt_global_settings;

		echo '<p>' . __( 'Here you can see the sum of all past payments.', 'penci-pay-writer' ) . '</p>';

		echo '<div class="ppcp_payment_history_element">';
		echo '<p><strong>' . __( 'Paid total', 'penci-pay-writer' ) . '</strong>';

		echo '<div class="ppcp_payment_history_element_content">';
		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		echo '<th style="width: 40%">' . __( 'Type', 'penci-pay-writer' ) . '</th>';
		echo '<th style="width: 20%">' . __( 'Count', 'penci-pay-writer' ) . '</th>';
		echo '<th style="width: 20%">' . __( 'Payment', 'penci-pay-writer' ) . '</th>';
		echo '</tr>';
		echo '</thead>';

		if ( ! empty( $paid_total['ppc_payment'] ) ) {
			foreach ( $paid_total['ppc_payment'] as $id => $data ) {
				if ( $id == 'total' ) {
					continue;
				}
				if ( $data == 0.00 and ( ! isset( $paid_total['ppc_count'][ $id ] ) or $paid_total['ppc_count'][ $id ] == 0.000000 ) ) {
					continue;
				}

				echo '<tr>';
				if ( ! isset( $counting_types[ $id ] ) ) {
					echo '<td>' . $id . '</td>';

					if ( isset( $paid_total['ppc_count'][ $id ] ) ) //possible payment_only counting types of disabled addons
					{
						echo '<td>' . $paid_total['ppc_count'][ $id ] . '</td>';
					} else {
						echo '<td>-</td>';
					}

				} else {

					echo '<td>' . $counting_types[ $id ]['label'] . '</td>';
					if ( ! isset( $counting_types[ $id ]['payment_only'] ) or $counting_types[ $id ]['payment_only'] == false ) {
						echo '<td>' . $paid_total['ppc_count'][ $id ] . '</td>';
					} else {
						echo '<td>-</td>';
					}

				}

				echo '<td>' . PenciPWT_General_Functions::format_payment( $data ) . '</td>';

				echo '</tr>';
			}
		}

		do_action( 'ppcp_paid_total_display_bottom' );

		echo '</table>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Prints a transaction.
	 *
	 * @access  public
	 *
	 * @param    $transaction array (PenciPWT_Payment_History::get_transaction() result)
	 *
	 * @since   1.5.9.3
	 */
	static function print_transaction( $transaction ) {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		if ( $transaction['transaction']['payment_note'] == '' ) {
			$transaction['transaction']['payment_note'] = __( 'None', 'penci-pay-writer' );
		}

		echo '<div id="ppcp_transaction_loading" class="ppc_ajax_loader"><img src="' . $pencipwt_global_settings['folder_path'] . 'style/images/ajax-loader.gif' . '" alt="' . __( 'Loading', 'penci-pay-writer' ) . '..." title="' . __( 'Loading', 'penci-pay-writer' ) . '..." /></div>';
		echo '<div id="ppcp_transaction_error" class="ppc_error"></div>';
		echo '<div id="ppcp_transaction_success" class="ppc_success">' . __( 'Transaction deleted successfully!', 'penci-pay-writer' ) . '</div>';

		echo '<div id="ppcp_transaction_wrap">';

		if ( $perm->can_delete_payment_history() ) {
			echo '<a href="#" class="ppcp_transaction_delete" title="' . __( 'Delete this record', 'penci-pay-writer' ) . '" accesskey="' . $transaction['transaction']['tracking_id'] . '">' . __( 'Delete this record', 'penci-pay-writer' ) . '</a>';
		}

		echo '<h4 style="text-transform: uppercase; margin-bottom: 0;">' . __( 'Transaction details', 'penci-pay-writer' ) . '</h4>';

		echo '<strong>' . __( 'Date', 'penci-pay-writer' ) . '</strong>: ' . date_i18n( get_option( 'date_format' ), $transaction['transaction']['time'] ) . ' ' . date( 'H:i:s', $transaction['transaction']['time'] ) . '<br />';
		echo '<strong>' . __( 'Payment note', 'penci-pay-writer' ) . '</strong>: ' . $transaction['transaction']['payment_note'] . '<br />';

		if ( isset( $transaction['transaction']['payment_method'] ) ) {
			if ( $transaction['transaction']['payment_method'] == 'paypal' or $transaction['transaction']['payment_method'] == 'PayPal' ) {
				$payment_method = 'PayPal';
			} else if ( $transaction['transaction']['payment_method'] == 'mark' ) {
				$payment_method = __( 'Mark as paid', 'penci-pay-writer' );
			}

			echo '<strong>' . __( 'Payment method', 'penci-pay-writer' ) . '</strong>: ' . $payment_method . '<br />';
		}

		echo '<h4 style="text-transform: uppercase; margin-bottom: 0;">' . __( 'Authors and posts', 'penci-pay-writer' ) . '</h4>';
		echo '' . __( 'Below you can see all authors involved in this transaction (user id - username), with a summary of the posts you paid for each of them (post id - post title). Clicking on an author/post will lead you to its payment history.', 'penci-pay-writer' ) . '';
		echo '<br /><br />';

		$posts = new WP_Query( array(
			'post__in'       => $transaction['posts_ids'],
			'orderby'        => 'author date',
			'post_type'      => 'any',
			'post_status'    => array( 'publish', 'pending', 'future', 'private' ),
			'posts_per_page' => - 1
		) );

		$last_author = '';
		$n           = 0;
		foreach ( $posts->posts as $key => $post ) {
			if ( $last_author != $post->post_author ) {

				if ( $n != 0 ) {
					echo '</div>';
				}

				$payment_history_ajax_url = add_query_arg(
					array(
						'action'      => 'ppcp_show_author_payment_history',
						'author_id'   => $post->post_author,
						'height'      => 500,
						'_ajax_nonce' => wp_create_nonce( 'ppcp_show_author_payment_history_' . $post->post_author )
					),
					'admin-ajax.php'
				);

				$transaction_display = '<a href="' . $transaction_ajax_url . '" class="thickbox ppcp_transaction_link" title="' . __( "View transaction details", "ppcp" ) . '">' . $key . '</a>';
				$user                = get_userdata( $post->post_author );


				echo '<a style="text-transform: uppercase; font-weight: bold;" href="' . $payment_history_ajax_url . '" class="thickbox ppcp_transaction_link" title="' . __( 'View payment history', 'penci-pay-writer' ) . '">' . $post->post_author . ' - ' . $user->display_name . '</a><br />';
				echo '<div class="ppcp_transaction_author">';
			}

			$payment_history_ajax_url = add_query_arg(
				array(
					'action'      => 'ppcp_show_post_payment_history',
					'post_id'     => $post->ID,
					'height'      => 500,
					'_ajax_nonce' => wp_create_nonce( 'ppcp_show_post_payment_history_' . $post->ID )
				),
				'admin-ajax.php'
			);

			echo '<a style="" href="' . $payment_history_ajax_url . '" class="thickbox ppcp_transaction_link" title="' . __( 'View payment history', 'penci-pay-writer' ) . '">' . $post->ID . ' - ' . $post->post_title . '</a><br />';

			$last_author = $post->post_author;
			++ $n;
		}

		echo '</div>';

		echo '</div>';
	}

	/**
	 * Shows payment form after stats table in general stats.
	 *
	 * Hooks to post_pay_counter::show_stats() - ppc_html_stats_general_after_stats_form.
	 *
	 * @access  public
	 * @since   1.0
	 */
	static function show_payment_form_general_stats() {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		echo '<div class="ppcp_payment_buttons_wrapper">';

		if ( $perm->can_mark_as_paid() ) {
			echo '<div class="ppcp_payment_buttons">';
			echo '<input type="submit" class="button-secondary ppcp_payment" name="ppcp_mark_as_paid_author" id="ppcp_mark_as_paid_author" value="' . __( 'Mark selected users\' posts as paid', 'penci-pay-writer' ) . ' &raquo;" />';
			echo '</div>';
		}

		if ( $perm->can_see_paypal_functions() ) {
			echo '<div class="ppcp_payment_buttons">';
			echo '<input type="submit" class="button-secondary ppcp_payment" name="ppcp_paypal_payment_author" id="ppcp_paypal_payment_author" value="' . __( 'Pay selected users\' posts with PayPal', 'penci-pay-writer' ) . ' &raquo;" />';
			echo '</div>';
		}

		echo '<input type="hidden" name="ppcp_stats_tstart" value="' . $pencipwt_global_settings['stats_tstart'] . '" />';
		echo '<input type="hidden" name="ppcp_stats_tend" value="' . $pencipwt_global_settings['stats_tend'] . '" />';
		wp_nonce_field( 'ppcp_confirm_payment' );

		echo '<div id="ppcp_payment_error" class="ppc_error"></div>';

		echo '</div>';
	}

	/**
	 * Shows payment form after stats table in author stats.
	 *
	 * Hooks to post_pay_counter::show_stats() - ppc_html_stats_author_after_stats_form.
	 *
	 * @access  public
	 * @since   1.0
	 */
	static function show_payment_form_author_stats() {
		global $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();

		if ( $perm->can_mark_as_paid() ) {
			echo '<div class="ppcp_payment_buttons">';
			echo '<input type="submit" class="button-secondary ppcp_payment" name="ppcp_mark_as_paid_post" id="ppcp_mark_as_paid_post" value="' . __( 'Mark selected posts as paid', 'penci-pay-writer' ) . ' &raquo;" />';
			echo '</div>';
		}

		if ( $perm->can_see_paypal_functions() ) {
			echo '<div class="ppcp_payment_buttons">';
			echo '<input type="submit" class="button-secondary ppcp_payment" name="ppcp_paypal_payment_post" id="ppcp_paypal_payment_post" value="' . __( 'Pay selected posts with PayPal', 'penci-pay-writer' ) . ' &raquo;" />';
			echo '</div>';
		}

		echo '<input type="hidden" name="ppcp_stats_tstart" value="' . $pencipwt_global_settings['stats_tstart'] . '" />';
		echo '<input type="hidden" name="ppcp_stats_tend" value="' . $pencipwt_global_settings['stats_tend'] . '" />';
		wp_nonce_field( 'ppcp_confirm_payment' );

		echo '<div id="ppcp_payment_error" class="ppc_error"></div>';
	}

	/**
	 * Shows export to csv link in stats header.
	 *
	 * Hooks to post_pay_counter::show_stats() - ppc_before_stats_html.
	 *
	 * @access  public
	 *
	 * @param    $page_permalink string page permalink
	 *
	 * @since   1.0
	 */
	static function csv_export( $page_permalink ) {
		$perm = new PenciPWT_Permissions();

		if ( $perm->can_csv_export() ) {
			echo '<br />';
			echo '<a href="' . wp_nonce_url( $page_permalink . '&amp;export=csv&amp;noheader=true', 'ppcp_csv_export' ) . '" title="' . __( 'Export to csv', 'penci-pay-writer' ) . '">' . __( 'Export to csv', 'penci-pay-writer' ) . '</a>';
		}
	}


	/**
	 * Shows author payment history link in stats header.
	 *
	 * Hooks to post_pay_counter::show_stats() - ppc_before_stats_html.
	 *
	 * @access  public
	 *
	 * @param    $page_permalink string page permalink
	 *
	 * @since   1.8.1
	 */
	static function author_payment_history_header( $page_permalink ) {
		global $pencipwt_global_settings;

		$perm = new PenciPWT_Permissions();

		if ( $pencipwt_global_settings['current_page'] == 'stats_detailed' and $perm->can_see_payment_history() ) {
			$payment_history_ajax_url = add_query_arg(
				array(
					'action'      => 'ppcp_show_author_payment_history',
					'author_id'   => (int) $_REQUEST['author'],
					'height'      => 500,
					'_ajax_nonce' => wp_create_nonce( 'ppcp_show_author_payment_history_' . (int) $_REQUEST['author'] )
				),
				'admin-ajax.php'
			);

			echo '<br />';
			echo '<a href="' . $payment_history_ajax_url . '" class="thickbox author_payment_history" title="' . __( 'See payment history', 'penci-pay-writer' ) . '">' . __( 'See payment history', 'penci-pay-writer' ) . '</a>';
		}
	}

	/**
	 * Adds currency symbol to a payment amount either before or after it depending on settings.
	 *
	 * @access  public
	 *
	 * @param    $amount string payment amount
	 *
	 * @return  string amount with currency symbol
	 * @since   1.2
	 */
	static function add_currency_symbol( $amount ) {
		if ( ! is_array( self::$currency_symbol ) ) {
			if ( pencipwt_get_setting( 'currency_symbol_before' ) ) {
				self::$currency_symbol['position'] = 'before';
			} else if ( pencipwt_get_setting( 'currency_symbol_after' ) ) {
				self::$currency_symbol['position'] = 'after';
			} else {
				self::$currency_symbol['position'] = 'after';
			}

			self::$currency_symbol['symbol'] = html_entity_decode( pencipwt_get_setting( 'currency_symbol' ) );
		}

		if ( self::$currency_symbol['position'] == 'before' ) {
			$amount = self::$currency_symbol['symbol'] . $amount;
		} else if ( self::$currency_symbol['position'] == 'after' ) {
			$amount = $amount . self::$currency_symbol['symbol'];
		}

		return $amount;
	}

	/**
	 * Prints due payment total in overall stats.
	 *
	 * Hooks to PenciPWT_HTML_Functions::print_overall_stats() - ppc_html_overall_stats.
	 *
	 * @access  public
	 *
	 * @param   $overall_stats array computed overall stats
	 *
	 * @since   1.3
	 */
	static function print_overall_stats_implement( $overall_stats ) {
		?>

        <tr>

            <td width="40%"><?php _e( 'Total Paid:', 'penci-pay-writer' ); ?></td>
            <td align="left"
                width="10%"><?php echo PenciPWT_General_Functions::format_payment( sprintf( '%.2f', $overall_stats['total_paid'] ) ); ?></td>
        </tr>

        <tr>
            <td width="40%"><?php _e( 'Total Unpaid:', 'penci-pay-writer' ); ?></td>
            <td align="left"
                width="10%"><?php echo PenciPWT_General_Functions::format_payment( sprintf( '%.2f', $overall_stats['due_payment'] ) ); ?></td>
        </tr>


		<?php
	}

	/**
	 * Acts as PayPal return & cancel URL, mark as paid confirmation, displays messages.
	 *
	 * Hooks to post_pay_counter::show_stats() - ppc_before_stats_html.
	 *
	 * @access  public
	 * @since   1.3
	 */
	static function paypal_messages() {
		if ( isset( $_GET['success'] ) ) {

			if ( $_GET['success'] == 'mark_paid' ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Selected items have been marked as paid successfully.', 'penci-pay-writer' ) . '</p></div>';
			}

			if ( $_GET['success'] == 'paypal' ) {
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'The PayPal payment was %2$ssuccessfully executed%3$s. A new record has been added for each post\'s payment history, and in a while PayPal will verify the payment. As soon as the payment will be notified as valid, the related record in the payment history will gain a %1$s badge.', 'penci-pay-writer' ), '<em>' . __( 'Verified', 'penci-pay-writer' ) . '</em>', '<strong>', '</strong>' ) . '</p></div>';
			}

		}

		if ( isset( $_GET['error'] ) ) {

			if ( $_GET['error'] == 'paypal' ) {
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'The PayPal payment was %2$ssuccessfully canceled%3$s. No money has been moved. However, %2$sa new record has been added%3$s for each post\'s payment history. They will show up as %1$s, but you may want to delete those records to prevent them from interfering in the due payment computing.', 'penci-pay-writer' ), '<em>' . __( 'Unverified', 'penci-pay-writer' ) . '</em>', '<strong>', '</strong>' ) . '</p></div>';
			}

		}
	}

	/**
	 * Shows header part for the stats page, including the form to adjust the time window
	 *
	 * @access  public
	 *
	 * @param   $current_page string current page title
	 * @param   $page_permalink string current page permalink
	 *
	 * @since   2.0
	 */
	static function show_stats_page_header( $current_page, $page_permalink ) {
		global $pencipwt_global_settings, $wp_roles;
		$perm             = new PenciPWT_Permissions();
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		?>

        <form action="" method="post">
            <div id="ppc_stats_header" style="direction: ltr;">
                <div id="ppc_stats_header_datepicker">
                    <h3>

						<?php echo __( 'Showing Posts for', 'penci-pay-writer' ) . ' ';
						$time_range_options = array(
							'this_month' => __( 'This Month', 'penci-pay-writer' ),
							'last_month' => __( 'Last Month', 'penci-pay-writer' ),
							'this_year'  => __( 'This Year', 'penci-pay-writer' ),
							'this_week'  => __( 'This Week', 'penci-pay-writer' ),
							'all_time'   => __( 'All Time', 'penci-pay-writer' ),
							'custom'     => __( 'Custom', 'penci-pay-writer' )
						);

						echo '<select name="ppc-time-range" id="ppc-time-range">';

						$_REQUEST = array_merge( $_GET, $_POST );
						foreach ( $time_range_options as $key => $value ) {
							$checked = '';

							//Default select choice
							if ( isset( $_REQUEST['ppc-time-range'] ) ) {
								if ( $_REQUEST['ppc-time-range'] == $key ) {
									$checked = 'selected="selected"';
								}
							} else {
								if ( ( pencipwt_get_setting( 'default_stats_time_range_custom' ) or pencipwt_get_setting( 'default_stats_time_range_start_day' ) or isset( $_GET['tstart'] ) ) and $key == 'custom' ) {
									$checked = 'selected="selected"';
								} else if ( pencipwt_get_setting( 'default_stats_time_range_week' ) and $key == 'this_week' ) {
									$checked = 'selected="selected"';
								} else if ( pencipwt_get_setting( 'default_stats_time_range_month' ) and $key == 'this_month' ) {
									$checked = 'selected="selected"';
								} else if ( pencipwt_get_setting( 'default_stats_time_range_last_month' ) and $key == 'last_month' ) {
									$checked = 'selected="selected"';
								} else if ( pencipwt_get_setting( 'default_stats_time_range_this_year' ) and $key == 'this_year' ) {
									$checked = 'selected="selected"';
								} else if ( pencipwt_get_setting( 'default_stats_time_range_all_time' ) and $key == 'all_time' ) {
									$checked = 'selected="selected"';
								}
							}

							echo '<option value="' . $key . '" ' . $checked . '>' . $value . '</option>';
						}

						echo '</select>';

						if ( 'General' != $current_page ) {
							echo ' - "' . $current_page . '"';
						}

						echo '<div id="ppc-time-range-custom" style="display: none; margin-top: 10px;">';
						echo sprintf( __( 'From %1$s to %2$s', 'penci-pay-writer' ), '<input type="text" name="tstart" id="post_pay_counter_time_start" class="mydatepicker" value="' . date( 'Y-m-d', $pencipwt_global_settings['stats_tstart'] ) . '" accesskey="' . $pencipwt_global_settings['stats_tstart'] . '" size="8" />', '<input type="text" name="tend" id="post_pay_counter_time_end" class="mydatepicker" value="' . date( 'Y-m-d', $pencipwt_global_settings['stats_tend'] ) . '" accesskey="' . $pencipwt_global_settings['stats_tend'] . '" size="8" />' );
						echo '</div>';

						//Display filter by user role, user and/or category in general stats
						echo '<div style="margin-top: 10px;">';
						if ( $pencipwt_global_settings['current_page'] == 'stats_general' and $perm->can_see_others_general_stats() ) {
							echo __( 'Filter by User Role', 'penci-pay-writer' ) . ' ';
							echo '<select name="role" id="ppc_stats_role">';
							echo '<option value="ppc_any">' . __( 'Any', 'penci-pay-writer' ) . '</option>';

							foreach ( $wp_roles->role_names as $key => $value ) {
								if ( ! in_array( $key, pencipwt_get_setting( 'counting_allowed_user_roles' ) ) ) {
									continue;
								} //skip non-allowed roles

								$checked = '';

								if ( isset( $pencipwt_global_settings['stats_role'] ) and $key == $pencipwt_global_settings['stats_role'] ) {
									$checked = 'selected="selected"';
								}

								echo '<option value="' . $key . '" ' . $checked . '>' . $value . '</option>';
							}

							echo '</select>';

							echo ' - ' . __( 'User', 'penci-pay-writer' ) . ' ';
							echo '<select name="author" id="ppc_stats_user">';
							echo '<option value="ppc_any">' . __( 'Any', 'penci-pay-writer' ) . '</option>';

							$all_users = get_users( array(
								'orderby'  => 'nicename',
								'role__in' => pencipwt_get_setting( 'counting_allowed_user_roles' ),
								'fields'   => array( 'ID', 'display_name' )
							) );
							foreach ( $all_users as $user ) {
								$checked = '';

								if ( isset( $pencipwt_global_settings['stats_user'] ) and $key == $pencipwt_global_settings['stats_user'] ) {
									$checked = 'selected="selected"';
								}

								echo '<option value="' . $user->ID . '" ' . $checked . ' />' . $user->display_name . '</option>';
							}

							echo '</select>';
						}

						echo '</div>';

						/**
						 * Fires after the HTML display of "Showing stats from ... to ... - "General|User" - Role" in stats page heading.
						 *
						 * @param string $current_page whether "General" or username of currently displayed author.
						 * @param string $page_permalink page URL
						 *
						 * @since    2.49
						 */

						do_action( 'ppc_stats_after_time_range_fields', $current_page, $page_permalink );
						?>
                    </h3>
                    <div style="margin: 15px 0"><input type="submit" class="button-secondary"
                                                       name="post_pay_counter_submit"
                                                       value="<?php echo __( 'Show Data', 'penci-pay-writer' ); ?>"/>
                    </div>

                </div>

                <div id="ppc_stats_header_features">
			<span id="ppc_stats_header_links">
		<?php
		if ( $pencipwt_global_settings['current_page'] == 'stats_detailed' ) {

			if ( ! isset( $_REQUEST['ppc-time-range'] ) ) { ?>

                <a href="<?php echo admin_url( $pencipwt_global_settings['stats_menu_link'] . '&amp;tstart=' . $pencipwt_global_settings['stats_tstart'] . '&amp;tend=' . $pencipwt_global_settings['stats_tend'] ); ?>"
                   title="<?php _e( 'Back to general', 'penci-pay-writer' ); ?>"><?php _e( 'Back to general', 'penci-pay-writer' ); ?></a>

			<?php } else { ?>

                <a href="<?php echo admin_url( $pencipwt_global_settings['stats_menu_link'] . '&amp;tstart=' . $pencipwt_global_settings['stats_tstart'] . '&amp;tend=' . $pencipwt_global_settings['stats_tend'] . '&amp;ppc-time-range=' . $_REQUEST['ppc-time-range'] ); ?>"
                   title="<?php _e( 'Back to general', 'penci-pay-writer' ); ?>"><?php _e( 'Back to general', 'penci-pay-writer' ); ?></a>

			<?php }
		}

		do_action( 'ppc_stats_header_links', $page_permalink ); ?>

			</span>
                    <a href="<?php echo $page_permalink; ?>"
                       title="<?php _e( 'Get Current View URL', 'penci-pay-writer' ); ?>"><?php _e( 'Get Current View URL', 'penci-pay-writer' ); ?></a>
                </div>

            </div>
        </form>
        <div class="clear"></div>
        <hr class="ppc_hr_divider"/>

		<?php
	}

	/**
	 * Shows HTML stats.
	 *
	 * @access  public
	 *
	 * @param   $formatted_stats array formatted stats
	 * @param   $raw_stats array ordered-by-author stats
	 * @param   $author array optional whether detailed stats
	 *
	 * @since   2.0
	 */
	static function get_html_stats( $formatted_stats, $raw_stats, $author = null ) {
		?>

        <table class="widefat fixed" id="ppc_stats_table">
            <thead>
            <tr>

				<?php
				foreach ( $formatted_stats['cols'] as $col_id => $value ) { //cols work the same both for general and user
					?>

                    <th scope="col"><?php echo $value; ?></th>

					<?php
					if ( is_array( $author ) ) {
						do_action( 'ppc_general_stats_html_cols_after_' . $col_id );
					} else {
						do_action( 'ppc_author_stats_html_cols_after_default' . $col_id );
					}
				}

				if ( is_array( $author ) ) {
					do_action( 'ppc_general_stats_html_cols_after_default' );
				} else {
					do_action( 'ppc_author_stats_html_cols_after_default' );
				}
				?>

            </tr>
            </thead>

            <tfoot>
            <tr>

				<?php
				foreach ( $formatted_stats['cols'] as $col_id => $value ) {
					?>

                    <th scope="col"><?php echo $value; ?></th>

					<?php
					if ( is_array( $author ) ) {
						do_action( 'ppc_general_stats_html_cols_after_' . $col_id );
					} else {
						do_action( 'ppc_author_stats_html_cols_after_default' . $col_id );
					}
				}

				if ( is_array( $author ) ) {
					do_action( 'ppc_author_stats_html_cols_after_default' );
				} else {
					do_action( 'ppc_general_stats_html_cols_after_default' );
				}
				?>

            </tr>
            </tfoot>

            <tbody>

			<?php
			echo self::get_html_stats_tbody( $formatted_stats, $raw_stats, $author, "html", true, "echo" );
			?>

            </tbody>
        </table>

		<?php
	}

	/**
	 * Builds the stats table body for html display.
	 *
	 * @param array $formatted_stats
	 * @param array $raw_stats
	 * @param string (optional) $author id
	 * @param string (optional) $filter_name filter name
	 * @param bool (optional) $format_payment whether to format payment (eg. add currency symbol)
	 * @param string (optional) $echo_or_return what to do with the output, if echo or return. If echoed, actions are fired as well
	 *
	 * @return    string html stats
	 * @since    2.503
	 * @access    public
	 */
	static function get_html_stats_tbody( $formatted_stats, $raw_stats, $author = null, $filter_name = "html", $format_payment = true, $echo_or_return = "return" ) {
		global $current_user, $pencipwt_global_settings;
		$perm = new PenciPWT_Permissions();
		$html = "";

		if ( is_array( $author ) ) {
			foreach ( $formatted_stats['stats'] as $author => $author_stats ) {

				$user_settings  = PenciPWT_General_Functions::get_settings( $author, true );
				$counting_types = $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' );

				//Handle sorting
				if ( isset( $_REQUEST['orderby'] ) and isset( $_REQUEST['order'] )
				                                       and ( $_REQUEST['order'] == 'desc' or $_REQUEST['order'] == 'asc' )
				                                           and ! ( $_REQUEST['orderby'] == 'post_publication_date' and $_REQUEST['order'] == 'desc' ) ) { //don't sort if post_publication_date desc, it's already sorted
					uasort( $author_stats, 'ppc_uasort_stats_sort' );
				}

				foreach ( $author_stats as $post_id => $post_stats ) {
					$post = $raw_stats[ $author ][ $post_id ];

					$tr_opacity = '';
					if ( penci_get_setting( 'counting_payment_only_when_total_threshold' ) ) {
						if ( $post->ppc_misc['exceed_threshold'] == false ) {
							$tr_opacity = ' style="opacity: 0.40;"';
						}
					}

					$html .= '<tr' . $tr_opacity . '>';

					foreach ( $post_stats as $field_name => $field_value ) {
						$maybe_skip = apply_filters( 'ppc_author_stats_' . $filter_name . '_skip_field', false, $field_name );
						if ( $maybe_skip ) {
							continue;
						}

						$field_value = apply_filters( 'ppc_author_stats_' . $filter_name . '_each_field_value', $field_value, $field_name, $post );

						switch ( $field_name ) {
							//Attach link to post title: if user can edit posts, attach edit link (faster), if not post permalink (slower)
							case 'post_title':

								if ( $user_settings['stats_display_edit_post_link'] ) {
									$post_link = get_edit_post_link( $post->ID );
									if ( $post_link == '' ) {
										$post_link = get_permalink( $post->ID );
									}

									$field_value = '<a href="' . $post_link . '" title="' . esc_html( $post->post_title ) . '">' . esc_html( $field_value ) . '</a>';
								}

								break;

							case 'post_total_payment':
								$tooltip = PenciPWT_Counting_Stuff::build_payment_details_tooltip( $post->ppc_count['normal_count'], $post->ppc_payment['normal_payment'], $counting_types );
								if ( $format_payment ) {
									$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . PenciPWT_General_Functions::format_payment( $field_value ) . '</abbr>';
								} else {
									$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . $field_value . '</abbr>';
								}
								break;

							case 'post_words':
							case 'post_visits':
							case 'post_images':
							case 'post_comments':
								$count_field_value = substr( $field_name, 5, strlen( $field_name ) );
								if ( $post->ppc_count['normal_count'][ $count_field_value ]['real'] != $post->ppc_count['normal_count'][ $count_field_value ]['to_count'] ) {
									$field_value = '<abbr title="' . sprintf( __( 'Total is %1$s. %2$s Displayed is what you\'ll be paid for.', 'penci-pay-writer' ), $post->ppc_count['normal_count'][ $count_field_value ]['real'], '&#13;' ) . '" class="ppc_count_column">' . $field_value . '</abbr>';
								}

								break;

							//Terrible hack to localize at least some post statuses
							case 'post_status':
								if ( $field_value == 'publish' ) {
									$field_value = __( 'Publish', 'penci-pay-writer' );
								} else if ( $field_value == 'pending' ) {
									$field_value = __( 'Pending', 'penci-pay-writer' );
								} else if ( $field_value == 'future' ) {
									$field_value = __( 'Future', 'penci-pay-writer' );
								}

								break;
						}

						$html .= '<td class="' . $field_name . '">' . $field_value . '</td>';
						$html = apply_filters( 'ppc_author_stats_' . $filter_name . '_after_' . $field_name, $html, $author, $formatted_stats, $post );
					}

					$html = apply_filters( 'ppc_author_stats_' . $filter_name . '_after_each_default_filter', $html, $author, $formatted_stats, $post );

					//Bit entangled due to retro-compatibility with PRO versions <= 1.5.8.3, when this function echoed directly (thus using actions and not filters)
					if ( $echo_or_return == "echo" ) {
						echo $html;
						$html = "";
						do_action( 'ppc_author_stats_' . $filter_name . '_after_each_default', $author, $formatted_stats, $post );
					}

					$html .= '</tr>';
				}
			}

		} else {

			//Handle sorting
			if ( isset( $_REQUEST['orderby'] ) and isset( $_REQUEST['order'] ) and ( $_REQUEST['order'] == 'desc' or $_REQUEST['order'] == 'asc' ) ) {
				uasort( $formatted_stats['stats'], 'ppc_uasort_stats_sort' );
			}

			foreach ( $formatted_stats['stats'] as $author => $author_stats ) {
				$html .= '<tr>';

				foreach ( $formatted_stats['cols'] as $field_name => $label ) {
					$maybe_skip = apply_filters( 'ppc_general_stats_' . $filter_name . '_skip_field', false, $field_name );
					if ( $maybe_skip ) {
						continue;
					}

					if ( isset( $author_stats[ $field_name ] ) ) {
						$field_value = $author_stats[ $field_name ];
						$field_value = apply_filters( 'ppc_general_stats_' . $filter_name . '_each_field_value', $field_value, $field_name, $raw_stats[ $author ], $author );

						//Cases in which other stuff needs to be added to the output
						switch ( $field_name ) {
							case 'author_name':
								if ( ( $perm->can_see_others_detailed_stats() or $author == $current_user->ID ) and $filter_name == "html" ) {
									$field_value = '<a href="' . PenciPWT_General_Functions::get_the_author_link( $author ) . '" title="' . __( 'Go to detailed view', 'penci-pay-writer' ) . '">' . esc_html( $field_value ) . '</a>';
								}
								break;

							case 'author_total_payment':
								//Avoid tooltip non-isset notice
								if ( isset( $raw_stats[ $author ]['total']['ppc_misc']['tooltip_normal_payment'] ) ) {
									$tooltip = $raw_stats[ $author ]['total']['ppc_misc']['tooltip_normal_payment'];
								} else {
									$tooltip = '';
								}

								if ( $format_payment ) {
									$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . PenciPWT_General_Functions::format_payment( $field_value ) . '</abbr>';
								} else {
									$field_value = '<abbr title="' . $tooltip . '" class="ppc_payment_column">' . $field_value . '</abbr>';
								}
								break;

							case 'author_words':
							case 'author_visits':
							case 'author_images':
							case 'author_comments':
								$count_field_name = substr( $field_name, 7, strlen( $field_name ) );
								if ( $raw_stats[ $author ]['total']['ppc_count']['normal_count'][ $count_field_name ]['real'] != $raw_stats[ $author ]['total']['ppc_count']['normal_count'][ $count_field_name ]['to_count'] ) {
									$field_value = '<abbr title="Total is ' . $raw_stats[ $author ]['total']['ppc_count']['normal_count'][ $count_field_name ]['real'] . '&#13;' . __( 'Displayed is what you\'ll be paid for.', 'penci-pay-writer' ) . '" class="ppc_count_column">' . $field_value . '</abbr>';
								}
								break;
						}

						$html .= '<td class="' . $field_name . '">' . $field_value . '</td>';
						$html = apply_filters( 'ppc_general_stats_' . $filter_name . '_after_' . $field_name, $html, $author, $formatted_stats, $raw_stats );

					} else {
						$html .= '<td class="' . $field_name . '">' . apply_filters( 'ppc_general_stats_each_field_empty_value', '0', $field_name, $raw_stats[ $author ], $author ) . '</td>';
					}
				}

				$html = apply_filters( 'ppc_general_stats_' . $filter_name . '_after_each_default_filter', $html, $author, $formatted_stats, $raw_stats );

				//Bit entangled due to retro-compatibility with PRO versions <= 1.5.8.3, when this function echoed directly (thus using actions and not filters)
				if ( $echo_or_return == "echo" ) {
					echo $html;
					$html = "";
					do_action( 'ppc_general_stats_' . $filter_name . '_after_each_default', $author, $formatted_stats, $raw_stats );
				}

				$html .= '</tr>';
			}

		}

		return $html;
	}

	/**
	 * Shows HTML overall stats.
	 *
	 * @access  public
	 *
	 * @param   $overall_stats array overall stats
	 *
	 * @since   2.0
	 */
	static function print_overall_stats( $overall_stats ) {
		global $pencipwt_global_settings;

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		$counting_types   = array_merge( $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'author' ), $pencipwt_global_settings['counting_types_object']->get_all_counting_types( 'post' ) );
		?>

        <table class="widefat fixed ppc_overall_stats_table" style="max-width: 750px;">
            <tr>
                <td width="40%"><?php _e( 'Total Posts:', 'penci-pay-writer' ); ?></td>
                <td align="left" width="10%"><?php echo $overall_stats['posts']; ?></td>
            </tr>

			<?php
			do_action( 'ppc_html_overall_stats', $overall_stats );
			?>

            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <strong><?php echo __( 'Totals', 'penci-pay-writer' ); ?></strong></td>
            </tr>

			<?php
			$n = 0;
			foreach ( $overall_stats['payment'] as $id => $data ) {
				if ( $n % 2 == 0 ) {
					echo '<tr>';
				}

				if ( isset( $counting_types[ $id ] ) ) {

					if ( isset( $counting_types[ $id ]['display_status_index'] ) and isset( $general_settings[ $counting_types[ $id ]['display_status_index'] ] ) ) {
						$display = $general_settings[ $counting_types[ $id ]['display_status_index'] ];
					} else {
						$display = $counting_types[ $id ]['display'];
					}

					switch ( $display ) {
						case 'both':
							$disp = $overall_stats['count'][ $id ] . ' (' . PenciPWT_General_Functions::format_payment( $overall_stats['payment'][ $id ] ) . ')';
							break;

						case 'count':
							$disp = $overall_stats['count'][ $id ];
							break;

						case 'payment':
						case 'none':
						case 'tooltip':
							$disp = PenciPWT_General_Functions::format_payment( $overall_stats['payment'][ $id ] );
							break;
					}
				}

				if ( isset( $counting_types[ $id ] ) ):
					?>

                    <td width="40%"><?php echo ucfirst( sprintf( '%s:', $counting_types[ $id ]['label'] ) ); ?></td>
                    <td align="left" width="10%"><?php echo $disp; ?></td>

				<?php
				endif;
				if ( $n % 2 == 1 ) {
					echo '</tr>';
				}

				++ $n;
			}

			do_action( 'ppc_html_overall_stats_counts', $overall_stats );
			?>

        </table>

		<?php
	}


}
