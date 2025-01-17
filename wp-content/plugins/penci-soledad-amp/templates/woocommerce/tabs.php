<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>

	<div class="penci-amp-woo-tabs wc-tabs-wrapper">
		<amp-accordion>
			<?php $i = 0; foreach ( $tabs as $key => $tab ) : ?>
				<section class="<?php echo esc_attr( $key ); ?>_tab" <?php echo ( $i < 1 ? 'expanded' : '' ); ?>>
					<h4 class="tab-title-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $tab['title'] ); ?></h4>
					<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content">
						<?php
						if( 'description' == $key ){
							the_content();
						}elseif( 'reviews' == $key ){
							comments_template( );
						}else{
							if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); }
						}
						?>
					</div>
				</section>
			<?php $i ++;  endforeach; ?>
		</amp-accordion>
	</div>

<?php endif; ?>




