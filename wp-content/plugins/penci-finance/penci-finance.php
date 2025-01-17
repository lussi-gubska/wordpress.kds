<?php
/*
Plugin Name: Penci Finance
Plugin URI: https://pencidesign.net/
Description: Display finance stock or cryptocurrency data on the Soledad WordPress Theme.
Version: 1.2
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-frontend-submission
*/
define( 'PENCI_FINANCE_VERSION', '1.1' );
define( 'PENCI_FINANCE_URL', plugin_dir_url( __FILE__ ) );

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require plugin_dir_path( __FILE__ ) . 'inc/stock_api.php';
require plugin_dir_path( __FILE__ ) . 'inc/crypto_api.php';
require plugin_dir_path( __FILE__ ) . 'widgets/stock.php';
require plugin_dir_path( __FILE__ ) . 'widgets/crypto.php';

class Penci_Finance {

	private static $instance;

	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init() {
		if ( ! self::is_soledad() ) {
			wp_admin_notice( __( 'Penci Finance only working with the Soledad theme.', 'penci-finance' ), [ 'type' => 'error' ] );
			return;
		}
		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
		add_action( 'wp_ajax_penci_get_stock_by_query', [ $this, 'penci_get_stock_by_query' ] );
		add_action( 'wp_ajax_nopriv_penci_get_stock_by_query', [ $this, 'penci_get_stock_by_query' ] );
		add_action( 'wp_ajax_penci_get_stock_title_by_id', [ $this, 'penci_get_stock_title_by_id' ] );
		add_action( 'wp_ajax_nopriv_penci_get_stock_title_by_id', [ $this, 'penci_get_stock_title_by_id' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'penci_load_scripts' ] );
	}

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public static function is_soledad() {
		$theme = wp_get_theme();

		if ( is_child_theme() ) {
			$parent_theme = wp_get_theme( $theme->template );
			$name         = $parent_theme->get( 'Name' );
		} else {
			$name = $theme->get( 'Name' );
		}

		return $name == 'soledad';
	}

	public static function penci_load_scripts() {
		wp_enqueue_style( 'datatable', PENCI_FINANCE_URL . 'assets/datatables.css', array(), PENCI_FINANCE_VERSION );
		wp_enqueue_style( 'penci-finance', PENCI_FINANCE_URL . 'assets/penci-finance.css', array(), PENCI_FINANCE_VERSION );
		wp_enqueue_script( 'datatable', PENCI_FINANCE_URL . 'assets/datatables.min.js', array( 'jquery' ), PENCI_FINANCE_VERSION, true );
		wp_enqueue_script( 'chart.js', PENCI_FINANCE_URL . 'assets/chart.umd.min.js', array( 'jquery' ), PENCI_FINANCE_VERSION, true );
		wp_enqueue_script( 'marquee.js', PENCI_FINANCE_URL . 'assets/jquery.marquee.min.js', array( 'jquery' ), PENCI_FINANCE_VERSION, true );
		wp_enqueue_script( 'penci-finance', PENCI_FINANCE_URL . 'assets/penci-finance.js', array( 'jquery' ), PENCI_FINANCE_VERSION, true );
	}

	public static function penci_get_stock_by_query() {
		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore
		$results       = array();

		$searchs = Penci_Finance_Stock::search( $search_string );

		$cache_options = get_transient( 'penci_finance_stock_data' );


		foreach ( $searchs as $result ) {
			// Get the symbol for each result
			$symbol = $result->getSymbol();
			$text   = $result->getName() . ' - ' . $result->getExchDisp();

			$results[] = array(
				'id'   => $symbol,
				'text' => $text,
			);
		}

		if ( empty( $cache_options ) ) {
			$cache_data = $results;
		} else {
			$cache_data = array_merge( $cache_options, $results );
		}

		set_transient( 'penci_finance_stock_data', $cache_data );

		wp_send_json( $results );
	}

	public static function penci_get_stock_title_by_id() {
		$ids     = isset( $_POST['id'] ) ? $_POST['id'] : array(); // phpcs:ignore
		$results = array();

		$searchs = get_transient( 'penci_finance_stock_data' );

		if ( empty ( $searchs ) ) {

			$searchs = Penci_Finance_Stock::getQuotes( $ids );

			foreach ( $searchs as $search ) {
				$results[ $search->getSymbol() ] = $search->getLongName() . ' - ' . $search->getFullExchangeName();
			}

		} else {
			foreach ( $searchs as $index => $data ) {
				$results[ $data['id'] ] = $data['text'];
			}
		}

		wp_send_json( $results );
	}

