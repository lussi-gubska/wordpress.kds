<?php

// Declare a namespace for the class
namespace PenciAIContentGenerator;

class PenciAI_Admin {

	// Define the constructor method
	public function __construct() {
		// Call the require_admin_dependencies method
		$this->require_admin_dependencies();
		// Call the initWpActions method
		$this->initWpActions();

		// Link to settings page from plugins screen
		add_filter( 'plugin_action_links_' . PENCI_AI_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );

	}

	public function add_action_links( $links ) {
		$mylinks = array(
			'<a target="_blank" href="' . add_query_arg( [ 'autofocus[section]' => 'penci_ai_api_section' ], admin_url( 'customize.php' ) ) . '">' . __( 'Settings', 'penci-ai' ) . '</a>',
		);

		return array_merge( $links, $mylinks );
	}

	// Define the require_admin_dependencies method
	public function require_admin_dependencies() {
		// Require the add-menu-page.php file
		require_once 'includes/add-menu-page.php';
		new \PenciAI_Menu( $this );

		//include admin global functions
		include 'includes/admin-global.php';

		//include pcacg settings items
		include 'includes/settings-menu-items.php';

		// Require all the ajax functionality
		require_once 'includes/ajax-requests.php';

		require_once 'includes/register-meta-boxes.php';
		new AddMetaBoxes_( $this );

		//Image generation functions
		require_once 'includes/image-generator-class.php';

	}

	// Define the initWpActions method
	public function initWpActions() {
		if ( $this->hasCurrentPostType()
		     || ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'penci-ai' )
		     || ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'ai-image-generator' )
		) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'post_row_actions', array( $this, 'add_suggest_title_post_row_action' ), 10, 2 );
			add_action( 'page_row_actions', array( $this, 'add_suggest_title_post_row_action' ), 10, 2 );
		}

	}

	public function add_suggest_title_post_row_action( $actions, $post ) {

		$actions = array_merge( $actions, array(
			'suggest_titles' => '<a href="#">' . __( "Suggest titles", "penci-ai" ) . '</a>',
		) );

		return $actions;
	}

	public function hasAccess() {
		require( ABSPATH . WPINC . '/pluggable.php' );
		$capabilities = get_theme_mod( 'penci_ai_user_roles', array( 'administrator' ) );

		if ( ! empty( $capabilities ) ) {
			foreach ( $capabilities as $cap ) {
				if ( current_user_can( $cap ) ) {
					return true;
					break;
				}
			}
		}
		if ( current_user_can( 'administrator' ) ) {
			return true;
		}

		return false;
	}

	public function hasCurrentPostType() {
		$postTypes = get_theme_mod( 'penci_ai_enabled_post_types', array( 'post', 'page', 'product' ) );

		if ( in_array( penciai_get_post_type(), $postTypes ) ) {
			return true;
		}

		return false;
	}
	public function enqueue_scripts( $hook ) {

		wp_enqueue_style( 'jquery-ui-style' );
		wp_enqueue_style( 'penci-ai', PENCI_AI_DIR_URL . 'admin/assets/css/plugin.css', array(), PENCI_AI_VERSION );
		wp_enqueue_style( 'penciai-tinymodal', PENCI_AI_DIR_URL . 'admin/assets/css/jquery.tinymodal.css', array(), PENCI_AI_VERSION );
		wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'penci-ai-tinymodal', PENCI_AI_DIR_URL . 'admin/assets/js/jquery.tinymodal.js', array( 'jquery' ), PENCI_AI_VERSION, true );
		wp_enqueue_script( 'penci-ai', PENCI_AI_DIR_URL . 'admin/assets/js/plugin.js', array(
			'jquery',
		), PENCI_AI_VERSION, true );
		wp_enqueue_script( 'penci-ai-post-editor', PENCI_AI_DIR_URL . 'admin/assets/js/post-editor.js', array( 'jquery' ), PENCI_AI_VERSION, true );
		wp_enqueue_script( 'penci-ai-post-editor-button', PENCI_AI_DIR_URL . 'admin/assets/js/post-editor-button.js', array( 'wp-components' ), PENCI_AI_VERSION, true );
	}
}



