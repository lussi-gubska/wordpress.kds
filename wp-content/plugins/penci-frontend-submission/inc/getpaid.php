<?php

namespace PenciFrontendSubmission;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Penci_Post_Package_GetPaid {
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
			'frontend_subbmit_metas'
		], 20, 1 );
		add_action( 'getpaid_item_metabox_save', [ $this, 'getpaid_item_metabox_save' ], 10, 2 );
		add_action( 'getpaid_invoice_status_publish', [ $this, 'wpi_custom_on_payment_complete' ], 10, 1 );
		add_action( 'getpaid_invoice_status_wpi-renewal', [ $this, 'wpi_custom_on_payment_complete' ], 10, 1 );
	}

	public function wpi_custom_register_item_type( $item_types ) {
		$item_types['post_package'] = __( 'Post Package', 'penci-frontend-submission' );

		return $item_types;
	}

	public function wpi_custom_on_payment_complete( $invoice ) {

		$user_id = $invoice->get_user_id();
		if ( ! empty( $invoice ) ) {
			$cart_items = $invoice->get_cart_details();

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $cart_item ) {
					$item = ! empty( $cart_item['item_id'] ) ? new \WPInv_Item( $cart_item['item_id'] ) : null;

					if ( ! empty( $item ) && $item->get_type() == 'post_package' ) {
						$item_id = $item->ID;

						$post_limit         = get_post_meta( $item_id, 'post_limit', true );
						$current_post_limit = get_user_meta( $user_id, 'listing_left', true );
						update_user_meta( $user_id, 'listing_left', (int) $post_limit + (int) $current_post_limit );

						$post_types         = get_post_meta( $item_id, 'post_types', true );
						$current_post_types = get_user_meta( $user_id, 'post_types_support', true );

						$current_post_types = explode( ',', $current_post_types );
						$post_types         = explode( ',', $post_types );

						update_user_meta( $user_id, 'post_types_support', implode( ',', array_unique( array_merge( $current_post_types, $post_types ) ) ) );

						if ( $invoice->get_total() == 0 ) {
							update_user_meta( $user_id, 'bought_free', true );
						}

					}
				}
			}
		}

	}

	public function frontend_subbmit_metas( $item ) {
		?>
        <div class="penci_gp_options pc_show_post_package">

            <div class="form-group mb-3 row">
                <label class="col-sm-3 col-form-label"
                       for="_penci_post_limit"><?php echo esc_html__( 'Post Package - Post Limit', 'penci-frontend-submission' ); ?></label>

                <div class="col-sm-8">
                    <div>
                        <input type="number" name="_penci_post_limit" style="width: 100%;" placeholder="0"
                               id="_penci_post_limit"
                               value="<?php echo get_post_meta( get_the_ID(), 'post_limit', true ); ?>">
                    </div>
                </div>

                <div class="col-sm-1 pt-2 pl-0">
                    <span class="wpi-help-tip dashicons dashicons-editor-help" title=""
                          data-original-title="Number of post user can submit."></span>
                </div>

            </div>

        </div>
		<?php
	}

	public function getpaid_item_metabox_save( $post_id, $item ) {
		$post_limit = isset( $_POST['_penci_post_limit'] ) ? getpaid_standardize_amount( $_POST['_penci_post_limit'] ) : null;
		update_post_meta( $post_id, 'post_limit', $post_limit );

	}
}

Penci_Post_Package_GetPaid::getInstance();