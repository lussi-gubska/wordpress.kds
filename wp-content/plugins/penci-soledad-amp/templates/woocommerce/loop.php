<?php
global $post;

if ( have_posts() ) :
	$product_i = 0;
	echo '<div class="penci-archive__list_product">';
	while ( have_posts() ) : the_post(); $product_i ++; $product = wc_get_product( get_the_ID() );  ?>
		<?php
		if ( empty( $product ) || ! $product->is_visible() ) {
			return;
		}
		?>
		<article <?php post_class( 'penci-product-item penci-product-item-' . $product_i  ); ?>>
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
					<a class="penci-link-post" href="<?php the_permalink(); ?>"><?php penci_amp_post_thumbnail( array( 'post'   => get_the_ID() ,'size'   => 'shop_catalog' ) ); ?></a>
				</div>
				<div class="entry-text">
					<h3 class="penci__product__title">
						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
							<?php the_title() ?>
						</a>
					</h3>

					<div class="woocommerce-price"><?php echo $product->get_price_html(); ?></div>
				</div>
				<div class="entry-meta entry-meta-hide">
					<?php $this->load_parts( array( 'entry-meta' ) ); ?>
				</div>
			</div>
		</article>
		<?php
	endwhile;
	echo '</div>';
endif;
wp_reset_postdata();