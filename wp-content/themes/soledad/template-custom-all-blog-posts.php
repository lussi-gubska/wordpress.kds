<?php
/**
 * Template Name: Custom Blog Page
 *
 * @package Wordpress
 * @since   2.3
 */

get_header();

/* Sidebar position */
$sidebar_position = penci_get_sidebar_position_archive();

$featured_boxes = get_post_meta( get_the_ID(), 'penci_page_display_featured_boxes', true );
$pagetitle      = get_post_meta( $post->ID, 'penci_page_display_title', true );
$breadcrumb     = get_post_meta( get_the_ID(), 'penci_page_breadcrumb', true );

$two_sidebar_class = '';
if ( 'two-sidebar' == $sidebar_position ): $two_sidebar_class = ' two-sidebar'; endif;

/* Categories layout */
$layout_this = get_theme_mod( 'penci_archive_layout' );
if ( ! isset( $layout_this ) || empty( $layout_this ) ): $layout_this = 'standard'; endif;
$class_layout = '';
if ( $layout_this == 'classic' ): $class_layout = ' classic-layout'; endif;

$page_meta     = get_post_meta( get_the_ID(), 'penci_page_slider', true );
$rev_shortcode = get_post_meta( get_the_ID(), 'penci_page_rev_shortcode', true );
$title_acls = 'pcatitle-' . get_theme_mod('penci_archive_titlealign', 'default');

if ( in_array( $page_meta, array(
	'style-1',
	'style-2',
	'style-3',
	'style-4',
	'style-5',
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
	'style-28',
	'style-29',
	'style-30',
	'style-31',
	'style-32',
	'style-33',
	'style-34',
	'style-35',
	'style-36',
	'style-37',
	'style-38',
	'style-40',
	'style-41',
	'style-42',
	'style-44',
	'video'
) ) ) {
	if ( $page_meta == 'video' && get_theme_mod( 'penci_featured_video_url' ) ) {
		get_template_part( 'inc/featured_slider/featured_video' );
	} else {
		if ( ( $page_meta == 'style-33' || $page_meta == 'style-34' ) && $rev_shortcode ) {
			echo '<div class="featured-area featured-' . $page_meta . '">';
			if ( $page_meta == 'style-34' ): echo '<div class="container">'; endif;
			echo do_shortcode( $rev_shortcode );
			if ( $page_meta == 'style-34' ): echo '</div>'; endif;
			echo '</div>';
		} else {
			if ( get_theme_mod( 'penci_body_boxed_layout' ) && ! get_theme_mod( 'penci_vertical_nav_show' ) ) {
				if ( $page_meta == 'style-3' ) {
					$page_meta == 'style-1';
				} elseif ( $page_meta == 'style-5' ) {
					$page_meta == 'style-4';
				} elseif ( $page_meta == 'style-7' ) {
					$page_meta == 'style-8';
				} elseif ( $page_meta == 'style-9' ) {
					$page_meta == 'style-10';
				} elseif ( $page_meta == 'style-11' ) {
					$page_meta == 'style-12';
				} elseif ( $page_meta == 'style-13' ) {
					$page_meta == 'style-14';
				} elseif ( $page_meta == 'style-15' ) {
					$page_meta == 'style-16';
				} elseif ( $page_meta == 'style-17' ) {
					$page_meta == 'style-18';
				} elseif ( $page_meta == 'style-29' ) {
					$page_meta == 'style-30';
				} elseif ( $page_meta == 'style-35' ) {
					$page_meta == 'style-36';
				}
			}
			$slider_class = $page_meta;
			if ( $page_meta == 'style-5' ) {
				$slider_class = 'style-4 style-5';
			} elseif ( $page_meta == 'style-30' ) {
				$slider_class = 'style-29 style-30';
			} elseif ( $page_meta == 'style-36' ) {
				$slider_class = 'style-35 style-36';
			}
			$data_auto = 'false';
			$data_loop = 'true';
			$data_res  = '';

			if ( $page_meta == 'style-7' || $page_meta == 'style-8' ) {
				$data_res = ' data-item="4" data-desktop="4" data-tablet="2" data-tabsmall="1"';
			} elseif ( $page_meta == 'style-9' || $page_meta == 'style-10' ) {
				$data_res = ' data-item="3" data-desktop="3" data-tablet="2" data-tabsmall="1"';
			} elseif ( $page_meta == 'style-11' || $page_meta == 'style-12' ) {
				$data_res = ' data-item="2" data-desktop="2" data-tablet="2" data-tabsmall="1"';
			} elseif ( in_array( $page_meta, array( 'style-31', 'style-32', 'style-35', 'style-36', 'style-37' ) ) ) {
				$data_next_prev = get_theme_mod( 'penci_enable_next_prev_penci_slider' ) ? 'true' : 'false';
				$data_dots      = get_theme_mod( 'penci_disable_dots_penci_slider' ) ? 'false' : 'true';
				$data_res       = ' data-dots="' . $data_dots . '" data-nav="' . $data_next_prev . '"';
			}

			if ( get_theme_mod( 'penci_featured_autoplay' ) ): $data_auto = 'true'; endif;
			if ( get_theme_mod( 'penci_featured_loop' ) ): $data_loop = 'false'; endif;
			$auto_time = get_theme_mod( 'penci_featured_slider_auto_time' );
			if ( ! is_numeric( $auto_time ) ): $auto_time = '4000'; endif;
			$auto_speed = get_theme_mod( 'penci_featured_slider_auto_speed' );
			if ( ! is_numeric( $auto_speed ) ): $auto_speed = '600'; endif;
			$open_container  = '';
			$close_container = '';
			if ( in_array( $page_meta, array(
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
				'style-44'
			) ) ):
				$open_container  = '<div class="container">';
				$close_container = '</div>';
			endif;

			if ( get_theme_mod( 'penci_enable_flat_overlay' ) && in_array( $page_meta, array(
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
				) ) ): $slider_class .= ' penci-flat-overlay'; endif;

			$slider_lib = 'elsl-' . $page_meta;;

			$swiper = true;

			if ( $page_meta == 'style-40' ) {
				wp_enqueue_script( 'ff40' );
				wp_enqueue_script( 'gsap' );
				$slider_lib .= ' no-df-swiper';
				$swiper     = false;
			}

			if ( $page_meta == 'style-41' || $page_meta == 'style-42' || $page_meta == 'style-44' ) {
				$slider_lib .= ' no-df-swiper';
				$swiper     = false;
			}

			if ( $swiper ) {
				$slider_lib .= ' swiper penci-owl-carousel';
			}

			echo '<div class="featured-area featured-' . $slider_class . '">' . $open_container;
			if ( $page_meta == 'style-37' ):
				echo '<div class="penci-featured-items-left">';
			endif;
			echo '<div class="' . $slider_lib . ' penci-owl-featured-area"' . $data_res . ' data-style="' . $page_meta . '" data-auto="' . $data_auto . '" data-autotime="' . $auto_time . '" data-speed="' . $auto_speed . '" data-loop="' . $data_loop . '">';
			if ( $swiper ) {
				echo '<div class="penci-owl-nav"><div class="owl-prev"><i class="penciicon-left-chevron"></i></div><div class="owl-next"><i class="penciicon-right-chevron"></i></div></div>';
				echo '<div class="swiper-wrapper">';
			}

			get_template_part( 'inc/featured_slider/' . $page_meta );
			if ( $swiper && $page_meta != 'style-37' ) {
				echo '</div>';
			}
			echo '</div>';
			echo $close_container . '</div>';
		}
	}
}

