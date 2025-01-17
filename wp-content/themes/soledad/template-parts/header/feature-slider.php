<?php
if ( get_theme_mod( 'penci_enable_featured_video_bg' ) && get_theme_mod( 'penci_featured_video_url' ) ) {
	get_template_part( 'inc/featured_slider/featured_video' );
} else {
	if ( get_theme_mod( 'penci_featured_slider' ) ) {
		$slider_style = get_theme_mod( 'penci_featured_slider_style', 'style-1' );

		if ( in_array( $slider_style, [ 'style-33', 'style-34' ] ) && get_theme_mod( 'penci_feature_rev_sc' ) ) {
			$rev_shortcode = get_theme_mod( 'penci_feature_rev_sc' );
			echo '<div class="featured-area featured-' . $slider_style . '">';
			if ( $slider_style == 'style-34' ) {
				echo '<div class="container">';
			}
			echo do_shortcode( $rev_shortcode );
			if ( $slider_style == 'style-34' ) {
				echo '</div>';
			}
			echo '</div>';
		} else {
			$style_mappings = [
				'style-3'  => 'style-1',
				'style-5'  => 'style-4',
				'style-7'  => 'style-8',
				'style-9'  => 'style-10',
				'style-11' => 'style-12',
				'style-13' => 'style-14',
				'style-15' => 'style-16',
				'style-17' => 'style-18',
				'style-29' => 'style-30',
				'style-35' => 'style-36'
			];

			if ( get_theme_mod( 'penci_body_boxed_layout' ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) {
				$slider_style = $style_mappings[ $slider_style ] ?? $slider_style;
			}

			$combined_classes = [
				'style-5'  => 'style-4 style-5',
				'style-30' => 'style-29 style-30',
				'style-36' => 'style-35 style-36'
			];

			$slider_class = $combined_classes[ $slider_style ] ?? $slider_style;
			$data_auto    = get_theme_mod( 'penci_featured_autoplay' ) ? 'true' : 'false';
			$data_loop    = get_theme_mod( 'penci_featured_loop' ) ? 'false' : 'true';
			$auto_time    = is_numeric( $auto_time = get_theme_mod( 'penci_featured_slider_auto_time' ) ) ? $auto_time : '4000';
			$auto_speed   = is_numeric( $auto_speed = get_theme_mod( 'penci_featured_slider_auto_speed' ) ) ? $auto_speed : '600';

			$data_res = '';
			switch ( $slider_style ) {
				case 'style-7':
				case 'style-8':
					$data_res = ' data-item="4" data-desktop="4" data-tablet="2" data-tabsmall="1"';
					break;
				case 'style-9':
				case 'style-10':
				case 'style-43':
					$data_res = ' data-item="3" data-desktop="3" data-tablet="2" data-tabsmall="1"';
					break;
				case 'style-11':
				case 'style-12':
					$data_res = ' data-item="2" data-desktop="2" data-tablet="2" data-tabsmall="1"';
					break;
				case 'style-31':
				case 'style-32':
				case 'style-35':
				case 'style-36':
				case 'style-37':
					$data_next_prev = get_theme_mod( 'penci_enable_next_prev_penci_slider' ) ? 'true' : 'false';
					$data_dots      = get_theme_mod( 'penci_disable_dots_penci_slider' ) ? 'false' : 'true';
					$data_res       = ' data-dots="' . $data_dots . '" data-nav="' . $data_next_prev . '"';
					break;
			}

			$container_styles = [
				'style-1',
				'style-4',
				'style-6',
				'style-8',
				'style-10',
				'style-12',
				'style-14',
				'style-16',
				'style-18',
				'style-19',
				'style-20',
				'style-21',
				'style-22',
				'style-23',
				'style-24',
				'style-25',
				'style-26',
				'style-27',
				'style-30',
				'style-32',
				'style-36',
				'style-37',
				'style-41',
				'style-42',
				'style-43',
				'style-44'
			];

			$open_container = $close_container = '';
			if ( in_array( $slider_style, $container_styles ) ) {
				$open_container  = '<div class="container">';
				$close_container = '</div>';
			}

			$output_anim = '';
			if ( $animation_in = get_theme_mod( 'penci_featured_slider_ain' ) ) {
				$output_anim .= ' data-animation-in="' . $animation_in . '"';
			}
			if ( $animation_out = get_theme_mod( 'penci_featured_slider_aout' ) ) {
				$output_anim .= ' data-animation-out="' . $animation_out . '"';
			}

			$slider_lib = 'penci-owl-featured-area';
			if ( get_theme_mod( 'penci_enable_flat_overlay' ) && in_array( $slider_style, [
					'style-6',
					'style-7',
					'style-8',
					'style-9',
					'style-10',
					'style-11',
					'style-12',
					'style-13',
					'style-14',
					'style-15',
					'style-16',
					'style-17',
					'style-18',
					'style-19',
					'style-20',
					'style-21',
					'style-22',
					'style-23',
					'style-24',
					'style-25',
					'style-26',
					'style-27',
					'style-28'
				] ) ) {
				$slider_class .= ' penci-flat-overlay';
			}

			$slider_lib .= ' elsl-' . $slider_style;
			$swiper     = true;
			if ( $slider_style == 'style-40' ) {
				wp_enqueue_script( 'ff40' );
				wp_enqueue_script( 'gsap' );
				$slider_lib .= ' no-df-swiper';
				$swiper     = false;
			}

			if ( $slider_style == 'style-41' || $slider_style == 'style-42' || $slider_style == 'style-44' ) {
				$slider_lib .= ' no-df-swiper';
				$swiper     = false;
			}

			$new_attr = '';
			if ( $slidespg = get_theme_mod( 'penci_featured_penci_slider_slidespg' ) ) {
				$new_attr = ' data-slidespg="' . $slidespg . '"';
			}
			if ( $slideTo = get_theme_mod( 'penci_featured_penci_slider_slideTo' ) ) {
				$new_attr = ' data-slideTo="' . $slideTo . '"';
			}

			if ( $swiper ) {
				$slider_lib .= ' swiper penci-owl-carousel';
			}

			echo '<div class="featuredsl-customizer featured-area featured-' . $slider_class . '">' . $open_container;
			if ( $slider_style == 'style-37' ) {
				echo '<div class="penci-featured-items-left">';
			}
			echo '<div class="' . $slider_lib . '"' . $data_res . $new_attr . ' data-style="' . $slider_style . '" data-auto="' . $data_auto . '" data-autotime="' . $auto_time . '" data-speed="' . $auto_speed . '" data-loop="' . $data_loop . '"' . $output_anim . '>';
			if ( $swiper ) {
				echo '<div class="penci-owl-nav"><div class="owl-prev"><i class="penciicon-left-chevron"></i></div><div class="owl-next"><i class="penciicon-right-chevron"></i></div></div>';
				echo '<div class="swiper-wrapper">';
			}

			get_template_part( 'inc/featured_slider/' . $slider_style );

			if ( $swiper && $slider_style != 'style-37' ) {
				echo '</div>';
			}

			echo '</div>';
			echo $close_container . '</div>';
		}
	}
}