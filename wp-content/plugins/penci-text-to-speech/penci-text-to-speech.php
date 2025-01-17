<?php
/*
Plugin Name: Penci Text To Speech
Plugin URI: https://pencidesign.net/
Description: A plugin to help you converts text into human-like speech. The Plugin uses the latest technology of machine learning and artificial intelligence to play a high-quality human voice.
Version: 1.5
Author: PenciDesign
Text Domain: penci-text-to-speech
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/

namespace PenciDesign;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/inc/autoload.php';

/** Includes the autoloader for libraries installed with Composer. */
require __DIR__ . '/vendor/autoload.php';

require plugin_dir_path( __FILE__ ) . 'helper.php';

use PenciDesign\MetaBox;
use PenciDesign\RSS;
use PenciDesign\Shortcodes;
use PenciDesign\AdminStyles;
use PenciDesign\FrontStyles;
use PenciDesign\AdminScripts;
use PenciDesign\FrontScripts;
use PenciDesign\Elementor;
use PenciDesign\WPBakery;
use PenciDesign\SpeechCaster;

/**
 * SINGLETON: Core class used to instantiate and control a PenciTextToSpeech plugin.
 *
 * @since 1.0.0
 **/
final class PenciTextToSpeech {

	/**
	 * Plugin version.
	 *
	 * @string version
	 * @since 1.0.0
	 **/
	public static $version;

	/**
	 * Plugin name.
	 *
	 * @string version
	 * @since 3.0.4
	 **/
	public static $name;

	/**
	 * Use minified libraries if SCRIPT_DEBUG is turned off.
	 *
	 * @since 1.0.0
	 **/
	public static $suffix;

	/**
	 * URL (with trailing slash) to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $url;

	/**
	 * PATH to plugin folder.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $path;

	/**
	 * Plugin base name.
	 *
	 * @var string
	 * @since 1.0.0
	 **/
	public static $basename;

	/**
	 * Plugin admin menu base.
	 *
	 * @var string
	 * @since 3.0.0
	 **/
	public static $menu_base;

	/**
	 * Plugin slug base.
	 *
	 * @var string
	 * @since 3.0.5
	 **/
	public static $slug;

	/**
	 * Full path to main plugin file.
	 *
	 * @var string
	 * @since 3.0.5
	 **/
	public static $plugin_file;

