<?php
/**
 * All functions for Penci Review Plugin
 * @since 1.0
 */

/**
 * Get review average score for a posts
 *
 * @param $post_id
 *
 * @return string
 */
function penci_get_review_average_score( $post_id, $loop = null ) {
	// Get review data
	$review_1     = get_post_meta( $post_id, 'penci_review_1', true );
	$review_1num  = get_post_meta( $post_id, 'penci_review_1_num', true );
	$review_2     = get_post_meta( $post_id, 'penci_review_2', true );
	$review_2num  = get_post_meta( $post_id, 'penci_review_2_num', true );
	$review_3     = get_post_meta( $post_id, 'penci_review_3', true );
	$review_3num  = get_post_meta( $post_id, 'penci_review_3_num', true );
	$review_4     = get_post_meta( $post_id, 'penci_review_4', true );
	$review_4num  = get_post_meta( $post_id, 'penci_review_4_num', true );
	$review_5     = get_post_meta( $post_id, 'penci_review_5', true );
	$review_5num  = get_post_meta( $post_id, 'penci_review_5_num', true );
	$review_6     = get_post_meta( $post_id, 'penci_review_6', true );
	$review_6num  = get_post_meta( $post_id, 'penci_review_6_num', true );
	$review_7     = get_post_meta( $post_id, 'penci_review_7', true );
	$review_7num  = get_post_meta( $post_id, 'penci_review_7_num', true );
	$review_8     = get_post_meta( $post_id, 'penci_review_8', true );
	$review_8num  = get_post_meta( $post_id, 'penci_review_8_num', true );
	$review_9     = get_post_meta( $post_id, 'penci_review_9', true );
	$review_9num  = get_post_meta( $post_id, 'penci_review_9_num', true );
	$review_10    = get_post_meta( $post_id, 'penci_review_10', true );
	$review_10num = get_post_meta( $post_id, 'penci_review_10_num', true );

	if ( $loop ) {
		$loop_data = get_post_meta( $post_id, 'penci_review_items', true );
		$loop_item = isset( $loop_data[ $loop ] ) && is_array( $loop_data[ $loop ] ) ? $loop_data[ $loop ] : false;
		if ( $loop_item ) {
			$review_1     = isset( $loop_item['penci_review_1'] ) && $loop_item['penci_review_1'] ? $loop_item['penci_review_1'] : null;
			$review_1num  = isset( $loop_item['penci_review_1_num'] ) && $loop_item['penci_review_1_num'] ? $loop_item['penci_review_1_num'] : null;
			$review_2     = isset( $loop_item['penci_review_2'] ) && $loop_item['penci_review_2'] ? $loop_item['penci_review_2'] : null;
			$review_2num  = isset( $loop_item['penci_review_2_num'] ) && $loop_item['penci_review_2_num'] ? $loop_item['penci_review_2_num'] : null;
			$review_3     = isset( $loop_item['penci_review_3'] ) && $loop_item['penci_review_3'] ? $loop_item['penci_review_3'] : null;
			$review_3num  = isset( $loop_item['penci_review_3_num'] ) && $loop_item['penci_review_3_num'] ? $loop_item['penci_review_3_num'] : null;
			$review_4     = isset( $loop_item['penci_review_4'] ) && $loop_item['penci_review_4'] ? $loop_item['penci_review_4'] : null;
			$review_4num  = isset( $loop_item['penci_review_4_num'] ) && $loop_item['penci_review_4_num'] ? $loop_item['penci_review_4_num'] : null;
			$review_5     = isset( $loop_item['penci_review_5'] ) && $loop_item['penci_review_5'] ? $loop_item['penci_review_5'] : null;
			$review_5num  = isset( $loop_item['penci_review_5_num'] ) && $loop_item['penci_review_5_num'] ? $loop_item['penci_review_5_num'] : null;
			$review_6     = isset( $loop_item['penci_review_6'] ) && $loop_item['penci_review_6'] ? $loop_item['penci_review_6'] : null;
			$review_6num  = isset( $loop_item['penci_review_6_num'] ) && $loop_item['penci_review_6_num'] ? $loop_item['penci_review_6_num'] : null;
			$review_7     = isset( $loop_item['penci_review_7'] ) && $loop_item['penci_review_7'] ? $loop_item['penci_review_7'] : null;
			$review_7num  = isset( $loop_item['penci_review_7_num'] ) && $loop_item['penci_review_7_num'] ? $loop_item['penci_review_7_num'] : null;
			$review_8     = isset( $loop_item['penci_review_8'] ) && $loop_item['penci_review_8'] ? $loop_item['penci_review_8'] : null;
			$review_8num  = isset( $loop_item['penci_review_8_num'] ) && $loop_item['penci_review_8_num'] ? $loop_item['penci_review_8_num'] : null;
			$review_9     = isset( $loop_item['penci_review_9'] ) && $loop_item['penci_review_9'] ? $loop_item['penci_review_9'] : null;
			$review_9num  = isset( $loop_item['penci_review_9_num'] ) && $loop_item['penci_review_9_num'] ? $loop_item['penci_review_9_num'] : null;
			$review_10    = isset( $loop_item['penci_review_10'] ) && $loop_item['penci_review_10'] ? $loop_item['penci_review_10'] : null;
			$review_10num = isset( $loop_item['penci_review_10_num'] ) && $loop_item['penci_review_10_num'] ? $loop_item['penci_review_10_num'] : null;
		}
	}

	$total_score = 0;
	$total_num   = 0;

	if ( $review_1 && $review_1num ):
		$total_score = $total_score + $review_1num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_2 && $review_2num ):
		$total_score = $total_score + $review_2num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_3 && $review_3num ):
		$total_score = $total_score + $review_3num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_4 && $review_4num ):
		$total_score = $total_score + $review_4num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_5 && $review_5num ):
		$total_score = $total_score + $review_5num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_6 && $review_6num ):
		$total_score = $total_score + $review_6num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_7 && $review_7num ):
		$total_score = $total_score + $review_7num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_8 && $review_8num ):
		$total_score = $total_score + $review_8num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_9 && $review_9num ):
		$total_score = $total_score + $review_9num;
		$total_num   = $total_num + 1;
	endif;
	if ( $review_10 && $review_10num ):
		$total_score = $total_score + $review_10num;
		$total_num   = $total_num + 1;
	endif;

	$total_review = 0;
	if ( $total_score && $total_num ) {
		$total_review = $total_score / $total_num;
	}

	return $total_review;
}

