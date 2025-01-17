<?php

/*
Plugin Name: Penci Mobile Template
Plugin URI: https://pencidesign.net/
Description: Create separate Page Content and Templates for desktop and mobile.
Version: 1.2
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-frontend-submission
*/

class Penci_Mobile_Templates {
	private static $instance;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'penci_tepl_gutenberg_button_enqueue' ] );
		add_action( 'edit_form_after_title', [ $this, 'edit_form_after_title' ] );
		add_filter( 'manage_page_posts_columns', [ $this, 'posts_columns' ] );
		add_filter( 'manage_page_posts_custom_column', [ $this, 'manage_page_posts_custom_column' ], 10, 2 );
		add_filter( 'manage_archive-template_posts_columns', [ $this, 'penci_ctemplates_mobile_col_def' ] );
		add_filter( 'manage_custom-post-template_posts_columns', [ $this, 'penci_ctemplates_mobile_col_def' ] );
		add_action( 'manage_archive-template_posts_custom_column', [ $this, 'penci_ctemplates_mobile_col' ], 10, 2 );
		add_action( 'manage_custom-post-template_posts_custom_column', [
			$this,
			'penci_ctemplates_mobile_col'
		], 10, 2 );
		add_action( 'pre_get_posts', [ $this, 'penci_modify_mobile_page_id' ] );
		add_action( 'admin_init', function () {
			add_action( 'save_post', [ $this, 'save_template_action' ] );
		} );
		add_filter( 'option_page_on_front', function ( $value, $option ) {
			$mobile_object_id = get_post_meta( $value, 'penci_mobile_page_id', true );
			if ( wp_is_mobile() && $mobile_object_id && 'publish' == get_post_status( $mobile_object_id ) ) {
				$value = $mobile_object_id;
			}

			return $value;
		}, 10, 2 );
		add_filter( 'option_page_for_posts', function ( $value, $option ) {
			$mobile_object_id = get_post_meta( $value, 'penci_mobile_page_id', true );
			if ( wp_is_mobile() && $mobile_object_id && 'publish' == get_post_status( $mobile_object_id ) ) {
				$value = $mobile_object_id;
			}

			return $value;
		}, 10, 2 );
	}

	function edit_form_after_title( $post ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;
		if ( in_array( $post_type, [ 'page', 'archive-template', 'custom-post-template' ] ) ) {
			$verified_mobile  = get_post_meta( $post_id, 'penci_mobile_page_id', true );
			$verified_desktop = get_post_meta( $post_id, 'penci_desktop_page_id', true );
			$verified_mobile  = 'publish' == get_post_status( $verified_mobile ) ? $verified_mobile : false;
			$verified_desktop = 'publish' == get_post_status( $verified_desktop ) ? $verified_desktop : false;

			if ( $verified_desktop ) {
				echo '<div class="penci-mobile-templates-btn">';
				echo '<a class="button button-primary button-hero" title="' . __( 'Edit Desktop Page', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_desktop . '&action=edit' ) ) . '">' . __( 'Edit Desktop Page', 'soledad' ) . '</a>';
				echo '</div>';
			} else if ( $verified_mobile ) {
				echo '<div class="penci-mobile-templates-btn">';
				echo '<a class="button button-primary button-hero" title="' . __( 'Edit Mobile Page', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_mobile . '&action=edit' ) ) . '">' . __( 'Edit Mobile Page', 'soledad' ) . '</a>';
				echo '</div>';
			} else {
				echo '<div class="penci-mobile-templates-btn">';
				echo '<a class="button button-primary button-hero" title="' . __( 'Create Mobile Page', 'soledad' ) . '" href="' . esc_url( admin_url( 'post-new.php?post_type=page&desktop_id=' . $post_id ) ) . '">' . __( 'Create Mobile Page', 'soledad' ) . '</a>';
				echo '</div>';
			}
		}
	}

	function manage_page_posts_custom_column( $column_key, $post_id ) {
		if ( $column_key == 'penci_mobile_page_id' ) {

			$verified_mobile  = get_post_meta( $post_id, 'penci_mobile_page_id', true );
			$verified_desktop = get_post_meta( $post_id, 'penci_desktop_page_id', true );
			$verified_mobile  = 'publish' == get_post_status( $verified_mobile ) ? $verified_mobile : false;
			$verified_desktop = 'publish' == get_post_status( $verified_desktop ) ? $verified_desktop : false;

			if ( $verified_desktop ) {
				echo '<span>';
				echo '<a title="' . __( 'Edit Desktop Page', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_desktop . '&action=edit' ) ) . '">' . __( 'Edit Desktop Page', 'soledad' ) . '</a>';
				echo '</span>';
			} else if ( $verified_mobile ) {
				echo '<span>';
				echo '<a title="' . __( 'Edit Mobile Page', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_mobile . '&action=edit' ) ) . '">' . __( 'Edit Mobile Page', 'soledad' ) . '</a>';
				echo '</span>';
			} else {
				echo '<span>';
				echo '<a title="' . __( 'Create Mobile Page', 'soledad' ) . '" href="' . esc_url( admin_url( 'post-new.php?post_type=page&desktop_id=' . $post_id ) ) . '">' . __( 'Create Mobile Page', 'soledad' ) . '</a>';
				echo '</span>';
			}
		}
	}

	function posts_columns( $columns ) {
		return array_merge( $columns, [ 'penci_mobile_page_id' => '<i class="dashicons dashicons-smartphone"></i>' . __( 'Mobile Page', 'soledad' ) ] );
	}

	function penci_tepl_gutenberg_button_enqueue() {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, [ 'page', 'archive-template', 'custom-post-template' ] ) ) {
			return;
		}

		$verified_mobile  = get_post_meta( $post_id, 'penci_mobile_page_id', true );
		$verified_desktop = get_post_meta( $post_id, 'penci_desktop_page_id', true );
		$verified_mobile  = 'publish' == get_post_status( $verified_mobile ) ? $verified_mobile : false;
		$verified_desktop = 'publish' == get_post_status( $verified_desktop ) ? $verified_desktop : false;

		if ( $verified_desktop ) {
			$checking = [
				'type' => 'desktop',
				'btn'  => __( 'Edit Desktop Page', 'soledad' ),
				'url'  => esc_url( admin_url( 'post.php?post=' . $verified_desktop . '&action=edit' ) )
			];
		} else if ( $verified_mobile ) {
			$checking = [
				'type' => 'mobile',
				'btn'  => __( 'Edit Mobile Page', 'soledad' ),
				'url'  => esc_url( admin_url( 'post.php?post=' . $verified_mobile . '&action=edit' ) )
			];
		} else {
			$checking = [
				'type' => 'new',
				'btn'  => __( 'Create Mobile Page', 'soledad' ),
				'url'  => esc_url( admin_url( 'post-new.php?post_type=page&desktop_id=' . $post_id ) )
			];
		}

		wp_enqueue_script(
			'pc-btn-editor',
			plugins_url( 'editor.js', __FILE__ ),
			filemtime( plugin_dir_path( __FILE__ ) . 'editor.js' )
		);
		wp_localize_script( 'pc-btn-editor', 'penci_editor_btn', $checking );

	}

	function penci_modify_mobile_page_id( $query ) {
		// Ensure we're modifying the main query
		if ( ! is_admin() && $query->is_main_query() ) {
			if ( wp_is_mobile() && is_page() ) {
				$object_id = '';
				if ( isset( $query->queried_object ) && is_object( $query->queried_object ) && isset( $query->queried_object->ID ) ) {
					$object_id = $query->queried_object->ID;
				}
				if ( $object_id ) {
					$mobile_object_id = get_post_meta( $object_id, 'penci_mobile_page_id', true );
					if ( $mobile_object_id && 'publish' == get_post_status( $mobile_object_id ) ) {
						$query->set( 'page_id', $mobile_object_id );
					}
				}
			}
		}
	}

	function penci_ctemplates_mobile_col_def( $columns ) {
		return array_merge( $columns, [ 'penci_mobile_page_id' => '<i class="dashicons dashicons-smartphone"></i>' . __( 'Mobile Template', 'soledad' ) ] );
	}

	function penci_ctemplates_mobile_col( $column_key, $post_id ) {
		if ( $column_key == 'penci_mobile_page_id' ) {
			$post_type        = get_post_type( $post_id );
			$verified_mobile  = get_post_meta( $post_id, 'penci_mobile_page_id', true );
			$verified_desktop = get_post_meta( $post_id, 'penci_desktop_page_id', true );


			$verified_mobile  = 'publish' == get_post_status( $verified_mobile ) ? $verified_mobile : false;
			$verified_desktop = 'publish' == get_post_status( $verified_desktop ) ? $verified_desktop : false;


			if ( $verified_mobile ) {
				echo '<span>';
				echo '<a title="' . __( 'Edit Mobile Template', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_mobile . '&action=edit' ) ) . '">' . __( 'Edit Mobile Template', 'soledad' ) . '</a>';
				echo '</span>';
			} else if ( $verified_desktop ) {
				echo '<span>';
				echo '<a title="' . __( 'Edit Mobile Template', 'soledad' ) . '" style="font-weight:bold" href="' . esc_url( admin_url( 'post.php?post=' . $verified_desktop . '&action=edit' ) ) . '">' . __( 'Edit Desktop Template', 'soledad' ) . '</a>';
				echo '</span>';
			} else {
				echo '<span>';
				echo '<a title="' . __( 'Create Mobile Template', 'soledad' ) . '" href="' . esc_url( admin_url( 'post-new.php?post_type=' . $post_type . '&desktop_id=' . $post_id ) ) . '">' . __( 'Create Mobile Template', 'soledad' ) . '</a>';
				echo '</span>';
			}
		}
	}

	function save_template_action( $post_id ) {

		$penci_desktop_page_id = ( isset( $_REQUEST['desktop_id'] ) && $_REQUEST['desktop_id'] ) ? $_REQUEST['desktop_id'] : '';

		if ( $penci_desktop_page_id ) {
			update_post_meta( $post_id, 'penci_desktop_page_id', $penci_desktop_page_id );
			update_post_meta( $penci_desktop_page_id, 'penci_mobile_page_id', $post_id );
		}
	}

}

Penci_Mobile_Templates::getInstance();