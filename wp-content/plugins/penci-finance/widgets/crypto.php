<?php

add_action( 'widgets_init', 'penci_finance_crypto_widget' );

function penci_finance_crypto_widget() {
	register_widget( 'penci_finance_crypto_widget' );
}

if ( ! class_exists( 'penci_finance_crypto_widget' ) ) {
	class penci_finance_crypto_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */
		function __construct() {
			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'penci_finance_crypto_widget',
				'description' => esc_html__( 'A widget that displays the crypto data.', 'penci-finance' )
			);

			/* Widget control settings. */
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'penci_finance_crypto_widget' );

			parent::__construct( 'penci_finance_crypto_widget', self::penci_get_theme_name( '.Soledad', true ) . esc_html__( 'Cryptocurrency', 'penci-finance' ), $widget_ops, $control_ops );

		}

		function penci_get_theme_name( $name = 'Penci', $dot = false ) {

			$theme_name = get_theme_mod( 'admin_wel_page_sname' );
	
			if ( $theme_name && get_theme_mod( 'activate_white_label' ) ) {
				$name = $dot ? '.' . $theme_name : $theme_name;
			}
	
			return $name . ' ';
		}

		/**
		 * How to display the widget on the screen.
		 */
		function widget( $args, $instance ) {
			extract( $args );

			/* Our variables from the widget settings. */
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$title = apply_filters( 'widget_title', $title );


			/* Before widget (defined by themes). */
			echo ent2ncr( $before_widget );

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) {
				echo ent2ncr( $before_title ) . $title . ent2ncr( $after_title );
			}

			$settings = $instance;
			include plugin_dir_path( __DIR__ ) . 'templates/style-2.php';

			/* After widget (defined by themes). */
			echo ent2ncr( $after_widget );
		}

		/**
		 * Update the widget settings.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$data_instance = $this->soledad_widget_defaults();

			foreach ( $data_instance as $data => $value ) {
				$instance[ $data ] = ! empty( $new_instance[ $data ] ) ? $new_instance[ $data ] : '';
			}

			return $instance;
		}

		public function soledad_widget_defaults() {
			return array(
				'title'           => esc_html__( 'Cryptocurrency Data', 'penci-finance' ),
				'data_show'       => [ 'name', 'price' ],
				'ids'             => '',
				'layout'          => '',
				'order_by'        => '',
				'order'           => 'desc',
				'vs_currency'     => 'usd',
				'per_page'        => 5,
				'text_name'       => __( 'Name', 'penci-finance' ),
				'text_price'      => __( 'Price', 'penci-finance' ),
				'text_1h'         => __( '1h %', 'penci-finance' ),
				'text_24h'        => __( '24h %', 'penci-finance' ),
				'text_7d'         => __( '7d %', 'penci-finance' ),
				'text_market_cap' => __( 'Market Cap', 'penci-finance' ),
				'text_volume'     => __( 'Volume', 'penci-finance' ),
				'text_supply'     => __( 'Circulating Supply', 'penci-finance' ),
				'text_chart'      => __( '7 Days Chart', 'penci-finance' ),
			);
		}

		public function isupdown( $value ) {
			return $value < 0 ? 'down' : 'up';
		}

		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults       = $this->soledad_widget_defaults();
			$instance       = wp_parse_args( (array) $instance, $defaults );
			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';
			$data_show      = isset( $instance['data_show'] ) ? $instance['data_show'] : [];
			$layout         = isset( $instance['layout'] ) ? $instance['layout'] : 'style-1';
			$order_by       = isset( $instance['order_by'] ) ? $instance['order_by'] : 'market_cap';
			$order          = isset( $instance['order'] ) ? $instance['order'] : 'desc';
			$vs_currency    = isset( $instance['vs_currency'] ) ? $instance['vs_currency'] : 'usd';
			?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'penci-finance' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo esc_attr( $instance_title ); ?>" class="widefat" type="text"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout', 'penci-finance' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<?php
					$layout_op = array(
						'style-1' => __( 'Table - Style 1', 'penci-finance' ),
						'style-2' => __( 'Table - Style 2', 'penci-finance' ),
						'style-3' => __( 'Columns', 'penci-finance' ),
					);
					foreach ( $layout_op as $name => $label ) {
						echo '<option ' . selected( $name, $layout ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'per_page' ) ); ?>"><?php esc_html_e( 'Number of Items to Show', 'penci-finance' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'per_page' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'per_page' ) ); ?>"
                       value="<?php echo esc_attr( $instance['per_page'] ); ?>" class="widefat" type="text"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'ids' ) ); ?>"><?php esc_html_e( 'Custom Symbol IDs (Coin/Token Name)', 'penci-finance' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'ids' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'ids' ) ); ?>"
                       value="<?php echo esc_attr( $instance['ids'] ); ?>" class="widefat" type="text"/>
            <p style="font-style: italic;"><?php _e( 'Enter the list of symbols, separated by commas. Example: <span style="color:red">bitcoin,ethereum,tether,binancecoin</span>', 'penci-finance' ) ?></p>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"><?php esc_html_e( 'Showing Data', 'penci-finance' ) ?></label>
                <select multiple style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'data_show' ) ); ?>[]">
					<?php
					$post_meta = array(
						'order'      => __( '#', 'penci-finance' ),
						'name'       => __( 'Name', 'penci-finance' ),
						'price'      => __( 'Price', 'penci-finance' ),
						'1h'         => __( '1h %', 'penci-finance' ),
						'24h'        => __( '24h %', 'penci-finance' ),
						'7d'         => __( '7d %', 'penci-finance' ),
						'market_cap' => __( 'Market Cap', 'penci-finance' ),
						'volume'     => __( 'Volume(24h)', 'penci-finance' ),
						'supply'     => __( 'Circulating Supply', 'penci-finance' ),
						'chart'      => __( '7 Days Chart', 'penci-finance' ),
					);
					foreach ( $post_meta as $name => $label ) {
						$selected = in_array( $name, $data_show ) ? 'selected' : '';
						echo '<option ' . $selected . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'order_by' ) ); ?>"><?php esc_html_e( 'Order by', 'penci-finance' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'order_by' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'order_by' ) ); ?>">
					<?php
					$order_by_op = array(
						'market_cap' => __( 'Market Cap', 'penci-finance' ),
						'volume'     => __( 'Volume', 'penci-finance' ),
						'id'         => __( 'ID', 'penci-finance' ),
					);
					foreach ( $order_by_op as $name => $label ) {
						echo '<option ' . selected( $name, $order_by ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'penci-finance' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
					<?php
					$order_op = array(
						'desc' => __( 'Descending', 'penci-finance' ),
						'asc'  => __( 'Ascending', 'penci-finance' ),
					);
					foreach ( $order_op as $name => $label ) {
						echo '<option ' . selected( $name, $order ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'vs_currency' ) ); ?>"><?php esc_html_e( 'Currency', 'penci-finance' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'vs_currency' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'vs_currency' ) ); ?>">
					<?php
					$vs_currency_op = array(
						"btc"  => "Bitcoin",
						"eth"  => "Ethereum",
						"ltc"  => "Litecoin",
						"bch"  => "Bitcoin Cash",
						"bnb"  => "Binance Coin",
						"eos"  => "EOS",
						"xrp"  => "Ripple",
						"xlm"  => "Stellar",
						"link" => "Chainlink",
						"dot"  => "Polkadot",
						"yfi"  => "Yearn Finance",
						"usd"  => "US Dollar",
						"aed"  => "United Arab Emirates Dirham",
						"ars"  => "Argentine Peso",
						"aud"  => "Australian Dollar",
						"bdt"  => "Bangladeshi Taka",
						"bhd"  => "Bahraini Dinar",
						"bmd"  => "Bermudian Dollar",
						"brl"  => "Brazilian Real",
						"cad"  => "Canadian Dollar",
						"chf"  => "Swiss Franc",
						"clp"  => "Chilean Peso",
						"cny"  => "Chinese Yuan",
						"czk"  => "Czech Koruna",
						"dkk"  => "Danish Krone",
						"eur"  => "Euro",
						"gbp"  => "British Pound",
						"gel"  => "Georgian Lari",
						"hkd"  => "Hong Kong Dollar",
						"huf"  => "Hungarian Forint",
						"idr"  => "Indonesian Rupiah",
						"ils"  => "Israeli New Shekel",
						"inr"  => "Indian Rupee",
						"jpy"  => "Japanese Yen",
						"krw"  => "South Korean Won",
						"kwd"  => "Kuwaiti Dinar",
						"lkr"  => "Sri Lankan Rupee",
						"mmk"  => "Myanmar Kyat",
						"mxn"  => "Mexican Peso",
						"myr"  => "Malaysian Ringgit",
						"ngn"  => "Nigerian Naira",
						"nok"  => "Norwegian Krone",
						"nzd"  => "New Zealand Dollar",
						"php"  => "Philippine Peso",
						"pkr"  => "Pakistani Rupee",
						"pln"  => "Polish Zloty",
						"rub"  => "Russian Ruble",
						"sar"  => "Saudi Riyal",
						"sek"  => "Swedish Krona",
						"sgd"  => "Singapore Dollar",
						"thb"  => "Thai Baht",
						"try"  => "Turkish Lira",
						"twd"  => "New Taiwan Dollar",
						"uah"  => "Ukrainian Hryvnia",
						"vef"  => "Venezuelan Bolívar",
						"vnd"  => "Vietnamese Dong",
						"zar"  => "South African Rand",
						"xdr"  => "IMF Special Drawing Rights",
						"xag"  => "Silver (Troy Ounce)",
						"xau"  => "Gold (Troy Ounce)",
						"bits" => "Bits",
						"sats" => "Satoshis"
					);
					foreach ( $vs_currency_op as $name => $label ) {
						echo '<option ' . selected( $name, $vs_currency ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>

			<?php
			$text_translations = [
				'name'       => __( 'Name', 'penci-finance' ),
				'price'      => __( 'Price', 'penci-finance' ),
				'1h'         => __( '1h %', 'penci-finance' ),
				'24h'        => __( '24h %', 'penci-finance' ),
				'7d'         => __( '7d %', 'penci-finance' ),
				'market_cap' => __( 'Market Cap', 'penci-finance' ),
				'volume'     => __( 'Volume', 'penci-finance' ),
				'supply'     => __( 'Circulating Supply', 'penci-finance' ),
				'chart'      => __( '7 Days Chart', 'penci-finance' ),
			];

			foreach ( $text_translations as $id => $text ) {
				$field_id = 'text_' . $id;
				?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( $field_id ) ); ?>"><?php echo esc_html__( 'Text: ', 'penci-finance' ). $text; ?></label>
                    <input id="<?php echo esc_attr( $this->get_field_id( $field_id ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( $field_id ) ); ?>"
                           value="<?php echo esc_attr( $instance[ $field_id ] ); ?>" class="widefat" type="text"/>
                </p>
				<?php
			}
			?>


			<?php
		}
	}
}
?>
