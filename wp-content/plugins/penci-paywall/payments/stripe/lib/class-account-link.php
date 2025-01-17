<?php

namespace PenciPaywall\Payments\Stripe\Lib;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;

/**
 * Class AccountLink
 *
 * @property string $object
 * @property int $created
 * @property int $expires_at
 * @property string $url
 *
 * @package Stripe
 */
class Account_Link extends Api_Resource {

	const OBJECT_NAME = 'account_link';

	use Api_Operations\Create;
}
