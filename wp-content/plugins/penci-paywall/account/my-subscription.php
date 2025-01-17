<?php
$subscribe_status        = get_user_option( 'pencipw_subscribe_status', get_current_user_id() );
$subscription_type       = get_user_option( 'pencipw_subs_type', get_current_user_id() );
$subscription_id         = get_user_option( 'pencipw_' . $subscription_type . '_subs_id', get_current_user_id() );
$date_format             = get_option( 'date_format' );
$expired                 = get_user_option( 'pencipw_expired_date', get_current_user_id() ) ? get_user_option( 'pencipw_expired_date', get_current_user_id() ) : Date( $date_format );
$remaining               = date_diff( new DateTime(), new DateTime( $expired ) );
$current_date            = new DateTime();
$expired_date            = new DateTime( $expired );
$class                   = 'no-sub';
$cancel_subscription_url = '#';

if ( function_exists( 'wcs_get_subscription' ) ) {
	$wcs_order_id = get_user_option( 'pencipw_subscribe_id', get_current_user_id() );
	if ( $wcs_order_id ) {
		$subscription_id   = $wcs_order_id;
		$wcs_order         = wcs_get_subscription( $wcs_order_id );
		$subscription_type = $wcs_order->get_payment_method();
	}
}

if ( class_exists( 'WPInv_Subscription' ) ) {
	$subscription            = new \WPInv_Subscription( $subscription_id );
	$cancel_subscription_url = $subscription->get_cancel_url();
}

if ( $subscribe_status && 'ACTIVE' === $subscribe_status && $current_date <= $expired_date ) {
	$mystatus = '<div class="pencipw_leftbox">
						<span><strong>' . pencipw_text_translation( 'subid' ) . ' : </strong>' . $subscription_id . '</span>
                        <span><strong>' . pencipw_text_translation( 'sub_status' ) . ' : </strong>' . pencipw_text_translation( 'active' ) . '</span>
                        <span><strong>' . pencipw_text_translation( 'remaining_time' ) . ' : </strong>' . $remaining->format( '%a ' . esc_html__( 'days', 'penci-paywall' ) . ' %h ' . esc_html__( 'hours', 'penci-paywall' ) ) . '</span>
                        <span><strong>' . pencipw_text_translation( 'next_due' ) . ' : </strong>' . date_i18n( $date_format, strtotime( $expired ) ) . '</span>
                        <span><strong>' . pencipw_text_translation( 'payment_type' ) . ' : </strong>' . ucwords( $subscription_type ) . '</span>
                    </div>
                    <div class="pencipw_rightbox">
                        <a aria-label="' . pencipw_text_translation( 'cancel_subscription' ) . '" class="subscription" href="' . esc_url( $cancel_subscription_url ) . '">' . pencipw_text_translation( 'cancel_subscription' ) . '</a>
                    </div>';
	$class    = 'has-sub';
} else {
	$mystatus = '<h3>' . pencipw_text_translation( 'no_subscribed' ) . '</h3><div class="btn_wrapper"><a class="button" href="' . ( get_theme_mod( 'pencipw_subscribe_url', 'none' ) === 'none' ? '#' : get_permalink( get_theme_mod( 'pencipw_subscribe_url', 'none' ) ) ) . '">' . pencipw_text_translation( 'subscribed_now' ) . '</a></div>';
}

$output = '<div class="pencipw_manage_status ' . $class . '">
                <div class="pencipw_boxed">
                    ' . $mystatus . '
                </div>
            </div>';

echo $output;
