<?php
$output = $el_class = $css_animation = $css = '';

$_design_style = $_use_img = $_image = '';
$image_width   = $image_height = $image_mar_top = $image_mar_bottom = $responsive_spacing = '';
$_heading      = $_subheading = $_price = $_unit = '';
$_btn_text     = $_btn_link = $_featured = $min_height = '';

$_heading_mar_bottom = $_subheading_mar_b = $_price_mar_bottom = '';
$_unit_mar_bottom    = $_features_martop = $_features_bottom = $item_fea_bottom = '';
$btn_mar_top         = $btn_width = $btn_height = '';

$bg_color          = $border_color = '';
$_heading_color    = $_heading_fsize = $_heading_typo = '';
$_subheading_color = $_subheading_fsize = $_subheading_typo = '';
$_price_color      = $_price_fsize = $_price_typo = '';
$_unit_color       = $_unit_fsize = $_unit_typo = '';
$features_color    = $features_fsize = $features_typo = '';
$btn_bgcolor       = $btn_borcolor = $btn_text_color = '';
$btn_hbgcolor      = $btn_hborcolor = $btn_text_hcolor = '';
$_btn_fsize        = $_btn_typo = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css_class = 'penci-pricing-table';
$css_class .= ' penci-pricing-item';
$css_class .= 'yes' == $_featured ? ' penci-pricing_featured' : '';
$css_class .= $_design_style ? ' penci-pricing-' . esc_attr( $_design_style ) : '';

$class_to_filter = vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class       .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$block_id        = Penci_Vc_Helper::get_unique_id_block( 'pricing_table' );
wp_enqueue_style('penci-paywall');
if ( class_exists( '\WPInv_Item' ) ) {
	$product                 = new \WPInv_Item( $atts['paywall_list'] );
	$settings['_heading']    = $product->get_title();
	$settings['_subheading'] = $product->get_description();
	$settings['_price']      = $product->get_the_price();
	if ( $product->is_recurring() ) {
		$settings['_unit'] = pencipw_duration_text( $product->get_recurring_interval(), $product->get_recurring_period() );
	}
	$button_html = '';
	if ( $settings['_btn_text'] ) {
		$button_html = '<div class="penci-pricing-pbutton">' . do_shortcode( '[getpaid item=' . $product->ID . ' button="Buy Now"]' ) . '</div>';
		$button_html = str_replace( 'btn btn-primary getpaid-payment-button', 'getpaid-payment-button penci-pricing-btn penci-button', $button_html );
	}
} elseif ( class_exists( 'WooCommerce' ) ) {
	$product     = wc_get_product( $atts['paywall_list'] );
	$_heading    = $product->get_title();
	$_subheading = $product->get_short_description();
	$_price      = $product->get_price_html();
	$total       = get_post_meta( $atts['paywall_list'], '_pencipw_total', true );
	$duration    = get_post_meta( $atts['paywall_list'], '_pencipw_duration', true );


	if ( $product->is_type( 'paywall_subscribe' ) ) {
		$_unit = pencipw_duration_text( $total, $duration );
	} elseif ( $product->is_type( 'paywall_unlock' ) ) {
		$_unit = $product->get_total_unlock() . ' ' . __( 'posts', 'penci-paywall' );
	} elseif ( $product->is_type( 'subscription' ) ) {
		$total             = $product->__get( 'subscription_period_interval' );
		$duration          = $product->__get( 'subscription_period' );
		$_unit = pencipw_duration_text( $total, $duration );
	}

	$button_html = '';
	if ( $_btn_text ) {
		$button_html = '<a href=\'\' class=\'button penci-pricing-btn penci-button\' data-product_id=\'' . esc_attr( $atts['paywall_list'] ) . '\' data-recurring=\'no\'><span>' . $_btn_text . '</span><i class=\'fa fa-spinner fa-pulse\' style=\'display: none;\'></i></a>';
	}
}

?>
    <div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $css_class ); ?>">
		<?php
		if ( 'yes' == $_featured && 's2' == $_design_style ) {
			echo '<span class="penci-pricing-ribbon">' . penci_icon_by_ver( 'fas fa-star' ) . '</span>';
		}
		?>
        <div class="penci-block_content">
			<?php
			echo '<div class="penci-pricing-header">';
			if ( $_image && 'yes' == $_use_img ) {
				echo '<div class="penci-pricing-image"><img src="' . esc_url( wp_get_attachment_url( $_image ) ) . '"></div>';
			}
			if ( $_heading ) {
				echo '<div class="penci-pricing-title">' . do_shortcode( $_heading ) . '</div>';
			}

			if ( $_subheading ) {
				echo '<div class="penci-pricing-subtitle">' . do_shortcode( $_subheading ) . '</div>';
			}
			echo '</div>';

			if ( $_price || $_unit ) {
				echo '<div class="penci-price-unit">';

				if ( $_price ) {
					echo '<span class="penci-pricing-price">' . do_shortcode( $_price ) . '</span>';
				}

				if ( $_unit ) {
					echo '<span class="penci-pricing-unit">' . do_shortcode( $_unit ) . '</span>';
				}

				echo '</div>';
			}

			if ( $content ) {
				$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
				$content = do_shortcode( shortcode_unautop( $content ) );

				echo '<div class="penci-pricing-featured">' . $content . '</div>';
			}

			echo $button_html;
			?>
        </div>
    </div>
<?php
$css_custom        = '';
$id_pricing_table  = '#' . $block_id;
$id_pricing_table2 = 'body:not(.pcdm-enable) #' . $block_id;

