<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciPaywallOption extends CustomizerOptionAbstract {

	public $panelID = 'penci_paywall_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Content Paywall', 'soledad' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_paywall_general_section', esc_html__( 'General', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_paywall_advanced_section', esc_html__( 'Advanced Settings', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_paywall_translations_section', esc_html__( 'Texts Translation', 'soledad' ), $this->panelID );
	}
}