<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PENCI_PODCAST_IMPORTER_VERSION', PENCI_PODCAST_VERSION );
define( "PENCI_PODCAST_IMPORTER_BASE_FILE_PATH", __FILE__ );
define( "PENCI_PODCAST_IMPORTER_BASE_PATH", dirname( PENCI_PODCAST_IMPORTER_BASE_FILE_PATH ) );
define( "PENCI_PODCAST_IMPORTER_PLUGIN_IDENTIFIER", ltrim( str_ireplace( dirname( PENCI_PODCAST_IMPORTER_BASE_PATH ), '', PENCI_PODCAST_IMPORTER_BASE_FILE_PATH ), '/' ) );

require_once PENCI_PODCAST_IMPORTER_BASE_PATH . "/autoload.php";
require_once PENCI_PODCAST_IMPORTER_BASE_PATH . "/definitions.php";
require_once PENCI_PODCAST_IMPORTER_BASE_PATH . "/functions.php";
require_once PENCI_PODCAST_IMPORTER_BASE_PATH . '/lib/action-scheduler/action-scheduler.php';

PenciPodcast\ActionScheduler::instance()->setup();

// Various Hooks & Additions.
PenciPodcast\Hooks::instance()->setup();

// Post Types
add_action( 'init', [ PenciPodcast\PostTypes::instance(), 'setup' ] );

// RestAPI
add_action( 'rest_api_init', [ PenciPodcast\RestAPI::instance(), 'setup' ] );

// General Functionality
add_action( 'plugins_loaded', [ PenciPodcast\Controller::instance(), 'setup' ] );

// Site Health
add_filter( 'site_status_tests', [ PenciPodcast\SiteHealth::instance(), 'tests' ] );

// Hook for importer cron job
use PenciPodcast\Helper\Scheduler as Helper_Scheduler;

add_action( PENCI_PODCAST_IMPORTER_ALIAS . '_cron', 'pencipdc_importer_test_scheduler_integrity' );
if ( ! wp_next_scheduled( PENCI_PODCAST_IMPORTER_ALIAS . '_cron' ) ) {
	wp_schedule_event( current_time( 'timestamp' ), 'daily', PENCI_PODCAST_IMPORTER_ALIAS . '_cron' );
}
function pencipdc_importer_test_scheduler_integrity() {
	if ( Helper_Scheduler::is_everything_scheduled() ) {
		return true;
	} else {
		return Helper_Scheduler::schedule_posts();
	}
}

if ( is_admin() ) {
	add_action( 'admin_menu', [ PenciPodcast\AdminMenu::instance(), 'setup' ] );
	add_action( 'admin_enqueue_scripts', [ PenciPodcast\AdminAssets::instance(), 'setup' ] );
}

register_deactivation_hook( __FILE__, function () {
	$next_schedule = wp_next_scheduled( 'pencipdc_importer_cron' );

	if ( false !== $next_schedule ) {
		wp_unschedule_event( $next_schedule, PENCI_PODCAST_IMPORTER_CRON_JOB_FREQUENCY, 'pencipdc_importer_cron' );
	}

	$next_schedule = wp_next_scheduled( 'pencipdc_import_cron_process_queue' );

	if ( false !== $next_schedule ) {
		wp_unschedule_event( $next_schedule, PENCI_PODCAST_IMPORTER_CRON_JOB_PROCESS_FREQUENCY, 'pencipdc_import_cron_process_queue' );
	}

	as_unschedule_action( PENCI_PODCAST_IMPORTER_ALIAS . '_scheduler_feeds_sync' );
	as_unschedule_all_actions( '', [], PENCI_PODCAST_IMPORTER_SCHEDULER_FEED_GROUP );
} );