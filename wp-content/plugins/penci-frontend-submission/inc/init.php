<?php

namespace PenciFrontendSubmission;

use Exception;

/**
 * Class Soledad Account Page
 */
class AccountPage {
	private static $instance;

	private $endpoint;

	private $post_id;
	private $page_title;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {

		add_action( 'penci_account_main_content', array( $this, 'user_get_right_content' ) );
		add_action( 'init', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'penci_after_account_nav', array( $this, 'after_account_nav' ) );
		add_action( 'wp_loaded', array( $this, 'submit_handler' ), 20 );
		add_action( 'elementor/widgets/register', [ $this, 'elementor_widget' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_style' ] );
		add_action( 'template_include', array( $this, 'add_page_template' ) );
		add_filter( 'penci_account_page_endpoint', [ $this, 'setup_endpoint' ], 10, 1 );
		add_filter( 'penci_logged_in_items', [ $this, 'add_loggedin_items' ], 10, 1 );
		add_action( 'admin_notices', [ $this, 'paywall_notice' ] );
		add_filter( 'penci_maxsize_upload_featured_image', function ( $size ) {
			if ( get_theme_mod( 'penci_frontend_submit_maxupload' ) ) {
				$size = get_theme_mod( 'penci_frontend_submit_maxupload' ) . 'mb';
			}

			return $size;
		} );
	}

	public function paywall_notice() {
		if ( get_theme_mod( 'penci_frontend_submit_enable_woocommerce', false ) && ! class_exists( 'WooCommerce' ) && ! function_exists( 'getpaid' ) ):
			?>
            <div class="notice notice-error">
                <p><?php _e( '<strong>Penci Frontend Submission</strong> required <strong>GetPaid</strong> or <strong>WooCommerce</strong> plugin to add the payment gateway. Please install these plugins <a href="' . esc_url( admin_url( 'themes.php?page=tgmpa-install-plugins#recommended-plugins' ) ) . '">here</a>.', 'penci-paywall' ); ?></p>
            </div>
		<?php
		endif;
	}

	public function register_style() {
		wp_enqueue_style( 'penci-frontend-package', PENCI_FRONTEND_SUBMISSION_URL . 'assets/package.css', '', PENCI_FRONTEND_SUBMISSION );
	}

	public function elementor_widget( $widgets_manager ) {
		require_once( plugin_dir_path( __DIR__ ) . 'builder/elementor.php' );
		$widgets_manager->register( new \PenciPostPackage() );
	}

	public function load_script() {
		wp_enqueue_media();
	}

	protected function is_account_page( $wp ) {
		if ( is_user_logged_in() && ! is_admin() ) {
			if ( isset( $wp->query_vars[ $this->endpoint['account']['slug'] ] ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );

				return true;
			}
		}

		return false;
	}

	public function setup_endpoint( $endpoint ) {

		$custom_enp['editor']  = array(
			'title' => penci_ftsub_get_text( 'create_new_post' ),
			'label' => 'create_new_post',
			'icon'  => 'fa fa-plus',
			'slug'  => get_theme_mod( 'penci_frontend_submit_editor_slug', 'editor' ),
		);
		$custom_enp['my_post'] = array(
			'title' => penci_ftsub_get_text( 'my_post' ),
			'label' => 'my_post',
			'icon'  => 'fa fa-file-text',
			'slug'  => get_theme_mod( 'penci_frontend_submit_my_post_slug', 'my-post' ),
		);

		$this->endpoint = $custom_enp;

		return array_merge( $endpoint, $custom_enp );
	}

	public function add_loggedin_items( $items ) {
		$custom_items = $this->endpoint;

		$post_slug = get_theme_mod( 'penci_frontend_submit_account_slug', 'account' );

		foreach ( $custom_items as $item ) {
			$slug = $post_slug . '/';
			$link = esc_attr( home_url( '/' ) . $slug . $item['slug'] );

			if ( $item['label'] == 'create_new_post' ) {
				$spage_id = get_theme_mod( 'penci_frontend_subpage' );
				$page_url = $spage_id && is_page( $spage_id ) ? get_page_link( $spage_id ) : '';

				if ( $this->is_user_allow_access() ) {
					$link = esc_attr( home_url( '/' ) . $item['slug'] );
				} elseif ( $page_url ) {
					$link          = $page_url;
					$item['title'] = penci_ftsub_get_text( 'bauthor' );
				}
			}

			$items[ $item['label'] ] = array(
				'icon' => $item['icon'],
				'link' => $link,
				'text' => $item['title'],
			);

		}

		return $items;
	}

	protected function is_user_allow_access( $user_id = null ) {
		$value = true;

		if ( get_theme_mod( 'penci_frontend_submit_enable_woocommerce', false ) ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			$post_limit = get_user_meta( $user_id, 'listing_left', true );

			if ( (int) $post_limit <= 0 ) {
				$value = false;
			}
		}

		return apply_filters( 'penci_frontend_submit_user_subscription', $value );
	}

	public function get_editor_slug() {
		return penci_home_url_multilang( '/' . $this->endpoint['editor']['slug'] );
	}

	public function after_account_nav() {

		$page_url = get_theme_mod( 'penci_frontend_subpage' ) ? get_page_link( get_theme_mod( 'penci_frontend_subpage' ) ) : '';

		if ( $this->is_user_allow_access() ) {
			echo
				'<div class="frontend-submit-button">
                    <a class="button" href="' . $this->get_editor_slug() . '"><i class="fa fa-file-text-o"></i> ' . penci_ftsub_get_text( 'spost' ) . '</a>
                </div>';
		} elseif ( $page_url ) {
			echo
				'<div class="frontend-submit-button">
                    <a class="button" href="' . esc_url( $page_url ) . '"><i class="fa fa-file-text-o"></i> ' . penci_ftsub_get_text( 'bauthor' ) . '</a>
                </div>';
		}
	}

	public function add_rewrite_rule() {
		if ( isset( $this->endpoint['editor']['slug'] ) && ! empty( $this->endpoint['editor']['slug'] ) ) {
			add_rewrite_endpoint( $this->endpoint['editor']['slug'], EP_ROOT | EP_PAGES );
			add_rewrite_rule( '^' . $this->endpoint['editor']['slug'] . '/page/?([0-9]{1,})/?$', 'index.php?&paged=$matches[1]&' . $this->endpoint['editor']['slug'], 'top' );
		}
	}

	public function flush_rewrite_rules() {
		$this->add_rewrite_rule();

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	public function add_page_template( $template ) {
		global $wp;

		if ( is_user_logged_in() ) {
			$editor = isset( $this->endpoint['editor']['slug'] ) ? $this->endpoint['editor']['slug'] : '';

			if ( isset( $wp->query_vars[ $editor ] ) ) {
				add_action( 'wp_print_styles', array( $this, 'load_assets' ) );
				add_filter( 'document_title_parts', array( $this, 'account_title' ) );


				if ( current_user_can( 'upload_files' ) ) {
					add_action(
						'wp_enqueue_scripts',
						function () {
							wp_enqueue_media();
						}
					);
				}

				if ( ! empty( $wp->query_vars['editor'] ) ) {
					if ( $this->is_user_can_edit_post( $wp->query_vars['editor'] ) ) {
						$file = plugin_dir_path( __DIR__ ) . 'templates/edit_post.php';
					}
					$this->page_title = penci_ftsub_get_text( 'epost' );
				} else {
					if ( $this->is_user_allow_access() ) {
						$file             = plugin_dir_path( __DIR__ ) . 'templates/create_post.php';
						$this->page_title = penci_ftsub_get_text( 'cpost' );
					}
				}

				if ( ! empty( $file ) && file_exists( $file ) ) {
					$template = $file;
				}
			}
		}

		return $template;
	}

	protected function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	public function get_post_id() {
		return $this->post_id;
	}

	public function load_assets() {
		wp_enqueue_style( 'penci-frontend-submission', PENCI_FRONTEND_SUBMISSION_URL . 'assets/style.css', '', PENCI_FRONTEND_SUBMISSION );
		wp_enqueue_style( 'selectize', PENCI_FRONTEND_SUBMISSION_URL . 'assets/frontend.css', null );
		wp_enqueue_script( 'selectize', PENCI_FRONTEND_SUBMISSION_URL . 'assets/selectize.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'penci-frontend-submission', PENCI_FRONTEND_SUBMISSION_URL . 'assets/frontend.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable'
		), PENCI_FRONTEND_SUBMISSION, true );
		$locale_settings = [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'pencifts_deleted_post' ),
			'confirm' => penci_ftsub_get_text( 'cfconfirm' ),
		];
		wp_localize_script( 'penci-frontend-submission', 'pencifts', $locale_settings );

		if ( get_theme_mod( 'penci_frontend_submit_acf_groups' ) && function_exists( 'acf_get_fields' ) ) {
			wp_enqueue_style( 'acf-global' );
			wp_enqueue_style( 'acf-field-group' );
			wp_enqueue_script( 'acf' );
			wp_enqueue_script( 'acf-field-group' );
		}
	}

	public function account_title( $title ) {
		global $wp;
		$split      = $title;
		$additional = '';

		if ( isset( $this->page_title ) ) {
			$additional = $this->page_title;
		}

		$additional = apply_filters( 'penci_account_title', $additional, $wp, $this->endpoint );

		global $wp_query;
		$split['title'] = isset( $wp_query->queried_object->post_title );

		if ( ! empty( $additional ) ) {
			$title['title'] = $additional . ' ' . $split['title'];
		}

		return $title;
	}

	public function is_user_can_edit_post( $post_id = null ) {
		$value   = false;
		$user_id = get_current_user_id();

		if ( ! empty( $post_id ) ) {
			$this->set_post_id( $post_id );

			$author_id = get_post_field( 'post_author', $post_id );

			if ( $author_id == $user_id ) {
				$value = true;
			}
		}

		return apply_filters( 'penci_frontend_user_can_edit_post', $value );
	}

	public function get_category() {
		$result = array();
		$terms  = get_categories( array( 'hide_empty' => 0 ) );
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}

		return $result;
	}

