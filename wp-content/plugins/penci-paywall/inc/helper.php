<?php
if ( ! function_exists( 'pencipw_pages_list' ) ) {
	function pencipw_pages_list() {
		$pages       = get_pages( array( 'post_status' => 'publish' ) );
		$page_option = array( 'none' => esc_html__( '-Select Page-', 'penci-paywall' ) );

		foreach ( $pages as $page ) {
			$page_option[ $page->ID ] = esc_html( $page->post_title );
		}

		return $page_option;
	}
}

if ( ! function_exists( 'pencipw_wc_multiple_option' ) ) {
	/**
	 * Custom wc option.
	 *
	 * @param $args
	 */
	function pencipw_wc_multiple_option( $args ) {
		$args    = wp_parse_args(
			$args,
			array(
				'class'             => 'multiple input',
				'style'             => '',
				'wrapper_class'     => '',
				'name'              => $args['id'],
				'desc_tip'          => false,
				'custom_attributes' => array(),
				'options'           => array(),
			)
		);
		$options = '';
		if ( is_array( $args['options'] ) && ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $id => $field ) {
				$type = $field['type'];
				if ( function_exists( 'woocommerce_' . $type ) ) {
					unset( $field['type'] );
					$field['wrapper_class'] = 'multiple-item';
					$field['id']            = $id;
					ob_start();
					call_user_func( 'woocommerce_' . $type, $field );
					$options .= ob_get_clean();
				}
			}
		}

		$wrapper_attributes = array(
			'class' => $args['wrapper_class'] . " form-field {$args['id']}_field",
		);

		$label_attributes = array(
			'for' => $args['id'],
		);

		$tooltip     = ! empty( $args['description'] ) && false !== $args['desc_tip'] ? $args['description'] : '';
		$description = ! empty( $args['description'] ) && false === $args['desc_tip'] ? $args['description'] : '';
		$options     = ! empty( $options ) ? str_replace( '<p', '<span', $options ) : false;
		$options     = ! empty( $options ) ? str_replace( '</p', '</span', $options ) : false;
		if ( class_exists( 'WooCommerce' ) ) {
			?>
            <p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
                <label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $args['label'] ); ?></label>
				<?php if ( $tooltip ) : ?>
					<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
				<?php endif; ?>
				<?php if ( $options ) : ?>
                    <span class="wrap"><?php echo $options; ?></span>
				<?php endif; ?>
				<?php if ( $description ) : ?>
                    <span class="description"><?php echo wp_kses_post( $description ); ?></span>
				<?php endif; ?>
            </p>
			<?php
		}
	}
}

/**
 * Check subscription product
 */
