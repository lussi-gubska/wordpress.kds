<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'penci_amp_post_template_head', 'penci_amp_header_custom_code' );
if( ! function_exists( 'penci_amp_header_custom_code' ) ){
	function penci_amp_header_custom_code(){
		echo get_theme_mod( 'penci_amp_header_custom_code' );
	}
}

add_action( 'penci_amp_after_body_tag', 'penci_amp_after_body_tag_custom_code' );
if( ! function_exists( 'penci_amp_after_body_tag_custom_code' ) ){
	function penci_amp_after_body_tag_custom_code(){
		echo get_theme_mod( 'penci_amp_afterbody_custom_code' );
	}
}

if( ! function_exists( 'penci_amp_get_permalink' ) ) {
	function penci_amp_get_permalink( $post_id ) {
		$pre_url = apply_filters( 'penci_amp_pre_get_permalink', false, $post_id );

		if ( false !== $pre_url ) {
			return $pre_url;
		}

		$structure = get_option( 'permalink_structure' );
		if ( empty( $structure ) ) {
			$penci_amp_url = add_query_arg( PENCI_AMP_QUERY_VAR, 1, get_permalink( $post_id ) );
		} else {
			$penci_amp_url = trailingslashit( get_permalink( $post_id ) ) . user_trailingslashit( PENCI_AMP_QUERY_VAR, 'single_amp' );
		}

		return apply_filters( 'penci_amp_get_permalink', $penci_amp_url, $post_id );
	}
}

if( !function_exists( 'penci_post_supports_amp' ) ) {
	function penci_post_supports_amp( $post ) {

		if ( ! post_type_supports( $post->post_type, PENCI_AMP_QUERY_VAR ) ) {
			return false;
		}

		if ( post_password_required( $post ) ) {
			return false;
		}

		if ( true === apply_filters( 'penci_amp_skip_post', false, $post->ID, $post ) ) {
			return false;
		}

		return true;
	}
}

/**
 * Are we currently on an AMP URL?
 */
if( !function_exists( 'is_penci_amp_endpoint' ) ) {
	function is_penci_amp_endpoint() {
		if ( 0 === did_action( 'parse_query' ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( "is_penci_amp_endpoint() was called before the 'parse_query' hook was called. This function will always return 'false' before the 'parse_query' hook is called.", 'penci-amp' ) ), '1.0.0' );
		}

		if( ! defined( 'PENCI_AMP_QUERY_VAR' ) ){
			define( 'PENCI_AMP_QUERY_VAR', apply_filters( 'penci_amp_query_var', PENCI_STARTPOINT ) );
		}

		return false !== get_query_var( PENCI_AMP_QUERY_VAR, false );
	}
}

if( !function_exists( 'penci_amp_get_asset_url' ) ) {
	function penci_amp_get_asset_url( $file ) {
		return plugins_url( sprintf( 'assets/%s', $file ), PENCI_AMP_FILE );
	}
}

