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
<body  class="<?php echo join( ' ', get_body_class( 'penci-amp-body penci-amp-product-single' . $sticky_header ) ); ?>">
<?php do_action( 'penci_amp_after_body_tag', $this ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>
<div class="penci-amp-wrapper">
	<?php $this->load_parts( array( 'header-bar' ) ); ?>
	<div class="wrap">
		<?php while ( have_posts() ) : the_post(); ?>
		<?php $this->load_parts( array( 'woocommerce/content-single' ) ); ?>

		<?php endwhile; // end of the loop. ?>
		<?php $this->load_parts( array( 'footer' ) ); ?>
	</div>
	<?php
	do_action( 'penci_amp_post_template_footer', $this );
	do_action( 'amp_post_template_footer', $this );
	?>
</div>
</body>
</html>
