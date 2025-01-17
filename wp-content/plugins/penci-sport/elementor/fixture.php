<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use PenciSoledadElementor\Base\Base_Widget;

class PenciSportElementorFixture extends Base_Widget {

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'Football Fixture', 'penci-sport' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'sport', 'football', 'standing' ];
	}

	public function get_name() {
		return 'penci-football-fixture';
	}

	protected function register_controls() {

		$this->start_controls_section( 'general_section', [
			'label' => esc_html__( 'General', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'token',
			array(
				'label'       => __( 'Token Key', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'description' => penci_sport_api_help(),
				'ai'          => [
					'active' => false,
				],
			)
		);

		$this->add_control(
			'league',
			array(
				'label'       => __( 'League', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'PL',
				'label_block' => true,
				'options'     => penci_sport_list_league(),
			)
		);

		$this->add_control(
			'status',
			array(
				'label'       => __( 'Status', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'SCHEDULED',
				'label_block' => true,
				'options'     => [
					'SCHEDULED' => __( 'Scheduled', 'penci-sport' ),
					'FINISHED'  => __( 'Finished', 'penci-sport' ),
				],
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Limit', 'penci-sport' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
			)
		);

		$this->add_control(
			'offset',
			array(
				'label' => __( 'Offset', 'penci-sport' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
			)
		);

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section', [
			'label' => esc_html__( 'Item Style', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-football-match' => 'background: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bd_color',
			array(
				'label'     => __( 'Border Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-football-match' => '--pcborder-cl: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bdr',
			array(
				'label'      => __( 'Border Radius', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-football-match' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bds_enable',
			array(
				'label' => __( 'Enable Box Shadow', 'penci-sport' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'bds',
			array(
				'label'      => __( 'Box Shadow', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::BOX_SHADOW,
				'size_units' => array( 'px' ),
				'condition'  => array( 'bds_enable' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .penci-football-match' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};' ),
			)
		);

		$this->add_control(
			'bdw',
			array(
				'label'      => __( 'Border Width', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-football-match' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'imgsize',
			array(
				'label'      => __( 'Team Image Size', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .pcteam_club img' => 'max-width: {{SIZE}}px;' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'typo_section', [
			'label' => esc_html__( 'Typography', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_typo',
			'label'    => __( 'Team Typography', 'penci-sport' ),
			'selector' => '{{WRAPPER}} .penci-football-match .pcfm-item-title',
		) );

		$this->add_control(
			'team_color',
			array(
				'label'     => __( 'Team Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .penci-football-match .pcfm-item-title' => 'color: {{VALUE}}' ],
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'time_typo',
			'label'    => __( 'Time Typography', 'penci-sport' ),
			'selector' => '{{WRAPPER}} .penci-football-match .penci-matche-time',
		) );

		$this->add_control(
			'time_color',
			array(
				'label'     => __( 'Time Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .penci-football-match .penci-matche-time' => 'color: {{VALUE}}' ],
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'score_typo',
			'label'    => __( 'Score Typography', 'penci-sport' ),
			'selector' => '{{WRAPPER}} .penci-football-match .penci-matche-score',
		) );

		$this->add_control(
			'score_color',
			array(
				'label'     => __( 'Score Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .penci-football-match .penci-matche-score' => 'color: {{VALUE}}' ],
			)
		);

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();

	}

	protected function render() {
		$settings = $this->get_settings();
		if ( empty( $settings['token'] ) && current_user_can( 'manage_options' ) ) {
			echo __( 'Please enter the Token Key', 'penci-sport' );
		} else {
			$this->markup_block_title( $settings, $this );
			include plugin_dir_path( __DIR__ ) . 'templates/fixture.php';
		}
	}
}