/* Display Featured Boxes */
if ( $featured_boxes == 'yes' && ! get_theme_mod( 'penci_home_hide_boxes' ) && ( get_theme_mod( 'penci_home_box_img1' ) || get_theme_mod( 'penci_home_box_img2' ) || get_theme_mod( 'penci_home_box_img3' ) || get_theme_mod( 'penci_home_box_img4' ) ) ):
	get_template_part( 'inc/modules/home_boxes' );
endif;
?>
<?php $show_page_title = penci_is_pageheader(); ?>
<?php if ( ! $show_page_title ): ?>
	<?php if ( ! get_theme_mod( 'penci_disable_breadcrumb' ) && ( 'no' != $breadcrumb ) && ! get_theme_mod( 'penci_move_breadcrumbs' ) ): ?>
		<?php
		$yoast_breadcrumb = $rm_breadcrumb = '';
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb' . $two_sidebar_class . '">', '</div>', false );
		}

		if ( function_exists( 'rank_math_get_breadcrumbs' ) ) {
			$rm_breadcrumb = rank_math_get_breadcrumbs( [
				'wrap_before' => '<div class="container penci-breadcrumb' . $two_sidebar_class . '"><nav aria-label="breadcrumbs" class="rank-math-breadcrumb">',
				'wrap_after'  => '</nav></div>',
			] );
		}


		if ( $rm_breadcrumb ) {
			echo $rm_breadcrumb;
		} elseif ( $yoast_breadcrumb ) {
			echo $yoast_breadcrumb;
		} else { ?>
            <div class="container penci-breadcrumb<?php echo $two_sidebar_class; ?>">
                <span><a class="crumb"
                         href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><?php penci_fawesome_icon( 'fas fa-angle-right' ); ?>
                <span><?php the_title(); ?></span>
            </div>
		<?php } ?>
	<?php endif; ?>
<?php endif; ?>

