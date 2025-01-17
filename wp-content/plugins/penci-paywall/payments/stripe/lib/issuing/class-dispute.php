<?php

namespace PenciPaywall\Payments\Stripe\Lib\Issuing;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;
use PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Class Dispute
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property string $disputed_transaction
 * @property mixed $evidence
 * @property bool $livemode
 * @property \Stripe\StripeObject $metadata
 * @property string $reason
 * @property string $status
 *
 * @package Stripe\Issuing
 */
class Dispute extends Api_Resource {

	const OBJECT_NAME = 'issuing.dispute';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;
}