if( !function_exists( 'penci_amp_get_branding_info' ) ) {
	function penci_amp_get_branding_info( $pos = 'header' ) {

		$info = array(
			'logo'        => '',
			'logo-tag'    => '',
			'name'        => get_bloginfo( 'name', 'display' ),
			'description' => get_bloginfo( 'description', 'display' ),
			'class_img'   => ''
		);

		$id_text_logo = 'penci_amp_header_text_logo';
		$id_img_logo  = 'penci_amp_img_logo';

		if ( 'sidebar' == $pos ) {
			$id_img_logo = 'penci_amp_img_logo_sidebar';
		}

		$name            = penci_amp_get_setting( $id_text_logo );
		$logo            = penci_amp_get_setting( $id_img_logo );
		$mobile_nav_logo = get_theme_mod( 'penci_mobile_nav_logo' );

		// Text logo
		if ( $name ) {
			$info['name'] = $name;
		}

		if ( $logo ) {

			$logo = wp_get_attachment_image_src( $logo, 'full' );

			$logo_width   = isset( $logo[1] ) ? $logo[1] : '';
			$logo_height = isset( $logo[2] ) ? $logo[2] : '';

			if( 'sidebar' == $pos && $logo_width > 230 ){
				$logo_height = ( 230 * $logo_height ) / $logo_width;
				$logo_width = 230;
			}

			if( 'header' == $pos){
				if( $logo_height > 50 ){
					$logo_width = ( 50 * $logo_width ) / $logo_height;
					$logo_height = 50;
				}elseif( $logo_width > 200 ){
					$logo_height = ( 200 * $logo_height ) / $logo_width;
					$logo_width = 200;
				}
			}

			if ( $logo ) {
				$logo = array(
					'src'    => isset( $logo[0] ) ? $logo[0] : '',
					'width'  => $logo_width,
					'height' => $logo_height,
				);
			}

			if ( ! empty( $logo['src'] ) ) {
				$info['logo']        = $logo;
				$info['logo']['alt'] = $info['name'] . ' - ' . $info['description'];

				$info['logo-tag'] = '<amp-img src="' . esc_url( $logo['src'] ) . '" alt="' . esc_attr( $info['name'] . ' - ' . $info['description'] ) . '" height="' . $logo['height'] . '" width="' . $logo['width'] . '"></amp-img>';
			}
		}elseif( $mobile_nav_logo && ! $name ){
			$info['logo-tag'] = '<amp-img src="' . esc_url( $mobile_nav_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" height="50" width="230"></amp-img>';
		}

		return $info;
	}
}

if ( ! function_exists( 'penci_amp_site_icon_url' ) ) {
	function penci_amp_site_icon_url( $logo_size ) {

		$logo_desk = get_theme_mod( 'penci_logo' );
		$logo_amp  = penci_amp_get_setting( 'penci_amp_img_logo' );
		$url_logo  = wp_get_attachment_image_url( $logo_amp, $logo_size );

		if ( empty( $url_logo ) ) {
			if ( $logo_desk ) {
				$url_logo = $logo_desk;
			} else {
				$url_logo = get_template_directory_uri() . '/images/logo.png';
			}

		}
		return $url_logo;
	}
}


if ( ! function_exists( 'penci_amp_css_hidden_attr' ) ) {
	function penci_amp_css_hidden_attr( $option_id, $element ) {
		$style = '';

		if ( ! penci_amp_get_setting( $option_id ) ) {
			$style = $element . '{ display:none }';
		}
		echo $style;
	}
}

if ( ! function_exists( 'penci_amp_css_inlineblock_attr' ) ) {
	function penci_amp_css_inlineblock_attr( $option_id, $element ) {
		$style = '';

		if ( penci_amp_get_setting( $option_id ) ) {
			$style = $element . '{ display:inline-block; }';
		}
		echo $style;
	}
}

if ( ! function_exists( 'is_penci_amp' ) ) {

	function is_penci_amp( $wp_query = null ) {
		
		if( is_admin() ){
			return false;
		}

		if ( $wp_query instanceof WP_Query ) {
			return $wp_query->get( PENCI_STARTPOINT, false );
		}

		if ( did_action( 'template_redirect' ) && ! is_404() ) {

			global $wp_query;
			if ( is_null( $wp_query ) ) {
				return false;
			} else {
				return $wp_query->get( PENCI_STARTPOINT, false );
			}
			
		} else if( get_option( 'permalink_structure' ) ) {
			
			$path = trim( dirname( $_SERVER['SCRIPT_NAME'] ), '/' );

			/**
			 * WPML Support
			 */
			if ( function_exists( 'wpml_get_setting_filter' ) && wpml_get_setting_filter( false, 'language_negotiation_type' ) ) {

				if ( $current_lang = apply_filters( 'wpml_current_language', false ) ) {
					$path .= "/$current_lang";
				}
			}

			$amp_query_var = defined( 'PENCI_AMP_QUERY_VAR' ) ? PENCI_AMP_QUERY_VAR : 'amp';

			return preg_match( "#^/?$path/*(.*?)/$amp_query_var/*$#", $_SERVER['REQUEST_URI'] )
			       ||
			       preg_match( "#^/?$path/*$amp_query_var/*#", $_SERVER['REQUEST_URI'] );
		} else {
			return ! empty( $_GET[ PENCI_STARTPOINT ] );
		}
	}

}

if( !function_exists( 'penci_amp_get_post_meta' ) ) {
	function penci_amp_get_post_meta( ) {

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date( 'M j, Y' ) ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		echo '<span class="entry-meta-item penci-posted-on"><i class="fa fa-clock-o"></i>' . $time_string . '</span>'; // WPCS: XSS OK.
	}
}


if ( ! function_exists( 'penci_amp_menu_fallback' ) ) {
	function penci_amp_menu_fallback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_page_menu();
		} else {
			echo '<ul class="menu">';
			echo '<li class="menu-item-first">';
			echo '<a href="' . esc_url( home_url( '/' ) ) . 'wp-admin/nav-menus.php?action=locations">' . penci_amp_get_setting( 'penci_amp_text_select_menu' ) . '</a>';
			echo '</li>';
			echo '</ul>';
		}
	}
}


