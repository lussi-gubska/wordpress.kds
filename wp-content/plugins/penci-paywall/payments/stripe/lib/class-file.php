<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class File
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string|null $filename
 * @property \Stripe\Collection|null $links
 * @property string $purpose
 * @property int $size
 * @property string|null $title
 * @property string|null $type
 * @property string|null $url
 *
 * @package Stripe
 */
class File extends Api_Resource {

	// This resource can have two different object names. In latter API
	// versions, only `file` is used, but since stripe-php may be used with
	// any API version, we need to support deserializing the older
	// `file_upload` object into the same class.
	const OBJECT_NAME     = 'file';
	const OBJECT_NAME_ALT = 'file_upload';

	use Api_Operations\All;
	use Api_Operations\Create {
		create as protected _create;
	}
	use Api_Operations\Retrieve;

	public static function classUrl() {
		 return '/v1/files';
	}

	/**
	 * @param array|null        $params
	 * @param array|string|null $opts
	 *
	 * @throws Exception\Api_Error_Exception if the request fails
	 *
	 * @return \Stripe\File The created resource.
	 */
	public static function create( $params = null, $opts = null ) {
		 $opts = \Stripe\Util\RequestOptions::parse( $opts );
		if ( is_null( $opts->apiBase ) ) {
			$opts->apiBase = Stripe::$apiUploadBase;
		}
		// Manually flatten params, otherwise curl's multipart encoder will
		// choke on nested arrays.
		$flatParams = array_column( \Stripe\Util\Util::flattenParams( $params ), 1, 0 );
		return static::_create( $flatParams, $opts );
	}
}
