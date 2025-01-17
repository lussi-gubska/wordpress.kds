<?php

namespace PenciPaywall\Payments\Paypal\Lib\Jpaypal\Core;

class Gzip_Injector implements Injector {
	public function inject( $request ) {
		$request->headers['Accept-Encoding'] = 'gzip';
	}
}
