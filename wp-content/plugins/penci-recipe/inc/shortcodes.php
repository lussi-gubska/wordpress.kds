<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Don't run the shortcode inside dashboard */
if ( is_admin() ) {
	return;
}

function penci_recipe_get_post_category( $id ) {
	$cat_return   = 'Uncategorized';
	$the_category = get_the_category( $id );

	if ( ! empty( $the_category ) ) {
		$cat_return = $the_category[0]->name;
	}

	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term               = get_term( $wpseo_primary_term );
		if ( ! is_wp_error( $term ) ) {
			$cat_return = $term->name;
		}
	}

	return $cat_return;
}

function penci_recipe_control_actions() {
	if ( get_theme_mod( 'penci_recipe_jump_button' ) || get_theme_mod( 'penci_recipe_print_btn' ) ) {
		$align = get_theme_mod( 'penci_recipe_btn_align' ) ? get_theme_mod( 'penci_recipe_btn_align' ) : 'align-center';
		echo '<div class="penci-recipe-action-buttons buttons-' . $align . '">';
		if ( get_theme_mod( 'penci_recipe_jump_button' ) ):
			$jump_text = get_theme_mod( 'penci_recipe_jump_text' ) ? do_shortcode( get_theme_mod( 'penci_recipe_jump_text' ) ) : esc_html__( 'Jump to Recipe', 'penci-recipe' );
			?>
            <a class="penci-recipe-button penci-jump-recipe"
               href="#penci-recipe-card"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
					penci_fawesome_icon( 'fas fa-arrow-down' );
				} else {
					echo '<i class="fa fa-angle-down"></i>';
				}
				echo $jump_text; ?></a>
		<?php
		endif;
		if ( get_theme_mod( 'penci_recipe_print_btn' ) ):
			$printbtn_text = get_theme_mod( 'penci_recipe_print_btn_text' ) ? do_shortcode( get_theme_mod( 'penci_recipe_print_btn_text' ) ) : esc_html__( 'Print Recipe', 'penci-recipe' );
			?>
            <a class="penci-recipe-button penci-recipe-print-btn penci-printbutton-recipe" href="#"
               data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
					penci_fawesome_icon( 'fas fa-print' );
				} else {
					echo '<i class="fa fa-print"></i>';
				}
				echo $printbtn_text; ?></a>
		<?php
		endif;
		echo '</div>';
	}
}

add_action( 'penci_recipes_action_hook', 'penci_recipe_control_actions' );

/**
 * Penci Recipe Shortcode
 * Use penci_recipe to display the recipe on single a post
 */
