<?php
// Callbacks for adding content to an AMP template
function penci_amp_post_template_init_hooks() {
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_favicon' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_title' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_preload' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_enqueue_static' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_canonical' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_scripts' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_fonts' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_boilerplate_css' );
	add_action( 'penci_amp_post_template_head', 'penci_amp_post_template_add_schemaorg_metadata' );
	add_action( 'penci_amp_post_template_css', 'penci_amp_post_template_add_styles', 99 );
	//add_action( 'penci_amp_post_template_data', 'penci_amp_post_template_add_analytics_script' );
	//add_action( 'penci_amp_post_template_footer', 'penci_amp_post_template_add_analytics_data' );
}

function penci_amp_post_template_add_preload() {
?>
<link href="https://fonts.gstatic.com" rel="dns-prefetch preconnect" crossorigin>
<link href="https://fonts.googleapis.com" rel="dns-prefetch preconnect" crossorigin>
<link as="script" href="https://cdn.ampproject.org/v0.js" rel="preload">
<link rel="preload" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0" as="font" crossorigin>
<?php
}

function penci_amp_post_template_add_enqueue_static( $penci_amp_template ){


	$_font_urls = penci_amp_get_font_urls();
	if( $_font_urls ){
		foreach ( (array)$_font_urls as $_font_handle => $_font_src ){
			wp_enqueue_style( $_font_handle, $_font_src );
		}
	}

	echo '<script async custom-element="amp-user-notification" src="https://cdn.ampproject.org/v0/amp-user-notification-0.1.js"></script>';

	if( is_search( ) ){
		echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
	}

	if ( $ga_code = penci_amp_get_setting( 'penci-amp-analytics' ) || penci_amp_get_setting('penci-amp-analytics-v4') ) {
		echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
	}

	if( is_singular() ){
		echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
		echo '<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>';
	}


	// Show script support ad
	$ad_home_below_slider       = penci_amp_get_setting( 'penci_amp_ad_home_below_slider' );
	$ad_home_below_latest_posts = penci_amp_get_setting( 'penci_amp_ad_home_below_latest_posts' );
	$ad_archive_above_posts     = penci_amp_get_setting( 'penci_amp_ad_archive_above_posts' );
	$ad_archive_below_posts     = penci_amp_get_setting( 'penci_amp_ad_archive_below_posts' );
	$ad_single_above_cat        = penci_amp_get_setting( 'penci_amp_ad_single_above_cat' );
	$ad_single_below_img        = penci_amp_get_setting( 'penci_amp_ad_single_below_img' );
	$ad_single_below_content    = penci_amp_get_setting( 'penci_amp_ad_single_below_content' );

	if( ( !  function_exists( 'is_woocommerce' ) || ( function_exists( 'is_woocommerce' ) && ! is_woocommerce() ) ) ) {
		if ( ( ( is_home() || is_front_page() ) && ( $ad_home_below_slider || $ad_home_below_latest_posts ) ) ||
		     ( ( is_archive() || is_tax() ) && ( $ad_archive_above_posts || $ad_archive_below_posts ) ) ||
		     ( ( is_single() || is_page() ) && ( $ad_single_above_cat || $ad_single_below_img || $ad_single_below_content ) )
		) {

			echo '<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>';
		}
	}
	
	$enable_auto_ads = get_theme_mod( 'penci_amp_enable_auto_ads' );
	if( $enable_auto_ads ){
		echo '<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>';
	}
	
	?>
<link rel="stylesheet" id="font-awesome-css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all">
<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
	<?php
	if( is_front_page() || is_home() || penci_amp_polylang_front_page() ) {
		echo '<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>';
	}elseif( is_single() ) {
		if( 'post' == get_post_type() ){
			if( has_post_format( 'gallery' ) ){
				echo '<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>';
				
			}
		} else {
			$content = get_the_content();
			$check = strpos( $content, 'gallery ids="' );

			if( $check !== false ) {
				echo '<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>';
			}
		}
	}

	$theme_location = '';
	if ( has_nav_menu( 'penci-amp-sidebar-nav' ) ) :
		$theme_location = 'penci-amp-sidebar-nav';
	elseif( has_nav_menu( 'main-menu' ) ):
		$theme_location = 'main-menu';
	endif;
	$menu_items_has_children = false;
	if( $theme_location ) {
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $theme_location ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $theme_location ] );

			if ( $menu && ! is_wp_error( $menu ) && ! isset( $menu_items ) ) {
				$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

				foreach ( (array) $menu_items as $menu_item ) {
					if ( $menu_item->menu_item_parent ) {
						$menu_items_has_children = true;
					}
				}
			}
		}
	}

	if ( $menu_items_has_children || is_singular( 'product' ) ) {
		echo '<script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>';
	}

}

