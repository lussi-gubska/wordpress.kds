<?php

namespace PenciFrontendSubmission;

use Exception;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommercePackage {

	private static $instance;

	private $post_package_query;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		$this->setup_hook();
	}

	protected function setup_hook() {
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'product_register_term' ) );
			add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'package_product_data' ) );
			add_action( 'woocommerce_product_data_tabs', array( $this, 'package_data_tabs' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ) );
		} else {
			add_action( 'pre_get_posts', array( $this, 'exclude_post_package' ), 100 );
		}

		add_action( 'wp_loaded', array( $this, 'form_handler' ), 20 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ) );
		add_action( 'woocommerce_add_to_cart', array( $this, 'product_added' ), 10, 2 );
	}

	public function form_handler() {
		if ( isset( $_REQUEST['penci_action'] ) && ! empty( $_REQUEST['penci-frontend-package-nonce'] ) && wp_verify_nonce( $_REQUEST['penci-frontend-package-nonce'], 'penci-frontend-package-nonce' ) ) {
			$action = $_REQUEST['penci_action'];

			switch ( $action ) {
				case 'add-post-package':
					$this->add_package();
					break;
			}
		}
	}

	protected function add_package() {
		$product_id = (int) sanitize_text_field( $_POST['package_id'] );
		$product    = get_post( $product_id );

		try {
			if ( $product->post_type !== 'product' ) {
				throw new Exception( esc_html__( 'Not a Product', 'penci-frontend-submission' ) );
			}

			if ( ! $this->allow_purchase( $product_id ) ) {
				throw new Exception( esc_html__( 'Free product only purchasable once', 'penci-frontend-submission' ) );
			} else {
				WC()->cart->add_to_cart( $product_id );

				if ( $_POST['redirect_checkout'] ) {
					$redirect = wc_get_checkout_url();
					wp_redirect( $redirect );
					exit;
				}
			}
		} catch ( Exception $e ) {
			penci_flash_message( 'message', $e->getMessage(), 'alert-danger' );
		}
	}

	public function product_added( $cart_item_key, $product_id ) {
		if ( $this->allow_purchase( $product_id ) ) {
			foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
				if ( $cart_item['data']->product_type === 'post_package' ) {
					if ( $product_id == $cart_item['product_id'] ) {
						WC()->cart->set_quantity( $key, 1 );
					} else {
						WC()->cart->set_quantity( $key, 0 );
					}
				}
			}
		} else {
			WC()->cart->set_quantity( $cart_item_key, 0 );
		}

		return $cart_item_key;
	}

	public function order_paid( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( get_post_meta( $order_id, 'penci_post_package_processed', true ) ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );

			if ( $product->is_type( 'post_package' ) && $order->customer_user ) {
				$user_id = $order->customer_user;

				$package = array(
					'order_id'   => $order_id,
					'product_id' => $product->get_id(),
					'post_limit' => absint( $product->get_listing_limit() ),
				);

				if ( $this->allow_purchase( $product->get_id(), $user_id ) ) {
					$this->update_user_package( $package, $user_id );
				}
			}
		}
		update_post_meta( $order_id, 'penci_post_package_processed', true );
	}

	protected function allow_purchase( $product_id, $user_id = null ) {
		if ( $user_id === null ) {
			$user_id = get_current_user_id();
		}

		$product     = wc_get_product( $product_id );
		$bought_free = get_user_meta( $user_id, 'bought_free', true );

		if ( $product->is_type( 'post_package' ) ) {
			if ( $bought_free == 1 && $product->get_price() == 0 ) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	protected function update_user_package( $package, $user_id ) {
		$product = wc_get_product( $package['product_id'] );

		// order
		update_user_meta( $user_id, 'order', $package['order_id'] );

		// product
		update_user_meta( $user_id, 'product', $package['product_id'] );

		// add post limit
		$post_limit = get_user_meta( $user_id, 'listing_left', true );
		update_user_meta( $user_id, 'listing_left', ( (int) $package['post_limit'] + (int) $post_limit ) );

		// flag free
		if ( $product->get_price() == 0 ) {
			update_user_meta( $user_id, 'bought_free', true );
		}
	}

	public function product_register_term() {
		if ( ! get_term_by( 'slug', sanitize_title( 'post_package' ), 'product_type' ) ) {
			wp_insert_term(
				'post_package',
				'product_type',
				array( 'description' => 'Post Package' )
			);
		}
	}

	public function product_type_selector( $types ) {
		$types['post_package'] = esc_html__( 'Post Package', 'penci-frontend-submission' );

		return $types;
	}

	public function package_product_data() {
		global $post;
		$post_id = $post->ID;
		?>
        <div class="options_group show_if_post_package">
			<?php
			woocommerce_wp_text_input(
				array(
					'id'                => '_penci_post_limit',
					'label'             => 'Post limit',
					'description'       => 'The number of post a user can submit with this package',
					'value'             => ( $limit = get_post_meta( $post_id, '_penci_post_limit', true ) ) ? $limit : '',
					'placeholder'       => 'Unlimited',
					'type'              => 'number',
					'desc_tip'          => true,
					'custom_attributes' => array(
						'min'  => '',
						'step' => '1',
					),
				)
			);
			?>
            <script type="text/javascript">
                jQuery('.pricing').addClass('show_if_post_package');
            </script>
        </div>
		<?php
	}

	public function package_data_tabs( $product_data_tabs ) {
		if ( empty( $product_data_tabs ) ) {
			return;
		}

		if ( isset( $product_data_tabs['shipping'] ) && isset( $product_data_tabs['shipping']['class'] ) ) {
			$product_data_tabs['shipping']['class'][] = 'hide_if_post_package';
		}
		if ( isset( $product_data_tabs['linked_product'] ) && isset( $product_data_tabs['linked_product']['class'] ) ) {
			$product_data_tabs['linked_product']['class'][] = 'hide_if_post_package';
		}
		if ( isset( $product_data_tabs['attribute'] ) && isset( $product_data_tabs['attribute']['class'] ) ) {
			$product_data_tabs['attribute']['class'][] = 'hide_if_post_package';
		}

		return $product_data_tabs;
	}

	public function save_product_data( $post_id ) {
		$fields = array(
			'_penci_post_limit' => 'int',
		);

		foreach ( $fields as $key => $value ) {
			$value = ! empty( $_POST[ $key ] ) ? $_POST[ $key ] : '';

			switch ( $value ) {
				case 'int':
					$value = absint( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $key, $value );
		}
	}

	public function allow_post_package_query() {
		$this->post_package_query = true;
	}

	protected function _property_query( $query = null ) {
		if ( empty( $query ) ) {
			return false;
		}

		if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] === 'product' ) {
			return true;
		}

		if ( is_post_type_archive( 'product' ) || is_product_taxonomy() ) {
			return true;
		}

		return false;
	}

	public function exclude_post_package( $query ) {

		if ( empty( $this->post_package_query ) && $this->_property_query( $query ) ) {
			$tax_query = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'post_package' ),
				'operator' => 'NOT IN',
			);

			if ( isset( $query->tax_query ) ) {
				$query->tax_query->queries[]    = $tax_query;
				$query->query_vars['tax_query'] = $query->tax_query->queries;
			}
		}

		$this->post_package_query = false;

	}

}
