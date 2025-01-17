<?php

namespace PenciPaywall;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class Init {
	/**
	 * @var Init
	 */
	private static $instance;

	/**
	 * @return Init
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
		$this->setup_init();
		$this->setup_hook();
		$this->register_gateway();
		PaywallAjaxHandle::instance();
		add_action( 'init', array( $this, 'update_user_status' ) );
		add_action( 'plugins_loaded', array( $this, 'load_woocommerce_class' ) );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
		add_action( 'elementor/widgets/register', [ $this, 'elementor_widget' ] );
		add_action( 'wp_footer', [ $this, 'footer_login_form' ] );
		add_action( 'soledad_theme/custom_css', [ $this, 'custom_css' ] );
		add_action( 'admin_notices', [ $this, 'paywall_notice' ] );
		add_action( 'init', [ $this, 'remove_ads' ] );
		add_action( 'init', [ $this, 'hide_ads_unit' ] );
		add_action( 'category_add_form_fields', array( $this, 'add_fields' ), 10, 2 );
		add_action( 'category_edit_form', array( $this, 'add_fields' ), 10, 2 );
		add_action( 'create_category', array( $this, 'save_fields' ) );
		add_action( 'edited_category', array( $this, 'save_fields' ) );
	}

	public function add_fields( $tag ) {
		$guest_mode = '';

		if ( isset( $tag->term_id ) ) {
			$term_id    = $tag->term_id;
			$guest_mode = get_term_meta( $term_id, 'penci_guest_mode', true );
		}
		?>
        <div id="poststuff" style="min-width: 300px;">
            <div id="postimagediv" class="postbox">
                <h2 class="hndle ui-sortable-handle">
                    <span><?php esc_html_e( 'Penci Paywall Settings', 'soledad' ); ?></span></h2>
                <div class="inside">
                    <div class="penci-tax-meta-fields">
                        <div class="penci-tab-content-widget">
                            <div id="general" class="tab-content" style="display: block">
                                <p class="penci-field-item ">
                                    <select name="penci_guest_mode" id="penci_guest_mode">
                                        <option
											<?php echo selected( $guest_mode, '' ); ?>value=""><?php esc_html_e( 'Follow Customizer Setting', 'soledad' ); ?></option>
                                        <option
											<?php echo selected( $guest_mode, 'enable' ); ?>value="enable"><?php esc_html_e( 'Enable Guest Mode for This Category', 'soledad' ); ?></option>
                                        <option
											<?php echo selected( $guest_mode, 'disable' ); ?>value="disable"><?php esc_html_e( 'Disable Guest Mode for This Category', 'soledad' ); ?></option>
                                    </select>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function save_fields( $term_id ) {
		$guest_mode = isset( $_POST['penci_guest_mode'] ) ? $_POST['penci_guest_mode'] : 0;
		update_term_meta( $term_id, 'penci_guest_mode', $guest_mode );
	}

	public function remove_ads() {
		$subscribe_status = get_user_option( 'pencipw_subscribe_status', get_current_user_id() );
		$expired          = get_user_option( 'pencipw_expired_date', get_current_user_id() ) ? get_user_option( 'pencipw_expired_date', get_current_user_id() ) : Date( 'F d, Y' );
		$current_date     = new DateTime();
		$expired_date     = new DateTime( $expired );
		$hide_ads         = get_theme_mod( 'pencipw_subscribe_ads' );

		if ( $hide_ads && $subscribe_status && 'ACTIVE' === $subscribe_status && $current_date <= $expired_date ) {
			add_filter( 'penci_show_ads', '__return_false' );
		}
	}

	public function hide_ads_unit() {
		$show_ads = apply_filters( 'penci_show_ads', true );

		if ( ! $show_ads ) {
			$options = [
				'penci_header_3_adsense',
				'penci_infeedads_home_code',
				'penci_home_adsense_below_slider',
				'penci_arcf_adbelow',
				'penci_archive_ad_above',
				'penci_archive_ad_below',
				'penci_infeedads_archi_code',
				'penci_ads_inside_content_html',
				'penci_post_adsense_single10',
				'penci_post_adsense_one',
				'penci_post_adsense_two',
				'penci_loadnp_ads',
				'penci_floatads_enable',
				'penci_footer_adsense'
			];
			foreach ( $options as $option ) {
				add_filter( 'theme_mod_' . $option, '__return_false' );
			}
		}
	}

	public function paywall_notice() {
		if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'getpaid' ) ):
			?>
            <div class="notice notice-error">
                <p><?php _e( '<strong>Penci Paywall</strong> required <strong>GetPaid</strong> or <strong>WooCommerce</strong> plugin to add the payment gateway. Please install these plugins <a href="' . esc_url( admin_url( 'themes.php?page=tgmpa-install-plugins#recommended-plugins' ) ) . '">here</a>.', 'penci-paywall' ); ?></p>
            </div>
		<?php
		endif;
	}

	public function footer_login_form() {
		if ( ! function_exists( 'penci_soledad_login_register_popup' ) ) {
			return false;
		}
		if ( ! get_theme_mod( 'penci_tblogin' ) ) {
			penci_soledad_login_register_popup();
		}

		if ( function_exists( 'is_penci_amp' ) && is_penci_amp() ) {
			return;
		}

		/**
		 * Unlock Popup
		 */
		$unlock_remaining = get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) ? get_user_option( 'pencipw_unlock_remaining', get_current_user_id() ) : 0;
		$unlock_popup     = '<div id=\'pencipw_unlock_popup\' class=\'pencipw_popup mfp-with-anim mfp-hide\'>
                        <div class=\'pencipw_popup\'>
                            <h5>' . pencipw_text_translation( 'unclock_confirm' ) . '</h5>
                            <span>' . pencipw_text_translation( 'unclock_left' ) . ' : ' . $unlock_remaining . '</span>
                            <button type=\'button\' class=\'btn yes\'><span>' . pencipw_text_translation( 'yes' ) . '</span><i class="fa fa-spinner fa-pulse" style="display: none;"></i></button>
                            <button type=\'button\' class=\'btn no\'>' . pencipw_text_translation( 'no' ) . '</button>
                        </div>
                    </div>';

		echo $unlock_popup;

		/**
		 * Cancel Subs Popup
		 */
		$cancel_subs = '<div id=\'pencipw_cancel_subs_popup\' class=\'pencipw_popup mfp-with-anim mfp-hide\'>
                        <div class=\'pencipw_popup\'>
                            <h5>' . pencipw_text_translation( 'cancal_confirm' ) . '</h5>
                            <button type=\'button\' class=\'btn yes\'><span>' . pencipw_text_translation( 'yes' ) . '</span><i class="fa fa-spinner fa-pulse" style="display: none;"></i></button>
                            <button type=\'button\' class=\'btn no\'>' . pencipw_text_translation( 'no' ) . '</button>
                        </div>
                    </div>';

		echo $cancel_subs;

	}

	public function admin_assets() {
		wp_enqueue_style( 'penci-admin-paywall', PENCI_PAYWALL_URL . 'assets/admin.css', null, PENCI_PAYWALL );
	}

	public function frontend_assets() {
		wp_enqueue_style( 'penci-paywall', plugin_dir_url( __DIR__ ) . 'assets/style.css', '', PENCI_PAYWALL );
		wp_enqueue_script( 'penci-paywall', PENCI_PAYWALL_URL . 'assets/frontend.js', null, PENCI_PAYWALL, true );
		wp_register_script( 'penci-gp-paywall', PENCI_PAYWALL_URL . 'assets/frontend-gp.js', null, PENCI_PAYWALL, true );
		wp_localize_script( 'penci-paywall', 'pencipw_var', [
			'site_slug'      => '',
			'site_domain'    => '',
			'login_reload'   => '',
			'penci_ajax_url' => admin_url( 'admin-ajax.php' ),
		] );
	}

	private function setup_init() {
		ContentFilter::instance();
	}

	public function elementor_widget( $widgets_manager ) {
		require_once( plugin_dir_path( __DIR__ ) . 'builder/elementor.php' );
		$widgets_manager->register( new \PenciPayWallElementor() );
	}

	private function setup_hook() {

	}

	private function register_gateway() {
		include_once PENCI_PAYWALL_PATH . 'payments/autoload.php';
		include_once PENCI_PAYWALL_PATH . 'payments/init.php';
		PenciPW_Gateways::instance();
	}

	/**
	 * Load Penci Paywall Woocommerce Classes
	 */
	public function load_woocommerce_class() {
		if ( class_exists( 'WC_Product' ) ) {
			require_once plugin_dir_path( __DIR__ ) . 'woocommerce/class-product.php';
			require_once plugin_dir_path( __DIR__ ) . 'woocommerce/class-wc-product.php';
			require_once plugin_dir_path( __DIR__ ) . 'woocommerce/class-wc-product-unclock.php';
			require_once plugin_dir_path( __DIR__ ) . 'woocommerce/class-wc-order.php';

			Woocommerce\Product::instance();
			Woocommerce\Order::instance();
		}
	}

	/**
	 * Update User status
	 *
	 * @throws Exception
	 */
	public function update_user_status() {
		// New Check for Expired.
		$subscribe_status = get_user_option( 'penci_subscribe_status', get_current_user_id() );
		$expired          = get_user_option( 'penci_expired_date', get_current_user_id() ) ? get_user_option( 'penci_expired_date', get_current_user_id() ) : Date( 'F d, Y' );
		if ( ! empty( $subscribe_status ) && $subscribe_status && 'ACTIVE' === $subscribe_status ) {
			$current_date = new DateTime();
			$expired_date = new DateTime( $expired );
			$current_date->setTimezone( new DateTimeZone( 'UTC' ) );
			$expired_date->setTimezone( new DateTimeZone( 'UTC' ) );
			$expired_date->add( new DateInterval( 'PT1H' ) ); // We need to wait for recurring payment.
			if ( $current_date >= $expired_date ) {
				update_user_option( get_current_user_id(), 'penci_subscribe_status', false );
				update_user_option( get_current_user_id(), 'penci_expired_date', false );

				/** WCS Integration */
				if ( function_exists( 'wcs_get_subscription' ) ) {
					update_user_option( get_current_user_id(), 'penci_subscribe_id', false );
				}
			}
		}
	}

	public function custom_css() {
		$options = [
			'pencipw_premium_heading_text'      => [ 'span.pc-premium-post:before' => 'content:"{{VALUE}}"' ],
			'pencipw_premium_heading_text_cl'   => [ 'span.pc-premium-post' => 'color:{{VALUE}}' ],
			'pencipw_premium_heading_text_bgcl' => [ 'span.pc-premium-post.btn' => 'background-color:{{VALUE}}' ],
		];
		$css     = '';
		foreach ( $options as $option => $attr ) {
			$data = get_theme_mod( $option );
			if ( $data ) {
				foreach ( $attr as $selector => $value ) {
					$value = str_replace( '{{VALUE}}', $data, $value );
					$css   .= $selector . '{' . $value . '}';
				}
			}
		}
		echo $css;
	}
}