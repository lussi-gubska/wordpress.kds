<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciPayWriterOption extends CustomizerOptionAbstract {

	public $panelID = 'penci_pay_writer_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Pay Writer', 'soledad' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_pay_writer_general_section', esc_html__( 'Donation Settings', 'soledad' ), $this->panelID );
		$this->add_lazy_section( 'penci_pay_writer_posts_section', esc_html__( 'Pay Per Posts', 'soledad' ), $this->panelID );
	}
}