<?php

class Penci_FTE_Admin {

	private static $instance;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'register_filter_type' ] );
		add_action( 'penci_get_options_data', [ $this, 'load_admin_options' ] );
		add_action( 'init', [ $this, 'load_customizer_options' ] );

		self::load_metabox();
	}

	public function load_metabox() {
		if ( self::is_penci_filter_edit_page() ) {

			require_once PENCI_FTE_DIR . '/lib/metabox/classes/setup.class.php';
			require_once PENCI_FTE_DIR . '/inc/metabox.php';

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		}
	}

	public function load_customizer_options() {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once PENCI_FTE_DIR . '/customizer/panel.php';
			require_once PENCI_FTE_DIR . '/customizer/settings.php';
			\SoledadFW\PenciFTECustomizer::getInstance();
		}
	}
	

	public function load_admin_options( $options ) {

		$options['penci_fte_panel'] = array(
			'priority'                    => 30,
			'path'                        => PENCI_FTE_DIR . '/customizer/',
			'panel'                       => array(
				'icon'  => 'fas fa-bookmark',
				'title' => esc_html__( 'Filter Everything', 'penci-filter-everything' ),
			),
			'penci_fte_general_section'   => array( 'title' => esc_html__( 'Colors & Font Size', 'penci-bookmark-follow' ) ),
			'penci_fte_translate_section' => array( 'title' => esc_html__( 'Quick Text Translation', 'penci-bookmark-follow' ) ),
		);

		return $options;
	}

	public function admin_scripts() {
		wp_enqueue_style( 'penci-fte-admin', PENCI_FTE_URL . 'assets/admin.css', [], PENCI_FTE_VERSION );
	}

	public function is_penci_filter_edit_page() {
		// Check if we are in the admin area
		if ( ! is_admin() ) {
			return false;
		}

		global $pagenow;

		// Check if it's the 'penci-filter' post type
		if ( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) {
			return true;
		}

		return false;
	}

	public function register_filter_type() {

		$labels = array(
			'name'                     => __( 'Penci Filters', 'penci-filter-everything' ),
			'singular_name'            => __( 'Penci Filter', 'penci-filter-everything' ),
			'add_new'                  => __( 'Add New', 'penci-filter-everything' ),
			'add_new_item'             => __( 'Add New Filter', 'penci-filter-everything' ),
			'edit_item'                => __( 'Edit Filter', 'penci-filter-everything' ),
			'new_item'                 => __( 'New Filter', 'penci-filter-everything' ),
			'view_item'                => __( 'View Filter', 'penci-filter-everything' ),
			'view_items'               => __( 'View Filters', 'penci-filter-everything' ),
			'search_items'             => __( 'Search Filters', 'penci-filter-everything' ),
			'not_found'                => __( 'No Filters found.', 'penci-filter-everything' ),
			'not_found_in_trash'       => __( 'No Filters found in Trash.', 'penci-filter-everything' ),
			'parent_item_colon'        => __( 'Parent Filters:', 'penci-filter-everything' ),
			'all_items'                => __( 'All Filters', 'penci-filter-everything' ),
			'archives'                 => __( 'Penci Archives', 'penci-filter-everything' ),
			'attributes'               => __( 'Penci Attributes', 'penci-filter-everything' ),
			'insert_into_item'         => __( 'Insert into Penci Filter', 'penci-filter-everything' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this Penci Filter', 'penci-filter-everything' ),
			'featured_image'           => __( 'Featured Image', 'penci-filter-everything' ),
			'set_featured_image'       => __( 'Set featured image', 'penci-filter-everything' ),
			'remove_featured_image'    => __( 'Remove featured image', 'penci-filter-everything' ),
			'use_featured_image'       => __( 'Use as featured image', 'penci-filter-everything' ),
			'menu_name'                => __( 'Penci Filters', 'penci-filter-everything' ),
			'filter_items_list'        => __( 'Filter Penci Filter list', 'penci-filter-everything' ),
			'filter_by_date'           => __( 'Filter by date', 'penci-filter-everything' ),
			'items_list_navigation'    => __( 'Penci Filters list navigation', 'penci-filter-everything' ),
			'items_list'               => __( 'Penci Filters list', 'penci-filter-everything' ),
			'item_published'           => __( 'Penci Filter published.', 'penci-filter-everything' ),
			'item_published_privately' => __( 'Penci Filter published privately.', 'penci-filter-everything' ),
			'item_reverted_to_draft'   => __( 'Penci Filter reverted to draft.', 'penci-filter-everything' ),
			'item_scheduled'           => __( 'Penci Filter scheduled.', 'penci-filter-everything' ),
			'item_updated'             => __( 'Penci Filter updated.', 'penci-filter-everything' ),
			'item_link'                => __( 'Penci Filter Link', 'penci-filter-everything' ),
			'item_link_description'    => __( 'A link to an Penci Filter.', 'penci-filter-everything' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Manage Penci Filters', 'penci-filter-everything' ),
			'public'              => false,
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-filter',
			'capability_type'     => 'post',
			'capabilities'        => array(),
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'delete_with_user'    => false,
			'template'            => array(),
			'template_lock'       => false,
		);

		register_post_type( 'penci-filter', $args );

	}
}