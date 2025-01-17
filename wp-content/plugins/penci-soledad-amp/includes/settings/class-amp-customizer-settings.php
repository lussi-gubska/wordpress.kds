<?php

class Penci_AMP_Customizer_Settings {
	private static function get_stored_options() {
		return get_option( 'penci_amp_customizer', array() );
	}

	public static function get_settings() {
		$settings = self::get_stored_options();

		return apply_filters( 'penci_amp_customizer_get_settings', $settings );
	}

	public static function init() {
		add_action( 'penci_amp_customizer_init', array( __CLASS__, 'init_customizer' ) );
	}

	public static function init_customizer() {
		add_action( 'penci_amp_customizer_register_settings', array( __CLASS__, 'register_customizer_settings' ) );
		add_action( 'penci_amp_customizer_register_ui', array( __CLASS__, 'register_customizer_ui' ) );
	}

	public static function register_customizer_settings( $wp_customize ) {

		require_once( PENCI_AMP_DIR . '/includes/settings/sanitizer.php' );
		$sanitizer = new Penci_AMP_Customize_Sanitizer();

		$wp_customize->add_setting( 'penci_amp_header_text_logo', array(
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		if ( $wp_customize->selective_refresh ) {

			$wp_customize->selective_refresh->add_partial( 'penci_amp_header_text_logo', array(
				'settings'            => array( 'penci_amp_header_text_logo' ),
				'selector'            => '.branding',
				'render_callback'     => 'penci_amp_default_theme_logo',
				'container_inclusive' => true,
			) );
		}

		$wp_customize->add_setting( 'penci_amp_img_logo', array(
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		if ( $wp_customize->selective_refresh ) {

			$wp_customize->selective_refresh->add_partial( 'penci_amp_img_logo', array(
				'settings'            => array( 'penci_amp_img_logo' ),
				'selector'            => '.branding',
				'render_callback'     => 'penci_amp_default_theme_logo',
				'container_inclusive' => true,
			) );
		}

		$wp_customize->add_setting( 'penci_amp_show_search', array(
			'default'           => penci_amp_default_setting( 'penci_amp_show_search' ),
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_sticky_header', array(
			'default'           => penci_amp_default_setting( 'penci_amp_sticky_header' ),
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_header_custom_code', array(
			'default' => '',
		) );

		$wp_customize->add_setting( 'penci_amp_afterbody_custom_code', array(
			'default' => '',
		) );

		$wp_customize->add_setting( 'penci_amp_disable_canonical', array(
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'default'           => false,
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_enable_auto_ads', array(
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'default'           => false,
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_home_show_slider', array(
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'default'           => penci_amp_default_setting( 'penci_amp_home_show_slider' ),
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_home_show_textlasestp', array(
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'default'           => penci_amp_default_setting( 'penci_amp_home_show_textlasestp' ),
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_setting( 'penci_amp_fcat_below_lposts', array(
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
			'default'           => penci_amp_default_setting( 'penci_amp_fcat_below_lposts' ),
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_show_on_front', array(
			'default'   => penci_amp_default_setting( 'penci_amp_show_on_front' ),
			'transport' => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_admin_page_on_front', array(
			'default'           => false,
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_home_listing', array(
			'default'   => penci_amp_default_setting( 'penci_amp_home_listing' ),
			'transport' => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_featured_cat_listing', array(
			'default'   => penci_amp_default_setting( 'penci_amp_featured_cat_listing' ),
			'transport' => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_home_featured_cat', array(
			'default'   => '',
			'transport' => '',
		) );

		$wp_customize->add_setting( 'penci_amp_home_featured_cat_numbers', array(
			'default'           => 5,
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		$home_checklist = array(
			'penci_amp_home_show_pauthor'    => esc_html__( 'Show Post Author', 'penci-amp' ),
			'penci_amp_home_show_pdate'      => esc_html__( 'Show Post Date', 'penci-amp' ),
			'penci_amp_home_show_pcomments'  => esc_html__( 'Show Comment Count', 'penci-amp' ),
			'penci_amp_home_show_pview'      => esc_html__( 'Show Post Views', 'penci-amp' ),
			'penci_amp_home_show_excrept'    => esc_html__( 'Show Post Excrept', 'penci-amp' ),
			'penci_amp_home_show_readmore'   => esc_html__( 'Show Button Read more', 'penci-amp' ),
			'penci_amp_home_show_pagination' => esc_html__( 'Show Paginated Navigation', 'penci-amp' ),
		);

		foreach ( $home_checklist as $id_option => $label_option ) {
			$wp_customize->add_setting( $id_option, array(
				'sanitize_callback' => array( $sanitizer, 'checkbox' ),
				'default'           => penci_amp_default_setting( $id_option ),
				'transport'         => 'postMessage',
				'transport'         => 'postMessage',
			) );
		}

		$wp_customize->add_setting( 'penci_amp_archive_listing', array(
			'default' => penci_amp_default_setting( 'penci_amp_archive_listing' ),
		) );

		$post_checklist = array(
			'penci_amp_dis_desc_sanitizer'  => esc_html__( 'Disable Sanitizer For Description', 'penci-amp' ),
			'penci_amp_posts_show_thumb'    => esc_html__( 'Show Thumbnail', 'penci-amp' ),
			'penci_amp_posts_show_pmeta'    => esc_html__( 'Show Post Meta', 'penci-amp' ),
			'penci_amp_posts_show_pcat'     => esc_html__( 'Show Post Categories', 'penci-amp' ),
			'penci_amp_posts_show_pauthor'  => esc_html__( 'Show Author Name', 'penci-amp' ),
			'penci_amp_posts_show_pdate'    => esc_html__( 'Show Post Date', 'penci-amp' ),
			'penci_amp_posts_show_pcomment' => esc_html__( 'Show Comment Count', 'penci-amp' ),
			'penci_amp_posts_show_pview'    => esc_html__( 'Show Post Views', 'penci-amp' ),
			'penci_amp_posts_show_ptag'     => esc_html__( 'Show Post Tags', 'penci-amp' ),
			'penci_amp_posts_show_comment'  => esc_html__( 'Show Comments', 'penci-amp' ),
			'penci_amp_posts_show_show_pag' => esc_html__( 'Show Pagination', 'penci-amp' ),
			'penci_amp_posts_show_share'    => esc_html__( 'Show Share Box', 'penci-amp' ),

			'penciamp_hide_share_facebook'    => esc_html__( 'Hide Facebook Share Button', 'penci-amp' ),
			'penciamp_hide_share_twitter'     => esc_html__( 'Hide Twitter Share Button', 'penci-amp' ),
			'penciamp_hide_share_pinterest'   => esc_html__( 'Hide Pinterest Share Button', 'penci-amp' ),
			'penciamp_hide_share_linkedin'    => esc_html__( 'Hide Linkedin Share Button', 'penci-amp' ),
			'penciamp_hide_share_tumblr'      => esc_html__( 'Hide Tumblr Share Button', 'penci-amp' ),
			'penciamp_hide_share_vk'          => esc_html__( 'Hide VK Share Button', 'penci-amp' ),
			'penciamp_hide_share_ok'          => esc_html__( 'Hide Odnoklassniki Share Button', 'penci-amp' ),
			'penciamp_hide_share_reddit'      => esc_html__( 'Hide Reddit Share Button', 'penci-amp' ),
			'penciamp_hide_share_stumbleupon' => esc_html__( 'Hide Stumbleupon Share Button', 'penci-amp' ),
			'penciamp_hide_share_whatsapp'    => esc_html__( 'Hide Whatsapp Share Button', 'penci-amp' ),
			'penciamp_hide_share_telegram'    => esc_html__( 'Hide Telegram Share Button', 'penci-amp' ),
			'penciamp_hide_share_email'       => esc_html__( 'Hide Email Share Button', 'penci-amp' ),
			'penciamp_hide_share_pocket'      => esc_html__( 'Hide Pocket Share Button', 'penci-amp' ),
			'penciamp_hide_share_skype'       => esc_html__( 'Hide Skype Share Button', 'penci-amp' ),

			'penci_amp_posts_show_related' => esc_html__( 'Show Related Posts', 'penci-amp' ),
		);

		foreach ( $post_checklist as $id_option => $label_option ) {
			$wp_customize->add_setting( $id_option, array(
				'sanitize_callback' => array( $sanitizer, 'checkbox' ),
				'default'           => penci_amp_default_setting( $id_option ),
				'transport'         => 'postMessage',
				'transport'         => 'postMessage',
			) );
		}

		$wp_customize->add_setting( 'penci_amp_related_by', array(
			'default'           => 'categories',
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		// Sidebar
		$wp_customize->add_setting( 'penci_amp_show_sidebar', array(
			'default'   => penci_amp_default_setting( 'penci_amp_show_sidebar' ),
			'transport' => 'postMessage',
		) );

		$wp_customize->add_setting( 'penci_amp_img_logo_sidebar', array(
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		if ( $wp_customize->selective_refresh ) {

			$wp_customize->selective_refresh->add_partial( 'penci_amp_img_logo_sidebar', array(
				'settings'            => array( 'penci_amp_img_logo_sidebar' ),
				'selector'            => '.sidebar-branding',
				'render_callback'     => 'penci_amp_img_logo_sidebar',
				'container_inclusive' => true,
			) );
		}

		$sidebar_checklist = array(
			'penci_amp_sidebar_show_socail' => esc_html__( 'Show Social Media', 'penci-amp' ),
			'penci_amp_sidebar_show_logo'   => esc_html__( 'Show Logo', 'penci-amp' ),
		);

		foreach ( $sidebar_checklist as $id_option => $label_option ) {
			$wp_customize->add_setting( $id_option, array(
				'sanitize_callback' => array( $sanitizer, 'checkbox' ),
				'default'           => penci_amp_default_setting( $id_option ),
				'transport'         => 'postMessage',
			) );
		}


		$wp_customize->add_setting( 'penci_amp_footer_copy_right', array(
			'default'   => penci_amp_default_setting( 'penci_amp_footer_copy_right' ),
			'transport' => 'postMessage',
		) );
		$footer_checklist = array(
			'penci_amp_no_version_link' => esc_html__( 'Show NO-AMP version link', 'penci-amp' ),
			'penci_amp_gototop'         => esc_html__( 'Show button go to top', 'penci-amp' ),
		);

		foreach ( $footer_checklist as $id_option => $label_option ) {
			$wp_customize->add_setting( $id_option, array(
				'sanitize_callback' => array( $sanitizer, 'checkbox' ),
				'default'           => penci_amp_default_setting( $id_option ),
				'transport'         => 'postMessage',
			) );
		}

		$wp_customize->add_setting( 'penci_amp_404_image', array(
			'sanitize_callback' => 'esc_url_raw',
			'default'           => penci_amp_default_setting( 'penci_amp_404_image' ),
		) );
		$wp_customize->add_setting( 'penci_amp_404_heading', array(
			'default'           => penci_amp_default_setting( 'penci_amp_404_heading' ),
			'sanitize_callback' => array( $sanitizer, 'text' ),
		) );
		$wp_customize->add_setting( 'penci_amp_404_sub_heading', array(
			'default'           => penci_amp_default_setting( 'penci_amp_404_sub_heading' ),
			'sanitize_callback' => array( $sanitizer, 'text' ),
		) );

		$options_transition = array(
			'penci_amp_latest_posts_text'        => esc_html__( 'Text "Latest Posts"', 'penci-amp' ),
			'penci_amp_search_on_site'           => esc_html__( 'Text "Search on site"', 'penci-amp' ),
			'penci_amp_search_input_placeholder' => esc_html__( 'Text "Enter keyword..."', 'penci-amp' ),
			'penci_amp_search_button'            => esc_html__( 'Text "Search"', 'penci-amp' ),
			'penci_content_not_found'            => esc_html__( 'Text "Not found"', 'penci-amp' ),
			'penci_amp_nopost_found'             => esc_html__( 'Text "No Posts Found!"', 'penci-amp' ),
			'penci_amp_search_not_found'         => esc_html__( 'Text "Sorry, but nothing matched your search terms. Please try again with some different keywords."', 'penci-amp' ),
			'penci_content_pre'                  => esc_html__( 'Text "previous post"', 'penci-amp' ),
			'penci_content_next'                 => esc_html__( 'Text "next post"', 'penci-amp' ),
			'penci_content_no_more_post'         => esc_html__( 'Text "Sorry, No more posts"', 'penci-amp' ),
			'penci_amp_tex_single_related'       => esc_html__( 'Text "Related posts"', 'penci-amp' ),

			'penci_amp_text_select_menu'  => esc_html__( 'Text "Select a menu for AMP Sidebar"', 'penci-amp' ),
			'penci_amp_text_view_desktop' => esc_html__( 'Text "View Desktop Version"', 'penci-amp' ),
			'penci_amp_text_backtotop'    => esc_html__( 'Text "Back To Top"', 'penci-amp' ),

			'penci_amp_browsing_product_category' => esc_html__( 'Text "Browsing category"', 'penci-amp' ),
			'penci_amp_browsing_product_tag'      => esc_html__( 'Text "Browsing tag"', 'penci-amp' ),
			'penci_amp_browsing'                  => esc_html__( 'Text "Browsing"', 'penci-amp' ),
			'penci_amp_product-shop'              => esc_html__( 'Text "Browsing shop"', 'penci-amp' ),
			'penci_amp_browsing_category'         => esc_html__( 'Text "Browsing shop category"', 'penci-amp' ),
			'penci_amp_browsing_tag'              => esc_html__( 'Text "Browsing shop tag"', 'penci-amp' ),
			'penci_amp_browsing_author'           => esc_html__( 'Text "Browsing author"', 'penci-amp' ),
			'penci_amp_browsing_yearly'           => esc_html__( 'Text "Browsing yearly archive"', 'penci-amp' ),
			'penci_amp_browsing_monthly'          => esc_html__( 'Text "Browsing monthly archive"', 'penci-amp' ),
			'penci_amp_browsing_daily'            => esc_html__( 'Text "Browsing daily archive"', 'penci-amp' ),
			'penci_amp_browsing_archive'          => esc_html__( 'Text "Browsing archive"', 'penci-amp' ),
			'penci_amp_asides'                    => esc_html__( 'Text "Asides"', 'penci-amp' ),
			'penci_amp_galleries'                 => esc_html__( 'Text "Galleries"', 'penci-amp' ),
			'penci_amp_images'                    => esc_html__( 'Text "Images"', 'penci-amp' ),
			'penci_amp_videos'                    => esc_html__( 'Text "Videos"', 'penci-amp' ),
			'penci_amp_links'                     => esc_html__( 'Text "Links"', 'penci-amp' ),
			'penci_amp_statuses'                  => esc_html__( 'Text "Statuses"', 'penci-amp' ),
			'penci_amp_audio'                     => esc_html__( 'Text "Audio"', 'penci-amp' ),
			'penci_amp_chats'                     => esc_html__( 'Text "Chats"', 'penci-amp' ),
			'penci_amp_archive'                   => esc_html__( 'Text "Archive"', 'penci-amp' ),
			'penci-amp-product-sale'              => esc_html__( 'Text "Sale!"', 'penci-amp' ),
			'penci_amp_product_view'              => esc_html__( 'Text "View"', 'penci-amp' ),
			'penci_amp_related_product'           => esc_html__( 'Text "Related products"', 'penci-amp' ),
			'penci_amp_add_comment'               => esc_html__( 'Text "Add Comment"', 'penci-amp' ),
			'penci_amp_text_readmore'             => esc_html__( 'Text "Read more"', 'penci-amp' ),

		);
		foreach ( $options_transition as $key => $label ) {
			$wp_customize->add_setting( $key, array(
				'sanitize_callback' => array( $sanitizer, 'html' ),
				'default'           => penci_amp_default_setting( $key ),
			) );
		}


		$wp_customize->add_setting( 'penci_amp_font_for_body', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_for_body' ),
			'sanitize_callback' => array( $sanitizer, 'text' ),
		) );

		$wp_customize->add_setting( 'penci_amp_font_weight_body', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_weight_body' ),
			'sanitize_callback' => array( $sanitizer, 'select' ),
		) );

		$wp_customize->add_setting( 'penci_amp_font_for_size_body', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_for_size_body' ),
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		$wp_customize->add_setting( 'penci_amp_font_for_title', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_for_title' ),
			'sanitize_callback' => array( $sanitizer, 'text' ),
		) );

		$wp_customize->add_setting( 'penci_amp_font_weight_title', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_weight_title' ),
			'sanitize_callback' => array( $sanitizer, 'select' ),
		) );

		$wp_customize->add_setting( 'penci_amp_font_for_size_title', array(
			'default'           => penci_amp_default_setting( 'penci_amp_font_for_size_title' ),
			'sanitize_callback' => array( $sanitizer, 'html' ),
		) );

		$wp_customize->add_setting( 'penci_amp_use_site_address_url', array(
			'default'           => '',
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
		) );
		$wp_customize->add_setting( 'penci_amp_mobile_version', array(
			'default'           => '',
			'sanitize_callback' => array( $sanitizer, 'checkbox' ),
		) );
		$wp_customize->add_setting( 'penci_amp_url_format', array(
			'default'           => 'end-point',
			'sanitize_callback' => array( $sanitizer, 'select' ),
		) );

		$disamp_checklist = array(
			'penciamp_dison_home' => esc_html__( 'Disable AMP for Homepage', 'penci-amp' ),
			'penciamp_dison_cat'  => esc_html__( 'Disable AMP for All Category Pages', 'penci-amp' ),
			'penciamp_dison_tag'  => esc_html__( 'Disable AMP for All Tag Pages', 'penci-amp' ),
			'penciamp_dison_arch' => esc_html__( 'Disable AMP for All Archive Pages', 'penci-amp' ),
			'penciamp_dison_post' => esc_html__( 'Disable AMP for All Single Post Pages', 'penci-amp' ),
			'penciamp_dison_page' => esc_html__( 'Disable AMP for All Pages', 'penci-amp' ),
		);
		foreach ( $disamp_checklist as $id_option => $label_option ) {
			$wp_customize->add_setting( $id_option, array(
				'sanitize_callback' => array( $sanitizer, 'checkbox' ),
				'default'           => '',
			) );
		}

		$wp_customize->add_setting( 'penci_amp_customcss', array(
			'sanitize_callback' => array( $sanitizer, 'html' ),
			'default'           => '',
		) );

		$wp_customize->add_setting( 'penci-amp-analytics', array(
			'default' => penci_amp_default_setting( 'penci-amp-analytics' ),
		) );

		$wp_customize->add_setting( 'penci-amp-analytics-v4', array(
			'default' => penci_amp_default_setting( 'penci-amp-analytics' ),
		) );

		$options_for_ga4 = array(
			'penci-amp-analytics-ga4-dpe',
			'penci-amp-analytics-ga4-gce',
			'penci-amp-analytics-ga4-wvt',
			'penci-amp-analytics-ga4-ptt',
		);

		foreach ( $options_for_ga4 as $key ) {
			$wp_customize->add_setting( $key, array(
				'default' => '',
			) );
		}

		// Google Adsense
		$options_google_ads = array(
			'penci_amp_ad_home_below_slider',
			'penci_amp_ad_home_below_latest_posts',
			'penci_amp_ad_archive_above_posts',
			'penci_amp_ad_archive_below_posts',
			'penci_amp_ad_single_above_cat',
			'penci_amp_ad_single_below_img',
			'penci_amp_ad_single_below_content',
		);
		foreach ( $options_google_ads as $key ) {
			$wp_customize->add_setting( $key, array(
				'default' => '',
			) );
		}
	}

	public static function register_customizer_ui( $wp_customize ) {

		require_once( PENCI_AMP_DIR . '/includes/settings/custom-control/custom-control.php' );


		// Header
		$wp_customize->add_section( 'penci_amp_header', array(
			'title'    => __( 'Header', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 1,
		) );

		// Text logo
		$wp_customize->add_control( 'penci_amp_header_text_logo', array(
			'label'    => esc_html__( 'Text Logo', 'penci-amp' ),
			'section'  => 'penci_amp_header',
			'settings' => 'penci_amp_header_text_logo',
		) );

		// Image logo
		$control_class = class_exists( 'WP_Customize_Cropped_Image_Control' ) ? 'WP_Customize_Cropped_Image_Control' : 'WP_Customize_Image_Control';
		$wp_customize->add_control( new $control_class( $wp_customize, 'penci_amp_img_logo', array(
			'label'         => esc_html__( 'Logo', 'penci-amp' ),
			'section'       => 'penci_amp_header',
			'settings'      => 'penci_amp_img_logo',
			'height'        => penci_amp_default_setting( 'logo-height' ),
			'width'         => penci_amp_default_setting( 'logo-width' ),
			'flex_height'   => penci_amp_default_setting( 'logo-flex-height' ),
			'flex_width'    => penci_amp_default_setting( 'logo-flex-width' ),
			'button_labels' => array(
				'select'       => __( 'Select logo', 'penci-amp' ),
				'change'       => __( 'Change logo', 'penci-amp' ),
				'remove'       => __( 'Remove', 'penci-amp' ),
				'default'      => __( 'Default', 'penci-amp' ),
				'placeholder'  => __( 'No logo selected', 'penci-amp' ),
				'frame_title'  => __( 'Select logo', 'penci-amp' ),
				'frame_button' => __( 'Choose logo', 'penci-amp' ),
			),
		) ) );

		// Show search

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_show_search',
			array(
				'label'    => esc_html__( 'Show Search Icon', 'penci-amp' ),
				'section'  => 'penci_amp_header',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_show_search',
			)
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_sticky_header',
			array(
				'label'    => esc_html__( 'Sticky The Header', 'penci-amp' ),
				'section'  => 'penci_amp_header',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_sticky_header',
			)
		) );

		$wp_customize->add_control( 'penci_amp_header_custom_code', array(
			'label'   => __( 'Add Custom Code Inside <head> Tag', 'penci-amp' ),
			'section' => 'penci_amp_header',
			'type'    => 'textarea',
		) );

		$wp_customize->add_control( 'penci_amp_afterbody_custom_code', array(
			'label'   => esc_html__( 'Add Custom Codes After <body> Tag', 'penci-amp' ),
			'section' => 'penci_amp_header',
			'type'    => 'textarea',
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_disable_canonical',
			array(
				'label'    => esc_html__( 'Disable the canonical tag auto render on AMP pages from Penci AMP plugin', 'penci-amp' ),
				'section'  => 'penci_amp_header',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_disable_canonical',
			)
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_enable_auto_ads',
			array(
				'label'       => esc_html__( 'Load AMP Auto Ads Javascript', 'penci-amp' ),
				'section'     => 'penci_amp_header',
				'description' => 'You can check to this option to load <strong>amp-auto-ads-0.1.js</strong> to your <head> tag - check more on <a href="https://amp.dev/documentation/components/amp-auto-ads/" target="_blank">this guide</a>.<br>But, we recommend you use non-auto-ads because when you use auto-ads, the ads will be auto appears in random places on your pages and it\'s not look good.',
				'type'        => 'checkbox',
				'settings'    => 'penci_amp_enable_auto_ads',
			)
		) );

		// Homepage
		$wp_customize->add_section( 'penci_amp_homepage', array(
			'title'    => __( 'Homepage', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 2,
		) );


		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_home_show_slider',
			array(
				'label'    => esc_html__( 'Show Featured Slider', 'penci-amp' ),
				'section'  => 'penci_amp_homepage',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_home_show_slider',
			)
		) );
		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_home_show_textlasestp',
			array(
				'label'    => esc_html__( 'Show "Latest Posts" Heading Text', 'penci-amp' ),
				'section'  => 'penci_amp_homepage',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_home_show_textlasestp',
			)
		) );

		$wp_customize->add_control( 'penci_amp_home_featured_cat', array(
			'label'       => esc_html__( 'List Featured Categories', 'penci-amp' ),
			'section'     => 'penci_amp_homepage',
			'settings'    => 'penci_amp_home_featured_cat',
			'type'        => 'textarea',
			'description' => 'Please copy and paste categories slug you want display above Latest Posts here - check <a rel="nofollow" href="https://imgresources.s3.amazonaws.com/magazine-2.png" target="_blank">this image</a> to understand what is categories slug. Example: You want display categories "Life Style, Travel, Music" above Latest Posts, fill categories slug like "life-style, travel, music"',
		) );
		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_fcat_below_lposts',
			array(
				'label'    => esc_html__( 'Move Featured Categories To Bellow Latest Posts', 'penci-amp' ),
				'section'  => 'penci_amp_homepage',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_fcat_below_lposts',
			)
		) );

		$number_control = class_exists( 'Customize_Number_Control' ) ? 'Customize_Number_Control' : 'WP_Customize_Control';
		$wp_customize->add_control( new $number_control( $wp_customize, 'penci_amp_home_featured_cat_numbers', array(
			'label'    => esc_html__( 'Amount of Posts Display on Featured Categories', 'penci-amp' ),
			'section'  => 'penci_amp_homepage',
			'settings' => 'penci_amp_home_featured_cat_numbers',
			'type'     => 'number',
		) ) );
		$wp_customize->add_control( 'penci_amp_featured_cat_listing', array(
			'label'   => __( 'Featured Categories Layout', 'penci-amp' ),
			'section' => 'penci_amp_homepage',
			'type'    => 'select',
			'choices' => array(
				'listing-1' => __( 'Small Image Listing', 'penci-amp' ),
				'listing-2' => __( 'Large Image Listing', 'penci-amp' ),
				'listing-3' => __( 'Large + Small Image Listing', 'penci-amp' ),
			)
		) );

		$wp_customize->add_control( 'penci_amp_home_listing', array(
			'label'   => __( 'Homepage Layout', 'penci-amp' ),
			'section' => 'penci_amp_homepage',
			'type'    => 'select',
			'choices' => array(
				'listing-1' => __( 'Small Image Listing', 'penci-amp' ),
				'listing-2' => __( 'Large Image Listing', 'penci-amp' ),
				'listing-3' => __( 'Large + Small Image Listing', 'penci-amp' ),
			)
		) );

		$home_checklist2 = array(
			'penci_amp_home_show_pauthor'    => esc_html__( 'Show Author Name', 'penci-amp' ),
			'penci_amp_home_show_pdate'      => esc_html__( 'Show Post Date', 'penci-amp' ),
			'penci_amp_home_show_pcomments'  => esc_html__( 'Show Comment Count', 'penci-amp' ),
			'penci_amp_home_show_pview'      => esc_html__( 'Show Post Views', 'penci-amp' ),
			'penci_amp_home_show_excrept'    => esc_html__( 'Show Post Excrept For Large Image Listing', 'penci-amp' ),
			'penci_amp_home_show_readmore'   => esc_html__( 'Show Read more Button', 'penci-amp' ),
			'penci_amp_home_show_pagination' => esc_html__( 'Show Pagination', 'penci-amp' ),
		);

		foreach ( $home_checklist2 as $id_option => $label_option ) {
			$wp_customize->add_control( new WP_Customize_Control(
				$wp_customize,
				$id_option,
				array(
					'label'    => $label_option,
					'section'  => 'penci_amp_homepage',
					'type'     => 'checkbox',
					'settings' => $id_option,
				)
			) );
		}

		$wp_customize->add_control( 'penci_amp_ad_home_below_slider', array(
			'label'       => esc_html__( 'Add Google Adsense Code Below The Slider', 'penci-amp' ),
			'section'     => 'penci_amp_homepage',
			'settings'    => 'penci_amp_ad_home_below_slider',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		$wp_customize->add_control( 'penci_amp_ad_home_below_latest_posts', array(
			'label'       => esc_html__( 'Add Google Adsense Code Below The Latest Posts', 'penci-amp' ),
			'section'     => 'penci_amp_homepage',
			'settings'    => 'penci_amp_ad_home_below_latest_posts',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		// Archive
		$wp_customize->add_section( 'penci_amp_archive', array(
			'title'    => __( 'Archive Pages', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 3,
		) );

		$wp_customize->add_control( 'penci_amp_archive_listing', array(
			'label'   => __( 'Archive Pages Layout', 'penci-amp' ),
			'section' => 'penci_amp_archive',
			'type'    => 'select',
			'choices' => array(
				'listing-1' => __( 'Small Image Listing', 'penci-amp' ),
				'listing-2' => __( 'Large Image Listing', 'penci-amp' ),
				'listing-3' => __( 'Large + Small Image Listing', 'penci-amp' ),
			)
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_dis_desc_sanitizer',
			array(
				'label'    => esc_html__( 'Disable Sanitizer For Description', 'penci-amp' ),
				'section'  => 'penci_amp_archive',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_dis_desc_sanitizer',
			)
		) );

		$wp_customize->add_control( 'penci_amp_ad_archive_above_posts', array(
			'label'       => esc_html__( 'Add Google Adsense Code Above The Posts', 'penci-amp' ),
			'section'     => 'penci_amp_archive',
			'settings'    => 'penci_amp_ad_archive_above_posts',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		$wp_customize->add_control( 'penci_amp_ad_archive_below_posts', array(
			'label'       => esc_html__( 'Add Google Adsense Code Below The Posts', 'penci-amp' ),
			'section'     => 'penci_amp_archive',
			'settings'    => 'penci_amp_ad_archive_below_posts',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		// Posts
		$wp_customize->add_section( 'penci_amp_posts', array(
			'title'    => __( 'Single Posts', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 4,
		) );

		$post_checklist = array(
			'penci_amp_posts_show_pcat'       => esc_html__( 'Show Categories', 'penci-amp' ),
			'penci_amp_posts_show_thumb'      => esc_html__( 'Show Featured Image', 'penci-amp' ),
			'penci_amp_posts_show_pmeta'      => esc_html__( 'Show Post Meta', 'penci-amp' ),
			'penci_amp_posts_show_pauthor'    => esc_html__( 'Show Author Name', 'penci-amp' ),
			'penci_amp_posts_show_pdate'      => esc_html__( 'Show Post Date', 'penci-amp' ),
			'penci_amp_posts_show_pcomment'   => esc_html__( 'Show Comment Count', 'penci-amp' ),
			'penci_amp_posts_show_pview'      => esc_html__( 'Show Post Views', 'penci-amp' ),
			'penci_amp_posts_show_ptag'       => esc_html__( 'Show Post Tags', 'penci-amp' ),
			'penci_amp_posts_show_comment'    => esc_html__( 'Show Comments', 'penci-amp' ),
			'penci_amp_posts_show_show_pag'   => esc_html__( 'Show Pagination', 'penci-amp' ),
			'penci_amp_posts_show_share'      => esc_html__( 'Show Share Box', 'penci-amp' ),
			'penciamp_hide_share_facebook'    => esc_html__( 'Hide Facebook Share Button', 'penci-amp' ),
			'penciamp_hide_share_twitter'     => esc_html__( 'Hide Twitter Share Button', 'penci-amp' ),
			'penciamp_hide_share_pinterest'   => esc_html__( 'Hide Pinterest Share Button', 'penci-amp' ),
			'penciamp_hide_share_linkedin'    => esc_html__( 'Hide Linkedin Share Button', 'penci-amp' ),
			'penciamp_hide_share_tumblr'      => esc_html__( 'Hide Tumblr Share Button', 'penci-amp' ),
			'penciamp_hide_share_vk'          => esc_html__( 'Hide VK Share Button', 'penci-amp' ),
			'penciamp_hide_share_ok'          => esc_html__( 'Hide Odnoklassniki Share Button', 'penci-amp' ),
			'penciamp_hide_share_reddit'      => esc_html__( 'Hide Reddit Share Button', 'penci-amp' ),
			'penciamp_hide_share_stumbleupon' => esc_html__( 'Hide Stumbleupon Share Button', 'penci-amp' ),
			'penciamp_hide_share_whatsapp'    => esc_html__( 'Hide Whatsapp Share Button', 'penci-amp' ),
			'penciamp_hide_share_telegram'    => esc_html__( 'Hide Telegram Share Button', 'penci-amp' ),
			'penciamp_hide_share_email'       => esc_html__( 'Hide Email Share Button', 'penci-amp' ),
			'penciamp_hide_share_pocket'      => esc_html__( 'Hide Pocket Share Button', 'penci-amp' ),
			'penciamp_hide_share_skype'       => esc_html__( 'Hide Skype Share Button', 'penci-amp' ),

			'penci_amp_posts_show_related' => esc_html__( 'Show Related Posts', 'penci-amp' ),
		);

		foreach ( $post_checklist as $id_option => $label_option ) {
			$wp_customize->add_control( new WP_Customize_Control(
				$wp_customize,
				$id_option,
				array(
					'label'    => $label_option,
					'section'  => 'penci_amp_posts',
					'type'     => 'checkbox',
					'settings' => $id_option,
				)
			) );
		}

		$wp_customize->add_control( 'penci_amp_related_by', array(
			'label'   => __( 'Display Related Posts By:', 'penci-amp' ),
			'section' => 'penci_amp_posts',
			'type'    => 'select',
			'choices' => array(
				'categories' => __( 'Categories', 'penci-amp' ),
				'tags'       => __( 'Tags', 'penci-amp' ),
			)
		) );

		$wp_customize->add_control( 'penci_amp_ad_single_above_cat', array(
			'label'       => esc_html__( 'Add Google Adsense Code Above Post Categories', 'penci-amp' ),
			'section'     => 'penci_amp_posts',
			'settings'    => 'penci_amp_ad_single_above_cat',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		$wp_customize->add_control( 'penci_amp_ad_single_below_img', array(
			'label'       => esc_html__( 'Google Adsense Code Below The Featured Image', 'penci-amp' ),
			'section'     => 'penci_amp_posts',
			'settings'    => 'penci_amp_ad_single_below_img',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );

		$wp_customize->add_control( 'penci_amp_ad_single_below_content', array(
			'label'       => esc_html__( 'Google Adsense Code Below Post Content', 'penci-amp' ),
			'section'     => 'penci_amp_posts',
			'settings'    => 'penci_amp_ad_single_below_content',
			'type'        => 'textarea',
			'description' => 'NOTE IMPORTANT: Check guide for use google adsense codes the right way for this option <a href="http://soledad.pencidesign.com/soledad-document/#amp" target="_blank">here</a>',
		) );


		// Sidebar
		$wp_customize->add_section( 'penci_amp_sidebar', array(
			'title'    => __( 'Vertical Navigation', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 5,
		) );
		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_show_sidebar',
			array(
				'label'    => esc_html__( 'Show Vertical Navigation', 'penci-amp' ),
				'section'  => 'penci_amp_sidebar',
				'type'     => 'checkbox',
				'settings' => 'penci_amp_show_sidebar',
			)
		) );

		// Image logo
		$wp_customize->add_control( new $control_class( $wp_customize, 'penci_amp_img_logo_sidebar', array(
			'label'         => esc_html__( 'Logo', 'penci-amp' ),
			'section'       => 'penci_amp_sidebar',
			'settings'      => 'penci_amp_img_logo_sidebar',
			'height'        => penci_amp_default_setting( 'sidebar-logo-height' ),
			'width'         => penci_amp_default_setting( 'sidebar-logo-width' ),
			'flex_height'   => penci_amp_default_setting( 'sidebar-logo-flex-height' ),
			'flex_width'    => penci_amp_default_setting( 'sidebar-logo-flex-width' ),
			'button_labels' => array(
				'select'       => __( 'Select logo', 'penci-amp' ),
				'change'       => __( 'Change logo', 'penci-amp' ),
				'remove'       => __( 'Remove', 'penci-amp' ),
				'default'      => __( 'Default', 'penci-amp' ),
				'placeholder'  => __( 'No logo selected', 'penci-amp' ),
				'frame_title'  => __( 'Select logo', 'penci-amp' ),
				'frame_button' => __( 'Choose logo', 'penci-amp' ),
			),
		) ) );

		$sidebar_checklist = array(
			'penci_amp_sidebar_show_socail' => esc_html__( 'Show Social Media', 'penci-amp' ),
			'penci_amp_sidebar_show_logo'   => esc_html__( 'Show Logo Image', 'penci-amp' ),
		);

		foreach ( $sidebar_checklist as $id_option => $label_option ) {
			$wp_customize->add_control( new WP_Customize_Control(
				$wp_customize,
				$id_option,
				array(
					'label'    => $label_option,
					'section'  => 'penci_amp_sidebar',
					'type'     => 'checkbox',
					'settings' => $id_option,
				)
			) );
		}

		// Footer
		$wp_customize->add_section( 'penci_amp_footer', array(
			'title'    => __( 'Footer', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 6,
		) );

		$wp_customize->add_control( 'penci_amp_footer_copy_right', array(
			'label'   => __( 'Copyright text', 'penci-amp' ),
			'section' => 'penci_amp_footer',
			'type'    => 'textarea',
		) );

		$footer_checklist = array(
			'penci_amp_no_version_link' => esc_html__( 'Show none-AMP version link', 'penci-amp' ),
			'penci_amp_gototop'         => esc_html__( 'Show go to top button', 'penci-amp' ),
		);

		foreach ( $footer_checklist as $id_option => $label_option ) {
			$wp_customize->add_control( new WP_Customize_Control(
				$wp_customize,
				$id_option,
				array(
					'label'    => $label_option,
					'section'  => 'penci_amp_footer',
					'type'     => 'checkbox',
					'settings' => $id_option,
				)
			) );
		}

		// Page 404
		$wp_customize->add_section( 'penci_amp_page404', array(
			'title'    => esc_html__( '404 Page', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 6,

		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'penci_404_image', array(
			'label'    => esc_html__( 'Custom Main Image', 'penci-amp' ),
			'section'  => 'penci_amp_page404',
			'settings' => 'penci_amp_404_image',
			'priority' => 5
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci_404_heading', array(
			'label'    => esc_html__( 'Custom Heading Text', 'penci-amp' ),
			'section'  => 'penci_amp_page404',
			'settings' => 'penci_amp_404_heading',

		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci_404_sub_heading', array(
			'label'    => esc_html__( 'Custom Message Text', 'penci-amp' ),
			'section'  => 'penci_amp_page404',
			'settings' => 'penci_amp_404_sub_heading',
			'type'     => 'textarea',
		) ) );


		$wp_customize->add_section( 'penci_amp_section_transition_text', array(
			'title'       => esc_html__( 'Quick Text Transition', 'penci-amp' ),
			'panel'       => Penci_AMP_Customizer::PANEL_ID,
			'priority'    => 7,
			'description' => sprintf(
				esc_html__( 'Use shortcode [pencilang] with multiple languages site (ex: [pencilang en_US="Share" fr_FR="Partager" language_code="Your language text" /]). You can check language code %s', 'penci-amp' ),
				'<a href="' . esc_url( 'http://www.aurodigo.com/2015/02/wordpress-locale-codes-complete-list.html' ) . '" target="_blank">' . esc_html__( 'here', 'penci-amp' ) . '</a>'
			),
		) );

		$options_transition = array(
			'penci_amp_latest_posts_text'        => esc_html__( 'Text "Latest Posts"', 'penci-amp' ),
			'penci_amp_search_on_site'           => esc_html__( 'Text "Search on site"', 'penci-amp' ),
			'penci_amp_search_input_placeholder' => esc_html__( 'Text "Enter keyword..."', 'penci-amp' ),
			'penci_amp_search_button'            => esc_html__( 'Text "Search"', 'penci-amp' ),
			'penci_content_not_found'            => esc_html__( 'Text "Not found"', 'penci-amp' ),
			'penci_amp_nopost_found'             => esc_html__( 'Text "No Posts Found!"', 'penci-amp' ),
			'penci_amp_search_not_found'         => esc_html__( 'Text "Sorry, but nothing matched your search terms. Please try again with some different keywords."', 'penci-amp' ),
			'penci_content_pre'                  => esc_html__( 'Text "previous post"', 'penci-amp' ),
			'penci_content_next'                 => esc_html__( 'Text "next post"', 'penci-amp' ),
			'penci_content_no_more_post'         => esc_html__( 'Text "Sorry, No more posts"', 'penci-amp' ),
			'penci_amp_tex_single_related'       => esc_html__( 'Text "Related posts"', 'penci-amp' ),

			'penci_amp_text_select_menu'  => esc_html__( 'Text "Select a menu for AMP Sidebar"', 'penci-amp' ),
			'penci_amp_text_view_desktop' => esc_html__( 'Text "View Desktop Version"', 'penci-amp' ),
			'penci_amp_text_backtotop'    => esc_html__( 'Text "Back To Top"', 'penci-amp' ),

			'penci_amp_browsing_product_category' => esc_html__( 'Text "Browsing category"', 'penci-amp' ),
			'penci_amp_browsing_product_tag'      => esc_html__( 'Text "Browsing tag"', 'penci-amp' ),
			'penci_amp_browsing'                  => esc_html__( 'Text "Browsing"', 'penci-amp' ),
			'penci_amp_product-shop'              => esc_html__( 'Text "Browsing shop"', 'penci-amp' ),
			'penci_amp_browsing_category'         => esc_html__( 'Text "Browsing shop category"', 'penci-amp' ),
			'penci_amp_browsing_tag'              => esc_html__( 'Text "Browsing shop tag"', 'penci-amp' ),
			'penci_amp_browsing_author'           => esc_html__( 'Text "Browsing author"', 'penci-amp' ),
			'penci_amp_browsing_yearly'           => esc_html__( 'Text "Browsing yearly archive"', 'penci-amp' ),
			'penci_amp_browsing_monthly'          => esc_html__( 'Text "Browsing monthly archive"', 'penci-amp' ),
			'penci_amp_browsing_daily'            => esc_html__( 'Text "Browsing daily archive"', 'penci-amp' ),
			'penci_amp_browsing_archive'          => esc_html__( 'Text "Browsing archive"', 'penci-amp' ),
			'penci_amp_asides'                    => esc_html__( 'Text "Asides"', 'penci-amp' ),
			'penci_amp_galleries'                 => esc_html__( 'Text "Galleries"', 'penci-amp' ),
			'penci_amp_images'                    => esc_html__( 'Text "Images"', 'penci-amp' ),
			'penci_amp_videos'                    => esc_html__( 'Text "Videos"', 'penci-amp' ),
			'penci_amp_links'                     => esc_html__( 'Text "Links"', 'penci-amp' ),
			'penci_amp_statuses'                  => esc_html__( 'Text "Statuses"', 'penci-amp' ),
			'penci_amp_audio'                     => esc_html__( 'Text "Audio"', 'penci-amp' ),
			'penci_amp_chats'                     => esc_html__( 'Text "Chats"', 'penci-amp' ),
			'penci_amp_archive'                   => esc_html__( 'Text "Archive"', 'penci-amp' ),
			'penci-amp-product-sale'              => esc_html__( 'Text "Sale!"', 'penci-amp' ),
			'penci_amp_product_view'              => esc_html__( 'Text "View"', 'penci-amp' ),
			'penci_amp_related_product'           => esc_html__( 'Text "Related products"', 'penci-amp' ),
			'penci_amp_add_comment'               => esc_html__( 'Text "Add Comment"', 'penci-amp' ),
			'penci_amp_text_readmore'             => esc_html__( 'Text "Read more"', 'penci-amp' ),

		);
		foreach ( $options_transition as $key => $label ) {
			$wp_customize->add_control( $key, array(
				'label'    => $label,
				'section'  => 'penci_amp_section_transition_text',
				'settings' => $key,
			) );
		}

		$wp_customize->add_section( 'penci_amp_section_typo', array(
			'title'    => __( 'Typography', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 8,
		) );

		$wp_customize->add_control( 'penci_amp_font_for_body', array(
			'label'       => esc_html__( 'Font For Body Text', 'penci-amp' ),
			'section'     => 'penci_amp_section_typo',
			'settings'    => 'penci_amp_font_for_body',
			'description' => 'Default font is "Open sans"',
			'type'        => 'select',
			'choices'     => function_exists( 'penci_all_fonts' ) ? penci_all_fonts() : array()
		) );

		$wp_customize->add_control( 'penci_amp_font_weight_body', array(
			'label'    => esc_html__( 'Font Weight For Body Text', 'penci-amp' ),
			'section'  => 'penci_amp_section_typo',
			'settings' => 'penci_amp_font_weight_body',
			'type'     => 'select',
			'choices'  => array(
				'normal'  => 'Normal',
				'bold'    => 'Bold',
				'bolder'  => 'Bolder',
				'lighter' => 'Lighter',
				'100'     => '100',
				'200'     => '200',
				'300'     => '300',
				'400'     => '400',
				'500'     => '500',
				'600'     => '600',
				'700'     => '700',
				'800'     => '800',
				'900'     => '900'
			)
		) );


		$number_control = class_exists( 'Customize_Number_Control' ) ? 'Customize_Number_Control' : 'WP_Customize_Control';

		$wp_customize->add_control( new $number_control( $wp_customize, 'penci_amp_font_for_size_body', array(
			'label'    => esc_html__( 'Font Size for Body Text', 'penci-amp' ),
			'section'  => 'penci_amp_section_typo',
			'settings' => 'penci_amp_font_for_size_body',
			'type'     => 'number',
		) ) );

		$wp_customize->add_control( 'penci_amp_font_for_title', array(
			'label'       => esc_html__( 'Font For Heading Titles', 'penci-amp' ),
			'section'     => 'penci_amp_section_typo',
			'settings'    => 'penci_amp_font_for_title',
			'description' => 'Default font is "Roboto"',
			'type'        => 'select',
			'choices'     => function_exists( 'penci_all_fonts' ) ? penci_all_fonts() : array()
		) );

		$wp_customize->add_control( 'penci_amp_font_weight_title', array(
			'label'    => esc_html__( 'Font Weight For Heading Titles', 'penci-amp' ),
			'section'  => 'penci_amp_section_typo',
			'settings' => 'penci_amp_font_weight_title',
			'type'     => 'select',
			'choices'  => array(
				'normal'  => 'Normal',
				'bold'    => 'Bold',
				'bolder'  => 'Bolder',
				'lighter' => 'Lighter',
				'100'     => '100',
				'200'     => '200',
				'300'     => '300',
				'400'     => '400',
				'500'     => '500',
				'600'     => '600',
				'700'     => '700',
				'800'     => '800',
				'900'     => '900'
			)
		) );

		$wp_customize->add_control( new $number_control( $wp_customize, 'penci_amp_font_for_size_title', array(
			'label'    => esc_html__( 'Font Size for Heading Titles', 'penci-amp' ),
			'section'  => 'penci_amp_section_typo',
			'settings' => 'penci_amp_font_for_size_title',
			'type'     => 'number',
		) ) );

		$wp_customize->add_section( 'penci_amp_section_analytics', array(
			'title'    => __( 'Google Analytics', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 10,
		) );

		$wp_customize->add_control( 'penci-amp-analytics', array(
			'label'       => __( 'Google Analytics v3', 'penci-amp' ),
			'section'     => 'penci_amp_section_analytics',
			'description' => __( 'Insert Google Analytics account ID here.<br/> It’ll be in the format UA-XXXXXXXX-X', 'penci-amp' ),
		) );

		$wp_customize->add_control( 'penci-amp-analytics-v4', array(
			'label'       => __( 'Google Analytics v4', 'penci-amp' ),
			'section'     => 'penci_amp_section_analytics',
			'description' => __( 'Insert Google Analytics v4 account ID here.<br/> It’ll be in the format UA-XXXXXXXX-X', 'penci-amp' ),
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci-amp-analytics-ga4-dpe', array(
			'label'    => __( 'Default Pageview Enabled', 'penci-amp' ),
			'description'  => __( 'This option only applies to Google Analytics V4.', 'penci-amp' ),
			'section'  => 'penci_amp_section_analytics',
			'type'     => 'checkbox',
			'settings' => 'penci-amp-analytics-ga4-dpe',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci-amp-analytics-ga4-gce', array(
			'label'    => __( 'Google Consent Enabled', 'penci-amp' ),
			'description'  => __( 'This option only applies to Google Analytics V4.', 'penci-amp' ),
			'section'  => 'penci_amp_section_analytics',
			'type'     => 'checkbox',
			'settings' => 'penci-amp-analytics-ga4-gce',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci-amp-analytics-ga4-wvt', array(
			'label'    => __( 'Webvitals Tracking', 'penci-amp' ),
			'description'  => __( 'This option only applies to Google Analytics V4.', 'penci-amp' ),
			'section'  => 'penci_amp_section_analytics',
			'type'     => 'checkbox',
			'settings' => 'penci-amp-analytics-ga4-wvt',
		) ) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'penci-amp-analytics-ga4-ptt', array(
			'label'    => __( 'Performance Timing Tracking', 'penci-amp' ),
			'description'  => __( 'This option only applies to Google Analytics V4.', 'penci-amp' ),
			'section'  => 'penci_amp_section_analytics',
			'type'     => 'checkbox',
			'settings' => 'penci-amp-analytics-ga4-ptt',
		) ) );

		// Advanced Settings
		$wp_customize->add_section( 'penci_amp_section_advanced', array(
			'title'    => __( 'Advanced Settings', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 11,
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_use_site_address_url',
			array(
				'label'       => esc_html__( 'Use Site Address (URL)', 'penci-amp' ),
				'section'     => 'penci_amp_section_advanced',
				'type'        => 'checkbox',
				'settings'    => 'penci_amp_use_site_address_url',
				'description' => __( 'Use this option if you your site home page to be different from your WordPress installation directory.', 'penci-amp' ),
			)
		) );

		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'penci_amp_mobile_version',
			array(
				'label'       => esc_html__( 'Show AMP for Mobile Visitors', 'penci-amp' ),
				'section'     => 'penci_amp_section_advanced',
				'type'        => 'checkbox',
				'settings'    => 'penci_amp_mobile_version',
				'description' => __( 'By default, the mobile will be showing the responsive version from the theme. This option helps you can redirect all visitors to the AMP version automatically on mobile.', 'penci-amp' ),
			)
		) );

		$wp_customize->add_control( 'penci_amp_url_format', array(
			'label'       => esc_html__( 'AMP URL Format', 'penci-amp' ),
			'section'     => 'penci_amp_section_advanced',
			'settings'    => 'penci_amp_url_format',
			'type'        => 'select',
			'choices'     => array(
				'end-point'    => __( 'End Point - At the end of the URL', 'penci-amp' ),
				'start-point'  => __( 'Start Point - At the beginning of the URL', 'penci-amp' ),
				'simple-point' => __( '?amp=1 - At the end of the URL', 'penci-amp' ),
			),
			'description' => __( 'End Point: yoursite.com/post/amp/ <br>Start Point: yoursite.com/amp/post/ <br/>We recommend use the End Point', 'penci-amp' ),
		) );

		$disamp_checklist = array(
			'penciamp_dison_home' => esc_html__( 'Disable AMP for Homepage', 'penci-amp' ),
			'penciamp_dison_cat'  => esc_html__( 'Disable AMP for All Category Pages', 'penci-amp' ),
			'penciamp_dison_tag'  => esc_html__( 'Disable AMP for All Tag Pages', 'penci-amp' ),
			'penciamp_dison_arch' => esc_html__( 'Disable AMP for All Archive Pages', 'penci-amp' ),
			'penciamp_dison_post' => esc_html__( 'Disable AMP for All Single Post Pages', 'penci-amp' ),
			'penciamp_dison_page' => esc_html__( 'Disable AMP for All Pages', 'penci-amp' ),
		);
		foreach ( $disamp_checklist as $id_option => $label_option ) {
			$wp_customize->add_control( new WP_Customize_Control(
				$wp_customize,
				$id_option,
				array(
					'label'    => $label_option,
					'section'  => 'penci_amp_section_advanced',
					'type'     => 'checkbox',
					'settings' => $id_option,
				)
			) );
		}

		$wp_customize->add_section( 'penci_amp_section_customcss', array(
			'title'    => __( 'Custom CSS', 'penci-amp' ),
			'panel'    => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 11,
		) );

		$wp_customize->add_control( 'penci_amp_customcss', array(
			'label'    => '',
			'section'  => 'penci_amp_section_customcss',
			'settings' => 'penci_amp_customcss',
			'type'     => 'textarea',
		) );

	}
}
