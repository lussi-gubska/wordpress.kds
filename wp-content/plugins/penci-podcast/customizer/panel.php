<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciPodCastOption extends CustomizerOptionAbstract {

	public $panelID = 'penci_podcast_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Podcast', 'soledad' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_podcast_general_section', esc_html__( 'General Settings', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_podcast_category_section', esc_html__( 'Categories Layout', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_podcast_series_section', esc_html__( 'Series Layout', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_podcast_colors_section', esc_html__( 'Colors', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_podcast_translate_section', esc_html__( 'Text Translation', 'soledad' ), $this->panelID );
	}
}