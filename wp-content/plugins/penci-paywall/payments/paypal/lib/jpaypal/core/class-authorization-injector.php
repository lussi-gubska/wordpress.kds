<?php

namespace PenciPaywall\Payments\Paypal\Lib\Jpaypal\Core;

use PenciPaywall\Payments\Paypal\Lib\Jpaypal\Penci_Paypal_Api_Credentials;

class Authorization_Injector extends Penci_Paypal_Api_Credentials {
	private $client;
	private $environment;
	private $refresh_token;

	public function __construct( $client, $environment, $refresh_token ) {
		$this->client        = $client;
		$this->environment   = $environment;
		$this->refresh_token = $refresh_token;
	}

	public function inject( $request ) {
		parent::auth_inject( $request, $this->environment );
	}
}
