<?php

namespace PenciPayWriter;


class Init {
	/**
	 * @var Init
	 */
	private static $instance;

	private string $type;
	public array $settings;
	public bool $is_soledad;
	public $dashboard;
	public array $default_options;

	/**
	 * @return Init
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function __construct() {
		add_filter( 'the_content', array( $this, 'show_widget_content' ), 10 );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'penci_single_meta_content', [ $this, 'show_meta_content' ] );
		add_action( 'elementor/widgets/register', [ $this, 'elementor_widget' ] );
		register_activation_hook( PENCI_PAY_WRITER_FILE, [ $this, 'activate' ] );

		add_shortcode( 'paywriter', [ $this, 'paywriter_shortcode' ] );

		add_filter( 'user_contactmethods', [ $this, 'add_paypal_option_field' ] );

		$this->type = get_theme_mod( 'penci_paywriter_element_type', 'button' );

		add_action( 'init', [ $this, 'check_paypal_ipn' ] );

		$this->settings = array(
			'current_page'                   => '',
			'option_stats_cache_incrementor' => 'pencipwt_stats_cache_incrementor',
			'temp'                           => array( 'settings' => array() ),
			'stats_menu_link'                => 'admin.php?page=pencipwt-post-stats',
		);

		$this->default_options['first_available_post_time']['exp']  = time();
		$this->default_options['last_available_post_time']['exp']   = time();
		$this->default_options['first_available_post_time']['time'] = current_time( 'timestamp' );
		$this->default_options['last_available_post_time']['time']  = current_time( 'timestamp' );

		$themes           = wp_get_theme();
		$this->is_soledad = ( ( $themes->parent() && $themes->parent()->get( 'TextDomain' ) === 'soledad' ) || $themes->get( 'TextDomain' ) === 'soledad' );
	}

	public function activate() {
		Penci_Pay_Writer()->database->create_table();
		Helper::update_general_option( 'version', PENCI_PAY_WRITER );
	}

	public function check_paypal_ipn() {
		if ( isset( $_REQUEST['pencipwt_paypal_ipn'] ) && $_REQUEST['pencipwt_paypal_ipn'] == "process" ) {
			self::pencipwt_validate_paypl_ipn();
			exit;
		}
	}

	public function should_render_button() {
		global $post;
		$render = false;

		if ( get_theme_mod( 'penci_paywriter_enable_all_post' ) ) {
			$render = true;
		}

		if ( get_post_meta( $post->ID, 'pencipwt_enable_post_donation', true ) == 'enable' ) {
			$render = true;
		}

		if ( get_post_meta( $post->ID, 'pencipwt_enable_post_donation', true ) == 'disable' ) {
			$render = false;
		}

		return $render;
	}

	public function pencipwt_validate_paypl_ipn() {

		$wpapp_ipn_validated = true;

		// Reading POSTed data directly from POST causes serialization issues with array data in the POST.
		// Instead, read raw POST data from the input stream.
		$raw_post_data  = file_get_contents( 'php://input' );
		$raw_post_array = explode( '&', $raw_post_data );
		$myPost         = array();
		foreach ( $raw_post_array as $keyval ) {
			$keyval = explode( '=', $keyval );
			if ( count( $keyval ) == 2 ) {
				$myPost[ $keyval[0] ] = urldecode( $keyval[1] );
			}
		}

		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		if ( function_exists( 'get_magic_quotes_gpc' ) ) {
			$get_magic_quotes_exists = true;
		}
		foreach ( $myPost as $key => $value ) {
			if ( $get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1 ) {
				$value = urlencode( stripslashes( $value ) );
			} else {
				$value = urlencode( $value );
			}
			$req .= "&$key=$value";
		}

		// Step 2: POST IPN data back to PayPal to validate
		$params = array(
			'body'        => $req,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'PayPal Donations Plugin/TTHQ'
		);

		$connection_url = 'https://www.paypal.com/cgi-bin/webscr';
		$response       = wp_safe_remote_post( $connection_url, $params );

		if ( ! is_wp_error( $response ) && strstr( $response['body'], 'VERIFIED' ) ) {
			// The IPN is verified, process it
			$wpapp_ipn_validated = true;
		} else {
			// IPN invalid, log for manual investigation
			$wpapp_ipn_validated = false;
		}

		if ( ! $wpapp_ipn_validated ) {
			// IPN validation failed. Email the admin to notify this event.
			$admin_email = get_bloginfo( 'admin_email' );
			$subject     = 'IPN validation failed for a payment';
			$body        = "This is a notification email from the WP Accept PayPal Payment plugin letting you know that a payment verification failed." .
			               "\n\nPlease check your paypal account to make sure you received the correct amount in your account before proceeding";
			wp_mail( $admin_email, $subject, $body );
			exit;
		}
	}

	public function register_assets() {
		wp_register_style( 'penci-pay-writer', plugin_dir_url( __DIR__ ) . 'assets/style.css', '', PENCI_PAY_WRITER );
		wp_register_script( 'penci-pay-writer', plugin_dir_url( __DIR__ ) . 'assets/pay-writer.js', [ 'jquery' ], PENCI_PAY_WRITER, true );
	}

	public function show_donation_content_button() {
		global $post;

		if ( ! self::should_render_button() ) {
			return false;
		}

		$button_text = do_shortcode( get_theme_mod( 'penci_paywriter_button_text', 'Donate' ) );

		wp_enqueue_style( 'penci-pay-writer' );
		wp_enqueue_script( 'penci-pay-writer' );

		$out = "<span class='pencipwt_meta_donation'>
				<a class='pencipwt-donation-submit' data-id='pencipwt_donation_form_{$post->ID}' href='#' aria-label='Paypal Donate' target='_blank'><i class='pencipwt-icon pencipwt-pay fa'></i> <span>{$button_text}</span></a>
			</span>";

		$out .= self::paypal_form();

		return $out;
	}

	public function show_donation_content_banner() {
		global $post;

		if ( ! self::should_render_button() ) {
			return false;
		}

		$button_text  = do_shortcode( get_theme_mod( 'penci_paywriter_button_text', 'Donate' ) );
		$widget_title = do_shortcode( get_theme_mod( 'penci_paywriter_widget_title', 'Donation for Author' ) );
		$widget_desc  = do_shortcode( get_theme_mod( 'penci_paywriter_widget_description', 'Buy author a coffee' ) );

		wp_enqueue_style( 'penci-pay-writer' );
		wp_enqueue_script( 'penci-pay-writer' );


		$out = '<div class="pencipwt-donation-widget">
				    <div class="pencipwt-donation-text-container">
				        <div class="pencipwt-donation-text-wrapper">
				            <h3 class="pencipwt-donation-title">' . esc_html( $widget_title ) . '</h3>
				            <p class="pencipwt-donation-description">' . esc_html( $widget_desc ) . '</p>
				        </div>
				    </div>
				    <div class="pencipwt-donation-form-container">
				        <div class="pencipwt-donation-form-wrapper">
							<a data-id="pencipwt_donation_form_' . esc_attr( $post->ID ) . '" href="#" aria-label="Paypal Donate" class="pencipwt-donation-submit" target="_blank"><span>' . $button_text . '</span></a>
				        </div>
				    </div>
				</div>';


		$out .= self::paypal_form();

		return $out;
	}

	public function show_widget_content( $content ) {
		if ( is_single() && ( 'both' == $this->type || 'widget' == $this->type ) ) {
			$content = $content . $this->show_donation_content_banner();
		}

		return $content;
	}

	public function show_meta_content() {
		if ( 'button' == $this->type || 'both' == $this->type ) {
			echo $this->show_donation_content_button();
		}
	}

	public function add_paypal_option_field() {
		$user_contact_method['paypal_account'] = __( 'Paypal Email Address' );

		return $user_contact_method;
	}

	public static function paypal_form( $custom = array() ) {
		global $post;

		$default_email = get_theme_mod( 'penci_paywriter_donation_custom_email' );
		$default_email = $default_email ? $default_email : get_user_meta( $post->post_author, 'paypal_account' );

		$default = [
			'currency'    => get_theme_mod( 'penci_paywriter_currency', 'USD' ),
			'description' => get_theme_mod( 'penci_paywriter_checkout_description', 'Buy author a coffee' ),
			'return'      => get_theme_mod( 'penci_paywriter_return_url' ),
			'cancel_url'  => get_theme_mod( 'penci_paywriter_cancel_url' ),
			'fix_amount'  => get_theme_mod( 'penci_paywriter_enable_fix_amount' ),
			'amount'      => get_theme_mod( 'penci_paywriter_fix_amount', '5.00' ),
			'email'       => $default_email,
			'form_id'     => 'pencipwt_donation_form_' . esc_attr( $post->ID ),
		];

		$default = wp_parse_args( $custom, $default );


		extract( $default );

		if ( is_array( $email ) ) {
			$email = isset( $email[0] ) ? $email[0] : $email;
		}

		if ( ! $return ) {
			$return = get_the_permalink( $post->ID );
		}

		if ( ! $cancel_url ) {
			$cancel_url = get_the_permalink( $post->ID );
		}

		$notify_url = site_url() . '/?pencipwt_paypal_ipn=process';
		$cancel_url = add_query_arg( [ 'pencipwt_donation_cancel' => 'yes' ], $cancel_url );
		$return     = add_query_arg( [ 'pencipwt_success' => 'yes' ], $return );

		$output = '<form name="_xclick" id="' . esc_attr( $form_id ) . '" class="pencipwt_donation_form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
		$output .= '<input type="hidden" name="cmd" value="_donations"/>';
		$output .= '<input type="hidden" name="bn" value="PenciPayWriter_Donate_WPS_US" />';
		$output .= '<input type="hidden" name="business" value="' . esc_attr( $email ) . '">';
		$output .= '<input type="hidden" name="currency_code" value="' . esc_attr( $currency ) . '">';
		$output .= '<input type="hidden" name="item_name" value="' . esc_attr( stripslashes( $description ) ) . '">';
		$output .= '<input type="hidden" name="return" value="' . esc_url( $return ) . '" />';
		$output .= '<input type="hidden" name="rm" value="0" />';
		$output .= '<input type="hidden" name="cancel_return" value="' . esc_url( $cancel_url ) . '" />';

		if ( $fix_amount ) {
			$output .= '<input type="hidden" name="amount" value="' . $amount . '" />';
		}

		$output .= '<input type="hidden" name="notify_url" value="' . esc_url( $notify_url ) . '" />';
		$output .= '</form>';

		return $output;
	}

	public function elementor_widget( $widgets_manager ) {
		require_once( plugin_dir_path( __DIR__ ) . 'elements/elementor.php' );
		$widgets_manager->register( new \PenciPayWriterElementor() );
	}

	public function paywriter_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'currency'    => get_theme_mod( 'penci_paywriter_currency', 'USD' ),
			'description' => get_theme_mod( 'penci_paywriter_checkout_description', 'Buy author a coffee' ),
			'return'      => get_theme_mod( 'penci_paywriter_return_url' ),
			'cancel_url'  => get_theme_mod( 'penci_paywriter_cancel_url' ),
			'fix_amount'  => get_theme_mod( 'penci_paywriter_enable_fix_amount' ),
			'amount'      => get_theme_mod( 'penci_paywriter_fix_amount', '5.00' ),
			'email'       => '',
			'button'      => 'Click to Donate',
			'form_id'     => 'pencipwt_donation_form_' . rand(),
		), $atts, 'paywriter' );

		$button_text = $atts['button'];

		wp_enqueue_script( 'penci-pay-writer' );
		wp_enqueue_style( 'penci-pay-writer' );

		$out = "<a class='pencipwt-donation-submit el' data-id='{$atts['form_id']}' href='#' aria-label='{$button_text}' target='_blank'><span>{$button_text}</span></a>";
		$out .= \PenciPayWriter\Init::paypal_form( [
			'form_id'     => $atts['form_id'],
			'currency'    => $atts['currency'],
			'description' => $atts['description'],
			'return'      => $atts['return'],
			'cancel_url'  => $atts['cancel_url'],
			'fix_amount'  => $atts['fix_amount'],
			'amount'      => $atts['amount'],
			'email'       => $atts['email'],
		] );

		return $out;
	}
}