<?php
/*
Plugin Name: Penci Live Blog
Plugin URI: https://pencidesign.net/
Description: Covering a conference, sports event, breaking news or other quickly developing events? You want your readers to be updated as quickly as possible. The best way to do that is by providing them with a liveblog.
Version: 1.3
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-liveblog
*/

if ( 'soledad' != get_option( 'template' ) ) {
	return;
}

define( 'PENCI_LB_VERSION', '1.3' );
define( 'PENCI_LB_PLUGIN', __FILE__ );
define( 'PENCI_LB_PLUGIN_BASENAME', plugin_basename( PENCI_LB_PLUGIN ) );
define( 'PENCI_LB_PLUGIN_DIR', untrailingslashit( dirname( PENCI_LB_PLUGIN ) ) );
define( 'PENCI_LB_META_KEY', 'pcliveblog_updates' );
define( 'PENCI_LB_STATUS_KEY', 'pcliveblog_status' );

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciLiveBlogCustomizer::getInstance();
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_liveblog_panel'] = array(
			'priority'                            => 30,
			'path'                                => plugin_dir_path( __FILE__ ) . '/customizer/',
			'panel'                               => array(
				'title' => esc_html__( 'Live Blog', 'soledad' ),
				'icon'  => 'fas fa-record-vinyl',
			),
			'penci_liveblog_general_section'      => array( 'title' => esc_html__( 'General', 'penci-liveblog' ) ),
			'penci_liveblog_styles_section'       => array( 'title' => esc_html__( 'Font Size & Colors', 'penci-liveblog' ) ),
			'penci_liveblog_translations_section' => array( 'title' => esc_html__( 'Text Translations', 'penci-liveblog' ) ),
		);
		return $options;
	}
);

require 'inc/metabox.php';
if ( ! function_exists( 'penci_load_admin_metabox_liveblog_style' ) ) {
	function penci_load_admin_metabox_liveblog_style() {
		$screen = get_current_screen();
		if ( $screen->id == 'post' ) {

			$localize_script = array(
				'url'     => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'penci_liveblog_update_content' ),
				'timeout' => (int) get_theme_mod( 'penci_liveblog_timeout', 60 ) * 1000,
				'update'  => penci_livesub_text( 'event_update' ),
				'cancel'  => penci_livesub_text( 'event_cancel' ),
			);

			wp_enqueue_style( 'penciliveblog_box_review_styles', plugin_dir_url( __FILE__ ) . 'css/admin-css.css', '', PENCI_LB_VERSION );
			wp_enqueue_script(
				'penciliveblog_js',
				plugin_dir_url( __FILE__ ) . 'js/liveblog.js',
				array(
					'jquery',
					'jquery-ui-datepicker',
				),
				PENCI_LB_VERSION,
				true
			);

			wp_localize_script( 'penciliveblog_js', 'penciliveblog', $localize_script );
		}
	}

	add_action( 'admin_enqueue_scripts', 'penci_load_admin_metabox_liveblog_style' );
}

if ( ! function_exists( 'penci_liveblog_register_assets' ) ) {
	function penci_liveblog_register_assets() {
		$localize_script = array(
			'url'           => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'penci_liveblog_update_content' ),
			'tab'           => (bool) get_theme_mod( 'penci_liveblog_change_page_title' ),
			'timeout'       => (int) get_theme_mod( 'penci_liveblog_timeout', 60 ) * 1000,
			'ads_num'       => (int) get_theme_mod( 'penci_liveblog_infeedads_num', 3 ),
			'ads_code'      => get_theme_mod( 'penci_liveblog_infeedads_code' ),
			'event_copies'  => penci_livesub_text( 'event_copies' ),
			'disable_share' => (bool) get_theme_mod( 'penci_liveblog_share' ),
		);
		wp_enqueue_style( 'penci-liveblog', plugin_dir_url( __FILE__ ) . 'css/liveblog.css', '', PENCI_LB_VERSION );
		wp_enqueue_script( 'penci-liveblog', plugin_dir_url( __FILE__ ) . 'js/liveblog-front.js', array( 'jquery' ), PENCI_LB_VERSION, true );

		wp_localize_script( 'penci-liveblog', 'penciliveblog', $localize_script );
	}

	add_action( 'wp_enqueue_scripts', 'penci_liveblog_register_assets' );
}


