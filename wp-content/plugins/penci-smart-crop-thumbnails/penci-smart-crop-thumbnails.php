<?php
/*
Plugin Name: Penci Smart Crop Thumbnail
Plugin URI: https://pencidesign.net/
Description: A plugin that allows you to select an Interest Point in your images during cropping. This feature helps you manage how your images will be cropped, rather than using the default crop from WordPress.
Version: 1.2
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciSmartThumbnailCustomizer::getInstance();
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_smartthumbnails_general_section'] = array(
			'priority'                              => 30,
			'path'                                  => plugin_dir_path( __FILE__ ) . '/customizer/',
			'penci_smartthumbnails_general_section' => array(
				'title' => esc_html__( 'Penci Smart Thumbnail', 'soledad' ),
				'icon'  => 'fas fa-image',
			),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-smart-crop-thumbnails', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

class Penci_Smart_Crop_Thumbnails {

	/**
	 * @var array
	 */
	protected $focus_point = array();

	/**
	 * Current plugin version number
	 *
	 * @var string
	 */
	public static $version = '1.1';


	/**
	 * Inner array of instances
	 *
	 * @var array
	 */
	protected static $instances = array();


	/**
	 * Initialize!
	 */
	function __construct() {

		// make sure following code only one time run
		static $initialized;

		if ( $initialized ) {
			return;
		} else {
			$initialized = true;
		}

		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Used for accessing plugin directory URL
	 *
	 * @param string $address
	 *
	 * @return string
	 */
	public static function dir_url( $address = '' ) {

		static $url;

		if ( is_null( $url ) ) {
			$url = trailingslashit( plugin_dir_url( __FILE__ ) );
		}

		return $url . ltrim( $address, '/' );
	}


	/**
	 * Used for accessing plugin directory path
	 *
	 * @param string $address
	 *
	 * @return string
	 */
	public static function dir_path( $address = '' ) {

		static $path;

		if ( is_null( $path ) ) {
			$path = trailingslashit( plugin_dir_path( __FILE__ ) );
		}

		return $path . ltrim( $address, '/' );
	}


	/**
	 * Returns current version
	 *
	 * @return string
	 */
	public static function get_version() {

		return self::$version;
	}


	/**
	 * Build the required object instance
	 *
	 * @param string $object
	 * @param bool   $fresh
	 * @param bool   $just_include
	 *
	 * @return self|null
	 */
	public static function factory( $object = 'self', $fresh = false, $just_include = false ) {

		if ( isset( self::$instances[ $object ] ) && ! $fresh ) {
			return self::$instances[ $object ];
		}

		switch ( $object ) {

			/**
			 * Main Penci_Smart_Crop_Thumbnails Class
			 */
			case 'self':
				$class = 'Penci_Smart_Crop_Thumbnails';
				break;
		}

		// Just prepare/includes files
		if ( $just_include ) {
			return null;
		}

		// don't cache fresh objects
		if ( $fresh ) {
			return new $class();
		}

		self::$instances[ $object ] = new $class();

		return self::$instances[ $object ];
	}


	/**
	 * Used for accessing alive instance class
	 *
	 * @return Penci_Smart_Crop_Thumbnails
	 * @since 1.0
	 */
	public static function self() {

		return self::factory();
	}


	/**
	 * @hooked init
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Enqueue assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ) );

		add_filter( 'image_resize_dimensions', array( $this, 'resize_dimensions' ), 100, 6 );

		// Do not crop medium image size because it will show for preview image on `Featured Image` modal
		add_filter( 'pre_option_medium_crop', '__return_false', 99 );

		add_filter( 'attachment_fields_to_edit', array( $this, 'pass_data2_media_modal' ), 30, 2 );

		add_action( 'wp_ajax_penci-st-regenerate-thumbnails', array( $this, 'ajax_get_crop_image_data' ) );
		add_action( 'wp_ajax_penci-st-preview-thumbnails', array( $this, 'ajax_get_preview_image_data' ) );

		if ( get_theme_mod( 'penci_smart_thumbnail_force_clear_cache' ) ) {

			add_filter( 'wp_get_attachment_metadata', array( $this, 'append_time2images' ) );

		}
	}


	/**
	 * @param int $attachment_id
	 *
	 * @return array
	 * @since    1.0.0
	 */
	protected function delete_useless_thumbnails( $attachment_id ) {

		if ( ! class_exists( 'Penci_ST_CleanUp_Thumbnail' ) ) {

			require self::dir_path( 'includes/class-penci-st-cleanup-thumbnails.php' );
		}

		$cleanup = new Penci_ST_CleanUp_Thumbnail( $attachment_id );

		$cleanup->cleanup();
	}


	/**
	 * @hooked wp_ajax_penci-st-regenerate-thumbnails
	 *
	 * @since  1.0.0
	 */
	public function ajax_get_crop_image_data() {

		if ( empty( $_REQUEST['thumbnail_id'] ) || ! isset( $_REQUEST['focus_x'] ) || ! isset( $_REQUEST['focus_y'] ) ) {
			return;
		}

		$attachment_id = intval( $_REQUEST['thumbnail_id'] );

		check_ajax_referer( $this->get_nonce_key( $attachment_id ), 'nonce' );

		$focus_x = intval( $_REQUEST['focus_x'] ) / 100;
		$focus_y = intval( $_REQUEST['focus_y'] ) / 100;

		// Validate x, y values

		if ( ! ( $focus_x >= 0 && $focus_x <= 1 ) ) {
			return;
		}

		if ( ! ( $focus_y >= 0 && $focus_y <= 1 ) ) {
			return;
		}

		$this->focus_point = array( $focus_x, $focus_y );

		$this->set_focus_point( $attachment_id, $focus_x, $focus_y );

		$metadata                  = wp_generate_attachment_metadata( $attachment_id, get_attached_file( $attachment_id ) );
		$metadata['penci-st-time'] = time();

		wp_update_attachment_metadata( $attachment_id, $metadata );

		if ( get_theme_mod( 'penci_smart_thumbnail_delete_unused_thumbnail' ) ) {
			$this->delete_useless_thumbnails( $attachment_id );
		}

		wp_send_json_success( array( 'message' => __( 'All thumbnail sizes have been successfully generated.', 'penci-smart-crop-thumbnails' ) ) );
	}


	/**
	 * @hooked wp_ajax_penci-st-preview-thumbnails
	 *
	 * @since  1.0.0
	 */
	public function ajax_get_preview_image_data() {

		if ( empty( $_REQUEST['thumbnail_id'] ) ) {
			return;
		}

		$attachment_id = intval( $_REQUEST['thumbnail_id'] );

		check_ajax_referer( $this->get_nonce_key( $attachment_id ), 'nonce' );

		$data = array();

		$data['l10n'] = array(
			'all_l10n' => __( 'All', 'penci-smart-crop-thumbnails' ),
			'header'   => __( 'Preview Cropped Images', 'penci-smart-crop-thumbnails' ),
		);

		$data['images'] = array();

		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( ! empty( $metadata['sizes'] ) ) {

			$base_url = wp_upload_dir();
			$base_url = $base_url['baseurl'];
			$sub_dir  = dirname( $metadata['file'] );

			foreach ( $metadata['sizes'] as $id => $size ) {

				$file = $size['file'];

				array_push(
					$data['images'],
					array(
						'id'    => $id,
						'img'   => "$base_url/$sub_dir/$file",
						'label' => ucwords( str_replace( array( '-', '_' ), ' ', $id ) ),
					)
				);
			}
		}

		wp_send_json_success( compact( 'data' ) );
	}


	/**
	 *
	 * @param array   $form_fields An array of attachment form fields.
	 * @param WP_Post $post The WP_Post attachment object.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function pass_data2_media_modal( $form_fields, $post ) {

		$nonce             = sprintf( '<input value="%s" class="penci-st-regenerate-nonce" type="hidden">', esc_attr( $this->get_nonce( $post->ID ) ) );
		$saved_focus_point = sprintf( '<input value="%s" class="penci-st-focus-point-xy" type="hidden">', esc_attr( $this->get_focus_point( $post->ID ) ) );

		$form_fields['penci-st-regenerate-nonce'] = array(
			'show_in_edit' => true,
			'tr'           => $nonce . $saved_focus_point,
		);

		return $form_fields;
	}


	/**
	 * @param null|mixed $null Whether to preempt output of the resize dimensions.
	 * @param int        $orig_w Original width in pixels.
	 * @param int        $orig_h Original height in pixels.
	 * @param int        $dest_w New width in pixels.
	 * @param int        $dest_h New height in pixels.
	 * @param bool|array $crop Whether to crop image to specified width and height or resize.
	 *
	 * @return array
	 */
	function resize_dimensions( $null, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {

		if ( ! $crop ) {
			return $null;
		}

		$is_portrait = ( $orig_w + ( $orig_h * 0.14 ) ) < $orig_h;

		if ( $is_portrait && empty( $this->focus_point ) ) {

			if ( get_theme_mod( 'penci_smart_thumbnail_portrait_default_top' ) ) {
				$this->focus_point = array( 0.5, 0 );
			}
		}

		if ( empty( $this->focus_point ) ) {
			return $null;
		}

		if ( ! get_theme_mod( 'penci_smart_thumbnail_enlarge_smaller' ) ) {

			$dest_w = min( $dest_w, $orig_w );
			$dest_h = min( $dest_h, $orig_h );
		}

		$dest_x     = $dest_y = 0;
		$size_ratio = max( $dest_w / $orig_w, $dest_h / $orig_h );

		$crop_w = round( $dest_w / $size_ratio );
		$crop_h = round( $dest_h / $size_ratio );

		$s_x = floor( ( $orig_w - $crop_w ) * $this->focus_point[0] );
		$s_y = floor( ( $orig_h - $crop_h ) * $this->focus_point[1] );

		// The canvas is the same as the resulting image.
		$dst_canvas_w = $dest_w;
		$dst_canvas_h = $dest_h;

		if (
			$dest_w >= $orig_w &&
			$dest_h >= $orig_h
		) {
			return $null;
		}

		return array(
			(int) $dest_x,
			(int) $dest_y,
			(int) $s_x,
			(int) $s_y,
			(int) $dest_w,
			(int) $dest_h,
			(int) $crop_w,
			(int) $crop_h,
			(int) $dst_canvas_w,
			(int) $dst_canvas_h,
		);
	}


	/**
	 * @param int $thumbnail_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_nonce( $thumbnail_id ) {

		return wp_create_nonce( $this->get_nonce_key( $thumbnail_id ) );
	}


	/**
	 * @param int $thumbnail_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_nonce_key( $thumbnail_id ) {

		return sprintf( 'ajax-protect-%d', $thumbnail_id );
	}


	/**
	 * Used for registering scripts in admin area
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts_admin() {

		wp_enqueue_script(
			'penci-smart-crop-thumbnails',
			plugin_dir_url( __FILE__ ) . 'assets/js/penci-smart-thumbnails.js',
			array( 'jquery' ),
			self::$version,
			true
		);

		wp_enqueue_style(
			'penci-smart-crop-thumbnails',
			plugin_dir_url( __FILE__ ) . 'assets/css/penci-smart-thumbnails.css',
			array(),
			self::$version
		);

		$this->localize_script();
	}


	/**
	 * Print localization vars.
	 *
	 * @hooked wp_enqueue_scripts
	 *
	 * @since  1.0.0
	 */
	public function localize_script() {

		wp_localize_script(
			'penci-smart-crop-thumbnails',
			'st_loc',
			array(

				'translate'            => array(
					'preview' => __( 'Preview Images', 'penci-smart-crop-thumbnails' ),
				),

				'grid'                 => (bool) get_theme_mod( 'penci_smart_thumbnail_grid' ),
				'default_fp'           => $this->default_focus_point(),
				'portrait_default_top' => (bool) get_theme_mod( 'penci_smart_thumbnail_portrait_default_top' ),
			)
		);
	}

	/**
	 * @hooked wp_get_attachment_metadata
	 *
	 * @param array|bool $data Array of meta data for the given attachment, or false
	 *                            if the object does not exist.
	 *
	 * @return array|bool
	 * @since  1.0.0
	 */
	public function append_time2images( $data ) {

		if ( ! $data || empty( $data['penci-st-time'] ) ) {

			return $data;
		}

		$time          = $data['penci-st-time'];
		$data['file'] .= '?' . $time;

		if ( ! empty( $data['sizes'] ) ) {

			$sizes = array();

			foreach ( $data['sizes'] as $size => $info ) {

				$info['file'] .= '?' . $time;

				$sizes[ $size ] = $info;
			}

			$data['sizes'] = $sizes;
		}

		return $data;
	}


	/**
	 * Save attachment image focus point
	 *
	 * @param int $attachment_id
	 * @param int $focus_x
	 * @param int $focus_y
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function set_focus_point( $attachment_id, $focus_x, $focus_y ) {

		setcookie( 'pcsct-' . $attachment_id, "$focus_x-$focus_y" );
		return (bool) update_post_meta( $attachment_id, 'penci-st-focus-point', "$focus_x-$focus_y" );
	}


	/**
	 * Save attachment image focus point
	 *
	 * @param int $attachment_id
	 *
	 * @return string "x-y" on success
	 * @since 1.0.0
	 */
	public function get_focus_point( $attachment_id ) {

		return get_post_meta( $attachment_id, 'penci-st-focus-point', true );
	}


	/**
	 * Get default image focus point.
	 *
	 * @return array x,y in position values in 0-1 range
	 * @since 1.0.0
	 */
	public function default_focus_point() {

		$position_default = get_theme_mod( 'penci_smart_thumbnail_default_focus_point' ) ? get_theme_mod( 'penci_smart_thumbnail_default_focus_point' ) : 'center-center';

		list( $y_position_name, $x_position_name ) = explode( '-', $position_default, 2 );

		$location_percentage = array(

			'x' => array(
				'left'   => 0.165,
				'center' => 0.5,
				'right'  => 0.835,
			),

			'y' => array(
				'top'    => 0.165,
				'center' => 0.5,
				'bottom' => 0.835,
			),
		);

		if ( isset( $location_percentage['y'][ $y_position_name ] ) && isset( $location_percentage['x'][ $x_position_name ] ) ) {

			return array(
				$location_percentage['x'][ $x_position_name ],
				$location_percentage['y'][ $y_position_name ],
			);
		}

		return array( 50, 50 );
	}
}

Penci_Smart_Crop_Thumbnails::self();
