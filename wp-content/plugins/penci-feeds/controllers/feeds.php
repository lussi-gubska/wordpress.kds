<?php

namespace PenciFeeds;

require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/models/feed.php' );
require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/validate-form.php' );
require_once( PENCIFEEDS_PLUGIN_DIR . '/classes/posts-grid.php' );

/**
 * Feeds controller
 */
class Feeds {
	/**
	 * Access to bootstrap object
	 */
	private $bootstrap = null;

	public function __construct( $bootstrap = null ) {
		$this->bootstrap = $bootstrap;

		if ( is_admin() ) {
			$screen = get_current_screen();
			$screen->add_help_tab( array(
				'id'      => 'pcfds-feeds-help',
				'title'   => __( 'Getting started', 'penci-feeds' ),
				'content' =>
					'<p>' . __( 'This is a page you should start with. You can see a list of existing feeds on index page.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'For more details check <a href="' . pencifeeds_plugin_url( 'PenciFeeds-UserManual.pdf' ) . '">User Manual</a>', 'penci-feeds' ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'      => 'pcfds-feeds-general-help',
				'title'   => __( 'General Options', 'penci-feeds' ),
				'content' =>
					'<p>' . __( 'You can put any RSS or Atom URL into Feed URL input.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Author, status and categories are default settings for new posts.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Update frequency defines how often script will check feeds.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'You can download all images to your website using "Download images" option', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Add canonical URL option adds meta tag to your post page with link to original article.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'You can add "Read more" link using your specific template that will be appended to the end of content', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Web scrapper downloads pages by URLs from feed and tries to parse post content, it is useful when summary is too short', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Some feeds provide thumbnails. You can use "Thumbnails from feed" option in this case. If there are no thumbnails then you can try "Thumbnail from content". You can always preview feed before saving.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'For more details check <a href="' . pencifeeds_plugin_url( 'PenciFeeds-UserManual.pdf' ) . '">User Manual</a>', 'penci-feeds' ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'      => 'pcfds-feeds-filters-help',
				'title'   => __( 'Filters', 'penci-feeds' ),
				'content' =>
					'<p>' . __( 'You can filter feed posts by specific words you want or don\'t want to appear', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'In order to do this select "Enable content filters" and put and words separating them by commmas.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'Words may appear in title, article or feed summary.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'For more details check <a href="' . pencifeeds_plugin_url( 'PenciFeeds-UserManual.pdf' ) . '">User Manual</a>', 'penci-feeds' ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'      => 'pcfds-feeds-scrapper-help',
				'title'   => __( 'Content Scrapper', 'penci-feeds' ),
				'content' =>
					'<p>' . __( 'In cases when summary is too short you can enable Content Scrapper option. In most cases it works perfect, but, in some cases you may need to specify content box yourself.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'To select content on a web page click "Configure content extractor" button just below. You will see a dialog with first page from feed opened in it. Here you can select any box by clicking on it and it will become green. Close dialog and you will see text box below button updated with XPath necessary to get this article. Click "Preview" button to see sample results.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'You can also "remove" some blocks inside selected block by clicking on them. It is pretty simple: if block has green background - it\'s contents will be scraped except for those that are red.', 'penci-feeds' ) . '</p>' .
					'<p>' . __( 'For more details check <a href="' . pencifeeds_plugin_url( 'PenciFeeds-UserManual.pdf' ) . '">User Manual</a>', 'penci-feeds' ) . '</p>'
			) );
		}
	}

	/**
	 * List of feeds
	 * @return array
	 */
	public function index() {
		add_action( 'admin_notices', 'pencifeeds_display_compatibility_check' );
		$grid = new \PenciFeeds\PostsGrid( 'pcfds-feed' );

		return array(
			'grid' => $grid
		);
	}

	/**
	 * Create new feed
	 * @return array
	 */
	function add() {
		add_action( 'admin_notices', 'pencifeeds_display_compatibility_check' );
		$request = $this->bootstrap->getRequest();
		$data    = $request->getPost();

		if ( ! empty( $data ) && $request->isAjaxRequest() ) {
			check_admin_referer( 'pcfds-save-penci-feeds' );

			$form = $this->getFeedFormValidator( $data );

			if ( ! $form->isValid() ) {
				$this->sendAjaxRespone( false, $form->validate() );
			} else {
				$dataSource = new \PenciFeeds\FeedModel( $data );
				$dataSource->save();

				$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
				$this->sendAjaxRespone( true, array(), array(), $redirectUrl );
			}
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'penci-feeds',
			pencifeeds_plugin_url( 'admin/js/feeds.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION,
			'all'
		);

		wp_enqueue_script(
			'base64encode',
			pencifeeds_plugin_url( 'admin/js/base64.min.js' ),
			array(),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_script(
			'fancybox',
			pencifeeds_plugin_url( 'admin/js/fancybox/jquery.fancybox.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_style(
			'fancybox-css',
			pencifeeds_plugin_url( 'admin/js/fancybox/jquery.fancybox.css' ),
			array(),
			PENCIFEEDS_VERSION,
			'all'
		);

		// Tooltips
		wp_enqueue_script(
			'tooltipster',
			pencifeeds_plugin_url( 'admin/js/jquery.tooltipster.min.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_style(
			'tooltipster-css',
			pencifeeds_plugin_url( 'admin/css/tooltipster.css' ),
			array(),
			PENCIFEEDS_VERSION,
			'all'
		);

		return array(
			'statuses'         => $this->_getStatuses(),
			'updateFrequences' => $this->_getUpdateFrequences(),
			'postTypes'        => get_post_types( '', 'names' ),
			'languages'        => get_locale(),
		);
	}

	/**
	 * Edit feed
	 * @return array
	 */
	function edit() {
		add_action( 'admin_notices', 'pencifeeds_display_compatibility_check' );
		$request = $this->bootstrap->getRequest();
		$data    = $request->getPost();
		$post    = $request->getRequest( 'post' );

		if ( ! empty( $data ) && $request->isAjaxRequest() ) {
			check_admin_referer( 'pcfds-save-penci-feeds' );

			$form = $this->getFeedFormValidator( $data );

			if ( ! $form->isValid() ) {
				$this->sendAjaxRespone( false, $form->validate() );
			} else {
				$dataSource = new \PenciFeeds\FeedModel( $post );

				if ( ! isset( $data['post_category'] ) ) {
					$data['post_category'] = [];
				}

				if ( ! isset( $data['enable_scrapper'] ) ) {
					$data['enable_scrapper'] = 0;
				}
				if ( ! isset( $data['display_readmore'] ) ) {
					$data['display_readmore'] = 0;
				}
				if ( ! isset( $data['use_date'] ) ) {
					$data['use_date'] = 0;
				}
				if ( ! isset( $data['download_images'] ) ) {
					$data['download_images'] = 0;
				}
				if ( ! isset( $data['add_canonical'] ) ) {
					$data['add_canonical'] = 0;
				}
				if ( ! isset( $data['enable_filters'] ) ) {
					$data['enable_filters'] = 0;
				}
				if ( ! isset( $data['overwrite_posts'] ) ) {
					$data['overwrite_posts'] = 0;
				}
				if ( ! isset( $data['dont_add_excerpt'] ) ) {
					$data['dont_add_excerpt'] = 0;
				}
				if ( ! isset( $data['add_more_tag'] ) ) {
					$data['add_more_tag'] = 0;
				}
				if ( ! isset( $data['remove_links'] ) ) {
					$data['remove_links'] = 0;
				}

				if ( ! isset( $data['extract_categories'] ) ) {
					$data['extract_categories'] = 0;
				}
				if ( ! isset( $data['extract_tags'] ) ) {
					$data['extract_tags'] = 0;
				}

				$dataSource->setValues( $data );
				$dataSource->save();

				$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
				$this->sendAjaxRespone( true, array(), array(), $redirectUrl );
			}
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'penci-feeds',
			pencifeeds_plugin_url( 'admin/js/feeds.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION,
			'all'
		);

		wp_enqueue_script(
			'base64encode',
			pencifeeds_plugin_url( 'admin/js/base64.min.js' ),
			array(),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_script(
			'fancybox',
			pencifeeds_plugin_url( 'admin/js/fancybox/jquery.fancybox.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_style(
			'fancybox-css',
			pencifeeds_plugin_url( 'admin/js/fancybox/jquery.fancybox.css' ),
			array(),
			PENCIFEEDS_VERSION,
			'all'
		);

		// Tooltips
		wp_enqueue_script(
			'tooltipster',
			pencifeeds_plugin_url( 'admin/js/jquery.tooltipster.min.js' ),
			array( 'jquery' ),
			PENCIFEEDS_VERSION
		);

		wp_enqueue_style(
			'tooltipster-css',
			pencifeeds_plugin_url( 'admin/css/tooltipster.css' ),
			array(),
			PENCIFEEDS_VERSION,
			'all'
		);

		$feed = new \PenciFeeds\FeedModel( $post );

		return array(
			'feed'             => $feed,
			'statuses'         => $this->_getStatuses(),
			'updateFrequences' => $this->_getUpdateFrequences(),
			'postTypes'        => get_post_types( '', 'names' ),
			'languages'        => get_locale(),
		);
	}

	/**
	 * Delete feed action
	 */
	public function delete() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );

		if ( $post ) {
			$posts = array();
			if ( is_array( $post ) ) {
				$posts = $post;
			} else {
				$posts = array( $post );
			}

			foreach ( $posts as $postId ) {
				$feed = new \PenciFeeds\FeedModel( array( 'id' => $postId ) );
				$feed->delete();
			}

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error deleting', 'penci-feeds' ) );
		}
	}

	/**
	 * Delete posts added by feed
	 */
	public function removePosts() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );

		if ( $post ) {
			$feed = new \PenciFeeds\FeedModel( $post );

			$posts = get_posts( array(
				'post_type'      => 'any',
				'posts_per_page' => - 1,
				'post_status'    => 'any',
				'post_parent'    => null,
				'meta_key'       => '_rss_feed_id',
				'meta_value'     => $post
			) );

			foreach ( $posts as $item ) {
				// Get attachments and delete them
				$attachments = get_attached_media( 'image', $item->ID );
				foreach ( $attachments as $attachment ) {
					wp_delete_attachment( $attachment->ID, true );
				}

				// Delete post
				wp_delete_post( $item->ID, true );
			}

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error deleting posts', 'penci-feeds' ) );
		}
	}

	/**
	 * Change feed status action
	 */
	public function changeStatus() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );
		$status  = $request->getRequest( 'status' );

		if ( $post ) {
			if ( $status != 'started' ) {
				$status = 'stopped';
			}

			$feed                  = new \PenciFeeds\FeedModel( $post );
			$feed->campaign_status = $status;
			$feed->save();

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error changing status', 'penci-feeds' ) );
		}
	}

	/**
	 * Disable feeds action
	 */
	public function disable() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );

		if ( $post ) {
			$posts = array();
			if ( is_array( $post ) ) {
				$posts = $post;
			} else {
				$posts = array( $post );
			}

			foreach ( $posts as $postId ) {
				$feed                  = new \PenciFeeds\FeedModel( $postId );
				$feed->campaign_status = 'stopped';
				$feed->save();
			}

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error deleting', 'penci-feeds' ) );
		}
	}

	/**
	 * Enable feeds action
	 */
	public function enable() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );

		if ( $post ) {
			$posts = array();
			if ( is_array( $post ) ) {
				$posts = $post;
			} else {
				$posts = array( $post );
			}

			foreach ( $posts as $postId ) {
				$feed                  = new \PenciFeeds\FeedModel( $postId );
				$feed->campaign_status = 'started';
				$feed->save();
			}

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error deleting', 'penci-feeds' ) );
		}
	}

