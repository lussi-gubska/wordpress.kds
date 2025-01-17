<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class Penci_TextToSpeech_Option extends CustomizerOptionAbstract {

	public $panelID = 'penci_texttospeech_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'          => $this->panelID,
			'title'       => esc_html__( 'Text To Speech', 'soledad' ),
			'description' => __( 'Please check <a target="_blank" href="https://soledad.pencidesign.net/soledad-document/#text-to-speech">this video tutorial</a> to know how to setup this feature.', 'soledad' ),
			'priority'    => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_texttospeech_general_section', esc_html__( 'General', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_texttospeech_voice_section', esc_html__( 'Player Settings', 'soledad' ), $this->panelID );
	}
}
