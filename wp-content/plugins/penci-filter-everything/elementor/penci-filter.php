<?php

use PenciSoledadElementor\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PenciFilterElement extends Base_Widget {

	public function get_name() {
		return 'penci-filter';
	}

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'Filter', 'penci-filter-everything' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return array( 'sort', 'ajax', 'filter' );
	}

	public function get_script_depends() {
		return [ 'penci-post-filter-widget' ];
	}

	protected function register_controls() {


		// Section General
		$this->start_controls_section(
			'section_aboutme', array(
				'label' => esc_html__( 'Filter', 'penci-filter-everything' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'filter_type',
			array(
				'label'   => __( 'Filter Preset', 'penci-filter-everything' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'preset' => __( 'Preset', 'penci-filter-everything' ),
					'custom' => __( 'Custom', 'penci-filter-everything' ),
				),
				'default' => 'preset',
			)
		);

		$this->add_control(
			'filter_stype',
			array(
				'label'     => __( 'Filter Style', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'ajax'   => __( 'AJAX Filter', 'penci-filter-everything' ),
					'button' => __( 'Button Filter', 'penci-filter-everything' ),
				),
				'default'   => 'ajax',
				'condition' => [ 'filter_type' => 'custom' ]
			)
		);

		$this->add_control(
			'reset_button',
			array(
				'label'     => __( 'Show Reset Button?', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 'filter_type' => 'custom' ]
			)
		);

		$filter_lists_options = [ '' => '-Select-' ];

		$filter_lists = get_posts( [
			'post_type'      => 'penci-filter',
			'posts_per_page' => - 1,
		] );
		foreach ( $filter_lists as $filter_data ) {
			$filter_lists_options[ $filter_data->ID ] = $filter_data->post_title;
		}

		$post_types  = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ), 'object' );
		$tax_options = [];
		foreach ( $post_types as $post_type => $type ) {
			foreach ( get_object_taxonomies( $type->name, 'object' ) as $tax_name => $tax_info ) {
				if ( ! in_array( $tax_name, [ 'post_format', 'elementor_library_type', 'penci_block_category' ] ) ) {
					$tax_options[ $tax_name ] = $type->label . ' - ' . $tax_info->label;
				}
			}
		}

		$this->add_control(
			'filter_preset',
			array(
				'label'     => __( 'Filter Preset', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $filter_lists_options,
				'condition' => [ 'filter_type' => 'preset' ]
			)
		);

		$this->add_control(
			'filter_id',
			array(
				'label'   => __( 'Query ID', 'penci-filter-everything' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_meta',
			array(
				'label'     => __( 'Filter Groups', 'penci-filter-everything' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'filter_type' => 'custom' ]
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'filter_title',
			[
				'label'       => esc_html__( 'Title', 'penci-filter-everything' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
			]
		);
		$repeater->add_control(
			'filter_by',
			[
				'label'       => esc_html__( 'Filter Type', 'penci-filter-everything' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => [
					'tax'  => __( 'Taxonomy', 'penci-filter-everything' ),
					'meta' => __( 'Meta Key', 'penci-filter-everything' ),
				],
			]
		);

		$repeater->add_control( 'filter_view', array(
			'label'   => __( 'View', 'penci-filter-everything' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'checkbox',
			'options' => [
				'checkbox'     => __( 'Checkboxes', 'penci-filter-everything' ),
				'radio'        => __( 'Radio Buttons', 'penci-filter-everything' ),
				'label'        => __( 'Label List', 'penci-filter-everything' ),
				'select'       => __( 'Select Boxed', 'penci-filter-everything' ),
				'multi-select' => __( 'Multi Select Boxed', 'penci-filter-everything' ),
			],
		) );

		$repeater->add_control( 'filter_tax', array(
			'label'     => __( 'Taxonomy', 'penci-filter-everything' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $tax_options,
			'default'   => 'category',
			'condition' => [ 'filter_by' => 'tax' ]
		) );

		$repeater->add_control( 'tax_order_by', array(
			'label'     => __( 'Order By', 'penci-filter-everything' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'term_id',
			'condition' => [ 'filter_by' => 'tax' ],
			'options'   => [
				'term_id' => __( 'ID', 'penci-filter-everything' ),
				'name'    => __( 'Name', 'penci-filter-everything' ),
				'slug'    => __( 'Slug', 'penci-filter-everything' ),
				'count'   => __( 'Count', 'penci-filter-everything' ),
			],
		) );

		$repeater->add_control( 'tax_order', array(
			'label'     => __( 'Order', 'penci-filter-everything' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'DESC',
			'condition' => [ 'filter_by' => 'tax' ],
			'options'   => [
				'ASC'  => __( 'ASC', 'penci-filter-everything' ),
				'DESC' => __( 'DESC', 'penci-filter-everything' ),
			],
		) );

		$repeater->add_control( 'hide_empty', array(
			'label'     => __( 'Show Empty Categories?', 'penci-filter-everything' ),
			'type'      => Controls_Manager::SWITCHER,
			'condition' => [ 'filter_by' => 'tax' ],
		) );

		$repeater->add_control( 'show_counter', array(
			'label' => __( 'Show posts count?', 'penci-filter-everything' ),
			'type'  => Controls_Manager::SWITCHER,
		) );

		$repeater->add_control( 'number', array(
			'label'     => __( 'Limit Category to Show', 'penci-filter-everything' ),
			'type'      => Controls_Manager::NUMBER,
			'condition' => [ 'filter_by' => 'tax' ],
			'default'   => 10,
		) );

		$repeater->add_control( 'excluded', array(
			'label'       => __( 'Exclude Term IDs:', 'penci-filter-everything' ),
			'description' => __( 'E.g:  1, 2, 4', 'penci-filter-everything' ),
			'type'        => Controls_Manager::TEXT,
			'condition'   => [ 'filter_by' => 'tax' ],
		) );

		$repeater->add_control( 'filter_custom_field', array(
			'label'     => __( 'Meta Key', 'penci-filter-everything' ),
			'type'      => Controls_Manager::TEXT,
			'condition' => [ 'filter_by' => 'meta' ],
		) );

		$this->add_control(
			'filter_keys',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'filter_title' => esc_html__( 'Post Categories', 'penci-filter-everything' ),
						'filter_by'    => 'tax',
						'filter_tax'   => 'category',
					],
					[
						'filter_title' => esc_html__( 'Post Tags', 'penci-filter-everything' ),
						'filter_by'    => 'tax',
						'filter_tax'   => 'post_tag',
					],
				],
				'title_field' => '{{{ filter_title }}}',
			]
		);

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'penci-filter-everything' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_05',
			array(
				'label' => __( 'Filter Box', 'penci-filter-everything' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'filter_boxed',
			array(
				'label'     => __( 'Padding', 'penci-filter-everything' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array( '{{WRAPPER}} .pcptf-mt.ele' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'),
			)
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'filter_boxed_bg',
				'label'    => esc_html__( 'Backgrund', 'penci-filter-everything' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pcptf-mt.ele',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_boxed_border',
				'label'    => esc_html__( 'Border', 'penci-filter-everything' ),
				'selector' => '{{WRAPPER}} .pcptf-mt.ele',
				'fields_options' => [
					'color' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_responsive_control(
			'filter_boxed_bdradius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .pcptf-mt.ele' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_padding',
			array(
				'label'     => __( 'List Spacing', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array( '{{WRAPPER}} .pcptf-mt.ele li' => 'padding-bottom: {{SIZE}}{{UNIT}};padding-top: {{SIZE}}{{UNIT}};' )
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'title_typo',
				'label'    => __( 'Title Typography', 'penci-filter-everything' ),
				'selector' => '{{WRAPPER}} .pcptf-mt.ele li a',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt.ele li a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_hcolor',
			array(
				'label'     => __( 'Title Hover Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt.ele li a:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_added_color',
			array(
				'label'     => __( 'Title Added Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt.ele li a.added' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'heading_01',
			array(
				'label' => __( 'Checkbox', 'penci-filter-everything' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_responsive_control(
			'checkbox_fsize',
			array(
				'label'     => __( 'Font Size', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa:after' => 'font-size: {{SIZE}}{{UNIT}};' )
			)
		);
		$this->add_responsive_control(
			'checkbox_size',
			array(
				'label'     => __( 'Box Size', 'penci-filter-everything' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa:after' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};' )
			)
		);

		$this->start_controls_tabs(
			'checbox_tabs'
		);
		$this->start_controls_tab(
			'checbox_tabs_default',
			[
				'label' => esc_html__( 'Default', 'penci-filter-everything' ),
			]
		);

		// default
		$this->add_control(
			'checkbox_color',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa:after' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'checkbox_bcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa:after' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'checkbox_bgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa:after' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'checbox_tabs_added',
			[
				'label' => esc_html__( 'Active', 'penci-filter-everything' ),
			]
		);

		// added
		$this->add_control(
			'checkbox_acolor',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa.added:after' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'checkbox_abcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa.added:after' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'checkbox_abgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcptf-mt .pmfa.added:after' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// buttons
		$this->start_controls_section(
			'section_style_btn_01',
			array(
				'label' => __( 'Filter Button', 'penci-filter-everything' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => ['filter_stype'=>'button'],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'btn_01_typo',
				'label'    => __( 'Button Typography', 'penci-filter-everything' ),
				'selector' => '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn',
			)
		);

		$this->start_controls_tabs(
			'button_01_tabs'
		);
		$this->start_controls_tab(
			'button_01_tabs_default',
			[
				'label' => esc_html__( 'Default', 'penci-filter-everything' ),
			]
		);

		// default
		$this->add_control(
			'button_01_color',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_01_bcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn' => 'border: 1px solid {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_01_bgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_01_tabs_added',
			[
				'label' => esc_html__( 'Hover', 'penci-filter-everything' ),
			]
		);

		// added
		$this->add_control(
			'button_01acolor',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_01abcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn:hover' => 'border: 1px solid {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_01abgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-filter-btn:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		// reset btn
		$this->start_controls_section(
			'section_style_btn_02',
			array(
				'label' => __( 'Reset Button', 'penci-filter-everything' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => ['filter_stype'=>'button'],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'btn_02_typo',
				'label'    => __( 'Button Typography', 'penci-filter-everything' ),
				'selector' => '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn',
			)
		);

		$this->start_controls_tabs(
			'button_02_tabs'
		);
		$this->start_controls_tab(
			'button_02_tabs_default',
			[
				'label' => esc_html__( 'Default', 'penci-filter-everything' ),
			]
		);

		// default
		$this->add_control(
			'button_02_color',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_02_bcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn' => 'border: 1px solid {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_02_bgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_02_tabs_added',
			[
				'label' => esc_html__( 'Hover', 'penci-filter-everything' ),
			]
		);

		// added
		$this->add_control(
			'button_02acolor',
			array(
				'label'     => __( 'Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_02abcolor',
			array(
				'label'     => __( 'Border Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn:hover' => 'border: 1px solid {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_02abgcolor',
			array(
				'label'     => __( 'Background Color', 'penci-filter-everything' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .pcft-buttons .pcft-button.pcft-reset-btn:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->register_block_title_style_section_controls();

	}

	protected function render() {
		$settings    = $this->get_settings();
		$filter_type = $settings['filter_type'];
		$query_id    = $settings['filter_id'];

		$this->markup_block_title( $settings, $this );

		echo '<div class="pcptf-mt ele" data-selector="pcft-ele-' . $query_id . '">';

		if ( 'preset' == $filter_type && $settings['filter_preset'] ) {
			$filter = $settings['filter_preset'];
		} else {
			$elementor_preset                      = true;
			$elementor_filter_data                 = $elementor_filter_grn = [];
			$elementor_filter_data['post_type']    = 'post';
			$elementor_filter_data['filter_set']   = $settings['filter_keys'];
			$elementor_filter_data['reset_button'] = $settings['reset_button'];
			$elementor_filter_data['filter_type']  = $settings['filter_stype'];
		}

		include PENCI_FTE_DIR . '/templates/filter.php';

		echo '</div>';

		add_action( "penci_elementor_query_{$query_id}", 'penci_fe_elementor_query', 999 );
	}
}