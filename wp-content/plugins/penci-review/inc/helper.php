<?php
/**
 * Callback function for sanitizing checkbox settings.
 * Use for PenciDesign
 *
 * @param $input
 *
 * @return string default value if type is not exists
 */
function penci_review_sanitize_checkbox_field( $input ) {
	if ( $input == 1 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Customize colors
 * @since 3.0
 */
function penci_review_customizer_css() {
	?>
    <style type="text/css">
        <?php if(get_theme_mod( 'penci_review_border_color' )) : ?>.wrapper-penci-review {
            border-color: <?php echo get_theme_mod( 'penci_review_border_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_title_color' )) : ?>.penci-review-container.penci-review-count h4 {
            color: <?php echo get_theme_mod( 'penci_review_title_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_desc_color' )) : ?>.post-entry .penci-review-desc p {
            color: <?php echo get_theme_mod( 'penci_review_desc_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_point_title_color' )) : ?>.penci-review-text {
            color: <?php echo get_theme_mod( 'penci_review_point_title_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_process_main_color' )) : ?>.penci-review-process {
            background-color: <?php echo get_theme_mod( 'penci_review_process_main_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_process_run_color' )) : ?>.penci-review .penci-review-process span {
            background-color: <?php echo get_theme_mod( 'penci_review_process_run_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_title_good_color' )) : ?>.penci-review-stuff .penci-review-good h5 {
            color: <?php echo get_theme_mod( 'penci_review_title_good_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_good_icon' )) : ?>.penci-review .penci-review-good ul li:before {
            color: <?php echo get_theme_mod( 'penci_review_good_icon' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_bad_icon' )) : ?>.penci-review .penci-review-bad ul li:before {
            color: <?php echo get_theme_mod( 'penci_review_bad_icon' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_average_total_bg' )) : ?>.penci-review .penci-review-score-total {
            background-color: <?php echo get_theme_mod( 'penci_review_average_total_bg' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_average_total_color' )) : ?>.penci-review-score-num {
            color: <?php echo get_theme_mod( 'penci_review_average_total_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_average_text_color' )) : ?>.penci-review-score-total span {
            color: <?php echo get_theme_mod( 'penci_review_average_text_color' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_piechart_text' )) : ?>.penci-chart-text {
            color: <?php echo get_theme_mod( 'penci_review_piechart_text' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_star_bgcl' )) : ?>.penci-rv-sm-show.stars-rating .normal-stars {
            color: <?php echo get_theme_mod( 'penci_review_star_bgcl' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_star_cl' )) : ?>.penci-rv-sm-show.stars-rating .rate-stars {
            color: <?php echo get_theme_mod( 'penci_review_star_cl' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_star_bgclb' )) : ?>.penci-review-score-total .total-stars,.penci-review-number.star-rating .penci-review-process::before {
            color: <?php echo get_theme_mod( 'penci_review_star_bgclb' ); ?>;
        }
        <?php endif; ?>
        <?php if(get_theme_mod( 'penci_review_star_clb' )) : ?>.penci-review-score-num .current-stars,.penci-review-number.star-rating .penci-review-process span:before {
            color: <?php echo get_theme_mod( 'penci_review_star_clb' ); ?>;
        }
        <?php endif; ?>
    </style>
	<?php
}

add_action( 'wp_head', 'penci_review_customizer_css' );
