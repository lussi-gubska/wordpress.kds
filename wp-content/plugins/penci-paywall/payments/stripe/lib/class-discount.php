<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class Discount
 *
 * @property string $object
 * @property Coupon $coupon
 * @property string $customer
 * @property int $end
 * @property int $start
 * @property string $subscription
 *
 * @package Stripe
 */
class Discount extends Stripe_Object {

	const OBJECT_NAME = 'discount';
}
