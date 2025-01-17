<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use \Elementor\Widget_Base;
use PenciDesign\SpeechCaster;

/**
 * Penci Text To Speech - Custom Elementor Widget.
 *
 * @since 3.0.0
 **/
final class text_to_speech_elementor extends Widget_Base {

	/**
	 * Return a widget name.
	 *
	 * @return string
	 * @since 3.0.0
	 **/
	public function get_name() {

		return 'pc-tts-elementor';

	}

	public function get_script_depends() {
		return [ 'penci-texttospeech', 'wp-mediaelement', 'penci-texttospeech-el' ];
	}

	/**
	 * Return the widget title that will be displayed as the widget label.
	 *
	 * @return string
	 * @since 3.0.0
	 **/
	public function get_title() {

		return esc_html__( 'Text To Speech', 'penci-text-to-speech' );

	}

	/**
	 * Set the widget icon.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function get_icon() {

		return 'eicon-play';

	}

	/**
	 * Set the category of the widget.
	 *
	 * @return array with category names
	 **@since 3.0.0
	 *
	 */
	public function get_categories() {

		return [ 'general' ];

	}

	/**
	 * Get widget keywords. Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function get_keywords() {

		return [ 'speech', 'text', 'voice' ];

	}

	/**
	 * Add the widget controls.
	 *
	 * @return void with category names
	 **@since 3.0.0
	 * @access protected
	 *
	 */
	protected function register_controls() {

		$this->start_controls_section( 'section_image', [ 'label' => esc_html__( 'Content', 'penci-text-to-speech' ) ] );
		$query['autofocus[panel]'] = 'penci_texttospeech_panel';
		$section_link              = add_query_arg( $query, admin_url( 'customize.php' ) );
		$note                      = '<div class="elementor-panel-alert elementor-panel-alert-info">';
		$note                      .= esc_html__( 'To configure plugin appearance go to ', 'penci-text-to-speech' );
		$note                      .= '<a href="' . esc_url( $section_link ) . '" target="_blank">';
		$note                      .= esc_html__( 'Penci Text To Speech Settings', 'penci-text-to-speech' );
		$note                      .= '</a>';
		$note                      .= esc_html__( ' page', 'penci-text-to-speech' );
		$note                      .= '</div>';

		$this->add_control(
			'important_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => $note,
			]
		);

		$this->add_control( 'penci_texttospeech_style', [
			'label'   => esc_html__( 'Player Style', 'soledad' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'style-4',
			'options' => [
				'style-1' => esc_html__( 'Round player', 'soledad' ),
				'style-2' => esc_html__( 'Rounded player', 'soledad' ),
				'style-3' => esc_html__( 'Squared player', 'soledad' ),
				'style-4' => esc_html__( 'WordPress default player', 'soledad' ),
				'style-5' => esc_html__( 'Chrome Style player', 'soledad' ),
				'style-6' => esc_html__( 'Browser default player', 'soledad' )
			]
		] );

		$this->add_control( 'penci_texttospeech_link', [
			'label'   => esc_html__( 'Download Link', 'soledad' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'none',
			'options' => [
				'none'     => esc_html__( 'Do not show', 'soledad' ),
				'frontend' => esc_html__( 'Showing', 'soledad' ),
			]
		] );

		$this->add_control( 'penci_texttospeech_autoplay', [
			'label'       => esc_html__( 'Autoplay', 'soledad' ),
			'description' => __( 'Autoplay the audio after page load. May not work in some browsers due to Browser Autoplay Policy. More details for <a href="https://developers.google.com/web/updates/2017/09/autoplay-policy-changes" target="_blank" rel="noreferrer">WebKit Browsers</a> and <a href="https://hacks.mozilla.org/2019/02/firefox-66-to-block-automatically-playing-audible-video-and-audio/" target="_blank" rel="noreferrer">Firefox</a>', 'soledad' ),
			'type'        => \Elementor\Controls_Manager::SWITCHER,
		] );

		$this->add_control( 'penci_texttospeech_loop', [
			'label'       => esc_html__( 'Loop', 'soledad' ),
			'description' => __( 'Loop the audio playback', 'soledad' ),
			'type'        => \Elementor\Controls_Manager::SWITCHER,
		] );
		$this->add_control( 'penci_texttospeech_speed_controls', [
			'label'       => esc_html__( 'Speed controls', 'soledad' ),
			'description' => __( 'Speed controls for the audio player the audio after page load', 'soledad' ),
			'type'        => \Elementor\Controls_Manager::SWITCHER,
		] );
		$this->add_control( 'penci_texttospeech_speed_title', [
			'label'       => esc_html__( 'Speed Block Title', 'soledad' ),
			'description' => __( 'Specify the title for speeds section', 'soledad' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'penci_texttospeech_speed', [
			'label'       => esc_html__( 'Available Speed', 'soledad' ),
			'description' => 'Specify speeds separated by commas. Speed must be in range from 0.1 to 16. Use period for decimal numbers, for example: 1.2, 1.5, 1.75',
			'type'        => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'penci_texttospeech_preload', [
			'label'       => esc_html__( 'Audio Preload', 'soledad' ),
			'description' => __( 'The preload attribute specifies if and how the audio file should be loaded when the page loads.', 'soledad' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => 'none',
			'options'     => [
				'none'     => esc_html__( 'None', 'penci-text-to-speech' ),
				'metadata' => esc_html__( 'Metadata', 'penci-text-to-speech' ),
				'auto'     => esc_html__( 'Auto', 'penci-text-to-speech' ),
				'backend'  => esc_html__( 'Backend', 'penci-text-to-speech' ),
			]
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'color_style', [
			'label' => esc_html__( 'Color & Styles', 'soledad' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'player_bg_color', [
			'label'     => 'Player Background Color',
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .penci-texttospeech-box' => '--pcaccent-cl:{{VALUE}}' ],
		] );

		$this->end_controls_section();

	}

	/**
	 * Render Frontend Output. Generate the final HTML on the frontend.
	 *
	 * @since 3.0.0
	 * @access protected
	 **/
	protected function render() {
		$this->overwrite_mods();
		$preview = \Elementor\Plugin::$instance->editor->is_edit_mode();
		?>
        <div class="penci-tts-widget"><?php if ( $preview ) {
				echo SpeechCaster::get_instance()->get_player( 0, $preview );
			} else {
				echo SpeechCaster::get_instance()->get_player( 0, false, 'builder' );
			} ?></div>
		<?php
	}

	protected function overwrite_mods() {
		$settings = $this->get_settings_for_display();
		$mods     = [
			'penci_texttospeech_style',
			'penci_texttospeech_link',
			'penci_texttospeech_autoplay',
			'penci_texttospeech_loop',
			'penci_texttospeech_speed_controls',
			'penci_texttospeech_speed_title',
			'penci_texttospeech_speed',
			'penci_texttospeech_preload',
		];
		foreach ( $mods as $mod ) {
			$value = $settings[ $mod ];
			add_filter( 'theme_mod_' . $mod, function () use ( $value ) {
				return $value;
			} );
		}
	}

}