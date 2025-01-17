<?php

class PenciAI_Menu {

	private $admin;

	// Constructor function
	public function __construct( $a ) {
		// Add an action hook to create the menu page

		$this->admin = $a;

		if ( $a->hasAccess() ) {
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 90 );
		}

	}


	// Function to add the menu page
	public function add_menu_page() {
		// Use the add_menu_page function to add a new menu page to the WordPress dashboard

		add_submenu_page(
			'soledad_dashboard_welcome', // Parent menu slug
			__( 'AI Image Generator', 'penci-ai' ), // Page title
			__( 'AI Image Generator', 'penci-ai' ), // Menu title
			'manage_options', // Capability
			'ai-image-generator', // Menu slug
			array( $this, 'ai_image_generator' ), // Function to display the page content
			3
		);

	}

	// Function to render the menu page content

	public function ai_image_generator() {
		require 'menu-pages/image-generator.php';
	}
}


