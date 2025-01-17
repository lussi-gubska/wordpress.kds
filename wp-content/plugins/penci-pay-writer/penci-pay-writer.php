<?php
/*
	Plugin Name: Penci Pay Writer
	Plugin URI: https://pencidesign.net/
	Description: Provide payment for authors and help readers can donation for the post they made.
	Version: 1.4
	Author: PenciDesign
	Author URI: https://pencidesign.net/
	License: GPL2
	Text Domain: penci-pay-writer
*/

defined( 'PENCI_PAY_WRITER' ) || define( 'PENCI_PAY_WRITER', '1.4' );
defined( 'PENCI_PAY_WRITER_DIR' ) || define( 'PENCI_PAY_WRITER_DIR', plugin_dir_path( __DIR__ ) );
defined( 'PENCI_PAY_WRITER_URL' ) || define( 'PENCI_PAY_WRITER_URL', plugin_dir_url( __FILE__ ) );
defined( 'PENCI_PAY_WRITER_FILE' ) || define( 'PENCI_PAY_WRITER_FILE', plugin_dir_path( __FILE__ ) );
defined( 'PENCI_PAY_WRITER_CLASSPATH' ) || define( 'PENCI_PAY_WRITER_CLASSPATH', PENCI_PAY_WRITER_FILE . 'admin/' );
defined( 'PENCI_PAY_WRITER_DB_DATA' ) or define( 'PENCI_PAY_WRITER_DB_DATA', 'pencipwt_paymentsdata' );
defined( 'PENCI_PAY_WRITER_DB_SUMMARY' ) or define( 'PENCI_PAY_WRITER_DB_SUMMARY', 'pencipwt_paymentssummary' );

