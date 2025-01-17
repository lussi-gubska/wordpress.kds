<?php

$mess          = array();
$post_settings = get_post_meta( $post_id, 'td_post_theme_settings', true );

// Post format
$post_format   = get_post_format( $post_id );
$td_post_video = get_post_meta( $post_id, 'td_post_video', true );
if ( isset( $td_post_video['td_video'] ) && $td_post_video['td_video'] ) {
	$video_url_updated = get_post_meta( $post_id, '_format_video_embed', $td_post_video['td_video'] );

	if ( $video_url_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'video_url' );
	}
}

// Sub Title
$has_post_title = false;
$subtitle       = get_post_meta( $post_id, 'td_post_theme_settings', true );
if ( isset( $subtitle['td_subtitle'] ) && $subtitle['td_subtitle'] ) {
	$has_post_title = update_post_meta( $post_id, 'penci_post_sub_title', $subtitle['td_subtitle'] );
}

if ( $has_post_title ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'sub_title' );
}

// Count views
$views_all = (int) get_post_meta( $post_id, 'post_views_count', true );
if ( ! empty( $views_all ) ) {
	$views_all_updated = update_post_meta( $post_id, 'penci_post_views_count', $views_all );

	if ( $views_all_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'count_views' );
	}
}

// Primary Category
$primary_category = isset( $post_settings['td_primary_cat'] ) ? $post_settings['td_primary_cat'] : '';

if ( $primary_category ) {
	$meta_prefix_seo = class_exists( 'WPSEO_Meta' ) ? WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
	$primary_term    = update_post_meta( $post_id, $meta_prefix_seo . 'primary_category', true );
	if ( $primary_term ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'primary_term' );
	}
}

// Update custom sidebar
$sidebars_updated = get_option( 'newsmag_custom_sidebars_updated' );

if ( ! $sidebars_updated ) {
	$list_sidebar = get_option( 'soledad_custom_sidebars', array() );

	list( $before_widget, $after_widget, $before_title, $after_title ) = Penci_Soledad_MG_Helper::get_param_sidebars();

	$jannah_options  = get_option( 'td_010' );
	$custom_sidebars = isset( $jannah_options['sidebars'] ) ? $jannah_options['sidebars'] : array();

	if ( ! empty( $custom_sidebars ) && is_array( $custom_sidebars ) ) {
		foreach ( $custom_sidebars as $sidebar ) {

			$sidebar_id = str_replace( array( ' ' ), '-', trim( $sidebar ) );
			$sidebar_id = str_replace( array( "'", '"' ), '', trim( $sidebar_id ) );
			$sidebar_id = 'td-' . strtolower( $sidebar_id );

			$list_sidebar[ $sidebar_id ] = array(
				'class'         => 'soledad-custom-sidebar',
				'id'            => Penci_Soledad_MG_Helper::convert_sidebar_id( $sidebar ),
				'name'          => $sidebar,
				'description'   => '',
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title
			);
		}
	}

	$custom_sidebars = update_option( 'newsmag_custom_sidebars_updated', $list_sidebar );
	if ( $custom_sidebars ) {
		update_option( 'newsmag_custom_sidebars_updated', 1 );
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'custom_sidebar', timer_stop() );
	}
}

// Post layout
$post_layout  = isset( $post_settings['td_sidebar_position'] ) ? $post_settings['td_sidebar_position'] : '';
$post_sidebar = isset( $post_settings['td_sidebar'] ) ? Penci_Soledad_MG_Helper::convert_sidebar_id( $post_settings['td_sidebar'] ) : '';

$post_layout_pre = 'right';
if ( 'sidebar_left' == $post_layout ) {
	$post_layout_pre = 'left';
} elseif ( 'no_sidebarn' == $post_layout ) {
	$post_layout_pre = 'no';
}

$single_sidebar_pos   = update_post_meta( $post_id, 'penci_post_sidebar_display', $post_layout_pre );
$single_sidebar_left  = update_post_meta( $post_id, 'penci_custom_sidebar_left_page_field', $post_sidebar );
$single_sidebar_right = update_post_meta( $post_id, 'penci_custom_sidebar_page_display', $post_sidebar );

$single_sidebar_left  = get_post_meta( $post_id, 'penci_custom_sidebar_left_page_field' );
$single_sidebar_right = get_post_meta( $post_id, 'penci_custom_sidebar_page_display' );

if ( $single_sidebar_pos ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'post_layout' );
}


// Review
$has_review = isset( $post_settings['has_review'] ) ? $post_settings['has_review'] : 'rate_point';

$review_style = 'style_1';

$penci_review = array(
	'penci_review_style' => 'style_1',
	'penci_review_title' => esc_html__( 'Review overview', 'penci-data-migrator' ),
	'penci_review_des'   => isset( $post_settings['review'] ) ? $post_settings['review'] : '',
	'penci_review_1'     => '',
	'penci_review_1_num' => '',
	'penci_review_2'     => '',
	'penci_review_2_num' => '',
	'penci_review_3'     => '',
	'penci_review_3_num' => '',
	'penci_review_4'     => '',
	'penci_review_4_num' => '',
	'penci_review_5'     => '',
	'penci_review_5_num' => '',
	'penci_review_good'  => '',
	'penci_review_bad'   => '',
);

if ( 'rate_point' == $has_review ) {
	$penci_review['penci_review_style'] = 'style_1';

	if ( ! empty( $post_settings['p_review_points'] ) ) {
		$points_count = 1;
		foreach ( $post_settings['p_review_points'] as $section ) {
			$desc = isset( $section['desc'] ) ? $section['desc'] : '';
			$rate = isset( $section['rate'] ) ? $section['rate'] : '';

			if ( $desc || $rate ) {
				$penci_review[ 'penci_review_' . $points_count ]          = $desc;
				$penci_review[ 'penci_review_' . $points_count . '_num' ] = $rate;
			}

			$points_count ++;
		}
	}
} elseif ( 'rate_percent' == $has_review ) {
	$penci_review['penci_review_style'] = 'style_2';

	if ( ! empty( $post_settings['p_review_percents'] ) ) {
		$percents_count = 1;
		foreach ( $post_settings['p_review_percents'] as $section ) {
			$desc = isset( $section['desc'] ) ? $section['desc'] : '';
			$rate = isset( $section['rate'] ) ? $section['rate'] : '';

			if ( $desc || $rate ) {
				$penci_review[ 'penci_review_' . $percents_count ]          = $desc;
				$penci_review[ 'penci_review_' . $percents_count . '_num' ] = $rate;
			}

			$percents_count ++;
		}
	}

} elseif ( 'rate_stars' == $has_review ) {
	$penci_review['penci_review_style'] = 'style_3';

	if ( ! empty( $post_settings['p_review_stars'] ) ) {
		$stars_count = 1;
		foreach ( $post_settings['p_review_stars'] as $section ) {
			$desc = isset( $section['desc'] ) ? $section['desc'] : '';
			$rate = isset( $section['rate'] ) ? $section['rate'] : '';

			if ( $desc || $rate ) {
				$penci_review[ 'penci_review_' . $stars_count ]          = $desc;
				$penci_review[ 'penci_review_' . $stars_count . '_num' ] = $rate;
			}

			$stars_count ++;
		}
	}
}
$penci_review_updated = false;
foreach ( $penci_review as $penci_review_item => $penci_review_value ) {
	$penci_review_updated = update_post_meta( $post_id, $penci_review_item, $penci_review_value );
}

if ( $penci_review_updated ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'review' );
}

$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );


return ( $mess ? implode( '<br>', $mess ) : '' );
