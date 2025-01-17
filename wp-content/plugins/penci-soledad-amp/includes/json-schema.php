<?php
if ( ! class_exists( 'Penci_AMP_JSON_Schema_Validator' ) ) {
	class Penci_AMP_JSON_Schema_Validator {

		function __construct() {
			add_action( 'penci_amp_post_template_head', array( $this, 'output_schema_breadcrumbList' ) );
		}

		public static function output_schema_breadcrumbList(){
			$json = array();

			$breadcrumbs_enabled = false;

			$yoast_bread_enabled = current_theme_supports( 'yoast-seo-breadcrumbs' );
			if ( ! $yoast_bread_enabled && class_exists( 'WPSEO_Options' ) ) {
				if ( defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {
					$options = get_option( 'wpseo_internallinks' );
					if ( isset( $options['breadcrumbs-enable'] ) && $options['breadcrumbs-enable'] == true ) {
						$breadcrumbs_enabled = true;
					}
				} elseif ( method_exists( 'WPSEO_Options', 'get' ) && is_callable( array( 'WPSEO_Options', 'get' ) ) ) {
					$breadcrumbs_enabled = WPSEO_Options::get( 'breadcrumbs-enable', false );
				}
			}

			if ( ! is_front_page() &&  ! $breadcrumbs_enabled && ! get_theme_mod( 'penci_schema_breadcrumbs' ) ) {
				if( class_exists( 'Penci_JSON_Schema_Validator' ) ){
					$breadcrumb_list = Penci_JSON_Schema_Validator::BreadcrumbList_data();
					$itemListElement = isset( $breadcrumb_list['itemListElement'] ) ? $breadcrumb_list['itemListElement'] : array();

					if( $itemListElement ){
						foreach ( (array)$itemListElement as $item_key => $item ){
							$pre_link = isset( $item['item']['@id'] ) ? $item['item']['@id'] : '';
							if( $pre_link ){
								$pre_link = Penci_AMP_Link_Sanitizer::__pre_url ( $pre_link );

								$breadcrumb_list['itemListElement'][$item_key]['item']['@id'] = $pre_link;
							}
						}
					}

					$json = $breadcrumb_list;
				}

			}

			if( $json ){
				echo '<script type="application/ld+json" class="penci-breadcrumb-schema">' . wp_json_encode( $json ) . '</script>';
			}
		}
	}
}

new Penci_AMP_JSON_Schema_Validator;