if ( ! function_exists( 'penci_amp_get_site_url' ) ) {
	function penci_amp_get_site_url( $path = '', $before = '' ) {
		$link   = site_url( '/' );
		$use_site_address_url = get_theme_mod( 'penci_amp_use_site_address_url' );
		if ( $use_site_address_url ) {
			$link = home_url( '/' );
		}


        $url_format = get_theme_mod( 'penci_amp_url_format' );

		if ( 'simple-point' == $url_format ) {
			return add_query_arg( 'amp', '1', $link );
		}

		$before = trailingslashit( $before );

		if( $before && false === strpos( $link, $before ) ){
			$link .= $before;
		}

		$link .= PENCI_STARTPOINT;

		if ( $path ) {
			$path = ltrim( $path, '/' );
			$link  .= "/$path";
		}
		return $link;
	}
}
if ( ! function_exists( 'penci_amp_get_site_url_end_point' ) ) {
	function penci_amp_get_site_url_end_point( $path = '', $before = '' ) {

		$link   = site_url( '/' );
		$use_site_address_url = get_theme_mod( 'penci_amp_use_site_address_url' );
		if ( $use_site_address_url ) {
			$link = home_url( '/' );
		}


		$before = trailingslashit( $before );

		if( $before && false === strpos( $link, $before ) ){
			$link .= $before;
		}

		if ( $path ) {
			$path = ltrim( $path, '/' );
			$link  .= "$path/";
		}

		$link .= PENCI_STARTPOINT;

		$link = str_replace( '//amp' , '/amp', $link );
		return trailingslashit( $link );
	}
}

if ( ! function_exists( 'penci_amp_post_thumbnail' ) ) {

	function penci_amp_post_thumbnail( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'post'       => null,
			'class'      => 'penci-post-thumb',
			'size'       => 'thumbnail',
			'return_url' => false,
			'before'     => '',
			'after'      => '',
			'echo'       => true,
		) );

		$url       = '';
		$image_alt = esc_html__( 'Default image', 'penci-amp' );

		$image_size_info = penci_amp_get_image_sizes( $args['size'] );

		// Get post thumbnail
		if ( has_post_thumbnail( $args['post'] ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $args['post'] );
			$image_alt         = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
			$url               = wp_get_attachment_image_url( $post_thumbnail_id, $args['size'] );
		}

		if ( empty( $url ) ) {

			$link_url = 'images/no-thumb.jpg';

			if( false !== strpos( $args['size'], 'shop' ) ) {
				$link_url = 'images/no-image-product.jpg';
			}

			$url = penci_amp_get_asset_url( $link_url );
		}

		if ( $args['return_url'] ) {
			return $url;
		}

		$image = sprintf( '<amp-img data-hero class="%s" src="%s" alt="%s" width="%s" height="%s" layout="responsive"></amp-img>',
			esc_attr( $args['class'] ), esc_url( $url ), esc_attr( $image_alt ),
			isset( $image_size_info['width'] ) ? $image_size_info['width'] : '',
			isset( $image_size_info['height'] ) ? $image_size_info['height'] : ''
			);

		$image = $args['before'] . $image . $args['after'];

		if ( ! $args['echo'] ) {
			return $image;
		}

		echo $image;
	}
}

if ( ! function_exists( 'penci_amp_get_image_sizes' ) ) {
	function penci_amp_get_image_sizes( $_size ) {
		global $_wp_additional_image_sizes;

		$info = array(
			'width'  => '480',
			'height' => '320',
		);
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$info['width']  = get_option( "{$_size}_size_w" );
			$info['height'] = get_option( "{$_size}_size_h" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$info = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
			);
		}

		return $info;
	}
}

