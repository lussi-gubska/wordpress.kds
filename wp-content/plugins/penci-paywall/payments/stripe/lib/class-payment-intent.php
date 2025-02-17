<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class PaymentIntent
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $amount_capturable
 * @property int $amount_received
 * @property string|null $application
 * @property int|null $application_fee_amount
 * @property int|null $canceled_at
 * @property string|null $cancellation_reason
 * @property string $capture_method
 * @property \Stripe\Collection $charges
 * @property string|null $client_secret
 * @property string $confirmation_method
 * @property int $created
 * @property string $currency
 * @property string|null $customer
 * @property string|null $description
 * @property string|null $invoice
 * @property mixed|null $last_payment_error
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property mixed|null $next_action
 * @property string|null $on_behalf_of
 * @property string|null $payment_method
 * @property mixed|null $payment_method_options
 * @property string[] $payment_method_types
 * @property string|null $receipt_email
 * @property string|null $review
 * @property string|null $setup_future_usage
 * @property mixed|null $shipping
 * @property string|null $source
 * @property string|null $statement_descriptor
 * @property string|null $statement_descriptor_suffix
 * @property string $status
 * @property mixed|null $transfer_data
 * @property string|null $transfer_group
 *
 * @package Stripe
 */
class Payment_Intent extends Api_Resource {

	const OBJECT_NAME = 'payment_intent';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;

	/**
	 * These constants are possible representations of the status field.
	 *
	 * @link https://stripe.com/docs/api/payment_intents/object#payment_intent_object-status
	 */
	const STATUS_CANCELED                = 'canceled';
	const STATUS_PROCESSING              = 'processing';
	const STATUS_REQUIRES_ACTION         = 'requires_action';
	const STATUS_REQUIRES_CAPTURE        = 'requires_capture';
	const STATUS_REQUIRES_CONFIRMATION   = 'requires_confirmation';
	const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
	const STATUS_SUCCEEDED               = 'succeeded';

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Exception\Api_Error_Exception if the request fails
	 *
	 * @return PaymentIntent The canceled payment intent.
	 */
	public function cancel( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/cancel';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Exception\Api_Error_Exception if the request fails
	 *
	 * @return PaymentIntent The captured payment intent.
	 */
	public function capture( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/capture';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Exception\Api_Error_Exception if the request fails
	 *
	 * @return PaymentIntent The confirmed payment intent.
	 */
	public function confirm( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/confirm';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}
}