/**
 * Get review markup piechart for a posts
 * Use this function in a loop
 *
 * @param $post_id
 *
 * @return string
 */
function penci_display_piechart_review_html( $post_id, $size = 'normal' ) {
	$total_score = penci_get_review_average_score( $post_id );
	if ( empty( $total_score ) || get_theme_mod( 'penci_review_hide_piechart' ) ) {
		return;
	}
	$format      = number_format( $total_score, 1, '.', '' );
	$percent     = $format * 10;
	$review_meta = get_post_meta( $post_id, 'penci_review_meta', true );
	$star_rating = get_theme_mod( 'penci_rv_enable_star_review' );
	$star_rating = isset( $review_meta['penci_rv_star_rating'] ) && $review_meta['penci_rv_star_rating'] ? $review_meta['penci_rv_star_rating'] : $star_rating;

	$pie_size = 50;
	if ( $size == 'small' ) {
		$pie_size = 34;

		if ( defined('PENCI_BL_VERSION')) {
			$pie_size = 28;
		}

	} else {
		$pie_size = 50;
	}

	$color = '#6eb48c';
	if ( get_theme_mod( 'penci_color_accent' ) ):
		$color = get_theme_mod( 'penci_color_accent' );
	endif;
	if ( get_theme_mod( 'penci_review_piechart_border' ) ):
		$color = get_theme_mod( 'penci_review_piechart_border' );
	endif;
	if ( $star_rating == 'enable' ) {
		?>
        <div class="penci-rv-sm-show stars-rating penci-stars-<?php echo $size; ?>">
            <span class="star-progress normal-stars"></span>
            <span class="star-progress rate-stars" style="width:<?php echo $percent; ?>%"></span>
        </div>
		<?php
	} else {
		?>
        <div class="penci-rv-sm-show penci-piechart penci-piechart-<?php echo $size; ?>"
             data-percent="<?php echo $percent; ?>"
             data-color="<?php echo $color; ?>" data-trackcolor="rgba(0, 0, 0, .2)" data-size="<?php echo $pie_size; ?>"
             data-thickness="<?php if ( $size == 'small' ) {
			     echo '2';
		     } else {
			     echo '3';
		     } ?>">
            <span class="penci-chart-text"><?php echo $format; ?></span>
        </div>
		<?php
	}
}

if ( ! function_exists( 'penci_predata_customize_pmeta' ) ) {
	function penci_predata_customize_pmeta( $penci_review, $id_customize, $id_pmeta ) {
		$data_customize = get_theme_mod( $id_customize );
		$data_pmeta     = isset( $penci_review[ $id_pmeta ] ) ? $penci_review[ $id_pmeta ] : '';

		if ( 'enable' == $data_pmeta ) {
			$data_customize = false;
		} elseif ( 'disable' == $data_pmeta ) {
			$data_customize = true;
		}

		return $data_customize;
	}
}