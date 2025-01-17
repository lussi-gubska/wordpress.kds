<?php

namespace PenciPaywall\Payments\Stripe\Lib\Terminal;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;
use PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Class ConnectionToken
 *
 * @property string $object
 * @property string $location
 * @property string $secret
 *
 * @package Stripe\Terminal
 */
class Connection_Token extends Api_Resource {

	const OBJECT_NAME = 'terminal.connection_token';

	use Api_Operations\Create;
}