/* Add new item */
add_action( 'wp_ajax_penci_liveblog_update_content', 'penci_liveblog_update_content' );
if ( ! function_exists( 'penci_liveblog_update_content' ) ) {

	function penci_liveblog_update_content() {
		wp_verify_nonce( $_REQUEST['nonce'], 'penci_liveblog_update_content' );

		$post_id      = $_REQUEST['id'];
		$item_id      = isset( $_REQUEST['iid'] ) && $_REQUEST['iid'] ? $_REQUEST['iid'] : '';
		$meta_time    = isset( $_REQUEST['update'] ) && $_REQUEST['update'] ? $_REQUEST['update'] : current_time( 'timestamp' );
		$meta_content = $_REQUEST['content'];
		$meta_title   = $_REQUEST['title'];
		$utime        = current_time( 'timestamp' );

		$all_live_events = get_post_meta( $post_id, PENCI_LB_META_KEY, true );

		$all_live_events = ! empty( $all_live_events ) ? $all_live_events : array();

		$update_content = array(
			'time'    => $meta_time,
			'title'   => $meta_title,
			'content' => $meta_content,
		);

		if ( $item_id ) {
			$all_live_events[ $item_id ]             = $update_content;
			$all_live_events['update']               = $utime;
			$all_live_events['update_log'][ $utime ] = array( 'edited' => $item_id );
		} else {
			$all_live_events[ $utime ] = $update_content;
		}

		if ( update_post_meta( $post_id, PENCI_LB_META_KEY, $all_live_events ) ) {
			wp_cache_delete( $post_id, 'post_meta' );
			wp_send_json_success(
				array(
					'time'        => $utime,
					'time_format' => pclive_blog_time_format( $meta_time ),
				)
			);
		} else {
			wp_send_json_error( 'error' );
		}
	}
}

/* Delete item */
add_action( 'wp_ajax_penci_liveblog_delete_content', 'penci_liveblog_delete_content' );
if ( ! function_exists( 'penci_liveblog_delete_content' ) ) {

	function penci_liveblog_delete_content() {
		wp_verify_nonce( $_REQUEST['nonce'], 'penci_liveblog_update_content' );
		$post_id = $_REQUEST['id'];
		$item_id = $_REQUEST['itemid'];
		$utime   = $_REQUEST['time'];

		$all_live_events = get_post_meta( $post_id, PENCI_LB_META_KEY, true );

		$all_live_events[ $item_id ]             = array();
		$all_live_events['update']               = $utime;
		$all_live_events['update_log'][ $utime ] = array( 'delete' => $item_id );

		if ( update_post_meta( $post_id, PENCI_LB_META_KEY, $all_live_events ) ) {
			wp_cache_delete( $post_id, 'post_meta' );
			wp_send_json_success(
				array(
					'time'        => $utime,
					'time_format' => pclive_blog_time_format( $utime ),
				)
			);
		} else {
			wp_send_json_error( 'error' );
		}
	}
}

// update status
add_action( 'wp_ajax_penci_liveblog_update_status', 'penci_liveblog_update_status' );
if ( ! function_exists( 'penci_liveblog_update_status' ) ) {
	function penci_liveblog_update_status() {
		wp_verify_nonce( $_REQUEST['nonce'], 'penci_liveblog_update_content' );
		$post_id = $_REQUEST['id'];
		$value   = $_REQUEST['value'];

		if ( update_post_meta( $post_id, PENCI_LB_STATUS_KEY, $value ) ) {
			wp_send_json_success( 'done' );
		} else {
			wp_send_json_error( 'error' );
		}
	}
}

add_action( 'wp_ajax_penci_liveblog_get_content', 'penci_liveblog_get_content' );
add_action( 'wp_ajax_nopriv_penci_liveblog_get_content', 'penci_liveblog_get_content' );

