<?php

namespace PenciPaywall\Payments\Stripe\Lib\Exception;

/**
 * RateLimitException is thrown in cases where an account is putting too much
 * load on Stripe's API servers (usually by performing too many requests).
 * Please back off on request rate.
 *
 * @package PenciPaywall\Payments\Stripe\Exception
 */
class Rate_Limit_Exception extends Invalid_Request_Exception {

}