function penci_amp_post_template_add_favicon( $penci_amp_template ) {
	if ( get_theme_mod( 'penci_favicon' ) ){ ?>
<link rel="shortcut icon" href="<?php echo esc_url( get_theme_mod( 'penci_favicon' ) ); ?>" type="image/x-icon" />
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_theme_mod( 'penci_favicon' ) ); ?>">
	<?php }
}
function penci_amp_post_template_add_title( $penci_amp_template ) {
	if ( ! ( defined( 'WPSEO_FILE' ) && is_callable( 'WPSEO_Frontend::get_instance' ) ) ) {
		?>
<title><?php echo esc_html( $penci_amp_template->get( 'document_title' ) ); ?></title>
		<?php
	}
}

function penci_amp_post_template_add_canonical( $penci_amp_template ) {
	$disable_canonical = get_theme_mod( 'penci_amp_disable_canonical' );
	if( ! $disable_canonical ){
	?>
<link rel="canonical" href="<?php echo esc_url( $penci_amp_template->get( 'canonical_url' ) ); ?>" />
	<?php
	}
}

function penci_amp_post_template_add_scripts( $penci_amp_template ) {
	$scripts = $penci_amp_template->get( 'penci_amp_component_scripts', array() );

	$video_amp_scripts = array();
	if ( is_single() && has_post_format( 'video' ) ) {

		$post_id           = get_the_ID();
		$penci_video       = get_post_meta( $post_id, '_format_video_embed', true );
		$penci_amp_content = new Penci_AMP_Content( $penci_video,
			apply_filters( 'penci_amp_content_embed_handlers', array(
				'Penci_AMP_Twitter_Embed_Handler'     => array(),
				'Penci_AMP_YouTube_Embed_Handler'     => array(),
				'Penci_AMP_DailyMotion_Embed_Handler' => array(),
				'Penci_AMP_Vimeo_Embed_Handler'       => array(),
				'Penci_AMP_SoundCloud_Embed_Handler'  => array(),
				'Penci_AMP_Instagram_Embed_Handler'   => array(),
				'Penci_AMP_Vine_Embed_Handler'        => array(),
				'Penci_AMP_Facebook_Embed_Handler'    => array(),
				'Penci_AMP_Pinterest_Embed_Handler'   => array(),
				'Penci_AMP_Gallery_Embed_Handler'     => array(),
			), $penci_video ),
			apply_filters( 'amp_content_sanitizers', array(
				'Penci_AMP_Style_Sanitizer'       => array(),
				'Penci_AMP_Img_Sanitizer'         => array(),
				'Penci_AMP_Video_Sanitizer'       => array(),
				'Penci_AMP_Audio_Sanitizer'       => array(),
				'Penci_AMP_Form_Sanitizer'        => array(),
				'Penci_AMP_Playbuzz_Sanitizer'    => array(),
				'Penci_AMP_Iframe_Sanitizer'      => array(
					'add_placeholder' => true,
				),
				'AMP_Tag_And_Attribute_Sanitizer' => array(),
			), $penci_video )
		);

		$video_amp_scripts = $penci_amp_content->get_penci_amp_scripts();
	}

	if(  $video_amp_scripts && is_array(  $video_amp_scripts ) ) {
		$scripts = array_merge(  $scripts, $video_amp_scripts );
	}

	if( ! empty( $scripts ) ){
		foreach ( (array)$scripts as $element => $script ) :
		$custom_type = ($element == 'amp-mustache') ? 'template' : 'element'; ?>
<script custom-<?php echo esc_attr( $custom_type ); ?>="<?php echo esc_attr( $element ); ?>" src="<?php echo esc_url( $script ); ?>" async></script>
		<?php endforeach;
	}
	?>
<script src="<?php echo esc_url( $penci_amp_template->get( 'penci_amp_runtime_script' ) ); ?>" async></script>
	<?php
}

