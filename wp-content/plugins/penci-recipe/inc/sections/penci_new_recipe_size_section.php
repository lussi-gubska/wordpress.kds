<?php
$options   = [];
$options[] = array(
	'default'     => '14',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Custom Font Size for Buttons "Jump to Recipe" & "Print Recipe" at the Top',
	'id'          => 'penci_recipe_btn_fsize',
	'ids'         => [ 'desktop' => 'penci_recipe_btn_fsize' ],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Recipe Title',
	'id'          => 'penci_recipe_title_fsize',
	'description' => '',
	'type'        => 'soledad-fw-size',
	'ids'         => [
		'desktop' => 'penci_recipe_title_fsize',
		'mobile'  => 'penci_recipe_title_mfsize',
	],
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Recipe Title on Mobile',
	'id'          => 'penci_recipe_title_mfsize',
	'description' => '',
	'type'        => 'soledad-fw-hidden',
	'priority'    => 1
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Custom Font Size for Recipe Card Data Icons',
	'id'          => 'penci_recipe_meta_ifsize',
	'ids'         => [
		'desktop' => 'penci_recipe_meta_ifsize',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'General Font Size for Recipe Card Data',
	'id'          => 'penci_recipe_meta_fsize',
	'description' => '',
	'type'        => 'soledad-fw-size',
	'ids'         => [
		'desktop' => 'penci_recipe_meta_fsize',
	],
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Text: "Serves", "Prep Time", "Cooking Time" on Style 2 & 3',
	'id'          => 'penci_recipe_meta2_fsize',
	'ids'         => [
		'desktop' => 'penci_recipe_meta2_fsize',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Recipe Card Headings: "INGREDIENTS", "INSTRUCTIONS", "NOTES"',
	'id'          => 'penci_recipe_headings',
	'ids'         => [
		'desktop' => 'penci_recipe_headings',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for heading on "DID YOU MAKE THIS RECIPE?" section',
	'id'          => 'penci_recipe_makethis_head',
	'description' => '',
	'type'        => 'soledad-fw-size',
	'ids'         => [
		'desktop' => 'penci_recipe_makethis_head',
	],
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for description on "DID YOU MAKE THIS RECIPE?" section',
	'id'          => 'penci_recipe_makethis_desc',
	'ids'         => [
		'desktop' => 'penci_recipe_makethis_desc',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'  => false,
	'sanitize' => 'penci_recipe_sanitize_checkbox_field',
	'label'    => 'Disable Uppercase on Recipe Card Headings: "INGREDIENTS", "INSTRUCTIONS", "NOTES"',
	'id'       => 'penci_recipe_upperheadings',
	'type'     => 'soledad-fw-toggle',
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Recipe Index Section Title',
	'id'          => 'penci_recipei_title',
	'ids'         => [
		'desktop' => 'penci_recipei_title',
		'mobile'  => 'penci_recipei_mtitle',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Recipe Index Section Title on Mobile',
	'id'          => 'penci_recipei_mtitle',
	'description' => '',
	'type'        => 'soledad-fw-hidden',
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Categories on Recipe Index',
	'id'          => 'penci_recipei_cat',
	'ids'         => [
		'desktop' => 'penci_recipei_cat',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Post Titles on Recipe Index',
	'id'          => 'penci_recipei_ptitle',
	'ids'         => [
		'desktop' => 'penci_recipei_ptitle',
		'mobile'  => 'penci_recipei_pmtitle',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
		'mobile'  => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Post Titles on Recipe Index on Mobile',
	'id'          => 'penci_recipei_pmtitle',
	'description' => '',
	'type'        => 'soledad-fw-hidden',
);
$options[] = array(
	'default'     => '',
	'sanitize'    => 'sanitize_text_field',
	'label'       => 'Font Size for Date on Recipe Index',
	'id'          => 'penci_recipei_date',
	'ids'         => [
		'desktop' => 'penci_recipei_date',
	],
	'description' => '',
	'type'        => 'soledad-fw-size',
	'choices'     => array(
		'desktop' => array(
			'min'  => 1,
			'max'  => 500,
			'step' => 1,
			'edit' => true,
			'unit' => 'px',
		),
	),
);

return $options;
