<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class AlipayAccount
 *
 * @package Stripe
 *
 * @deprecated Alipay accounts are deprecated. Please use the sources API instead.
 * @link https://stripe.com/docs/sources/alipay
 */
class Alipay_Account extends Api_Resource {

	const OBJECT_NAME = 'alipay_account';

	use Api_Operations\Delete;
	use Api_Operations\Update;

	/**
	 * @return string The instance URL for this resource. It needs to be special
	 *    cased because it doesn't fit into the standard resource pattern.
	 */
	public function instanceUrl() {
		if ( $this['customer'] ) {
			$base   = Customer::classUrl();
			$parent = $this['customer'];
			$path   = 'sources';
		} else {
			$msg = 'Alipay accounts cannot be accessed without a customer ID.';
			throw new Exception\UnexpectedValueException( $msg );
		}
		$parentExtn = urlencode( Util\Util::utf8( $parent ) );
		$extn       = urlencode( Util\Util::utf8( $this['id'] ) );
		return "$base/$parentExtn/$path/$extn";
	}

	/**
	 * @param array|string      $_id
	 * @param array|string|null $_opts
	 *
	 * @throws \Stripe\Exception\BadMethodCallException
	 *
	 * @deprecated Alipay accounts are deprecated. Please use the sources API instead.
	 * @link https://stripe.com/docs/sources/alipay
	 */
	public static function retrieve( $_id, $_opts = null ) {
		$msg = 'Alipay accounts cannot be retrieved without a customer ID. ' .
			   'Retrieve an Alipay account using `Customer::retrieveSource(' .
			   "'customer_id', 'alipay_account_id')`.";
		throw new Exception\BadMethodCallException( $msg );
	}

	/**
	 * @param string            $_id
	 * @param array|null        $_params
	 * @param array|string|null $_options
	 *
	 * @throws \Stripe\Exception\BadMethodCallException
	 *
	 * @deprecated Alipay accounts are deprecated. Please use the sources API instead.
	 * @link https://stripe.com/docs/sources/alipay
	 */
	public static function update( $_id, $_params = null, $_options = null ) {
		$msg = 'Alipay accounts cannot be updated without a customer ID. ' .
			   'Update an Alipay account using `Customer::updateSource(' .
			   "'customer_id', 'alipay_account_id', \$updateParams)`.";
		throw new Exception\BadMethodCallException( $msg );
	}
}