function penci_recipe_shortcode_function( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id'    => '',
		'style' => '',
	), $atts ) );

	$recipe_id = get_the_ID();
	if ( ! empty( $id ) && is_numeric( $id ) ) {
		$recipe_id = $id;
	}

	// Get recipe meta
	$recipe_style = get_theme_mod( 'penci_recipe_layout' ) ? get_theme_mod( 'penci_recipe_layout' ) : 'style-1';
	if ( $style && in_array( $style, array( 'style-1', 'style-2', 'style-3', 'style-4' ) ) ) {
		$recipe_style = $style;
	}
	$recipe_class = $recipe_style;
	if ( $recipe_style == 'style-3' ) {
		$recipe_class = 'style-2 precipe-style-3';
	}
	$recipe_title        = get_post_meta( $recipe_id, 'penci_recipe_title', true );
	$recipe_servings     = get_post_meta( $recipe_id, 'penci_recipe_servings', true );
	$recipe_cooktime     = get_post_meta( $recipe_id, 'penci_recipe_cooktime', true );
	$recipe_cooktime_fm  = get_post_meta( $recipe_id, 'penci_recipe_cooktime_format', true );
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
	$recipe_ingredients_array = '';
	if ( $recipe_ingredients ):
		$recipe_ingredients_trim  = wp_strip_all_tags( $recipe_ingredients );
		$recipe_ingredients_array = preg_split( '/\r\n|[\r\n]/', $recipe_ingredients_trim );
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
	$flag_title = false;
	$flag_style = false;
	if ( in_array( $recipe_style, array(
			'style-2',
			'style-3'
		) ) && get_theme_mod( 'penci_recipe_title_overlay' ) && ! get_theme_mod( 'penci_recipe_featured_image' ) ): $flag_title = true; endif;
	if ( in_array( $recipe_style, array( 'style-2', 'style-3' ) ) ): $flag_style = true; endif;
	$recipe_url = get_the_permalink( $recipe_id );
	$pin_url    = 'https://www.pinterest.com/pin/create/button/?url=' . urlencode( $recipe_url );
	if ( has_post_thumbnail( $recipe_id ) ) {
		$pin_url .= '&media=' . urlencode( get_the_post_thumbnail_url( $recipe_id, 'penci-full-thumb' ) );
	}
	if ( $recipe_title ) {
		$pin_url .= '&description=' . urlencode( $recipe_title );
	}
	ob_start();
	?>
    <div id="penci-recipe-card"></div>
    <div class="wrapper-penci-recipe<?php if ( get_theme_mod( 'penci_recipe_make_trecipe' ) ): echo ' showing-tagged-recipe'; endif; ?>">
        <div class="penci-recipe<?php if ( ! has_post_thumbnail( $recipe_id ) || get_theme_mod( 'penci_recipe_featured_image' ) ): echo ' penci-recipe-hide-featured'; endif; ?><?php if ( get_theme_mod( 'penci_recipe_hide_image_print' ) ): echo ' penci-hide-images-print'; endif; ?><?php if ( $flag_title == true ) {
			echo ' penci-recipe-overtitle';
		} else {
			echo ' penci-recipe-novertitle';
		} ?> precipe-<?php echo $recipe_class; ?>">
            <div class="penci-recipe-heading">

				<?php if ( has_post_thumbnail( $recipe_id ) && ! get_theme_mod( 'penci_recipe_featured_image' ) ): ?>
					<?php $sthumb = 'penci-thumb-square';
					if ( $flag_style == true ) {
						$sthumb = 'penci-full-thumb';
					}
					?>
                    <div class="penci-recipe-thumb">
						<?php if ( $recipe_style == 'style-4' ): echo '<span class="recipe-thumb-top">'; endif; ?>
                        <img src="<?php echo get_the_post_thumbnail_url( $recipe_id, $sthumb ); ?>"
                             alt="<?php echo $thumb_alt; ?>"<?php echo $thumb_title_html; ?> />
						<?php if ( $recipe_style == 'style-4' ): echo '</span>'; endif; ?>
						<?php if ( $recipe_title && $recipe_style == 'style-2' && get_theme_mod( 'penci_recipe_title_overlay' ) ) { ?>
                            <h2 class="recipe-title-overlay"><?php echo $recipe_title; ?></h2>
						<?php } ?>
						<?php if ( $flag_style == true && ( ! get_theme_mod( 'penci_recipe_print' ) || get_theme_mod( 'penci_recipe_pinterest' ) ) ) { ?>
                            <div class="wrapper-buttons-overlay">
								<?php if ( get_theme_mod( 'penci_recipe_pinterest' ) ) : ?>
                                    <a href="<?php echo $pin_url; ?>" target="_blank" class="penci-recipe-pin"
                                       data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
											penci_fawesome_icon( 'fas fa-pinterest-p' );
										} else {
											echo '<i class="fa fa-pinterest-p"></i>';
										} ?><?php if ( get_theme_mod( 'penci_recipe_pin_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_pin_text' ) );
										} else {
											esc_html_e( 'Pin', 'penci-recipe' );
										} ?></a>
								<?php endif; ?>
								<?php if ( ! get_theme_mod( 'penci_recipe_print' ) ) : ?>
                                    <a href="#" class="penci-recipe-print-btn penci-recipe-print-overlay"
                                       data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
											penci_fawesome_icon( 'fas fa-print' );
										} else {
											echo '<i class="fa fa-print"></i>';
										} ?><?php if ( get_theme_mod( 'penci_recipe_print_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_print_text' ) );
										} else {
											esc_html_e( 'Print', 'penci-recipe' );
										} ?></a>
								<?php endif; ?>
								<?php if ( $recipe_title && $recipe_style == 'style-3' && get_theme_mod( 'penci_recipe_title_overlay' ) ) { ?>
                                    <h2 class="recipe-title-overlay"><?php echo $recipe_title; ?></h2>
								<?php } ?>
                            </div>
						<?php } ?>
						<?php if ( $recipe_title && $flag_title == true ) { ?>
                            <div class="recipe-header-overlay"></div>
						<?php } ?>
                    </div>
				<?php endif; ?>

                <div class="penci-recipe-metades">
					<?php if ( $recipe_title && $flag_title != true ) : ?>
                        <h2 class="recipe-title-nooverlay"><?php echo $recipe_title; ?></h2>
					<?php endif; ?>

					<?php if ( ! get_theme_mod( 'penci_recipe_print' ) && $recipe_style == 'style-1' ) : ?>
                        <a href="#" class="penci-recipe-print-btn penci-recipe-print"
                           data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
								penci_fawesome_icon( 'fas fa-print' );
							} else {
								echo '<i class="fa fa-print"></i>';
							} ?><?php if ( get_theme_mod( 'penci_recipe_print_text' ) ) {
								echo do_shortcode( get_theme_mod( 'penci_recipe_print_text' ) );
							} else {
								esc_html_e( 'Print', 'penci-recipe' );
							} ?></a>
					<?php endif; ?>

					<?php if ( ! get_theme_mod( 'penci_recipe_rating' ) && $flag_style == true ) : ?>
                        <div class="penci-recipe-rating penci-recipe-review">
							<span class="penci-rate-text">
								<?php if ( get_theme_mod( 'penci_recipe_rating_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_rating_text' ) ) . ' ';
								} else {
									esc_html_e( 'Rating: ', 'penci-recipe' );
								} ?>
								<span class="penci-rate-number"><?php echo $rate_number; ?></span>/5
							</span>
                            <div class="penci_rateyo" id="penci_rateyo" data-allow="<?php esc_attr_e( $allow_rate ) ?>"
                                 data-rate="<?php esc_attr_e( $rate_number ); ?>"
                                 data-postid="<?php esc_attr_e( $recipe_id ); ?>"
                                 data-people="<?php echo $rate_people; ?>"
                                 data-total="<?php echo $rate_total; ?>"></div>
                            <span class="penci-numbers-rate">( <span
                                        class="penci-number-people"><?php echo $rate_people; ?></span> <?php if ( get_theme_mod( 'penci_recipe_voted_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_voted_text' ) );
								} else {
									esc_html_e( 'voted', 'penci-recipe' );
								} ?> )</span>
                        </div>
					<?php endif; ?>

					<?php if ( $recipe_servings || $recipe_cooktime || $recipe_preptime ) : ?>
                        <div class="penci-recipe-meta">
							<?php if ( $recipe_servings ) : ?><span>
                                <i class="penci-ficon ficon-hot-food"></i> <span
                                        class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_serves_text' ) ) {
										echo do_shortcode( get_theme_mod( 'penci_recipe_serves_text' ) );
									} else {
										esc_html_e( 'Serves', 'penci-recipe' );
									} ?>:</span> <span class="servings"><?php echo $recipe_servings; ?></span>
                                </span>
							<?php endif; ?>
							<?php if ( $recipe_preptime ) : ?>
                                <span>
								<i class="penci-ficon ficon-clock"></i> <span
                                            class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_prep_time_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_prep_time_text' ) );
										} else {
											esc_html_e( 'Prep Time', 'penci-recipe' );
										} ?>:</span> <time <?php if ( $recipe_preptime_fm ): echo 'datetime="PT' . $recipe_preptime_fm . '" '; endif; ?>><?php echo $recipe_preptime; ?></time>
								</span>
							<?php endif; ?>
							<?php if ( $recipe_cooktime ) : ?>
                                <span>
								<i class="penci-ficon ficon-cooking"></i> <span
                                            class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_cooking_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_cooking_text' ) );
										} else {
											esc_html_e( 'Cooking Time', 'penci-recipe' );
										} ?>:</span> <time <?php if ( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm . '" '; endif; ?>><?php echo $recipe_cooktime; ?></time>
								<time class="penci-hide-tagupdated" <?php if ( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm . '" '; endif; ?>><?php echo $recipe_cooktime; ?></time>
								</span>
							<?php endif; ?>
							<?php if ( ! get_theme_mod( 'penci_recipe_remove_nutrition' ) && $flag_style == true ): ?>
                                <span class="penci-nutrition-meta<?php if ( get_theme_mod( 'penci_recipe_nutrition' ) ): echo ' penci-show-nutrition'; endif; ?>">
									<i class="penci-ficon ficon-fire"></i>
									<span class="remeta-item nutrition-lable"><?php if ( get_theme_mod( 'penci_recipe_nutrition_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_nutrition_text' ) );
										} else {
											esc_html_e( 'Nutrition facts:', 'penci-recipe' );
										} ?></span>
									<span class="nutrition-item<?php if ( get_theme_mod( 'penci_recipe_calories' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_calories . ' ';
										if ( get_theme_mod( 'penci_recipe_calories_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_calories_text' ) );
										} else {
											esc_html_e( 'calories', 'penci-recipe' );
										} ?></span>
									<span class="nutrition-item<?php if ( get_theme_mod( 'penci_recipe_fat' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_fat . ' ';
										if ( get_theme_mod( 'penci_recipe_fat_text' ) ) {
											echo do_shortcode( get_theme_mod( 'penci_recipe_fat_text' ) );
										} else {
											esc_html_e( 'fat', 'penci-recipe' );
										} ?></span>
								</span>
							<?php endif; ?>
                        </div>
					<?php endif; ?>

					<?php if ( ! get_theme_mod( 'penci_recipe_remove_nutrition' ) && $flag_style != true ) : ?>
                        <div class="penci-recipe-rating penci-nutrition<?php if ( get_theme_mod( 'penci_recipe_nutrition' ) ): echo ' penci-show-nutrition'; endif; ?>">
                            <i class="penci-ficon ficon-fire"></i><span
                                    class="nutrition-lable"><?php if ( get_theme_mod( 'penci_recipe_nutrition_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_nutrition_text' ) );
								} else {
									esc_html_e( 'Nutrition facts:', 'penci-recipe' );
								} ?></span>
                            <span class="nutrition-item<?php if ( get_theme_mod( 'penci_recipe_calories' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_calories . ' ';
								if ( get_theme_mod( 'penci_recipe_calories_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_calories_text' ) );
								} else {
									esc_html_e( 'calories', 'penci-recipe' );
								} ?></span>
                            <span class="nutrition-item<?php if ( get_theme_mod( 'penci_recipe_fat' ) ): echo ' penci-hide-nutrition'; endif; ?>"><?php echo $recipe_fat . ' ';
								if ( get_theme_mod( 'penci_recipe_fat_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_fat_text' ) );
								} else {
									esc_html_e( 'fat', 'penci-recipe' );
								} ?></span>
                        </div>
					<?php endif; ?>

					<?php if ( ! get_theme_mod( 'penci_recipe_rating' ) && $flag_style != true ) : ?>
                        <div class="penci-recipe-rating penci-recipe-review">
							<span class="penci-rate-text">
								<?php if ( get_theme_mod( 'penci_recipe_rating_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_rating_text' ) ) . ' ';
								} else {
									esc_html_e( 'Rating: ', 'penci-recipe' );
								} ?>
								<span class="penci-rate-number"><?php echo $rate_number; ?></span>/5
							</span>
                            <div class="penci_rateyo" id="penci_rateyo" data-allow="<?php esc_attr_e( $allow_rate ) ?>"
                                 data-rate="<?php esc_attr_e( $rate_number ); ?>"
                                 data-postid="<?php esc_attr_e( $recipe_id ); ?>"
                                 data-people="<?php echo $rate_people; ?>"
                                 data-total="<?php echo $rate_total; ?>"></div>
                            <span class="penci-numbers-rate">( <span
                                        class="penci-number-people"><?php echo $rate_people; ?></span> <?php if ( get_theme_mod( 'penci_recipe_voted_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_voted_text' ) );
								} else {
									esc_html_e( 'voted', 'penci-recipe' );
								} ?> )</span>
                        </div>
					<?php endif; ?>
                </div>
            </div>

			<?php if ( $recipe_style == 'style-4' && ( ! get_theme_mod( 'penci_recipe_print' ) || get_theme_mod( 'penci_recipe_pinterest' ) ) ) { ?>
                <div class="wrapper-buttons-style4">
					<?php if ( get_theme_mod( 'penci_recipe_pinterest' ) ) : ?>
                        <div class="wrapper-col-btn">
                            <a href="<?php echo $pin_url; ?>" target="_blank" class="penci-recipe-pin"
                               data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
									penci_fawesome_icon( 'fas fa-pinterest-p' );
								} else {
									echo '<i class="fa fa-pinterest-p"></i>';
								} ?><?php if ( get_theme_mod( 'penci_recipe_pin_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_pin_text' ) );
								} else {
									esc_html_e( 'Pin', 'penci-recipe' );
								} ?></a>
                        </div>
					<?php endif; ?>
					<?php if ( ! get_theme_mod( 'penci_recipe_print' ) ) : ?>
                        <div class="wrapper-col-btn">
                            <a href="#" class="penci-recipe-print-btn penci-recipe-print-overlay"
                               data-print="<?php echo plugin_dir_url( __FILE__ ) . 'print.css?ver=' . PENCI_RECIPE_VER; ?>"><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
									penci_fawesome_icon( 'fas fa-print' );
								} else {
									echo '<i class="fa fa-print"></i>';
								} ?><?php if ( get_theme_mod( 'penci_recipe_print_text' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_recipe_print_text' ) );
								} else {
									esc_html_e( 'Print', 'penci-recipe' );
								} ?></a>
                        </div>
					<?php endif; ?>
                </div>
			<?php } ?>

			<?php if ( $recipe_ingredients ) : ?>
                <div class="penci-recipe-ingredients<?php if ( get_theme_mod( 'penci_recipe_ingredients_visual' ) ) {
					echo ' penci-recipe-ingre-visual';
				} else {
					echo ' penci-recipe-not-visual';
				} ?>">
                    <h3 class="penci-recipe-title"><?php if ( get_theme_mod( 'penci_recipe_ingredients_text' ) ) {
							echo do_shortcode( get_theme_mod( 'penci_recipe_ingredients_text' ) );
						} else {
							esc_html_e( 'Ingredients', 'penci-recipe' );
						} ?></h3>
					<?php if ( ! get_theme_mod( 'penci_recipe_ingredients_visual' ) ) { ?>
                        <ul>
							<?php foreach ( $recipe_ingredients_array as $ingredient ) : ?>
								<?php if ( $ingredient ) :
									$ing_trim = trim( $ingredient );
									$str_ing = substr( $ing_trim, 0, 2 );
									$str_echo = substr( $ing_trim, 2 );
									if ( $str_ing == '==' ) {
										?>
                                        <h4 class="recipe-ingredient-heading"><span><?php echo $str_echo; ?></span></h4>
									<?php } else { ?>
                                        <li><span><?php echo $ingredient; ?></span></li>
									<?php } endif; ?>
							<?php endforeach; ?>
                        </ul>
					<?php } else { ?>
						<?php
						//echo apply_filters( 'the_content', htmlspecialchars_decode( $recipe_ingredients ) );
						$content_autop = do_shortcode( wpautop( htmlspecialchars_decode( $recipe_ingredients ) ) );
						echo $content_autop;
						?>
					<?php } ?>
                </div>
			<?php endif; ?>

			<?php if ( $recipe_instructions ) : ?>
                <div class="penci-recipe-method">
                    <h3 class="penci-recipe-title"><?php if ( get_theme_mod( 'penci_recipe_instructions_text' ) ) {
							echo do_shortcode( get_theme_mod( 'penci_recipe_instructions_text' ) );
						} else {
							esc_html_e( 'Instructions', 'penci-recipe' );
						} ?></h3>
					<?php
					$content_insautop = do_shortcode( wpautop( htmlspecialchars_decode( $recipe_instructions ) ) );
					echo $content_insautop;
					?>
                </div>
			<?php endif; ?>

			<?php if ( $recipe_note ) : ?>
                <div class="penci-recipe-notes<?php if ( get_theme_mod( 'penci_recipe_notes_visual' ) ) {
					echo ' penci-recipe-notes-visual';
				} else {
					echo ' penci-recipe-notes-novisual';
				} ?>">
                    <h3 class="penci-recipe-title"><?php if ( get_theme_mod( 'penci_recipe_notes_text' ) ) {
							echo do_shortcode( get_theme_mod( 'penci_recipe_notes_text' ) );
						} else {
							esc_html_e( 'Notes', 'penci-recipe' );
						} ?></h3>
					<?php if ( ! get_theme_mod( 'penci_recipe_notes_visual' ) ) { ?>
                        <p><?php echo $recipe_note; ?></p>
					<?php } else {
						$notes_autop = do_shortcode( wpautop( htmlspecialchars_decode( $recipe_note ) ) );
						echo $notes_autop;
					} ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
	<?php if ( get_theme_mod( 'penci_recipe_make_trecipe' ) ): ?>
        <div class="penci-recipe-tagged">
            <span class="prt-wrap-span prt-icon"><span><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
						penci_fawesome_icon( 'fab fa-instagram' );
					} else {
						echo '<i class="fa fa-instagram"></i>';
					} ?></span></span>
            <div class="prt-wrap-span prt-wrap-spantext">
                <span class="prt-span-heading"><?php if ( get_theme_mod( 'penci_recipe_did_you_make' ) ) {
						echo do_shortcode( get_theme_mod( 'penci_recipe_did_you_make' ) );
					} else {
						esc_html_e( 'Did You Make This Recipe?', 'penci-recipe' );
					} ?></span>
                <div class="prt-span-des"><?php if ( get_theme_mod( 'penci_recipe_descmake_recipe' ) ) {
						echo do_shortcode( get_theme_mod( 'penci_recipe_descmake_recipe' ) );
					} else {
						echo 'How you went with my recipes? Tag me on Instagram at <a href="https://www.instagram.com/">@PenciDesign.</a>';
					} ?></div>
            </div>
        </div>
	<?php endif; ?>

	<?php
	return ob_get_clean();
}

