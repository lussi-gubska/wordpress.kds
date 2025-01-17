<?php
/**
 * Get theme default settings.
 *
 * @param string $name
 *
 * @return mixed
 */
function penci_amp_default_setting( $name ) {

	$defaults = array(

		// Header
		'logo-height'           => 50,
		'logo-width'            => 230,
		'logo-flex-height'      => false,
		'logo-flex-width'       => true,
		'penci_amp_show_search' => 1,
		'penci_amp_sticky_header' => 1,

		// Homepage
		'penci_amp_home_show_slider'   => 1,
		'penci_amp_show_on_front'      => 'posts',
		'penci_amp_home_listing'       => 'listing-1',
		'penci_amp_featured_cat_listing'       => 'listing-1',
		'penci_amp_home_show_pdate'    => 1,
		'penci_amp_home_show_excrept'  => 1,
		'penci_amp_home_show_readmore' => 1,
		'penci_amp_home_show_pagination' => 1,
		'penci_amp_text_readmore'      => esc_html__( 'Read more', 'penci-amp' ),

		// Archive
		'penci_amp_archive_listing' => 'listing-1',

		// Posts
		'penci_amp_posts_show_thumb'      => 1,
		'penci_amp_posts_show_pmeta'      => 1,
		'penci_amp_posts_show_pcat'       => 1,
		'penci_amp_posts_show_pauthor'      => 1,
		'penci_amp_posts_show_pdate'      => 1,
		'penci_amp_posts_show_pcomment'      => 1,
		'penci_amp_posts_show_ptag'       => 1,
		'penci_amp_posts_show_comment'    => 1,
		'penci_amp_posts_show_share'      => 1,
		'penci_amp_posts_show_related'    => 1,
		'penci_amp_posts_show_show_pag'   => 1,
	
		// Sidebar
		'penci_amp_show_sidebar' => 1,
		'sidebar-logo-height'           => 150,
		'sidebar-logo-width'            => 150,
		'sidebar-logo-flex-height'      => false,
		'sidebar-logo-flex-width'       => true,
		'penci_amp_sidebar_show_socail' => 1,
		'penci_amp_sidebar_show_logo'   => 1,

		// Footer
		'penci_amp_footer_copy_right' => 'Powered by <a href="http://pencidesign.com/" target="_blank">PenciDesign</a>',
		'penci_amp_no_version_link'   => 1,
		'penci_amp_gototop'           => 1,

		'penci_amp_404_image'           => '',
		'penci_amp_404_heading'         => esc_html__( "404 PAGE NOT FOUND", 'penci-amp' ),
		'penci_amp_404_sub_heading'     => wp_kses_post( sprintf( 'This page couldn\'t be found! Back to <a href="%s"> home page</a> if you like. Please use search for help!', esc_url( penci_amp_get_site_url() ) ) ),


		// Social
		'penci-social-share-text'      => esc_html__( 'Share', 'penci-amp' ),
		'penci_socail_title_facebook'  => esc_html__( 'Share this on Facebook', 'penci-amp' ),
		'penci_socail_title_twitter'   => esc_html__( 'Tweet on Twitter', 'penci-amp' ),
		'penci_socail_title_google'    => esc_html__( 'Share on Google+', 'penci-amp' ),
		'penci_socail_title_pinterest' => esc_html__( 'Pin it on Pinterest', 'penci-amp' ),
		'penci_socail_title_email'     => esc_html__( 'Share it on Email', 'penci-amp' ),

		// Typo
		'penci_amp_font_for_body'       => '"Roboto", "100:100italic:300:300italic:regular:italic:500:500italic:700:700italic:900:900italic", sans-serif',
		'penci_amp_font_weight_body'    => '400',
		'penci_amp_font_for_size_body'  => '14',
		'penci_amp_font_for_title'      => '"Open Sans", "300:300italic:regular:italic:600:600italic:700:700italic:800:800italic", sans-serif'  ,
		'penci_amp_font_weight_title'   => '700',
		'penci_amp_font_for_size_title' => '15',

		// Transition
		'penci_amp_latest_posts_text'        => esc_html__( 'Latest Posts', 'penci-amp' ),
		'penci_amp_search_on_site'           => esc_html__( 'Search on site', 'penci-amp' ),
		'penci_amp_search_input_placeholder' => esc_html__( 'Enter keyword...', 'penci-amp' ),
		'penci_amp_search_button'            => esc_html__( 'Search', 'penci-amp' ),
		'penci_content_not_found'            => esc_html__( 'Not found', 'penci-amp' ),
		'penci_amp_nopost_found'            => esc_html__( 'No Posts Found!', 'penci-amp' ),
		'penci_amp_search_not_found'            => esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords', 'penci-amp' ),
		'penci_content_pre'                  => esc_html__( 'previous post', 'penci-amp' ),
		'penci_content_next'                 => esc_html__( 'next post', 'penci-amp' ),
		'penci_content_no_more_post'         => esc_html__( 'Sorry, No more posts', 'penci-amp' ),
		'penci_amp_tex_single_related'          => esc_html__( 'Related posts', 'penci-amp' ),
		'penci_amp_text_select_menu' => esc_html__( 'Select a menu for "AMP Sidebar"', 'penci-amp' ),
		'penci_amp_text_view_desktop'  => esc_html__( 'View Desktop Version', 'penci-amp' ),
		'penci_amp_text_backtotop' => esc_html__( 'Back To Top', 'penci-amp' ),

		'penci_amp_browsing_product_category' => esc_html__( 'Category', 'penci-amp' ),
		'penci_amp_browsing_product_tag'      => esc_html__( 'Tag', 'penci-amp' ),
		'penci_amp_product-shop'              => esc_html__( 'Shop', 'penci-amp' ),
		'penci_amp_browsing'                  => esc_html__( 'Browsing', 'penci-amp' ),
		'penci_amp_browsing_category'         => esc_html__( 'Category', 'penci-amp' ),
		'penci_amp_browsing_tag'              => esc_html__( 'Tag', 'penci-amp' ),
		'penci_amp_browsing_author'           => esc_html__( 'Author', 'penci-amp' ),
		'penci_amp_browsing_yearly'           => esc_html__( 'Yearly archive', 'penci-amp' ),
		'penci_amp_browsing_monthly'          => esc_html__( 'Monthly archive', 'penci-amp' ),
		'penci_amp_browsing_daily'            => esc_html__( 'Daily archive', 'penci-amp' ),
		'penci_amp_browsing_archive'          => esc_html__( 'Archive', 'penci-amp' ),
		'penci_amp_asides'                    => esc_html__( 'Asides', 'penci-amp' ),
		'penci_amp_galleries'                 => esc_html__( 'Galleries', 'penci-amp' ),
		'penci_amp_images'                    => esc_html__( 'Images', 'penci-amp' ),
		'penci_amp_videos'                    => esc_html__( 'Videos', 'penci-amp' ),
		'penci_amp_links'                     => esc_html__( 'Links', 'penci-amp' ),
		'penci_amp_statuses'                  => esc_html__( 'Statuses', 'penci-amp' ),
		'penci_amp_audio'                     => esc_html__( 'Audio', 'penci-amp' ),
		'penci_amp_chats'                     => esc_html__( 'Chats', 'penci-amp' ),
		'penci_amp_archive'                   => esc_html__( 'Archive', 'penci-amp' ),
		'penci-amp-product-sale'              => esc_html__( 'Sale!', 'penci-amp' ),
		'penci_amp_product_view'              => esc_html__( 'View', 'penci-amp' ),
		'penci_amp_related_product'           => esc_html__( 'Related products', 'penci-amp' ),
		'penci_amp_add_comment'               => esc_html__( 'Add Comment', 'penci-amp' ),

		'penciamp_hide_share_linkedin'    => 1,
		'penciamp_hide_share_tumblr'      => 1,
		'penciamp_hide_share_reddit'      => 1,
		'penciamp_hide_share_stumbleupon' => 1,
		'penciamp_hide_share_whatsapp'    => 1,
		'penciamp_hide_share_telegram'    => 1,
		'penciamp_hide_share_vk'	=> 1,
		'penciamp_hide_share_ok'	=> 1,
		'penciamp_hide_share_pocket'	=> 1,
		'penciamp_hide_share_skype'	=> 1,
		
		'penci_amp_url_format'	=> 'end-point',
	);

	return isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
}

/**
 * Get theme settings.
 *
 * @param string $name
 *
 * @return mixed
 */
if ( ! function_exists( 'penci_amp_get_setting' ) ):
	function penci_amp_get_setting( $name ) {
		$value = get_theme_mod( $name, penci_amp_default_setting( $name ) );

		return do_shortcode( $value );
	}
endif;