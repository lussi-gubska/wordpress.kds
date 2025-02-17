<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class Transfer
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $amount_reversed
 * @property string|null $balance_transaction
 * @property int $created
 * @property string $currency
 * @property string|null $description
 * @property string|null $destination
 * @property string $destination_payment
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property \Stripe\Collection $reversals
 * @property bool $reversed
 * @property string|null $source_transaction
 * @property string|null $source_type
 * @property string|null $transfer_group
 *
 * @package Stripe
 */
class Transfer extends Api_Resource {

	const OBJECT_NAME = 'transfer';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Nested_Resource;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;

	const PATH_REVERSALS = '/reversals';

	/**
	 * Possible string representations of the source type of the transfer.
	 *
	 * @link https://stripe.com/docs/api/transfers/object#transfer_object-source_type
	 */
	const SOURCE_TYPE_ALIPAY_ACCOUNT = 'alipay_account';
	const SOURCE_TYPE_BANK_ACCOUNT   = 'bank_account';
	const SOURCE_TYPE_CARD           = 'card';
	const SOURCE_TYPE_FINANCING      = 'financing';

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return Transfer The canceled transfer.
	 */
	public function cancel( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/cancel';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}

	/**
	 * @param string            $id The ID of the transfer on which to create the transfer reversal.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return TransferReversal
	 */
	public static function createReversal( $id, $params = null, $opts = null ) {
		return self::_createNestedResource( $id, static::PATH_REVERSALS, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the transfer to which the transfer reversal belongs.
	 * @param string            $reversalId The ID of the transfer reversal to retrieve.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return TransferReversal
	 */
	public static function retrieveReversal( $id, $reversalId, $params = null, $opts = null ) {
		 return self::_retrieveNestedResource( $id, static::PATH_REVERSALS, $reversalId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the transfer to which the transfer reversal belongs.
	 * @param string            $reversalId The ID of the transfer reversal to update.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return TransferReversal
	 */
	public static function updateReversal( $id, $reversalId, $params = null, $opts = null ) {
		return self::_updateNestedResource( $id, static::PATH_REVERSALS, $reversalId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the transfer on which to retrieve the transfer reversals.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\ApiErrorException if the request fails
	 *
	 * @return Collection The list of transfer reversals.
	 */
	public static function allReversals( $id, $params = null, $opts = null ) {
		return self::_allNestedResources( $id, static::PATH_REVERSALS, $params, $opts );
	}
}
