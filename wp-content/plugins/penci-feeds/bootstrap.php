<?php
/**
 * Plugin bootstrap class
 */

namespace PenciFeeds;

use Exception;

require_once PENCIFEEDS_PLUGIN_DIR . '/classes/request.php';

class Bootstrap {

	private $prefix = 'pcfds-';
	private $namespace = '\PenciFeeds';
	private $routes = array();
	private $templateVariables = array();

	/**
	 * @var string
	 */
	public $currentPage = '';

	/**
	 * @var string
	 */
	public $currentAction = '';

	private $request = null;

	/**
	 *
	 */
	public function __construct() {
		$this->request = \PenciFeeds\Request::getInstance();
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Register admin menu
	 * @return void
	 */
	public function registerMenu() {
		// Main menu block
		add_menu_page( __( 'Penci RSS Aggregator', 'penci-feeds' ),
			__( 'Penci RSS Aggregator', 'penci-feeds' ),
			'activate_plugins', 'pcfds-feeds',
			array( $this, 'loadTemplate' ), 'dashicons-rss' );

		$this->addSubMenu(
			'pcfds-feeds',
			__( 'All Feeds', 'penci-feeds' ),
			'activate_plugins',
			'feeds'
		);

		// Add feed
		$this->addSubMenu(
			'pcfds-feeds',
			__( 'Add Feed', 'penci-feeds' ),
			'activate_plugins',
			'feeds',
			'add'
		);

		// Show logs
		$this->addSubMenu(
			'pcfds-feeds',
			__( 'Logs', 'penci-feeds' ),
			'activate_plugins',
			'feeds',
			'logs'
		);
	}

	/**
	 * Add admin submenu
	 *
	 * @param $parent
	 * @param $title
	 * @param $permission
	 * @param $controller
	 * @param string $action
	 *
	 * @return false|string
	 */
	private function addSubMenu( $parent, $title, $permission, $controller, $action = '' ) {
		$path = $this->prefix . $controller . ( $action ? '&action=' . $action : '' );

		$name = add_submenu_page(
			$parent,
			$title, $title,
			$permission, $path,
			array( $this, 'loadTemplate' )
		);

		$this->routes[ $name ] = array(
			'controller' => $controller,
			'action'     => $action ? $action : 'index'
		);

		add_action( 'load-' . $name, array( $this, 'route' ) );

		return $name;
	}

	/**
	 * Load admin panel specific hooks
	 */
	public function loadAdmin() {
		// Register menu
		add_action( 'admin_menu', array( $this, 'registerMenu' ) );
	}

	/**
	 *
	 */
	public function updateFeeds() {
		require_once( PENCIFEEDS_PLUGIN_DIR . '/../../../wp-admin/includes/file.php' );
		require_once( PENCIFEEDS_PLUGIN_DIR . '/../../../wp-admin/includes/media.php' );
		require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/models/feed.php' );

		@ini_set( 'safe_mode', 'Off' );
		@ini_set( 'ignore_user_abort', 'Off' );
		@ignore_user_abort( true );

		// Get all feeds from DB
		$feeds = get_posts( array(
			'post_type'      => 'pcfds-feed',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_parent'    => null
		) );
		pencifeeds_add_log( 'Feeds selected: ' . count( $feeds ) );

		// Temporary remove post filters to allow adding iframes and objects as post content
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
		remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		// Execute updateNews on each feed that requires it
		foreach ( $feeds as $item ) {
			pencifeeds_add_log( 'Checking: ' . $item->post_title );
			$feed = new \PenciFeeds\FeedModel( $item->ID );

			if ( $feed->campaign_status != 'started' ) {
				pencifeeds_add_log( 'Ignoring inactive campaign: ' . $item->post_title );
				continue;
			}
			if ( ! $feed->last_update ) {
				$feed->last_update = 0;
			}

			if ( time() > ( (int) $feed->last_update + (int) $feed->update_frequency ) ) {
				pencifeeds_add_log( 'Loading: ' . $feed->title );
				if ( ! $feed->url ) {
					pencifeeds_add_log( 'Error: URL not specified' );
				} else {
					try {
						$feed->last_update = time();
						$feed->save();
						$feed->updateNews();
						$feed->save();
					} catch ( Exception $e ) {
						pencifeeds_add_log( 'Caught exception: ' . $e->getMessage() );
					}
				}

			} else {
				pencifeeds_add_log( 'Update not needed for ' . $feed->title );
			}
		}

		// Add post filters back
		add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
	}

	/**
	 * Router
	 */
	public function route() {
		$page   = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$action = ( isset( $_GET['action'] ) && ( ! empty( $_GET['action'] ) ) ) ? $_GET['action'] : 'index';
		if ( ! $page ) {
			return;
		}

		$page = substr( $page, strlen( $this->prefix ) );

		$this->currentPage   = $page;
		$this->currentAction = $action;

		$className = $this->namespace . '\\' . ucfirst( $page );
		$path      = PENCIFEEDS_PLUGIN_DIR . '/controllers/' . $page . '.php';

		if ( file_exists( $path ) ) {
			require_once( $path );
			if ( class_exists( $className ) ) {
				$controller              = new $className( $this );
				$this->templateVariables = $controller->$action();
			}
		}

		return;
	}

	/**
	 * Render template
	 *
	 * @param array $vars
	 * @param null $page
	 * @param null $template
	 */
	public function loadTemplate( $vars = array(), $page = null, $template = null ) {
		if ( ! $page ) {
			$page = isset( $_GET['page'] ) ? $_GET['page'] : null;
		}

		if ( ! $template ) {
			$template = ( isset( $_GET['action'] ) && ! empty( $_GET['action'] ) ) ? $_GET['action'] : 'index';
		}

		if ( ! $page ) {
			return;
		}

		$controller = substr( $page, strlen( $this->prefix ) );

		if ( ! $vars || ! count( $vars ) ) {
			$vars = $this->templateVariables;
		}
		if ( isset( $vars ) ) {
			extract( $vars );
		}
		include( PENCIFEEDS_PLUGIN_DIR . '/templates/' . $controller . '/' . $template . '.php' );
	}

	/**
	 * Get menu URL
	 *
	 * @param $controller
	 * @param string $action
	 * @param array $params
	 *
	 * @return string
	 */
	public function menuUrl( $controller, $action = '', $params = array() ) {
		$url = menu_page_url( $controller, false );

		if ( $action ) {
			$url = add_query_arg( array( 'action' => $action ), $url );
		}

		if ( count( $params ) ) {
			$url = add_query_arg( $params, $url );
		}

		return $url;
	}

	/**
	 * Init and register post types
	 */
	public function init() {
		register_post_type( 'pcfds-feed', array(
				'labels'    => array(
					'name'          => __( 'News Feed', 'penci-feeds' ),
					'singular_name' => __( 'News Feed', 'penci-feeds' )
				),
				'rewrite'   => false,
				'query_var' => false
			)
		);
	}

	/**
	 * Returns request object
	 * @return Request|null
	 */
	public function getRequest() {
		return $this->request;
	}
}

?>