<?php
$options   = array();
$options[] = array(
	'id'          => 'penci_ai_img_api_key',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Replicate API', 'penci-ai' ),
	'description' => __( 'Fill to this option if you want to use <strong>Open Journey, Stable Diffusion, Text To Pokémon, VAE, Anything and text2image</strong> to generate images. Enter your API key to use the Replicate API. <a target="_blank" href="https://replicate.com/">Get the API key</a>', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_discord_channel_id',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Discord Channel API', 'penci-ai' ),
	'description' => __( 'Fill to this option if you want to use <strong>Midjourney</strong> to generate images. Replaces this value with the Channel ID where the Midjourney Bot is installed. You can get the Channel ID right-clicking on the channel and Copy Channel ID. You can check more in <a href="https://www.youtube.com/watch?v=5ryvvn6ztM4" target="_blank">this video tutorial</a>.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_discord_user_token',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'Discord User Token', 'penci-ai' ),
	'description' => __( 'Fill to this option if you want to use <strong>Midjourney</strong> to generate images. To get your user token, visit <a target="_blank" href="https://discord.com/channels/@me">https://discord.com/channels/@me</a> and open the Network tab inside the Developers Tools. Find between your XHR requests the Authorization header. You can check more in <a href="https://www.youtube.com/watch?v=5ryvvn6ztM4" target="_blank">this video tutorial</a>.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_img_type',
	'transport'   => 'postMessage',
	'default'     => 'dall_e',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'dall_e'                 => 'DALL-E (use ChatGPT API)',
		'midjourney'             => 'Midjourney (use Discord API)',
		'open_journey'           => 'Open Journey',
		'stable_diffusion'       => 'Stable Diffusion',
		'text-to-pokemon'        => 'Text To Pokémon',
		'anything-v3-better-vae' => 'VAE',
		'anything-v4.0'          => 'Anything',
		'text2image'             => 'text2image',
	],
	'label'       => esc_html__( 'AI Engine', 'penci-ai' ),
	'description' => __( 'Choose the Image AI Engine you want to use to generate featured image', 'penci-ai' ),
);

return $options;