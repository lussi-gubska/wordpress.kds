<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */
class Penci_Bf_Scripts {

	public function __construct() {

	}

	/**
	 * Enqueue Styles for admin
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_admin_print_styles( $hook_suffix ) {

		//Get main page
		$main_page_slug = sanitize_title( esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ) );

		$pages_hook_suffix = array(
			$main_page_slug . '_page_penci-bf-add-follower',
			$main_page_slug . '_page_penci-bf-send-email',
		);

		//Check pages when you needed
		if ( in_array( $hook_suffix, $pages_hook_suffix ) ) {

			wp_register_style( 'chosen-style', PENCI_BL_URL . '/inc/css/chosen/chosen.css', array(), PENCI_BL_VERSION );
			wp_enqueue_style( 'chosen-style' );

			wp_register_style( 'chosen-custom-style', PENCI_BL_URL . '/inc/css/chosen/chosen-custom.css', array(), PENCI_BL_VERSION );
			wp_enqueue_style( 'chosen-custom-style' );

			wp_register_style( 'penci-bf-admin-styles', PENCI_BL_URL . '/inc/css/penci-bf-admin.css', array(), PENCI_BL_VERSION );
			wp_enqueue_style( 'penci-bf-admin-styles' );
		}

		wp_register_style( 'penci-bf-admin-general-styles', PENCI_BL_URL . '/inc/css/penci-bf-general-admin.css', array(), PENCI_BL_VERSION );
		wp_enqueue_style( 'penci-bf-admin-general-styles' );
	}

	/**
	 * Enqueue Scripts for backend
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_admin_enqueue_scripts( $hook_suffix ) {

		global $wp_version;

		//Get main page
		$main_page_slug = sanitize_title( esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ) );

		$pages_hook_suffix = array(
			$main_page_slug . '_page_penci-bf-add-follower',
			$main_page_slug . '_page_penci-bf-send-email',
		);

		//Check pages when you needed
		if ( in_array( $hook_suffix, $pages_hook_suffix ) ) {

			wp_register_script( 'chosen', PENCI_BL_URL . '/inc/js/chosen/chosen.jquery.js', array( 'jquery' ), PENCI_BL_VERSION, true );
			wp_enqueue_script( 'chosen' );

			wp_register_script( 'ajax-chosen', PENCI_BL_URL . '/inc/js/chosen/ajax-chosen.jquery.js', array( 'jquery' ), PENCI_BL_VERSION, true );
			wp_enqueue_script( 'ajax-chosen' );

			wp_register_script( 'penci-bf-admin-scripts', PENCI_BL_URL . '/inc/js/admin.js', array(
				'jquery',
				'jquery-ui-datepicker',
				'jquery-ui-sortable'
			), PENCI_BL_VERSION, true );
			wp_enqueue_script( 'penci-bf-admin-scripts' );

			//localize script
			$newui = $wp_version >= '3.5' ? '1' : '0'; //check wp version for showing media uploader

			wp_localize_script( 'penci-bf-admin-scripts', 'Penci_Bf_Settings', array(
				'new_media_ui'     => $newui,
				'resetmsg'         => esc_html__( 'Click OK to reset all options. All settings will be lost!', 'penci-bookmark-follow' ),
				'testemailsuccess' => esc_html__( 'Test email has been sent successfully.', 'penci-bookmark-follow' ),
				'testemailerror'   => esc_html__( 'Test email could not sent.', 'penci-bookmark-follow' )
			) );

			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			// loads the required scripts for the meta boxes
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

		}
	}

	/**
	 * Enqueue Styles for public
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_public_print_styles() {

		wp_enqueue_style( 'jquery.toast' );

		wp_register_style( 'penci-bf-public-style', PENCI_BL_URL . '/inc/css/penci-bf-public.css', array(), PENCI_BL_VERSION );
		wp_enqueue_style( 'penci-bf-public-style' );
		wp_register_style( 'penci-bf-admin-styles', PENCI_BL_URL . '/inc/css/penci-bf-admin.css', array(), PENCI_BL_VERSION );
	}

	/**
	 * Loading Additional Java Script
	 *
	 * Loads the JavaScript required for toggling the meta boxes on the theme settings page.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_send_email_page_load_scripts( $hook_suffix ) {

		//Get main menu slug
		$main_page_slug = sanitize_title( esc_html__( 'Penci Bookmark & Follow', 'penci-bookmark-follow' ) );
		wp_register_script( 'penci-bf-sent-email-page-scripts', PENCI_BL_URL . '/inc/js/penci-bf-sent-email-page.js', array( 'jquery' ), PENCI_BL_VERSION, true );
		wp_enqueue_script( 'penci-bf-sent-email-page-scripts' );

		wp_localize_script( 'penci-bf-sent-email-page-scripts', 'Penci_Bf_Email_Settings',
			array(
				'main_page_slug' => $main_page_slug
			)
		);
	}

	/**
	 * Enqueue Scripts for frontside
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_front_scripts() {

		wp_enqueue_script( 'penci-bf-cookie-script', PENCI_BL_URL . 'inc/js/js.cookie.min.js', array( 'jquery' ), PENCI_BL_VERSION, true );
		wp_enqueue_script( 'penci-bf-main-scripts', PENCI_BL_URL . 'inc/js/main.js', array( 'jquery' ), PENCI_BL_VERSION, true );
		wp_enqueue_script( 'jquery.toast' );

		$pages         = get_option( 'penci_bl_set_pages' );
		$bookmark_page = isset( $pages['subscribe_manage_page'] ) && $pages['subscribe_manage_page'] ? get_page_link( $pages['subscribe_manage_page'] ) : '';

		$loggin_flag = is_user_logged_in() ? '1' : '0';
		wp_localize_script( 'penci-bf-main-scripts', 'Penci_Bf_Vars', array(
			'nonce'               => wp_create_nonce( 'penci_bookmark_follow' ),
			'ajaxurl'             => admin_url( 'admin-ajax.php' ),
			'bookmarkpage'        => esc_url( $bookmark_page ),
			'emailempty'          => esc_html__( 'Please enter email.', 'penci-bookmark-follow' ),
			'emailinvalid'        => esc_html__( 'Please enter valid email.', 'penci-bookmark-follow' ),
			'loginflag'           => $loggin_flag,
			'processing'          => esc_html__( 'Processing', 'penci-bookmark-follow' ),
			'popup'               => (boolean) get_theme_mod( 'pencibf_enable_popup_notify', true ),
			'popup_text_cl'       => get_theme_mod( 'pencibf_popup_notify_text_cl' ),
			'popup_bg_cl'         => get_theme_mod( 'pencibf_popup_notify_bg_cl' ),
			'popup_timeout'       => get_theme_mod( 'pencibf_popup_notify_timeout', '2000' ),
			'popup_position'      => get_theme_mod( 'pencibf_popup_position', 'bottom-center' ),
			'popup_success_title' => get_theme_mod( 'pencibf_popup_success_title', __( 'Success', 'penci-bookmark-follow' ) ),
			'popup_success_mess'  => get_theme_mod( 'pencibf_popup_success_mess', __( 'Success add to the Bookmark list', 'penci-bookmark-follow' ) ),
			'popup_remove_title'  => get_theme_mod( 'pencibf_popup_remove_title', __( 'Removed', 'penci-bookmark-follow' ) ),
			'popup_remove_mess'   => get_theme_mod( 'pencibf_popup_remove_mess', __( 'Post remove from Bookmark list', 'penci-bookmark-follow' ) ),

			'popup_success_author_title' => get_theme_mod( 'pencibf_popup_success_author_title', __( 'Success', 'penci-bookmark-follow' ) ),
			'popup_success_author_mess'  => get_theme_mod( 'pencibf_popup_success_author_mess', __( 'Successfully add author from the favorite list', 'penci-bookmark-follow' ) ),
			'popup_remove_author_title'  => get_theme_mod( 'pencibf_popup_remove_author_title', __( 'Removed', 'penci-bookmark-follow' ) ),
			'popup_remove_author_mess'   => get_theme_mod( 'pencibf_popup_remove_author_mess', __( 'Successfully remove author from the favorite list', 'penci-bookmark-follow' ) ),

			'popup_success_term_title' => get_theme_mod( 'pencibf_popup_success_term_title', __( 'Success', 'penci-bookmark-follow' ) ),
			'popup_success_term_mess'  => get_theme_mod( 'pencibf_popup_success_term_mess', __( 'Successfully add category from the favorite list', 'penci-bookmark-follow' ) ),
			'popup_remove_term_title'  => get_theme_mod( 'pencibf_popup_remove_term_title', __( 'Removed', 'penci-bookmark-follow' ) ),
			'popup_remove_term_mess'   => get_theme_mod( 'pencibf_popup_remove_term_mess', __( 'Successfully remove category from the favorite list', 'penci-bookmark-follow' ) ),
		) );

		wp_register_script( 'ajax-chosen', PENCI_BL_URL . '/inc/js/chosen/ajax-chosen.jquery.js', array( 'jquery' ), PENCI_BL_VERSION, true );
		wp_register_script( 'penci-bf-admin-scripts', PENCI_BL_URL . '/inc/js/admin.js', array(
			'jquery',
			'jquery-ui-datepicker',
			'jquery-ui-sortable'
		), PENCI_BL_VERSION, true );
	}

	/**
	 * Display button in post / page container
	 *
	 * Handles to display button in post / page container
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function penci_bl_shortcode_display_button( $buttons ) {

		array_push( $buttons, "|", "penci_bl_follow_post" );

		return $buttons;
	}

	/**
	 * style on head of page
	 *
	 * Handles style code display when wp head initialize
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.8.5
	 */
	public function penci_bl_custom_style() {

		//Get custom css code
		global $penci_bl_options;

		if ( ! empty( $penci_bl_options['custom_css'] ) ) {//if custom css code not available
			echo '<style>' . $penci_bl_options['custom_css'] . '</style>';
		}
	}

	/**
	 * Adding Hooks
	 *
	 * Adding proper hoocks for the scripts.
	 *
	 * @package Penci Bookmark Follow
	 * @since 1.0.0
	 */
	public function add_hooks() {

		//add styles for admin
		add_action( 'admin_enqueue_scripts', array( $this, 'penci_bl_admin_print_styles' ) );

		//add scripts for admin
		add_action( 'admin_enqueue_scripts', array( $this, 'penci_bl_admin_enqueue_scripts' ) );

		//add styles for public
		add_action( 'wp_enqueue_scripts', array( $this, 'penci_bl_public_print_styles' ) );

		//script for front side
		add_action( 'wp_enqueue_scripts', array( $this, 'penci_bl_front_scripts' ) );

		//style code on wp head
		add_action( 'wp_head', array( $this, 'penci_bl_custom_style' ) );
	}
}