if( ! function_exists( 'penci_amp_default_theme_logo' ) ) {
	function penci_amp_default_theme_logo() {

		ob_start();
		$site_branding = penci_amp_get_branding_info(  );

		$class_img     = $site_branding['class_img'];
		$class_img     .= ! empty( $site_branding['logo-tag'] ) ? ' penci-amp-site-icon image-logo' : ' text-logo';

		$logo_tag      = isset( $site_branding['logo-tag'] ) ? $site_branding['logo-tag'] : '';
		$branding_name = isset( $site_branding['name'] ) ? $site_branding['name'] : '';

		?>
		<a href="<?php echo esc_url( penci_amp_get_site_url() ); ?>" class="branding <?php echo $class_img; ?> "><?php echo ( $logo_tag ? $logo_tag :  $branding_name ); ?></a>
		<?php

		return ob_get_clean();
	}
}



if( ! function_exists( 'penci_amp_get_font_urls' ) ) {
	function penci_amp_get_font_urls() {
		if( get_theme_mod( 'penci_disable_default_fonts' ) ) {
			return array();
		}

		$_font_urls = array(
			'opensans' => 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=optional',
			'roboto'   => 'https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i&display=optional',
			'teko'     =>  'https://fonts.googleapis.com/css?family=Teko:300,400,500,600,700&display=optional'
		);

		$penci_font_return = array();

		if( function_exists( 'penci_font_browser' ) &&  function_exists( 'penci_get_custom_fonts' )) {
			$penci_font_return = 	array_merge( penci_get_custom_fonts(), penci_font_browser() );
		}

		$font_body = penci_amp_get_setting( 'penci_amp_font_for_body' );
		if( $font_body && ! in_array( $font_body, $penci_font_return ) ){


			$font_family     = str_replace( '"', '', $font_body );
			$font_explo      = explode( ", ", $font_family );
			$font            = isset( $font_explo[0] ) ? $font_explo[0] : '';
			$font_type       = isset( $font_explo[1] ) ? str_replace( ':', ',', $font_explo[1] ) : '';
			$font_replace    = str_replace( ' ', '+', $font );


			$_font_urls[ penci_amp_build_safe_css_class( urlencode( $font ) )] =  'https://fonts.googleapis.com/css?family=' . esc_attr( $font_replace ) . ':' . esc_attr( $font_type ) . '&display=optional';

		}

		$font_title = penci_amp_get_setting( 'penci_amp_font_for_title' );
		if( $font_title && ! in_array( $font_title, $penci_font_return ) ){

			$font_family     = str_replace( '"', '', $font_title );
			$font_explo      = explode( ", ", $font_family );
			$font            = isset( $font_explo[0] ) ? $font_explo[0] : '';
			$font_type       = isset( $font_explo[1] ) ? str_replace( ':', ',', $font_explo[1] ) : '';
			$font_replace    = str_replace( ' ', '+', $font );


			$_font_urls[ penci_amp_build_safe_css_class( urlencode( $font ) )] =  'https://fonts.googleapis.com/css?family=' . esc_attr( $font_replace ) . ':' . esc_attr( $font_type ) . '&display=optional';
		}

		return $_font_urls;
	}

}

if( ! function_exists( 'penci_amp_customize_typo_body' ) ) {
	function penci_amp_customize_typo_body() {
		$font_family = penci_amp_get_setting( 'penci_amp_font_for_body' );
		$font_weight = penci_amp_get_setting( 'penci_amp_font_weight_body' );
		$font_size   = penci_amp_get_setting( 'penci_amp_font_for_size_body' );

		$css = '';
		if ( $font_family ) {
			$css .= sprintf( 'font-family: %s;', penci_amp_google_fonts_parse_attributes( $font_family ) );
		}

		if ( $font_weight ) {
			$css .= sprintf( 'font-weight: %s;', $font_weight );
		}

		if ( $font_size ) {
			$css .= sprintf( 'font-size: %spx;', $font_size );
		}

		return $css;
	}
}

if( !function_exists( 'penci_amp_customize_typo_title' ) ) {
	function penci_amp_customize_typo_title() {
		$font_family = penci_amp_get_setting( 'penci_amp_font_for_title' );
		$font_weight = penci_amp_get_setting( 'penci_amp_font_weight_title' );
		$css = '';
		if ( $font_family ) {
			$css .= sprintf( 'font-family: %s;', penci_amp_google_fonts_parse_attributes( $font_family ) );
		}

		if ( $font_weight ) {
			$css .= sprintf( 'font-weight: %s;', $font_weight );
		}

		return $css;
	}
}