	public function get_terms( $term ) {
		$result = array();
		$terms  = get_terms( $term, array( 'hide_empty' => 0 ) );
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}

		return $result;
	}

	public function get_tag() {
		$result = array();
		$terms  = get_tags( array( 'hide_empty' => 0 ) );
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}

		return $result;
	}

	public function submit_handler() {
		if ( defined( 'PENCI_SANDBOX_URL' ) ) {
			return false;
		}

		if ( isset( $_REQUEST['penci-action'] ) && ! empty( $_REQUEST['penci-editor-nonce'] ) && wp_verify_nonce( $_REQUEST['penci-editor-nonce'], 'penci-editor' ) ) {
			$action = $_REQUEST['penci-action'];

			switch ( $action ) {
				case 'create-post':
					$this->create_post_handler();
					break;

				case 'edit-post':
					$this->edit_post_handler();
					break;
			}
		}
	}

	protected function create_post_handler() {
		$user_id   = get_current_user_id();
		$post_type = get_theme_mod( 'penci_frontend_submit_enabled_post_types', 'post' );
		$post_type = isset( $_POST['penci-post-type'] ) && $_POST['penci-post-type'] ? $_POST['penci-post-type'] : $post_type;

		try {

			if ( empty( $_POST['title'] ) ) {
				throw new Exception( penci_ftsub_get_text( 'ptitle_n' ) );
			}

			if ( get_theme_mod( 'penci_frontend_submit_enable_woocommerce' ) ) {

				$post_limit = get_user_meta( $user_id, 'listing_left', true );

				if ( $post_limit <= 0 ) {
					throw new Exception( penci_ftsub_get_text( 'not_allow' ) );
				} else {
					update_user_meta( $user_id, 'listing_left', $post_limit - 1 );
				}
			}

			$title   = sanitize_text_field( $_POST['title'] );
			$content = $_POST['content'];

			$args = array(
				'post_title'   => $title,
				'post_type'    => $post_type,
				'post_status'  => get_theme_mod( 'penci_frontend_submit_status', 'pending' ),
				'post_author'  => $user_id,
				'post_content' => $content,
			);
			$args = apply_filters( 'penci_frontend_submit_create_post', $args );

			$post_id = wp_insert_post( $args );

			if ( is_wp_error( $post_id ) ) {
				throw new Exception( $post_id->get_error_message() );
			} else {

				if ( isset( $_POST['subtitle'] ) ) {
					update_post_meta( $post_id, 'penci_post_sub_title', sanitize_text_field( $_POST['subtitle'] ) );
				}

				do_action( 'penci_frontend_submit_save_post_handler' );

				if ( isset( $_POST['category'] ) ) {
					wp_set_post_terms( $post_id, $_POST['category'], 'category' );
				}

				if ( isset( $_POST['tag'] ) ) {
					wp_set_post_tags( $post_id, array_map( 'intval', explode( ',', $_POST['tag'] ) ) );
				}

				if ( isset( $_POST['format'] ) ) {
					if ( $_POST['format'] == 'gallery' ) {
						set_post_format( $post_id, 'gallery' );
						update_post_meta( $post_id, '_format_gallery_images', isset( $_POST['gallery'] ) ? array_unique( $_POST['gallery'] ) : '' );
						update_post_meta( $post_id, '_thumbnail_id', isset( $_POST['gallery_featured_image'][0] ) ? (int) sanitize_text_field( $_POST['gallery_featured_image'][0] ) : '' );
					} elseif ( $_POST['format'] == 'video' ) {
						set_post_format( $post_id, 'video' );

						if ( isset( $_POST['video'] ) ) {
							update_post_meta( $post_id, '_format_video_embed', sanitize_textarea_field( $_POST['video'] ) );
						}
					} elseif ( $_POST['format'] == 'audio' ) {
						set_post_format( $post_id, 'audio' );

						if ( isset( $_POST['audio'] ) ) {
							update_post_meta( $post_id, '_format_audio_embed', sanitize_textarea_field( $_POST['audio'] ) );
						}
					} else {
						set_post_format( $post_id, false );
						update_post_meta( $post_id, '_thumbnail_id', isset( $_POST['image'][0] ) ? (int) sanitize_text_field( $_POST['image'][0] ) : '' );
					}
				}

				if ( isset( $_POST['acf'] ) && function_exists( 'update_field' ) ) {
					$acf_fields = $_POST['acf'];
					foreach( $acf_fields as $acf_field => $val ) {
						update_field( $acf_field, $val, $post_id );
					}
				}

				update_post_meta( $post_id, 'penci_frontend_submit_post_flag', true );

				penci_flash_message( 'message', penci_ftsub_get_text( 'pcreated_n' ), 'alert-success' );
				wp_redirect( penci_home_url_multilang( $this->endpoint['editor']['slug'] . '/' . $post_id ) );
				exit;
			}
		} catch ( Exception $e ) {
			penci_flash_message( 'message', $e->getMessage(), 'alert-danger' );
		}
	}

	public function user_get_right_content() {
		global $wp;

		$all_points = \PenciUserProfile::getInstance();
		$endpoints  = $all_points->get_endpoint();

		if ( is_user_logged_in() ) {
			$account_slug = $endpoints['account']['slug'];
			$posts_slug   = $endpoints['my_post']['slug'];

			if ( isset( $wp->query_vars[ $account_slug ] ) && ! empty( $wp->query_vars[ $account_slug ] ) ) {
				$query_vars = explode( '/', $wp->query_vars[ $account_slug ] );

				$this->load_assets();

				if ( $query_vars[0] == $posts_slug ) {
					$paged = 1;

					if ( isset( $query_vars[2] ) ) {
						$paged = (int) $query_vars[2];
					}

					$template = plugin_dir_path( __DIR__ ) . 'templates/list_post.php';

					if ( file_exists( $template ) ) {
						include $template;
					}
				}
			}
		}
	}

	protected function edit_post_handler() {
		$post_id = (int) sanitize_text_field( $_POST['post_id'] );

		try {

			if ( empty( $_POST['title'] ) ) {
				throw new Exception( penci_ftsub_get_text( 'ptitle_n' ) );
			}
			$title   = sanitize_text_field( $_POST['title'] );
			$content = $_POST['content'];

			$args = array(
				'ID'           => $post_id,
				'post_title'   => $title,
				'post_content' => $content,
			);
			$args = apply_filters( 'penci_frontend_submit_edit_post', $args );

			wp_update_post( $args );

			if ( isset( $_POST['subtitle'] ) ) {
				update_post_meta( $post_id, 'penci_post_sub_title', sanitize_text_field( $_POST['subtitle'] ) );
			}

			/* save enable donation handler (penci-pay-writer)  */
			do_action( 'penci_frontend_submit_save_post_handler' );

			if ( isset( $_POST['category'] ) ) {
				wp_set_post_terms( $post_id, $_POST['category'], 'category' );
			}

			if ( isset( $_POST['tag'] ) ) {
				wp_set_post_tags( $post_id, array_map( 'intval', explode( ',', $_POST['tag'] ) ) );
			}

			if ( isset( $_POST['format'] ) ) {
				if ( $_POST['format'] == 'gallery' ) {
					set_post_format( $post_id, 'gallery' );
					update_post_meta( $post_id, '_format_gallery_images', isset( $_POST['gallery'] ) ? array_unique( $_POST['gallery'] ) : '' );
					update_post_meta( $post_id, '_thumbnail_id', isset( $_POST['gallery_featured_image'][0] ) ? (int) sanitize_text_field( $_POST['gallery_featured_image'][0] ) : '' );

				} elseif ( $_POST['format'] == 'video' ) {
					set_post_format( $post_id, 'video' );

					if ( isset( $_POST['video'] ) ) {
						update_post_meta( $post_id, '_format_video_embed', sanitize_textarea_field( $_POST['video'] ) );
					}
				} elseif ( $_POST['format'] == 'audio' ) {
					set_post_format( $post_id, 'audio' );

					if ( isset( $_POST['audio'] ) ) {
						update_post_meta( $post_id, '_format_audio_embed', sanitize_textarea_field( $_POST['audio'] ) );
					}
				} else {
					set_post_format( $post_id, false );
					update_post_meta( $post_id, '_thumbnail_id', isset( $_POST['image'][0] ) ? (int) sanitize_text_field( $_POST['image'][0] ) : '' );
				}
			}

			if ( isset( $_POST['acf'] ) && function_exists( 'update_field' ) ) {
				$acf_fields = $_POST['acf'];
				foreach( $acf_fields as $acf_field => $val ) {
					update_field( $acf_field, $val, $post_id );
				}
			}

			penci_flash_message( 'message', penci_ftsub_get_text( 'pupdate_n' ), 'alert-success' );

			wp_redirect( penci_home_url_multilang( $this->endpoint['editor']['slug'] . '/' . $post_id ) );
			exit;

		} catch ( Exception $e ) {
			penci_flash_message( 'message', $e->getMessage(), 'alert-danger' );
		}
	}
}
