<?php
$options   = array();
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Jump to Recipe" text',
	'id'       => 'penci_recipe_jump_text',
	'type'     => 'soledad-fw-text',
	'priority' => 7,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Print Recipe" text',
	'id'       => 'penci_recipe_print_btn_text',
	'type'     => 'soledad-fw-text',
	'priority' => 7,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Pin" text',
	'id'       => 'penci_recipe_pin_text',
	'type'     => 'soledad-fw-text',
	'priority' => 7,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Print" text',
	'id'       => 'penci_recipe_print_text',
	'type'     => 'soledad-fw-text',
	'priority' => 7,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Serves" text',
	'id'       => 'penci_recipe_serves_text',
	'type'     => 'soledad-fw-text',
	'priority' => 8,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Prep Time" text',
	'id'       => 'penci_recipe_prep_time_text',
	'type'     => 'soledad-fw-text',
	'priority' => 9,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Cooking Time" text',
	'id'       => 'penci_recipe_cooking_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Nutrition facts:" text',
	'id'       => 'penci_recipe_nutrition_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "calories" text',
	'id'       => 'penci_recipe_calories_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "fat" text',
	'id'       => 'penci_recipe_fat_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "Rating:" text',
	'id'       => 'penci_recipe_rating_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "voted" text',
	'id'       => 'penci_recipe_voted_text',
	'type'     => 'soledad-fw-text',
	'priority' => 10,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "INGREDIENTS" text',
	'id'       => 'penci_recipe_ingredients_text',
	'type'     => 'soledad-fw-text',
	'priority' => 11,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "INSTRUCTIONS" text',
	'id'       => 'penci_recipe_instructions_text',
	'type'     => 'soledad-fw-text',
	'priority' => 12,
);
$options[] = array(
	'default'  => '',
	'sanitize' => 'sanitize_text_field',
	'label'    => 'Custom "NOTES" text',
	'id'       => 'penci_recipe_notes_text',
	'type'     => 'soledad-fw-text',
	'priority' => 13,
);
// $options[] = array(
// 	'default'  => '',
// 	'sanitize' => 'sanitize_text_field',
// 	'label'    => 'Custom "Step" text',
// 	'id'       => 'penci_recipe_step_text',
// 	'type'     => 'soledad-fw-text',
// 	'priority' => 12,
// );
$options[] = array(
	'default'  => 'Did You Make This Recipe?',
	'sanitize' => 'penci_recipe_html_field',
	'label'    => 'Custom "Did You Make This Recipe?" text',
	'id'       => 'penci_recipe_did_you_make',
	'type'     => 'soledad-fw-text',
	'priority' => 13,
);
$options[] = array(
	'default'     => 'How you went with my recipes? Tag me on Instagram at <a href="https://www.instagram.com/">@PenciDesign.</a>',
	'sanitize'    => 'penci_recipe_html_field',
	'label'       => 'Custom Description for "Did You Make This Recipe?" Section',
	'id'          => 'penci_recipe_descmake_recipe',
	'description' => 'You can use Custom HTML here',
	'type'        => 'soledad-fw-textarea',
	'priority'    => 13,
);

return $options;
