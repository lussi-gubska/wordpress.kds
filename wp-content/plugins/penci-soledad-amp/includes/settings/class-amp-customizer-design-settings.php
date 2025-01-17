<?php

class Penci_AMP_Customizer_Design_Settings {
	const DEFAULT_HEADER_COLOR = '#313131';
	const DEFAULT_TITLE_COLOR = '#313131';
	const DEFAULT_HEADER_BACKGROUND_COLOR = '#ffffff';
	const DEFAULT_TEXT_COLOR = '#313131';
	const DEFAULT_LINK_COLOR = '#6eb48c';

	const DEFAULT_COLOR_SCHEME = 'light';

	public static function init() {
		add_action( 'penci_amp_customizer_init', array( __CLASS__, 'init_customizer' ) );

		add_filter( 'penci_amp_customizer_get_settings', array( __CLASS__, 'append_settings' ) );
	}

	public static function init_customizer() {
		add_action( 'penci_amp_customizer_register_settings', array( __CLASS__, 'register_customizer_settings' ) );
		add_action( 'penci_amp_customizer_register_ui', array( __CLASS__, 'register_customizer_ui' ) );
		add_action( 'penci_amp_customizer_enqueue_preview_scripts', array( __CLASS__, 'enqueue_customizer_preview_scripts' ) );
	}

	public static function register_customizer_settings( $wp_customize ) {
		// Header text color setting
		$wp_customize->add_setting( 'penci_amp_customizer[header_color]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_HEADER_COLOR,
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		// Header background color
		$wp_customize->add_setting( 'penci_amp_customizer[header_background_color]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_HEADER_BACKGROUND_COLOR,
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		// Text color
		$wp_customize->add_setting( 'penci_amp_customizer[text_color]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_TEXT_COLOR,
			'sanitize_callback' => 'sanitize_hex_color',
		) );


		$wp_customize->add_setting( 'penci_amp_customizer[title_color]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_TITLE_COLOR,
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		// Link color
		$wp_customize->add_setting( 'penci_amp_customizer[link_color]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_LINK_COLOR,
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		// Background color scheme
		$wp_customize->add_setting( 'penci_amp_customizer[color_scheme]', array(
			'type'              => 'option',
			'default'           => self::DEFAULT_COLOR_SCHEME,
			'sanitize_callback' => array( __CLASS__ , 'sanitize_color_scheme' ),
		) );
	}

	public static function register_customizer_ui( $wp_customize ) {
		$wp_customize->add_section( 'penci_amp_design', array(
			'title' => __( 'Colors', 'penci-amp' ),
			'panel' => Penci_AMP_Customizer::PANEL_ID,
			'priority' => 9,
		) );

		// Header text color control.
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, 'penci_amp_header_color', array(
				'settings'   => 'penci_amp_customizer[header_color]',
				'label'    => __( 'Header Text Color', 'penci-amp' ),
				'section'  => 'penci_amp_design',
				'priority' => 10,
			) )
		);

		// Header background color control.
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, 'penci_amp_header_background_color', array(
				'settings'   => 'penci_amp_customizer[header_background_color]',
				'label'    => __( 'Header Background', 'penci-amp' ),
				'section'  => 'penci_amp_design',
				'priority' => 20,
			) )
		);


		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, 'penci_amp_text_color', array(
				'settings'   => 'penci_amp_customizer[text_color]',
				'label'    => __( 'Text color', 'penci-amp' ),
				'section'  => 'penci_amp_design',
				'priority' => 20,
			) )
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, 'penci_amp_title_color', array(
				'settings'   => 'penci_amp_customizer[title_color]',
				'label'    => __( 'Titles Color', 'penci-amp' ),
				'section'  => 'penci_amp_design',
				'priority' => 20,
			) )
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize, 'penci_amp_link_color', array(
				'settings'   => 'penci_amp_customizer[link_color]',
				'label'    => __( 'Links color', 'penci-amp' ),
				'section'  => 'penci_amp_design',
				'priority' => 20,
			) )
		);

		// Background color scheme
		$wp_customize->add_control( 'penci_amp_color_scheme', array(
			'settings'   => 'penci_amp_customizer[color_scheme]',
			'label'      => __( 'Color Scheme', 'penci-amp' ),
			'section'    => 'penci_amp_design',
			'type'       => 'radio',
			'priority'   => 30,
			'choices'    => self::get_color_scheme_names(),
		));
	}

	public static function enqueue_customizer_preview_scripts() {
		wp_enqueue_script(
			'amp-customizer-design-preview',
			penci_amp_get_asset_url( 'js/amp-customizer-design-preview.js' ),
			array( 'amp-customizer' ),
			false,
			true
		);

		wp_localize_script( 'amp-customizer-design-preview', 'penci_amp_customizer_design', array(
			'color_schemes' => self::get_color_schemes(),
		) );
	}

	public static function append_settings( $settings ) {
		$settings = wp_parse_args( $settings, array(
			'header_color' => self::DEFAULT_HEADER_COLOR,
			'header_background_color' => self::DEFAULT_HEADER_BACKGROUND_COLOR,
			'text_color' => self::DEFAULT_TEXT_COLOR,
			'title_color' => self::DEFAULT_TITLE_COLOR,
			'link_color' => self::DEFAULT_LINK_COLOR,
			'color_scheme' => self::DEFAULT_COLOR_SCHEME,
		) );

		$theme_colors = self::get_colors_for_color_scheme( $settings['color_scheme'] );

		return array_merge( $settings, $theme_colors, array( ) );
	}

	protected static function get_color_scheme_names() {
		return array(
			'light'   => __( 'Light', 'penci-amp' ),
			'dark'    => __( 'Dark', 'penci-amp' ),
		);
	}

	protected static function get_color_schemes() {
		return array(
			'light' => array(
				// Convert colors to greyscale for light theme color; see http://goo.gl/2gDLsp
				'theme_color'      => '#ffffff',
				'muted_text_color' => '#999999',
				'border_color'     => '#dedede',
			),
			'dark' => array(
				// Convert and invert colors to greyscale for dark theme color; see http://goo.gl/uVB2cO
				'theme_color'      => '#111111',
				'muted_text_color' => '#b1b1b1',
				'border_color'     => '#262626',

			),
		);
	}

	protected static function get_colors_for_color_scheme( $scheme ) {
		$color_schemes = self::get_color_schemes();

		if ( isset( $color_schemes[ $scheme ] ) ) {
			return $color_schemes[ $scheme ];
		}

		return $color_schemes[ self::DEFAULT_COLOR_SCHEME ];
	}

	public static function sanitize_color_scheme( $value ) {
		$schemes = self::get_color_scheme_names();
		$scheme_slugs = array_keys( $schemes );

		if ( ! in_array( $value, $scheme_slugs, true ) ) {
			$value = self::DEFAULT_COLOR_SCHEME;
		}

		return $value;
	}
}