add_shortcode( 'penci_recipe', 'penci_recipe_shortcode_function' );


/**
 * Penci Recipe Index
 *
 * Use penci_index to display the recipe on single a post
 */
function penci_recipe_index_function( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title'         => '',
		'cat'           => '',
		'numbers_posts' => '',
		'columns'       => '',
		'display_title' => 'yes',
		'display_cat'   => 'no',
		'display_date'  => 'yes',
		'display_image' => 'yes',
		'image_size'    => 'square',
		'cat_link'      => 'yes',
		'cat_link_text' => 'View All'
	), $atts ) );

	$index_title         = isset( $title ) ? $title : '';
	$index_cat           = isset( $cat ) ? $cat : '';
	$index_numbers_posts = isset( $numbers_posts ) ? $numbers_posts : '3';
	$index_cols          = isset( $columns ) ? $columns : '3';
	$index_display_title = isset( $display_title ) ? $display_title : 'yes';
	$index_display_cat   = isset( $display_cat ) ? $display_cat : 'no';
	$index_display_date  = isset( $display_date ) ? $display_date : 'yes';
	$index_display_image = isset( $display_image ) ? $display_image : 'yes';
	$index_image_size    = isset( $image_size ) ? $image_size : 'square';
	$index_cat_link      = isset( $cat_link ) ? $cat_link : 'yes';
	$index_cat_text      = isset( $cat_link_text ) ? $cat_link_text : 'View All';

	$index_query = new WP_Query( array(
		'category_name'       => $index_cat,
		'posts_per_page'      => $index_numbers_posts,
		'ignore_sticky_posts' => true
	) );

	$post_found = $index_query->found_posts;

	ob_start();

	$cat_link   = '';
	$open_link  = '';
	$close_link = '';
	if ( $index_cat ) :
		$index_cat = do_shortcode( $index_cat );
		$catOj     = get_category_by_slug( $index_cat );
		$cat_id    = $catOj->term_id;
		$cat_link  = get_category_link( $cat_id );
	endif;

	if ( $index_cat_link == "yes" && $cat_link ):
		$open_link  = '<a href="' . esc_url( $cat_link ) . '">';
		$close_link = '</a>';
	endif;
	?>

	<?php if ( $index_query->have_posts() ) : ?>
        <div class="penci-recipe-index-wrap">
			<?php if ( $index_title ) : ?>
                <h4 class="recipe-index-heading">
                    <span><?php echo $open_link . do_shortcode( $index_title ) . $close_link; ?></span></h4>
			<?php endif; ?>

			<?php
			/* Define columns of recipe index */
			$columns_class = '3';
			if ( $index_cols == '2' || $index_cols == '4' ) {
				$columns_class = $index_cols;
			}
			?>
            <ul class="penci-recipe-index column-<?php echo $columns_class; ?>">
				<?php while ( $index_query->have_posts() ) : $index_query->the_post(); ?>
                    <li>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'penci-recipe-item' ); ?>>
							<?php if ( $index_display_image != 'no' && function_exists( 'penci_get_featured_image_size' ) ) : ?>
                                <div class="penci-index-post-img">
									<?php $thumbnail_size = 'penci-thumb-square';
									if ( $index_image_size == 'vertical' ) {
										$thumbnail_size = 'penci-thumb-vertical';
									} elseif ( $index_image_size == 'horizontal' ) {
										$thumbnail_size = 'penci-thumb';
									}
									?>
									<?php if ( ! get_theme_mod( 'penci_disable_lazyload_layout' ) ) { ?>
                                        <a href="<?php the_permalink(); ?>"
                                           class="penci-image-holder penci-holder-size-<?php echo $index_image_size; ?> penci-lazy"
                                           data-bgset="<?php echo penci_get_featured_image_size( get_the_ID(), $thumbnail_size ); ?>"
                                           title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
									<?php } else { ?>
                                        <a href="<?php the_permalink(); ?>"
                                           class="penci-image-holder penci-holder-size-<?php echo $index_image_size; ?>"
                                           style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), $thumbnail_size ); ?>');"
                                           title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
									<?php } ?>
                                </div>
							<?php endif; /* End check for thumbnails */ ?>

							<?php if ( $index_display_cat == 'yes' ) : ?>
                                <span class="cat"><?php penci_category( '' ); ?></span>
							<?php endif; ?>

							<?php if ( $index_display_title != 'no' ) : ?>
                                <h2 class="penci-recipe-index-title"><a
                                            href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php endif; ?>

							<?php if ( $index_display_date != 'no' ) : ?>
                                <span class="date"><?php the_time( get_option( 'date_format' ) ); ?></span>
							<?php endif; ?>
                        </article>
                    </li>
				<?php endwhile; ?>
            </ul>
			<?php if ( $index_cat_link == "yes" && $cat_link && ( $post_found > $index_numbers_posts ) ) : ?>
                <div class="penci-index-more-link"><a
                            href="<?php echo esc_url( $cat_link ); ?>"><?php echo do_shortcode( $index_cat_text ); ?><?php if ( function_exists( 'penci_fawesome_icon' ) ) {
							penci_fawesome_icon( 'fas fa-long-arrow-alt-right' );
						} else {
							echo '<i class="fa fa-long-arrow-right"></i>';
						} ?></a></div>
			<?php endif; ?>

        </div>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

