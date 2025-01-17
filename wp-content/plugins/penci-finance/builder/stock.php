<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Repeater;
use PenciSoledadElementor\Base\Base_Widget;

class PenciFinanceElementorStock extends Base_Widget {

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'Stock Market Data', 'penci-finance' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'finance', 'stock', 'market' ];
	}

	public function get_name() {
		return 'penci-finance-elementor';
	}

	protected function register_controls() {

		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'layout',
			array(
				'label'       => __( 'Layout', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'style-1',
				'label_block' => true,
				'options'     => [
					'style-1' => __( 'Row', 'penci-finance' ),
					'style-2' => __( 'Columns', 'penci-finance' ),
					'style-3' => __( 'Sticker', 'penci-finance' ),
				],
			)
		);

		$this->add_control(
			'sticker_speed',
			array(
				'label'     => __( 'Sticker Speed', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => [ 'layout' => 'style-3' ],
			)
		);

		$this->add_responsive_control(
			'col',
			array(
				'label'       => __( 'Number of Columns', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'condition'   => [ 'layout' => 'style-2' ],
				'default'     => [ 'size' => '3' ],
				'range'       => array( 'px' => array( 'min' => 1, 'max' => 12, ) ),
				'selectors'   => array( '{{WRAPPER}} .penci-finance-list.style-2' => '--col: {{SIZE}};' ),
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
				'label'       => __( 'Show Datas', 'penci-finance' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => [ 'longname', 'ename', 'sname', 'ask', 'cur', 'mkchange', 'mkchangep' ],
				'label_block' => true,
				'multiple'    => true,
				'options'     => [
					'longname'  => __( 'Name', 'penci-finance' ),
					'sname'     => __( 'Symbol Name', 'penci-finance' ),
					'ename'     => __( 'Exchange Name', 'penci-finance' ),
					'ask'       => __( 'Price', 'penci-finance' ),
					'cur'       => __( 'Financial Currency', 'penci-finance' ),
					'mkchange'  => __( 'Regular Market Change', 'penci-finance' ),
					'mkchangep' => __( 'Regular Market Change Percent', 'penci-finance' ),
				],
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'extra_menu_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Symbol', 'penci-finance' ),
			]
		);

		$repeater->add_control(
			'symbol',
			[
				'label'       => esc_html__( 'Search symbol name', 'penci-finance' ),
				'description' => esc_html__( 'Add symbol by name.', 'penci-finance' ),
				'type'        => 'penci_el_autocomplete',
				'search'      => 'penci_get_stock_by_query',
				'render'      => 'penci_get_stock_title_by_id',
				'multiple'    => false,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => esc_html__( 'Custom Symbol Name', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Logo', 'penci-finance' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Logo Image', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'colors_tab',
			[
				'label' => esc_html__( 'Colors', 'penci-finance' ),
			]
		);

		$repeater->add_control(
			'bd_color',
			[
				'label'     => esc_html__( 'Border Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-color: {{VALUE}}' )
			]
		);

		$repeater->add_control(
			'bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}' )
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'symbols_list',
			[
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'label'       => esc_html__( 'Symbol Items', 'penci-finance' ),
				'separator'   => 'before',
				'title_field' => '{{{ name }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'symbol' => 'AAPL',
						'name'   => 'AAPL',
					],
					[
						'symbol' => 'MSFT',
						'name'   => 'MSFT',
					],
					[
						'symbol' => 'AMZN',
						'name'   => 'AMZN',
					],
				],
			]
		);

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section', [
			'label' => esc_html__( 'Items Style', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control(
			'item_logo',
			array(
				'label'     => __( 'Logo Size', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array(
					'{{WRAPPER}} .penci-fnlt-item-head .penci-fnlt-logo' => 'max-width: {{SIZE}}px;width: {{SIZE}}px;height: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'item_spacing',
			array(
				'label'     => __( 'Items Spacing', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( 
					'{{WRAPPER}} .penci-finance-list' => '--gap: {{SIZE}}px;'
				),
			)
		);

		$this->add_control(
			'bd_color',
			array(
				'label'     => __( 'Border Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-item, {{WRAPPER}} .penci-finance-list.style-2 .penci-fnlt-item-body' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-item' => 'background: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .penci-fnlt-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);
		$this->add_responsive_control(
			'item_spacing_t',
			array(
				'label'     => __( 'Spacing Between Name & Price', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'condition' => array( 'layout' => 'style-2' ),
				'selectors' => array(
					'{{WRAPPER}} .penci-finance-list.style-2 .penci-fnlt-item-body' => 'padding-top:calc( {{SIZE}}px / 2 );margin-top: calc( {{SIZE}}px / 2 );'
				),
			)
		);

		$this->add_control(
			'bdw',
			array(
				'label'      => __( 'Border Width', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fnlt-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bdr',
			array(
				'label'      => __( 'Border Radius', 'penci-finance' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-fnlt-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bds_enable',
			array(
				'label' => __( 'Enable Box Shadow', 'penci-finance' ),
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
				'selectors'  => array( '{{WRAPPER}} .penci-fnlt-item' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};' ),
			)
		);


		$this->end_controls_section();

		$this->start_controls_section( 'typo_section', [
			'label' => esc_html__( 'Items Typography', 'penci-finance' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'heading_01',
			array(
				'label' => __( 'Symbol Title', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} } .penci-fnlt-name h4',
		) );

		$this->add_control(
			'symbol_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-name h4' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_02',
			array(
				'label' => __( 'Symbol Meta', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_meta_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} } .penci-fnlt-subname',
		) );

		$this->add_control(
			'symbol_meta_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-subname' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_03',
			array(
				'label' => __( 'Symbol Value', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_vl_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} } ..penci-fnlt-data',
		) );

		$this->add_control(
			'symbol_vl_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-data' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_vl_c_typo',
			'label'    => __( 'Currency Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} .penci-fnlt-data .pcfnlt-symbol-cur',
		) );

		$this->add_control(
			'symbol_vl_c_cl',
			array(
				'label'     => __( 'Currency Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-data .pcfnlt-symbol-cur' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_04',
			array(
				'label' => __( 'Symbol Status', 'penci-finance' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_st_typo',
			'label'    => __( 'Typography', 'penci-finance' ),
			'selector' => '{{WRAPPER}} } .penci-fnlt-change',
		) );

		$this->add_control(
			'symbol_st_cl',
			array(
				'label'     => __( 'Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-change' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'symbol_stu_cl',
			array(
				'label'     => __( 'Up Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-item.up .penci-fnlt-change' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'symbol_std_cl',
			array(
				'label'     => __( 'Down Color', 'penci-finance' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-fnlt-item.down .penci-fnlt-change' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();

	}

	protected function render() {

		$settings     = $this->get_settings();
		$symbol_lists = $settings['symbols_list'];
		$data_show    = $settings['data_show'];
		$layout       = $settings['layout'];

		$symbol_attr  = [];
		$symbol_class = [];
		$symbol_img   = [];

		if ( ! empty ( $symbol_lists ) ) {
			foreach ( $symbol_lists as $index => $symbol ) {
				$symbol_attr[ $index ]  = $symbol['symbol'];
				$symbol_img[ $index ]   = isset( $symbol['image'] ) && $symbol['image'] ? $symbol['image'] : '';
				$symbol_class[ $index ] = 'elementor-repeater-item-' . $symbol['_id'];
			}
		}

		$this->markup_block_title( $settings, $this );

		$finance_data = Penci_Finance_Stock::getQuotes( $symbol_attr );

		include plugin_dir_path( __DIR__ ) . 'templates/style-1.php';
	}
}