if( !function_exists( 'penci_amp_google_fonts_parse_attributes' ) ) {
	function penci_amp_google_fonts_parse_attributes( $font_family, $return_font_url = false  ) {
		$penci_font_return = array();

		if( function_exists( 'penci_font_browser' ) &&  function_exists( 'penci_get_custom_fonts' )) {
			$penci_font_return = 	array_merge( penci_get_custom_fonts(), penci_font_browser() );
		}

		if ( in_array( $font_family, $penci_font_return ) ) {
			return $font_family;
		}

		$font_family     = str_replace( '"', '', $font_family );
		$font_explo      = explode( ", ", $font_family );
		$font            = isset( $font_explo[0] ) ? $font_explo[0] : '';
		$font_serif      = isset( $font_explo[2] ) ? $font_explo[2] : '';
		$font_type       = isset( $font_explo[1] ) ? str_replace( ':', ',', $font_explo[1] ) : '';
		$font_replace    = str_replace( ' ', '+', $font );

		$font_family_end = "'" . $font . "'";

		if( $font_serif ){
			$font_family_end .= ', ' . $font_serif;
		}

		if( $return_font_url ){
			return array( penci_amp_build_safe_css_class( urlencode( $font ) ), 'https://fonts.googleapis.com/css?family=' . esc_attr( $font_replace ) . ':' . esc_attr( $font_type ) );
		}

		return $font_family_end;
	}
}

if( !function_exists( 'penci_amp_build_safe_css_class' ) ) {
	function penci_amp_build_safe_css_class( $class ) {
		return preg_replace( '/\W+/', '', strtolower( str_replace( ' ', '_', strip_tags( $class ) ) ) );
	}
}

if( ! function_exists( 'penci_amp_get_canonical_url' ) ) {
	function penci_amp_get_canonical_url() {
		global $wp_query, $wp_rewrite;
		$canonical_url = '';

		if ( is_front_page() ) {
			$canonical_url = get_bloginfo( 'url' );

			$current_page = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
			if( $current_page > 1 ) {

				$canonical_url_pre = trailingslashit( $canonical_url );
				$canonical_url = $canonical_url_pre;

				$canonical_url .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/' . $current_page , 'paged' ) : '?paged=' . $current_page;
			}

		}elseif ( is_singular() ) {

			$queried_object = get_queried_object();
			$canonical_url      = get_permalink( $queried_object->ID );
		}elseif ( is_category() || is_tax() || is_tag() ) {

			$term = get_queried_object();

			if ( ! empty( $term ) ) {
				$term_taxonomy = isset( $term->taxonomy ) ? $term->taxonomy : '';
				$term_link     = get_term_link( $term, $term_taxonomy );

				if ( $term_link && ! is_wp_error( $term_link ) ) {

					$canonical_url = trailingslashit( $term_link );

					$current_page = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
					if( $current_page > 1 ) {
						$canonical_url .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/' . $current_page , 'paged' ) : '?paged=' . $current_page;
					}
				}
			}

		} elseif ( is_post_type_archive() ) {

			$post_type = get_query_var( 'post_type' );

			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$canonical_url = get_post_type_archive_link( $post_type );

		} elseif ( is_author() ) {
			$canonical_url = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
		} elseif ( is_day() ) {
			$canonical_url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
		} elseif ( is_month() ) {
			$canonical_url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
		} elseif ( is_year() ) {
			$canonical_url = get_year_link( get_query_var( 'year' ) );
		} else if ( is_search() ) {

			$search_query = get_search_query();
			if ( ! preg_match( '|^page/\d+$|', $search_query ) && ! empty( $search_query ) ) {
				$canonical_url = get_search_link();
			}
		}

		if ( empty( $canonical_url ) ) {
			$_site_url = penci_amp_get_site_url();
			$canonical_url = trailingslashit( $_site_url );

			$current_page = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
			if( $current_page > 1 ) {
				$canonical_url .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/' . $current_page , 'paged' ) : '?paged=' . $current_page;
			}
		}

		return Penci_AMP_Link_Sanitizer::__pre_url_off ( $canonical_url );
	}
}

