<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciGaViews_Option extends CustomizerOptionAbstract {


	public function set_option() {
		$this->set_section();
	}

	public function set_section() {
		$this->add_lazy_section( 'pencidesign_general_gviews_section', esc_html__( 'Google Analytics Page Views', 'penci-ga-views' ), '', __('Sync pageview data from Google Analytics to your WordPress Database, enabling you to sort posts, view pageview data in the WordPress Dashboard, and output pageviews to your visitors.','penci-ga-views') );
	}
}