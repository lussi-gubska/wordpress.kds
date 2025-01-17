<?php
add_action( 'penci_amp_post_template_head', 'penci_recipe_json_schema' );
add_action( 'wp_head', 'penci_recipe_json_schema' );

if ( ! function_exists( 'penci_recipe_json_schema' ) ) {
	function penci_recipe_json_schema() {
		global $post;
		if ( is_singular() ) {

			$recipe_id = $id = get_the_ID();
			if ( ! empty( $id ) && is_numeric( $id ) ) {
				$recipe_id = $id;
			}

			// Get recipe meta
			$recipe_title = get_post_meta( $recipe_id, 'penci_recipe_title', true );

			$image_sizes = array(
				get_the_post_thumbnail_url( get_the_ID(), 'penci-recipe-1200x1200' ),
				get_the_post_thumbnail_url( get_the_ID(), 'penci-recipe-1200x900' ),
				get_the_post_thumbnail_url( get_the_ID(), 'penci-recipe-1200x675' ),
			);

			if ( $recipe_title ) {
				$recipe_servings     = get_post_meta( $recipe_id, 'penci_recipe_servings', true );
				$recipe_cooktime     = get_post_meta( $recipe_id, 'penci_recipe_cooktime', true );
				$recipe_cooktime_fm  = get_post_meta( $recipe_id, 'penci_recipe_cooktime_format', true );
				$recipe_totaltime_fm = get_post_meta( $recipe_id, 'penci_recipe_totaltime_format', true ) ? get_post_meta( $recipe_id, 'penci_recipe_totaltime_format', true ) : $recipe_cooktime_fm;
				$recipe_preptime     = get_post_meta( $recipe_id, 'penci_recipe_preptime', true );
				$recipe_preptime_fm  = get_post_meta( $recipe_id, 'penci_recipe_preptime_format', true );
				$recipe_ingredients  = get_post_meta( $recipe_id, 'penci_recipe_ingredients', true );
				$recipe_instructions = get_post_meta( $recipe_id, 'penci_recipe_instructions', true );
				$recipe_note         = get_post_meta( $recipe_id, 'penci_recipe_note', true );

				$recipe_calories      = get_post_meta( $recipe_id, 'penci_recipe_calories', true ) ? get_post_meta( $recipe_id, 'penci_recipe_calories', true ) : get_theme_mod( 'penci_recipe_dfcalories' );
				$recipe_fat           = get_post_meta( $recipe_id, 'penci_recipe_fat', true ) ? get_post_meta( $recipe_id, 'penci_recipe_fat', true ) : get_theme_mod( 'penci_recipe_dffat' );
				$recipe_keywords      = get_post_meta( $recipe_id, 'penci_recipe_keywords', true ) ? get_post_meta( $recipe_id, 'penci_recipe_keywords', true ) : get_theme_mod( 'penci_recipe_dfkeywords' );
				$recipe_cuisine       = get_post_meta( $recipe_id, 'penci_recipe_cuisine', true ) ? get_post_meta( $recipe_id, 'penci_recipe_cuisine', true ) : get_theme_mod( 'penci_recipe_dfcuisine' );
				$recipe_videoid       = get_post_meta( $recipe_id, 'penci_recipe_videoid', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videoid', true ) : get_theme_mod( 'penci_recipe_dfvideoid' );
				$recipe_videotitle    = get_post_meta( $recipe_id, 'penci_recipe_videotitle', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videotitle', true ) : get_theme_mod( 'penci_recipe_dfvideotitle' );
				$recipe_videoduration = get_post_meta( $recipe_id, 'penci_recipe_videoduration', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videoduration', true ) : get_theme_mod( 'penci_recipe_dfvideoduration' );
				$recipe_videodate     = get_post_meta( $recipe_id, 'penci_recipe_videodate', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videodate', true ) : get_theme_mod( 'penci_recipe_dfvideodate' );
				$recipe_videodes      = get_post_meta( $recipe_id, 'penci_recipe_videodes', true ) ? get_post_meta( $recipe_id, 'penci_recipe_videodes', true ) : get_theme_mod( 'penci_recipe_dfvideodes' );

				$recipe_calories = $recipe_calories ? $recipe_calories : '200';
				$recipe_fat      = $recipe_fat ? $recipe_fat : '20 grams';

				if ( ! metadata_exists( 'post', $recipe_id, 'penci_recipe_rate_total' ) ) {
					add_post_meta( $recipe_id, 'penci_recipe_rate_total', '5' );
				}
				if ( ! metadata_exists( 'post', $recipe_id, 'penci_recipe_rate_people' ) ) {
					add_post_meta( $recipe_id, 'penci_recipe_rate_people', '1' );
				}

				$rate_total  = get_post_meta( $recipe_id, 'penci_recipe_rate_total', true );
				$rate_people = get_post_meta( $recipe_id, 'penci_recipe_rate_people', true );

				// Turn ingredients into an array
				$recipe_ingredients_array = array();
				if ( $recipe_ingredients ) :
					$recipe_ingredients_replace = preg_replace( '/<h\d.*?>(.*?)<\/h\d>/', '', $recipe_ingredients );
					$recipe_ingredients_trim    = wp_strip_all_tags( $recipe_ingredients_replace );
					$recipe_ingredients_array   = preg_split( '/\r\n|[\r\n]/', $recipe_ingredients_trim );
				endif;

				// Rate number
				$rate_number = 5;
				if ( $rate_total && $rate_people ) {
					$rate_number = number_format( intval( $rate_total ) / intval( $rate_people ), 1 );
					if ( ( $rate_number * 10 ) > 50 ) {
						$rate_number = '5.0';
					}
				}
				$allow_rate = 1;
				if ( isset( $_COOKIE[ 'recipe_rate_postid_' . $recipe_id ] ) ) {
					$allow_rate = 0;
				}

				$rand = rand( 100, 9999 );
				wp_enqueue_script( 'jquery-recipe-print' );
				$excerpt = has_excerpt() ? get_the_excerpt() : get_the_title();

				$thumb_alt = $thumb_title_html = '';
				if ( has_post_thumbnail( $recipe_id ) && function_exists( 'penci_get_image_alt' ) && function_exists( 'penci_get_image_title' ) ) {
					$thumb_id         = get_post_thumbnail_id( $recipe_id );
					$thumb_alt        = penci_get_image_alt( $thumb_id, $recipe_id );
					$thumb_title_html = penci_get_image_title( $thumb_id );
				}

				$json_recipe_ingredients = $json_recipe_instructions = array();
				if ( $recipe_ingredients ) {
					if ( ! get_theme_mod( 'penci_recipe_ingredients_visual' ) ) {
						foreach ( $recipe_ingredients_array as $ingredient ) {
							if ( $ingredient ) {
								$ing_trim = trim( $ingredient );
								$str_ing  = substr( $ing_trim, 0, 2 );
								$str_echo = substr( $ing_trim, 2 );

								$json_recipe_ingredients[] = $str_ing == '==' ? $str_echo : $ingredient;
							}
						}
					} else {
						$json_recipe_ingredients = penci_recipe_reverse_wpautop( $recipe_ingredients );
					}
				}

				if ( $recipe_instructions ) {
					$json_recipe_instructions = array_values( penci_recipe_reverse_wpautop_ins( $recipe_instructions ) );

				}

				$json_recipe = array(
					'@context'         => 'https://schema.org/',
					'@type'            => 'Recipe',
					'name'             => $recipe_title,
					'image'            => $image_sizes,
					'author'           => array(
						'@type' => 'Person',
						'name'  => penci_recipe_get_post_author(),
					),
					'description'      => $excerpt,
					'datePublished'    => get_the_date(DATE_W3C),
					'recipeCategory'   => penci_recipe_get_post_category( $recipe_id ),
					'keywords'         => $recipe_keywords ? $recipe_keywords : wp_strip_all_tags( get_the_title() ),
					'recipeCuisine'    => $recipe_cuisine ? $recipe_cuisine : 'European',
					'recipeYield'      => $recipe_servings,
					'prepTime'         => 'PT' . $recipe_preptime_fm,
					'totalTime'        => 'PT' . $recipe_totaltime_fm,
					'cookTime'         => 'PT' . $recipe_cooktime_fm,
					'aggregateRating'  => array(
						'@type'       => 'AggregateRating',
						'ratingValue' => $rate_number,
						'reviewCount' => $rate_people,
					),
					'recipeIngredient' => $json_recipe_ingredients,
				);

				if ( $json_recipe_instructions ) {
					$json_recipe['recipeInstructions'] = $json_recipe_instructions;
				}

				if ( ! get_theme_mod( 'penci_recipe_remove_nutrition' ) ) {
					$json_recipe['nutrition'] = array(
						'@type'      => 'NutritionInformation',
						'calories'   => $recipe_calories,
						'fatContent' => $recipe_fat,
					);
				}

				if ( $recipe_videoid && $recipe_videotitle && $recipe_videoduration && $recipe_videodate && $recipe_videodes ) {
					$json_recipe['video'] = array(
						'@type'        => 'VideoObject',
						'name'         => $recipe_videotitle,
						'description'  => $recipe_videodes,
						'thumbnailUrl' => 'https://img.youtube.com/vi/' . $recipe_videoid . '/maxresdefault.jpg',
						'contentUrl'   => 'https://www.youtube.com/watch?v=' . $recipe_videoid,
						'embedUrl'     => 'https://www.youtube.com/embed/' . $recipe_videoid,
						'uploadDate'   => $recipe_videodate,
						'duration'     => 'PT' . $recipe_videoduration,
					);
				}

				?>
				<script type="application/ld+json" class="penci-recipe-schema"><?php echo wp_json_encode( $json_recipe ); ?></script>
				<?php
			}
		}
	}
}

function penci_recipe_reverse_wpautop( $str ) {
	$str = wpautop( do_shortcode( htmlspecialchars_decode( $str ) ) );

	$str = str_replace( array( "\n", '<ol>', '</ol>', '<ul>', '</ul>' ), array( '', '', '', '', '' ), $str );

	// Strip all <p> tags
	$str = str_replace( '<p>', '', $str );
	$str = str_replace( '<li>', '', $str );

	// Replace </p> with a known delimiter
	$str = str_replace( '</p>', '::|::', $str );
	$str = str_replace( '</li>', '::|::', $str );

	$str     = substr( $str, 0, -5 );
	$str_arr = array();

	if ( $str ) {
		$str     = wp_strip_all_tags( $str );
		$str_arr = explode( '::|::', $str );
	}
	return $str_arr;
}

function penci_recipe_reverse_wpautop_ins( $str ) {
	$str = wpautop( do_shortcode( htmlspecialchars_decode( $str ) ) );

	$str = str_replace( array( "\n", '<ol>', '</ol>', '<ul>', '</ul>' ), array( '', '', '', '', '' ), $str );

	// Strip all <p> tags
	$str = str_replace( '<p>', '', $str );
	$str = str_replace( '<li>', '', $str );

	// Replace </p> with a known delimiter
	$str = str_replace( '</p>', '::|::', $str );
	$str = str_replace( '</li>', '::|::', $str );

	$str             = substr( $str, 0, -5 );
	$str_arr         = array();
	$str_arr_content = array();

	if ( $str ) {
		$str     = wp_strip_all_tags( $str );
		$str_arr = explode( '::|::', $str );

		foreach ( $str_arr as $step => $content ) {

			if ( $content ) {
				$str_arr_content[ $step ]['@type'] = 'HowToStep';
				$str_arr_content[ $step ]['text']  = preg_replace( '/<img[^>]+\>/i', '', $content );
			}

		}
	}
	return $str_arr_content;
}

function penci_recipe_get_post_author() {
	global $post;

	$post_author = isset( $post->post_author ) ? $post->post_author : '';
	$author      = get_the_author_meta( 'display_name', $post_author );

	return $author ? $author : 'author';
}
