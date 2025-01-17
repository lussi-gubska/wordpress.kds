<div class="options_group show_if_paywall_unlock">
	<?php
	global $post;
	$post_id = $post->ID;

	woocommerce_wp_text_input(
		array(
			'id'                => '_penci_total_unlock',
			'label'             => esc_html__( 'Number of Post Unlock', 'penci-paywall' ),
			'description'       => esc_html__( 'The number of posts that the user could buy/unlock.', 'penci-paywall' ),
			'value'             => get_post_meta( $post_id, '_penci_total_unlock', true ) ? get_post_meta( $post_id, '_penci_total_unlock', true ) : 1,
			'type'              => 'number',
			'desc_tip'          => true,
			'custom_attributes' => array(
				'min'  => 1,
				'step' => 1,
			),
		)
	);

	?>
    <script type="text/javascript">
        (function ($) {
            $('.pricing').addClass('show_if_paywall_unlock')
        })(jQuery)
    </script>
</div>
