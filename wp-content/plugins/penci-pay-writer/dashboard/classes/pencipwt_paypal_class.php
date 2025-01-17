<?php
class PenciPWT_PayPal_Functions {
	public $error;
	public $sandbox;
	public $paypal_status;
	public $fees_payer;
	public $currency_code;

	/**
	 * Populates class variables with PayPal credentials and settings.
	 */

	function __construct() {
		$perm = new PenciPWT_Permissions();
		if ( ! $perm->can_see_paypal_functions() ) {
			$this->error = new WP_Error( 'ppcp_can_see_paypal_functions_error', __( 'Error: you do not have the permissions to do this.', 'penci-pay-writer' ) );
		}

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );

		//Get sandbox settings
		$this->sandbox = self::is_sandbox( pencipwt_get_setting( 'paypal_sandbox' ) );
		if ( is_wp_error( $this->sandbox ) ) {
			$this->error = $this->sandbox;
		}

		//Checks that PayPal is correctly set up
		$this->paypal_status = self::is_paypal_available( $general_settings );
		if ( is_wp_error( $this->paypal_status ) ) {
			$this->error = $this->paypal_status;
		}

		$this->currency_code = pencipwt_get_setting( 'paypal_currency_code' );
	}

	/**
	 * Calls home for payment handling.
	 *
	 * @access  public
	 *
	 * @param    $parameters array http request parameters
	 *
	 * @return    array request result details
	 * @since   1.3
	 */

	function payment_request( $parameters ) {
		return $parameters;
	}

	/**
	 * Handles PayPal Adaptive Payments requests.
	 *
	 * Thanks to Angell's eye PayPal PHP class for the chunk (although has been edited).
	 *
	 * @access  public
	 *
	 * @param    $current_page string redirect page for adaptive
	 * @param    $receivers array payment receivers
	 * @param    $tracking_id string unique tracking id
	 *
	 * @return    array PayPal result
	 * @since   1.3
	 */
	function execute_adaptive_payment( $current_page, $receivers, $tracking_id ) {
		global $pencipwt_global_settings;

		//Get fees payer
		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		$fees_payer       = self::get_paypal_fees_payer( $general_settings );
		if ( is_wp_error( $fees_payer ) ) {
			return $fees_payer;
		}

		$current_page = str_replace( '&', '&amp;', $current_page );

		$ipn_url = "";
		if ( pencipwt_get_setting( "paypal_ipn" ) ) {
			$ipn_url = get_bloginfo( 'wpurl' ) . '/ppcp/paypal';
		}

		//ApplicationID, APIUsername, APIPassword, APISignature are set on plugin's servers (they're the developer ones)
		$PayPalConfig = array(
			'Sandbox'               => $this->sandbox,
			'DeveloperAccountEmail' => '',
			'ApplicationID'         => '',
			'DeviceID'              => '',
			'IPAddress'             => $_SERVER['REMOTE_ADDR'],
			'APIUsername'           => '',
			'APIPassword'           => '',
			'APISignature'          => '',
			'APISubject'            => ''
		);

		$PayRequestFields = array(
			'ActionType'                        => 'PAY',
			// Required.  Whether the request pays the receiver or whether the request is set up to create a payment request, but not fulfill the payment until the ExecutePayment is called.  Values are:  PAY, CREATE, PAY_PRIMARY
			'CancelURL'                         => $current_page . '&amp;error=paypal',
			// Required.  The URL to which the sender's browser is redirected if the sender cancels the approval for the payment after logging in to paypal.com.  1024 char max.
			'CurrencyCode'                      => $this->currency_code,
			// Required.  3 character currency code.
			'FeesPayer'                         => $fees_payer,
			// The payer of the fees.  Values are:  SENDER, PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
			'IPNNotificationURL'                => $ipn_url,
			// The URL to which you want all IPN messages for this payment to be sent.  1024 char max.
			'Memo'                              => '',
			// A note associated with the payment (text, not HTML).  1000 char max
			'Pin'                               => '',
			// The sener's personal id number, which was specified when the sender signed up for the preapproval
			'PreapprovalKey'                    => '',
			// The key associated with a preapproval for this payment.  The preapproval is required if this is a preapproved payment.
			'ReturnURL'                         => $current_page . '&amp;success=paypal',
			// Required.  The URL to which the sender's browser is redirected after approving a payment on paypal.com.  1024 char max.
			'ReverseAllParallelPaymentsOnError' => '',
			// Whether to reverse paralel payments if an error occurs with a payment.  Values are:  TRUE, FALSE
			'SenderEmail'                       => '',
			// Sender's email address.  127 char max.
			'TrackingID'                        => $tracking_id
			// Unique ID that you specify to track the payment.  127 char max.
		);

		$DisplayOptions = array(
			'BusinessName' => get_bloginfo( 'name' )
		);

		$request_data = apply_filters( 'ppcp_paypal_adaptive_request_data', array(
			'Action'           => 'Adaptive',
			'PayPalConfig'     => $PayPalConfig,
			'PayRequestFields' => $PayRequestFields,
			'Receivers'        => $receivers,
			'DisplayOptions'   => $DisplayOptions
		) );

		//Make remote request
		$payment_url  = [];
		$PayPalResult = $this->payment_request( $request_data );

		$custom_ipnurl = get_theme_mod( 'penci_paywriter_paypal_forward_ipn_response_urls' );

		$fields = [
			'cmd'           => '_xclick',
			'business'      => $PayPalResult['Receivers'][0]['Email'][0],
			'amount'        => $PayPalResult['Receivers'][0]['Amount'],
			'currency_code' => $PayPalResult['PayRequestFields']['CurrencyCode'],
			'return'        => $PayPalResult['PayRequestFields']['ReturnURL'],
			'cancel_return' => $PayPalResult['PayRequestFields']['CancelURL'],
			'notify_url'    => $custom_ipnurl ? $custom_ipnurl : $PayPalResult['PayRequestFields']['IPNNotificationURL'],
		];

		$query_string = http_build_query( $fields );

		if ( get_theme_mod( 'penci_paywriter_paypal_sandbox' ) ) {
			$payment_url['RedirectURL'] = 'https://sandbox.paypal.com/cgi-bin/webscr?' . $query_string;
		} else {
			$payment_url['RedirectURL'] = 'https://www.paypal.com/cgi-bin/webscr?' . $query_string;
		}

		do_action( 'ppcp_done_adaptive_payment', $PayPalResult );

		return $payment_url;
	}

	/**
	 * Check whether the sandbox is enabled.
	 *
	 * @access  public
	 *
	 * @param   $sandbox_settings int plugin sandbox settings
	 *
	 * @return    bool
	 * @since   1.3
	 */

	static function is_sandbox( $sandbox_settings ) {
		switch ( $sandbox_settings ) {
			case 1:
				return true;
				break;

			case 0:
				return false;
				break;

			default:
				return new WP_Error( 'ppcp_is_sandbox_error', __( 'Error: could not determine PayPal sandbox settings.', 'penci-pay-writer' ) );
				break;
		}
	}

	/**
	 * Gets PayPal IPN endpoint.
	 *
	 * @access  public
	 *
	 * @param   $sandbox_settings int plugin sandbox settings
	 *
	 * @return    string endpoint
	 * @since   1.3
	 */

	static function get_paypal_ipn_endpoint( $sandbox_settings ) {
		switch ( $sandbox_settings ) {
			case 1:
				return 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
				break;

			case 0:
				return 'https://ipnpb.paypal.com/cgi-bin/webscr';
				break;

			default:
				return new WP_Error( 'ppcp_get_ipn_endpoint_error', __( 'Error: could not determine PayPal IPN endpoint.', 'penci-pay-writer' ) );
				break;
		}
	}

	/**
	 * Gets fees payer.
	 *
	 * @access  public
	 *
	 * @param   $settings array plugin settings
	 *
	 * @return    string fees payer
	 * @since   1.3
	 */

	static function get_paypal_fees_payer( $settings ) {
		if ( pencipwt_get_setting( 'paypal_fees_sender' ) ) {
			return 'SENDER';
		} else if ( pencipwt_get_setting( 'paypal_fees_receivers' ) ) {
			return 'EACHRECEIVER';
		} else {
			return new WP_Error( 'ppcp_get_fees_payer_error', __( 'Error: could not determine PayPal fees payer settings.', 'penci-pay-writer' ) );
		}
	}

	/**
	 * Checks whether Paypal is correctly set up (i.e. all needed credentials are set).
	 *
	 * @access  public
	 *
	 * @param   $settings array plugin settings
	 *
	 * @return    mixed
	 * @since   1.3
	 */

	static function is_paypal_available( $settings ) {
		if ( ! pencipwt_get_setting( 'paypal_currency_code' ) ) {
			return new WP_Error( 'ppcp_paypal_currency_error', __( 'PayPal is not properly set. Please head to the Options page and check that everything is fine.', 'penci-pay-writer' ) );
		}

		return true;
	}

	/**
	 * Sets up a PayPal transaction.
	 *
	 * @access  public
	 *
	 * @param   $payment_data array a PenciPWT_Payment::prepare_payment result
	 * @param   $current_page string current page, on which return&cancel url will be built upon
	 *
	 * @return  array 'return_url' holding redirect url; 'payment_data' holding payment_data to be used for payment history update
	 * @since   1.3
	 */

	function execute_payment( &$payment_data, $current_page ) {
		global $pencipwt_global_settings;

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		$authors_ids      = array_keys( $payment_data['authors_for_payment_data'] );
		$tracking_id      = $payment_data['authors_for_payment_data'][ $authors_ids[0] ]['tracking_id']; //Extract tracking id from random author

		$receivers = array();
		foreach ( $payment_data['authors_for_payment_data'] as $author => &$stats ) {
			$author_paypal_email = '';
			if ( pencipwt_get_setting( 'paypal_use_users_email' ) ) {
				$user_data           = get_userdata( $author );
				$author_paypal_email = $user_data->user_email;
			}

			//See if user has a specific paypal email address
			$custom_author_paypal_email = get_user_meta( $author, 'paypal_account' );
			if ( $custom_author_paypal_email ) {
				$author_paypal_email = $custom_author_paypal_email;
			}

			//If user doesn't have paypal email or payment amount == 0, delete user & posts from array to prevent it from being marked as paid, then skip it
			if ( ! $author_paypal_email or $stats['ppc_payment']['total'] == 0 ) {
				unset( $payment_data['authors_for_payment_data'][ $author ] );

				foreach ( $payment_data['posts_for_payment_history_data'] as $post_id => &$stats ) {
					$post = get_post( $post_id );

					if ( $post->post_author == $author ) {
						unset( $payment_data['posts_for_payment_history_data'][ $post_id ] );
					}
				}

				continue;
			}

			$receivers[] = array(
				'Amount'         => sprintf( '%.2f', $stats['ppc_payment']['total'] ),
				// Required.  Amount to be paid to the receiver.
				'Email'          => $author_paypal_email,
				// Receiver's email address. 127 char max.
				'InvoiceID'      => '',
				// The invoice number for the payment.  127 char max.
				'PaymentType'    => 'SERVICE',
				// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
				'PaymentSubType' => '',
				// The transaction subtype for the payment.
				'Phone'          => array( 'CountryCode' => '', 'PhoneNumber' => '', 'Extension' => '' ),
				// Receiver's phone number.   Numbers only.
				'Primary'        => ''
				// Whether this receiver is the primary receiver (chained).  Values are boolean:  TRUE, FALSE
			);
		}

		$receivers_count = count( $receivers );

		/**
		 * These are just double checks, everything should have already been settled in the page before.
		 */

		//Must have at least a user to be paid
		if ( $receivers_count == 0 ) {
			return new WP_Error( 'ppcp_paypal_no_receivers', __( 'Error: no authors were selected for the payment. Notice that authors that don\'t have a PayPal email address are skipped.', 'penci-pay-writer' ) );
		}

		//No more than 6 users can be paid at once with Adaptive
		if ( count( $receivers ) > 6 ) {
			return new WP_Error( 'ppcp_paypal_too_many_receivers', __( 'Error: due to PayPal Adaptive Payments limitations, no more than 6 authors can be paid at a time.', 'penci-pay-writer' ) );
		}

		/**
		 * Call for payment
		 */

		$paypal_result = $this->execute_adaptive_payment( $current_page, $receivers, $tracking_id );
		if ( is_wp_error( $paypal_result ) ) {
			return $paypal_result;
		}

		$return_url = $paypal_result['RedirectURL'];

		return array( 'return_url' => $return_url, 'payment_data' => $payment_data );
	}

	/**
	 * Adds plugin's IPN rewrite rule (ppcp/paypal).
	 *
	 * @access  public
	 * @since   1.3
	 */

	static function ipn_add_rewrite_rule() {
		global $wp_rewrite;

		if ( ! is_object( $wp_rewrite ) ) {
			$wp_rewrite = new WP_Rewrite();
		}

		//Add IPN listener rule if not there already
		$rules = $wp_rewrite->wp_rewrite_rules();
		if ( ! isset( $rules['ppcp/paypal$'] ) ) {
			add_rewrite_rule( 'ppcp/paypal$', 'index.php?ppcp=paypal', 'top' );
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Deletes plugin's IPN rewrite rule (ppcp/paypal).
	 *
	 * @access  public
	 * @since   1.3.2
	 */

	static function ipn_delete_rewrite_rule() {
		global $wp_rewrite;

		if ( ! is_object( $wp_rewrite ) ) {
			$wp_rewrite = new WP_Rewrite();
		}

		//Delete IPN listener rule if there
		$rules = $wp_rewrite->wp_rewrite_rules();
		if ( isset( $rules['ppcp/paypal$'] ) ) {
			unset( $rules['ppcp/paypal$'] );
			$wp_rewrite->flush_rules();
		}

	}

	/**
	 * Adds plugin's IPN vars to allowed GET ones.
	 *
	 * @access  public
	 *
	 * @param   $public_query_vars array already allowed query vars
	 *
	 * @return    array query vars
	 * @since   1.3
	 */

	static function ipn_query_vars( $public_query_vars ) {
		$public_query_vars[] = 'penci-pay-writer';

		return $public_query_vars;
	}

	/**
	 * If plugin's listener is called, invoke the IPN listener.
	 *
	 * @access  public
	 *
	 * @param   $wp array query stuff
	 *
	 * @since   1.3
	 */

	static function ipn_listener( $wp ) {
		if ( isset( $wp->query_vars['penci-pay-writer'] ) and $wp->query_vars['penci-pay-writer'] == 'paypal' ) {
			self::ipn_process();
		}
	}

	/**
	 * Processes IPN requests.
	 *
	 * @access  public
	 * @since   1.3
	 */

	static function ipn_process() {
		global $wpdb, $pencipwt_global_settings;
		require_once( 'pencipwt_payment_class.php' );

		$general_settings = PenciPWT_General_Functions::get_settings( 'general' );
		$paypal_endpoint  = self::get_paypal_ipn_endpoint( pencipwt_get_setting( 'paypal_sandbox' ) );

		//IPN validation action
		$IPN_decode = self::decode_IPN_request();
		$request    = 'cmd=_notify-validate' . $IPN_decode['string'];

		//$req = preg_replace( '/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}', $req ); //Someone online reported this to fix some problems

		//Back to Paypal to validate
		$response = wp_remote_post( $paypal_endpoint, array(
			'body'    => $request,
			'timeout' => 20
		) );


		/**
		 * REQUEST OUTCOME
		 */

		//If verified, payment history records with the given tracking id are set as verified
		if ( ! is_wp_error( $response ) and $response['response']['code'] >= 200 and $response['response']['code'] < 300 and strcmp( $response['body'], "VERIFIED" ) == 0 ) {

			/**
			 * PAYMENT PROCESS
			 */

			//If payment has been completed, update payment history with 'verified'
			if ( isset( $IPN_decode['array']['transaction_type'] ) and strpos( $IPN_decode['array']['transaction_type'], 'PAY' ) !== false and $IPN_decode['array']['status'] == 'COMPLETED' ) {

				//Build array of completed-confirmed transactions by users with their amounts (in the form email => amount)
				$verified_users = array();

				//ADAPTIVE PAYMENTS
				if ( isset( $IPN_decode['array']['transaction_type'] ) and strpos( $IPN_decode['array']['transaction_type'], 'PAY' ) !== false ) {
					$n = 0;
					while ( isset( $IPN_decode['array'][ 'transaction[' . $n . '].receiver' ] ) ) {
						//If user transaction is completed, add to verified array
						if ( $IPN_decode['array'][ 'transaction[' . $n . '].status_for_sender_txn' ] == 'Completed' ) {
							$amount_and_currency                                                          = explode( ' ', $IPN_decode['array'][ 'transaction[' . $n . '].amount' ] ); //It's like USD 5.15, and only need the 5.15
							$verified_users[ $IPN_decode['array'][ 'transaction[' . $n . '].receiver' ] ] = (float) $amount_and_currency[1];
						}

						++ $n;
					}

					$tracking_id = $IPN_decode['array']['tracking_id']; //Store tracking id in same var both for adaptive&mass
				}

				//Get payment history, with posts & authors details, given tracking id
				$transaction_details = PenciPWT_Payment_History::get_transaction( $tracking_id );
				$paid_users          = $transaction_details['authors_info'];

				//Build payment data for email notification if needed
				if ( pencipwt_get_setting( 'payment_notification_paypal' ) ) {
					$payment_data = array(
						'author_for_payment_data' => array()
					);
				}

				foreach ( $paid_users as $author_id => $author_info ) {
					//Get author payment history
					$author_history = PenciPWT_Payment_History::get_author_payment_history( $author_id );

					if ( ! isset( $author_history[ $tracking_id ] ) ) {
						return;
					}

					//If somehow already confirmed, skip
					if ( $author_history[ $tracking_id ]['verified'] == 1 ) {
						continue;
					}

					//Check if user is in the list of confirmed transaction ones, skip it if not
					$author_paypal_email = get_user_option( $pencipwt_global_settings['paypal_email'], $author_id );
					if ( ! array_key_exists( $author_paypal_email, $verified_users ) ) {
						continue;
					}


					//Set as verified and update db record
					$author_meta                             = current( get_user_meta( $author_id, $pencipwt_global_settings['payment_history_field'] ) );
					$author_meta[ $tracking_id ]['verified'] = 1;
					$update                                  = PenciPWT_Payment_History::update_author_payment_history( $author_id, $author_meta );
					if ( is_wp_error( $update ) ) {
						exit;
					}
				}

				//Send out email notification to users if needed
				if ( pencipwt_get_setting( 'payment_notification_paypal' ) ) {
					$send_notifications = PenciPWT_Payment::send_notifications( $tracking_id );
					if ( is_wp_error( $send_notifications ) ) {
						exit;
					}
				}
			}

		}

		exit;
	}

	/**
	 * Decodes IPN POST data.
	 *
	 * @access  public
	 * @return  array string to be posted back to PayPal for validation + array usable by plugin
	 * @since   1.3
	 */

	static function decode_IPN_request() {
		$raw_post = file_get_contents( 'php://input' ); //Using $_POST results in a messed up request array
		$pairs    = explode( '&', $raw_post );

		$post_to_paypal  = '';
		$post_for_plugin = array();

		foreach ( $pairs as $pair ) {
			list( $key, $value ) = explode( '=', $pair, 2 );
			$key   = urldecode( $key );
			$value = urldecode( $value );

			//PayPal handling stops here. Building request string
			$post_to_paypal .= '&' . $key . '=' . urlencode( stripslashes( $value ) );

			//This builds up a request array usable by the plugin
			$post_for_plugin[ $key ] = $value;
		}

		return array( 'string' => $post_to_paypal, 'array' => $post_for_plugin );
	}
}