<div class="options_group show_if_paywall_subscribe">
	<?php
	global $post;
	$post_id        = $post->ID;
	$custom_options = array(
		'id'          => 'multiple_input',
		'label'       => esc_html__( 'Billing Time', 'penci-paywall' ),
		'description' => esc_html__( 'Choose the billing interval, and period', 'penci-paywall' ),
		'desc_tip'    => true,
		'options'     => array(
			'_pencipw_total'    => array(
				'label'   => '',
				'type'    => 'wp_select',
				'options' => array(
					1 => esc_html__( 'every', 'penci-paywall' ),
					2 => esc_html__( 'every 2nd', 'penci-paywall' ),
					3 => esc_html__( 'every 3rd', 'penci-paywall' ),
					4 => esc_html__( 'every 4th', 'penci-paywall' ),
					5 => esc_html__( 'every 5th', 'penci-paywall' ),
					6 => esc_html__( 'every 6th', 'penci-paywall' ),
				),
			),
			'_pencipw_duration' => array(
				'label'   => '',
				'type'    => 'wp_select',
				'options' => array(
					'day'   => esc_html__( 'Days', 'penci-paywall' ),
					'week'  => esc_html__( 'Weeks', 'penci-paywall' ),
					'month' => esc_html__( 'Months', 'penci-paywall' ),
					'year'  => esc_html__( 'Years', 'penci-paywall' ),
				),
			),
		),
	);

	pencipw_wc_multiple_option( $custom_options );
	?>
    <script type="text/javascript">
        (function ($) {
            $('.pricing').addClass('show_if_paywall_subscribe')
            $('.pricing ._sale_price_field').addClass('hide_if_paywall_subscribe')
        })(jQuery)
    </script>
</div>
