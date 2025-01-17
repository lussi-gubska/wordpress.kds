<?php

class PenciPostcast {
	/**
	 * @var PenciPostcast
	 */
	private static $instance;

	/**
	 * @return PenciPostcast
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Init constructor.
	 */
	private function __construct() {
		// Register Portfolio Post Type
		add_action( 'init', array( $this, 'register_podcast_post_type' ) );

		// Register Portfolio Category
		add_action( 'init', array( $this, 'register_podcast_category' ) );
		add_action( 'init', array( $this, 'register_podcast_series' ) );
		add_filter( 'template_include', array( $this, 'podcast_template' ) );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'elementor/widgets/register', [ $this, 'elementor_widget' ] );
		add_action( 'soledad_theme/custom_css', [ $this, 'custom_css' ] );
	}


	/**
	 * @return mixed|void
	 */
	public function localize_script() {
		$option = array();

		$option['lang'] = array(
			'added_queue' => pencipdc_translate( 'add_to_queue' ),
			'failed'      => pencipdc_translate( 'wrong' ),
		);

		$option['player_option'] = sanitize_title( get_bloginfo( 'name' ) ) . '-penci-player';
		$option['ajax_url']      = esc_url( admin_url( 'admin-ajax.php' ) );

		return $option;
	}

	public function register_assets() {
		wp_enqueue_style( 'penci-podcast', plugin_dir_url( __DIR__ ) . 'assets/style.css', '', PENCI_PODCAST_VERSION );
		wp_enqueue_script( 'penci-podcast', plugin_dir_url( __DIR__ ) . 'assets/js/plugin.js', [ 'jquery' ], PENCI_PODCAST_VERSION, true );
		wp_localize_script( 'penci-podcast', 'pencipodcast', $this->localize_script() );
	}

	public function elementor_widget( $widgets_manager ) {
		if ( class_exists( 'PenciSoledadElementor\Base\Base_Widget' ) ) {
			require_once( plugin_dir_path( __DIR__ ) . 'builder/elementor/podcast_listing.php' );
			require_once( plugin_dir_path( __DIR__ ) . 'builder/elementor/podcast_series.php' );
			require_once( plugin_dir_path( __DIR__ ) . 'builder/elementor/podcast_categories.php' );
			$widgets_manager->register( new \PenciPodcastElementor() );
			$widgets_manager->register( new \PenciPodcastSeriesElementor() );
			$widgets_manager->register( new \PenciPodcastCategoriesElementor() );
		}
	}

	public function podcast_template( $template ) {
		if ( is_tax( [ 'podcast-category', 'podcast-tag' ] ) ) {
			$template = PENCI_PODCAST_DIR . '/templates/archive-podcast.php';
		}

		if ( is_tax( 'podcast-series' ) ) {
			$template = PENCI_PODCAST_DIR . '/templates/archive-series.php';
		}

		return $template;
	}

	public function register_podcast_post_type() {
		$labels = array(
			'name'               => _x( 'Podcast', 'post type general name', 'penci-podcast' ),
			'singular_name'      => _x( 'Podcast', 'post type singular name', 'penci-podcast' ),
			'add_new'            => __( 'Add New', 'penci-podcast' ),
			'add_new_item'       => __( 'Add New Podcast', 'penci-podcast' ),
			'edit_item'          => __( 'Edit Podcast', 'penci-podcast' ),
			'new_item'           => __( 'New Podcast', 'penci-podcast' ),
			'all_items'          => __( 'All Podcasts', 'penci-podcast' ),
			'view_item'          => __( 'View Podcast', 'penci-podcast' ),
			'search_items'       => __( 'Search Podcast', 'penci-podcast' ),
			'not_found'          => __( 'No podcast found', 'penci-podcast' ),
			'not_found_in_trash' => __( 'No podcast found in Trash', 'penci-podcast' ),
			'parent_item_colon'  => '',
			'menu_name'          => _x( 'Podcast', 'post type general name', 'penci-podcast' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-playlist-audio',
			'query_var'          => 'podcast',
			'rewrite'            => array( 'slug' => 'podcast' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
		);

		register_post_type( 'podcast', $args );
	}

	public function register_podcast_category() {
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'penci-podcast' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'penci-podcast' ),
			'search_items'      => __( 'Search Podcast Categories', 'penci-podcast' ),
			'all_items'         => __( 'All Podcast Categories', 'penci-podcast' ),
			'parent_item'       => __( 'Parent Podcast Category', 'penci-podcast' ),
			'parent_item_colon' => __( 'Parent Podcast Category:', 'penci-podcast' ),
			'edit_item'         => __( 'Edit Podcast Category', 'penci-podcast' ),
			'update_item'       => __( 'Update Podcast Category', 'penci-podcast' ),
			'add_new_item'      => __( 'Add New Podcast Category', 'penci-podcast' ),
			'new_item_name'     => __( 'New Podcast Category Name', 'penci-podcast' ),
			'menu_name'         => __( 'Categories', 'penci-podcast' )
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'podcast-category' )
		);

		register_taxonomy( 'podcast-category', array( 'podcast' ), $args );
	}

	public function register_podcast_series() {
		$labels = array(
			'name'              => _x( 'Podcast Series', 'taxonomy general name', 'penci-podcast' ),
			'singular_name'     => _x( 'Podcast Series', 'taxonomy singular name', 'penci-podcast' ),
			'search_items'      => __( 'Search Podcast Series', 'penci-podcast' ),
			'all_items'         => __( 'All Podcast Series', 'penci-podcast' ),
			'parent_item'       => __( 'Parent Podcast Series', 'penci-podcast' ),
			'parent_item_colon' => __( 'Parent Podcast Series:', 'penci-podcast' ),
			'edit_item'         => __( 'Edit Podcast Series', 'penci-podcast' ),
			'update_item'       => __( 'Update Podcast Series', 'penci-podcast' ),
			'add_new_item'      => __( 'Add New Podcast Series', 'penci-podcast' ),
			'new_item_name'     => __( 'New Podcast Series Name', 'penci-podcast' ),
			'menu_name'         => __( 'Series', 'penci-podcast' )
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'podcast-series' )
		);

		register_taxonomy( 'podcast-series', array( 'podcast' ), $args );
	}

	public function custom_css() {
		$out        = '';
		$custom_css = [
			'pencipodcast_player_closetxtcolor' => '.pencipdc_dock_player_button span{color: {{VALUE}}}',
			'pencipodcast_player_closebgcolor'  => '.pencipdc_dock_player_button span{background: {{VALUE}}}',
			'pencipodcast_player_trackbgcolor'  => '.pencipdc_progress_bar__seek, .pencipdc_volume_bar{background-color: {{VALUE}}}',
			'pencipodcast_player_bgcolor'       => '.pencipdc_mobile_player_wrapper,.pencipdc_player_control__playlist,.pencipdc_player_inner{background-color: {{VALUE}}}',
			'pencipodcast_player_textcolor'     => '.pencipdc_player_bar__current_time, .pencipdc_player_bar__duration,.pencipdc_dock_player .pencipdc_player_control a,.pencipdc_mobile_player_wrapper a,.pencipdc_player_control__close_player,.pencipdc_control_bar_toggle_player .pencipdc_player_control__toggle_player{color: {{VALUE}}}.pencipdc_podcast{--pcheading-cl:{{VALUE}}}',
			'pencipodcast_player_boderscolor'   => '.pencipdc_player_control__playlist .pencipdc_block_heading,.pencipdc_control_bar_left .pencipdc_player_control__play .fa,.pencipdc_player_control .pencipdc_player_control__pause .fa, .pencipdc_player_control .pencipdc_player_control__play .fa{border-color: {{VALUE}}}',
			'pencipodcast_player_activecolor'   => '.pencipdc_progress_bar__play, .pencipdc_volume_bar__value{background-color: {{VALUE}}}.pencipdc_podcast{--pcaccent-cl:{{VALUE}}}',
		];

		foreach ( $custom_css as $mod => $selector ) {
			$value = get_theme_mod( $mod );
			if ( $value ) {
				$out .= str_replace( '{{VALUE}}', $value, $selector );
			}
		}

		echo $out;

	}
}

PenciPostcast::instance();