<?php

class PenciPWT_Payment_Bonus {

	/**
	 * Registers payment bonus counting type.
	 *
	 * Hooks to ppc_registered_built_in_counting_types - PenciPWT_Counting_Types::register_built_in_counting_types()
	 *
	 * @access    public
	 * @since    1.5
	 */

	static function register_counting_type_payment_bonus() {
		global $pencipwt_global_settings;

		$payment_bonus_counting_type = array(
			'id'                    => 'bonus',
			'label'                 => __( 'Bonus', 'penci-pay-writer' ),
			'apply_to'              => 'post',
			'settings_status_index' => 'enable_payment_bonus',
			'display'               => 'payment',
			'payment_only'          => true,
			'payment_callback'      => array( 'PenciPWT_Payment_Bonus', 'get_post_payment_bonus' )
		);

		$pencipwt_global_settings['counting_types_object']->register_counting_type( $payment_bonus_counting_type );
	}

	/**
	 * Applies post payment bonus to payment total, if available.
	 *
	 * @access  public
	 *
	 * @param   $countings array countings
	 * @param    $post_id int post id
	 *
	 * @return  float WP post
	 * @since   1.5
	 */

	static function get_post_payment_bonus( $countings, $post_id ) {
		global $pencipwt_global_settings;

		$post_bonus = (float) get_post_meta( $post_id, $pencipwt_global_settings['meta_payment_bonus'], true );

		return $post_bonus;
	}
}
