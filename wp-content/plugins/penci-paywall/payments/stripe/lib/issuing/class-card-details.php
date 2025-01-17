<?php

namespace PenciPaywall\Payments\Stripe\Lib\Issuing;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;

/**
 * Class CardDetails
 *
 * @property string $id
 * @property string $object
 * @property Card $card
 * @property string $cvc
 * @property int $exp_month
 * @property int $exp_year
 * @property string $number
 *
 * @package PenciPaywall\Payments\Stripe\Issuing
 */
class Card_Details extends Api_Resource {

	const OBJECT_NAME = 'issuing.card_details';
}
