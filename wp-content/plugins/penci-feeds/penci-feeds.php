<?php
/*
Plugin Name: Penci RSS Aggregator
Plugin URI: https://pencidesign.net/
Description: The most powerful WordPress RSS aggregator, helping you curate content, autoblog, import and display unlimited RSS feeds within a few minutes.
Version: 1.2
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-feeds
*/

define( 'PENCIFEEDS_VERSION', '1.2' );
define( 'PENCIFEEDS_REQUIRED_WP_VERSION', '6.1' );
define( 'PENCIFEEDS_PLUGIN', __FILE__ );
define( 'PENCIFEEDS_PLUGIN_BASENAME', plugin_basename( PENCIFEEDS_PLUGIN ) );
define( 'PENCIFEEDS_PLUGIN_NAME', trim( dirname( PENCIFEEDS_PLUGIN_BASENAME ), '/' ) );
define( 'PENCIFEEDS_PLUGIN_DIR', untrailingslashit( dirname( PENCIFEEDS_PLUGIN ) ) );
define( 'PENCIFEEDS_PLUGIN_MODULES_DIR', PENCIFEEDS_PLUGIN_DIR . '/modules' );

require_once PENCIFEEDS_PLUGIN_DIR . '/bootstrap.php';
$rssapBootstrap = new \PenciFeeds\Bootstrap();
if ( is_admin() ) {
	$rssapBootstrap->loadAdmin();
}

/**
 * Get plugin URL
 *
 * @param string $path
 *
 * @return string
 */
function pencifeeds_plugin_url( $path = '' ) {
	$url = plugins_url( $path, PENCIFEEDS_PLUGIN );

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}

// pencifeeds_update_feeds();
/**
 * Admin panel CSS
 */
add_action( 'admin_enqueue_scripts', 'pencifeeds_admin_enqueue_scripts' );
function pencifeeds_admin_enqueue_scripts() {
	wp_enqueue_style(
		'penci-feeds-admin',
		pencifeeds_plugin_url( 'admin/css/styles.css' ),
		array(),
		PENCIFEEDS_VERSION,
		'all'
	);

	wp_enqueue_style(
		'penci-feeds-main',
		pencifeeds_plugin_url( 'admin/css/main.css' ),
		array(),
		PENCIFEEDS_VERSION,
		'all'
	);
}

/**
 * Activate plugin hook
 */
register_activation_hook( __FILE__, 'pencifeeds_activate' );
add_action( 'pencifeeds_update_event', 'pencifeeds_update_feeds' );

function pencifeeds_add_log( $message ) {
	$file = PENCIFEEDS_PLUGIN_DIR . '/logs.txt';

	if ( ! file_exists( $file ) ) {
		$fp = fopen( $file, 'w' );
		fclose( $fp );
	}

	if ( is_writable( $file ) ) {
		if ( filesize( $file ) > 3000000 ) {
			@unlink( $file );
		}

		$content = "\n" . '[' . date( 'Y-m-d H:i:s', time() ) . '] ' . $message;
		$fp      = fopen( $file, 'a' );
		fwrite( $fp, $content );
		fclose( $fp );
	}
}

function pencifeeds_update_feeds() {
	pencifeeds_add_log( 'Task started' );
	@set_time_limit( 600 );
	require_once PENCIFEEDS_PLUGIN_DIR . '/../../../wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	global $rssapBootstrap;
	$rssapBootstrap->updateFeeds();
	pencifeeds_add_log( 'Task completed' );
}

add_filter( 'cron_schedules', 'pencifeeds_new_interval' );

// add every minute interval to wp schedules
function pencifeeds_new_interval( $interval ) {

	$interval['every_10_minutes'] = array(
		'interval' => 600,
		'display'  => 'Every 10 minutes',
	);

	return $interval;
}

function pencifeeds_activate() {
	$version = get_option( '_pencifeeds_version' );

	if ( ( ! $version ) || ( version_compare( $version, PENCIFEEDS_VERSION ) < 0 ) ) {
		update_option( '_pencifeeds_version', PENCIFEEDS_VERSION );
	}

	wp_schedule_event( time(), 'every_10_minutes', 'pencifeeds_update_event' );
}

/**
 * Register deactivation hook
 */
register_deactivation_hook( __FILE__, 'pencifeeds_deactivate' );
function pencifeeds_deactivate() {
	wp_clear_scheduled_hook( 'pencifeeds_update_event' );
}

function pencifeeds_json_encode( $input ) {
	return preg_replace_callback(
		'/\\\\u([0-9a-zA-Z]{4})/',
		function ( $matches ) {
			return mb_convert_encoding( pack( 'H*', $matches[1] ), 'UTF-8', 'UTF-16' );
		},
		json_encode( $input )
	);
}


add_action( 'wp', 'pencifeeds_remove_canonical' );
add_action( 'wp_head', 'pencifeeds_on_head_load' );

function pencifeeds_remove_canonical() {
	if ( 'post' === get_post_type() && is_singular() ) {
		$feedId = get_post_meta( get_the_ID(), '_rss_feed_id', true );
		if ( $feedId ) {
			$addCanonical = get_post_meta( $feedId, '_add_canonical', true );
			if ( $addCanonical ) {
				remove_action( 'wp_head', 'rel_canonical' );
			}
		}
	}
}

/**
 * Add canonical URL if set
 */
function pencifeeds_on_head_load() {
	if ( is_single() ) {
		$feedId       = get_post_meta( get_the_ID(), '_rss_feed_id', true );
		$addCanonical = get_post_meta( $feedId, '_add_canonical', true );
		$originalUrl  = get_post_meta( get_the_ID(), '_rss_original_url', true );
		if ( $feedId && $addCanonical && $originalUrl ) {
			echo '<link href="' . esc_attr( $originalUrl ) . '" rel="canonical" />' . "\n";
		}
	}
}

function pencifeeds_compatibility_check() {
	$messages = '';

	if ( ! extension_loaded( 'mbstring' ) ) {
		$messages .= '<p>Penci Feeds plugin requires mbstring PHP exntesion</p>';
	}

	if ( ! extension_loaded( 'libxml' ) ) {
		$messages .= '<p>Penci Feeds plugin requires libxml PHP exntesion</p>';
	}

	if ( ! extension_loaded( 'dom' ) ) {
		$messages .= '<p>Penci Feeds plugin requires DOM PHP exntesion</p>';
	}

	if ( ! extension_loaded( 'simplexml' ) ) {
		$messages .= '<p>Penci Feeds plugin requires simpleXML PHP exntesion</p>';
	}

	if ( ! extension_loaded( 'curl' ) ) {
		$messages .= '<p>Penci Feeds plugin requires cURL PHP exntesion</p>';
	}

	if ( ! extension_loaded( 'iconv' ) ) {
		$messages .= '<p>Penci Feeds plugin requires iconv PHP exntesion</p>';
	}

	// allow_url_fopen=On
	if ( ! ini_get( 'allow_url_fopen' ) ) {
		$messages .= '<p>Penci Feeds plugin requires PHP.ini setting allow_url_fopen=On</p>';
	}

	// PHP version
	if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		$messages .= '<p>Penci Feeds plugin requires PHP v5.3.0 or greater</p>';
	}

	return $messages;
}

function pencifeeds_display_compatibility_check() {
	if ( $messages = pencifeeds_compatibility_check() ) :
		?>
		<div class="notice notice-warning is-dismissible">
			<?php _e( $messages, 'penci-feeds' ); ?>
		</div>
		<?php
	endif;
}

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-feeds', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);
