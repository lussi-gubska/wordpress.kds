<?php
require_once plugin_dir_path( __FILE__ ) . '/google-api/vendor/autoload.php';

// Define the class
class Soledad_GA_Views {
	// Define initial properties
	public $profile = false; // Google Analytics profile ID
	public $the_title = 'Google Analytics Page view Sync'; // Page title and menu item text
	public $key_location = false; // Location of the JSON key file
	public $post_meta_key = 'penci_ga_views'; // Define meta key to save page views under
	public $cron_name = 'penci_ga_views_cron';
	public $cron_frequency = 'o'; // default cron frequency "o" (off)

	// on calling the class...
	function __construct() {
		// Retrieve the profile ID setting and update the corresponding class property
		$this->profile = get_theme_mod( 'penci_gviews_profile_id', false );
		// Set the location of the JSON key
		$this->key_location = get_theme_mod( 'penci_gviews_json' );
		// Update the class property with the cron frequency
		$this->cron_frequency = get_theme_mod( 'penci_ga_views_cron_freq', 'o' );
		$this->update_cron();

		add_filter( 'penci_get_postviews_key', function( $key ) {
			return $this->profile ? $this->$post_meta_key : $key;
		});
	}

	function cron_updater() {
		// Run the method to get the statistics
		$results = $this->update_stats( $this->profile );
		// Run the method to save the statistics
		$this->save_results( $results['rows'] );
	}

	// Convert cron setting to WordPress recurrence value
	function cron_recurrence() {
		if ( $this->cron_frequency == 'd' ) {
			return 'daily';
		}
		if ( $this->cron_frequency == 'w' ) {
			return 'weekly';
		}
	}

	function update_cron() {
		// Get current cron timestamp, false if no current cron
		$timestamp = wp_next_scheduled( $this->cron_name );
		// Add cron if doesn't exist
		if ( ! $timestamp ) {
			wp_schedule_event( time(), $this->cron_recurrence(), $this->cron_name );
		}
		// Amend cron if does exist
		if ( $timestamp > 0 ) { // because timestamp is positive integer
			// Unschedule
			wp_unschedule_event( $timestamp, $this->cron_name );
			// Reschedule
			wp_schedule_event( time(), $this->cron_recurrence(), $this->cron_name );
		}
		// Add cron action
		add_action( $this->cron_name, array( $this, 'cron_updater' ) );
		// Remove cron if setting is "o" = off
		if ( $this->cron_frequency == 'o' ) {
			// Unschedule
			wp_unschedule_event( $timestamp, $this->cron_name );
		}
	}

	function update_stats() {
		// New Google API PHP Client
		$client = new Google_Client();
		// Set the application name
		$client->setApplicationName( $this->the_title );
		// Provide the JSON API key
		$client->setAuthConfig( $this->key_location );
		// Set readonly analyics scope
		$client->setScopes( [ 'https://www.googleapis.com/auth/analytics.readonly' ] );
		// New instance of Google Service Analytics class
		$analytics = new Google_Service_Analytics( $client );

		// Return the statistics from the Google Analytics API
		return $analytics->data_ga->get(
			'ga:' . $this->profile, // Google Analytics Profile ID
			'2005-01-01', // Date range start
			'today', // Date range end
			'ga:pageviews', // Choose page views dimension
			array(
				'dimensions' => 'ga:pagePath' // Retrieve page path for matching purposes later on
			)
		);
	}

	// Method to save the fetched statistics to the WordPress database
	function save_results( $rows ) {
		// Array to merge duplicates
		$unique_stats = array();
		// Loop each row of results (pages/posts are one row each)
		foreach ( $rows as $row ) {
			// Get the path and number of page views from the $row variable
			list( $path, $views ) = $row;
			// Convert path to page/post ID
			$id = url_to_postid( $path );
			// Cast views as an integer
			$views = (int) $views;
			// If we have an ID at least one page view for the corresponding ID, then...
			if ( $id > 0 && $views > 0 ) {
				// Add the response from Google Analytics to a unique array
				if ( isset( $unique_stats[ $id ] ) ) {
					// Because there is an array key with the ID, add the views on
					$unique_stats[ $id ] = $views + $unique_stats[ $id ];
				} else {
					// Because there isn't an array key with the ID, define it and set to views
					$unique_stats[ $id ] = $views;
				}
			}
		}
		// Define a variable to use as a counter
		$updates = 0;
		// Loop through the $unique_stats array
		foreach ( $unique_stats as $id => $total_views ) {
			// Update the meta field in the WordPress database
			$success = update_post_meta( $id, $this->post_meta_key, $total_views );
			// If success equals true, a new page view value was added to the WordPress database
			if ( $success !== false ) {
				// Increment the counter
				$updates ++;
			}
		}

		// Return the counter value
		return $updates;
	}
}
new Soledad_GA_Views();