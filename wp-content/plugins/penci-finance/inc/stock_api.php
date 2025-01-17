<?php

use Scheb\YahooFinanceApi\ApiClientFactory;

class Penci_Finance_Stock {
	// Store the API client as a static property to avoid recreating it
	protected static $client;

	// Initialize the client only once
	public static function get_client() {
		if ( ! isset( self::$client ) ) {
			self::$client = ApiClientFactory::createApiClient();
		}

		return self::$client;
	}

	// Improved search function with caching
	public static function search( $s ) {
		return self::get_data( $s, 'search', function () use ( $s ) {
			return self::get_client()->search( $s );
		} );
	}

	// Optimized getQuotes with caching
	public static function getQuotes( $quotes ) {
		return self::get_data( $quotes, 'getQuotes', function () use ( $quotes ) {
			return self::get_client()->getQuotes( $quotes );
		} );
	}

	// Optimized exchange rate with caching
	public static function exchange_rate( $rates ) {
		return self::get_data( $rates, 'exchange_rate', function () use ( $rates ) {
			return self::get_client()->getExchangeRates( $rates );
		} );
	}

	// Optimized historical data with caching
	public static function historicalData( $character, $from, $to, $interval = '1d' ) {
		return self::get_data(
			[ $character, $interval, $from, $to ],
			'historicalData',
			function () use ( $character, $from, $to, $interval ) {
				return self::get_client()->getHistoricalQuoteData( $character, $interval, $from, $to );
			}
		);
	}

	// Improved caching with error handling and response validation
	public static function get_data( $q, $type, $callback ) {
		$id = self::get_id( $q, $type );

		// Check if cache exists
		if ( $cache = get_transient( $id ) ) {
			return $cache;
		}

		// Fetch data from API via callback
		try {
			$data = call_user_func($callback);
		} catch ( Exception $e ) {
			return $e->getMessage();
		}

		// Cache only if data is valid
		if ( self::is_valid_data( $data ) ) {
			self::set_cache( $id, $data, self::get_cache_expiry( $type ) );
		}

		return $data;
	}

	// Validate the API response before caching it
	protected static function is_valid_data( $data ) {
		// Implement your validation logic, e.g.:
		// return isset($data['status']) && $data['status'] === 'success';
		return ! empty( $data ) && is_array( $data );
	}

	// Centralized cache setting function
	protected static function set_cache( $id, $data, $expiry ) {
		set_transient( $id, $data, $expiry );
	}

	// Generate a cache ID that combines the query and type
	public static function get_id( $q, $type ) {
		$hash = hash( 'sha256', serialize( $q ) . $type );

		return $type . substr( $hash, 0, 10 );
	}

	// Configurable cache expiry based on data type (can be fine-tuned)
	public static function get_cache_expiry( $type ) {
		switch ( $type ) {
			case 'getQuotes':
				return 60 * 5; // 5 minutes for quotes
			case 'historicalData':
				return 60 * 60 * 24; // 1 day for historical data
			default:
				return 60 * 60; // 1 hour default
		}
	}
}