if ( ! function_exists( 'penci_amp_comment_template' ) ) {
	function penci_amp_comment_template( $comment_template ) {
		global $post;

		$is_penci_amp = is_penci_amp();

		if ( ! $is_penci_amp || get_theme_mod( 'penci_post_hide_comments' ) ) {
			return $comment_template;
		}

		if ( $post->post_type == 'product' ) {
			return PENCI_AMP_DIR . '/templates/woocommerce/single-product-reviews.php';
		}else{
			return PENCI_AMP_DIR . '/templates/reviews.php';
		}
	}
}

add_filter( "comments_template", "penci_amp_comment_template",999 );


if ( ! function_exists( 'penci_amp_comments_template' ) ) {
	function penci_amp_comments_template( $comment, $args, $depth ) {

		$GLOBALS['comment'] = $comment;
		?>
		<div <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
		<div class="thecomment">
			<div class="comment-text">
				<span class="author"><?php echo get_comment_author(); ?></span>
				<span class="date"><i class="fa fa-clock-o"></i><?php printf( esc_html__( '%1$s - %2$s', 'soledad' ), get_comment_date(), get_comment_time() ) ?></span>
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><i class="icon-info-sign"></i> <?php esc_html_e( 'Your comment awaiting approval', 'soledad' ); ?></em>
				<?php endif; ?>
				<div class="comment-content">
				<?php
				$comment = get_comment();
				$comment_text = get_comment_text( $comment );
				if( $comment_text  ){
					$penci_amp_comment_text = new Penci_AMP_Content( $comment_text,array(),
						apply_filters( 'amp_content_sanitizers', array(
							'Penci_AMP_Img_Sanitizer'    => array(),
							'Penci_AMP_Video_Sanitizer'  => array(),
							'Penci_AMP_Style_Sanitizer'  => array(),
							'Penci_AMP_Iframe_Sanitizer' => array( 'add_placeholder' => true ),
						), $comment_text ),
						array( 'comment_content' => true )
					);
					echo make_clickable( $penci_amp_comment_text->get_penci_amp_content() );
				}
				?>
				</div>
			</div>
		</div>
		<?php
	}
}

function penci_amp_woocommerce_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">
		<div class="comment-text">

			<?php
			do_action( 'woocommerce_review_before_comment_text', $comment );

			/**
			 * The woocommerce_review_comment_text hook
			 *
			 * @hooked woocommerce_review_display_comment_text - 10
			 */
			do_action( 'woocommerce_review_comment_text', $comment );

			do_action( 'woocommerce_review_after_comment_text', $comment ); ?>

		</div>
	</div>
	<?php
}


if ( ! function_exists( 'penci_amp_custom_pagination_numbers' ) ) {
	function penci_amp_custom_pagination_numbers( $custom_query = false ) {
		$prev = '<i class="fa fa-angle-left"></i>' . penci_amp_get_setting( 'penci_amp_prev' );
		$next = penci_amp_get_setting( 'penci_amp_next' ) . '<i class="fa fa-angle-right"></i>';

		$max_num_pages = isset( $custom_query->max_num_pages ) ? $custom_query->max_num_pages : '';


		$paged_get = 'paged';
		if( is_front_page() && ! is_home() ):
			$paged_get = 'page';
		endif;

		echo '<div class="penci-amp-pagination">';

		$navigation = '';
		if ( $max_num_pages > 1 ) {

			// Set up paginated links.
			$links = penci_amp_paginate_links( array(
				'mid_size'           => 0,
				'prev_text'          => $prev,
				'next_text'          => $next,
				'current' => max( 1, get_query_var( $paged_get ) ),
				'total' => $max_num_pages,
				'screen_reader_text' => __( 'Posts navigation' )
			) );

			if ( $links ) {
				$navigation = _navigation_markup( $links, 'pagination', __( 'Posts navigation' ) );
			}
		}

		echo $navigation;

		echo '</div>';
	}
}
if ( ! function_exists( 'penci_amp_the_posts_pagination' ) ) {
	function penci_amp_the_posts_pagination( $args = array() ) {
		$navigation = '';

		// Don't print empty markup if there's only one page.
		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
			$args = wp_parse_args( $args, array(
				'mid_size'           => 1,
				'prev_text'          => _x( 'Previous', 'previous set of posts' ),
				'next_text'          => _x( 'Next', 'next set of posts' ),
				'screen_reader_text' => __( 'Posts navigation' ),
			) );

			// Make sure we get a string back. Plain is the next best thing.
			if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
				$args['type'] = 'plain';
			}

			// Set up paginated links.
			$links = penci_amp_paginate_links( $args );

			if ( $links ) {
				$navigation = _navigation_markup( $links, 'pagination', $args['screen_reader_text'] );
			}
		}

		echo $navigation;
	}
}

