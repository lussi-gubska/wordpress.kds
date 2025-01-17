<?php

namespace PenciPodcast;

use PenciPodcast\Settings;

class Hooks {

	/**
	 * @var Hooks;
	 */
	protected static $_instance;

	/**
	 * @return Hooks
	 */
	public static function instance(): Hooks {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function setup() {
		add_filter( 'wp_kses_allowed_html', [ $this, '_wp_kses_allowed_html' ], 10, 2 );
		add_filter( 'oembed_providers', [ $this, '_oembed_providers' ] );
	}

	public function _wp_kses_allowed_html( $tags, $context ) {
		if ( ! in_array( $context, pencipdc_importer_supported_post_types() ) ) {
			return $tags;
		}

		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'style'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'scrolling'       => true,
			'seamless'        => true,
		);

		return $tags;
	}

	public function _oembed_providers( $providers ) {
		$providers['#https?://(.+).podbean.com/e/.+#i'] = [ 'https://api.podbean.com/v1/oembed', true ];

		return $providers;
	}
}