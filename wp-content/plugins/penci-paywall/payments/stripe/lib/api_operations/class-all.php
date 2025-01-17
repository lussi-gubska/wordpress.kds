<?php

namespace PenciPaywall\Payments\Stripe\Lib\Api_Operations;

use PenciPaywall\Payments\Stripe\Lib\Util\Util;
use PenciPaywall\Payments\Stripe\Lib\Collection;
use PenciPaywall\Payments\Stripe\Lib\Exception\Api_Error_Exception;
use PenciPaywall\Payments\Stripe\Lib\Exception\Unexpected_Value_Exception;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from StripeObject.
 */
trait All {

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Api_Error_Exception if the request fails
	 *
	 * @return Collection of ApiResources
	 */
	public static function all( $params = null, $opts = null ) {
		self::_validateParams( $params );
		$url = static::classUrl();

		list($response, $opts) = static::_staticRequest( 'get', $url, $params, $opts );
		$obj                   = Util::convertToStripeObject( $response->json, $opts );
		if ( ! ( $obj instanceof Collection ) ) {
			throw new Unexpected_Value_Exception(
				'Expected type ' . Collection::class . ', got "' . get_class( $obj ) . '" instead.'
			);
		}
		$obj->setLastResponse( $response );
		$obj->setFilters( $params );
		return $obj;
	}
}
