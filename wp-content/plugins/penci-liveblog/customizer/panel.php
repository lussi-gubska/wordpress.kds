<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciLiveBlog_Option extends CustomizerOptionAbstract {

	public $panelID = 'penci_liveblog_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Live Blog', 'penci-liveblog' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_liveblog_general_section', esc_html__( 'General', 'penci-liveblog' ), $this->panelID );
		$this->add_lazy_section( 'penci_liveblog_styles_section', esc_html__( 'Font Size & Colors', 'penci-liveblog' ), $this->panelID );
		$this->add_lazy_section( 'penci_liveblog_translations_section', esc_html__( 'Text Translations', 'penci-liveblog' ), $this->panelID );
	}
}