	/**
	 * Run feed
	 */
	public function run() {
		$request = $this->bootstrap->getRequest();
		$post    = $request->getRequest( 'post' );

		if ( $post ) {
			@set_time_limit( 600 );
			$feed = new \PenciFeeds\FeedModel( $post );

			$feed->last_update = time();
			$feed->save();
			$feed->updateNews();

			$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds' );
			wp_safe_redirect( $redirectUrl );
			exit();
		} else {
			wp_die( __( 'Error changing status', 'penci-feeds' ) );
		}
	}

	/**
	 * Preview feed
	 * @return array
	 */
	public function preview() {
		$request = $this->bootstrap->getRequest();
		$data    = $request->getPost();

		$url = strtolower( trim( $data['url'] ) );
		if ( ( substr( $url, 0, 4 ) == 'http' ) || ( substr( $url, 0, 4 ) == 'feed' ) ) {
			$feed = new \PenciFeeds\FeedModel( $data );
			$list = $feed->getNews( 5 );
		} else {
			$list = array();
			$feed = null;
		}

		$this->bootstrap->loadTemplate(
			array(
				'list' => $list,
				'feed' => $feed
			)
		);

		exit;
	}

	/**
	 * Preview sample article for extracting content
	 */
	public function extract() {
		$request = $this->bootstrap->getRequest();
		$feedUrl = $request->getRequest( 'feedUrl' );
		$page    = null;

		if ( $feedUrl ) {
			$feedUrl = base64_decode( $feedUrl );
			$feed    = new \PenciFeeds\FeedModel( array( 'url' => $feedUrl ) );
			$page    = $feed->getFirstPage( $this->bootstrap->menuUrl( 'pcfds-feeds', 'downloader' ) );
		}

		$this->bootstrap->loadTemplate(
			array(
				'page' => $page
			)
		);
		exit;
	}

