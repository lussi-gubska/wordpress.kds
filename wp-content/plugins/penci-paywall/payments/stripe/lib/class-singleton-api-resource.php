<?php

namespace PenciPaywall\Payments\Stripe\Lib;

/**
 * Class SingletonApiResource
 *
 * @package Stripe
 */
abstract class Singleton_Api_Resource extends Api_Resource {

	protected static function _singletonRetrieve( $options = null ) {
		$opts     = Util\RequestOptions::parse( $options );
		$instance = new static( null, $opts );
		$instance->refresh();
		return $instance;
	}

	/**
	 * @return string The endpoint associated with this singleton class.
	 */
	public static function classUrl() {
		 // Replace dots with slashes for namespaced resources, e.g. if the object's name is
		// "foo.bar", then its URL will be "/v1/foo/bar".
		$base = str_replace( '.', '/', static::OBJECT_NAME );
		return "/v1/${base}";
	}

	/**
	 * @return string The endpoint associated with this singleton API resource.
	 */
	public function instanceUrl() {
		 return static::classUrl();
	}
}
