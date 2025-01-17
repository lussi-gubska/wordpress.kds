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
<body  class="<?php echo join( ' ', get_body_class( 'penci-amp-body ' . $sticky_header ) ); ?>">
<?php do_action( 'penci_amp_after_body_tag', $this ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>
<div class="penci-amp-wrapper">
	<?php $this->load_parts( array( 'header-bar' ) ); ?>
	<div class="wrap">
		<?php $this->load_parts( array( 'searchform' ) ); ?>
		<?php if(  function_exists( 'penci_amp_render_google_adsense' ) ) : echo penci_amp_render_google_adsense( 'penci_amp_ad_archive_above_posts' ); endif; ?>
		<?php
		// Show search result only when user searched!
		if ( get_search_query( false ) !== '' ) {
			$this->load_parts( array( 'featured-posts' ) );
			$this->load_parts( array( 'pagination' ) );
		}
		?>
		<?php if(  function_exists( 'penci_amp_render_google_adsense' ) ) : echo penci_amp_render_google_adsense( 'penci_amp_ad_archive_below_posts' ); endif; ?>
		<?php $this->load_parts( array( 'footer' ) ); ?>
	</div>
	<?php
	do_action( 'penci_amp_post_template_footer', $this );
	do_action( 'amp_post_template_footer', $this );
	?>
</div>
</body>
</html>