if ( ! function_exists( 'pencipw_is_subscribe' ) ) {
	function pencipw_is_subscribe( $order_id = null ) {
		$order = null;

		if ( isset( $order_id ) ) {
			$order = new WC_Order( $order_id );
		} elseif ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) ) {
			$order_id = wc_get_order_id_by_order_key( wc_clean( wp_unslash( (int) sanitize_text_field( $_GET['key'] ) ) ) );
			$order    = new WC_Order( $order_id );
		} elseif ( is_wc_endpoint_url( 'add-payment-method' ) ) {
			return false;
		}

		if ( isset( $order ) ) {
			foreach ( $order->get_items() as $item ) {
				$product = wc_get_product( $item['product_id'] );

				if ( $product->is_type( 'paywall_subscribe' ) ) {
					return true;
				}
			}
		} else {
			if ( ! is_null( WC()->cart ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product = wc_get_product( $cart_item['product_id'] );

					if ( $product->is_type( 'paywall_subscribe' ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}
}

/**
 * Check unlock product
 */
if ( ! function_exists( 'pencipw_is_unlock' ) ) {
	function pencipw_is_unlock( $order_id = null ) {
		$order = null;

		if ( isset( $order_id ) ) {
			$order = new WC_Order( $order_id );
		} elseif ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) ) {
			$order_id = wc_get_order_id_by_order_key( wc_clean( wp_unslash( sanitize_text_field( $_GET['key'] ) ) ) );
			$order    = new WC_Order( $order_id );
		} elseif ( is_wc_endpoint_url( 'add-payment-method' ) ) {
			return false;
		}

		if ( isset( $order ) ) {
			foreach ( $order->get_items() as $item ) {
				$product = wc_get_product( $item['product_id'] );

				if ( $product->is_type( 'paywall_unlock' ) ) {
					return true;
				}
			}
		} else {
			if ( ! is_null( WC()->cart ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product = wc_get_product( $cart_item['product_id'] );

					if ( $product->is_type( 'paywall_unlock' ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'pencipw_timezone_list' ) ) {
	/**
	 * Gives a list of timezone.
	 *
	 * @since 1.0.0
	 */
	function pencipw_timezone_list() {
		$structure = array();

		// Do manual UTC offsets.
		$offset_range = array(
			- 12,
			- 11.5,
			- 11,
			- 10.5,
			- 10,
			- 9.5,
			- 9,
			- 8.5,
			- 8,
			- 7.5,
			- 7,
			- 6.5,
			- 6,
			- 5.5,
			- 5,
			- 4.5,
			- 4,
			- 3.5,
			- 3,
			- 2.5,
			- 2,
			- 1.5,
			- 1,
			- 0.5,
			0,
			0.5,
			1,
			1.5,
			2,
			2.5,
			3,
			3.5,
			4,
			4.5,
			5,
			5.5,
			5.75,
			6,
			6.5,
			7,
			7.5,
			8,
			8.5,
			8.75,
			9,
			9.5,
			10,
			10.5,
			11,
			11.5,
			12,
			12.75,
			13,
			13.75,
			14,
		);
		foreach ( $offset_range as $offset ) {
			$offset_value = $offset;

			if ( 0 <= $offset ) {
				$offset_name = '+' . $offset;
			} else {
				$offset_name = (string) $offset;
			}

			$offset_name                            = str_replace(
				array( '.25', '.5', '.75' ),
				array( ':15', ':30', ':45' ),
				$offset_name
			);
			$offset_name                            = 'UTC' . $offset_name;
			$structure[ esc_attr( $offset_value ) ] = esc_html( $offset_name );

		}

		return $structure;
	}
}

if ( ! function_exists( 'pencipw_text_translation_list' ) ) {
	function pencipw_text_translation_list() {
		return [
			'unclock_confirm'     => __( 'Are you sure want to unlock this post?', 'penci-paywall' ),
			'unclock_left'        => __( 'Unlock left', 'penci-paywall' ),
			'yes'                 => __( 'Yes', 'penci-paywall' ),
			'no'                  => __( 'No', 'penci-paywall' ),
			'cancal_confirm'      => __( 'Are you sure want to cancel subscription?', 'penci-paywall' ),
			'penci_paywall_sub'   => __( 'Subscription', 'penci-paywall' ),
			'penci_paywall_unl'   => __( 'Unlocked Posts', 'penci-paywall' ),
			'subid'               => __( 'Subscription ID', 'penci-paywall' ),
			'sub_status'          => __( 'Subscription Status', 'penci-paywall' ),
			'remaining_time'      => __( 'Remaining Time', 'penci-paywall' ),
			'next_due'            => __( 'Next Payment Due', 'penci-paywall' ),
			'payment_type'        => __( 'Payment Type', 'penci-paywall' ),
			'active'              => __( 'ACTIVE', 'penci-paywall' ),
			'cancel_subscription' => __( 'Cancel Subscription', 'penci-paywall' ),
			'no_subscribed'       => __( 'You are not subscribed', 'penci-paywall' ),
			'subscribed_now'      => __( 'Subscribe Now', 'penci-paywall' ),
			'quotas_left'         => __( 'Quotas Left', 'penci-paywall' ),
			'posts_owned'         => __( 'Posts Owned', 'penci-paywall' ),
			'unlocks'             => __( 'unlocks', 'penci-paywall' ),
			'posts'               => __( 'posts', 'penci-paywall' ),
			'posts_collection'    => __( 'Unlocked Posts Collection', 'penci-paywall' ),
			'noposts'             => __( 'No Post Found !', 'penci-paywall' ),
		];
	}
}

if ( ! function_exists( 'pencipw_text_translation' ) ) {
	function pencipw_text_translation( $text ) {
		$translations = pencipw_text_translation_list();
		$option       = 'pencipw_text_' . $text;

		return get_theme_mod( $option ) ? do_shortcode( get_theme_mod( $option ) ) : $translations[ $text ];
	}
}
if ( ! function_exists( 'pencipw_duration_text' ) ) {
	function pencipw_duration_text( $total, $duration ) {
		$text = '';
		switch ( $duration ) {
			case 'day':
			case 'D':
				$text = __( 'days', 'penci-paywall' );
				break;

			case 'week':
			case 'W':
				$text = __( 'weeks', 'penci-paywall' );
				break;

			case 'month':
			case 'M':
				$text = __( 'months', 'penci-paywall' );
				break;

			case 'year':
			case 'Y':
				$text = __( 'years', 'penci-paywall' );
				break;
		}

		return $total . ' ' . $text;
	}
}