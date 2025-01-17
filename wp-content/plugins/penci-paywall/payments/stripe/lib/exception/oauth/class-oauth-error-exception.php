<?php

namespace PenciPaywall\Payments\Stripe\Exception\OAuth;

/**
 * Implements properties and methods common to all (non-SPL) Stripe OAuth
 * exceptions.
 */
abstract class OAuth_Error_Exception extends PenciPaywall\Payments\Stripe\Exception\Api_Error_Exception {

	protected function constructErrorObject() {
		if ( is_null( $this->jsonBody ) ) {
			return null;
		}

		return PenciPaywall\Payments\Stripe\OAuth_Error_Object::constructFrom( $this->jsonBody );
	}
}
