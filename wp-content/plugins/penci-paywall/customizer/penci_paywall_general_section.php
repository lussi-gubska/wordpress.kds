<?php
$options = array();

/* Content Restriction */
$options[] = array(
	'id'    => 'pencipw_header',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Content Restriction', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_block_all',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Block All Posts', 'penci-paywall' ),
	'description' => esc_html__( 'Block all posts for free user. If enabled, this option will override premium option in individual post. If you want to pay to unlock some specific posts, please check the option for it on the edit post screen.', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_limit',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Paragraph Limit', 'penci-paywall' ),
	'description' => esc_html__( 'Choose how much paragraphs to show for non-subscriber users.', 'penci-paywall' ),
	'default'     => '2',
	'choices'     => array(
		'min'  => '1',
		'max'  => '9999',
		'step' => '1',
	),
);

$options[] = array(
	'id'          => 'pencipw_hide_comment',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Hide Comment', 'penci-paywall' ),
	'description' => esc_html__( 'Hide comments for non-subscriber users.', 'penci-paywall' ),
);

/* Guest Option */
$options[] = array(
	'id'    => 'pencipw_header_guest',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Guest Mode', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_guest_mode',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Login is Required to View the Full Content?', 'penci-paywall' ),
	'description' => esc_html__( 'You can also enable this feature via the Post > Categories and Individual Post Settings.', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_guest_mode_header_title',
	'transport' => 'postMessage',
	'default'   => 'Login to view the full content',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Header Title', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_guest_mode_header_description',
	'transport' => 'postMessage',
	'default'   => 'You need to log in to view the full post content.',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Header Description', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_guest_mode_btn_txt',
	'transport' => 'postMessage',
	'default'   => 'Login',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Button Text', 'penci-paywall' ),
);

/* Advertisement Option */
$options[] = array(
	'id'    => 'pencipw_header_advertisement',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Advertisement Option', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_subscribe_ads',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Hide Ads for Subscriber', 'penci-paywall' ),
	'description' => esc_html__( 'Remove all Soledad ads from being displayed for user who has an active subscription.', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_unlock_ads',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Hide Ads for Unlocked Posts', 'penci-paywall' ),
	'description' => esc_html__( 'Remove all Soledad ads from being displayed for posts that has been unlocked.', 'penci-paywall' ),
);

/* Article Buttons */
$options[] = array(
	'id'    => 'pencipw_header_2',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Article Buttons', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_show_header_text',
	'transport'   => 'postMessage',
	'default'     => true,
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Show Header Text', 'penci-paywall' ),
	'description' => esc_html__( 'Show header text above the button.', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_header_title',
	'transport' => 'postMessage',
	'default'   => 'Support authors and subscribe to content',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Header Title', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_header_description',
	'transport' => 'postMessage',
	'default'   => 'This is premium stuff. Subscribe to read the entire article.',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Header Description', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_show_button',
	'transport'   => 'postMessage',
	'default'     => 'both_btn',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Show Button', 'penci-paywall' ),
	'description' => esc_html__( 'Choose which button will be showed on truncated articles', 'penci-paywall' ),
	'choices'     => array(
		'both_btn' => 'Both Buttons',
		'sub_btn'  => 'Subscribe Only',
		'unl_btn'  => 'Unlock Only',
	),
);

$options[] = array(
	'id'          => 'pencipw_subscribe_url',
	'transport'   => 'postMessage',
	'default'     => 'none',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Subscribe Redirect', 'penci-paywall' ),
	'description' => esc_html__( 'Choose where your non-subscriber will be redirected when click subscribe button on article', 'penci-paywall' ),
	'choices'     => pencipw_pages_list(),
);

$options[] = array(
	'id'          => 'pencipw_unlock_url',
	'transport'   => 'postMessage',
	'default'     => 'none',
	'type'        => 'soledad-fw-select',
	'label'       => esc_html__( 'Unlock Redirect', 'penci-paywall' ),
	'description' => esc_html__( 'Choose where your user will be redirected if they dont have unlock quota', 'penci-paywall' ),
	'choices'     => pencipw_pages_list(),
);

$options[] = array(
	'id'        => 'pencipw_subscribe_title',
	'transport' => 'postMessage',
	'default'   => 'Subscribe',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Subscribe Title', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_subscribe_description',
	'transport' => 'postMessage',
	'default'   => 'Gain access to all our Premium contents. <br/><strong>More than 100+ articles.</strong>',
	'type'      => 'soledad-fw-textarea',
	'label'     => esc_html__( 'Subscribe Description', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_subscribe_button_text',
	'transport' => 'postMessage',
	'default'   => 'Subscribe Now',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Subscribe Button Text', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_unlock_title',
	'transport' => 'postMessage',
	'default'   => 'Buy Article',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Unlock Title', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_unlock_description',
	'transport' => 'postMessage',
	'default'   => 'Unlock this article and gain permanent access to read it.',
	'type'      => 'soledad-fw-textarea',
	'label'     => esc_html__( 'Unlock Description', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_unlock_button_text',
	'transport' => 'postMessage',
	'default'   => 'Unlock Now',
	'type'      => 'soledad-fw-text',
	'label'     => esc_html__( 'Unlock Button Text', 'penci-paywall' ),
);

/* Premium Posts Text */
$options[] = array(
	'id'    => 'pencipw_header_3',
	'type'  => 'soledad-fw-header',
	'label' => esc_html__( 'Premium Posts Heading Style', 'penci-paywall' ),
);

$options[] = array(
	'id'          => 'pencipw_premium_heading_text',
	'transport'   => 'postMessage',
	'default'     => 'Premium:',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Premium Post Heading Text', 'penci-paywall' ),
	'description' => esc_html__( 'The text display before the post title.', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_premium_heading_style',
	'transport' => 'postMessage',
	'default'   => 'text',
	'type'      => 'soledad-fw-select',
	'label'     => esc_html__( 'Heading Style', 'penci-paywall' ),
	'choices'   => array(
		'text' => 'Text',
		'btn'  => 'Button',
	),
);

$options[] = array(
	'id'        => 'pencipw_premium_heading_text_cl',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Premium Post Heading Text Color', 'penci-paywall' ),
);

$options[] = array(
	'id'        => 'pencipw_premium_heading_text_bgcl',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-color',
	'label'     => esc_html__( 'Premium Post Heading Text Background Color (Apply for Button Style)', 'penci-paywall' ),
);

return $options;
