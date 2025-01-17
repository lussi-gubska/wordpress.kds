<?php

namespace PenciPaywall;

/**
 * Class Frontend_Status
 *
 * @package PenciPaywall
 */
class Account_Status {
	/**
	 * @var Account_Status
	 */
	private static $instance;

	private $endpoint;

	/**
	 * Frontend_Status constructor.
	 */
	private function __construct() {
		// actions.
		add_action( 'penci_account_main_content', array( $this, 'get_right_content' ) );

		// filters.
		add_filter( 'penci_account_page_endpoint', array( $this, 'add_account_endpoint' ) );
		add_filter( 'penci_logged_in_items', [ $this, 'add_loggedin_items' ], 20, 1 );
	}

	/**
	 * @return Account_Status
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Add menu to frontend account
	 *
	 * @param $endpoint
	 *
	 * @return array
	 */
	public function add_account_endpoint( $endpoint ) {
		$item['penci_paywall_sub'] = array(
			'icon'  => 'fa-credit-card',
			'title' => pencipw_text_translation( 'penci_paywall_sub' ),
			'slug'  => get_theme_mod( 'pencipw_text_subscription_slug', 'my-subscription' ),
			'label' => 'my_subscription',
		);

		$item['penci_paywall_unl'] = array(
			'icon'  => 'fa-unlock ',
			'title' => pencipw_text_translation( 'penci_paywall_unl' ),
			'slug'  => get_theme_mod( 'pencipw_text_unlocked_slug', 'unlocked-posts' ),
			'label' => 'unlocked_posts',
		);

		$this->endpoint = apply_filters( 'penci_paywall_endpoint', $item );

		if ( isset( $this->endpoint ) ) {
			$endpoint = array_merge( $endpoint, $this->endpoint );
		}

		return $endpoint;
	}

	public function add_loggedin_items( $items ) {
		$custom_items = $this->endpoint;
		$post_slug    = get_theme_mod( 'penci_frontend_submit_account_slug', 'account' );
		foreach ( $custom_items as $item ) {
			$items[ $item['label'] ] = array(
				'icon' => $item['icon'],
				'link' => esc_attr( home_url( '/' ) . $post_slug . '/' . $item['slug'] ),
				'text' => $item['title'],
			);
		}

		return $items;
	}

	/**
	 * Get content template for frontend account page
	 */
	public function get_right_content() {
		global $wp;

		if ( is_user_logged_in() ) {
			if ( isset( $wp->query_vars['account'] ) && ! empty( $wp->query_vars['account'] ) ) {

				$query_vars = explode( '/', $wp->query_vars['account'] );

				if ( $query_vars[0] == get_theme_mod( 'pencipw_text_subscription_slug', 'my-subscription' ) ) {
					$template = PENCI_PAYWALL_PATH . 'account/my-subscription.php';

					if ( file_exists( $template ) ) {
						include $template;
					}
				} elseif ( $query_vars[0] == get_theme_mod( 'pencipw_text_unlocked_slug', 'unlocked-posts' ) ) {
					$template = PENCI_PAYWALL_PATH . 'account/unlocked-posts.php';

					if ( file_exists( $template ) ) {
						include $template;
					}
				}
			}
		}
	}
}

Account_Status::instance();