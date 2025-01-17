<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciAI_Option extends CustomizerOptionAbstract {

	public $panelID = 'penci_ai_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Penci AI SmartContent Creator', 'soledad' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_ai_api_section', esc_html__( 'API Settings', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_ai_content_section', esc_html__( 'Content', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_ai_image_section', esc_html__( 'Images', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_ai_general_section', esc_html__( 'General', 'soledad' ), $this->panelID );
	}
}