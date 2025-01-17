<?php
$options   = [];
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default number calories for all Recipe Cards',
	'id'          => 'penci_recipe_dfcalories',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default number fat for all Recipe Cards',
	'id'          => 'penci_recipe_dffat',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default recipe cuisine for all Recipe Cards',
	'id'          => 'penci_recipe_dfcuisine',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default keywords for all Recipe Cards',
	'id'          => 'penci_recipe_dfkeywords',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default Youtube video ID for all Recipe Cards',
	'id'          => 'penci_recipe_dfvideoid',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default Youtube video title for all Recipe Cards',
	'id'          => 'penci_recipe_dfvideotitle',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default Youtube video duration for all Recipe Cards',
	'id'          => 'penci_recipe_dfvideoduration',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default Youtube video upload date for all Recipe Cards',
	'id'          => 'penci_recipe_dfvideodate',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Set default Youtube video description for all Recipe Cards',
	'id'          => 'penci_recipe_dfvideodes',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);

return $options;
