<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'rwmb_meta_boxes', 'penci_amp_register_meta_boxes' );

function penci_amp_register_meta_boxes( $meta_boxes ) {
	$meta_boxes[] = array(
		'title'      => esc_html__( 'Penci AMP Settings', 'soledad' ),
		'post_types' => array( 'post', 'page','product' ),
		'context'    => 'side',
		'priority'   => 'default',
		'fields'     => array(
			array(
				'id'   => 'penci_dis_amp_onpost',
				'name' => esc_html__( 'Disable amp version', 'soledad' ),
				'type' => 'checkbox',
			),
		)
	);

	return $meta_boxes;
}

if( ! class_exists( 'Penci_AMP_Taxonomy_Meta_Field' ) ){
	class Penci_AMP_Taxonomy_Meta_Field{

		public $taxonomies = array( 'category' );

		function __construct(){

			$taxonomies = $this->taxonomies;
			foreach ( $taxonomies as $taxonomy ) {
				add_action( "{$taxonomy}_add_form_fields", array( $this, '_add_fields' ), 10, 2 );
				add_action("{$taxonomy}_edit_form", array( $this, '_add_fields' ), 10, 2 );

				add_action( "create_{$taxonomy}", array( $this, '_save_fields' ) );
				add_action( "edited_{$taxonomy}", array( $this, '_save_fields' ) );
			}
		}

		public function _add_fields( $tag ){
			$dis_amp = '';

			if( isset( $tag->term_id ) ){
				$term_id = $tag->term_id;
				$dis_amp = get_term_meta( $term_id, 'penci_dis_amp_onpost', true );
			}
			?>
			<div id="poststuff" style="min-width: 300px;">
				<div id="postimagediv" class="postbox">
					<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Penci AMP Settings', 'soledad' );  ?></span></h2>
					<div class="inside">
						<div class="penci-tax-meta-fields">
							<div class="penci-tab-content-widget">
								<div id="general" class="tab-content" style="display: block">
									<p class="penci-field-item ">
										<input class="penci-checkbox" name="penci_dis_amp_onpost" type="checkbox" value="1" <?php echo ( $dis_amp ? 'checked="checked"': '' ); ?>>
										<label><?php esc_html_e( 'Disable amp version', 'soledad' ); ?></label>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		public function _save_fields( $term_id ) {
			$dis_amp = isset( $_POST['penci_dis_amp_onpost'] ) ? $_POST['penci_dis_amp_onpost'] : 0;
			update_term_meta($term_id, 'penci_dis_amp_onpost', $dis_amp );
		}
	}
	new Penci_AMP_Taxonomy_Meta_Field;
}

