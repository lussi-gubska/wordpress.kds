<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Penci_Soledad_MG_Helper' ) ) {
	class Penci_Soledad_MG_Helper {
		public static function get_list_theme_actived() {
			global $wpdb;

			$output = array();

			// Get list theme directory
			$sql         = "SELECT SUBSTRING(`option_name`,12) 
					FROM $wpdb->options 
					WHERE option_name 
					LIKE 'theme_mods_%' LIMIT 999";
			$list_themes = $wpdb->get_col( $sql );

			foreach ( $list_themes as $theme_folder ) {
				$theme       = wp_get_theme( $theme_folder );
				$theme_templ = $theme->get( 'Template' );

				if ( ! $theme->exists() ) {
					continue;
				}

				if ( $theme_templ ) {
					$theme = wp_get_theme( $theme_templ );
				}

				$theme_name = $theme->get( 'Name' );
				$stylesheet = $theme->get_stylesheet();

				$output[ $stylesheet ] = $theme_name;

			}

			return $output;
		}

		public static function get_themes_info() {
			return array(
				'jnews'       => array( 'author_name' => 'jegtheme', 'theme_name' => 'JNews', ),
				'jannah'      => array( 'author_name' => 'TieLabs', 'theme_name' => 'Jannah', ),
				'newsmag'     => array( 'author_name' => 'tagDiv', 'theme_name' => 'Newsmag', ),
				'smartmag'    => array( 'author_name' => 'ThemeSphere', 'theme_name' => 'SmartMag', ),
				'newspaper'   => array( 'author_name' => 'tagDiv', 'theme_name' => 'Newspaper', ),
				'publisher'   => array( 'author_name' => 'BetterStudio', 'theme_name' => 'Publisher', ),
				'sahifa'      => array( 'author_name' => 'TieLabs', 'theme_name' => 'Sahifa', ),
				'cheerup'     => array( 'author_name' => 'ThemeSphere', 'theme_name' => 'Cheerup', ),
				'bimber'      => array( 'author_name' => 'bringthepixel', 'theme_name' => 'Bimber', ),
				'sproutspoon' => array( 'author_name' => 'SoloPine', 'theme_name' => 'Sprout & Spoon', ),
				'pennews' 	  => array( 'author_name' => 'PenciDesign', 'theme_name' => 'Pennews', ),
			);
		}

		/**
		 * Get id  of all post on your site
		 * @return array
		 */
		public static function get_post_ids() {
			global $wpdb;

			$sql   = "SELECT ID 
				FROM $wpdb->posts 
				WHERE post_type = 'post' 
				AND post_status != 'trash' 
				AND post_status != 'inherit' 
				AND post_status != 'auto-draft'
				ORDER BY ID DESC";
			$posts = $wpdb->get_results( $sql );

			$post_ids = array();

			foreach ( $posts as $post ) {
				$post_ids[ $post->ID ] = $post->ID;
			}

			return $post_ids;
		}

		public static function get_count_posts() {
			return count( self::get_post_ids() );
		}

		public static function count_posts() {
			echo count( self::get_post_ids() );
		}

		public static function get_option_name() {

			$theme = isset( $_GET['item'] ) ? $_GET['item'] : '';

			return 'soledad_migrator_post_ids' . $theme;
		}

		/**
		 * Get Messenger by type
		 *
		 * @param string $type
		 * @param string $timer
		 *
		 * @return string
		 */
		public static function get_mess_by_type( $post_id, $type = 'success', $timer = '' ) {
			$mess = 'Skipped';

			if ( 'success' == $type ) {
				$mess = sprintf( esc_html__( 'Successfully to migrate Post "' . get_the_title( $post_id ) . '" in %s seconds', 'penci-data-migrator' ), $timer );
			} elseif ( 'review' == $type ) {
				$mess = esc_html__( 'Review Updated', 'penci-data-migrator' );
			} elseif ( 'recipe' == $type ) {
				$mess = esc_html__( 'Recipe Updated', 'penci-data-migrator' );
			} elseif ( 'count_views' == $type ) {
				$mess = esc_html__( 'Post count view Updated', 'penci-data-migrator' );
			} elseif ( 'video_url' == $type ) {
				$mess = esc_html__( 'Video url Updated', 'penci-data-migrator' );
			} elseif ( 'primary_term' == $type ) {
				$mess = esc_html__( 'Primary Category Updated', 'penci-data-migrator' );
			} elseif ( 'post_layout' == $type ) {
				$mess = esc_html__( 'Post Layout Updated', 'penci-data-migrator' );
			} elseif ( 'post_format' == $type ) {
				$mess = esc_html__( 'Post Format Updated', 'penci-data-migrator' );
			} elseif ( 'custom_sidebar' == $type ) {
				$mess = esc_html__( 'Custom Sidebar Updated', 'penci-data-migrator' );
			} elseif ( 'sub_title' == $type ) {
				$mess = esc_html__( 'Sub Title Updated', 'penci-data-migrator' );
			}

			return $mess;
		}

		public static function update_category_primary( $post_id, $primary_category, $mess ) {
			if ( ! $primary_category ) {
				return;
			}

			$meta_prefix_seo = class_exists( 'WPSEO_Meta' ) ? WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
			$primary_term    = update_post_meta( $post_id, $meta_prefix_seo . 'primary_category', true );
			if ( $primary_term ) {
				$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'primary_term' );
			}

			return $mess;
		}

		public static function get_param_sidebars() {


			$before_widget = '<aside id="%1$s" class="widget %2$s">';
			$after_widget  = '</aside>';
			$before_title  = '<h4 class="widget-title penci-border-arrow"><span class="inner-arrow">';
			$after_title   = '</span></h4>';


			return array( $before_widget, $after_widget, $before_title, $after_title );
		}

		public static function convert_sidebar_id( $sidebar ) {
			$sidebar_id = str_replace( array( ' ' ), '-', trim( $sidebar ) );
			$sidebar_id = str_replace( array( "'", '"' ), '', trim( $sidebar_id ) );
			$sidebar_id = 'td-' . strtolower( $sidebar_id );

			return $sidebar_id;
		}

	}
}