function penci_amp_post_template_add_fonts( $penci_amp_template ) {
	$font_urls = $penci_amp_template->get( 'font_urls', array() );
	foreach ( $font_urls as $slug => $url ) : ?>
<link rel="stylesheet" href="<?php echo esc_url( $url ); ?>">
	<?php endforeach;
}

function penci_amp_post_template_add_boilerplate_css( $penci_amp_template ) {
	?>
<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<?php
}

function penci_amp_post_template_add_schemaorg_metadata( $penci_amp_template ) {
	$metadata = $penci_amp_template->get( 'metadata' );
	if ( empty( $metadata ) ) {
		return;
	}
	?>
<script type="application/ld+json"><?php echo wp_json_encode( $metadata ); ?></script>
	<?php
}

function penci_amp_post_template_add_styles( $penci_amp_template ) {
	$styles = $penci_amp_template->get( 'post_penci_amp_styles' );
	if ( ! empty( $styles ) ) {
		echo '/* Inline styles */' . PHP_EOL;
		foreach ( $styles as $selector => $declarations ) {
			$declarations = implode( ';', $declarations ) . ';';
			printf( '%1$s{%2$s}', $selector, $declarations );
		}
	}

	if( is_front_page() || is_home() || penci_amp_polylang_front_page() ) {
		$featured_args = array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'meta_query'          => array( // only posts with thumbnail
				'key' => '_thumbnail_id'
			)
		);

		$featured_slider_query = new WP_Query( $featured_args );
		if ( $featured_slider_query->have_posts() ) {
			while ( $featured_slider_query->have_posts() ) {
				$featured_slider_query->the_post();

				$src = penci_amp_post_thumbnail( array(
						'post'       => get_the_ID(),
						'size'       => 'penci-full-thumb',
						'return_url' => true
					)
				);

				echo '#penci-slider-img-holder' . get_the_ID() . '{ background-image:url(' . $src . '); }';
			}

			wp_reset_postdata();
		}
	}elseif( is_single() ) {
		if( has_post_format( 'gallery' ) ) {
			$images = get_post_meta( get_the_ID(), '_format_gallery_images', true );

			foreach ( $images as $image ) {
				$the_image = wp_get_attachment_image_src( $image, 'penci-full-thumb' );
				$image_src = isset( $the_image[0] ) ? $the_image[0] : '';

				echo '#penci-slider-img-holder' . $image . '{ background-image:url(' . esc_url( $image_src ) . '); }';
			}
		}
	}
}

function penci_amp_post_template_add_analytics_script( $data ) {
	if ( ! empty( $data['penci_amp_analytics'] ) ) {
		$data['penci_amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js';
	}
	return $data;
}

function penci_amp_post_template_add_analytics_data( $penci_amp_template ) {
	$analytics_entries = $penci_amp_template->get( 'penci_amp_analytics' );
	if ( empty( $analytics_entries ) ) {
		return;
	}

	foreach ( $analytics_entries as $id => $analytics_entry ) {
		if ( ! isset( $analytics_entry['type'], $analytics_entry['attributes'], $analytics_entry['config_data'] ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'Analytics entry for %s is missing one of the following keys: `type`, `attributes`, or `config_data` (array keys: %s)', 'penci-amp' ), esc_html( $id ), esc_html( implode( ', ', array_keys( $analytics_entry ) ) ) ), '0.3.2' );
			continue;
		}
		$script_element = Penci_AMP_HTML_Utils::build_tag( 'script', array(
			'type' => 'application/json',
		), wp_json_encode( $analytics_entry['config_data'] ) );

		$penci_amp_analytics_attr = array_merge( array(
			'id' => $id,
			'type' => $analytics_entry['type'],
		), $analytics_entry['attributes'] );

		echo Penci_AMP_HTML_Utils::build_tag( 'amp-analytics', $penci_amp_analytics_attr, $script_element );
	}
}