if ( ! function_exists( 'penci_liveblog_get_content' ) ) {

	function penci_liveblog_get_content() {
		wp_verify_nonce( $_REQUEST['nonce'], 'penci_liveblog_get_content' );
		$post_id            = $_REQUEST['id'];
		$current            = isset( $_REQUEST['current'] ) && $_REQUEST['current'] ? (int) $_REQUEST['current'] : 1;
		$all_live_events    = pclive_blog_get_meta( $post_id, PENCI_LB_META_KEY );
		$total_current_live = count( $all_live_events );

		if ( ! empty( $all_live_events ) && $total_current_live > $current ) {

			$new_live_item = end( $all_live_events );

			$item_id = array_key_last( $all_live_events );

			wp_send_json_success(
				array(
					'time'    => pclive_blog_time_format( $new_live_item['time'] ),
					'title'   => $new_live_item['title'],
					'content' => $new_live_item['content'],
					'item'    => $current + 1,
					'id'      => $item_id,
					'update'  => isset( $all_live_events['update'] ) ? $all_live_events['update'] : '',
					'share'   => pclive_share_url( $item_id, $post_id, $new_live_item['title'], $new_live_item['content'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'message' => __( 'No update', 'penci-liveblog' ),
					'update'  => isset( $all_live_events['update'] ) ? $all_live_events['update'] : '',
				),
			);
		}
	}
}

// Get edited/deleted item
add_action( 'wp_ajax_penci_liveblog_get_update_content', 'penci_liveblog_get_update_content' );
add_action( 'wp_ajax_nopriv_penci_liveblog_get_update_content', 'penci_liveblog_get_update_content' );

if ( ! function_exists( 'penci_liveblog_get_update_content' ) ) {

	function penci_liveblog_get_update_content() {
		wp_verify_nonce( $_REQUEST['nonce'], 'penci_liveblog_get_content' );
		$post_id         = $_REQUEST['id'];
		$time            = isset( $_REQUEST['get'] ) && $_REQUEST['get'] ? (int) $_REQUEST['get'] : '';
		$all_live_events = pclive_blog_get_meta( $post_id, PENCI_LB_META_KEY );
		$update_time     = isset( $all_live_events['update'] ) ? $all_live_events['update'] : '';

		if ( $time && isset( $all_live_events['update_log'][ $time ] ) && $all_live_events['update_log'][ $time ] ) {
			foreach ( $all_live_events['update_log'][ $time ] as $type => $id ) {
				if ( 'edited' == $type ) {
					wp_send_json_success(
						array(
							'action'  => 'edited',
							'time'    => pclive_blog_time_format( $all_live_events[ $id ]['time'] ),
							'title'   => $all_live_events[ $id ]['title'],
							'content' => $all_live_events[ $id ]['content'],
							'id'      => $id,
							'update'  => $update_time,
							'share'   => pclive_share_url( $id, $post_id, $all_live_events[ $id ]['title'], $all_live_events[ $id ]['content'] ),
						)
					);
				} else {
					wp_send_json_success(
						array(
							'action' => 'delete',
							'id'     => $id,
							'update' => $update_time,
						)
					);
				}
			}
		} else {
			wp_send_json_success(
				array(
					'message' => __( 'No update', 'penci-liveblog' ),
					'update'  => $update_time,
				),
			);
		}
	}
}

if ( ! function_exists( 'penci_liveblog_shortcode_content' ) ) {
	add_shortcode( 'penci_liveblog', 'penci_liveblog_shortcode_content' );
	function penci_liveblog_shortcode_content( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'id' => '',
				),
				$atts
			)
		);

		$post_id = get_the_ID();
		if ( ! empty( $id ) && is_numeric( $id ) ) {
			$post_id = $id;
		}

		$status = get_post_meta( $post_id, PENCI_LB_STATUS_KEY, true );

		if ( ! $status ) {
			return;
		}

		ob_start();
		if ( ! get_theme_mod( 'penci_liveblog_notice' ) ) {
			?>
			<div class="pcliveblog-notice-wrapper <?php echo $status; ?>">
				<h4>
					<?php
					if ( $status == 'disable' ) {
						echo penci_livesub_text( 'event_end_notice' );
					} else {
						echo penci_livesub_text( 'event_live_notice' );
					}
					?>
				</h4>
			</div>
			<?php

		}

		$all_live_events = get_post_meta( $post_id, PENCI_LB_META_KEY, true );
		if ( ! empty( $all_live_events ) ) {
			if ( $status == 'enable' || ( isset( $_GET['pclive'] ) && 'newest' == $_GET['pclive'] ) ) {
				$all_live_events = array_reverse( $all_live_events, true );
			}
			?>
			<div data-status="<?php echo $status; ?>" class="pcliveblog-wrapper"
				data-update-time="<?php echo current_time( 'U' ); ?>"
				data-id="<?php echo $post_id; ?>"
				data-current-live="<?php echo count( $all_live_events ); ?>">
				<?php if ( $status == 'enable' ) { ?>
					<div class="pcliveblog-wrapper-status">
						<div class="pcliveblog-wrapper-notice">
							<?php
							$translate_text = str_replace( '{{value}}', '<span class="pcliveblog-count">%s</span>', penci_livesub_text( 'event_update_notice' ) );
							printf( $translate_text, '60' );
							?>
						</div>
						<?php if ( ! get_theme_mod( 'penci_liveblog_hide_btn' ) ) : ?>
							<div class="pcliveblog-wrapper-button">
								<a class="pcliveblog-wrapper-button-action"
									href="#"><?php echo penci_livesub_text( 'event_update_now' ); ?></a>
							</div>
						<?php endif; ?>
					</div>
				<?php } ?>

				<?php
				if ( $status == 'disable' ) {
					$newest_class = isset( $_GET['pclive'] ) && 'newest' == $_GET['pclive'] ? 'active' : 'inactive';
					$oldest_class = isset( $_GET['pclive'] ) && 'oldest' == $_GET['pclive'] ? 'active' : 'inactive';
					$oldest_class = ! isset( $_GET['pclive'] ) ? 'active' : $oldest_class;
					?>
					<div class="pcliveblog-wrapper-buttons">
						<a class="<?php echo $newest_class; ?>"
							href="<?php echo esc_url( get_the_permalink() . '?pclive=newest' ); ?>"><?php echo penci_livesub_text( 'event_newest' ); ?></a>
						<a class="<?php echo $oldest_class; ?>"
							href="<?php echo esc_url( get_the_permalink() . '?pclive=oldest' ); ?>"><?php echo penci_livesub_text( 'event_oldest' ); ?></a>
					</div>
				<?php } ?>

				<div class="pcliveblog-wrapper-listing">
					<?php
					$ads_code     = get_theme_mod( 'penci_liveblog_infeedads_code' );
					$ads_loop     = get_theme_mod( 'penci_liveblog_infeedads_num', 3 );
					$items_number = 0;
					foreach ( $all_live_events as $number => $event ) {
						if ( ! empty( $event ) && isset( $event['time'] ) && isset( $event['title'] ) && isset( $event['content'] ) ) {

							if ( $ads_code && ( $items_number % $ads_loop == 1 ) ) {
								echo '<div class="pcliveblog-listing-ads">' . $ads_code . '</div>';
							}
							?>
							<div class="pcliveblog-listing-item" id="pc-live-item-<?php echo $number; ?>"
								data-item="<?php echo $number; ?>">
								<div class="pcliveblog-date">
									<span><?php echo is_numeric( $event['time'] ) ? date_i18n( 'H:i:s', $event['time'] ) : $event['time']; ?></span>
								</div>
								<?php if ( isset( $event['title'] ) ) : ?>
									<div class="pcliveblog-title">
										<?php echo $event['title']; ?>
									</div>
								<?php endif; ?>
								<?php if ( isset( $event['content'] ) ) : ?>
									<div class="pcliveblog-content">
										<?php echo $event['content']; ?>
									</div>
								<?php endif; ?>
								<?php
								if ( ! get_theme_mod( 'penci_liveblog_share' ) ) :
									echo pclive_share_url( $number, $post_id, $event['title'], $event['content'] );
								endif;
								?>
							</div>
							<?php
							++$items_number;
						}
					}
					?>
				</div>
				<?php echo wp_nonce_field( 'penci_liveblog_get_content' ); ?>
			</div>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}
}
if ( ! function_exists( 'pclive_share_url' ) ) {
	function pclive_share_url( $item, $id, $title, $desc ) {
		$event_link     = get_the_permalink( $id ) . '#pc-live-item-' . $item;
		$facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' . $event_link;
		$twitter_share  = 'https://twitter.com/intent/tweet?text=' . rawurlencode( trim( $title ) ) . ':%20' . rawurlencode( wp_strip_all_tags( $desc ) ) . '%20-%20' . $event_link;

		$out  = '<div class="pcliveblog-item-share">';
		$out .= '<a class="pclb-sitem" target="_blank" title="' . penci_livesub_text( 'event_share_fb' ) . '" href="' . $facebook_share . '">' . penci_icon_by_ver( 'fab fa-facebook-f', '', true ) . ' ' . __( 'Facebook', 'penci-liveblog' ) . '</a>';
		$out .= '<a class="pclb-sitem" target="_blank" title="' . penci_livesub_text( 'event_share_tw' ) . '" href="' . $twitter_share . '">' . penci_icon_by_ver( 'penciicon-x-twitter', '', true ) . ' ' . __( 'Twitter', 'penci-liveblog' ) . '</a>';
		$out .= '<a class="pclb-sitem penci-copy-link" title="' . penci_livesub_text( 'event_copy_link' ) . '" target="_blank" href="' . $event_link . '">' . penci_livesub_text( 'event_copy_link' ) . '</a>';
		$out .= '</div>';

		return $out;
	}
}