if ( ! function_exists( 'penci_amp_paginate_links' ) ) {
	function penci_amp_paginate_links( $args = '' ) {
		global $wp_query, $wp_rewrite;

		// Setting up default values based on the current URL.
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$url_parts    = explode( '?', $pagenum_link );

		// Get max pages and current page out of the current query, if available.
		$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		// Append the format placeholder to the base URL.
		$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

		// URL base depends on permalink settings.
		$format = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$defaults = array(
			'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format'             => $format, // ?page=%#% : %#% is replaced by the page number
			'total'              => $total,
			'current'            => $current,
			'aria_current'       => 'page',
			'show_all'           => false,
			'prev_next'          => true,
			'prev_text'          => __( '&laquo; Previous' ),
			'next_text'          => __( 'Next &raquo;' ),
			'end_size'           => 1,
			'mid_size'           => 2,
			'type'               => 'plain',
			'add_args'           => array(), // array of query args to add
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! is_array( $args['add_args'] ) ) {
			$args['add_args'] = array();
		}

		// Merge additional query vars found in the original URL into 'add_args' array.
		if ( isset( $url_parts[1] ) ) {
			// Find the format argument.
			$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );

			// Find the query args of the requested URL.
			wp_parse_str( $url_parts[1], $url_query_args );

			// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
			foreach ( $format_args as $format_arg => $format_arg_value ) {
				unset( $url_query_args[ $format_arg ] );
			}

			$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
		}

		// Who knows what else people pass in $args
		$total = (int) $args['total'];
		if ( $total < 2 ) {
			return;
		}
		$current  = (int) $args['current'];
		$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
		if ( $end_size < 1 ) {
			$end_size = 1;
		}
		$mid_size = (int) $args['mid_size'];
		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}
		$add_args   = $args['add_args'];
		$r          = '';
		$page_links = array();
		$dots       = false;

		if ( $args['prev_next'] && $current && 1 < $current ) :
			$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current - 1, $link );
			if ( $add_args ) {
				$link = add_query_arg( $add_args, $link );
			}
			$link .= $args['add_fragment'];

			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @param string $link The paginated link URL.
			 *
			 *@since 3.0.0
			 *
			 */
			$page_links[] = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
		endif;
		for ( $n = 1; $n <= $total; $n ++ ) :
			if ( $n == $current ) :
				$page_links[] = "<span class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
				$dots         = true;
			else :
				if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :

					$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
					$link = str_replace( '%#%', $n, $link );



					if ( $add_args ) {
						$link = add_query_arg( $add_args, $link );
					}
					$link .= $args['add_fragment'];

					/** This filter is documented in wp-includes/general-template.php */
					$page_links[] = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
					$dots         = true;
				elseif ( $dots && ! $args['show_all'] ) :
					$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
					$dots         = false;
				endif;
			endif;
		endfor;
		if ( $args['prev_next'] && $current && $current < $total ) :
			$link = str_replace( '%_%', $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current + 1, $link );
			if ( $add_args ) {
				$link = add_query_arg( $add_args, $link );
			}
			$link .= $args['add_fragment'];

			/** This filter is documented in wp-includes/general-template.php */
			$page_links[] = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
		endif;
		switch ( $args['type'] ) {
			case 'array' :
				return $page_links;

			case 'list' :
				$r .= "<ul class='page-numbers'>\n\t<li>";
				$r .= join( "</li>\n\t<li>", $page_links );
				$r .= "</li>\n</ul>\n";
				break;

			default :
				$r = join( "\n", $page_links );
				break;
		}

		return $r;
	}
}

/**
 * Return google adsense markup
 *
 * @since 3.2
 */
if ( ! function_exists( 'penci_amp_render_google_adsense' ) ) {
	function penci_amp_render_google_adsense( $option ) {
		if( ! get_theme_mod( $option ) )
			{return '';}

		return '<div class="penci-google-adsense '. $option .'">'. get_theme_mod( $option ) .'</div>';
	}
}