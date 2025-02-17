<?php

namespace PenciPaywall\Payments\Stripe\Exception\OAuth;

/**
 * UnknownApiErrorException is thrown when the client library receives an
 * error from the OAuth API it doesn't know about. Receiving this error usually
 * means that your client library is outdated and should be upgraded.
 *
 * @package Stripe\Exception
 */
class Unknown_OAuth_Error_Exception extends OAuth_Error_Exception {

}
