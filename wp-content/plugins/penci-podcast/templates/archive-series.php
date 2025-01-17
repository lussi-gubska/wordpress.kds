<?php
/**
 * The template for displaying archive pages
 *
 * @package Wordpress
 * @since 1.0
 */
get_header();

/* Archive layout */
$layout_this = get_theme_mod( 'penci_archive_layout' );
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
	if ( penci_get_setting( 'penci_sidebar_archive' ) ) : ?> penci_sidebar left-sidebar<?php endif; ?>">
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

				<?php echo penci_render_google_adsense( 'penci_archive_ad_above' ); ?>

				<?php if ( have_posts() ) : ?>
					<?php
					echo '<div class="pencipdc_podcast pencipdc_postblock_episode_detail pencipdc_postblock pencipdc_module_hook">';
					echo '<ul class="pencipdc_block_container penci-wrapper-data penci-grid">';

					while ( have_posts() ) : the_post();
						load_template( PENCI_PODCAST_DIR . 'templates/loop/item-series.php', false );
					endwhile;

					echo '</ul>';
					echo '</div>';

					penci_soledad_archive_pag_style( $layout_this );
					?>
				<?php endif;
				wp_reset_postdata(); /* End if of the loop */ ?>

				<?php echo penci_render_google_adsense( 'penci_archive_ad_below' ); ?>

            </div>
        </div>
        <div class="penci-sidebar-right penci-sidebar-content penci-sticky-sidebar" id="sidebar">
            <div class="theiaStickySidebar">
                <div class="penci-podcast-series-info">
					<?php
					$id          = get_queried_object_id();
					$term        = get_term( $id );
					$featured_id = get_option( "pencipdc_series_$id" );
					$author_id   = pencipdc_get_podcast_author( $id );
					?>
					<?php if ( get_theme_mod( 'pencipodcast_series_show_featured', true ) && isset( $featured_id['featured_img'] ) && $featured_id['featured_img'] ): ?>
                        <div class="pcpd-featured-thumb">
                            <div class="penci-image-holder penci-lazy"
                                 data-bgset="<?php echo wp_get_attachment_image_url( $featured_id['featured_img'][0], 'penci-thumb' ); ?>"></div>
                        </div>
					<?php endif; ?>
                    <h1><?php single_term_title( '' ); ?></h1>

					<?php
					if ( get_theme_mod( 'pencipodcast_series_enable_post_excerpt', true ) ):
						the_archive_description( '<div class="post-entry penci-category-description penci-archive-description">', '</div>' );
					endif;
					?>

                    <div class="penci-podcast-series-meta grid-post-box-meta">
						<?php if ( get_theme_mod( 'pencipodcast_series_show_podcast_author', true ) ): ?>
                            <span class="otherl-date-author author-italic author vcard"><?php echo penci_get_setting( 'penci_trans_by' ); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) :
									coauthors_posts_links();
								else: ?>
                                    <a class="author-url url fn n"
                                       href="<?php echo get_author_posts_url( $author_id ); ?>"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></a>
								<?php endif; ?></span>
						<?php endif; ?>
						<?php if ( get_theme_mod( 'pencipodcast_series_show_podcast_total_episode', true ) ): ?>
                            <span>
                            <?php echo sprintf( _n( '%s Episode', '%s Episodes', $term->count ), number_format_i18n( $term->count ) ); ?>
                        </span>
						<?php endif; ?>
                    </div>
                    <div class="penci_podcast_post_option">
						<?php if ( get_theme_mod( 'pencipodcast_series_show_subscribe', true ) ): ?>
                            <div class="follow-wrapper"><a
                                        href="<?php echo pencipdc_podcast_feed_link( $id, $term->taxonomy ); ?>"><i
                                            class="fa fa-rss"></i><span>Subscribe</span></a></div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>