// General
if ( $bg_color ) {
	$css_custom .= $id_pricing_table2 . '.penci-pricing-item{ background-color:' . esc_attr( $bg_color ) . '; }';
}
if ( $border_color ) {
	$css_custom .= $id_pricing_table2 . '.penci-pricing-item{ border-color:' . esc_attr( $border_color ) . '; }';
}
if ( $min_height ) {
	$css_custom .= $id_pricing_table2 . '.penci-pricing-item{ min-height:' . esc_attr( $min_height ) . 'px; }';
}

// Image
if ( $_image && $_use_img ) {
	$custom_img_css = '';

	if ( $image_width ) {
		$custom_img_css .= 'width:' . esc_attr( $image_width ) . ';';
	}
	if ( $image_height ) {
		$custom_img_css .= 'height:' . esc_attr( $image_height ) . ';';
	}
	if ( $image_mar_top ) {
		$custom_img_css .= 'margin-top:' . esc_attr( $image_mar_top ) . ';';
	}
	if ( $image_height ) {
		$custom_img_css .= 'margin-bottom:' . esc_attr( $image_mar_bottom ) . ';';
	}

	if ( $custom_img_css ) {
		$css_custom .= $id_pricing_table . ' .penci-pricing-image{' . $custom_img_css . '}';
	}
}
// Heading
if ( $_heading_color ) {
	$css_custom .= $id_pricing_table2 . ' .penci-pricing-title{ color:' . esc_attr( $_heading_color ) . '; }';
}
if ( $_heading_mar_bottom ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-title{ margin-bottom:' . esc_attr( $_heading_mar_bottom ) . '; }';
}

$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $_heading_fsize,
	'font_style' => $_heading_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-title{ %s }',
) );

// Sub heading
if ( $_subheading_color ) {
	$css_custom .= $id_pricing_table2 . ' .penci-pricing-subtitle{ color:' . esc_attr( $_subheading_color ) . '; }';
}
if ( $_subheading_mar_b ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-subtitle{ margin-bottom:' . esc_attr( $_subheading_mar_b ) . '; }';
}
$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $_subheading_fsize,
	'font_style' => $_subheading_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-subtitle{ %s }',
) );

// Pricing
if ( $_price_color ) {
	$css_custom .= $id_pricing_table2 . ' .penci-pricing-price{ color:' . esc_attr( $_price_color ) . '; }';
}
if ( $_price_mar_bottom ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-price{ margin-bottom:' . esc_attr( $_price_mar_bottom ) . '; }';
}
$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $_price_fsize,
	'font_style' => $_price_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-price{ %s }',
) );

// Unit
if ( $_unit_color ) {
	$css_custom .= $id_pricing_table2 . ' .penci-pricing-unit{ color:' . esc_attr( $_unit_color ) . '; }';
}
if ( $_unit_mar_bottom ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-unit{ margin-bottom:' . esc_attr( $_unit_mar_bottom ) . '; }';
}
$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $_unit_fsize,
	'font_style' => $_unit_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-unit{ %s }',
) );
// Featured
if ( $features_color ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-featured li,';
	$css_custom .= $id_pricing_table2 . ' .penci-pricing-featured{ color:' . esc_attr( $features_color ) . '; }';
}
if ( $_features_martop ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-featured{ margin-top:' . esc_attr( $_features_martop ) . '; }';
}
if ( $_features_bottom ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-featured{ margin-bottom:' . esc_attr( $_features_bottom ) . '; }';
}
if ( $item_fea_bottom ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-featured li{ margin-bottom:' . esc_attr( $item_fea_bottom ) . '; }';
}
$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $features_fsize,
	'font_style' => $features_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-featured{ %s }',
) );

// Button
$btn_custom_css = '';
if ( $btn_mar_top ) {
	$btn_custom_css .= 'margin-top:' . esc_attr( $btn_mar_top ) . ';';
}
if ( $btn_width ) {
	$btn_custom_css .= 'width:' . esc_attr( $btn_width ) . ';';
}
if ( $btn_height ) {
	$btn_custom_css .= 'height:' . esc_attr( $btn_height ) . ';';
}

if ( $btn_bgcolor ) {
	$btn_custom_css .= 'background-color:' . esc_attr( $btn_bgcolor ) . ';';
}
if ( $btn_borcolor ) {
	$btn_custom_css .= 'border-color:' . esc_attr( $btn_borcolor ) . ';';
}
if ( $btn_text_color ) {
	$btn_custom_css .= 'color:' . esc_attr( $btn_text_color ) . ';';
}
if ( $btn_custom_css ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-btn{' . esc_attr( $btn_custom_css ) . '}';
}
$btn_custom_hover_css = '';
if ( $btn_hbgcolor ) {
	$btn_custom_hover_css .= 'background-color:' . esc_attr( $btn_hbgcolor ) . ';';
}
if ( $btn_hborcolor ) {
	$btn_custom_hover_css .= 'border-color:' . esc_attr( $btn_hborcolor ) . ';';
}
if ( $btn_text_hcolor ) {
	$btn_custom_hover_css .= 'color:' . esc_attr( $btn_text_hcolor ) . ';';
}
if ( $btn_custom_hover_css ) {
	$css_custom .= $id_pricing_table . ' .penci-pricing-btn:hover{' . esc_attr( $btn_custom_hover_css ) . '}';
}
$css_custom .= Penci_Vc_Helper::vc_google_fonts_parse_attributes( array(
	'font_size'  => $_btn_fsize,
	'font_style' => $_btn_typo,
	'template'   => $id_pricing_table . ' .penci-pricing-btn{ %s }',
) );

if ( $responsive_spacing ) {
	$css_custom .= penci_extract_spacing_style( $id_pricing_table, $responsive_spacing );
}

if ( $css_custom ) {
	echo '<style>';
	echo $css_custom;
	echo '</style>';
}
