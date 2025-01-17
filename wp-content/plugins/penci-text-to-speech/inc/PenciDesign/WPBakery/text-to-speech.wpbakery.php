<?php

use PenciDesign\SpeechCaster;

/** @noinspection PhpUnused */

class Penci_TextToSpeech_VC {

	/**
	 * Get things started.
	 *
	 * @since 3.0.0
	 * @access public
	 **/
	public function __construct() {

		/** Penci Text To Speech VC Element map. */
		$this->penci_tts_map();

		/** Shortcode for Text To Speech Element. */
		add_shortcode( 'penci_tts_wp', [ $this, 'penci_tts_wp_render' ] );

	}

	/**
	 * Shortcode [penci_tts_wp] output.
	 *
	 * @param $atts array - Shortcode parameters.
	 *
	 * @return false|string
	 **@since 3.0.0
	 * @access public
	 *
	 */
	public function penci_tts_wp_render( $atts ) {

		/** Prepare element parameters. */
		$css = '';

		extract( shortcode_atts( [
			'css'                               => '',
			'penci_texttospeech_style'          => '',
			'penci_texttospeech_link'           => '',
			'penci_texttospeech_autoplay'       => '',
			'penci_texttospeech_loop'           => '',
			'penci_texttospeech_speed_controls' => '',
			'penci_texttospeech_speed_title'    => '',
			'penci_texttospeech_speed'          => '',
			'penci_texttospeech_preload'        => '',
			'player_bg_color'                   => '',
		], $atts ) );

		$mods = [
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
			$value = $atts[ $mod ];
			add_filter( 'theme_mod_' . $mod, function () use ( $value ) {
				return $value;
			} );
		}

		/** Prepare custom css from css_editor. */
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, '' ), 'penci-tts-widget', $atts );
		$id        = 'penci-tts-' . rand();
		ob_start(); ?>
        <div id="<?php echo esc_attr( $id ); ?>" class="penci-tts-widget-elementor">
            <div class="<?php echo esc_attr( $css_class ); ?>"><?php echo SpeechCaster::get_instance()->get_player( 0, false, 'builder' ); ?></div>
        </div>
		<?php

		if ( $atts['player_bg_color'] ) {
			?>
            <style>
                <?php echo '#'.esc_attr($id);?>  {
                    --pcaccent-cl:  <?php echo $atts['player_bg_color'];?>
                }
            </style>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * Penci Text To Speech VC Element map.
	 *
	 * @return void
	 **/
	public function penci_tts_map() {

		$theme_prefix_text = 'Soledad';
		$cat_prefix_text = 'Penci';
		if( function_exists('penci_get_theme_name')) {
			$theme_prefix_text = penci_get_theme_name( 'Soledad' );
			$cat_prefix_text = penci_get_theme_name( 'Penci' );
		}

		vc_map( [
			'name'                    => $cat_prefix_text . esc_html__( ' Text To Speech', 'penci-text-to-speech' ),
			'description'             => esc_html__( 'Create an audio version of your posts, with a selection of more than 275 voices across 45+ languages and variants.', 'penci-text-to-speech' ),
			'base'                    => 'penci_tts_wp',
			'icon'                    => get_template_directory_uri() . '/images/vc-icon.png',
			'category'                => $theme_prefix_text,
			'show_settings_on_create' => true,
			'controls'                => 'full',
			'params'                  => [
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Player Style', 'soledad' ),
					'param_name' => 'penci_texttospeech_style',
					'value'      => array(
						esc_html__( 'Round player', 'soledad' )             => 'style-1',
						esc_html__( 'Rounded player', 'soledad' )           => 'style-2',
						esc_html__( 'Squared player', 'soledad' )           => 'style-3',
						esc_html__( 'WordPress default player', 'soledad' ) => 'style-4',
						esc_html__( 'Chrome Style player', 'soledad' )      => 'style-5',
						esc_html__( 'Browser default player', 'soledad' )   => 'style-6',
					),
					'std'        => 'style-4',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Download Link', 'soledad' ),
					'param_name' => 'penci_texttospeech_link',
					'value'      => array(
						esc_html__( 'Do not show', 'soledad' ) => 'none',
						esc_html__( 'Showing', 'soledad' )     => 'frontend',
					),
					'std'        => 'none',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Auto Play', 'soledad' ),
					'param_name' => 'penci_texttospeech_autoplay',
					'value'      => array(
						esc_html__( 'Disable', 'soledad' ) => '',
						esc_html__( 'Enable', 'soledad' )  => 'enable',
					),
					'std'        => '',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Loop', 'soledad' ),
					'param_name' => 'penci_texttospeech_loop',
					'value'      => array(
						esc_html__( 'Disable', 'soledad' ) => '',
						esc_html__( 'Enable', 'soledad' )  => 'enable',
					),
					'std'        => '',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Speed controls', 'soledad' ),
					'param_name' => 'penci_texttospeech_speed_controls',
					'value'      => array(
						esc_html__( 'Disable', 'soledad' ) => '',
						esc_html__( 'Enable', 'soledad' )  => 'enable',
					),
					'std'        => '',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Speed Block Title', 'soledad' ),
					'param_name' => 'penci_texttospeech_speed_title',
					'std'        => '',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Available Speed', 'soledad' ),
					'param_name' => 'penci_texttospeech_speed',
					'std'        => '',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Audio Preload', 'soledad' ),
					'param_name' => 'penci_texttospeech_preload',
					'value'      => array(
						esc_html__( 'Disable', 'soledad' ) => '',
						esc_html__( 'Enable', 'soledad' )  => 'enable',
					),
					'std'        => '',
				),
				[
					'param_name' => 'player_bg_color',
					'type'       => 'colorpicker',
					'heading'    => esc_html__( 'Player Background Color', 'penci-text-to-speech' ),
					'group'      => esc_html__( 'Design Options', 'penci-text-to-speech' ),
				],
				[
					'param_name' => 'css',
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS', 'penci-text-to-speech' ),
					'group'      => esc_html__( 'Design Options', 'penci-text-to-speech' ),
				]
			],
		] );

	}

}

/** Run Penci Text To Speech Element. */
new Penci_TextToSpeech_VC();