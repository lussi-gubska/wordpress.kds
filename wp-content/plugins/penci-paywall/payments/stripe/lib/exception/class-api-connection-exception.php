<?php

namespace PenciPaywall\Payments\Stripe\Lib\Exception;

/**
 * ApiConnection is thrown in the event that the SDK can't connect to Stripe's
 * servers. That can be for a variety of different reasons from a downed
 * network to a bad TLS certificate.
 *
 * @package Stripe\Exception
 */
class Api_Connection_Exception extends Api_Error_Exception {

}
