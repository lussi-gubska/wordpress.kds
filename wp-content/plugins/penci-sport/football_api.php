<?php

class Penci_Football_API {

	// Main function to get data with caching
	public static function get( $settings ) {
		return self::get_data(
			[ $settings ],
			'sport_api'
		);
	}

	// Fetch data from API
	public static function get_data_from_api( $parameters ) {

		$league = isset( $parameters['league'] ) ? $parameters['league'] : 'PL';
        if ( $parameters['type'] == 'standing' ) {
            $url = "https://api.football-data.org/v4/competitions/{$league}/standings";
        } else {
			$url = "https://api.football-data.org/v4/competitions/{$league}/matches";
		}

		if ( isset( $parameters['ids'] ) ) {
			$parameters['ids'] = preg_replace( '/\s+/', '', $parameters['ids'] );
		}

		// Build the query string
		$query_string = isset( $parameters['query'] ) && $parameters['query'] ? http_build_query( $parameters['query'] ) : '';

		// Define headers
		$headers = [
			'Accepts' => 'application/json',
			'X-Auth-Token' => $parameters['token'],
		];

		// Make API request
		$response = wp_remote_get( "{$url}?{$query_string}", [ 'headers' => $headers ] );

		// Check for errors in the response
		if ( is_wp_error( $response ) ) {
			return [ 'error' => true, 'message' => $response->get_error_message() ];
		}

		// Parse and return the response body
		$body = wp_remote_retrieve_body( $response );

		return json_decode( $body, true );
	}

	// Retrieve data with caching
	public static function get_data( $q, $type ) {
		$cache_id = self::get_cache_id( $q, $type );

		// Check if cached data exists
		if ( $cached_data = get_transient( $cache_id ) ) {
			return $cached_data;
		}

		// Fetch data from API if not in cache
		try {
			$data = self::get_data_from_api( $q[0] );
		} catch ( Exception $e ) {
			return ['error'=>true,'message'=>'Not found'];
		}

		// Cache valid data only
		if ( self::is_valid_data( $data ) ) {
			self::set_cache( $cache_id, $data, 60 * 60 ); // Cache for 1 hour
		}

		return $data;
	}

	// Validate data before caching
	protected static function is_valid_data( $data ) {
		return ! empty( $data ) && is_array( $data ) && ! isset( $data['error'] );
	}

	// Centralized cache setter
	protected static function set_cache( $cache_id, $data, $expiry ) {
		set_transient( $cache_id, $data, $expiry );
	}

	// Generate cache ID based on query and type
	public static function get_cache_id( $q, $type ) {
		$hash = hash( 'sha256', serialize( $q ) . $type );

		return $type . substr( $hash, 0, 10 );
	}
}