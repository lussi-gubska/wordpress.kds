<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class CreditNote
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property string $customer
 * @property string|null $customer_balance_transaction
 * @property string $invoice
 * @property bool $livemode
 * @property string|null $memo
 * @property \Stripe\StripeObject $metadata
 * @property string $number
 * @property string $pdf
 * @property string|null $reason
 * @property string|null $refund
 * @property string $status
 * @property string $type
 * @property int|null $voided_at
 *
 * @package Stripe
 */
class Credit_Note extends Api_Resource {

	const OBJECT_NAME = 'credit_note';

	use Api_Operations\All;
	use Api_Operations\Create;
	use Api_Operations\Retrieve;
	use Api_Operations\Update;
	use Api_Operations\Nested_Resource;

	/**
	 * Possible string representations of the credit note reason.
	 *
	 * @link https://stripe.com/docs/api/credit_notes/object#credit_note_object-reason
	 */
	const REASON_DUPLICATE              = 'duplicate';
	const REASON_FRAUDULENT             = 'fraudulent';
	const REASON_ORDER_CHANGE           = 'order_change';
	const REASON_PRODUCT_UNSATISFACTORY = 'product_unsatisfactory';

	/**
	 * Possible string representations of the credit note status.
	 *
	 * @link https://stripe.com/docs/api/credit_notes/object#credit_note_object-status
	 */
	const STATUS_ISSUED = 'issued';
	const STATUS_VOID   = 'void';

	/**
	 * Possible string representations of the credit note type.
	 *
	 * @link https://stripe.com/docs/api/credit_notes/object#credit_note_object-status
	 */
	const TYPE_POST_PAYMENT = 'post_payment';
	const TYPE_PRE_PAYMENT  = 'pre_payment';

	const PATH_LINES = '/lines';

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Exception\Api_Error_Exception if the request fails
	 *
	 * @return CreditNote The previewed credit note.
	 */
	public static function preview( $params = null, $opts = null ) {
		$url                   = static::classUrl() . '/preview';
		list($response, $opts) = static::_staticRequest( 'get', $url, $params, $opts );
		$obj                   = Util\Util::convertToStripeObject( $response->json, $opts );
		$obj->setLastResponse( $response );
		return $obj;
	}

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Exception\Api_Error_Exception if the request fails
	 *
	 * @return CreditNote The voided credit note.
	 */
	public function voidCreditNote( $params = null, $opts = null ) {
		$url                   = $this->instanceUrl() . '/void';
		list($response, $opts) = $this->_request( 'post', $url, $params, $opts );
		$this->refreshFrom( $response, $opts );
		return $this;
	}

	/**
	 * @param string            $id The ID of the credit note on which to retrieve the lines.
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Exception\Api_Error_Exception if the request fails
	 *
	 * @return Collection The list of lines (CreditNoteLineItem).
	 */
	public static function allLines( $id, $params = null, $opts = null ) {
		return self::_allNestedResources( $id, static::PATH_LINES, $params, $opts );
	}
}