	public function register_widget( $widgets_manager ) {

		$_elements = [
			'stock',
			'crypto',
		];

		foreach ( $_elements as $aelement ) {
			require_once( __DIR__ . "/builder/{$aelement}.php" );
			$classname = '\\PenciFinanceElementor' . ucwords( $aelement );
			$widgets_manager->register( new $classname() );
		}

	}

	public static function number_format( $number ) {
		if ( $number <= 1 ) {
			$format = number_format( $number, 3 );
		} elseif ( $number < 10 ) {
			$format = number_format( $number, 2 );
		} else {
			$format = number_format( $number );
		}

		return $format;
	}

	public static function convert_symbol( $c ) {
		$symbols = [
			"btc"  => "₿",     // Bitcoin symbol
			"eth"  => "Ξ",     // Ethereum symbol
			"ltc"  => "Ł",     // Litecoin symbol
			"bch"  => "₿",     // Bitcoin Cash (same as Bitcoin)
			"bnb"  => "B",     // Binance Coin (not an official symbol, but often used)
			"eos"  => "EOS",   // EOS symbol (no special character)
			"xrp"  => "XRP",   // Ripple symbol (no special character)
			"xlm"  => "*",     // Stellar symbol (sometimes represented by an asterisk)
			"link" => "LINK", // Chainlink (no special character)
			"dot"  => "DOT",   // Polkadot (no special character)
			"yfi"  => "YFI",   // Yearn Finance (no special character)
			"usd"  => "$",     // US Dollar
			"aed"  => "د.إ",   // UAE Dirham
			"ars"  => "$",     // Argentine Peso
			"aud"  => "A$",    // Australian Dollar
			"bdt"  => "৳",     // Bangladeshi Taka
			"bhd"  => ".د.ب",  // Bahraini Dinar
			"bmd"  => "$",     // Bermudian Dollar
			"brl"  => "R$",    // Brazilian Real
			"cad"  => "C$",    // Canadian Dollar
			"chf"  => "CHF",   // Swiss Franc
			"clp"  => "$",     // Chilean Peso
			"cny"  => "¥",     // Chinese Yuan
			"czk"  => "Kč",    // Czech Koruna
			"dkk"  => "kr",    // Danish Krone
			"eur"  => "€",     // Euro
			"gbp"  => "£",     // British Pound
			"gel"  => "₾",     // Georgian Lari
			"hkd"  => "HK$",   // Hong Kong Dollar
			"huf"  => "Ft",    // Hungarian Forint
			"idr"  => "Rp",    // Indonesian Rupiah
			"ils"  => "₪",     // Israeli New Shekel
			"inr"  => "₹",     // Indian Rupee
			"jpy"  => "¥",     // Japanese Yen
			"krw"  => "₩",     // South Korean Won
			"kwd"  => "د.ك",   // Kuwaiti Dinar
			"lkr"  => "Rs",    // Sri Lankan Rupee
			"mmk"  => "K",     // Myanmar Kyat
			"mxn"  => "$",     // Mexican Peso
			"myr"  => "RM",    // Malaysian Ringgit
			"ngn"  => "₦",     // Nigerian Naira
			"nok"  => "kr",    // Norwegian Krone
			"nzd"  => "NZ$",   // New Zealand Dollar
			"php"  => "₱",     // Philippine Peso
			"pkr"  => "₨",     // Pakistani Rupee
			"pln"  => "zł",    // Polish Zloty
			"rub"  => "₽",     // Russian Ruble
			"sar"  => "﷼",     // Saudi Riyal
			"sek"  => "kr",    // Swedish Krona
			"sgd"  => "S$",    // Singapore Dollar
			"thb"  => "฿",     // Thai Baht
			"try"  => "₺",     // Turkish Lira
			"twd"  => "NT$",   // New Taiwan Dollar
			"uah"  => "₴",     // Ukrainian Hryvnia
			"vef"  => "Bs.",   // Venezuelan Bolívar
			"vnd"  => "₫",     // Vietnamese Dong
			"zar"  => "R",     // South African Rand
			"xdr"  => "XDR",   // IMF Special Drawing Rights
			"xag"  => "XAG",   // Silver (Troy Ounce)
			"xau"  => "XAU",   // Gold (Troy Ounce)
			"bits" => "μBTC", // Bits
			"sats" => "sats"  // Satoshis
		];

		return isset( $symbols[ $c ] ) ? $symbols[ $c ] : $c;
	}
}

Penci_Finance::getInstance();