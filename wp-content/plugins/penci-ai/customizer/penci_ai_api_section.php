<?php
$options   = array();
$options[] = array(
	'id'          => 'penci_ai_api_key',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'soledad-fw-text',
	'label'       => esc_html__( 'API Key', 'penci-ai' ),
	'description' => __( 'Enter your API key to use the GPT-3 API. <a target="_blank" href="https://beta.openai.com/account/api-keys">Get the API key</a>', 'penci-ai' ),
);

$options[] = array(
	'id'      => 'penci_ai_model',
	'default' => 'gpt-3.5-turbo-instruct',
	'type'    => 'soledad-fw-select',
	'label'   => esc_html__( 'Default AI Model', 'penci-ai' ),
	'description'   => esc_html__( 'You can learn about the differences between the models at <a target="_blank" href="https://platform.openai.com/docs/models">this link</a>.', 'penci-ai' ),
	'choices' => [
		'gpt-3.5-turbo-instruct' => 'gpt-3.5-turbo-instruct',
		'gpt-3.5-turbo-1106'     => 'gpt-3.5-turbo-1106',
		'gpt-3.5-turbo'          => 'gpt-3.5-turbo',
		'gpt-3.5-turbo-0125'     => 'gpt-3.5-turbo-0125',
		'gpt-4-turbo'            => 'gpt-4-turbo',
		'gpt-4-turbo-2024-04-09' => 'gpt-4-turbo-2024-04-09',
		'gpt-4-turbo-preview'    => 'gpt-4-turbo-preview',
		'gpt-4-0125-preview'     => 'gpt-4-0125-preview',
		'gpt-4-1106-preview'     => 'gpt-4-1106-preview',
		'gpt-4'                  => 'gpt-4',
		'gpt-4-0613'             => 'gpt-4-0613',
		'gpt-4o-mini'            => 'gpt-4o-mini',
	]
);

$options[] = array(
	'id'          => 'penci_ai_temperature',
	'transport'   => 'postMessage',
	'default'     => '0.8',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Temperature', 'penci-ai' ),
	'description' => esc_html__( 'Control randomness: Lowering results in less random completions. As the temperature approaches zero, the model will become deterministic and repetitive. If it approaches one, the model will become more randomness and creative.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_max_tokens',
	'transport'   => 'postMessage',
	'default'     => '2000',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Max Tokens', 'penci-ai' ),
	'description' => esc_html__( 'Set the maximum number of tokens to generate in a single request.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_top_p',
	'transport'   => 'postMessage',
	'default'     => '0.5',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Top Prediction (Top-P)', 'penci-ai' ),
	'description' => esc_html__( 'Adjust the Top-P (Top Prediction) parameter to control the diversity of the generated text.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_best_of',
	'transport'   => 'postMessage',
	'default'     => '1',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Best of', 'penci-ai' ),
	'description' => esc_html__( 'Set the number of generated sequences to return.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_frequency_penalty',
	'transport'   => 'postMessage',
	'default'     => '0',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Frequency Penalty', 'penci-ai' ),
	'description' => esc_html__( 'Adjust the frequency penalty to control the frequency of words in the generated text.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_presence_penalty',
	'transport'   => 'postMessage',
	'default'     => '0',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Presence Penalty', 'penci-ai' ),
	'description' => esc_html__( 'Adjust the presence penalty to control the presence of words in the generated text.', 'penci-ai' ),
);

return $options;