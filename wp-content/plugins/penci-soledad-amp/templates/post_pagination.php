<?php
/**
 * Post navigation in single post
 * Create next and prev button to next and prev posts
 *
 * @since 1.0
 */
$prev_post = get_previous_post();
$next_post = get_next_post();
?>
<?php if ( ! empty( $prev_post ) || ! empty( $next_post ) ) : ?>
	<div class="penci-post-pagination">
		<?php if ( ! empty( $prev_post ) ) : ?>
			<div class="prev-post">
				<div class="prev-post-inner">
					<div class="prev-post-title">
						<span><i class="fa fa-angle-left"></i><?php echo esc_html( penci_amp_get_setting( 'penci_content_pre' ) ); ?></span>
					</div>
					<a href="<?php echo esc_url( get_the_permalink( $prev_post->ID ) ); ?>">
						<div class="pagi-text">
							<h5 class="prev-title"><?php echo sanitize_text_field( get_the_title( $prev_post->ID ) ); ?></h5>
						</div>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $next_post ) ) : ?>
			<div class="next-post ">
				<div class="next-post-inner">
					<div class="prev-post-title next-post-title">
						<span><?php echo esc_html( penci_amp_get_setting( 'penci_content_next' ) ); ?><i class="fa fa-angle-right"></i></span>
					</div>
					<a href="<?php echo esc_url( get_the_permalink( $next_post->ID ) ); ?>">
						<div class="pagi-text">
							<h5 class="next-title"><?php echo sanitize_text_field( get_the_title( $next_post->ID ) ); ?></h5>
						</div>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>