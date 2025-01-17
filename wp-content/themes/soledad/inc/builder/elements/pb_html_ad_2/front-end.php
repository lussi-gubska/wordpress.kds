<?php
$ads_html = penci_get_builder_mod( 'penci_header_builder_pb_html_2_name', false );
if ( empty( $ads_html ) ) {
	return false;
}
?>

<div class="penci-builder-element penci-html-ads penci-html-ads-2">
	<?php echo do_shortcode( $ads_html ); ?>
</div>
