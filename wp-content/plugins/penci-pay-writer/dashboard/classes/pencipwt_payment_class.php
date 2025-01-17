<?php

class PenciPWT_Payment {

	/**
	 * Unserializes given AJAX-request form data through successive explode().
	 *
	 * @access  public
	 *
	 * @param   $data string serialized data
	 *
	 * @return    array unserialized data
	 * @since   1.0
	 */
	static function unserialize_payment_data( $data ) {
		$return_array    = array();
		$separate_fields = explode( '&', $data );

		foreach ( $separate_fields as $single ) {
			$values                     = explode( '=', $single );
			$return_array[ $values[0] ] = $values[1];
		}

		return $return_array;
	}

	/**
	 * Prepares a payment to users.
	 *
	 * @access  public
	 *
	 * @param   $stats array stats
	 *
	 * @return    array payment data and stats
	 * @since   1.0
	 */
	static function prepare_payment( $stats ) {
		global $pencipwt_global_settings, $pencipwt_global_settings;

		$tracking_id = uniqid( rand( 1, 1000000000 ) );
		$authors_ids = array_keys( $stats['raw_stats'] );

		$payment_data = array(
			'transaction_details'              => array(
				'tracking_id' => $tracking_id,
				'time'        => current_time( 'timestamp' )
			),
			'authors_for_payment_data'         => array(),
			'authors_for_payment_history_data' => array(),
			'posts_for_payment_history_data'   => array()
		);

		foreach ( $authors_ids as $author ) {
			//This for the payment
			$payment_data['authors_for_payment_data'][ $author ]['ppc_payment'] = $stats['raw_stats'][ $author ]['total']['ppc_payment']['due_payment'];
			$payment_data['authors_for_payment_data'][ $author ]['tracking_id'] = $tracking_id;

			$payment_data['authors_for_payment_data'][ $author ] = apply_filters( 'ppcp_authors_for_payment_data', $payment_data['authors_for_payment_data'][ $author ], $stats, $author );

			//This for the author payment history
			//Verified index for each author
			$payment_data['authors_for_payment_history_data'][ $author ][ $tracking_id ]['verified']             = 0;
			$payment_data['authors_for_payment_history_data'][ $author ][ $tracking_id ]['ppc_payment']['total'] = 0;

			$payment_data['authors_for_payment_history_data'][ $author ][ $tracking_id ] = apply_filters( 'ppcp_authors_for_payment_history_data', $payment_data['authors_for_payment_history_data'][ $author ][ $tracking_id ], $stats, $author );

			//This for the post payment history
			foreach ( $stats['raw_stats'][ $author ] as $post_id => $single ) {

				//Skip author's total and posts with due payment == 0
				if ( $post_id === 'total' ) {
					continue;
				}
				if ( $single->ppc_payment['due_payment']['total'] == 0 ) {
					continue;
				}

				$ppc_count_history = array();
				foreach ( $single->ppc_count['due_payment'] as $counting => $data ) {
					if ( $data['to_count'] == 0.000000 ) {
						unset( $single->ppc_payment['due_payment'][ $counting ] );
						continue;
					}

					$ppc_count_history[ $counting ] = $data['to_count'];
				}

				$payment_data['posts_for_payment_history_data'][ $post_id ][ $tracking_id ]['ppc_count']   = $ppc_count_history;
				$payment_data['posts_for_payment_history_data'][ $post_id ][ $tracking_id ]['ppc_payment'] = $single->ppc_payment['due_payment'];

				$payment_data['posts_for_payment_history_data'][ $post_id ] = apply_filters( 'ppcp_posts_for_payment_history_data', $payment_data['posts_for_payment_history_data'][ $post_id ], $stats['raw_stats'][ $author ], $author );
			}

		}

		return apply_filters( 'ppcp_payment_data', array( 'payment_data' => $payment_data, 'stats' => $stats ) );
	}

