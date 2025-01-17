<?php

namespace PenciSoledadElementor\Modules\PenciMailChimp\Widgets;

use PenciSoledadElementor\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PenciMailChimp extends Base_Widget {

	public function get_name() {
		return 'penci-mail-chimp';
	}

	public function get_title() {
		return penci_get_theme_name( 'Penci' ) . ' ' . esc_html__( ' Mailchimp', 'soledad' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return array( 'mail', 'chimp' );
	}

	protected function register_controls() {


		// Section layout
		$this->start_controls_section(
			'section_page', array(
				'label' => esc_html__( 'Mailchimp', 'soledad' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'note_important', array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( __( 'Please set up the Mailchimp SignUp Form first by following <a href="%s" target="_blank">this guide</a>.', 'soledad' ), 'https://soledad.pencidesign.net/soledad-document/#setup_mailchimp' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		if ( function_exists( 'mc4wp_get_forms' ) ) {
			$forms              = mc4wp_get_forms();
			$mc4wp_premium_form = [];
			foreach ( $forms as $form ) {
				$mc4wp_premium_form[ $form->ID ] = $form->name . ' - ID: ' . $form->ID;
			}

			$this->add_control(
				'mailchimp_pre_form', array(
					'label'   => __( 'Select Mailchimp Form', 'soledad' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 's1',
					'options' => $mc4wp_premium_form
				)
			);
		}

		$this->add_control(
			'mailchimp_style', array(
				'label'   => __( 'Select Style', 'soledad' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 's1',
				'options' => array(
					's1' => esc_html__( 'Default', 'soledad' ),
					's2' => esc_html__( 'Boxed One Line', 'soledad' ),
					's3' => esc_html__( 'Background', 'soledad' ),
					's4' => esc_html__( 'Briefness', 'soledad' ),
				)
			)
		);

		$this->add_control(
			'hide_desc', array(
				'label'     => __( 'Hide Description Text?', 'soledad' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .penci-mailchimp-block .mdes' => 'display: none;',
				),
				'condition' => array( 'mailchimp_style!' => array( 's4' ) ),
			)
		);

		$this->add_control(
			'hide_name', array(
				'label'     => __( 'Hide Name Field?', 'soledad' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .penci-mailchimp-block .mname' => 'display: none;',
				),
				'condition' => array( 'mailchimp_style' => array( 's1', 's2' ) ),
			)
		);

		$this->add_responsive_control(
			'content_w', array(
				'label'      => __( 'Content width', 'soledad' ),
				'size_units' => [ 'px', '%' ],
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => array( '{{WRAPPER}} .penci-mailchimp-block' => 'max-width: {{SIZE}}px;width:100%;' ),
			)
		);

		$this->add_control( 'content_alignment', array(
			'label'                => __( 'Content Alignment', 'soledad' ),
			'description'          => __( 'Applies changes when you set a width for the form', 'soledad' ),
			'type'                 => Controls_Manager::CHOOSE,
			'label_block'          => false,
			'options'              => array(
				'left'   => array(
					'title' => __( 'Left', 'soledad' ),
					'icon'  => 'eicon-h-align-left',
				),
				'center' => array(
					'title' => __( 'Center', 'soledad' ),
					'icon'  => 'eicon-h-align-center',
				),
				'right'  => array(
					'title' => __( 'Right', 'soledad' ),
					'icon'  => 'eicon-h-align-right',
				),
			),
			'default'              => 'center',
			'selectors'            => array(
				'{{WRAPPER}} .penci-mailchimp-block' => '{{VALUE}}',
			),
			'selectors_dictionary' => array(
				'left'   => 'margin-right: auto',
				'center' => 'margin-left: auto; margin-right: auto;',
				'right'  => 'margin-left: auto',
			),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'spacing_section', array(
				'label' => esc_html__( 'Elements Spacing', 'soledad' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control( 'form_padding', array(
			'label'      => __( 'Content Padding', 'soledad' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .penci-mailchimp-s2 .penci-header-signup-form, {{WRAPPER}} .penci-mailchimp-s3 .footer-subscribe' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
			'condition'  => array( 'mailchimp_style' => array( 's2', 's3' ) ),
		) );

		$this->add_responsive_control(
			'mc4wp_formbot', array(
				'label'     => __( 'Sign Up Form Spacing Bottom', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form' => 'margin-bottom: {{SIZE}}px' ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_spacebet', array(
				'label'     => __( 'Horizontal Spacing Between Fields', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array(
					'{{WRAPPER}} .penci-mailchimp-s4 .mc4wp-form-fields'                                                                                                                                 => 'margin-left: -{{SIZE}}px;margin-right: -{{SIZE}}px;',
					'{{WRAPPER}} .penci-mailchimp-s4 .mc4wp-form-fields .mname, {{WRAPPER}} .penci-mailchimp-s4 .mc4wp-form-fields .memail, {{WRAPPER}} .penci-mailchimp-s4 .mc4wp-form-fields .msubmit' => 'padding-left: {{SIZE}}px;padding-right: {{SIZE}}px;'
				),
				'condition' => array( 'mailchimp_style' => array( 's4' ) ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_des_martop', array(
				'label'     => __( 'Description Spacing Top', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .mdes' => 'margin-top: {{SIZE}}px' ),
				'condition' => array( 'mailchimp_style' => array( 's1', 's3' ) ),
			)
		);
		$this->add_responsive_control(
			'mc4wp_des_marbt', array(
				'label'     => __( 'Description Spaing Bottom', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .mdes' => 'margin-bottom: {{SIZE}}px' ),
				'condition' => array( 'mailchimp_style' => array( 's1', 's3' ) ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_namebot', array(
				'label'     => __( 'Name Field Spaing Bottom', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .mname' => 'margin-bottom: {{SIZE}}px' ),
				'condition' => array( 'mailchimp_style' => array( 's1' ) ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_emailbot', array(
				'label'     => __( 'Email Field Spaing Bottom', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .memail' => 'margin-bottom: {{SIZE}}px' ),
				'condition' => array( 'mailchimp_style' => array( 's1' ) ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_buttonbot', array(
				'label'     => __( 'Submit Button Spaing Bottom', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .msubmit' => 'margin-bottom: {{SIZE}}px' ),
				'condition' => array( 'mailchimp_style' => array( 's1' ) ),
			)
		);

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section(
			'section_mailchimp_style',
			array(
				'label' => __( 'Mailchimp', 'soledad' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'mc4wp_inputh', array(
				'label'     => __( 'Custom Fields Height', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array( '{{WRAPPER}} .penci-mailchimp-block' => '--pcmc-height: {{SIZE}}px' ),
			)
		);

		$this->add_responsive_control(
			'mc4wp_borderw', array(
				'label'     => __( 'Custom Borders Width', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 10 ) ),
				'selectors' => array( '{{WRAPPER}} .penci-mailchimp-block' => '--pcmc-bdw: {{SIZE}}px' ),
			)
		);

		$this->add_control(
			'mc4wp_bg_color', array(
				'label'     => __( 'Background color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => array( 'mailchimp_style' => array( 's2', 's3' ) ),
				'selectors' => array(
					'body:not(.pcdm-enable) {{WRAPPER}} .footer-subscribe,' .
					'body:not(.pcdm-enable) {{WRAPPER}} .penci-header-signup-form' => 'background-color: {{VALUE}};',
				),

			)
		);

		$this->add_control(
			'tweets_desc_headings',
			array(
				'label'     => __( 'Description Text', 'soledad' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'mc4wp_des_width', array(
				'label'     => __( 'Description Width', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1000 ) ),
				'selectors' => array( '{{WRAPPER}} .mc4wp-form .mdes' => 'max-width: {{SIZE}}px;width:100%;display: inline-block' ),
			)
		);

		$this->add_control(
			'mc4wp_des_color', array(
				'label'     => __( 'Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .penci-header-signup-form .mc4wp-form-fields > p,' .
					'{{WRAPPER}} .penci-header-signup-form form > p,' .
					'{{WRAPPER}} .footer-subscribe .mc4wp-form .mdes,' .
					'{{WRAPPER}} .mc4wp-form-fields' => 'color: {{VALUE}};'
				)
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'mc4wp_des_typo',
				'label'    => __( 'Typography', 'soledad' ),
				'selector' => '{{WRAPPER}} .penci-header-signup-form .mc4wp-form-fields > p,{{WRAPPER}} .penci-header-signup-form form > p,{{WRAPPER}} .footer-subscribe .mc4wp-form .mdes,{{WRAPPER}} .mc4wp-form-fields'
			)
		);

		// Input
		$markup_input = '{{WRAPPER}} .widget input[type="text"],';
		$markup_input .= '{{WRAPPER}} .widget input[type="email"],';
		$markup_input .= '{{WRAPPER}} .widget input[type="date"],';
		$markup_input .= '{{WRAPPER}} .widget input[type="number"],';
		$markup_input .= '{{WRAPPER}} .widget input[type="search"],';
		$markup_input .= '{{WRAPPER}} .widget input[type="password"]';


		$this->add_control(
			'tweets_input_headings',
			array(
				'label'     => __( 'Name & Email Fields', 'soledad' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'mc4wp_bg_input_color', array(
				'label'     => __( 'Background Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( $markup_input => 'background-color: {{VALUE}};' )
			)
		);
		$this->add_control(
			'mc4wp_border_input_color', array(
				'label'     => __( 'Border Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( $markup_input => 'border-color: {{VALUE}};' )
			)
		);
		$this->add_control(
			'mc4wp_text_input', array(
				'label'     => __( 'Text Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( $markup_input => 'color: {{VALUE}};' )
			)
		);

		$this->add_control(
			'mc4wp_placeh_input', array(
				'label'     => __( 'Placeholder Text Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .widget_mc4wp_form_widget input::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .widget_mc4wp_form_widget input::-moz-placeholder'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .widget_mc4wp_form_widget input:-ms-input-placeholder,'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .widget_mc4wp_form_widget input::placeholder'               => 'color: {{VALUE}};',
				)
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'mc4wp_input_typo',
				'selector' => $markup_input,
			)
		);

		// Button
		$this->add_control(
			'mc4wp_button_headings',
			array(
				'label'     => __( 'Submit Button', 'soledad' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => 'mc4wp_btn_typo',
				'selector' => '{{WRAPPER}} .mc4wp-form input[type="submit"]',
			)
		);

		$this->add_responsive_control(
			'mc4wp_butpadding', array(
				'label'     => __( 'Padding Left & Right', 'soledad' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors' => array(
					'{{WRAPPER}} .penci-mailchimp-s2 .mc4wp-form-fields .msubmit input, {{WRAPPER}} .penci-mailchimp-s4 .mc4wp-form-fields .msubmit input' => 'padding-left: {{SIZE}}px;padding-right: {{SIZE}}px;',
					'{{WRAPPER}} .penci-mailchimp-s4 .msubmit'                                                                                             => 'width: auto;max-width: 100%;',
				),
				'condition' => array( 'mailchimp_style!' => array( 's1', 's3' ) ),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal', array(
				'label' => __( 'Normal', 'soledad' )
			)
		);

		$this->add_control(
			'button_text_color', array(
				'label'     => __( 'Text Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .mc4wp-form input[type="submit"]' => 'fill: {{VALUE}}; color: {{VALUE}};' )
			)
		);

		$this->add_control(
			'background_color', array(
				'label'     => __( 'Background Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( 'body:not(.pcdm-enable) {{WRAPPER}} .mc4wp-form input[type="submit"]' => 'background-color: {{VALUE}};' )
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover', array(
				'label' => __( 'Hover', 'soledad' )
			)
		);

		$this->add_control(
			'hover_color', array(
				'label'     => __( 'Text Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .mc4wp-form input[type="submit"]:hover' => 'color: {{VALUE}};',

				)
			)
		);

		$this->add_control(
			'button_background_hover_color', array(
				'label'     => __( 'Background Color', 'soledad' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'body:not(.pcdm-enable) {{WRAPPER}} .mc4wp-form input[type="submit"]:hover' => 'background-color: {{VALUE}};',
				)
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		$this->register_block_title_style_section_controls();

	}

	protected function render() {
		$settings = $this->get_settings();
		$form_id  = isset( $settings['mailchimp_pre_form'] ) && $settings['mailchimp_pre_form'] ? $settings['mailchimp_pre_form'] : 0;

		$mailchimp_style = $settings['mailchimp_style'];

		$css_class = 'penci-block-vc penci-mailchimp-block';
		$css_class .= ' penci-mailchimp-' . $mailchimp_style;

		$class_signup_form = 'widget widget_mc4wp_form_widget';
		if ( 's2' == $mailchimp_style ) {
			$class_signup_form .= ' penci-header-signup-form';
		} elseif ( 's3' == $mailchimp_style ) {
			$class_signup_form .= ' footer-subscribe';
		}
		?>
        <div class="<?php echo esc_attr( $css_class ); ?>">
			<?php $this->markup_block_title( $settings, $this ); ?>
            <div class="penci-block_content">
                <div class="<?php echo esc_attr( $class_signup_form ); ?>">
					<?php
					if ( function_exists( 'mc4wp_show_form' ) ) {
						mc4wp_show_form( $form_id );
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
}