	public function downloader() {
		$request = $this->bootstrap->getRequest();
		$url     = base64_decode( $request->getRequest( 'url' ) );

		if ( substr( $url, 0, 2 ) == '//' ) {
			$url = 'http://' . substr( $url, 2 );
		}

		echo file_get_contents( $url,
			false,
			stream_context_create(
				array(
					'http' => array(
						'ignore_errors' => true
					)
				)
			)
		);

		exit;
	}


	/**
	 * List of feeds
	 * @return array
	 */
	public function logs() {
		$file    = PENCIFEEDS_PLUGIN_DIR . '/logs.txt';
		$content = '';
		if ( file_exists( $file ) ) {
			$content = file_get_contents( $file );
		}

		return array(
			'content' => $content
		);
	}

	/**
	 * Clear logs action
	 */
	public function clearLogs() {
		$file = PENCIFEEDS_PLUGIN_DIR . '/logs.txt';

		if ( file_exists( $file ) ) {
			$fp = fopen( $file, 'w' );
			fclose( $fp );
		}

		$redirectUrl = $this->bootstrap->menuUrl( 'pcfds-feeds', 'logs' );

		wp_safe_redirect( $redirectUrl );
		exit();
	}

	/**
	 * Returns feed form validator
	 *
	 * @param $data
	 *
	 * @return ValidateForm
	 */
	private function getFeedFormValidator( $data ) {
		$form = new \PenciFeeds\ValidateForm();
		$form->setData( $data );
		$type = isset( $data['type'] ) ? $data['type'] : '';

		$form->addField(
			'title',
			__( 'Title', 'penci-feeds' ),
			array( 'required' )
		);

		$form->addField(
			'url',
			__( 'Feed URL', 'penci-feeds' ),
			array( 'required' )
		);

		return $form;
	}