if ( ! function_exists( 'pencipwt_get_setting' ) ) {
	function pencipwt_get_setting( $name ) {
		$default_settings = array(
			'general' => array(
				'userid'                                   => 'general',
				'basic_payment'                            => 1,
				'basic_payment_value'                      => get_theme_mod( 'penci_paywriter_payment_standard_amount', 1.5 ),
				'basic_payment_display_status'             => 'tooltip',
				'counting_words'                           => get_theme_mod( 'penci_paywriter_counting_words', true ),
				'counting_words_system_zonal'              => 0,
				'counting_words_system_zonal_value'        => array(
					0 => array(
						'threshold' => 100,
						'payment'   => 1,
					),
					1 => array(
						'threshold' => 200,
						'payment'   => 2,
					),
				),
				'counting_words_system_incremental'        => 1,
				'counting_words_system_incremental_value'  => get_theme_mod( 'penci_paywriter_payment_payment_word_rate', '0.01' ),
				'counting_words_threshold_max'             => 0,
				'counting_words_display_status'            => 'count',
				'counting_words_legacy'                    => get_theme_mod( 'penci_paywriter_counting_words_legacy' ),
				'counting_words_parse_spaces'              => 0,
				'counting_words_include_excerpt'           => 0,
				'counting_words_exclude_pre'               => 0,
				'counting_words_exclude_captions'          => 0,
				'counting_words_apply_shortcodes'          => 0,
				'counting_words_global_threshold'          => 0,
				'counting_visits'                          => get_theme_mod( 'penci_paywriter_payment_payment_view_enable', true ),
				'counting_visits_postmeta'                 => 1,
				'counting_visits_postmeta_value'           => 'penci_post_views_count',
				'counting_visits_callback'                 => 0,
				'counting_visits_callback_value'           => '',
				'counting_visits_system_zonal'             => 0,
				'counting_visits_system_zonal_value'       => array(
					0 => array(
						'threshold' => 100,
						'payment'   => 1,
					),
					1 => array(
						'threshold' => 200,
						'payment'   => 2,
					),
				),
				'counting_visits_system_incremental'       => 1,
				'counting_visits_system_incremental_value' => get_theme_mod( 'penci_paywriter_payment_payment_view_rate' ),
				'counting_visits_threshold_max'            => 0,
				'counting_visits_global_threshold'         => 0,
				'counting_visits_display_percentage'       => 100,
				'counting_visits_display_status'           => 'count',
				'counting_images'                          => get_theme_mod( 'penci_paywriter_img_payment_view_enable', false ),
				'counting_images_system_zonal'             => 0,
				'counting_images_system_zonal_value'       => array(
					0 => array(
						'threshold' => 100,
						'payment'   => 1,
					),
					1 => array(
						'threshold' => 200,
						'payment'   => 2,
					),
				),
				'counting_images_system_incremental'       => 1,
				'counting_images_system_incremental_value' => get_theme_mod( 'penci_paywriter_img_payment_view_rate', 0.2 ),
				'counting_images_threshold_min'            => get_theme_mod( 'penci_paywriter_img_min_rate', 2 ),
				'counting_images_threshold_max'            => 10,
				'counting_images_include_featured'         => 1,
				'counting_images_include_galleries'        => 1,
				'counting_images_global_threshold'         => 0,
				'counting_images_display_status'           => 'count',
				'counting_comments'                        => 0,
				'counting_comments_system_zonal'           => 0,
				'counting_comments_system_zonal_value'     => array(
					0 => array(
						'threshold' => 100,
						'payment'   => 1,
					),
					1 => array(
						'threshold' => 200,
						'payment'   => 2,
					),
				),
				'counting_comments_system_incremental'     => 1,
				'counting_comments_system_incremental_value' => 0.2,
				'counting_comments_threshold_min'          => 2,
				'counting_comments_threshold_max'          => 10,
				'counting_comments_global_threshold'       => 0,
				'counting_comments_display_status'         => 'count',
				'counting_payment_total_threshold'         => get_theme_mod( 'penci_paywriter_payment_max_amount', 0 ),
				'counting_payment_only_when_total_threshold' => 0,
				'counting_allowed_post_statuses'           => array(
					'publish' => 1,
					'future'  => 1,
					'pending' => 0,
					'private' => 0,
				),
				'counting_exclude_quotations'              => 1,
				'can_see_others_general_stats'             => 1,
				'can_see_others_detailed_stats'            => 1,
				'can_see_countings_special_settings'       => 1,
				'enable_post_stats_caching'                => 1,
				'display_overall_stats'                    => 1,
				'can_see_options_user_roles'               => array(
					'administrator' => 'administrator',
				),
				'can_see_stats_user_roles'                 => array(
					'administrator' => 'administrator',
					'editor'        => 'editor',
					'author'        => 'author',
					'contributor'   => 'contributor',
				),
				'counting_allowed_user_roles'              => array(
					'administrator' => 'administrator',
					'editor'        => 'editor',
					'author'        => 'author',
					'contributor'   => 'contributor',
				),
				'counting_allowed_post_types'              => array(
					'post',
				),
				'default_stats_time_range_month'           => 1,
				'default_stats_time_range_last_month'      => 0,
				'default_stats_time_range_this_year'       => 0,
				'default_stats_time_range_week'            => 0,
				'default_stats_time_range_all_time'        => 0,
				'default_stats_time_range_custom'          => 0,
				'default_stats_time_range_custom_value'    => 100,
				'default_stats_time_range_start_day'       => 0,
				'default_stats_time_range_start_day_value' => '1605-11-05',
				'admins_override_permissions'              => 1,
				'stats_display_edit_post_link'             => 0,
				'enable_stats_payments_tooltips'           => 1,
				'payment_display_round_digits'             => 2,
				'save_stats_order'                         => 1,
				'hide_column_total_payment'                => 0,
				'stats_show_all_users'                     => 0,
				'paypal_sandbox'                           => 0,
				'paypal_currency_code'                     => get_theme_mod( 'penci_paywriter_payment_currency', 'USD' ),
				'paypal_fees_sender'                       => 1,
				'paypal_fees_receivers'                    => 0,
				'payment_notification_mark_as_paid'        => 0,
				'payment_notification_paypal'              => 0,
				'enable_payment_bonus'                     => 0,
				'paypal_ipn'                               => 1,
				'paypal_use_users_email'                   => 0,
				'paypal_display_payment_history_status'    => 1,
				'currency_symbol'                          => get_theme_mod( 'penci_paywriter_cur_character', '$' ),
				'currency_symbol_before'                   => 'before' == get_theme_mod( 'penci_paywriter_cur_character_pos', 'after' ),
				'currency_symbol_after'                    => 'after' == get_theme_mod( 'penci_paywriter_cur_character_pos', 'after' ),
				'can_mark_as_paid'                         => 1,
				'can_see_paypal_functions'                 => 1,
			),
		);

		return isset( $default_settings['general'][ $name ] ) ? $default_settings['general'][ $name ] : false;
	}
}

require 'dashboard/dashboard.php';
require 'inc/init.php';
require 'inc/metabox.php';

if ( defined( 'WPB_VC_VERSION' ) ) {
	require_once 'elements/jscomposer.php';
}

\PenciPayWriter\Init::instance();


function Penci_Pay_Writer() {
	static $instance;

	// first call to instance() initializes the plugin
	if ( null === $instance || ! ( $instance instanceof \PenciPayWriter\Init ) ) {
		$instance = \PenciPayWriter\Init::instance();

	}

	return $instance;
}

add_action(
	'init',
	function () {
		if ( class_exists( 'SoledadFW\Customizer\CustomizerOptionAbstract' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'customizer/panel.php';
			require_once plugin_dir_path( __FILE__ ) . 'customizer/settings.php';
			\SoledadFW\PenciPayWriterCustomizer::getInstance();
		}
	}
);

add_action(
	'penci_get_options_data',
	function ( $options ) {

		$options['penci_pay_writer_panel'] = array(
			'priority'                         => 30,
			'path'                             => plugin_dir_path( __FILE__ ) . '/customizer/',
			'panel'                            => array(
				'title' => esc_html__( 'Pay Writer', 'soledad' ),
				'icon'  => 'fas fa-user-edit',
			),
			'penci_pay_writer_general_section' => array( 'title' => esc_html__( 'Donation Settings', 'soledad' ) ),
			'penci_pay_writer_posts_section'   => array( 'title' => esc_html__( 'Pay Per Posts', 'soledad' ) ),
		);
		return $options;
	}
);

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'penci-pay-writer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);
