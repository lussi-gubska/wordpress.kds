<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class Payout
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $arrival_date
 * @property bool $automatic
 * @property string|null $balance_transaction
 * @property int $created
 * @property string $currency
 * @property string|null $description
 * @property string|null $destination
 * @property string|null $failure_balance_transaction
 * @property string|null $failure_code
 * @property string|null $failure_message
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property string $method
 * @property string $source_type
 * @property string|null $statement_descriptor
 * @property string $status
 * @property string $type
 *
 * @package Stripe
 */
class Payout extends Api_Resource {

	const OBJECT_NAME = 'payout';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;

	/**
	 * Types of payout failure codes.
	 *
	 * @link https://stripe.com/docs/api#payout_failures
	 */
	const FAILURE_ACCOUNT_CLOSED                = 'account_closed';
	const FAILURE_ACCOUNT_FROZEN                = 'account_frozen';
	const FAILURE_BANK_ACCOUNT_RESTRICTED       = 'bank_account_restricted';
	const FAILURE_BANK_OWNERSHIP_CHANGED        = 'bank_ownership_changed';
	const FAILURE_COULD_NOT_PROCESS             = 'could_not_process';
	const FAILURE_DEBIT_NOT_AUTHORIZED          = 'debit_not_authorized';
	const FAILURE_DECLINED                      = 'declined';
	const FAILURE_INCORRECT_ACCOUNT_HOLDER_NAME = 'incorrect_account_holder_name';
	const FAILURE_INSUFFICIENT_FUNDS            = 'insufficient_funds';
	const FAILURE_INVALID_ACCOUNT_NUMBER        = 'invalid_account_number';
	const FAILURE_INVALID_CURRENCY              = 'invalid_currency';
	const FAILURE_NO_ACCOUNT                    = 'no_account';
	const FAILURE_UNSUPPORTED_CARD              = 'unsupported_card';

	/**
	 * Possible string representations of the payout methods.
	 *
	 * @link https://stripe.com/docs/api/payouts/object#payout_object-method
	 */
	const METHOD_STANDARD = 'standard';
	const METHOD_INSTANT  = 'instant';

	/**
	 * Possible string representations of the status of the payout.
	 *
	 * @link https://stripe.com/docs/api/payouts/object#payout_object-status
	 */
	const STATUS_CANCELED   = 'canceled';
	const STATUS_IN_TRANSIT = 'in_transit';
	const STATUS_FAILED     = 'failed';
	const STATUS_PAID       = 'paid';
	const STATUS_PENDING    = 'pending';

	/**
	 * Possible string representations of the type of payout.
	 *
	 * @link https://stripe.com/docs/api/payouts/object#payout_object-type
	 */
	const TYPE_BANK_ACCOUNT = 'bank_account';
	const TYPE_CARD         = 'card';

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return Payout The canceled payout.
	 */
	public function cancel( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/cancel';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}
}