	/**
	 * Sends out payment notifications to users for a specific transaction.
	 *
	 * Runs either when IPN confirmation is received (PayPal)
	 * or after 10 minutes after payment through a wp_schedule_single_event() under name "ppcp_send_email_notifications" 10 minutes after payment.
	 *
	 * @access  public
	 *
	 * @param   $tracking_id string
	 *
	 * @since   1.3
	 */
	static function send_notifications( $tracking_id ) {
		$blog_name = get_bloginfo( 'name' );
		$subject   = $blog_name . ' - You got money!';

		$headers   = array();
		$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>';
		$headers[] = 'Content-Type: text/html';

		$paid_posts = PenciPWT_Payment_History::get_posts_payment_histories_by_tracking_id( $tracking_id );
		$emails     = array();

		//Split paid posts in respective authors
		foreach ( $paid_posts as $single ) {
			$post_details                                                     = get_post( $single->post_id );
			$emails[ $single->post_author ]['paid_posts'][ $single->post_id ] = $post_details->post_title;
		}

		//Add author counting types
		$paid_authors = PenciPWT_Payment_History::get_authors_payment_histories_by_tracking_id( $tracking_id );
		foreach ( $paid_authors as $single ) {
			$emails[ $single->user_id ]['author_payment_history'] = maybe_unserialize( $single->meta_value )[ $tracking_id ];
		}

		//Build email for each author
		foreach ( $emails as $author => &$data ) {
			$total_payment     = 0;
			$userdata          = get_userdata( $author );
			$data['recipient'] = $userdata->user_email;

			$text            = __( 'Dear', 'penci-pay-writer' ) . ' ' . $userdata->display_name . ',<br />';
			$text            .= __( 'Guess what? You\'ve just been paid! See the details of your payment below', 'penci-pay-writer' ) . '<br />';
			$payment_details = '';

			foreach ( $data['author_payment_history']['ppc_payment'] as $cnt_type => $payment ) {
				if ( $cnt_type == 'total' ) {
					continue;
				}

				$payment_details .= '<p><strong>' . $cnt_type . '</strong>: ' . PenciPWT_HTML_Functions::add_currency_symbol( sprintf( '%.2f', $payment ) ) . '</p>';
				$total_payment   += $payment;
			}

			if ( isset( $data['paid_posts'] ) ) {
				$payment_details .= '<p><strong>POSTS</strong></p>';

				foreach ( $data['paid_posts'] as $post_id => $post_title ) {
					//Enqueue post title
					$payment_details .= '<p>' . $post_title . '<br />';

					//Enqueue post payment
					$payment_history = PenciPWT_Payment_History::get_post_payment_history( $post_id );
					$payment_details .= PenciPWT_HTML_Functions::add_currency_symbol( sprintf( '%.2f', $payment_history[ $tracking_id ]['ppc_payment']['total'] ) ) . '</p>';
					$total_payment   += $payment_history[ $tracking_id ]['ppc_payment']['total'];
				}
			}

			//Bail if no real payment for author
			if ( $total_payment == 0 ) {
				unset( $emails[ $author ] );
				continue;
			}

			$payment_details .= '<p><strong>' . __( 'Total amount:', 'penci-pay-writer' ) . ' ' . PenciPWT_HTML_Functions::add_currency_symbol( $total_payment ) . '</strong></p>';

			$text_complete = $text . $payment_details;

			$data['body'] = $text_complete;

			/* Filter email details.
			 *
			 * @since 1.6.8.2
			 */
			$subject      = apply_filters( 'ppcp_notification_email_subject', $subject, $data['recipient'], $tracking_id, $author );
			$data['body'] = apply_filters( 'ppcp_notification_email_body', $data['body'], $data['recipient'], $tracking_id, $author );
			$headers      = apply_filters( 'ppcp_notification_email_headers', $headers, $data['recipient'], $tracking_id, $author );


		}

		do_action( 'ppcp_sent_payment_notification_emails' );
	}
}
