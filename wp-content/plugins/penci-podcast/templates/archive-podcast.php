<?php
/**
 * The template for displaying archive pages
 *
 * @package Wordpress
 * @since 1.0
 */
get_header();

/* Sidebar position */
$sidebar_position = get_theme_mod( 'pencipodcast_single_layout', 'left-sidebar' );

/* Archive layout */
$layout_this      = get_theme_mod( 'penci_archive_layout' );
$archive_des_open = '<div class="post-entry penci-category-description penci-archive-description penci-acdes-below">';
if ( get_theme_mod( 'penci_archive_descalign' ) ) {
	$archive_desc_align = ' pcdesc-' . get_theme_mod( 'penci_archive_descalign' );
	$archive_des_open   = '<div class="post-entry penci-category-description penci-archive-description penci-acdes-below' . $archive_desc_align . '">';
}

if ( ! isset( $layout_this ) || empty( $layout_this ) ): $layout_this = 'standard'; endif;
$class_layout = '';
if ( $layout_this == 'classic' ): $class_layout = ' classic-layout'; endif;
?>

<?php if ( ! get_theme_mod( 'penci_disable_breadcrumb' ) && ! get_theme_mod( 'penci_move_breadcrumbs' ) ): ?>
	<?php
	$yoast_breadcrumb = $rm_breadcrumb = '';
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb">', '</div>', false );
	}

	if ( function_exists( 'rank_math_get_breadcrumbs' ) ) {
		$rm_breadcrumb = rank_math_get_breadcrumbs( [
			'wrap_before' => '<div class="container penci-breadcrumb"><nav aria-label="breadcrumbs" class="rank-math-breadcrumb">',
			'wrap_after'  => '</nav></div>',
		] );
	}

	if ( $rm_breadcrumb ) {
		echo $rm_breadcrumb;
	} elseif ( $yoast_breadcrumb ) {
		echo $yoast_breadcrumb;
	} else { ?>
        <div class="container penci-breadcrumb">
            <span><a class="crumb"
                     href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><?php penci_fawesome_icon( 'fas fa-angle-right' ); ?>
			<?php
			echo '<span>';
			echo penci_get_setting( 'penci_trans_archives' );
			echo '</span>';
			?>
        </div>
	<?php } ?>
<?php endif; ?>

<div class="container<?php echo esc_attr( $class_layout );
if ( $sidebar_position != 'no-sidebar' ) : ?> penci_sidebar <?php echo esc_attr( $sidebar_position ); ?><?php endif; ?>">
    <div id="main"
         class="penci-layout-<?php echo esc_attr( $layout_this ); ?><?php if ( get_theme_mod( 'penci_sidebar_sticky' ) ): ?> penci-main-sticky-sidebar<?php endif; ?>">
        <div class="theiaStickySidebar">

			<?php if ( ! get_theme_mod( 'penci_disable_breadcrumb' ) && get_theme_mod( 'penci_move_breadcrumbs' ) ): ?>
				<?php
				$yoast_breadcrumb = $rm_breadcrumb = '';
				if ( function_exists( 'yoast_breadcrumb' ) ) {
					$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-breadcrumb penci-crumb-inside">', '</div>', false );
				}

				if ( function_exists( 'rank_math_get_breadcrumbs' ) ) {
					$rm_breadcrumb = rank_math_get_breadcrumbs( [
						'wrap_before' => '<div class="container penci-breadcrumb penci-crumb-inside"><nav aria-label="breadcrumbs" class="rank-math-breadcrumb">',
						'wrap_after'  => '</nav></div>',
					] );
				}

				if ( $rm_breadcrumb ) {
					echo $rm_breadcrumb;
				} elseif ( $yoast_breadcrumb ) {
					echo $yoast_breadcrumb;
				} else { ?>
                    <div class="container penci-breadcrumb penci-crumb-inside">
                            <span><a class="crumb"
                                     href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><?php penci_fawesome_icon( 'fas fa-angle-right' ); ?>
						<?php
						echo '<span>';
						echo penci_get_setting( 'penci_trans_archives' );
						echo '</span>';
						?>
                    </div>
				<?php } ?>
			<?php endif; ?>

            <div class="archive-box">
                <div class="title-bar">
					<?php
					if ( is_day() ) :
						if ( penci_get_setting( 'penci_trans_daily_archives' ) ):
							echo '<span>';
							echo penci_get_setting( 'penci_trans_daily_archives' );
							echo ' </span>';
						endif;
						printf( wp_kses( __( '<h1 class="page-title">%s</h1>', 'soledad' ), penci_allow_html() ), get_the_date() );
                    elseif ( is_month() ) :
						if ( penci_get_setting( 'penci_trans_monthly_archives' ) ):
							echo '<span>';
							echo penci_get_setting( 'penci_trans_monthly_archives' );
							echo ' </span>';
						endif;
						printf( wp_kses( __( '<h1 class="page-title">%s</h1>', 'soledad' ), penci_allow_html() ), get_the_date( _x( 'F Y', 'monthly archives date format', 'soledad' ) ) );
                    elseif ( is_year() ) :
						if ( penci_get_setting( 'penci_trans_yearly_archives' ) ):
							echo '<span>';
							echo penci_get_setting( 'penci_trans_yearly_archives' );
							echo ' </span>';
						endif;
						printf( wp_kses( __( '<h1 class="page-title">%s</h1>', 'soledad' ), penci_allow_html() ), get_the_date( _x( 'Y', 'yearly archives date format', 'soledad' ) ) );
                    elseif ( is_author() ) :
						echo '<span>';
						echo penci_get_setting( 'penci_trans_author' );
						echo ' </span>';
						printf( wp_kses( __( '<h1 class="page-title">%s</h1>', 'soledad' ), penci_allow_html() ), get_userdata( get_query_var( 'author' ) )->display_name );
                    elseif ( is_tax() ) :
						the_archive_title( '<h1 class="page-title">', '</h1>' );
					else :
						echo '<h1 class="page-title">';
						echo penci_get_setting( 'penci_trans_archives' );
						echo '</h1>';
					endif;
					?>
                </div>
            </div>

			<?php if ( ! get_theme_mod( 'penci_archive_move_desc' ) ) {
				the_archive_description( $archive_des_open, '</div>' );
			} ?>

			<?php echo penci_render_google_adsense( 'penci_archive_ad_above' ); ?>

			<?php
			$queries_data          = get_queried_object();
			$id                    = get_queried_object_id();
			$list_series           = pencipdc_get_podcast_by_category( $id );
			$term_data             = [];
			$term_data['taxonomy'] = $queries_data->taxonomy;

			$thumb = get_theme_mod( 'pencipodcast_items_position', 'top' );
			$col   = get_theme_mod( 'pencipodcast_items_col', 2 );

			?>


            <div class="pencipdc-item-wrapper <?php echo $thumb; ?>-thumb col-<?php echo $col; ?>">
				<?php
				foreach ( $list_series as $list ) {
					$term_data['term_id'] = $list;
					load_template( PENCI_PODCAST_DIR . 'templates/loop/item-style.php', false, $term_data );
				}
				?>
            </div>

			<?php if ( get_theme_mod( 'penci_archive_move_desc' ) ) {
				the_archive_description( $archive_des_open, '</div>' );
			} ?>

			<?php echo penci_render_google_adsense( 'penci_archive_ad_below' ); ?>

        </div>
    </div>
	<?php if ( $sidebar_position != 'no-sidebar' ) : ?>
		<?php get_sidebar(); ?>
		<?php if ( $sidebar_position == 'two-sidebar' ) : get_sidebar( 'left' ); endif; ?>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
