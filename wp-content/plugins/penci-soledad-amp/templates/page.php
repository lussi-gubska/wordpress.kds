<!doctype html>
<html ⚡ <?php echo Penci_AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ); ?>>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<?php do_action( 'penci_amp_post_template_head', $this ); ?>
	<?php do_action( 'amp_post_template_head', $this ); ?>

	<style amp-custom>
		<?php $this->load_parts( array( 'style' ) ); ?>
		<?php do_action( 'penci_amp_post_template_css', $this ); ?>
		<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>

<?php $sticky_header = penci_amp_get_setting( 'penci_amp_sticky_header' ) ? ' sticky-header' : ''; ?>
<body  class="<?php echo join( ' ', get_body_class( 'penci-amp-body penci-amp-single' . $sticky_header ) ); ?>">
<?php do_action( 'penci_amp_after_body_tag', $this ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>
<div class="penci-amp-wrapper">
	<?php $this->load_parts( array( 'header-bar' ) ); ?>
	<div class="wrap">
		<article class="amp-wp-article">
			<header class="amp-wp-article-header">
				<?php if(  function_exists( 'penci_amp_render_google_adsense' ) ) : echo penci_amp_render_google_adsense( 'penci_amp_ad_single_above_cat' ); endif; ?>
				<?php $this->load_parts( array( 'meta-taxonomy' ) ); ?>
				<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>
				<div class="penci-amp-entry-meta">
					<?php $this->load_parts( array( 'entry-meta' ) ); ?>
				</div>
			</header>
			<?php $this->load_parts( array( 'featured-image' ) ); ?>
			<?php if(  function_exists( 'penci_amp_render_google_adsense' ) ) : echo penci_amp_render_google_adsense( 'penci_amp_ad_single_below_img' ); endif; ?>
			<div class="amp-wp-article-content">
				<?php echo $this->get( 'post_penci_amp_content' ); // amphtml content; no kses ?>
			</div>
			<?php if(  function_exists( 'penci_amp_render_google_adsense' ) ) : echo penci_amp_render_google_adsense( 'penci_amp_ad_single_below_content' ); endif; ?>
			<footer class="amp-wp-article-footer">
				<?php $this->load_parts( array( 'meta-tag', 'social-share' ) ); ?>
			</footer>

		</article>
		<?php $this->load_parts( array( 'footer' ) ); ?>
	</div>
	<?php
	do_action( 'penci_amp_post_template_footer', $this );
	do_action( 'amp_post_template_footer', $this );
	?>
</div>
</body>
</html>
