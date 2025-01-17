<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class Penci_SmartThumbnail_Option extends CustomizerOptionAbstract {

	public function set_option() {
		$this->set_section();
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_smartthumbnails_general_section', esc_html__( 'Penci Smart Thumbnail', 'soledad' ), '' );
	}
}
