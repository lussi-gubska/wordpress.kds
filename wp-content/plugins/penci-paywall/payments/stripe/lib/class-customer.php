<?php

namespace PenciPaywall\Payments\Stripe\Lib;

use PenciPaywall\Payments\Stripe\Lib\Exception\Api_Error_Exception;

/**
 * Class Customer
 *
 * @property string $id
 * @property string $object
 * @property mixed|null $address
 * @property int $balance
 * @property int $created
 * @property string|null $currency
 * @property string|null $default_source
 * @property bool|null $delinquent
 * @property string|null $description
 * @property \Stripe\Discount|null $discount
 * @property string|null $email
 * @property string|null $invoice_prefix
 * @property mixed $invoice_settings
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property string|null $name
 * @property string|null $phone
 * @property string[]|null $preferred_locales
 * @property mixed|null $shipping
 * @property \Stripe\Collection $sources
 * @property \Stripe\Collection $subscriptions
 * @property string|null $tax_exempt
 * @property \Stripe\Collection $tax_ids
 *
 * @package Stripe
 */
class Customer extends Api_Resource {

	const OBJECT_NAME = 'customer';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Delete;
	use Api_Operations\Nested_Resource;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;

	/**
	 * Possible string representations of the customer's type of tax exemption.
	 *
	 * @link https://stripe.com/docs/api/customers/object#customer_object-tax_exempt
	 */
	const TAX_EXEMPT_NONE    = 'none';
	const TAX_EXEMPT_EXEMPT  = 'exempt';
	const TAX_EXEMPT_REVERSE = 'reverse';

	public static function getSavedNestedResources() {
		static $savedNestedResources = null;
		if ( $savedNestedResources === null ) {
			$savedNestedResources = new Util\Set(
				array(
					'source',
				)
			);
		}
		return $savedNestedResources;
	}

	const PATH_BALANCE_TRANSACTIONS = '/balance_transactions';
	const PATH_SOURCES              = '/sources';
	const PATH_TAX_IDS              = '/tax_ids';

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @return Customer The updated customer.
	 */
	public function deleteDiscount( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/discount';
		list($response, $opts) = $this->_request( 'delete', $url, $params, $opts );
		$this->refreshFrom( array( 'discount' => null ), $opts, true );
	}

	/**
	 * @param string            $id The ID of the customer on which to create the source.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Api_Resource
	 */
	public static function createSource( $id, $params = null, $opts = null ) {
		return self::_createNestedResource( $id, static::PATH_SOURCES, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the source belongs.
	 * @param string            $sourceId The ID of the source to retrieve.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Api_Resource
	 */
	public static function retrieveSource( $id, $sourceId, $params = null, $opts = null ) {
		 return self::_retrieveNestedResource( $id, static::PATH_SOURCES, $sourceId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the source belongs.
	 * @param string            $sourceId The ID of the source to update.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Api_Resource
	 */
	public static function updateSource( $id, $sourceId, $params = null, $opts = null ) {
		return self::_updateNestedResource( $id, static::PATH_SOURCES, $sourceId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the source belongs.
	 * @param string            $sourceId The ID of the source to delete.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Api_Resource
	 */
	public static function deleteSource( $id, $sourceId, $params = null, $opts = null ) {
		return self::_deleteNestedResource( $id, static::PATH_SOURCES, $sourceId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer on which to retrieve the sources.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Collection The list of sources.
	 */
	public static function allSources( $id, $params = null, $opts = null ) {
		return self::_allNestedResources( $id, static::PATH_SOURCES, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer on which to create the tax id.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return TaxId
	 */
	public static function createTaxId( $id, $params = null, $opts = null ) {
		return self::_createNestedResource( $id, static::PATH_TAX_IDS, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the tax id belongs.
	 * @param string            $taxIdId The ID of the tax id to retrieve.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return TaxId
	 */
	public static function retrieveTaxId( $id, $taxIdId, $params = null, $opts = null ) {
		return self::_retrieveNestedResource( $id, static::PATH_TAX_IDS, $taxIdId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the tax id belongs.
	 * @param string            $taxIdId The ID of the tax id to delete.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return TaxId
	 */
	public static function deleteTaxId( $id, $taxIdId, $params = null, $opts = null ) {
		 return self::_deleteNestedResource( $id, static::PATH_TAX_IDS, $taxIdId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer on which to retrieve the tax ids.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Collection The list of tax ids.
	 */
	public static function allTaxIds( $id, $params = null, $opts = null ) {
		 return self::_allNestedResources( $id, static::PATH_TAX_IDS, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer on which to create the balance transaction.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return BalanceTransaction
	 */
	public static function createBalanceTransaction( $id, $params = null, $opts = null ) {
		return self::_createNestedResource( $id, static::PATH_BALANCE_TRANSACTIONS, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the balance transaction belongs.
	 * @param string            $balanceTransactionId The ID of the balance transaction to retrieve.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return BalanceTransaction
	 */
	public static function retrieveBalanceTransaction( $id, $balanceTransactionId, $params = null, $opts = null ) {
		 return self::_retrieveNestedResource( $id, static::PATH_BALANCE_TRANSACTIONS, $balanceTransactionId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer to which the balance transaction belongs.
	 * @param string            $balanceTransactionId The ID of the balance transaction to update.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return BalanceTransaction
	 */
	public static function updateBalanceTransaction( $id, $balanceTransactionId, $params = null, $opts = null ) {
		return self::_updateNestedResource( $id, static::PATH_BALANCE_TRANSACTIONS, $balanceTransactionId, $params, $opts );
	}

	/**
	 * @param string            $id The ID of the customer on which to retrieve the balance transactions.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Collection The list of balance transactions.
	 */
	public static function allBalanceTransactions( $id, $params = null, $opts = null ) {
		return self::_allNestedResources( $id, static::PATH_BALANCE_TRANSACTIONS, $params, $opts );
	}
}
