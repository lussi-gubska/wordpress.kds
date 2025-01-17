<?php

namespace PenciPaywall;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PenciPayWall_GetPaid {
	private static $instance;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {

		add_filter( 'wpinv_get_item_types', [ $this, 'wpi_custom_register_item_type' ], 10, 1 );
		add_action( 'wpinv_item_details_metabox_item_details', [
			$this,
			'wpinv_item_details_metabox_item_details'
		], 10, 1 );
		add_action( 'getpaid_item_metabox_save', [ $this, 'getpaid_item_metabox_save' ], 10, 2 );
		add_action( 'getpaid_invoice_status_publish', [ $this, 'wpi_custom_on_payment_complete' ], 10, 1 );
		add_action( 'getpaid_invoice_status_wpi-renewal', [ $this, 'wpi_custom_on_payment_complete' ], 10, 1 );
		add_action( 'getpaid_invoice_status_wpi-cancelled', [ $this, 'wpi_custom_on_payment_cancel' ], 10, 1 );
		add_action( 'getpaid_invoice_status_wpi-refunded', [ $this, 'wpi_custom_on_payment_cancel' ], 10, 1 );
		add_action( 'getpaid_invoice_status_wpi-pending', [ $this, 'wpi_custom_on_payment_cancel' ], 10, 1 );
		add_action( 'getpaid_subscription_status_changed', [ $this, 'wpi_custom_on_subscription_cancel' ], 10, 3 );
	}

	public function wpi_custom_register_item_type( $item_types ) {
		$item_types['paywall_subscribe'] = __( 'Soledad Post Subscribe', 'penci-paywall' );
		$item_types['paywall_unlock']    = __( 'Soledad Post Unlock', 'penci-paywall' );

		return $item_types;
	}

	public function wpi_custom_on_subscription_cancel( $subscription, $from, $to ) {
		$user_id      = $subscription->get_customer_id();
		$payment_type = $subscription->get_gateway();
		$product      = $subscription->get_product();
		if ( $to == 'cancelled' && $product->get_type() == 'paywall_subscribe' ) {
			update_user_option( $user_id, 'pencipw_subscribe_status', false );
			update_user_option( $user_id, 'pencipw_expired_date', false );
			update_user_option( $user_id, 'pencipw_' . $payment_type . '_subs_id', false );
			update_user_option( $user_id, 'pencipw_subs_type', false );
		}
	}

	public function wpi_custom_on_payment_cancel( $invoice ) {
		$user_id = $invoice->get_user_id();
		if ( ! empty( $invoice ) ) {
			$cart_items = $invoice->get_cart_details();

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $cart_item ) {
					$item = ! empty( $cart_item['item_id'] ) ? new \WPInv_Item( $cart_item['item_id'] ) : null;

					if ( ! empty( $item ) && $item->get_type() == 'paywall_unlock' ) {
						$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', $user_id ) ? get_user_option( 'pencipw_unlock_remaining', $user_id ) : 0;
						$unlocked_posts   = get_user_option( 'pencipw_unlocked_post_list', $user_id ) ? get_user_option( 'pencipw_unlocked_post_list', $user_id ) : array();

						if ( $unlock_remaining < 0 ) {
							$unlock_remaining = 0;
						}

						$get_total_unlock = get_post_meta( $item->get_id(), '_penci_total_unlock', true );

						if ( $unlock_remaining >= $get_total_unlock * $cart_item['quantity'] ) {
							$unlock_remaining -= $get_total_unlock * $cart_item['quantity'];
						} else {
							$leftover         = $get_total_unlock * $cart_item['quantity'] - $unlock_remaining;
							$unlock_remaining = 0;
							// lock post that has been unlocked
							for ( $i = 0; $i < $leftover; $i ++ ) {
								array_pop( $unlocked_posts );
							}
						}

						update_user_option( $user_id, 'pencipw_unlock_remaining', $unlock_remaining );
						update_user_option( $user_id, 'pencipw_unlocked_post_list', $unlocked_posts );
					}
				}
			}
		}
	}

	public function wpi_custom_on_payment_complete( $invoice ) {
		$user_id = $invoice->get_user_id();
		if ( ! empty( $invoice ) ) {
			$cart_items = $invoice->get_cart_details();

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $cart_item ) {
					$item = ! empty( $cart_item['item_id'] ) ? new \WPInv_Item( $cart_item['item_id'] ) : null;

					if ( ! empty( $item ) && $item->get_type() == 'paywall_subscribe' ) {
						$subscription = new \WPInv_Subscription( $invoice->get_subscription_id() );
						$payment_type = $invoice->get_gateway();
						update_user_option( $user_id, 'pencipw_subscribe_status', 'ACTIVE' );
						update_user_option( $user_id, 'pencipw_expired_date', Date( 'F d, Y', $subscription->get_expiration_time() ) );
						update_user_option( $user_id, 'pencipw_' . $payment_type . '_subs_id', $invoice->get_subscription_id() );
						update_user_option( $user_id, 'pencipw_subs_type', $payment_type );
					}

					if ( ! empty( $item ) && $item->get_type() == 'paywall_unlock' ) {
						$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', $user_id ) ? get_user_option( 'pencipw_unlock_remaining', $user_id ) : 0;

						if ( $unlock_remaining < 0 ) {
							$unlock_remaining = 0;
						}

						$get_total_unlock = get_post_meta( $item->get_id(), '_penci_total_unlock', true );
						$unlock_remaining += $get_total_unlock * $cart_item['quantity'];
						update_user_option( $user_id, 'pencipw_unlock_remaining', $unlock_remaining );
					}
				}
			}
		}

	}

	public function wpinv_item_details_metabox_item_details( $item ) {
		?>
        <div class="penci_gp_options pc_show_paywall_unlock">

            <div class="form-group mb-3 row">
                <label class="col-sm-3 col-form-label"
                       for="_penci_total_unlock"><?php echo esc_html__( 'Post Unlock - Total Post', 'penci-paywall' ); ?></label>

                <div class="col-sm-8">
                    <div>
                        <input type="number" name="_penci_total_unlock" style="width: 100%;" placeholder="0"
                               id="_penci_total_unlock"
                               value="<?php echo get_post_meta( get_the_ID(), '_penci_total_unlock', true ); ?>">
                    </div>
                </div>

                <div class="col-sm-1 pt-2 pl-0">
                    <span class="wpi-help-tip dashicons dashicons-editor-help" title=""
                          data-original-title="Number of post user can unlock."></span>
                </div>

            </div>

        </div>
		<?php
	}

	public function getpaid_item_metabox_save( $post_id, $item ) {
		$post_limit = isset( $_POST['_penci_total_unlock'] ) ? getpaid_standardize_amount( $_POST['_penci_total_unlock'] ) : null;
		update_post_meta( $post_id, '_penci_total_unlock', $post_limit );
	}
}

PenciPayWall_GetPaid::getInstance();