<?php
$options   = array();
$options[] = array(
	'id'       => 'penci_gviews_json',
	'sanitize' => 'esc_url_raw',
	'type'     => 'soledad-fw-upload',
	'description' => __('You can get the JSON API Token by go to <a target="_blank" href="https://console.cloud.google.com/apis/">Google Cloud Platform</a>. Check more in this <a target="_blank" href="https://docs.appodeal.com/faq-and-troubleshooting/faq/generate-the-json-file-in-google-cloud">article</a>.'),
	'label'    => __( 'Upload Google JSON File', 'penci-ga-views' ),
);
$options[] = array(
	'id'       => 'penci_gviews_profile_id',
	'type'     => 'soledad-fw-text',
	'label'    => __( 'Profile ID', 'penci-ga-views' ),
	'description'    => __( 'Login to Google Analytics then click Admin. It\'s a string similar to "UA-XXXXXXXXX-X". This is your Google Analytics profile ID.', 'penci-ga-views' ),
);
$options[] = array(
	'id'      => 'penci_ga_views_cron_freq',
	'type'    => 'soledad-fw-select',
	'label'   => __( 'Auto Update Time', 'penci-ga-views' ),
	'default' => 'o',
	'choices' => array(
		'o' => __('Disable','penci-ga-views' ),
		'd' => __('Daily','penci-ga-views' ),
		'w' => __('Weekly','penci-ga-views' ),
	),
);

return $options;