<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class Subscription
 *
 * @property string $id
 * @property string $object
 * @property float|null $application_fee_percent
 * @property int $billing_cycle_anchor
 * @property mixed|null $billing_thresholds
 * @property int|null $cancel_at
 * @property bool $cancel_at_period_end
 * @property int|null $canceled_at
 * @property string|null $collection_method
 * @property int $created
 * @property int $current_period_end
 * @property int $current_period_start
 * @property string $customer
 * @property int|null $days_until_due
 * @property string|null $default_payment_method
 * @property string|null $default_source
 * @property array|null $default_tax_rates
 * @property Discount|null $discount
 * @property int|null $ended_at
 * @property Collection $items
 * @property string|null $latest_invoice
 * @property bool $livemode
 * @property Stripe_Object $metadata
 * @property int|null $next_pending_invoice_item_invoice
 * @property mixed|null $pending_invoice_item_interval
 * @property string|null $pending_setup_intent
 * @property Plan|null $plan
 * @property int|null $quantity
 * @property string|null $schedule
 * @property int $start_date
 * @property string $status
 * @property float|null $tax_percent
 * @property int|null $trial_end
 * @property int|null $trial_start
 *
 * @package Stripe
 */
class Subscription extends Api_Resource
{
    const OBJECT_NAME = 'subscription';

    use Api_Operations\All;
    use Api_Operations\Create;
    use Api_Operations\Delete {
        delete as protected _delete;
    }
    use Api_Operations\Retrieve;
    use Api_Operations\Update;

    /**
     * These constants are possible representations of the status field.
     *
     * @link https://stripe.com/docs/api#subscription_object-status
     */
    const STATUS_ACTIVE             = 'active';
    const STATUS_CANCELED           = 'canceled';
    const STATUS_PAST_DUE           = 'past_due';
    const STATUS_TRIALING           = 'trialing';
    const STATUS_UNPAID             = 'unpaid';
    const STATUS_INCOMPLETE         = 'incomplete';
    const STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';

    public static function getSavedNestedResources()
    {
        static $savedNestedResources = null;
        if ($savedNestedResources === null) {
            $savedNestedResources = new Util\Set([
                'source',
            ]);
        }
        return $savedNestedResources;
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @throws \Exception\Api_Error_Exception if the request fails
     *
     * @return Subscription The deleted subscription.
     */
    public function cancel($params = null, $opts = null)
    {
        return $this->_delete($params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @throws \Exception\Api_Error_Exception if the request fails
     *
     * @return Subscription The updated subscription.
     */
    public function deleteDiscount($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/discount';
        list($response, $opts) = $this->_request('delete', $url, $params, $opts);
        $this->refreshFrom(['discount' => null], $opts, true);
    }
}
