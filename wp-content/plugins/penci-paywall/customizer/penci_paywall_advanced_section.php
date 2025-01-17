<?php
$options   = [];
$options[] = array(
	'id'      => 'pencipw_text_subscription_slug',
	'default' => 'my-subscription',
	'type'    => 'soledad-fw-text',
	'label'   => esc_html( 'Subscription URL Slug' ),
);

$options[] = array(
	'id'      => 'pencipw_text_unlocked_slug',
	'default' => 'unlocked-posts',
	'type'    => 'soledad-fw-text',
	'label'   => esc_html( 'Unblocked URL Slug' ),
);

return $options;