<?php
/*
Plugin Name: Penci Review
Plugin URI: http://pencidesign.com/
Description: Review Shortcode Plugin for Soledad theme.
Version: 3.4
Author: PenciDesign
Author URI: http://themeforest.net/user/pencidesign?ref=pencidesign
*/

use SoledadFW\ReviewCustomizer;

define( 'PENCI_REVIEW_VER', '3.3' );
/**
 * Include files
 */
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/schema-markup.php';
require_once __DIR__ . '/inc/default-options.php';
require_once __DIR__ . '/inc/shortcodes.php';

add_action( 'init', function () {
	if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
		require_once __DIR__ . '/inc/options/section.php';
		require_once __DIR__ . '/inc/options/settings.php';
		ReviewCustomizer::getInstance();
	} else {
		require_once __DIR__ . '/inc/customize.php';
	}
} );

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'penci-review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

add_action( 'penci_get_options_data', function ( $options ) {

	$options['penci_new_section_review_section'] = array(
		'priority'                         => 30,
		'path'                             => plugin_dir_path( __FILE__ ) . '/inc/options/',
		'penci_new_section_review_section' => array(
			'title' => esc_html__( 'Posts Review Options', 'soledad' ),
			'icon'  => 'fas fa-star-half-alt',
		),
	);

	return $options;
} );

require_once __DIR__ . '/inc/helper.php';
/**
 * Add admin meta box style
 */
function penci_load_admin_metabox_review_style() {
	$screen = get_current_screen();
	if ( $screen->id == 'post' ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_enqueue_style( 'penci_meta_box_review_styles', plugin_dir_url( __FILE__ ) . 'css/admin-css.css', '', PENCI_REVIEW_VER );
		wp_enqueue_script( 'penci_meta_box_review', plugin_dir_url( __FILE__ ) . 'js/admin-review.js', array(
			'jquery',
			'jquery-ui-datepicker',
		), PENCI_REVIEW_VER, true );

		wp_localize_script( 'penci_meta_box_review', 'PenciReview', array(
			'WidgetImageTitle'  => esc_html__( 'Select an image', 'penci' ),
			'WidgetImageButton' => esc_html__( 'Insert into widget', 'penci' ),
			'review_title'      => esc_html__( 'Review Title for Point', 'penci' ),
			'review_number'     => esc_html__( 'Review Number for Point', 'penci' ),
			'review_desc'       => esc_html__( 'Minimum is 1, Maximum is 10. Example: 8', 'penci' ),
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'ajax-nonce' ),
		) );
	}
}

add_action( 'admin_enqueue_scripts', 'penci_load_admin_metabox_review_style' );

/* Load Oswald font from selfhosting */
function penci_load_oswald_for_review() {
	if ( get_theme_mod( 'penci_disable_default_fonts' ) && ! get_theme_mod( 'penci_disable_all_fonts' ) ) {
		?>
        <style type="text/css">@font-face {
                font-family: 'Oswald';
                font-style: font-display: swap;
                normal;
                font-weight: 400;
                src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752HT8Ghe4.woff2) format('woff2');
                unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            }

            @font-face {
                font-family: 'Oswald';
                font-style: normal;
                font-weight: 400;
                src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752Fj8Ghe4.woff2) format('woff2');
                unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
            }

            @font-face {
                font-family: 'Oswald';
                font-style: normal;
                font-weight: 400;
                src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752Fz8Ghe4.woff2) format('woff2');
                unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            }

            @font-face {
                font-family: 'Oswald';
                font-style: normal;
                font-weight: 400;
                src: local('Oswald Regular'), local('Oswald-Regular'), url(<?php echo plugin_dir_url( __FILE__ ); ?>fonts/TK3iWkUHHAIjg752GT8G.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }</style>
		<?php
	}
}

add_action( 'wp_head', 'penci_load_oswald_for_review' );

/**
 * Add javascript for review plugin
 */
add_action( 'wp_enqueue_scripts', 'penci_register_review_scripts' );

function penci_register_review_scripts() {
	wp_enqueue_style( 'penci-review', plugin_dir_url( __FILE__ ) . 'css/style.css', '', PENCI_REVIEW_VER );
	wp_enqueue_script( 'jquery-penci-piechart', plugin_dir_url( __FILE__ ) . 'js/jquery.easypiechart.min.js', array( 'jquery' ), PENCI_REVIEW_VER, true );
	wp_enqueue_script( 'jquery-penci-review', plugin_dir_url( __FILE__ ) . 'js/review.js', array( 'jquery' ), PENCI_REVIEW_VER, true );
	if ( ! get_theme_mod( 'penci_disable_default_fonts' ) ) {
		wp_enqueue_style( 'penci-oswald', '//fonts.googleapis.com/css?family=Oswald:400&display=swap', array(), false, 'all' );
	}
}

/**
 * Adds Penci review meta box to the post editing screen
 */
function Penci_Review_Add_Custom_Metabox() {
	new Penci_Review_Add_Custom_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_Review_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_Review_Add_Custom_Metabox' );
}

/**
 * The Class.
 */
