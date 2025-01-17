<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW\Customizer;

/**
 * Class Theme Soledad Customizer
 */
class PenciBF_Option extends CustomizerOptionAbstract {

	public $panelID = 'penci_bookmark_follow_panel';

	public function set_option() {
		$this->set_panel();
		$this->set_section();
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'       => $this->panelID,
			'title'    => esc_html__( 'Bookmark & Follow', 'soledad' ),
			'priority' => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'penci_bookmark_follow_general_section', esc_html__( 'General', 'penci-bookmark-follow' ), $this->panelID );
		$this->add_lazy_section( 'penci_bookmark_follow_noti_section', esc_html__( 'Notifications Settings', 'penci-bookmark-follow' ), $this->panelID );
		$this->add_lazy_section( 'penci_bookmark_follow_email_section', esc_html__( 'Email Settings', 'penci-bookmark-follow' ), $this->panelID );
		$this->add_lazy_section( 'penci_bookmark_follow_translate_section', esc_html__( 'Quick Text Translation', 'penci-bookmark-follow' ), $this->panelID );
	}
}