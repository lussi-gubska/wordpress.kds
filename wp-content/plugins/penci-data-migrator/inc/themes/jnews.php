<?php
$mess = array();

// Layout style

$post_layout     = get_post_meta( $post_id, 'jnews_single_post', true );
$post_layout_pre = isset( $post_layout['override']['layout'] ) && $post_layout['override']['layout'] ? $post_layout['override']['layout'] : 'right';
$sidebar         = 'right';
if ( $post_layout_pre == 'left-sidebar' ) {
	$sidebar = 'left';
} elseif ( $post_layout_pre == 'no-sidebar' ) {
	$sidebar = 'no';
}

$single_sidebar_pos = update_post_meta( $post_id, 'penci_post_sidebar_display', $sidebar );
if ( $single_sidebar_pos ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'post_layout' );
}

// post review

if ( get_post_meta( $post_id, 'enable_review', true ) ) {
	update_post_meta( $post_id, 'penci_review_title', get_post_meta( $post_id, 'name', true ) );
	update_post_meta( $post_id, 'penci_review_des', get_post_meta( $post_id, 'summary', true ) );
	update_post_meta( $post_id, 'penci_review_good', implode( "\n", get_post_meta( $post_id, 'good', true ) ) );
	update_post_meta( $post_id, 'penci_review_bad', implode( "\n", get_post_meta( $post_id, 'bad', true ) ) );

	$review_ratings = get_post_meta( $post_id, 'rating', true );
	$i              = 1;
	foreach ( $review_ratings as $rating ) {
		$name  = isset( $rating['rating_text'] ) ? $rating['rating_text'] : '';
		$value = isset( $rating['rating_number'] ) ? $rating['rating_number'] : '';

		if ( $name && $value ) {
			update_post_meta( $post_id, 'penci_review_' . $i, $name );
			update_post_meta( $post_id, 'penci_review_' . $i . '_num', $value );
		}
	}
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'review' );
}


// post recipe
if ( get_post_meta( $post_id, 'enable_food_recipe', true ) ) {
	update_post_meta( $post_id, 'penci_recipe_title', get_post_meta( $post_id, 'food_recipe_title', true ) );
	update_post_meta( $post_id, 'penci_recipe_preptime', get_post_meta( $post_id, 'food_recipe_prep', true ) );
	update_post_meta( $post_id, 'penci_recipe_cooktime', get_post_meta( $post_id, 'food_recipe_time', true ) );
	update_post_meta( $post_id, 'penci_recipe_servings', get_post_meta( $post_id, 'food_recipe_serve', true ) );
	update_post_meta( $post_id, 'penci_recipe_instructions', get_post_meta( $post_id, 'instruction', true ) );
	update_post_meta( $post_id, 'penci_recipe_ingredients', implode( "\n", get_post_meta( $post_id, 'ingredient', true ) ) );
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'recipe', timer_stop() );
}

$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );

return ( $mess ? implode( '<br>', $mess ) : '' );