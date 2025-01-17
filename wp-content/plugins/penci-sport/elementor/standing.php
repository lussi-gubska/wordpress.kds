<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use PenciSoledadElementor\Base\Base_Widget;

class PenciSportElementorStanding extends Base_Widget {

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( 'Football Standing', 'penci-sport' );
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
		return 'penci-football-standing';
	}

	protected function register_controls() {

		$this->start_controls_section( 'general_section', [
			'label' => esc_html__( 'General', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'topinfo',
			array(
				'label'       => __( 'Notice', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::ALERT,
				'alert_type'  => 'info',
				'content' 	  => __( 'This widget only displays matches from the group stage or tournaments that use a group format. If you want to display knockout round results, please use the <strong>Penci Football Fixture</strong> widget.','penci-sport' ),
			)
		);

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

		$this->end_controls_section();


		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'layout',
			array(
				'label'       => __( 'Style', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'style-1',
				'label_block' => true,
				'options'     => [
					'style-1' => __( 'Table - Style 1', 'penci-sport' ),
					'style-2' => __( 'Table - Style 2', 'penci-sport' ),
				],
			)
		);

		$this->add_responsive_control(
			'rgap',
			array(
				'label'       => __( 'Rows Gap', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .penci-football-standing' => '--rgap: {{SIZE}}px;',
				],
			)
		);

		$this->add_responsive_control(
			'mtablecol',
			array(
				'label'       => __( 'Multi Table Columns', 'penci-sport' ),
				'description' => __( 'This option only applies if the league you have selected contains multiple tables/groups.', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 1, 'max' => 6, ) ),
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .pcspt-tb.multi-table' => '--col: {{SIZE}};',
				],
			)
		);

		$this->add_responsive_control(
			'mtablegap',
			array(
				'label'       => __( 'Multi Table Gap', 'penci-sport' ),
				'description' => __( 'This option only applies if the league you have selected contains multiple tables/groups.', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}} .pcspt-tb.multi-table' => '--gap: {{SIZE}}px;',
				],
			)
		);

		$this->add_responsive_control(
			'imgw',
			array(
				'label'     => __( 'Logo Size', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array( '{{WRAPPER}} .pcteam_club img' => 'width:100%;max-width: {{SIZE}}px;' ),
			)
		);

		$data_cols = array(
			'position' => __( 'Position', 'penci-sport' ),
			'club'     => __( 'Club', 'penci-sport' ),
			'played'   => __( 'Played', 'penci-sport' ),
			'won'      => __( 'Won', 'penci-sport' ),
			'drawn'    => __( 'Drawn', 'penci-sport' ),
			'lost'     => __( 'Lost', 'penci-sport' ),
			'gf'       => __( 'GF', 'penci-sport' ),
			'ga'       => __( 'GA', 'penci-sport' ),
			'gd'       => __( 'GD', 'penci-sport' ),
			'points'   => __( 'Points', 'penci-sport' ),
		);

		$this->add_control(
			'data_show',
			array(
				'label'       => __( 'Meta Data', 'penci-sport' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => [
					'position',
					'club',
					'played',
					'won',
					'drawn',
					'lost',
					'gf',
					'ga',
					'gd',
					'points'
				],
				'label_block' => true,
				'multiple'    => true,
				'options'     => $data_cols,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'text_trans', [
			'label' => esc_html__( 'Text Translation', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		foreach ( $data_cols as $id => $text ) {
			$this->add_control(
				'text_' . $id,
				array(
					'label'   => __( 'Text:', 'penci-sport' ) . $text,
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => $text,
				)
			);
		}

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section', [
			'label' => esc_html__( 'Table Style', 'penci-sport' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-football-standing' => 'background: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bd_color',
			array(
				'label'     => __( 'Border Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-football-standing' => '--pcborder-cl: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'bdr',
			array(
				'label'      => __( 'Table Border Radius', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-football-standing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'bds_enable',
			array(
				'label' => __( 'Enable Box Shadow for Table', 'penci-sport' ),
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
				'selectors'  => array( '{{WRAPPER}} .penci-football-standing' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};' ),
			)
		);

		$this->add_control(
			'bdw',
			array(
				'label'      => __( 'Cell Border Width', 'penci-sport' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .penci-football-standing > tbody > tr > *, {{WRAPPER}} .penci-football-standing > tbody > tr > *' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'typo_section', [
			'label'     => esc_html__( 'Typography', 'penci-sport' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout' => [ 'style-1', 'style-2' ] ],
		] );

		$this->add_control(
			'heading_01',
			array(
				'label' => __( 'Table Head', 'penci-sport' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_typo',
			'label'    => __( 'Typography', 'penci-sport' ),
			'selector' => '{{WRAPPER}} .penci-football-standing thead td',
		) );

		$this->add_control(
			'color_headbg',
			array(
				'label'     => __( 'Heading Background Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .penci-football-standing thead td' => 'background: {{VALUE}}' ],
			)
		);

		$this->add_control(
			'heading_09',
			array(
				'label' => __( 'Table Title', 'penci-sport' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'symbol_tit_typo',
			'label'    => __( 'Title Typography', 'penci-sport' ),
			'selector' => '{{WRAPPER}} .penci-football-standing-wrap .penci-football-ghead',
		) );

		$this->add_control(
			'color_titl_headbg',
			array(
				'label'     => __( 'Title Color', 'penci-sport' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .penci-football-standing-wrap .penci-football-ghead' => 'color: {{VALUE}}' ],
			)
		);

		foreach ( $data_cols as $id => $text ) {
			$this->add_control(
				'heading_typo_' . $id,
				array(
					'label'      => $text,
					'type'       => \Elementor\Controls_Manager::HEADING,
					'conditions' => [
						'terms' => [
							[
								'name'     => 'data_show',
								'operator' => 'contains',
								'value'    => $id,
							],
						],
					],
				)
			);

			$this->add_control(
				'color_' . $id,
				array(
					'label'      => __( 'Color', 'penci-sport' ),
					'type'       => \Elementor\Controls_Manager::COLOR,
					'selectors'  => [ '{{WRAPPER}} .penci-football-standing .pcteam_' . $id => 'color: {{VALUE}}' ],
					'conditions' => [
						'terms' => [
							[
								'name'     => 'data_show',
								'operator' => 'contains',
								'value'    => $id,
							],
						],
					],
				)
			);

			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'       => 'typo_' . $id,
				'label'      => __( 'Typography', 'penci-sport' ),
				'selectors'  => '{{WRAPPER}} .penci-football-standing .pcteam_' . $id,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'data_show',
							'operator' => 'contains',
							'value'    => $id,
						],
					],
				],
			) );

			$this->add_responsive_control(
				'width_' . $id,
				array(
					'label'      => __( 'Width', 'penci-sport' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'range'      => array( 'px' => array( 'min' => 0, 'max' => 500, ) ),
					'selectors'  => [ '{{WRAPPER}} .penci-football-standing .pcteam_' . $id => 'width: {{SIZE}}px' ],
					'conditions' => [
						'terms' => [
							[
								'name'     => 'data_show',
								'operator' => 'contains',
								'value'    => $id,
							],
						],
					],
				)
			);
		}

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();

	}

	protected function render() {
		$settings = $this->get_settings();
		if ( empty( $settings['token'] ) && current_user_can( 'manage_options' ) ) {
			echo __( 'Please enter the Token Key', 'penci-sport' );
		} else {
			$this->markup_block_title( $settings, $this );
			include plugin_dir_path( __DIR__ ) . 'templates/standing.php';
		}
	}
}