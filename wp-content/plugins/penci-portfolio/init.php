<?php
/*
Plugin Name: Penci Portfolio
Plugin URI: http://pencidesign.net/
Description: Portfolio Plugin for Soledad theme.
Version: 3.5
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define
 */
define( 'PENCI_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );
define( 'PENCI_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main class for Soledad Portfolio
 * Add this plugin to Soledad theme, you'll portfolio
 * Don't support when you use this plugin to another theme
 *
 * @author PenciDesign ( http://pencidesign.com/ )
 * @since 1.0
 */
if ( ! class_exists( 'Penci_Portfolio' ) ):

	class Penci_Portfolio {

		/**
		 * A reference to an instance of this class.
		 */
		private static $instance;


		/**
		 * Returns an instance of this class.
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new Penci_Portfolio();
			}

			return self::$instance;

		}

		/**
		 * Initializes the plugin by setting filters and administration functions.
		 */
		private function __construct() {

			// Include main shortcode for portfolio
			include_once( 'inc/metabox.php' );
			include_once( 'inc/portfolio.php' );

			// load plugin text domain for translations
			add_action( 'init', array( $this, 'load_text_domain' ) );

			// Register Portfolio Post Type
			add_action( 'init', array( $this, 'register_portfolio_post_type' ) );

			// Register Portfolio Category
			add_action( 'init', array( $this, 'register_portfolio_category' ) );

			// Override Single Portfolio Template
			add_filter( 'single_template', array( $this, 'register_portfolio_single' ) );

			// Override Categories Portfolio Template
			add_filter( 'template_include', array( $this, 'register_portfolio_categories' ) );

			// Custom Posts Per Page on Portfolio Categories
			add_action( 'pre_get_posts', array( $this, 'portfolio_custom_posts_per_page' ) );

			add_action( 'wp_head', array( $this, 'penci_global_js' ), 10 );

			add_action( 'admin_enqueue_scripts', array( $this, 'penci_load_admin_metabox_style' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'penci_load_portfolio_style' ), 90 );
			add_action( 'soledad_theme/custom_css', array( $this, 'single_portfolio_custom_style' ) );
			add_action( 'template_redirect', array( $this, 'portfolio_archive_page' ) );
			add_action( 'init', array( $this, 'settings' ) );
			add_action( 'penci_get_options_data', array( $this, 'options' ) );
		}

		public function options( $options ) {

			$options['penci_portfolio_section_panel'] = array(
				'path'                               => plugin_dir_path( __FILE__ ) . '/customizer/',
				'priority'                                => 18,
				'panel'                                   => array(
					'title' => esc_html__( 'Portfolio', 'soledad' ),
					'icon'  => 'fas fa-briefcase',
				),
				'pencidesign_portfolio_sgeneral_section'  => array( 'title' => esc_html__( 'General', 'soledad' ) ),
				'pencidesign_portfolio_scolor_section'    => array( 'title' => esc_html__( 'Colors', 'soledad' ) ),
				'pencidesign_portfolio_sfontsize_section' => array( 'title' => esc_html__( 'Font Size', 'soledad' ) ),
				'pencidesign_portfolio_sadvanced_section' => array( 'title' => esc_html__( 'Advanced', 'soledad' ) ),
			);
			return $options;
		}

		public function settings() {
			if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
				require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
				\SoledadFW\PenciPortfolioCustomizer::getInstance();
			}
		}

		public function portfolio_archive_page() {
			$id_page = get_theme_mod( 'penci_portfolio_cspage' );
			if ( $id_page && is_post_type_archive( 'portfolio' ) ) {
				wp_redirect( get_permalink( $id_page ), 301 );
				exit();
			}
		}

		/**
		 * Add admin meta box style
		 */
		public function penci_load_admin_metabox_style() {
			$screen = get_current_screen();
			if ( $screen->id == 'portfolio' ) {
				wp_enqueue_style( 'penci_portfolio_meta_box_styles', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
			}
		}

		/**
		 * Add admin meta box style
		 */
		public function penci_load_portfolio_style() {
			wp_enqueue_script( 'penci_portfolio_extra', plugin_dir_url( __FILE__ ) . 'js/penci-portfolio.js', array( 'jquery' ), '1.0', true );
		}

		function penci_global_js() {
			$output = '<script>var portfolioDataJs = portfolioDataJs || [];</script>';
			echo $output;
		}

		/**
		 * Transition ready
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'penci-portfolio', false, PENCI_PORTFOLIO_DIR . '/languages/' );
		}

		/**
		 * Register Portfolio Post Type
		 */
		public function register_portfolio_post_type() {
			$labels = array(
				'name'               => self::get_name(),
				'singular_name'      => self::get_name(),
				'add_new'            => __( 'Add New', 'penci-portfolio' ),
				'add_new_item'       => __( 'Add New Project', 'penci-portfolio' ),
				'edit_item'          => __( 'Edit Project', 'penci-portfolio' ),
				'new_item'           => __( 'New Project', 'penci-portfolio' ),
				'all_items'          => __( 'All Projects', 'penci-portfolio' ),
				'view_item'          => __( 'View Projects', 'penci-portfolio' ),
				'search_items'       => __( 'Search Projects', 'penci-portfolio' ),
				'not_found'          => __( 'No projects found', 'penci-portfolio' ),
				'not_found_in_trash' => __( 'No projects found in Trash', 'penci-portfolio' ),
				'parent_item_colon'  => '',
				'menu_name'          => self::get_name(),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => self::get_slug(),
				'rewrite'            => array( 'slug' => self::get_slug() ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			if ( ! get_theme_mod( 'penci_portfolio_classic_editor' ) ) {
				$args['show_in_rest'] = true;
			}

			register_post_type( 'portfolio', $args );
		}

		/**
		 * Register Portfolio Categories
		 */
		public function register_portfolio_category() {
			$labels = array(
				'name'              => self::get_name() . ' ' . __( 'Categories', 'penci-portfolio' ),
				'singular_name'     => self::get_name() . ' ' .  __( 'Category', 'penci-portfolio' ),
				'search_items'      => __( 'Search Categories', 'penci-portfolio' ),
				'all_items'         => __( 'All Categories', 'penci-portfolio' ),
				'parent_item'       => __( 'Parent Category', 'penci-portfolio' ),
				'parent_item_colon' => __( 'Parent Category:', 'penci-portfolio' ),
				'edit_item'         => __( 'Edit Category', 'penci-portfolio' ),
				'update_item'       => __( 'Update Category', 'penci-portfolio' ),
				'add_new_item'      => __( 'Add New Category', 'penci-portfolio' ),
				'new_item_name'     => __( 'New Category Name', 'penci-portfolio' ),
				'menu_name'         => self::get_name() . ' ' .  __( 'Categories', 'penci-portfolio' )
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => self::get_slug_tax() )
			);

			register_taxonomy( 'portfolio-category', array( 'portfolio' ), $args );
		}

		/**
		 * Register single portfolio template
		 */
		public function register_portfolio_single( $single_template ) {
			global $post;
			if ( $post->post_type == 'portfolio' ) {
				$single_template = dirname( __FILE__ ) . '/inc/single-portfolio.php';
			}

			return $single_template;
		}

		/**
		 *    Register categories portfolio template
		 */
		public function register_portfolio_categories( $template ) {
			if ( is_tax( 'portfolio-category' ) ) {
				$template = dirname( __FILE__ ) . '/inc/taxonomy-portfolio-category.php';
			}

			return $template;
		}

		/**
		 * Custom posts per page portfolio categories listing
		 */
		public function portfolio_custom_posts_per_page( $query ) {
			if ( is_tax( 'portfolio-category' ) && $query->is_main_query() ) {
				$numpost = get_theme_mod( 'penci_portfolio_cat_showposts' );
				if ( ! isset ( $numpost ) || empty( $numpost ) ): $numpost = '12'; endif;
				$query->set( 'posts_per_page', $numpost );
			}
		}

		public static function get_image_ratio( $postid, $image_thumb, $padding = false, $echo = true ) {
			$ratio  = '66.6666667';
			$output = '';

			if ( has_post_thumbnail( $postid ) ) {
				$image      = get_the_post_thumbnail( $postid, $image_thumb );
				$img_url    = get_the_post_thumbnail_url( $postid, $image_thumb );
				$image_type = substr( $img_url, - 4 );
				if ( '.gif' == $image_type ) {
					$image   = get_the_post_thumbnail( $postid, 'full' );
					$img_url = get_the_post_thumbnail_url( $postid, 'full' );
				}
			} else {
				$image   = '<img src="' . PENCI_PORTFOLIO_URL . '/images/no-thumbnail.jpg" alt="' . __( "No Thumbnail", "pencidesign" ) . '" />';
				$img_url = PENCI_PORTFOLIO_URL . '/images/no-thumbnail.jpg';
			}

			if ( preg_match_all( '#(width|height)=(\'|")?(?<dimensions>[0-9]+)(\'|")?#i', $image, $image_dis ) && 2 == count( (array) $image_dis['dimensions'] ) ) {
				$ratio = self::get_pre_ratio( $image_dis['dimensions'][0], $image_dis['dimensions'][1] );
			}

			if ( $padding && $ratio ) {
				$ratio = $padding;
			}

			$output = '<span class="penci-image-placeholder penci-lazy" style="padding-bottom:' . $ratio . '%;" data-bgset="' . $img_url . '"></span>';

			if ( ! $echo ) {
				return $output;
			}

			echo $output;
		}

		public static function get_pre_ratio( $width, $height ) {
			return number_format( $height / $width * 100, 8 );
		}

		public static function get_slug() {
			return get_theme_mod( 'penci_pfl_custom_slug' ) ? get_theme_mod( 'penci_pfl_custom_slug' ) : 'portfolio';
		}

		public static function get_name() {
			return get_theme_mod( 'penci_pfl_custom_name' ) ? get_theme_mod( 'penci_pfl_custom_name' ) : __( 'Portfolio','penci-portfolio' );
		}

		public static function get_slug_tax() {
			return get_theme_mod( 'penci_pfl_custom_catslug' ) ? get_theme_mod( 'penci_pfl_custom_catslug' ) : 'portfolio-category';
		}

		public function single_portfolio_custom_style() {
			$settings_color = array(
				'penci_portfolio_single_title_color'                      => array(
					'color' => 'body.single-portfolio .penci-page-header h1'
				),
				'penci_portfolio_single_text_color'                       => array(
					'color' => 'body.single-portfolio .post-entry p, body.single-portfolio .penci-shortdesc, body.single-portfolio .wpb_text_column p'
				),
				'penci_portfolio_single_meta_label_color'                 => array(
					'color' => 'ul.portfolio-meta-lists span.title'
				),
				'penci_portfolio_single_meta_value_color'                 => array(
					'color' => 'ul.portfolio-meta-lists span.value'
				),
				'penci_portfolio_single_border_color'                     => array(
					'border-color' => '.portfolio-share-box > span,body.single-portfolio .post-pagination,body.single-portfolio .post-related'
				),
				'penci_portfolio_single_relate_title_color'               => array(
					'color' => '.portfolio-releated-area .item-related h3 a',
				),
				'penci_portfolio_single_relate_title_hover_color'         => array(
					'color' => '.portfolio-releated-area .item-related h3 a:hover',
				),
				'penci_portfolio_single_relate_cat_color'                 => array(
					'color' => '.portfolio-releated-area .item-related .portfolio-cat a',
				),
				'penci_portfolio_single_relate_cat_hover_color'           => array(
					'color' => '.portfolio-releated-area .item-related .portfolio-cat a:hover',
				),
				'penci_portfolio_single_text_link_color'                  => array(
					'color' => 'body.single-portfolio .post-entry a, body.single-portfolio .wpb_text_column a'
				),
				'penci_portfolio_single_text_link_hover_color'            => array(
					'color' => 'body.single-portfolio .post-entry a:hover, body.single-portfolio .wpb_text_column a:hover'
				),
				'penci_portfolio_single_carousel_background_color'        => array(
					'background-color' => '.penci-owl-carousel-slider .owl-dot span, .penci-related-carousel .owl-dot span',
				),
				'penci_portfolio_single_carousel_border_color'            => array(
					'border-color' => '.penci-owl-carousel-slider .owl-dot span, .penci-related-carousel .owl-dot span',
				),
				'penci_portfolio_single_carousel_active_background_color' => array(
					'background-color' => '.penci-owl-carousel-slider .owl-dot.active span, .penci-related-carousel .owl-dot.active span',
				),
				'penci_portfolio_single_carousel_active_border_color'     => array(
					'border-color' => '.penci-owl-carousel-slider .owl-dot.active span, .penci-related-carousel .owl-dot.active span',
				),
			);

			$font_size = array(
				'penci_portfolio_single_title_fz'       => array(
					'font-size' => 'body.single-portfolio .penci-page-header h1',
				),
				'penci_portfolio_single_txt_fz'         => array(
					'font-size' => 'body.single-portfolio .post-entry p, body.single-portfolio .penci-shortdesc, body.single-portfolio .wpb_text_column p'
				),
				'penci_portfolio_single_txt_lh_fz'      => array(
					'line-height' => 'body.single-portfolio .post-entry p, body.single-portfolio .penci-shortdesc, body.single-portfolio .wpb_text_column p'
				),
				'penci_portfolio_single_meta_fz'        => array(
					'font-size' => 'body.single-portfolio ul.portfolio-meta-lists span'
				),
				'penci_portfolio_single_nextprev_fz'    => array(
					'font-size' => 'body.single-portfolio .project-pagination a'
				),
				'penci_portfolio_single_related_tt_fz'  => array(
					'font-size' => 'body.single-portfolio .portfolio-releated-area .item-related h3 a'
				),
				'penci_portfolio_single_related_cat_fz' => array(
					'font-size' => 'body.single-portfolio .portfolio-releated-area .item-related .portfolio-cat a'
				),
			);

			if ( is_singular( 'portfolio' ) ) {
				echo $this->css_parse( $settings_color );
				echo $this->css_parse( $font_size );
				echo $this->css_parse( $font_size, true );
			}

		}

		public function css_parse( $css, $mobile = false ) {
			$css_out = $before = $after = '';

			foreach ( $css as $setting => $props ) {
				$setting = $mobile ? str_replace( '_fz', '_mfz', $setting ) : $setting;
				$value   = get_theme_mod( $setting );
				$prefix  = is_numeric( $value ) ? 'px' : '';
				if ( ! empty( $value ) ) {
					foreach ( $props as $prop => $selector ) {
						$css_out .= $selector . '{' . $prop . ':' . $value . $prefix . '}';
					}
				}
			}

			if ( $mobile ) {
				$before = '@media only screen and (max-width:767px){';
				$after  = '}';
			}

			return $before . $css_out . $after;
		}

	}

	add_action( 'plugins_loaded', array( 'Penci_Portfolio', 'get_instance' ) );

endif; /* End check if class exists */

if ( ! function_exists( 'portfolio_meta_content' ) ) {
	function portfolio_meta_content( $social_style = 'style-1', $social_enable = true, $sticky = false, $page_title = false, $echo = true ) {
		$post_id             = get_the_ID();
		$portfolio_max_lists = apply_filters( 'penci_portfolio_list_numer', 5 );

		$short_desc = get_post_meta( $post_id, 'penci_portfolio_desc', true );


		$meta = '';
		$out  = '';

		for ( $i = 1; $i <= $portfolio_max_lists; $i ++ ) {
			$title = get_post_meta( $post_id, 'penci_portfolio_label_' . $i, true );
			$value = get_post_meta( $post_id, 'penci_portfolio_value_' . $i, true );

			if ( $title && $value ) {
				$meta .= '<li>
                                <span class="title">' . esc_attr( $title ) . '</span>
                                <span class="value">' . esc_attr( $value ) . '</span>
                            </li>';
			}

		}

		if ( $meta || $short_desc ) {
			$out .= '<div class="penci-portfolio-meta-wrapper">';

			if ( $sticky ) {
				$out .= '<div class="theiaStickySidebar">';
			}

			if ( $page_title ) {
				$out .= '<div class="penci-page-header"><h1>' . get_the_title() . '</h1></div>';
			}


			if ( $short_desc ) {
				$out .= '<div class="penci-portfolio-col penci-shortdesc post-entry">' . $short_desc . '</div>';
			}

			if ( $meta ) {
				$out .= '<div class="penci-portfolio-col penci-meta-lists"><ul class="portfolio-meta-lists">' . $meta . penci_portfolio_share_html( $social_style, $social_enable ) . '</ul></div>';
			}

			if ( $sticky ) {
				$out .= '</div>';
			}

			$out .= '</div>';
		}

		if ( $echo ) {
			echo $out;
		} else {
			$return = 'has_content';
			if ( empty( $meta ) && empty( $short_desc ) ) {
				$return = 'no_content';
			} elseif ( empty( $meta ) ) {
				$return = 'no_share';
			}

			return $return;
		}
	}
}
if ( ! function_exists( 'penci_portfolio_single_settings' ) ) {
	function penci_portfolio_single_settings( $customize, $meta_key, $default ) {
		$customize_setting = get_theme_mod( $customize, $default );
		$single_setting    = get_post_meta( get_the_ID(), $meta_key, true );

		return ! empty( $single_setting ) ? $single_setting : $customize_setting;
	}
}

if ( ! function_exists( 'penci_portfolio_option2logic' ) ) {
	function penci_portfolio_option2logic( $setting, $res = false ) {
		if ( $res ) {
			return 'enable' == $setting ? false : true;
		} else {
			return 'enable' == $setting;
		}

	}
}
if ( ! function_exists( 'penci_portfolio_share_html' ) ) {
	function penci_portfolio_share_html( $style = 'style-1', $enable = true, $format = 'li' ) {
		ob_start();
		?>
        <<?php echo $format; ?> class="portfolio-share-box tags-share-box <?php echo esc_attr( $style ); ?>">

        <span class="title share-title">
								<?php if ( get_theme_mod( 'penci_trans_share' ) ) {
									echo do_shortcode( get_theme_mod( 'penci_trans_share' ) );
								} else {
									esc_html_e( 'Share', 'soledad' );
								} ?></span>
        <span class="value list-posts-share">
            <?php penci_soledad_social_share(); ?>
        </span>

        </<?php echo $format; ?>>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( $enable ) {
			return $output;
		} else {
			return false;
		}
	}
}
if ( ! function_exists( 'penci_relate_projects_carousel' ) ) {
	function penci_relate_projects_carousel( $enable ) {
		if ( ! $enable ) {
			return false;
		}
		$columns              = get_theme_mod( 'penci_single_portfolio_related_col', 3 );
		$layout               = get_theme_mod( 'penci_single_portfolio_related_layout', 'carousel' );
		$relate_project_query = get_posts( array(
			'post_type'      => 'portfolio',
			'post__not_in'   => array( get_the_ID() ),
			'posts_per_page' => get_theme_mod( 'penci_single_portfolio_related_num', 3 ),
		) );
		$parent_class         = 'penci-related-carousel penci-related-grid-display';
		$parent_atts          = array();
		$lazy_class           = 'penci-lazy';
		if ( 'carousel' == $layout ) {
			$parent_atts[] = 'data-lazy="true"';
			$parent_atts[] = 'data-item="' . esc_attr( $columns ) . '"';
			$parent_atts[] = 'data-desktop="' . esc_attr( $columns ) . '"';
			$parent_atts[] = 'data-tablet="2"';
			$parent_atts[] = 'data-tabsmall="2"';
			$parent_atts[] = 'data-auto="false"';
			$parent_atts[] = 'data-speed="300"';
			$parent_atts[] = 'data-dots="true"';
			$parent_atts[] = 'data-nav="false"';
			$parent_class  = 'penci-owl-carousel penci-owl-carousel-slider penci-related-carousel';
			$lazy_class    = 'owl-lazy';
		}
		$parent_atts = implode( ' ', $parent_atts );
		if ( $relate_project_query ) {
			?>
            <div class="post-related portfolio-releated-area penci-related-projects">
                <div class="post-title-box"><h4
                            class="post-box-title"><?php echo get_theme_mod( 'penci_portfolio_related_text', esc_attr( 'Related Projects', 'soledad' ) ); ?></h4>
                </div>
                <div class="<?php echo esc_attr( $parent_class ); ?>" <?php echo $parent_atts; ?>>
					<?php foreach ( $relate_project_query as $project ):
						$project_link = get_permalink( $project->ID );
						$project_title = get_the_title( $project->ID );
						$get_terms = wp_get_post_terms( $project->ID, 'portfolio-category' );
						?>
                        <div class="item-related">
							<?php if ( ! get_theme_mod( 'penci_disable_lazyload_single', false ) ) { ?>
                                <a class="related-thumb penci-image-holder <?php echo esc_attr( $lazy_class ); ?>"
                                   data-bgset="<?php echo penci_get_featured_image_size( $project->ID, penci_featured_images_size() ); ?>"
                                   href="<?php esc_url( $project_link ); ?>"
                                   title="<?php echo wp_strip_all_tags( $project_title ); ?>"></a>
							<?php } else { ?>
                                <a class="related-thumb penci-image-holder"
                                   style="background-image: url('<?php echo penci_get_featured_image_size( $project->ID, penci_featured_images_size() ); ?>');"
                                   href="<?php esc_url( $project_link ); ?>"
                                   title="<?php echo wp_strip_all_tags( $project_title ); ?>"></a>
							<?php } ?>
                            <h3>
                                <a href="<?php echo esc_url( get_permalink( $project->ID ) ); ?>"><?php echo esc_attr( get_the_title( $project->ID ) ); ?></a>
                            </h3>
							<?php
							if ( ! empty( $get_terms ) ):
								?>
                                <span class="portfolio-cat">
                                   <?php foreach ( $get_terms as $term ): ?>
                                       <a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_attr( $term->name ); ?></a>
                                   <?php endforeach; ?>
                                </span>
							<?php endif; ?>
                        </div>
					<?php endforeach; ?>
                </div>
            </div>
			<?php
		}
	}
}
