<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop;

if ( ! $product ) {
	return;
}

$args = array(
	'post_type'      => 'product',
	'posts_per_page' => 2,
	'orderby'        => 'rand',
	'order'          => 'desc',
);

$related_query            = new WP_Query( $args );
if ( $related_query->have_posts() ) :
	$product_i = 0;

	echo '<div class="penci-post-related products">';
	echo '<div class="post-title-box"><h4 class="post-box-title">' . esc_html__( 'Related products', 'penci-amp' ) . '</h4></div>';
	echo '<div class="penci-archive__list_product">';
	while ( $related_query->have_posts() ) : $related_query->the_post();
		$product_i ++; ?>
		<?php
		global $post, $product;
		?>
		<article <?php post_class( 'penci-product-item penci-product-item-' . $product_i ); ?>>
			<div class="article_content">
				<?php
				if ( $product->is_on_sale() ) {
					echo apply_filters(
						'woocommerce_sale_flash',
						'<span class="onsale">' . penci_amp_get_setting( 'penci-amp-product-sale' ) . '</span>',
						$post,
						$product
					);
				}

				?>
				<div class="entry-media">
					<a class="penci-link-post" href="<?php the_permalink(); ?>"><?php penci_amp_post_thumbnail( array( 'post' => get_the_ID(), 'size' => 'shop_catalog' ) ); ?></a>
				</div>
				<div class="entry-text">
					<h3 class="penci__product__title">
						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
							<?php the_title() ?>
						</a>
					</h3>
					<div class="woocommerce-price"><?php echo $product->get_price_html(); ?></div>
				</div>
			</div>
		</article>
		<?php
	endwhile;
	echo '</div>';
	echo '</div>';
endif;
wp_reset_postdata();