	/**
	 * Send AJAX response of a specified format
	 *
	 * @param $status
	 * @param array $errors
	 * @param string $redirectUrl
	 */
	function sendAjaxRespone( $status, $errors = array(), $data = array(), $redirectUrl = '' ) {
		$response = array(
			'status' => $status
		);

		if ( $errors && count( $errors ) ) {
			$response['errors'] = $errors;
		}

		if ( $data && count( $data ) ) {
			$response['data'] = $data;
		}

		if ( $redirectUrl ) {
			$response['redirect_url'] = $redirectUrl;
		}

		echo pencifeeds_json_encode( $response );

		exit;
	}

	private function _getStatuses() {
		return get_post_statuses();
	}

	private function _getUpdateFrequences() {
		return array(
			'600'   => __( '10 minutes', 'penci-feeds' ),
			'1200'  => __( '20 minutes', 'penci-feeds' ),
			'1800'  => __( '30 minutes', 'penci-feeds' ),
			'3600'  => __( '1 hour', 'penci-feeds' ),
			'7200'  => __( '2 hours', 'penci-feeds' ),
			'14400' => __( '4 hours', 'penci-feeds' ),
			'28800' => __( '8 hours', 'penci-feeds' ),
			'57600' => __( '16 hours', 'penci-feeds' ),
			'86400' => __( '1 day', 'penci-feeds' ),
		);
	}
}

?>