	/**
	 * The one true PenciTextToSpeech.
	 *
	 * @var PenciTextToSpeech
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Initialize main variables. */
		$this->initialization();
	}

	/**
	 * Setup the plugin.
	 *
	 * @return void
	 * *@since 3.0.0
	 * @access public
	 */
	public function setup() {

		add_filter( 'https_ssl_verify', '__return_false' );

		/** Define hooks that runs on both the front-end as well as the dashboard. */
		$this->both_hooks();

		/** Define public hooks. */
		$this->public_hooks();

		/** Define admin hooks. */
		$this->admin_hooks();
	}

	/**
	 * Initialize main variables.
	 *
	 * @return void
	 * *@since 1.0.0
	 * @access public
	 */
	public function initialization() {

		/** Get Plugin version. */
		self::$version = 1.4;

		/** Plugin slug. */
		self::$slug = 'penci-text-to-speech';

		/** Get Plugin name. */
		self::$name = 'Penci Text To Speech';

		/** Gets the plugin URL (with trailing slash). */
		self::$url = plugin_dir_url( __FILE__ ) . 'assets/';

		/** Gets the plugin PATH. */
		self::$path = plugin_dir_path( __FILE__ );

		/** Use minified libraries if SCRIPT_DEBUG is turned off. */
		self::$suffix = '';

		/** Set plugin basename. */
		self::$basename = plugin_basename( __FILE__ );

		/** Plugin settings page base. */
		self::$menu_base = 'toplevel_page_penci_texttospeech_settings';

		/** Full path to main plugin file. */
		self::$plugin_file = __FILE__;

		add_action(
			'init',
			function () {
				if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
					require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
					require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
					\SoledadFW\PenciTextToSpeechCustomizer::getInstance();
				}
			}
		);

		add_action(
			'penci_get_options_data',
			function ( $options ) {

				$options['penci_texttospeech_panel'] = array(
					'priority'                           => 30,
					'panel'                              => array(
						'icon'        => 'fas fa-microphone-alt',
						'title'       => esc_html__( 'Text To Speech', 'soledad' ),
						'description' => __( 'Please check <a target="_blank" href="https://soledad.pencidesign.net/soledad-document/#text-to-speech">this video tutorial</a> to know how to setup this feature.', 'soledad' ),
					),
					'path'                               => plugin_dir_path( __FILE__ ) . '/customizer/',
					'penci_texttospeech_general_section' => array( 'title' => esc_html__( 'General', 'soledad' ) ),
					'penci_texttospeech_voice_section'   => array( 'title' => esc_html__( 'Player Settings', 'soledad' ) ),
				);
				return $options;
			}
		);

		PenciTextToSpeechUtilities::get_instance();
	}

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
	 * @return void
	 * *@since 3.0.0
	 * @access private
	 */
	private function both_hooks() {

		/** Adds all the necessary shortcodes. */
		Shortcodes::get_instance();

		/** Register Elementor Widgets. */
		$this->register_elementor_widgets();

		/** Register WPBakery Widgets. */
		$this->register_wpbakery_elements();
	}

	/**
	 * Register all the hooks related to the public-facing functionality.
	 *
	 * @return void
	 * *@since 3.0.0
	 * @access private
	 */
	private function public_hooks() {

		/** Work only on frontend area. */
		if ( is_admin() ) {
			return;
		}

		/** Load CSS for Frontend Area. */
		FrontStyles::get_instance();

		/** Load JavaScripts for Frontend Area. */
		FrontScripts::get_instance();

		/** Add player code to page. */
		SpeechCaster::get_instance()->add_player();

		add_filter( 'template_include', array( SpeechCaster::class, 'penci_tts_page_template' ), PHP_INT_MAX );

		/** Add Schema markup */
		add_action( 'wp_head', array( SpeechCaster::get_instance(), 'structured_data' ) );

		/** Add RSS feeds */
		RSS::get_instance();
	}

	/**
	 * Register all the hooks related to the admin area functionality.
	 *
	 * @return void
	 * *@since 3.0.0
	 * @access private
	 */
	private function admin_hooks() {

		/** Work only in admin area. */
		if ( ! is_admin() ) {
			return;
		}

		/** Create folder for audio files. */
		wp_mkdir_p( trailingslashit( wp_upload_dir()['basedir'] ) . 'penci-text-to-speech' );

		/** Add Ajax handlers and before_delete_post action. */
		SpeechCaster::get_instance()->add_actions();

		/** Add Meta Box for selected post types. */
		MetaBox::get_instance();

		/** Add admin styles. */
		AdminStyles::get_instance();

		/** Add admin javascript. */
		AdminScripts::get_instance();
	}

	public function register_elementor_widgets() {

		/** Initialize Elementor widgets. */
		Elementor::get_instance();
	}

	/**
	 * Registers a WPBakery element.
	 *
	 * @return void
	 * @since 3.0.0
	 * @access public
	 **/
	public function register_wpbakery_elements() {

		/** Initialize WPBakery Element. */
		WPBakery::get_instance();
	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * *@since 2.0.2
	 * @access public
	 */
	public function get_version() {
		return self::$version;
	}

	/**
	 * Main PenciTextToSpeech Instance.
	 *
	 * Insures that only one instance of PenciTextToSpeech exists in memory at any one time.
	 *
	 * @static
	 * @return PenciTextToSpeech
	 * *@since 1.0.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}
} // End Class PenciTextToSpeech.

/** Run PenciTextToSpeech class once after activated plugins have loaded. */
add_action( 'plugins_loaded', array( PenciTextToSpeech::get_instance(), 'setup' ) );
