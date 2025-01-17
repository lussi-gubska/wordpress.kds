<?php

use PenciPaywall\Payments\Stripe\Stripe_Api;
use PenciPaywall\Payments\Stripe\Stripe_Api_Credentials;
use PenciPaywall\Payments\Stripe\Stripe_Api_Request;

/**
 * PenciPW_Stripe_Sepa class.
 *
 * @extends PenciPW_Stripe
 */
class PenciPW_Stripe_Sepa extends PenciPW_Stripe {
	
	/**
	 * @var array
	 */
	protected $api_credentials = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 					   = 'stripepaywall_sepa';
		$this->has_fields         					   = true;
		$this->GATEWAYNAME        					   = 'Stripe Subscribe (SEPA Direct Debit)';
		$this->method_title       					   = 'Stripe Subscribe (SEPA Direct Debit)';
		$this->method_description 					   = esc_html__( 'Stripe Recurring Subscription settings for Penci Paywall', 'penci-paywall' );
		$this->method_description 					  .= sprintf( __( 'All other general Stripe Subscription settings can be adjusted <a href="%s">here</a>.', 'penci-paywall' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=stripepaywall' ) );
		$this->order_button_text  					   = esc_html__( 'Place Order', 'penci-paywall' );

		$this->init_form_fields();
		$this->init_settings();

		// The shop owner credentials
		$stripe_settings							   = get_option( 'woocommerce_stripepaywall_settings' );
		$this->api_credentials['webhook'] 		       = $stripe_settings[ 'webhookkey' ];

		$this->title 			  					   = esc_html__( 'Stripe Subscription (SEPA Direct Debit)', 'penci-paywall' );
		$this->testmode 		  					   = $stripe_settings[ 'testmode' ];

		if ( $this->testmode === 'yes' ) {
			$this->description 						   = sprintf( __( '(TEST MODE) Pay with your SEPA Direct Debit via Stripe. In test mode, you can use the card number DE89370400440532013000 or check the <a href="%s" target="_blank">Testing Stripe documentation</a> for more card numbers.', 'penci-paywall' ), 'https://stripe.com/docs/testing' );
			$this->api_credentials['publishable']      = $stripe_settings[ 'publishabletestkey' ];
			$this->api_credentials['secret']  		   = $stripe_settings[ 'secrettestkey' ];
		} else {
			$this->description = esc_html__( 'Pay with your SEPA Direct Debit via Stripe.', 'penci-paywall' );
			$this->api_credentials['publishable']      = $stripe_settings[ 'publishablelivekey' ];
			$this->api_credentials['secret']  		   = $stripe_settings[ 'secretlivekey' ];
		}

		// Hooks.
		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			[
				$this,
				'process_admin_options',
			]
		);

		if ( $this->is_valid_for_use() ) {
			$this->webhook_handler();
		} else {
			$this->enabled = 'no';
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters(
			'woocommerce_stripepaywall_sepa_settings',
			[
				'enabled'         => [
					'title'       => __( 'Enable/Disable', 'penci-paywall' ),
					'label'       => __( 'Enable Stripe Subscribe (SEPA Direct Debit)', 'penci-paywall' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no',
				],
			]
		);
	}

	/**
	 * Get Card Type Icons
	 */
	public function get_icon() {
		$icons = apply_filters(
			'pencipw_stripe_payment_icons',
			[
				'sepa'       => '<img src="' . PENCI_PAYWALL_URL . 'assets/img/sepa.svg" alt="SEPA" />',
			]
		);

		$icons_str = '';
		$icons_str .= isset( $icons['sepa'] ) ? $icons['sepa'] : '';

		return apply_filters( 'woocommerce_gateway_icon', $icons_str, $this->id );
	}

	/**
	 * Check if this gateway is valid for use.
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {
		$supported_currencies = [ 'EUR' ];

		// Not supported currency
		if ( ! in_array( get_woocommerce_currency(), $supported_currencies ) ) {
			return false;
		}

		// If no SSL in live mode.
		if ( ! $this->testmode && ! is_ssl() ) {
			return false;
		}

		// Keys are not set
		if ( empty( $this->api_credentials['secret'] ) || empty( $this->api_credentials['publishable'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Payment Fields.
	 * 
	 */
	public function payment_fields() {
		ob_start();
		
		print($this->description);
		
		?>

		<div class="form-row">
			<!--
			Using a label with a for attribute that matches the ID of the
			Element container enables the Element to automatically gain focus
			when the customer clicks on the label.
			-->
			<label for="iban-element">
			<?php
			print( esc_html__( 'IBAN.', 'penci-paywall' ) );
			?>
			</label>
			<div id="iban-element">
			<!-- A Stripe Element will be inserted here. -->
			</div>
		</div>

		<!-- Used to display form errors. -->
		<div id="iban-errors" role="alert"></div>

		<!-- Display mandate acceptance text. -->
		<div id="mandate-acceptance">
			<?php
			print( sprintf( __( 'By providing your IBAN, you are authorizing %1$s and Stripe,
			our payment service provider, to send instructions to your bank to debit
			your account in accordance with those instructions. Subsequent payments are
			entitled to a refund from your bank under the terms and conditions of your
			agreement with your bank. A refund must be claimed within eight weeks
			starting from the date on which your account was debited.', 'penci-paywall' ), get_bloginfo( 'name' ) ) );
			?>
		</div>
		
		<?php
		ob_end_flush();
	}

}
