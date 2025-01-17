<?php
$mess = array();
// Sidebar
$sidebar_update  = false;
$post_layout_pre = get_post_meta( $post_id, 'meta-select', true );
if ( 'full-post' == $post_layout_pre ) {
	$sidebar_update = update_post_meta( $post_id, 'penci_post_sidebar_display', $post_layout_pre );
}
if ( $sidebar_update ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'post_layout' );
}

// Recipe

$recipe_title       = get_post_meta( $post_id, 'sp-recipe-title', true );
$recipe_servings    = get_post_meta( $post_id, 'sp-recipe-servings', true );
$recipe_time        = get_post_meta( $post_id, 'sp-recipe-time', true );
$recipe_timeprep    = get_post_meta( $post_id, 'sp-recipe-time-prep', true );
$recipe_ingredients = get_post_meta( $post_id, 'sp-recipe-ingredients', true );
$recipe_notes       = get_post_meta( $post_id, 'sp-recipe-notes', true );

if ( $recipe_title || $recipe_servings || $recipe_time || $recipe_timeprep || $recipe_ingredients || $recipe_notes ) {
	update_post_meta( $post_id, 'penci_recipe_title', $recipe_title );
	update_post_meta( $post_id, 'penci_recipe_preptime', $recipe_timeprep );
	update_post_meta( $post_id, 'penci_recipe_cooktime', $recipe_time );
	update_post_meta( $post_id, 'penci_recipe_servings', $recipe_servings );
	update_post_meta( $post_id, 'penci_recipe_instructions', $recipe_notes );
	update_post_meta( $post_id, 'penci_recipe_ingredients', $recipe_ingredients );
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'recipe', timer_stop() );
}


$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );

return ( $mess ? implode( '<br>', $mess ) : '' );