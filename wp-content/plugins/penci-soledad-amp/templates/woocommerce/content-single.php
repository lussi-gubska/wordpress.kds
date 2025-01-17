<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php

wc_print_notices();

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php post_class( 'penci-amp-single-product' ); ?>>

	<?php $this->load_parts( array( 'woocommerce/sale-flash','woocommerce/product-image' ) );  ?>

	<div class="summary entry-summary">
		<?php $this->load_parts( array( 'woocommerce/single-title', 'woocommerce/short-description', 'woocommerce/single-meta' ) );  ?>
	</div><!-- .summary -->
	<div class="entry-meta entry-meta-hide">
		<?php $this->load_parts( array( 'entry-meta' ) ); ?>
	</div>
</div><!-- #product-<?php the_ID(); ?> -->
<?php $this->load_parts( array( 'woocommerce/tabs', 'woocommerce/related' ) );  ?>

