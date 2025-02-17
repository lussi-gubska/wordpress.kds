<?php
/**
 * Template part for Slider Style 35
 */

$post_thumb_size = ! empty( $post_thumb_size ) ? $post_thumb_size : 'penci-slider-full-thumb';
$penci_is_mobile = penci_is_mobile();

$post_thumb_msize = ! empty( $post_thumb_size_mobile ) ? $post_thumb_size_mobile : 'penci-masonry-thumb';

?>
<?php if ( $feat_query->have_posts() ) : while ( $feat_query->have_posts() ) :
$feat_query->the_post(); ?>
<div class="item swiper-slide swiper-mark-item">
	<?php do_action( 'penci_bookmark_post' ); ?>
	<?php if ( ! $disable_lazyload ) { ?>
    <div class="penci-swiper-mask penci-image-holder <?php echo penci_classes_slider_lazy(); ?>"
         data-bgset="<?php echo penci_image_srcset( get_the_ID(), $post_thumb_size, $post_thumb_msize ); ?>"
         data-href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
		<?php } else { ?>
        <div class="penci-swiper-mask penci-image-holder"
             style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), $penci_is_mobile ? $post_thumb_msize : $post_thumb_size ); ?>');"
             data-href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
			<?php } ?>
			<?php if ( ! $center_box ): ?>
                <div class="penci-featured-content-right">
                    <div class="feat-text-right">
						<?php if ( ! $hide_categories ): ?>
                            <div class="cat featured-cat"><?php penci_category( '' ); ?></div>
						<?php endif; ?>
                        <h3><a title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"
                               href="<?php the_permalink() ?>"><?php echo wp_trim_words( wp_strip_all_tags( get_the_title() ), $slider_title_length, '...' ); ?></a>
                        </h3>
						<?php if ( $settings['cspost_enable'] || ( get_the_excerpt() && ! $hide_meta_excerpt ) || ! $hide_meta_comment || ! $meta_date_hide || $show_viewscount ): ?>
                            <div class="featured-content-excerpt">
								<?php if ( ! $hide_meta_comment || ! $meta_date_hide || $show_viewscount || $show_meta_author ): ?>
                                    <div class="feat-meta">
										<?php if ( $show_meta_author ): ?>
                                            <span class="feat-author"><?php echo penci_get_setting( 'penci_trans_by' ); ?> <a
                                                        href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>
										<?php endif; ?>
										<?php if ( ! $meta_date_hide ): ?>
                                            <span class="feat-time"><?php penci_soledad_time_link(); ?></span>
										<?php endif; ?>
										<?php if ( ! $hide_meta_comment ): ?>
                                            <span class="feat-comments"><a
                                                        href="<?php comments_link(); ?> "><?php comments_number( '0 ' . penci_get_setting( 'penci_trans_comments' ), '1 ' . penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ) ); ?></a></span>
										<?php endif; ?>
										<?php
										if ( $show_viewscount ) {
											echo '<span class="feat-countviews">';
											echo penci_get_post_views( get_the_ID() );
											echo ' ' . penci_get_setting( 'penci_trans_countviews' );
											echo '</span>';
										}
										?>
										<?php echo penci_show_custom_meta_fields( [
											'validator' => isset( $settings['cspost_enable'] ) ? $settings['cspost_enable'] : '',
											'keys'      => isset( $settings['cspost_cpost_meta'] ) ? $settings['cspost_cpost_meta'] : '',
											'acf'       => isset( $settings['cspost_cpost_acf_meta'] ) ? $settings['cspost_cpost_acf_meta'] : '',
											'label'     => isset( $settings['cspost_cpost_meta_label'] ) ? $settings['cspost_cpost_meta_label'] : '',
											'divider'   => isset( $settings['cspost_cpost_meta_divider'] ) ? $settings['cspost_cpost_meta_divider'] : '',
										] ); ?>
                                    </div>
								<?php endif; ?>
								<?php if ( get_the_excerpt() && ! $hide_meta_excerpt ): ?>
									<?php
									if ( get_theme_mod( 'penci_excerptcharac' ) ) {
										the_excerpt();
									} else {
										$excerpt_length = get_theme_mod( 'penci_post_excerpt_length', 30 );
										penci_the_excerpt( $excerpt_length );
									}
									?>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
                        <div class="penci-featured-slider-button">
                            <a href="<?php the_permalink() ?>"><?php echo penci_get_setting( 'penci_trans_read_more' ); ?></a>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
	<?php endwhile;
	wp_reset_postdata();
	endif; ?>
