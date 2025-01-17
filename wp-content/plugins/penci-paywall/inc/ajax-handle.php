<?php

namespace PenciPaywall;

use Exception;
use PenciPaywall\Payments\Paypal\Paypal_Api;
use PenciPaywall\Payments\Stripe\Stripe_Api;

class PaywallAjaxHandle {
	/**
	 * @var Init
	 */
	private static $instance;

	/**
	 * @return Init
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Init constructor.
	 */
	private function __construct() {
		add_action( 'wp_ajax_add_paywall_product', [ $this, 'add_product_ajax' ] );
		add_action( 'wp_ajax_refresh_checkout_redirect', array( $this, 'refresh_redirect' ) );
		add_action( 'wp_ajax_nopriv_refresh_checkout_redirect', array( $this, 'refresh_redirect' ) );
		add_action( 'wp_ajax_paywall_handler', array( $this, 'unlock_post_ajax' ) );
		add_action( 'wp_ajax_cancel_subs_handler', array( $this, 'cancel_subs_ajax' ) );
		add_action( 'wp_ajax_default_source_handler', array( $this, 'default_source' ) );
	}

	public function refresh_redirect() {
		global $wp;

		$login_reload = home_url( $wp->request );

		if ( isset( $_COOKIE['paywall_product'] ) && function_exists( 'wc_get_checkout_url' ) ) {
			$login_reload = wc_get_checkout_url();
		}

		wp_send_json(
			array(
				'message'      => 'success',
				'login_reload' => $login_reload,
			)
		);
	}

	public function add_product_ajax() {
		if ( isset( $_POST['product_id'] ) && isset( $_POST['action'] ) && $_POST['action'] == 'add_paywall_product' ) {
			$product_id = (int) sanitize_text_field( $_POST['product_id'] );
			$product    = get_post( $product_id );

			try {

				if ( $product->post_type !== 'product' ) {
					throw new Exception( esc_html__( 'This is not a product', 'penci-paywall' ) );
				}

				WC()->cart->add_to_cart( $product_id );

				$redirect = wc_get_checkout_url();
				wp_send_json(
					array(
						'message'  => esc_html__( 'added', 'penci-paywall' ),
						'redirect' => $redirect,
					)
				);

			} catch ( Exception $e ) {
				throw new Exception( esc_html__( 'Error adding product', 'penci-paywall' ) );
			}
		}

		die();
	}

	/**
	 * Ajax for Unlock Post
	 */
	public function unlock_post_ajax() {
		if ( is_user_logged_in() && isset( $_POST['unlock_post_id'] ) ) {
			if ( $_POST['unlock_post_id'] == 1 && $_POST['action'] == 'paywall_handler' ) {
				$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) ? get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) : 0;
				$unlocked_posts   = get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) ? get_user_option( 'pencipw_unlocked_post_list', get_current_user_id() ) : array();

				$post_id = (int) sanitize_text_field( $_POST['post_id'] );

				if ( $unlock_remaining > 0 && ! in_array( (int) $post_id, $unlocked_posts ) ) {
					$unlocked_posts[] = $post_id;
					$unlock_remaining = $unlock_remaining - 1;

					update_user_option( get_current_user_id(), 'pencipw_unlocked_post_list', $unlocked_posts );
					update_user_option( get_current_user_id(), 'pencipw_unlock_remaining', $unlock_remaining );
				}
			}
		}

		wp_send_json(
			array(
				'message' => esc_html__( 'paywall ajax sent', 'penci-paywall' ),
			)
		);

		die();
	}

	public function cancel_subs_ajax() {
		if ( is_user_logged_in() && isset( $_POST['cancel_subscription'] ) ) {
			if ( $_POST['cancel_subscription'] == 'yes' && $_POST['action'] == 'cancel_subs_handler' ) {
				$paypal_subs_id = get_user_option( 'pencipw_paypal_subs_id', get_current_user_id() );
				$stripe_subs_id = get_user_option( 'pencipw_stripe_subs_id', get_current_user_id() );
				if ( $paypal_subs_id != '' ) {
					$credentials      = new \PenciPW_Paypal();
					$subscribe_cancel = new Paypal_Api( 'cancel', $credentials->get_api_credential() );
					if ( $subscribe_cancel->get_response_message() == '204' ) {
						update_user_option( get_current_user_id(), 'pencipw_subscribe_status', false );
						update_user_option( get_current_user_id(), 'pencipw_expired_date', false );
					}
				}
				if ( $stripe_subs_id != '' ) {
					$credentials      = new \PenciPW_Stripe();
					$subscribe_cancel = new Stripe_Api( 'cancel', $credentials->get_api_credential() );
					$response         = $subscribe_cancel->get_response_message();
					if ( isset( $response['status'] ) ) {
						if ( $response['status'] == 'canceled' || $response['status'] == 'error' ) {
							update_user_option( get_current_user_id(), 'pencipw_subscribe_status', false );
							update_user_option( get_current_user_id(), 'pencipw_expired_date', false );
						}
					}
				}

				/** WCS Integration */
				if ( function_exists( 'wcs_get_subscription' ) ) {
					$subscription_id = get_user_option( 'pencipw_subscribe_id', get_current_user_id() );
					if ( ! empty( $subscription_id ) || ! $subscription_id ) {
						$subscription = wcs_get_subscription( $subscription_id );
						if ( is_object( $subscription ) ) {
							$subscription->set_payment_method();
							if ( $subscription->can_be_updated_to( 'cancelled' ) ) {
								$subscription->update_status( 'cancelled' );
								update_user_option( get_current_user_id(), 'pencipw_subscribe_status', false );
								update_user_option( get_current_user_id(), 'pencipw_expired_date', false );
								update_user_option( get_current_user_id(), 'pencipw_subscribe_id', false );
							}
						}
					}
				}
			}
		}

		wp_send_json(
			array(
				'message' => esc_html__( 'cancel subscription', 'penci-paywall' ),
			)
		);

		die();
	}

	public function default_source() {
		if ( is_user_logged_in() && isset( $_POST['source_id'] ) ) {
			$customer_id = get_user_option( 'pencipw_stripe_customer_id', get_current_user_id() );
			$source_id   = sanitize_text_field( $_POST['source_id'] );
			if ( $customer_id != '' ) {
				$credentials = new \PenciPW_Stripe();
				$request     = new Stripe_Api( 'update_default_source', $credentials->get_api_credential(), null, null, array(
					$customer_id,
					array( 'default_source' => $source_id )
				) );
			}
		}

		wp_send_json(
			array(
				'message' => esc_html__( 'default source', 'penci-paywall' ),
			)
		);

		die();
	}

}

PaywallAjaxHandle::instance();