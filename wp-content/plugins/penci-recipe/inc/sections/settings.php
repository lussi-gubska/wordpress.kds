<?php
/**
 * @author : PenciDesign
 */

namespace SoledadFW;

/**
 * Class Theme Soledad Customizer
 */
class RecipeCustomizer {
	/**
	 * @var Customizer
	 */
	private static $instance;

	/**
	 * Package name
	 *
	 * @var string
	 */
	private static $package = 'PenciRecipe';

	/**
	 * @var Customizer
	 */
	private $customizer = null;

	/**
	 * Section of Customizer
	 */
	private $list_section = array(
		'penci_new_recipe_schema_section',
		'penci_new_recipe_size_section',
		'penci_new_recipe_translate_section',
		'penci_new_recipe_typo_section',
		'penci_new_section_recipe_section',
	);

	/**
	 * Construct
	 */
	private function __construct() {
		// Need to load Customizer early for this kind of request.
		if ( isset( $_REQUEST['action'] ) && 'penci_fw_customizer_disable_panel' === $_REQUEST['action'] ) {
			$this->customizer = \SoledadFW\Customizer\Customizer::get_instance();
		}

		if ( is_customize_preview() || ! is_admin() ) {
			add_filter( 'soledad_fw_register_lazy_section', array( $this, 'register_lazy_section' ) );
			add_action( 'soledad_fw_register_customizer_option', array( $this, 'load_customizer' ), 95 );
		}
	}

	/**
	 * @return Customizer
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function register_lazy_section( $result ) {
		$array = $this->list_section;

		$path = dirname(__DIR__) . '/sections/';

		foreach ( $array as $id ) {
			$result[ $id ][] = "{$path}{$id}.php";
		}

		return $result;
	}

	public function load_customizer() {
		$this->customizer = Customizer\Customizer::get_instance();
		new Customizer\RecipeOption( $this->customizer, 202 );
	}
}