if ( ! function_exists( 'pclive_blog_get_meta' ) ) {
	function pclive_blog_get_meta( $object_id, $meta_key ) {
		global $wpdb;
		$table    = _get_meta_table( 'post' );
		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $table WHERE meta_key = %s AND post_id = %d", $meta_key, $object_id ) );

		return isset( $meta_ids[0] ) && $meta_ids[0] ? maybe_unserialize( $meta_ids[0] ) : '';
	}
}

if ( ! function_exists( 'pclive_blog_time_format' ) ) {
	function pclive_blog_time_format( $time ) {
		$time_format = get_theme_mod( 'penci_liveblog_time_format' ) ? get_theme_mod( 'penci_liveblog_time_format' ) : get_option( 'date_format' );
		if ( is_numeric( $time ) ) {
			$time = date_i18n( $time_format, $time );
		}

		return $time;
	}
}

if ( ! function_exists( 'penci_livesub_text' ) ) {
	function penci_livesub_text( $text = '' ) {
		$texts = array(
			'event_live'          => __( 'Live', 'penci-liveblog' ),
			'event_live_notice'   => __( 'Live Updates', 'penci-liveblog' ),
			'event_end_notice'    => __( 'This event has ended.', 'penci-liveblog' ),
			'event_update_now'    => __( 'Update Now', 'penci-liveblog' ),
			'event_update_notice' => __( 'The content will auto-update after {{value}} seconds', 'penci-liveblog' ),
			'event_copy_link'     => __( 'Copy Link', 'penci-liveblog' ),
			'event_copies'        => __( 'Copied!', 'penci-liveblog' ),
			'event_title_new'     => __( 'New Update', 'penci-liveblog' ),
			'event_newest'        => __( 'Newest', 'penci-liveblog' ),
			'event_oldest'        => __( 'Oldest', 'penci-liveblog' ),
			'event_update'        => __( 'Update', 'penci-liveblog' ),
			'event_cancel'        => __( 'Cancel', 'penci-liveblog' ),
			'event_share_fb'      => __( 'Share on Facebook', 'penci-liveblog' ),
			'event_share_tw'      => __( 'Share on Twitter', 'penci-liveblog' ),
		);

		if ( $text ) {
			return do_shortcode( get_theme_mod( 'pclb_trans_' . $text ) ? get_theme_mod( 'pclb_trans_' . $text ) : $texts[ $text ] );
		} else {
			return $texts;
		}
	}
}

