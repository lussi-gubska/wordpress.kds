<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( ! class_exists( 'Penci_Amp_Compatibility' ) ):
	class Penci_Amp_Compatibility{

		public static $plugins = array();

		public static function init(){

			add_action( 'wp', array( __CLASS__, 'rocket_disable_options' ) );

			add_action( 'init', array( __CLASS__, 'pre_wpml_hooks' ) );

			$is_penci_amp = is_penci_amp();
			if ( ! $is_penci_amp ) {
				return;
			}

			self::$plugins = array_flip( wp_get_active_and_valid_plugins() );

			// WordPress Fastest Cache
			if ( isset( self::$plugins[ WP_PLUGIN_DIR . '/wp-fastest-cache/wpFastestCache.php' ] ) && ! isset( $GLOBALS["wp_fastest_cache_options"] ) ) {
				self::fastest_cache();
			}

			// Convert Plug plugin
			if ( class_exists( 'Convert_Plug' ) ) {
				add_filter( 'after_setup_theme', array( __CLASS__, 'convert_plug' ) );
			}

			// Above The Fold Plugin
			if ( class_exists( 'Abovethefold' ) ) {
				if ( ! defined( 'DONOTABTF' ) ) {
					define( 'DONOTABTF', TRUE );
				}
				$GLOBALS['Abovethefold']->disable = TRUE;

				self::remove_class_action( 'init', 'Abovethefold_Optimization', 'html_output_hook', 99999 );
				self::remove_class_action( 'wp_head', 'Abovethefold_Optimization', 'header', 1 );
				self::remove_class_action( 'wp_print_footer_scripts', 'Abovethefold_Optimization', 'footer', 99999 );
			}

			if ( class_exists( 'WP_Optimize' ) ) {
				self::remove_class_action( 'plugins_loaded', 'WP_Optimize', 'plugins_loaded', 1 );
			}

			self::$plugins = NULL;

			// WPML
			add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );

			add_action( 'template_redirect', array( __CLASS__, 'pre_wpml_hooks' ) );

			// Polylang
			add_filter( 'pll_check_canonical_url', '__return_false' );

			// wp-rocket
			add_filter( 'rocket_cache_search','__return_false' );
		}

		public static function pre_wpml_hooks(){
			global $wpml_language_resolution;

			$site_press = isset( $GLOBALS['sitepress'] ) ? $GLOBALS['sitepress'] : '';
			if ( ! $site_press || ! $site_press instanceof SitePress ) {
				return;
			}

			$language_negotiation_type = $site_press->get_setting( 'language_negotiation_type' );

			if ( $language_negotiation_type == '1' ) {
				add_filter( 'penci_amp_pre_exclude_subdir', array( $wpml_language_resolution, 'get_active_language_codes' ) );
			}
		}


		public static function plugins_loaded(){
			if ( function_exists( 'custom_permalinks_request' ) ) {
				add_filter( 'request', array( __CLASS__,'custom_permalinks'  ), 15 );
			}

			add_filter( 'run_ngg_resource_manager', '__return_false', 999 );

			if ( defined( 'WPML_PLUGIN_BASENAME' ) && WPML_PLUGIN_BASENAME ) {

				add_action( 'wpml_is_redirected', '__return_false' );
			}
		}

		public static function custom_permalinks( $query_vars ){
			$amp_query_vars = defined( 'AMP_QUERY_VAR' ) ? AMP_QUERY_VAR : 'amp';
			$path   = self::get_wp_installation_slug();

			if ( ! (
				preg_match( "#^$path/*$amp_query_vars/(.*?)/*$#", $_SERVER['REQUEST_URI'], $matched )
				||
				preg_match( "#^$path/*(.*?)/$amp_query_vars/*$#", $_SERVER['REQUEST_URI'], $matched )
			)
			) {
				return $query_vars;
			}

			if ( empty( $matched[1] ) ) {
				return $query_vars;
			}

			remove_filter( 'request', array( __CLASS__,'custom_permalinks'  ), 15 );

			$_SERVER['REQUEST_URI'] = '/' . $matched[1] . '/';
			$query_vars ['amp']     = '1';
			$_REQUEST['amp']        = '1';


			if ( $new_qv = custom_permalinks_request( $query_vars ) ) {

				$new_qv['amp'] = '1';
				remove_filter( 'template_redirect', 'custom_permalinks_redirect', 5 );

				return $new_qv;
			}

			return $query_vars;
		}

		public static function get_wp_installation_slug() {

			static $path;

			if ( $path ) {
				return $path;
			}

			$abspath_pre = str_replace( '\\', '/', ABSPATH );
			$abspath_pre = str_replace( '/usr', '', $abspath_pre );

			$fname_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );

			if ( $fname_dir . '/' == $abspath_pre ) {
				$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['PHP_SELF'] );

			} elseif ( false !== strpos( $_SERVER['SCRIPT_FILENAME'], $abspath_pre ) ) {
				$directory = str_replace( ABSPATH, '', $fname_dir );
				$path      = preg_replace( '#/' . preg_quote( $directory, '#' ) . '/[^/]*$#i', '', $_SERVER['REQUEST_URI'] );
			} elseif ( '' !== $fname_dir && false !== strpos( $abspath_pre, $fname_dir ) ) {
				$subdirectory = substr( $abspath_pre, strpos( $abspath_pre, $fname_dir ) + strlen( $fname_dir ) );
				$path         = preg_replace( '#/[^/]*$#i', '', $_SERVER['REQUEST_URI'] ) . $subdirectory;
			} else {
				$path = $_SERVER['REQUEST_URI'];
			}

			if ( is_multisite() && ! is_main_site() ) {
				$current_site_url = get_site_url();
				$append_path      = str_replace( get_site_url( get_current_site()->blog_id ), '', $current_site_url );

				if ( $append_path !== $current_site_url ) {
					$path .= $append_path;
				}
			}

			return $path;
		}

		public static function fastest_cache(){
			$options = get_option( "WpFastestCache" );

			if ( $options ) {

				$GLOBALS["wp_fastest_cache_options"] = json_decode( $options );

				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheRenderBlocking );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheCombineJsPowerFul );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheMinifyJs );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheCombineJs );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheCombineCss );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheLazyLoad );
				unset( $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheGoogleFonts );

			} else {
				$GLOBALS["wp_fastest_cache_options"] = array();
			}
		}

		public static function convert_plug(){
			self::remove_class_filter( 'the_content', 'Convert_Plug', 'cp_add_content', 10 );
		}

		public static function rocket_disable_options(){
			if ( is_penci_amp() ) {
				if ( function_exists( 'wp_resource_hints' ) ) {
					remove_filter( 'wp_resource_hints', 'rocket_dns_prefetch', 10, 2 );
				} else {
					remove_filter( 'rocket_buffer', 'rocket_dns_prefetch_buffer', 12 );
				}

				remove_filter( 'rocket_buffer', 'rocket_exclude_deferred_js', 11 );
				remove_filter( 'rocket_buffer', 'rocket_minify_process', 13 );
				remove_filter( 'rocket_buffer', 'rocket_defer_js', 14 );

				if( class_exists( 'Rocket_Critical_CSS' ) ){
					self::remove_class_filter( 'rocket_buffer', 'Rocket_Critical_CSS', 'async_css', 10 );
					self::remove_class_filter( 'rocket_buffer', 'Rocket_Critical_CSS', 'insert_critical_css_buffer', 10 );
				}

				remove_filter( 'rocket_buffer', 'rocket_minify_html', 20 );

				add_filter( 'do_rocket_lazyload', '__return_false' );

				// this filter is documented in inc/front/protocol.php.
				$do_rocket_protocol_rewrite = apply_filters( 'do_rocket_protocol_rewrite', false );

				if( function_exists( 'get_rocket_option' ) ){
					if ( ( get_rocket_option( 'do_cloudflare', 0 ) && get_rocket_option( 'cloudflare_protocol_rewrite', 0 ) || $do_rocket_protocol_rewrite ) ) {
						remove_filter( 'rocket_buffer', 'rocket_protocol_rewrite', PHP_INT_MAX );
						remove_filter( 'wp_calculate_image_srcset', 'rocket_protocol_rewrite_srcset', PHP_INT_MAX );
					}
				}

				remove_action( 'wp_footer', '__rocket_insert_minify_js_in_footer', PHP_INT_MAX );

				add_filter( 'pre_get_rocket_option_purge_cron_interval', function(){
					return 0;
				} );
				add_filter( 'pre_get_rocket_option_purge_cron_unit', function(){
					return 'SECOND_IN_SECONDS';
				} );
			}
		}

		/**
		 * Remove Class Filter Without Access to Class Object
		 * Copyright: https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
		 *
		 * @return bool Whether the function is removed.
		 */
		public static function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
			global $wp_filter;
			// Check that filter actually exists first
			if ( ! isset( $wp_filter[ $tag ] ) ) {
				return FALSE;
			}
			/**
			 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
			 * a simple array, rather it is an object that implements the ArrayAccess interface.
			 *
			 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
			 *
			 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
			 */
			if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
				// Create $fob object from filter tag, to use below
				$fob       = $wp_filter[ $tag ];
				$callbacks = &$wp_filter[ $tag ]->callbacks;
			} else {
				$callbacks = &$wp_filter[ $tag ];
			}
			// Exit if there aren't any callbacks for specified priority
			if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
				return FALSE;
			}
			// Loop through each filter for the specified priority, looking for our class & method
			foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
				// Filter should always be an array - array( $this, 'method' ), if not goto next
				if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
					continue;
				}
				// If first value in array is not an object, it can't be a class
				if ( ! is_object( $filter['function'][0] ) ) {
					continue;
				}
				// Method doesn't match the one we're looking for, goto next
				if ( $filter['function'][1] !== $method_name ) {
					continue;
				}
				// Method matched, now let's check the Class
				if ( get_class( $filter['function'][0] ) === $class_name ) {
					// WordPress 4.7+ use core remove_filter() since we found the class object
					if ( isset( $fob ) ) {
						// Handles removing filter, reseting callback priority keys mid-iteration, etc.
						$fob->remove_filter( $tag, $filter['function'], $priority );
					} else {
						// Use legacy removal process (pre 4.7)
						unset( $callbacks[ $priority ][ $filter_id ] );
						// and if it was the only filter in that priority, unset that priority
						if ( empty( $callbacks[ $priority ] ) ) {
							unset( $callbacks[ $priority ] );
						}
						// and if the only filter for that tag, set the tag to an empty array
						if ( empty( $callbacks ) ) {
							$callbacks = array();
						}
						// Remove this filter from merged_filters, which specifies if filters have been sorted
						unset( $GLOBALS['merged_filters'][ $tag ] );
					}
					return TRUE;
				}
			}
			return FALSE;
		}

		public static function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
			self::remove_class_filter( $tag, $class_name, $method_name, $priority );
		}
	}

	Penci_Amp_Compatibility::init();
endif;