class Penci_Review_Add_Custom_Metabox_Class {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array( 'post' );     // limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'penci_review_meta', esc_html__( 'Add A Review For This Posts', 'soledad' ), array(
				$this,
				'render_meta_box_content'
			), $post_type, 'advanced', 'default' );
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['penci_review_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['penci_review_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'penci_review_custom_box' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Update meta fields

		$group_items = array(
			'penci_review_ct_image',
			'penci_review_address',
			'penci_review_phone',
			'penci_review_website',
			'penci_review_price',
			'penci_review_linkbuy',
			'penci_review_textbuy',
			'penci_review_schema_markup',
			'penci_review_img_size',
			'penci_rv_dis_point',
			'penci_rv_dis_goodbad',
			'penci_rv_dis_desc',
			'penci_rv_enable_sim_author',
			'penci_rv_hide_featured_img',
			'penci_rv_hide_schema',
			'penci_rv_star_rating',
		);

		if ( isset( $_POST ) && is_array( $_POST ) ) {
			$review_meta = array();
			foreach ( $_POST as $meta_key => $meta_value ) {

				$meta_key_check = substr( $meta_key, 0, 12 ) === 'penci_review' || substr( $meta_key, 0, 8 ) === 'penci_rv';

				if ( $meta_key !== 'penci_review_meta' && $meta_key_check ) {
					if ( in_array( $meta_key, $group_items ) ) {
						$review_meta[ $meta_key ] = $meta_value;
					} else {
						update_post_meta( $post_id, $meta_key, $meta_value );
					}
				}
			}
			update_post_meta( $post_id, 'penci_review_meta', $review_meta );
		}
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'penci_review_custom_box', 'penci_review_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$review_title = get_post_meta( $post->ID, 'penci_review_title', true );
		$review_des   = get_post_meta( $post->ID, 'penci_review_des', true );
		// $review_type = get_post_meta( $post->ID, 'penci_review_etype', true );
		$review_1     = get_post_meta( $post->ID, 'penci_review_1', true );
		$review_1num  = get_post_meta( $post->ID, 'penci_review_1_num', true );
		$review_2     = get_post_meta( $post->ID, 'penci_review_2', true );
		$review_2num  = get_post_meta( $post->ID, 'penci_review_2_num', true );
		$review_3     = get_post_meta( $post->ID, 'penci_review_3', true );
		$review_3num  = get_post_meta( $post->ID, 'penci_review_3_num', true );
		$review_4     = get_post_meta( $post->ID, 'penci_review_4', true );
		$review_4num  = get_post_meta( $post->ID, 'penci_review_4_num', true );
		$review_5     = get_post_meta( $post->ID, 'penci_review_5', true );
		$review_5num  = get_post_meta( $post->ID, 'penci_review_5_num', true );
		$review_6     = get_post_meta( $post->ID, 'penci_review_6', true );
		$review_6num  = get_post_meta( $post->ID, 'penci_review_6_num', true );
		$review_7     = get_post_meta( $post->ID, 'penci_review_7', true );
		$review_7num  = get_post_meta( $post->ID, 'penci_review_7_num', true );
		$review_8     = get_post_meta( $post->ID, 'penci_review_8', true );
		$review_8num  = get_post_meta( $post->ID, 'penci_review_8_num', true );
		$review_9     = get_post_meta( $post->ID, 'penci_review_9', true );
		$review_9num  = get_post_meta( $post->ID, 'penci_review_9_num', true );
		$review_10    = get_post_meta( $post->ID, 'penci_review_10', true );
		$review_10num = get_post_meta( $post->ID, 'penci_review_10_num', true );

		$review_good = get_post_meta( $post->ID, 'penci_review_good', true );
		$review_bad  = get_post_meta( $post->ID, 'penci_review_bad', true );

		$review_meta     = get_post_meta( $post->ID, 'penci_review_meta', true );
		$review_address  = isset( $review_meta['penci_review_address'] ) ? $review_meta['penci_review_address'] : '';
		$review_phone    = isset( $review_meta['penci_review_phone'] ) ? $review_meta['penci_review_phone'] : '';
		$review_website  = isset( $review_meta['penci_review_website'] ) ? $review_meta['penci_review_website'] : '';
		$review_price    = isset( $review_meta['penci_review_price'] ) ? $review_meta['penci_review_price'] : '';
		$review_linkbuy  = isset( $review_meta['penci_review_linkbuy'] ) ? $review_meta['penci_review_linkbuy'] : '';
		$review_textbuy  = isset( $review_meta['penci_review_textbuy'] ) ? $review_meta['penci_review_textbuy'] : '';
		$schema_value    = isset( $review_meta['penci_review_schema_markup'] ) ? $review_meta['penci_review_schema_markup'] : 'thing';
		$review_img_size = isset( $review_meta['penci_review_img_size'] ) ? $review_meta['penci_review_img_size'] : '';

		$review_ct_image     = get_post_meta( $post->ID, 'penci_review_custom_image', true );
		$url_review_ct_image = $review_ct_image ? wp_get_attachment_thumb_url( $review_ct_image ) : '';

		$review_items = get_post_meta( $post->ID, 'penci_review_items', true );

		// Display the form, using the current value.
		?>

        <p>To display all the reviews for this post, you can use the following shortcode: <span
                    class="penci-review-shortcode">[penci_review]</span><br>If you do not need this feature, please go
            to <strong>Plugins > Installed Plugins > and deactivate the "Penci Review"</strong> plugin</p>
        <p>

		<?php
		$list_checkbox = array(
			'penci_rv_hide_featured_img' => esc_html__( 'Featured Image on Reviews Box', 'penci' ),
			'penci_rv_hide_schema'       => esc_html__( 'Reviewed Schema Info', 'penci' ),
			'penci_rv_star_rating'       => esc_html__( 'Show Stars Rating', 'penci' ),
		);

		foreach ( $list_checkbox as $id => $title ) {
			$checkbox_value = isset( $review_meta[ $id ] ) ? $review_meta[ $id ] : '';
			?>
            <p>
                <label for="<?php echo esc_attr( $id ); ?>"
                       class="penci-format-row penci-format-row2"><?php echo $title; ?></label>
                <select name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>">
                    <option value="" <?php selected( $checkbox_value, '' ); ?>><?php esc_html_e( 'Default' ); ?></option>
                    <option value="enable" <?php selected( $checkbox_value, 'enable' ); ?>><?php esc_html_e( 'Show' ); ?></option>
                    <option value="disable" <?php selected( $checkbox_value, 'disable' ); ?>><?php esc_html_e( 'Hide' ); ?></option>
                </select>
            </p>
		<?php } ?>

        <div class="penci-table-meta-button-area-top">
            <ul class="penci-table-meta-tabs">
                <li class="first"><a data-id="penci-review-area-1" class="penci-review-tabs-btn active" href="#">Review
                        {<span
                                class="order-number">1</span>}</a></li>
				<?php
				if ( $review_items ) :
					foreach ( $review_items as $number => $items ) :
						?>
                        <li><a data-id="penci-review-area-<?php echo $number; ?>" class="penci-review-tabs-btn"
                               href="#">Review
                                {<span
                                        class="order-number"><?php echo $number; ?></span>}</a></li>
					<?php
					endforeach;
				endif;
				?>
            </ul>
            <div class="penci-table-meta-action-area">
                <a class="button penci-table-area-action"
                   href="#"><?php _e( 'Add new Review', 'penci-review' ); ?></a>
            </div>
        </div>

        <div class="penci-table-meta active first penci-table-repeat" id="penci-review-area-1">
            <h3>Review settings #<span class="order-number">1</span></h3>
            <p><?php _e( 'You can show this single review into your post by insert shortcode', 'soledad' ); ?>
                <span class="penci-review-shortcode">[penci_review index=1]</span></p>
            <p>
                <label for="penci_review_title" class="penci-format-row">Review Title:</label>
                <input style="width:100%;" type="text" name="penci_review_title" id="penci_review_title"
                       value="<?php
				       if ( isset( $review_title ) ) :
					       echo $review_title;
				       endif;
				       ?>">
            </p>

            <div class="penci-grid-2">
                <p>
                    <label for="penci_review_address"
                           class="penci-format-row"><?php esc_html_e( 'Adress', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_address" id="penci_review_address"
                           value="<?php echo esc_attr( $review_address ); ?>">
                </p>
                <p>
                    <label for="penci_review_phone"
                           class="penci-format-row"><?php esc_html_e( 'Phone', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_phone" id="penci_review_phone"
                           value="<?php echo esc_attr( $review_phone ); ?>">
                </p>
                <p>
                    <label for="penci_review_website"
                           class="penci-format-row"><?php esc_html_e( 'Website', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_website" id="penci_review_website"
                           value="<?php echo esc_attr( $review_website ); ?>">
                </p>
                <p>
                    <label for="penci_review_price"
                           class="penci-format-row"><?php esc_html_e( 'Product Price', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_price" id="penci_review_price"
                           value="<?php echo esc_attr( $review_price ); ?>">
                </p>
                <p>
                    <label for="penci_review_linkbuy"
                           class="penci-format-row"><?php esc_html_e( 'Link for Buy', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_linkbuy" id="penci_review_linkbuy"
                           value="<?php echo esc_attr( $review_linkbuy ); ?>">
                </p>
                <p>
                    <label for="penci_review_textbuy"
                           class="penci-format-row"><?php esc_html_e( 'Custom "Buy Now" Text', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_textbuy" id="penci_review_textbuy"
                           value="<?php echo esc_attr( $review_textbuy ); ?>">
                </p>

            </div>
            <div>
                <label for="penci_review_custom_image"
                       class="penci-format-row"><?php esc_html_e( 'Custom Image for Reviews Box', 'penci' ); ?>:</label>
                <div class="penci-widget-image media-widget-control" style="max-width: 150px;">
                    <input name="penci_review_custom_image" type="hidden" class="penci-widget-image__input"
                           value="<?php echo esc_attr( $review_ct_image ); ?>">
                    <img src="<?php echo esc_url( $url_review_ct_image ); ?>"
                         class="penci-widget-image__image<?php echo $review_ct_image ? '' : ' hidden'; ?>">
                    <div class="placeholder <?php echo( $url_review_ct_image ? 'hidden' : '' ); ?>"><?php _e( 'No image selected' ); ?></div>
                    <button class="button penci-widget-image__select_review"><?php esc_html_e( 'Select' ); ?></button>
                    <button class="button penci-widget-image__remove"><?php esc_html_e( 'Remove' ); ?></button>
                </div>
            </div>
            <div class="penci-grid-2" style="clear:both;">
                <p>
                    <label for="penci_review_img_size"
                           class="penci-format-row"><?php esc_html_e( 'Image size', 'penci' ); ?></label>
                    <input style="width:100%;" type="text" name="penci_review_img_size" id="penci_review_img_size"
                           value="<?php echo esc_attr( $review_img_size ); ?>">
                    <span class="penci-recipe-description">Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme).</span>
                </p>
            </div>
			<?php $list_schema = Penci_Review_Schema_Markup::get_list_schema(); ?>
            <p>
                <label for="penci_review_schema_markup"
                       class="penci-format-row penci-format-row2"><?php esc_html_e( 'Reviewed Item Schema', 'penci' ); ?></label>
                <select name="penci_review_schema_markup" class="penci_review_schema_markup"
                        id="penci_review_schema_markup">
					<?php
					foreach ( $list_schema as $schema_type => $schema_label ) {
						echo '<option value="' . $schema_type . '" ' . selected( $schema_value, $schema_type, false ) . '>' . $schema_label . '</option>';
					}
					?>
                </select>
            </p>
            <div id="penci_review_schema_options"><?php foreach ( $list_schema as $schema_type => $schema_label ) {
					if ( 'Thing' == $schema_type || 'none' == $schema_type ) {
						continue;
					}
					Penci_Review_Schema_Markup::get_schema_filed( $schema_type, $schema_value, $post->ID );
				} ?></div>
            <p><label for="penci_review_des" class="penci-format-row">Description:</label><textarea
                        style="width:100%; height:120px;" name="penci_review_des"
                        id="penci_review_des"><?php if ( isset( $review_des ) ) :echo $review_des;endif; ?></textarea><span
                        class="penci-recipe-description">You can write some description for your review here.</span></p>
            <div class="penci-review-points" style="display: table; width: 100%;">
                <div class="penci-col-6 penci-review-left"><p class="review-odd"><label for="penci_review_1"
                                                                                        class="penci-format-row">Review
                            Title for Point 1:</label><input style="width:100px;" type="text" name="penci_review_1"
                                                             id="penci_review_1"
                                                             value="<?php if ( isset( $review_1 ) ) :echo $review_1;endif; ?>"><span
                                class="penci-recipe-description">Example: Design</span></p>
                    <p><label for="penci_review_1_num" class="penci-format-row">Review Number for Point 1:</label><input
                                style="width:100px;" type="number" name="penci_review_1_num" id="penci_review_1_num"
                                value="<?php if ( isset( $review_1num ) ) :echo $review_1num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_2" class="penci-format-row">Review Title for Point
                            2:</label><input style="width:100px;" type="text" name="penci_review_2" id="penci_review_2"
                                             value="<?php if ( isset( $review_2 ) ) :echo $review_2;endif; ?>"></p>
                    <p><label for="penci_review_2_num" class="penci-format-row">Review Number for Point 2:</label><input
                                style="width:100px;" type="number" name="penci_review_2_num" id="penci_review_2_num"
                                value="<?php if ( isset( $review_2num ) ) :echo $review_2num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_3" class="penci-format-row">Review Title for Point
                            3:</label><input style="width:100px;" type="text" name="penci_review_3" id="penci_review_3"
                                             value="<?php if ( isset( $review_3 ) ) :echo $review_3;endif; ?>"></p>
                    <p><label for="penci_review_3_num" class="penci-format-row">Review Number for Point 3:</label><input
                                style="width:100px;" type="number" name="penci_review_3_num" id="penci_review_3_num"
                                value="<?php if ( isset( $review_3num ) ) :echo $review_3num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_4" class="penci-format-row">Review Title for Point
                            4:</label><input style="width:100px;" type="text" name="penci_review_4" id="penci_review_4"
                                             value="<?php if ( isset( $review_4 ) ) :echo $review_4;endif; ?>"></p>
                    <p><label for="penci_review_4_num" class="penci-format-row">Review Number for Point 4:</label><input
                                style="width:100px;" type="number" name="penci_review_4_num" id="penci_review_4_num"
                                value="<?php if ( isset( $review_4num ) ) :echo $review_4num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_5" class="penci-format-row">Review Title for Point
                            5:</label><input style="width:100px;" type="text" name="penci_review_5" id="penci_review_5"
                                             value="<?php if ( isset( $review_5 ) ) :echo $review_5;endif; ?>"></p>
                    <p><label for="penci_review_5_num" class="penci-format-row">Review Number for Point 5:</label><input
                                style="width:100px;" type="number" name="penci_review_5_num" id="penci_review_5_num"
                                value="<?php if ( isset( $review_5num ) ) :echo $review_5num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                </div>
                <div class="penci-col-6 penci-review-right"><p class="review-odd"><label for="penci_review_6"
                                                                                         class="penci-format-row">Review
                            Title for Point 6:</label><input style="width:100px;" type="text" name="penci_review_6"
                                                             id="penci_review_6"
                                                             value="<?php if ( isset( $review_6 ) ) :echo $review_6;endif; ?>"><span
                                class="penci-recipe-description">Example: Design</span></p>
                    <p><label for="penci_review_6_num" class="penci-format-row">Review Number for Point 6:</label><input
                                style="width:100px;" type="number" name="penci_review_6_num" id="penci_review_6_num"
                                value="<?php if ( isset( $review_6num ) ) :echo $review_6num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_7" class="penci-format-row">Review Title for Point
                            7:</label><input style="width:100px;" type="text" name="penci_review_7" id="penci_review_7"
                                             value="<?php if ( isset( $review_7 ) ) :echo $review_7;endif; ?>"></p>
                    <p><label for="penci_review_7_num" class="penci-format-row">Review Number for Point 7:</label><input
                                style="width:100px;" type="number" name="penci_review_7_num" id="penci_review_7_num"
                                value="<?php if ( isset( $review_7num ) ) :echo $review_7num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_8" class="penci-format-row">Review Title for Point
                            8:</label><input style="width:100px;" type="text" name="penci_review_8" id="penci_review_8"
                                             value="<?php if ( isset( $review_8 ) ) :echo $review_8;endif; ?>"></p>
                    <p><label for="penci_review_8_num" class="penci-format-row">Review Number for Point 8:</label><input
                                style="width:100px;" type="number" name="penci_review_8_num" id="penci_review_8_num"
                                value="<?php if ( isset( $review_8num ) ) :echo $review_8num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_9" class="penci-format-row">Review Title for Point
                            9:</label><input style="width:100px;" type="text" name="penci_review_9" id="penci_review_9"
                                             value="<?php if ( isset( $review_9 ) ) :echo $review_9;endif; ?>"></p>
                    <p><label for="penci_review_9_num" class="penci-format-row">Review Number for Point 9:</label><input
                                style="width:100px;" type="number" name="penci_review_9_num" id="penci_review_9_num"
                                value="<?php if ( isset( $review_9num ) ) :echo $review_9num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                    <p class="review-odd"><label for="penci_review_10" class="penci-format-row">Review Title for Point
                            10:</label><input style="width:100px;" type="text" name="penci_review_10"
                                              id="penci_review_10"
                                              value="<?php if ( isset( $review_10 ) ) :echo $review_10;endif; ?>"></p>
                    <p><label for="penci_review_10_num" class="penci-format-row">Review Number for Point
                            10:</label><input style="width:100px;" type="number" name="penci_review_10_num"
                                              id="penci_review_10_num"
                                              value="<?php if ( isset( $review_10num ) ) :echo $review_10num;endif; ?>"><span
                                class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span></p>
                </div>
            </div>
            <p><label for="penci_review_good" class="penci-format-row">The Goods:</label><textarea
                        style="width:100%; height:120px;" name="penci_review_good"
                        id="penci_review_good"><?php if ( isset( $review_good ) ) :echo $review_good;endif; ?></textarea><span
                        class="penci-recipe-description">Type each the good on a new line.</span></p>
            <p><label for="penci_review_bad" class="penci-format-row">The Bads:</label><textarea
                        style="width:100%; height:120px;" name="penci_review_bad"
                        id="penci_review_bad"><?php if ( isset( $review_bad ) ) :echo $review_bad;endif; ?></textarea><span
                        class="penci-recipe-description">Type each the bad on a new line.</span></p></div>

        <!--start the loop-->
		<?php
		if ( $review_items ) :
			foreach ( $review_items as $number => $items ) :
				// Use get_post_meta to retrieve an existing value from the database.
				$review_title = $items['penci_review_title'];
				$review_des   = $items['penci_review_des'];
				$review_1     = $items['penci_review_1'];
				$review_1num  = $items['penci_review_1_num'];
				$review_2     = $items['penci_review_2'];
				$review_2num  = $items['penci_review_2_num'];
				$review_3     = $items['penci_review_3'];
				$review_3num  = $items['penci_review_3_num'];
				$review_4     = $items['penci_review_4'];
				$review_4num  = $items['penci_review_4_num'];
				$review_5     = $items['penci_review_5'];
				$review_5num  = $items['penci_review_5_num'];
				$review_6     = $items['penci_review_6'];
				$review_6num  = $items['penci_review_6_num'];
				$review_7     = $items['penci_review_7'];
				$review_7num  = $items['penci_review_7_num'];
				$review_8     = $items['penci_review_8'];
				$review_8num  = $items['penci_review_8_num'];
				$review_9     = $items['penci_review_9'];
				$review_9num  = $items['penci_review_9_num'];
				$review_10    = $items['penci_review_10'];
				$review_10num = $items['penci_review_10_num'];

				$review_good     = $items['penci_review_good'];
				$review_bad      = $items['penci_review_bad'];
				$review_address  = isset( $items['penci_review_address'] ) ? $items['penci_review_address'] : '';
				$review_phone    = isset( $items['penci_review_phone'] ) ? $items['penci_review_phone'] : '';
				$review_website  = isset( $items['penci_review_website'] ) ? $items['penci_review_website'] : '';
				$review_price    = isset( $items['penci_review_price'] ) ? $items['penci_review_price'] : '';
				$review_linkbuy  = isset( $items['penci_review_linkbuy'] ) ? $items['penci_review_linkbuy'] : '';
				$review_textbuy  = isset( $items['penci_review_textbuy'] ) ? $items['penci_review_textbuy'] : '';
				$schema_value    = isset( $items['penci_review_schema_markup'] ) ? $items['penci_review_schema_markup'] : 'thing';
				$review_img_size = isset( $items['penci_review_img_size'] ) ? $items['penci_review_img_size'] : '';

				$review_ct_image     = isset( $items['penci_review_custom_image'] ) ? $items['penci_review_custom_image'] : '';
				$url_review_ct_image = wp_get_attachment_thumb_url( $review_ct_image );
				$prefix              = 'penci_review_items[' . $number . ']';
				?>
                <div class="penci-table-meta penci-table-repeat" id="penci-review-area-<?php echo $number; ?>"><h3>
                        Review settings #<span class="order-number"><?php echo $number; ?></span></h3>
                    <p><?php _e( 'You can show this single review into your post by insert shortcode', 'soledad' ); ?>
                        <span class="penci-review-shortcode">[penci_review index=<span
                                    class="order-number"><?php echo $number; ?></span>]</span></p>
                    <p><label for="<?php echo $prefix; ?>[penci_review_title]" class="penci-format-row">Review
                            Title:</label><input style="width:100%;" type="text"
                                                 name="<?php echo $prefix; ?>[penci_review_title]"
                                                 id="<?php echo $prefix; ?>[penci_review_title]"
                                                 value="<?php if ( isset( $review_title ) ) :echo $review_title;endif; ?>">
                    </p>
                    <div class="penci-grid-2"><p><label
                                    for="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_address]]"
                                    class="penci-format-row"><?php esc_html_e( 'Address', 'penci' ); ?></label><input
                                    style="width:100%;" type="text"
                                    name="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_address]]"
                                    id="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_address]]"
                                    value="<?php echo esc_attr( $review_address ); ?>"></p>
                        <p><label for="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_phone]]"
                                  class="penci-format-row"><?php esc_html_e( 'Phone', 'penci' ); ?></label><input
                                    style="width:100%;" type="text"
                                    name="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_phone]]"
                                    id="<?php echo $prefix; ?>[<?php echo $prefix; ?>[penci_review_phone]]"
                                    value="<?php echo esc_attr( $review_phone ); ?>"></p>
                        <p><label for="<?php echo $prefix; ?>[penci_review_website]"
                                  class="penci-format-row"><?php esc_html_e( 'Website', 'penci' ); ?></label><input
                                    style="width:100%;" type="text" name="<?php echo $prefix; ?>[penci_review_website]"
                                    id="<?php echo $prefix; ?>[penci_review_website]"
                                    value="<?php echo esc_attr( $review_website ); ?>"></p>
                        <p><label for="<?php echo $prefix; ?>[penci_review_price]"
                                  class="penci-format-row"><?php esc_html_e( 'Product Price', 'penci' ); ?></label><input
                                    style="width:100%;" type="text" name="<?php echo $prefix; ?>[penci_review_price]"
                                    id="<?php echo $prefix; ?>[penci_review_price]"
                                    value="<?php echo esc_attr( $review_price ); ?>"></p>
                        <p><label for="<?php echo $prefix; ?>[penci_review_linkbuy]"
                                  class="penci-format-row"><?php esc_html_e( 'Link for Buy', 'penci' ); ?></label><input
                                    style="width:100%;" type="text" name="<?php echo $prefix; ?>[penci_review_linkbuy]"
                                    id="<?php echo $prefix; ?>[penci_review_linkbuy]"
                                    value="<?php echo esc_attr( $review_linkbuy ); ?>"></p>
                        <p><label for="<?php echo $prefix; ?>[penci_review_textbuy]"
                                  class="penci-format-row"><?php esc_html_e( 'Custom "Buy Now" Text', 'penci' ); ?></label><input
                                    style="width:100%;" type="text" name="<?php echo $prefix; ?>[penci_review_textbuy]"
                                    id="<?php echo $prefix; ?>[penci_review_textbuy]"
                                    value="<?php echo esc_attr( $review_textbuy ); ?>"></p></div>
                    <div><label for="<?php echo $prefix; ?>[penci_review_custom_image]"
                                class="penci-format-row"><?php esc_html_e( 'Custom Image for Reviews Box', 'penci' ); ?>
                            :</label>
                        <div class="penci-widget-image media-widget-control" style="max-width: 150px;"><input
                                    name="<?php echo $prefix; ?>[penci_review_custom_image]" type="hidden"
                                    class="penci-widget-image__input"
                                    value="<?php echo esc_attr( $review_ct_image ); ?>"><img
                                    src="<?php echo esc_url( $url_review_ct_image ); ?>"
                                    class="penci-widget-image__image<?php echo $review_ct_image ? '' : ' hidden'; ?>">
                            <div class="placeholder <?php echo( $url_review_ct_image ? 'hidden' : '' ); ?>"><?php _e( 'No image selected' ); ?></div>
                            <button class="button penci-widget-image__select_review"><?php esc_html_e( 'Select' ); ?></button>
                            <button class="button penci-widget-image__remove"><?php esc_html_e( 'Remove' ); ?></button>
                        </div>
                    </div>
                    <div class="penci-grid-2" style="clear:both;"><p><label
                                    for="<?php echo $prefix; ?>[penci_review_img_size]"
                                    class="penci-format-row"><?php esc_html_e( 'Image size', 'penci' ); ?></label><input
                                    style="width:100%;" type="text" name="<?php echo $prefix; ?>[penci_review_img_size]"
                                    id="<?php echo $prefix; ?>[penci_review_img_size]"
                                    value="<?php echo esc_attr( $review_img_size ); ?>"><span
                                    class="penci-recipe-description">Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme).</span>
                        </p></div><?php $list_schema = Penci_Review_Schema_Markup::get_list_schema(); ?><p><label
                                for="<?php echo $prefix; ?>[penci_review_schema_markup]"
                                class="penci-format-row penci-format-row2"><?php esc_html_e( 'Reviewed Item Schema', 'penci' ); ?></label><select
                                name="<?php echo $prefix; ?>[penci_review_schema_markup]"
                                class="penci_review_schema_markup"
                                id="<?php echo $prefix; ?>[penci_review_schema_markup]"><?php foreach ( $list_schema as $schema_type => $schema_label ) {
								echo '<option value="' . $schema_type . '" ' . selected( $schema_value, $schema_type, false ) . '>' . $schema_label . '</option>';
							} ?></select></p>
                    <div id="<?php echo $prefix; ?>[penci_review_schema_options]"><?php foreach ( $list_schema as $schema_type => $schema_label ) {
							if ( 'Thing' == $schema_type || 'none' == $schema_type ) {
								continue;
							}
							Penci_Review_Schema_Markup::get_schema_filed( $schema_type, $schema_value, $post->ID );
						} ?></div>
                    <p><label for="<?php echo $prefix; ?>[penci_review_des]"
                              class="penci-format-row">Description:</label><textarea style="width:100%; height:120px;"
                                                                                     name="<?php echo $prefix; ?>[penci_review_des]"
                                                                                     id="<?php echo $prefix; ?>[penci_review_des]"><?php if ( isset( $review_des ) ) :echo $review_des;endif; ?></textarea><span
                                class="penci-recipe-description">You can write some description for your review here.</span>
                    </p>
                    <div class="penci-review-points" style="display: table; width: 100%;">
                        <div class="penci-col-6 penci-review-left"><p class="review-odd"><label
                                        for="<?php echo $prefix; ?>[penci_review_1]" class="penci-format-row">Review
                                    Title for Point 1:</label><input style="width:100px;" type="text"
                                                                     name="<?php echo $prefix; ?>[penci_review_1]"
                                                                     id="<?php echo $prefix; ?>[penci_review_1]"
                                                                     value="<?php if ( isset( $review_1 ) ) :echo $review_1;endif; ?>"><span
                                        class="penci-recipe-description">Example: Design</span></p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_1_num]" class="penci-format-row">Review
                                    Number for Point 1:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_1_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_1_num]"
                                                                      value="<?php if ( isset( $review_1num ) ) :echo $review_1num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_2]"
                                                         class="penci-format-row">Review Title for Point
                                    2:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_2]"
                                                     id="<?php echo $prefix; ?>[penci_review_2]"
                                                     value="<?php if ( isset( $review_2 ) ) :echo $review_2;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_2_num]" class="penci-format-row">Review
                                    Number for Point 2:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_2_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_2_num]"
                                                                      value="<?php if ( isset( $review_2num ) ) :echo $review_2num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_3]"
                                                         class="penci-format-row">Review Title for Point
                                    3:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_3]"
                                                     id="<?php echo $prefix; ?>[penci_review_3]"
                                                     value="<?php if ( isset( $review_3 ) ) :echo $review_3;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_3_num]" class="penci-format-row">Review
                                    Number for Point 3:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_3_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_3_num]"
                                                                      value="<?php if ( isset( $review_3num ) ) :echo $review_3num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_4]"
                                                         class="penci-format-row">Review Title for Point
                                    4:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_4]"
                                                     id="<?php echo $prefix; ?>[penci_review_4]"
                                                     value="<?php if ( isset( $review_4 ) ) :echo $review_4;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_4_num]" class="penci-format-row">Review
                                    Number for Point 4:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_4_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_4_num]"
                                                                      value="<?php if ( isset( $review_4num ) ) :echo $review_4num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_5]"
                                                         class="penci-format-row">Review Title for Point
                                    5:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_5]"
                                                     id="<?php echo $prefix; ?>[penci_review_5]"
                                                     value="<?php if ( isset( $review_5 ) ) :echo $review_5;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_5_num]" class="penci-format-row">Review
                                    Number for Point 5:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_5_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_5_num]"
                                                                      value="<?php if ( isset( $review_5num ) ) :echo $review_5num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p></div>
                        <div class="penci-col-6 penci-review-right"><p class="review-odd"><label
                                        for="<?php echo $prefix; ?>[penci_review_6]" class="penci-format-row">Review
                                    Title for Point 6:</label><input style="width:100px;" type="text"
                                                                     name="<?php echo $prefix; ?>[penci_review_6]"
                                                                     id="<?php echo $prefix; ?>[penci_review_6]"
                                                                     value="<?php if ( isset( $review_6 ) ) :echo $review_6;endif; ?>"><span
                                        class="penci-recipe-description">Example: Design</span></p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_6_num]" class="penci-format-row">Review
                                    Number for Point 6:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_6_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_6_num]"
                                                                      value="<?php if ( isset( $review_6num ) ) :echo $review_6num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_7]"
                                                         class="penci-format-row">Review Title for Point
                                    7:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_7]"
                                                     id="<?php echo $prefix; ?>[penci_review_7]"
                                                     value="<?php if ( isset( $review_7 ) ) :echo $review_7;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_7_num]" class="penci-format-row">Review
                                    Number for Point 7:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_7_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_7_num]"
                                                                      value="<?php if ( isset( $review_7num ) ) :echo $review_7num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_8]"
                                                         class="penci-format-row">Review Title for Point
                                    8:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_8]"
                                                     id="<?php echo $prefix; ?>[penci_review_8]"
                                                     value="<?php if ( isset( $review_8 ) ) :echo $review_8;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_8_num]" class="penci-format-row">Review
                                    Number for Point 8:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_8_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_8_num]"
                                                                      value="<?php if ( isset( $review_8num ) ) :echo $review_8num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_9]"
                                                         class="penci-format-row">Review Title for Point
                                    9:</label><input style="width:100px;" type="text"
                                                     name="<?php echo $prefix; ?>[penci_review_9]"
                                                     id="<?php echo $prefix; ?>[penci_review_9]"
                                                     value="<?php if ( isset( $review_9 ) ) :echo $review_9;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_9_num]" class="penci-format-row">Review
                                    Number for Point 9:</label><input style="width:100px;" type="number"
                                                                      name="<?php echo $prefix; ?>[penci_review_9_num]"
                                                                      id="<?php echo $prefix; ?>[penci_review_9_num]"
                                                                      value="<?php if ( isset( $review_9num ) ) :echo $review_9num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p>
                            <p class="review-odd"><label for="<?php echo $prefix; ?>[penci_review_10]"
                                                         class="penci-format-row">Review Title for Point
                                    10:</label><input style="width:100px;" type="text"
                                                      name="<?php echo $prefix; ?>[penci_review_10]"
                                                      id="<?php echo $prefix; ?>[penci_review_10]"
                                                      value="<?php if ( isset( $review_10 ) ) :echo $review_10;endif; ?>">
                            </p>
                            <p><label for="<?php echo $prefix; ?>[penci_review_10_num]" class="penci-format-row">Review
                                    Number for Point 10:</label><input style="width:100px;" type="number"
                                                                       name="<?php echo $prefix; ?>[penci_review_10_num]"
                                                                       id="<?php echo $prefix; ?>[penci_review_10_num]"
                                                                       value="<?php if ( isset( $review_10num ) ) :echo $review_10num;endif; ?>"><span
                                        class="penci-recipe-description">Minimum is 1, Maximum is 10. Example: 8</span>
                            </p></div>
                    </div>
                    <p><label for="<?php echo $prefix; ?>[penci_review_good]" class="penci-format-row">The
                            Goods:</label><textarea style="width:100%; height:120px;"
                                                    name="<?php echo $prefix; ?>[penci_review_good]"
                                                    id="<?php echo $prefix; ?>[penci_review_good]"><?php if ( isset( $review_good ) ) :echo $review_good;endif; ?></textarea><span
                                class="penci-recipe-description">Type each the good on a new line.</span></p>
                    <p><label for="<?php echo $prefix; ?>[penci_review_bad]" class="penci-format-row">The
                            Bads:</label><textarea style="width:100%; height:120px;"
                                                   name="<?php echo $prefix; ?>[penci_review_bad]"
                                                   id="<?php echo $prefix; ?>[penci_review_bad]"><?php if ( isset( $review_bad ) ) :echo $review_bad;endif; ?></textarea><span
                                class="penci-recipe-description">Type each the bad on a new line.</span></p></div>

			<?php
			endforeach;
		endif;
		?>

        <div class="penci-table-meta-button-area"></div>
		<?php
	}
}
