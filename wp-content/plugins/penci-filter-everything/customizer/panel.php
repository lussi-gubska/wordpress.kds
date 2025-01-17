<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciFTE_Option extends CustomizerOptionAbstract {

	public $panelID = 'penci_filter_everything_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Filter Everything', 'penci-filter-everything' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_fte_general_section', esc_html__( 'Colors & Font Size', 'penci-filter-everything' ), $this->panelID );
		$this->add_lazy_section( 'penci_fte_translate_section', esc_html__( 'Quick Text Translation', 'penci-filter-everything' ), $this->panelID );
	}
}