<div class="container<?php echo esc_attr( $class_layout );
if ( penci_get_setting( 'penci_sidebar_archive' ) ) : ?> penci_sidebar <?php echo esc_attr( $sidebar_position ); ?><?php endif; ?>">
    <div id="main"
         class="penci-layout-<?php echo esc_attr( $layout_this ); ?><?php if ( get_theme_mod( 'penci_sidebar_sticky' ) ): ?> penci-main-sticky-sidebar<?php endif; ?>">
        <div class="theiaStickySidebar">

			<?php if ( ! $show_page_title ): ?>
				<?php if ( ! get_theme_mod( 'penci_disable_breadcrumb' ) && ( 'no' != $breadcrumb ) && get_theme_mod( 'penci_move_breadcrumbs' ) ): ?>
					<?php
					$yoast_breadcrumb = $rm_breadcrumb = '';
					if ( function_exists( 'yoast_breadcrumb' ) ) {
						$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb penci-crumb-inside' . $two_sidebar_class . '">', '</div>', false );
					}

					if ( function_exists( 'rank_math_get_breadcrumbs' ) ) {
						$rm_breadcrumb = rank_math_get_breadcrumbs( [
							'wrap_before' => '<div class="container penci-breadcrumb penci-crumb-inside' . $two_sidebar_class . '"><nav aria-label="breadcrumbs" class="rank-math-breadcrumb">',
							'wrap_after'  => '</nav></div>',
						] );
					}

					if ( $rm_breadcrumb ) {
						echo $rm_breadcrumb;
					} elseif ( $yoast_breadcrumb ) {
						echo $yoast_breadcrumb;
					} else { ?>
                        <div class="container penci-breadcrumb penci-crumb-inside<?php echo $two_sidebar_class; ?>">
                                <span><a class="crumb"
                                         href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><?php penci_fawesome_icon( 'fas fa-angle-right' ); ?>
                            <span><?php the_title(); ?></span>
                        </div>
					<?php } ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! $show_page_title ): ?>
				<?php if ( get_the_title() && ( 'no' != $pagetitle ) ): ?>
                    <div class="archive-box">
                        <div class="title-bar <?php echo $title_acls; ?>">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                    </div>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			$paged = max( get_query_var( 'paged' ), get_query_var( 'page' ), 1 );

			$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'paged' => $paged );

			$query_custom = new WP_Query( $args );

			if ( $query_custom->have_posts() ) :
				$class_grid_arr = array(
					'mixed',
					'mixed-2',
					'mixed-3',
					'mixed-4',
					'small-list',
					'overlay-grid',
					'overlay-list',
					'photography',
					'grid',
					'grid-2',
					'list',
					'boxed-1',
					'boxed-2',
					'boxed-3',
					'standard-grid',
					'standard-grid-2',
					'standard-list',
					'standard-boxed-1',
					'classic-grid',
					'classic-grid-2',
					'classic-list',
					'classic-boxed-1',
					'magazine-1',
					'magazine-2'
				);
				if ( in_array( $layout_this, $class_grid_arr ) ) {
					echo '<ul class="penci-wrapper-data penci-grid">';
				} elseif ( in_array( $layout_this, array( 'masonry', 'masonry-2' ) ) ) {
					echo '<div class="penci-wrap-masonry"><div class="penci-wrapper-data masonry penci-masonry">';
				} elseif ( get_theme_mod( 'penci_archive_nav_ajax' ) || get_theme_mod( 'penci_archive_nav_scroll' ) ) {
					echo '<div class="penci-wrapper-data">';
				}
				?>

				<?php /* The loop */
				$infeed_ads  = get_theme_mod( 'penci_infeedads_archi_code' ) ? get_theme_mod( 'penci_infeedads_archi_code' ) : '';
				$infeed_num  = get_theme_mod( 'penci_infeedads_archi_num' ) ? get_theme_mod( 'penci_infeedads_archi_num' ) : 3;
				$infeed_full = get_theme_mod( 'penci_infeedads_archi_layout' ) ? get_theme_mod( 'penci_infeedads_archi_layout' ) : '';

				while ( $query_custom->have_posts() ) : $query_custom->the_post();
					include( locate_template( 'content-' . $layout_this . '.php' ) );
				endwhile;
				?>

				<?php
				if ( in_array( $layout_this, $class_grid_arr ) ) {
					echo '</ul>';
				} elseif ( in_array( $layout_this, array( 'masonry', 'masonry-2' ) ) ) {
					echo '</div></div>';
				} elseif ( get_theme_mod( 'penci_archive_nav_ajax' ) || get_theme_mod( 'penci_archive_nav_scroll' ) ) {
					echo '</div>';
				}
				?>

				<?php
				if ( ! get_theme_mod( 'penci_archive_nav_ajax' ) && ! get_theme_mod( 'penci_archive_nav_scroll' ) ) {
					echo penci_pagination_numbers( $query_custom );
				} else {
					penci_soledad_archive_pag_style( $layout_this, $query_custom );
				}
				?>

			<?php endif;
			wp_reset_postdata(); /* End if of the loop */ ?>
        </div>
    </div>

	<?php if ( penci_get_setting( 'penci_sidebar_archive' ) ) : ?>
		<?php get_sidebar(); ?>
		<?php if ( get_theme_mod( 'penci_two_sidebar_archive' ) ) : get_sidebar( 'left' ); endif; ?>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
