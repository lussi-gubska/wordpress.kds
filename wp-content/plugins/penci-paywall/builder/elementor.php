<?php


use PenciSoledadElementor\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PenciPayWallElementor extends \Elementor\Widget_Base {

	public function get_name() {
		return 'penci-paywall';
	}

	public function get_title() {
		if ( function_exists('penci_get_theme_name')){
			return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( ' PayWall', 'penci-paywall' );
		} else {
			return esc_html__( 'Penci PayWall', 'penci-paywall' );
		}
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return array( 'Pricing', 'Table' );
	}

	protected function register_controls() {


		$this->start_controls_section(
			'section_general', array(
				'label' => esc_html__( 'Pricing Table', 'penci-paywall' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control( 'paywall_list', [
			'label'       => esc_html__( 'Post Subscription', 'penci-paywall' ),
			'description' => esc_html__( 'Select post subscription package.', 'penci-paywall' ),
			'type'        => \Elementor\Controls_Manager::SELECT2,
			'label_block' => true,
			'options'     => self::get_product_list(),
		] );

		$this->add_control(
			'_design_style', array(
				'label'   => __( 'Choose Style', 'penci-paywall' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 's1',
				'options' => array(
					's1' => esc_html__( 'Style 1', 'penci-paywall' ),
					's2' => esc_html__( 'Style 2', 'penci-paywall' ),
				)
			)
		);

		$this->add_control(
			'_featured_header', array(
				'label'     => esc_html__( 'Featured Header?', 'penci-paywall' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'penci-paywall' ),
				'label_off' => __( 'No', 'penci-paywall' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'_use_img', array(
				'label'     => esc_html__( 'Add image', 'penci-paywall' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'penci-paywall' ),
				'label_off' => __( 'No', 'penci-paywall' ),
			)
		);

		$this->add_control(
			'_image',
			array(
				'label'     => __( 'Choose Image', 'penci-paywall' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( '_use_img' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(), array(
				'name'      => 'thumbnail',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => array( '_use_img' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'image_width', array(
				'label'     => __( 'Image Width', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 600, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-image' => 'max-width: {{SIZE}}px; width: 100%;' ),
				'condition' => array( '_use_img' => 'yes' ),
			)
		);
		$this->add_responsive_control(
			'image_height', array(
				'label'     => __( 'Image Height', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-image' => 'height: {{SIZE}}px;' ),
				'condition' => array( '_use_img' => 'yes' ),
			)
		);
		$this->add_responsive_control(
			'image_mar_top', array(
				'label'     => __( 'Image margin top', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-image' => 'margin-top: {{SIZE}}px;' ),
				'condition' => array( '_use_img' => 'yes' )
			)
		);
		$this->add_responsive_control(
			'image_mar_bottom', array(
				'label'     => __( 'Image margin bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-image' => 'margin-bottom: {{SIZE}}px;' ),
				'condition' => array( '_use_img' => 'yes' ),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'_use_icon', array(
				'label'     => esc_html__( 'Add Icon', 'penci-paywall' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'penci-paywall' ),
				'label_off' => __( 'No', 'penci-paywall' ),
			)
		);

		$this->add_control(
			'_icon', array(
				'label'     => esc_html__( 'Select Icon', 'penci-paywall' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array( '_use_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'icon_mar_top', array(
				'label'     => __( 'Icon margin top', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-icon' => 'margin-top: {{SIZE}}px;' ),
				'condition' => array( '_use_icon' => 'yes' )
			)
		);

		$this->add_responsive_control(
			'icon_mar_bottom', array(
				'label'     => __( 'Icon margin bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-icon' => 'margin-bottom: {{SIZE}}px;' ),
				'condition' => array( '_use_icon' => 'yes' ),
				'separator' => 'after',
			)
		);
		$this->add_control(
			'pricing_oneline', array(
				'label'     => esc_html__( 'Display Pricing & Pricing Unit in One Line?', 'penci-paywall' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'penci-paywall' ),
				'label_off' => __( 'No', 'penci-paywall' ),
			)
		);
		$this->add_control(
			'content', array(
				'label'   => '',
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
				'default' => '<ul><li>Example Feature 1</li><li>Example Feature 2</li><li>Example Feature 3</li><li>Example Feature 4</li></ul>',
			)
		);
		$this->add_control(
			'_btn_text', array(
				'label'     => __( 'Button Text', 'penci-paywall' ),
				'type'      => Controls_Manager::TEXT,
				'separator' => 'before',
				'default'   => __( 'Sign Up', 'penci-paywall' ),
			)
		);

		$this->add_control(
			'_btn_pos', array(
				'label'   => __( 'Button Position', 'penci-paywall' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'below',
				'options' => array(
					'below' => esc_html__( 'Below Content', 'penci-paywall' ),
					'above' => esc_html__( 'Above Content', 'penci-paywall' ),
				)
			)
		);

		$this->add_control(
			'_featured', array(
				'label'     => esc_html__( 'Make this pricing box as featured', 'penci-paywall' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'penci-paywall' ),
				'label_off' => __( 'No', 'penci-paywall' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'_featured_style', array(
				'label'     => __( 'Featured Style', 'penci-paywall' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hheight',
				'options'   => array(
					'hheight' => esc_html__( 'Highlight Height', 'penci-paywall' ),
					'scale'   => esc_html__( 'Scale Up', 'penci-paywall' ),
				),
				'condition' => array(
					'_featured' => 'yes',
				)
			)
		);

		$this->add_control(
			'add_ribb', array(
				'label'   => __( 'Add Ribbon?', 'penci-paywall' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''         => esc_html__( 'None', 'penci-paywall' ),
					'rib_icon' => esc_html__( 'Ribbon Icon', 'penci-paywall' ),
					'rib_text' => esc_html__( 'Ribbon Text', 'penci-paywall' ),
				),
			)
		);

		$this->add_control(
			'ribb_icon', array(
				'label'     => __( 'Custom Ribbon Icon', 'penci-paywall' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array( 'add_ribb' => 'rib_icon' )
			)
		);

		$this->add_control(
			'ribb_text', array(
				'label'     => __( 'Custom Ribbon Text', 'penci-paywall' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Best Value', 'penci-paywall' ),
				'condition' => array( 'add_ribb' => 'rib_text' )
			)
		);

		$this->add_control(
			'min_height', array(
				'label'     => __( 'Minimum height for pricing item', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-item' => 'min-height: {{SIZE}}px' ),
			)
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Pricing Table', 'penci-paywall' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'_bg_color', array(
				'label'     => __( 'Background Color for Pricing Table', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-item' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'_pborder_color', array(
				'label'     => __( 'Border Color for Pricing Table', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-item' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_featured_header_bg', array(
				'label'     => __( 'Featured Header Background Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-fheader .penci-pricing-header' => 'background-color: {{VALUE}};' ),
				'condition' => array( '_featured_header' => 'yes' ),
			)
		);

		$this->add_control(
			'icon_heading_settings',
			array(
				'label'     => __( 'Icon', 'penci-paywall' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( '_use_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'_icon_color', array(
				'label'     => __( 'Icon Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-icon' => 'color: {{VALUE}};' ),
				'condition' => array( '_use_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'_icon_font_size', array(
				'label'     => __( 'Icon Font Size', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 300, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-icon' => 'font-size: {{SIZE}}px' ),
				'condition' => array( '_use_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'title_heading_settings',
			array(
				'label' => __( 'Title', 'penci-paywall' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'_heading_color', array(
				'label'     => __( 'Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_heading_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-title',
			)
		);
		$this->add_responsive_control(
			'_heading_mar_bottom', array(
				'label'     => __( 'Margin Bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-title' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		// Sub title
		$this->add_control(
			'subtitle_heading_settings',
			array(
				'label' => __( 'Subtitle', 'penci-paywall' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'_subheading_color', array(
				'label'     => __( 'Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-subtitle' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_subheading_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-subtitle',
			)
		);
		$this->add_responsive_control(
			'_subheading_mar_bottom', array(
				'label'     => __( 'Margin Bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-subtitle' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		// Price title
		$this->add_control(
			'_price_heading_settings',
			array(
				'label' => __( 'Price', 'penci-paywall' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'_price_color', array(
				'label'     => __( 'Price Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-price' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_price_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-price',
			)
		);
		$this->add_responsive_control(
			'_price_mar_bottom', array(
				'label'     => __( 'Margin Bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-price' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		// Price Unit
		$this->add_control(
			'_unit_heading_settings',
			array(
				'label' => __( 'Price Unit', 'penci-paywall' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'_unit_color', array(
				'label'     => __( 'Price Unit Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-unit' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_unit_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-unit',
			)
		);
		$this->add_responsive_control(
			'_unit_mar_bottom', array(
				'label'     => __( 'Margin Bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-unit' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		// Features
		$this->add_control(
			'features_heading_settings',
			array(
				'label' => __( 'Features', 'penci-paywall' ),
				'type'  => Controls_Manager::HEADING,
			)
		);
		$this->add_control(
			'features_color', array(
				'label'     => __( 'Features Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing-featured' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'features_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-featured',
			)
		);
		$this->add_responsive_control(
			'features_mar_bottom', array(
				'label'     => __( 'Margin Bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-featured' => 'margin-bottom: {{SIZE}}px' ),
			)
		);
		$this->add_control(
			'item_fea_bottom', array(
				'label'     => __( 'Item of list features margin bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-featured li, .penci-pricing-featured p' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		$this->add_control(
			'_ribbon_color', array(
				'label'     => __( 'Ribbon Background Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing_featured .penci-pricing-ribbon, {{WRAPPER}} .penci-pricing_featured .penci-pricing-ribbon-text' => 'background-color: {{VALUE}};' ),
				'condition' => array(
					'add_ribb' => array( 'rib_text', 'rib_icon' )
				)
			)
		);

		$this->add_control(
			'_ribbon_tcolor', array(
				'label'     => __( 'Ribbon Text Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-pricing_featured .penci-pricing-ribbon, {{WRAPPER}} .penci-pricing_featured .penci-pricing-ribbon-text' => 'color: {{VALUE}};' ),
				'condition' => array(
					'add_ribb' => array( 'rib_text', 'rib_icon' )
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'      => 'ribbon_typo',
				'label'     => __( 'Ribbon Text Typography', 'penci-paywall' ),
				'selector'  => '{{WRAPPER}} .penci-pricing_featured .penci-pricing-ribbon-text',
				'condition' => array(
					'add_ribb' => 'rib_text',
				)
			)
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Pricing Table Button', 'penci-paywall' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_style', array(
				'label'   => __( 'Button Style', 'penci-paywall' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'filled',
				'options' => array(
					'filled'   => esc_html__( 'Filled', 'penci-paywall' ),
					'bordered' => esc_html__( 'Bordered', 'penci-paywall' ),
				),
			)
		);

		$this->add_control(
			'psubmitbtn_color',
			array(
				'label'     => __( 'Button Text Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'psubmitbtn_bgcolor',
			array(
				'label'     => __( 'Button Background & Border Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn' => 'background-color: {{VALUE}};border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'psubmitbtn_hcolor',
			array(
				'label'     => __( 'Button Hover Text Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'psubmitbtn_hbgcolor',
			array(
				'label'     => __( 'Button Background & Border Hover Color', 'penci-paywall' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'psubmitbtn_typo',
				'label'    => __( 'Typography', 'penci-paywall' ),
				'selector' => '{{WRAPPER}} .penci-pricing-btn',
			)
		);

		$this->add_responsive_control(
			'button_radius', array(
				'label'      => __( 'Borders Radius', 'penci-paywall' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .penci-pricing-item .penci-pricing-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);

		$this->add_responsive_control(
			'button_borders_width', array(
				'label'      => __( 'Borders Width', 'penci-paywall' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .penci-pricing-item .penci-pricing-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				),
			)
		);

		$this->add_control(
			'btn_mar_top', array(
				'label'     => __( 'Button margin top', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn' => 'margin-top: {{SIZE}}px' ),
			)
		);
		$this->add_control(
			'btn_mar_bt', array(
				'label'     => __( 'Button margin bottom', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		$this->add_control(
			'btn_width', array(
				'label'     => __( 'Button width', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array( '{{WRAPPER}} .penci-pricing-btn' => 'width: {{SIZE}}px' ),
			)
		);

		$this->add_control(
			'btn_height', array(
				'label'     => __( 'Button Height', 'penci-paywall' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000, ) ),
				'selectors' => array(
					'{{WRAPPER}} .penci-pricing-btn' => 'line-height: {{SIZE}}px; padding-top: 0; padding-bottom: 0;',
				),
			)
		);
		$this->end_controls_section();

	}

	public function get_product_list() {
		$result   = array();
		$packages = null;

		if ( class_exists( 'WooCommerce' ) ) {
			$packages = get_posts(
				array(
					'post_type'   => 'product',
					'tax_query'   => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => [ 'paywall_subscribe', 'paywall_unlock' ],
						),
					),
					'orderby'     => 'menu_order title',
					'order'       => 'ASC',
					'post_status' => 'publish',
				)
			);
		}

		if ( function_exists( 'getpaid' ) ) {
			$packages = get_posts(
				array(
					'post_type'      => 'wpi_item',
					'orderby'        => 'title',
					'order'          => 'ASC',
					'posts_per_page' => - 1,
					'post_status'    => array( 'publish' ),
					'meta_query'     => array(
						array(
							'key'     => '_wpinv_type',
							'compare' => 'IN',
							'value'   => [ 'paywall_subscribe', 'paywall_unlock' ]
						)
					)
				)
			);
		}

		if ( $packages ) {
			foreach ( $packages as $value ) {
				$result[ $value->ID ] = $value->post_title;
			}
		} else {
			$result[''] = __( 'No Post Package Found', 'penci-paywall' );
		}

		return $result;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$class_block       = 'penci-block-vc penci-pricing-table penci-pricing-item';
		$class_block       .= $settings['_featured'] ? ' penci-pricing_featured' : '';
		$class_block       .= $settings['pricing_oneline'] ? ' penci-pricing-oneline' : '';
		$class_block       .= 'bordered' == $settings['button_style'] ? ' penci-pricing-btn-borders' : '';
		$class_block       .= $settings['_featured_style'] ? ' penci-pricing-f-' . $settings['_featured_style'] : ' penci-pricing-f-hheight';
		$class_block       .= $settings['_btn_pos'] ? ' penci-pricing-btn-' . $settings['_btn_pos'] : ' penci-pricing-btn-below';
		$class_block       .= 'yes' == $settings['_featured_header'] ? ' penci-pricing-fheader' : '';
		$class_block       .= $settings['_design_style'] ? ' penci-pricing-' . esc_attr( $settings['_design_style'] ) : '';
		$_btn_pos          = isset( $settings['_btn_pos'] ) ? $settings['_btn_pos'] : 'below';
		$featured_style    = isset( $settings['_featured_style'] ) ? $settings['_featured_style'] : 'hheight';
		$add_ribb          = isset( $settings['add_ribb'] ) ? $settings['add_ribb'] : '';
		$ribb_text         = isset( $settings['ribb_text'] ) ? $settings['ribb_text'] : 'Best Value';
		$settings['_unit'] = '';
		if ( isset( $settings['paywall_list'] ) && $settings['paywall_list'] ) {
			wp_enqueue_style( 'penci-paywall' );
			if ( class_exists( '\WPInv_Item' ) ) {
				$product                 = new \WPInv_Item( $settings['paywall_list'] );
				$settings['_heading']    = $product->get_title();
				$settings['_subheading'] = $product->get_description();
				$settings['_price']      = $product->get_the_price();
				if ( $product->is_recurring() ) {
					$settings['_unit'] = pencipw_duration_text( $product->get_recurring_interval(), $product->get_recurring_period() );
				}
				$button_html = '';
				if ( $settings['_btn_text'] ) {
					if ( is_user_logged_in() ) {
						$button_html = '<div class="penci-pricing-pbutton">' . do_shortcode( '[getpaid item=' . $product->ID . ' button="'.$settings['_btn_text'].'"]' ) . '</div>';
						$button_html = str_replace( 'btn btn-primary getpaid-payment-button', 'getpaid-payment-button penci-pricing-btn penci-button', $button_html );
					} else {
						$button_html = '<div class="penci-pricing-pbutton penci-login-popup-btn"><a class="penci-pricing-btn penci-button" href="#penci-login-popup">'. $settings['_btn_text'] . '</a></div>';
						add_filter( 'theme_mod_penci_tblogin', function () {
							return true;
						} );
					}
				}
			} elseif ( class_exists( 'WooCommerce' ) ) {
				$product                 = wc_get_product( $settings['paywall_list'] );
				$settings['_heading']    = $product->get_title();
				$settings['_subheading'] = $product->get_short_description();
				$settings['_price']      = $product->get_price_html();
				$total                   = get_post_meta( $settings['paywall_list'], '_pencipw_total', true );
				$duration                = get_post_meta( $settings['paywall_list'], '_pencipw_duration', true );


				if ( $product->is_type( 'paywall_subscribe' ) ) {
					$settings['_unit'] = pencipw_duration_text( $total, $duration );
				} elseif ( $product->is_type( 'paywall_unlock' ) ) {
					$settings['_unit'] = $product->get_total_unlock() . ' ' . __( 'posts', 'penci-paywall' );
				} elseif ( $product->is_type( 'subscription' ) ) {
					$total             = $product->__get( 'subscription_period_interval' );
					$duration          = $product->__get( 'subscription_period' );
					$settings['_unit'] = pencipw_duration_text( $total, $duration );
				}

				$button_html = '';
				if ( $settings['_btn_text'] ) {
					$button_html = '<a href=\'\' class=\'button pencipw-woo-btn penci-pricing-btn penci-button\' data-product_id=\'' . esc_attr( $settings['paywall_list'] ) . '\' data-recurring=\'no\'><span>' . $settings['_btn_text'] . '</span><i class=\'fa fa-spinner fa-pulse\' style=\'display: none;\'></i></a>';
				}
			}
			?>
            <div class="<?php echo esc_attr( $class_block ); ?>">
				<?php
				if ( 'rib_icon' == $add_ribb ) {
					if ( ! empty( $settings['ribb_icon'] ) ) {
						echo '<span class="penci-pricing-ribbon">';
						\Elementor\Icons_Manager::render_icon( $settings['ribb_icon'] );
						echo '</span>';
					} else {
						echo '<span class="penci-pricing-ribbon">' . penci_icon_by_ver( 'fas fa-star' ) . '</span>';
					}
				}
				if ( 'rib_text' == $add_ribb ) {
					echo '<span class="penci-pricing-ribbon-text">' . do_shortcode( $ribb_text ) . '</span>';
				}
				?>
                <div class="penci-block_content penci-pricing-inner">
					<?php

					echo '<div class="penci-pricing-header">';
					if ( ! empty( $settings['_image']['url'] ) && $settings['_use_img'] ) {
						$this->add_render_attribute( 'image', 'src', $settings['_image']['url'] );
						$this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $settings['_image'] ) );
						$this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $settings['_image'] ) );

						echo '<div class="penci-pricing-image">';
						echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', '_image' );
						echo '</div>';
					}

					if ( ! empty( $settings['_icon'] ) && ( 'yes' == $settings['_use_icon'] ) ) {
						echo '<div class="penci-pricing-icon">';
						\Elementor\Icons_Manager::render_icon( $settings['_icon'] );
						echo '</div>';
					}

					if ( $settings['_heading'] ) {
						echo '<div class="penci-pricing-title">' . do_shortcode( $settings['_heading'] ) . '</div>';
					}

					if ( $settings['_subheading'] ) {
						echo '<div class="penci-pricing-subtitle">' . do_shortcode( $settings['_subheading'] ) . '</div>';
					}

					echo '</div>';

					if ( $settings['_price'] || $settings['_unit'] ) {
						echo '<div class="penci-price-unit">';

						if ( $settings['_price'] ) {
							echo '<span class="penci-pricing-price">' . do_shortcode( $settings['_price'] ) . '</span>';
						}

						if ( $settings['_unit'] ) {
							echo '<span class="penci-pricing-unit">' . do_shortcode( $settings['_unit'] ) . '</span>';
						}

						echo '</div>';
					}

					if ( 'above' == $_btn_pos ) {
						echo $button_html;
					}

					if ( $settings['content'] ) {
						$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $settings['content'] ) . "\n" );
						$content = do_shortcode( shortcode_unautop( $content ) );


						echo '<div class="penci-pricing-featured">' . $content . '</div>';
					}

					if ( 'below' == $_btn_pos ) {
						echo $button_html;
					}
					?>
                </div>
            </div>
			<?php
		}
	}
}