add_filter(
	'the_title',
	function ( $post_title, $post_id ) {

		if ( get_theme_mod( 'penci_liveblog_post_prefix', true ) && get_post_meta( $post_id, PENCI_LB_STATUS_KEY, true ) == 'enable' && ! is_admin() ) {

			if ( 'before' == get_theme_mod( 'penci_liveblog_post_prefix_position', 'before' ) ) {
				$post_title = '<span class="pclive-btn livelbbf" data-title="' . penci_livesub_text( 'event_live' ) . '"></span>' . $post_title;
			} else {
				$post_title = $post_title . '<span class="pclive-btn livelbaf" data-title="' . penci_livesub_text( 'event_live' ) . '"></span>';
			}
		}

		return $post_title;
	},
	10,
	2
);


add_filter(
	'wp_trim_words',
	function ( $text, $num_words, $more, $original_text ) {

		$post_id       = get_the_ID();
		$post_title    = wp_strip_all_tags( get_the_title() );
		$original_text = wp_strip_all_tags( $original_text );

		if ( $post_title == $original_text ) {

			if ( get_theme_mod( 'penci_liveblog_post_prefix', true ) && get_post_meta( $post_id, PENCI_LB_STATUS_KEY, true ) == 'enable' && ! is_admin() ) {

				if ( 'before' == get_theme_mod( 'penci_liveblog_post_prefix_position', 'before' ) ) {
					$text = '<span class="pclive-btn livelbbf" data-title="' . penci_livesub_text( 'event_live' ) . '"></span>' . $text;
				} else {
					$text = $text . '<span class="pclive-btn livelbaf" data-title="' . penci_livesub_text( 'event_live' ) . '"></span>';
				}
			}
		}

		return $text;
	},
	10,
	4
);


