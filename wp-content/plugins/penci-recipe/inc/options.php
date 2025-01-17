<?php
add_action(
	'init',
	function () {
		require_once dirname( __DIR__ ) . '/inc/helper.php';
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once dirname( __DIR__ ) . '/inc/sections/panel.php';
			require_once dirname( __DIR__ ) . '/inc/sections/settings.php';
			\SoledadFW\RecipeCustomizer::getInstance();
		} else {
			require_once dirname( __DIR__ ) . '/inc/customize.php';
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_recipe_panel'] = array(
			'priority'                           => 30,
			'path'                               => plugin_dir_path( __DIR__ ) . '/inc/sections/',
			'panel'                              => array(
				'title' => esc_html__( 'Recipe Options', 'penci-recipe' ),
				'icon'  => 'fas fa-utensils',
			),
			'penci_new_section_recipe_section'   => array( 'title' => esc_html__( 'General Options', 'penci-recipe' ) ),
			'penci_new_recipe_schema_section'    => array(
				'title' => esc_html__( 'Default Schema Data', 'penci-recipe' ),
				'desc'  => __( 'Because Google will validate your recipes to make it display good on search results. So, you should set a default value for all default fileds below to make Google validate your recipe bestest. If you want to modify any default field, let go to edit your posts and change recipe data there.', 'penci-recipe' ),
			),
			'penci_new_recipe_size_section'      => array( 'title' => esc_html__( 'Font Size', 'penci-recipe' ) ),
			'penci_new_recipe_typo_section'      => array( 'title' => esc_html__( 'Colors', 'penci-recipe' ) ),
			'penci_new_recipe_translate_section' => array( 'title' => esc_html__( 'Text Translation', 'penci-recipe' ) ),
		);
		return $options;
	}
);
