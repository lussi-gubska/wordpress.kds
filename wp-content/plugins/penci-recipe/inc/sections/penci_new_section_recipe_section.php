<?php
$options   = [];
$options[] = array(
	'default'  => 'style-1',
	'label'    => 'Recipe Card Layout',
	'id'       => 'penci_recipe_layout',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'choices'  => array(
		'style-1' => 'Style 1',
		'style-2' => 'Style 2',
		'style-3' => 'Style 3',
		'style-4' => 'Style 4',
	)
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show Recipe Meta on the Archive Pages',
	'id'       => 'penci_recipe_archive_meta',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => [ 'serves', 'cooking_time' ],
	'label'    => 'Select the Recipe Meta',
	'id'       => 'penci_recipe_archive_meta_item',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'multiple' => 999,
	'choices'  => array(
		'serves'       => 'Serves',
		'prep_time'    => 'Prep Time',
		'cooking_time' => 'Cooking Time',
	),
	'active_callback' => [
		[
			'setting'  => 'penci_recipe_archive_meta',
			'operator' => '==',
			'value'    => true,
		],
	],
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Featured Image on Recipe Card',
	'id'       => 'penci_recipe_featured_image',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show "Jump to Recipe" button at the top of post',
	'id'       => 'penci_recipe_jump_button',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show "Print Recipe" button at the top of post',
	'id'       => 'penci_recipe_print_btn',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => 'align-center',
	'label'    => 'Align for "Jump to Recipe" & "Print Recipe" at The Top',
	'id'       => 'penci_recipe_btn_align',
	'type'     => 'soledad-fw-select',
	'priority' => 1,
	'choices'  => array(
		'align-center' => esc_html__( 'Center', 'penci-recipe' ),
		'align-left'   => esc_html__( 'Left', 'penci-recipe' ),
		'align-right'  => esc_html__( 'Right', 'penci-recipe' ),
	)
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Custom Ratio Width/Height of Recipe Image for Style 2 & 3. E.g: W/H = 50%, fill 50 below',
	'id'          => 'penci_recipe_ratio_fimage',
	'description' => '',
	'type'        => 'soledad-fw-text',
	'priority'    => 1
);
$options[] = array(
	'default'     => false,
	'sanitize'    => 'penci_recipe_sanitize_checkbox_field',
	'label'       => 'Display Recipe Card Title Overlay on Featured Image',
	'id'          => 'penci_recipe_title_overlay',
	'description' => 'This option apply for Recipe Style 2 & Style 3 only',
	'type'        => 'soledad-fw-toggle',
	'priority'    => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Print Button',
	'id'       => 'penci_recipe_print',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show Pin Button',
	'id'       => 'penci_recipe_pinterest',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Images on Recipe Igredients & Instructions When Users Print Recipe',
	'id'       => 'penci_recipe_hide_image_print',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show Nutrition Facts',
	'id'       => 'penci_recipe_nutrition',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'     => false,
	'sanitize'    => 'penci_recipe_sanitize_checkbox_field',
	'label'       => 'Completely Remove Nutrition Facts on Google Search Results',
	'id'          => 'penci_recipe_remove_nutrition',
	'description' => esc_html__( 'If you remove Nutrition Facts - you can get warning missing it for all your recipe cards in google search console. And we do not recommend you do that.', 'penci-recipe' ),
	'type'        => 'soledad-fw-toggle',
	'priority'    => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Calories',
	'id'       => 'penci_recipe_calories',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Fat',
	'id'       => 'penci_recipe_fat',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Hide Rating',
	'id'       => 'penci_recipe_rating',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);

$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Make Ingredients is Visual Editor on Edit Recipe Screen',
	'id'       => 'penci_recipe_ingredients_visual',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Make Notes is Visual Editor on Edit Recipe Screen',
	'id'       => 'penci_recipe_notes_visual',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Show "Did You Make This Recipe?" Section At The Bottom',
	'id'       => 'penci_recipe_make_trecipe',
	'type'     => 'soledad-fw-toggle',
	'priority' => 1
);

return $options;