add_action(
	'soledad_theme/custom_css',
	function () {
		$mods = array(
			'pencilb_date_color'           => '.pcliveblog-listing-item .pcliveblog-date{color:{{VALUE}}}',
			'pencilb_title_color'          => '.pcliveblog-listing-item .pcliveblog-title{color:{{VALUE}}}',
			'pencilb_content_color'        => '.pcliveblog-listing-item .pcliveblog-content,.pcliveblog-listing-item .pcliveblog-content p{color:{{VALUE}}}',
			'pencilb_content_link_color'   => '.pcliveblog-listing-item .pcliveblog-content a{color:{{VALUE}}}',
			'pencilb_content_link_hcolor'  => '.pcliveblog-listing-item .pcliveblog-content a:hover{color:{{VALUE}}}',
			'pencilb_share_color'          => '.pcliveblog-item-share .pclb-sitem, .post-entry .pcliveblog-item-share .pclb-sitem{color:{{VALUE}}}',
			'pencilb_share_hcolor'         => '.pcliveblog-item-share .pclb-sitem:hover, .post-entry .pcliveblog-item-share .pclb-sitem:hover{color:{{VALUE}}}',
			'pencilb_event_bcolor'         => '.pcliveblog-wrapper .pcliveblog-listing-item{border-color:{{VALUE}}}',
			'pencilb_top_status_bgcolor'   => '.pcliveblog-wrapper .pcliveblog-wrapper-status{background-color:{{VALUE}}}',
			'pencilb_top_status_txtcolor'  => '.pcliveblog-wrapper .pcliveblog-wrapper-status,.post-entry .pcliveblog-wrapper .pcliveblog-wrapper-status a, .pcliveblog-wrapper .pcliveblog-wrapper-status a{color:{{VALUE}}}',
			'penci_liveblog_date_fsize'    => '.pcliveblog-listing-item .pcliveblog-date{font-size:{{VALUE}}px}',
			'penci_liveblog_title_fsize'   => '.pcliveblog-listing-item .pcliveblog-title{font-size:{{VALUE}}px}',
			'penci_liveblog_content_fsize' => '.pcliveblog-listing-item .pcliveblog-content,.pcliveblog-listing-item .pcliveblog-content p{font-size:{{VALUE}}px}',
			'penci_liveblog_share_fsize'   => '.pcliveblog-listing-item .pcliveblog-item-share .pclb-sitem{font-size:{{VALUE}}px}',
		);

		$out = '';

		foreach ( $mods as $mod => $prop ) {
			$value        = get_theme_mod( $mod );
			$mobile_value = '';

			if ( strpos( $mod, 'fsize' ) !== false ) {
				$mobile_value = get_theme_mod( str_replace( 'fsize', 'mfsize', $mod ) );
			}
			if ( $value ) {
				$out .= str_replace( '{{VALUE}}', $value, $prop );
			}
			if ( $mobile_value ) {
				$out .= '@media only screen and (max-width: 767px){' . str_replace( '{{VALUE}}', $mobile_value, $prop ) . '}';
			}
		}

		echo $out;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-liveblog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);
