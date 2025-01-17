<?php
$options = [];

$options[] = array(
	'id'          => 'pencibf_double_opt_in',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Require Email Confirmation', 'penci-bookmark-follow' ),
	'description' => esc_html__( 'When enable, Registered users will be sent a confirmation email to subscribe, and will only be added once they confirmed the subscription', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_email_01',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Posts Notification Events', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_post_trigger_notification_post_update',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Email: When post / page updated', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_post_trigger_notification_new_comment',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Email: When new comment added', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_email_03',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Author Notification Events', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_trigger_notification_post_published',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Email: When post / page published', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_trigger_notification_post_update',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Email: When post / page updated', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_author_trigger_notification_new_comment',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'label'     => esc_html__( 'Email: When new comment added', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'    => 'pencibf_header_email_02',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Popup Notifications', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_enable_popup_notify',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-toggle',
	'default'   => true,
	'label'     => esc_html__( 'Enable Popup Notify', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_notify_text_cl',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Popup Text Color', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_notify_bg_cl',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Popup Background Color', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_notify_timeout',
	'transport' => 'postMessage',
	'default'   => '2000',
	'type'      => 'soledad-fw-number',
	'label'     => esc_html__( 'Popup Timeout', 'penci-bookmark-follow' ),
);

$options[] = array(
	'id'        => 'pencibf_popup_position',
	'transport' => 'postMessage',
	'default'   => 'bottom-center',
	'type'      => 'soledad-fw-select',
	'label'     => esc_html__( 'Popup Position', 'penci-bookmark-follow' ),
	'choices'   => [
		'top-left'      => 'Top Left',
		'top-right'     => 'Top Right',
		'top-center'    => 'Top Center',
		'mid-center'    => 'Middle Center',
		'bottom-left'   => 'Bottom Left',
		'bottom-right'  => 'Bottom Right',
		'bottom-center' => 'Bottom Center',
	]
);


return $options;