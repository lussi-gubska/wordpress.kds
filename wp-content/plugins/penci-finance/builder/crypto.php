<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use PenciSoledadElementor\Base\Base_Widget;

class PenciFinanceElementorCrypto extends Base_Widget {

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'CryptoCurrency Data', 'penci-finance' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'finance', 'cryptocurrency', 'crypto' ];
	}

	public function get_script_depends() {
		return [ 'datatable' ];
	}

	public function get_name() {
		return 'penci-finance-elementor-crypto';
	}

	protected function register_controls() {

		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'layout',
			array(
				'label'       => __( 'Style', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'style-1',
				'label_block' => true,
				'options'     => [
					'style-1' => __( 'Table - Style 1', 'penci-finance' ),
					'style-2' => __( 'Table - Style 2', 'penci-finance' ),
					'style-3' => __( 'Grid', 'penci-finance' ),
					'style-4' => __( 'Sticker', 'penci-finance' ),
				],
			)
		);

		$this->add_responsive_control(
			'sticker_speed',
			array(
				'label'     => __( 'Sticker Speed', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => [ 'layout' => 'style-4' ],
			)
		);

		$this->add_responsive_control(
			'style3_col',
			array(
				'label'       => __( 'Number of Columns', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 5,
						'step' => 1,
					],
				],
				'condition'   => [ 'layout' => 'style-3' ],
				'selectors'   => [
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3' => '--col: {{SIZE}}',
				],
			)
		);

		$this->add_responsive_control(
			'style3_cgap',
			array(
				'label'       => __( 'Columns Gap', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'condition'   => [ 'layout' => 'style-3' ],
				'selectors'   => [
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3' => '--cgap: {{SIZE}}px;',
				],
			)
		);

		$this->add_responsive_control(
			'style3_rgap',
			array(
				'label'       => __( 'Rows Gap', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'condition'   => [ 'layout' => 'style-3' ],
				'selectors'   => [
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3' => '--rgap: {{SIZE}}px;',
				],
			)
		);

		$this->add_responsive_control(
			'imgw',
			array(
				'label'     => __( 'Logo Size', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array( '{{WRAPPER}} .pcfic-name img' => 'width:100%;max-width: {{SIZE}}px;' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'content_section', [
			'label' => esc_html__( 'General', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'data_show',
			array(
				'label'       => __( 'Meta Data', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => [
					'order',
					'name',
					'price',
					'1h',
					'24h',
					'7d',
					'market_cap',
					'volume',
					'supply',
					'chart'
				],
				'label_block' => true,
				'multiple'    => true,
				'options'     => [
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
				],
			)
		);

		$this->add_control(
			'ids',
			array(
				'label'        => __( 'Custom Symbol Name', 'penci-finance' ),
				'label_block'  => true,
				'description'  => __( 'Enter the custom name you want to show. Example: bitcoin,ethereum,tether,binancecoin', 'penci-finance' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'   => __( 'Number of symbols', 'penci-finance' ),
				'default' => 10,
				'type'    => \Elementor\Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'vs_currency',
			array(
				'label'   => __( 'Currency', 'penci-finance' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'usd',
				'options' => [
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
				]
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => __( 'Order by', 'penci-finance' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'market_cap',
				'options' => [
					'market_cap' => __( 'Market Cap', 'penci-finance' ),
					'volume'     => __( 'Volume', 'penci-finance' ),
					'id'         => __( 'ID', 'penci-finance' ),
				]
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'penci-finance' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __( 'Ascending', 'penci-finance' ),
					'desc' => __( 'Descending', 'penci-finance' ),
				]
			)
		);


		$this->end_controls_section();

		$this->start_controls_section( 'text_trans', [
			'label' => esc_html__( 'Text Translation', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

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
			$this->add_control(
				'text_' . $id,
				array(
					'label'   => __( 'Text:', 'penci-finance' ) . $text,
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => $text,
				)
			);
		}

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section_03', [
			'label'     => esc_html__( 'General Style', 'penci-finance' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout' => [ 'style-3', 'style-4' ] ],
		] );

		$this->add_control(
			'i3bg_color',
			array(
				'label'     => __( 'Background Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3 .penci-fncrypto-item .penci-fncrypto-content' => 'background: {{VALUE}};',
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4'                                              => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'i3bg_bdcolor',
			array(
				'label'     => __( 'Border Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3 .penci-fncrypto-item .penci-fncrypto-content' => 'border-color: {{VALUE}};--pcborder-cl: {{VALUE}};',
					'{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4'                                              => 'border-color: {{VALUE}};--pcborder-cl: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'i3_padding',
			array(
				'label'      => __( 'Padding', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3 .penci-fncrypto-item .penci-fncrypto-content, {{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'i3_borderw',
			array(
				'label'      => __( 'Border Width', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'i3_borderdu',
			array(
				'label'      => __( 'Border Radius', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'i3bds_enable',
			array(
				'label' => __( 'Enable Box Shadow', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'i3bds',
			array(
				'label'      => __( 'Box Shadow', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::BOX_SHADOW,
				'size_units' => array( 'px' ),
				'condition'  => array( 'i3bds_enable' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4, {{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-3 .penci-fncrypto-item .penci-fncrypto-content' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'typo_section_s3', [
			'label'     => esc_html__( 'Typography', 'penci-finance' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout' => [ 'style-3', 'style-4' ] ],
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'i3order_number',
			'label'    => __( 'Order Number', 'penci-finance' ),
			'selector' => '{{WRAPPER}} .penci-fncrypto-item .pcfic-order',
		) );

		$this->add_control(
			'i3item_ocolor',
			array(
				'label'     => __( 'Symbol Items Order Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-item .pcfic-order' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'i3syname_typo',
			'label'    => __( 'Symbol Name', 'penci-finance' ),
			'selector' => '{{WRAPPER}} .penci-fncrypto-item .pcfic-name',
		) );

		$this->add_control(
			'i3symbol_color',
			array(
				'label'     => __( 'Symbol Name Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-item .pcfic-name' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_control(
			'i3symbol_scolor',
			array(
				'label'     => __( 'Symbol Short Name Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-item .pcfic-name .symbol' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'i3syitem_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-di',
		) );

		$this->add_control(
			'i3item_color',
			array(
				'label'     => __( 'Symbol Items Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-di' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_control(
			'i3item_lcolor',
			array(
				'label'     => __( 'Symbol Items Lable Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-label' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_control(
			'i3item_ucolor',
			array(
				'label'     => __( 'Up Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4 .penci-fncrypto-di.up:before' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_control(
			'i3item_dcolor',
			array(
				'label'     => __( 'Down Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fncrypto-table-wrapper.pcct-style-4 .penci-fncrypto-di.down:before' => 'color:{{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'i3item_ispacing',
			array(
				'label'       => __( 'Spacing Between Items', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'condition'   => [ 'layout' => 'style-3' ],
				'selectors'   => [
					'{{WRAPPER}} .penci-fncrypto-item .penci-fncrypto-di + .penci-fncrypto-di' => 'margin-top: {{SIZE}}px;',
				],
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'style_section', [
			'label'     => esc_html__( 'Item Style', 'penci-finance' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout' => [ 'style-1', 'style-2' ] ],
		] );

		$this->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} table.dataTable' => 'background: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bd_color',
			array(
				'label'     => __( 'Border Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} --pcborder-cl' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'table_padding',
			array(
				'label'      => __( 'Table Padding', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'item_logo',
			array(
				'label'     => __( 'Logo Size', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .pcfic-name img' => 'width: {{SIZE}}px;' ),
			)
		);

		$this->add_control(
			'tabbdw',
			array(
				'label'      => __( 'Table Border Width', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable.row-border' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bdr',
			array(
				'label'      => __( 'Table Border Radius', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bds_enable',
			array(
				'label' => __( 'Enable Box Shadow for Table', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'bds',
			array(
				'label'      => __( 'Box Shadow', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::BOX_SHADOW,
				'size_units' => array( 'px' ),
				'condition'  => array( 'bds_enable' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};' ),
			)
		);

		$this->add_control(
			'cell_padding',
			array(
				'label'      => __( 'Cell Padding', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable > tbody > tr > th, {{WRAPPER}} table.dataTable > tbody > tr > td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bdw',
			array(
				'label'      => __( 'Cell Border Width', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} table.dataTable.row-border > tbody > tr > *, {{WRAPPER}} table.dataTable.display > tbody > tr > *' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'chart_section', [
			'label' => esc_html__( 'Chart Style', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control(
			'chart_height',
			array(
				'label'     => __( 'Height', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 800, ) ),
				'selectors' => array( '{{WRAPPER}} .pcfic-chart-container' => 'height: {{SIZE}}px;' ),
			)
		);

		$this->add_control(
			'chart_up_border',
			array(
				'label'       => __( 'Up Status: Border Color', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array( '{{WRAPPER}} .pcfic-total-up .pcfic-chart' => '--upbdcl: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'chart_up_bg',
			array(
				'label'       => __( 'Up Status: Background Color', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array( '{{WRAPPER}} .pcfic-total-up .pcfic-chart' => '--upbgcl: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'chart_down_border',
			array(
				'label'       => __( 'Down Status: Border Color', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array( '{{WRAPPER}} .pcfic-total-down .pcfic-chart' => '--upbdcl: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'chart_down_bg',
			array(
				'label'       => __( 'Down Status: Background Color', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array( '{{WRAPPER}} .pcfic-total-down .pcfic-chart' => '--upbgcl: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'typo_section', [
			'label'     => esc_html__( 'Typography', 'penci-finance' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout' => [ 'style-1', 'style-2' ] ],
		] );

		$this->add_control(
			'heading_01',
			array(
				'label' => __( 'Table Head', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} table.dataTable thead > tr > th.dt-orderable-asc, {{WRAPPER}} table.dataTable thead > tr > th.dt-orderable-desc, {{WRAPPER}} table.dataTable thead > tr > td.dt-orderable-asc, {{WRAPPER}} table.dataTable thead > tr > td.dt-orderable-desc',
		) );

		$this->add_control(
			'symbol_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} table.dataTable thead > tr > th.dt-orderable-asc, {{WRAPPER}} table.dataTable thead > tr > th.dt-orderable-desc, {{WRAPPER}} table.dataTable thead > tr > td.dt-orderable-asc, {{WRAPPER}} table.dataTable thead > tr > td.dt-orderable-desc' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_02',
			array(
				'label' => __( 'Number', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_meta_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} table.dataTable td.dt-type-numeric',
		) );

		$this->add_control(
			'symbol_meta_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} table.dataTable td.dt-type-numeric' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'symbol_stu_cl',
			array(
				'label'     => __( 'Up Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} table.dataTable td.dt-type-numeric.up' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'symbol_std_cl',
			array(
				'label'     => __( 'Down Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} table.dataTable td.dt-type-numeric.down' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();

	}

	public function isupdown( $value ) {
		return $value < 0 ? 'down' : 'up';
	}

	protected function render() {
		$settings = $this->get_settings();

		$this->markup_block_title( $settings, $this );

		include plugin_dir_path( __DIR__ ) . 'templates/style-2.php';
	}
}