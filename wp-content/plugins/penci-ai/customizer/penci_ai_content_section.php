<?php
$options   = array();
$options[] = array(
	'id'          => 'penci_ai_select_post_title',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Select Post Title Before Generate', 'penci-ai' ),
	'description' => __( 'Check this if you want to select the post title first before the content generation.', 'penci-ai' ),
);
$options[] = array(
	'id'        => 'penci_ai_language',
	'transport' => 'postMessage',
	'default'   => 'en',
	'type'      => 'soledad-fw-select',
	'choices'   => penciai_get_languages_list(),
	'label'     => esc_html__( 'Content Languages', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_content_structure',
	'transport'   => 'postMessage',
	'default'     => 'topic-wise',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'topic-wise' => 'Topic Wise',
		'article'    => 'Article',
		'review'     => 'Review',
		'opinion'    => 'Opinion',
		'faq'        => 'FAQ',
	],
	'label'       => esc_html__( 'Content Structure', 'penci-ai' ),
	'description' => __( 'Choose the content type of your blog post which fit your need!', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_number_titles',
	'transport'   => 'postMessage',
	'default'     => '3',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'How many titles you want to show to select?', 'penci-ai' ),
	'description' => __( 'Enter a number of titles you want to show first to select before the content generate. <strong>For the free plan, you are limited to displaying a maximum of three titles per request.</strong>', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_random_post_title',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Randomely generate post title', 'penci-ai' ),
	'description' => __( 'Select this to generate blog post title.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_number_headings',
	'transport'   => 'postMessage',
	'default'     => '5',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'How many topics', 'penci-ai' ),
	'description' => __( 'Enter a number of topics you want to add to your blog post.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_number_headings_tag',
	'transport'   => 'postMessage',
	'default'     => 'h3',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'h1' => 'H1',
		'h2' => 'H2',
		'h3' => 'H3',
		'h4' => 'H4',
		'h5' => 'H5',
		'h6' => 'H6',
	],
	'label'       => esc_html__( 'Topics heading tag', 'penci-ai' ),
	'description' => __( 'Topics will automatically be wrapped by the selected heading tag.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_content_length',
	'transport'   => 'postMessage',
	'default'     => 'long',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'long'   => 'Long',
		'medium' => 'Medium',
		'short'  => 'Short',
	],
	'label'       => esc_html__( 'Content Length', 'penci-ai' ),
	'description' => __( 'Select a content length that fit your need.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_writing_style',
	'transport'   => 'postMessage',
	'default'     => 'normal',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'normal'        => __( 'normal', 'penci-ai' ),
		'business'      => __( 'business', 'penci-ai' ),
		'legal'         => __( 'legal', 'penci-ai' ),
		'technical'     => __( 'technical', 'penci-ai' ),
		'marketing'     => __( 'marketing', 'penci-ai' ),
		'creative'      => __( 'creative', 'penci-ai' ),
		'narrative'     => __( 'narrative', 'penci-ai' ),
		'expository'    => __( 'expository', 'penci-ai' ),
		'reflective'    => __( 'reflective', 'penci-ai' ),
		'persuasive'    => __( 'persuasive', 'penci-ai' ),
		'descriptive'   => __( 'descriptive', 'penci-ai' ),
		'instructional' => __( 'instructional', 'penci-ai' ),
		'news'          => __( 'news', 'penci-ai' ),
		'personal'      => __( 'personal', 'penci-ai' ),
		'travel'        => __( 'travel', 'penci-ai' ),
		'recipe'        => __( 'recipe', 'penci-ai' ),
		'poetic'        => __( 'poetic', 'penci-ai' ),
		'satirical'     => __( 'satirical', 'penci-ai' ),
		'formal'        => __( 'formal', 'penci-ai' ),
		'informal'      => __( 'informal', 'penci-ai' ),
	],
	'label'       => esc_html__( 'Writing Style', 'penci-ai' ),
	'description' => __( 'Choose the writing style of your blog post which fit your need!', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_writing_tone',
	'transport'   => 'postMessage',
	'default'     => 'informative',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'serious'       => __( 'Serious', 'penci-ai' ),
		'thoughtful'    => __( 'Thoughtful', 'penci-ai' ),
		'witty'         => __( 'Witty', 'penci-ai' ),
		'lighthearted'  => __( 'Lighthearted', 'penci-ai' ),
		'hilarious'     => __( 'Hilarious', 'penci-ai' ),
		'soothing'      => __( 'Soothing', 'penci-ai' ),
		'emotional'     => __( 'Emotional', 'penci-ai' ),
		'inspirational' => __( 'Inspirational', 'penci-ai' ),
		'persuasive'    => __( 'Persuasive', 'penci-ai' ),
		'vivid'         => __( 'Vivid', 'penci-ai' ),
		'imaginative'   => __( 'Imaginative', 'penci-ai' ),
		'musical'       => __( 'Musical', 'penci-ai' ),
		'rhythmical'    => __( 'Rhythmical', 'penci-ai' ),
		'humorous'      => __( 'Humorous', 'penci-ai' ),
		'critical'      => __( 'Critical', 'penci-ai' ),
		'clear'         => __( 'Clear', 'penci-ai' ),
		'neutral'       => __( 'Neutral', 'penci-ai' ),
		'objective'     => __( 'Objective', 'penci-ai' ),
		'biased'        => __( 'Biased', 'penci-ai' ),
		'passionate'    => __( 'Passionate', 'penci-ai' ),
		'argumentative' => __( 'Argumentative', 'penci-ai' ),
		'reflective'    => __( 'Reflective', 'penci-ai' ),
		'helpful'       => __( 'Helpful', 'penci-ai' ),
		'connective'    => __( 'Connective', 'penci-ai' ),
		'assertive'     => __( 'Assertive', 'penci-ai' ),
		'energetic'     => __( 'Energetic', 'penci-ai' ),
		'relaxed'       => __( 'Relaxed', 'penci-ai' ),
		'polite'        => __( 'Polite', 'penci-ai' ),
		'clever'        => __( 'Clever', 'penci-ai' ),
		'funny'         => __( 'Funny', 'penci-ai' ),
		'amusing'       => __( 'Amusing', 'penci-ai' ),
		'comforting'    => __( 'Comforting', 'penci-ai' ),
	],
	'label'       => esc_html__( 'Writing Tone', 'penci-ai' ),
	'description' => __( 'Choose the writing tone of your blog post which fit your need!', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_add_excerpt',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Add excerpt', 'penci-ai' ),
	'description' => __( 'Add conclusion end of the topics.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_excerpt_words',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-number',
	'label'       => esc_html__( 'Number of excerpt words', 'penci-ai' ),
	'description' => __( 'Enter a number of words to get the excerpt content withing the number of words.', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_add_intro',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Add Introduction', 'penci-ai' ),
	'description' => __( 'Add an introduction beginning of the topics.', 'penci-ai' ),
);
$options[] = array(
	'id'        => 'penci_ai_intro_heading',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => 'Introduction',
	'label'     => esc_html__( 'Introduction Heading Title', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_add_conclusion',
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Add Conclusion', 'penci-ai' ),
	'description' => __( 'Add an conclusion end of the topics.', 'penci-ai' ),
);
$options[] = array(
	'id'        => 'penci_ai_conclusion_heading',
	'transport' => 'postMessage',
	'type'      => 'soledad-fw-text',
	'default'   => 'Conclusion',
	'label'     => esc_html__( 'Conclusion Heading Title', 'penci-ai' ),
);

$options[] = array(
	'id'          => 'penci_ai_conclusion_size',
	'transport'   => 'postMessage',
	'default'     => 'short',
	'type'        => 'soledad-fw-select',
	'choices'     => [
		'long'   => 'Long',
		'medium' => 'Medium',
		'short'  => 'Short',
	],
	'label'       => esc_html__( 'Conclusion text size', 'penci-ai' ),
	'description' => __( ' Select a size to set how long your conclusion size is needed.', 'penci-ai' ),
);
$options[] = array(
	'id'          => 'penci_ai_auto_featured_image',
	'default'     => true,
	'transport'   => 'postMessage',
	'type'        => 'soledad-fw-toggle',
	'label'       => esc_html__( 'Auto Generate Featured Image', 'penci-ai' ),
	'description' => __( 'Select this to auto-generate the thumbnail image. It will generate from your main prompt.', 'penci-ai' ),
);

$options[] = array(
	'label'    => __( 'Image Size', 'penci-ai' ),
	'id'       => 'penci_ai_featured_image_size',
	'type'     => 'soledad-fw-ajax-select',
	'choices'  => [
		'thumbnail' => 'Thumbnail (256x256px)',
		'medium'    => 'Medium (512x512px)',
		'large'     => 'Large (1024x1024px)',
	],
	'default'  => 'large',
	'sanitize' => 'penci_sanitize_choices_field'
);

$options[] = array(
	'id'        => 'penci_ai_img_experiments',
	'transport' => 'postMessage',
	'default'   => [ 'realistic', '4k', 'high_resolution', 'trending_in_artstation', 'artstation_three' ],
	'type'      => 'soledad-fw-select',
	'multiple'  => 999,
	'choices'   => [
		'realistic'              => __( 'Realistic', 'penci-ai' ),
		'3d_render'              => __( '3D render', 'penci-ai' ),
		'4k'                     => __( '4K', 'penci-ai' ),
		'8k'                     => __( '8K', 'penci-ai' ),
		'amazing_art'            => __( 'Amazing art', 'penci-ai' ),
		'high_resolution'        => __( 'High resolution', 'penci-ai' ),
		'trending_in_artstation' => __( 'Trending in artstation', 'penci-ai' ),
		'artstation_three'       => __( 'Artstation 3', 'penci-ai' ),
		'oil_painting'           => __( 'Oil painting', 'penci-ai' ),
		'digital_painting'       => __( 'Digital painting', 'penci-ai' ),
		'professional'           => __( 'Professional', 'penci-ai' ),
		'expert'                 => __( 'Expert', 'penci-ai' ),
		'stunning'               => __( 'Stunning', 'penci-ai' ),
		'creative'               => __( 'Creative', 'penci-ai' ),
		'popular'                => __( 'Popular', 'penci-ai' ),
		'inspired'               => __( 'Inspired', 'penci-ai' ),
		'surreal'                => __( 'Surreal', 'penci-ai' ),
		'abstract'               => __( 'Abstract', 'penci-ai' ),
		'fantasy'                => __( 'Fantasy', 'penci-ai' ),
		'pop'                    => __( 'Pop', 'penci-ai' ),
		'art'                    => __( 'Art', 'penci-ai' ),
		'neo-expressionist'      => __( 'Neo-expressionist', 'penci-ai' ),
		'vector'                 => __( 'Vector', 'penci-ai' ),
		'neon'                   => __( 'Neon', 'penci-ai' ),
		'landscape'              => __( 'Landscape', 'penci-ai' ),
		'portrait'               => __( 'Portrait', 'penci-ai' ),
		'iconic'                 => __( 'Iconic', 'penci-ai' ),
	],
	'label'     => esc_html__( 'Image Experiments', 'penci-ai' ),
);

return $options;