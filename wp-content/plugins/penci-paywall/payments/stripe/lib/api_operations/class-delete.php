<?php

namespace PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Trait for deletable resources. Adds a `delete()` method to the class.
 *
 * This trait should only be applied to classes that derive from StripeObject.
 */
trait Delete {

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws \Stripe\Exception\Api_Error_Exception if the request fails
	 *
	 * @return static The deleted resource.
	 */
	public function delete( $params = null, $opts = null ) {
		self::_validateParams( $params );

		$url                   = $this->instanceUrl();
		list($response, $opts) = $this->_request( 'delete', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}
}