add_shortcode( 'penci_index', 'penci_recipe_index_function' );

function penci_recipe_meta_text( $id ) {
	$recipe_id          = $id;
	if ( !$recipe_id ) {
		$recipe_id = get_the_ID();
	}
	$recipe_servings    = get_post_meta( $recipe_id, 'penci_recipe_servings', true );
	$recipe_cooktime    = get_post_meta( $recipe_id, 'penci_recipe_cooktime', true );
	$recipe_cooktime_fm = get_post_meta( $recipe_id, 'penci_recipe_cooktime_format', true );
	$recipe_preptime    = get_post_meta( $recipe_id, 'penci_recipe_preptime', true );
	$recipe_preptime_fm = get_post_meta( $recipe_id, 'penci_recipe_preptime_format', true );

	$meta_show = get_theme_mod('penci_recipe_archive_meta_item');
	$meta_show = $meta_show ? $meta_show : [ 'serves', 'cooking_time' ];
	ob_start();
	if ( $recipe_servings || $recipe_cooktime || $recipe_preptime ) : ?>
        <div class="penci-recipe-meta archive-pos">
			<?php if ( $recipe_servings && in_array( 'serves', $meta_show ) ) : ?><span>
                <i class="penci-ficon ficon-hot-food"></i> <span
                        class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_serves_text' ) ) {
						echo do_shortcode( get_theme_mod( 'penci_recipe_serves_text' ) );
					} else {
						esc_html_e( 'Serves', 'penci-recipe' );
					} ?>:</span> <span class="servings"><?php echo $recipe_servings; ?></span>
                </span>
			<?php endif; ?>
			<?php if ( $recipe_preptime && in_array( 'prep_time', $meta_show ) ) : ?>
                <span>
				<i class="penci-ficon ficon-clock"></i> <span
                            class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_prep_time_text' ) ) {
							echo do_shortcode( get_theme_mod( 'penci_recipe_prep_time_text' ) );
						} else {
							esc_html_e( 'Prep Time', 'penci-recipe' );
						} ?>:</span> <time <?php if ( $recipe_preptime_fm ): echo 'datetime="PT' . $recipe_preptime_fm . '" '; endif; ?>><?php echo $recipe_preptime; ?></time>
				</span>
			<?php endif; ?>
			<?php if ( $recipe_cooktime && in_array( 'cooking_time', $meta_show ) ) : ?>
                <span>
				<i class="penci-ficon ficon-cooking"></i> <span
                            class="remeta-item"><?php if ( get_theme_mod( 'penci_recipe_cooking_text' ) ) {
							echo do_shortcode( get_theme_mod( 'penci_recipe_cooking_text' ) );
						} else {
							esc_html_e( 'Cooking Time', 'penci-recipe' );
						} ?>:</span> <time <?php if ( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm . '" '; endif; ?>><?php echo $recipe_cooktime; ?></time>
				<time class="penci-hide-tagupdated" <?php if ( $recipe_cooktime_fm ): echo 'datetime="PT' . $recipe_cooktime_fm . '" '; endif; ?>><?php echo $recipe_cooktime; ?></time>
				</span>
			<?php endif; ?>
        </div>
	<?php endif;
	return ob_get_clean();
}


add_action( 'penci_before_the_excerpt', function ( $id ) {
	if ( penci_recipe_meta_text( $id ) && get_theme_mod( 'penci_recipe_archive_meta' ) && is_archive() ) {
		echo penci_recipe_meta_text( $id );
	}
} );