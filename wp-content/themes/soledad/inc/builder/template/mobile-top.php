<?php
$classes     = '';
$stick_class = penci_get_builder_mod( 'penci_header_mobile_topbar_sticky_setting', 'enable' );
$classes     .= 'enable' == $stick_class ? 'sticky-enable' : 'sticky-disable';
$classes     .= penci_can_render_header( 'mobile', 'top' ) ? ' pc-hasel' : ' pc-noel';

$middle_content = penci_get_builder_mod( 'penci_header_mobile_topbar_column', 'enable' );
$classes        .= 'enable' == $middle_content ? ' pcmiddle-center' : ' pcmiddle-normal';

$bgtrans = penci_get_builder_mod( 'penci_header_mobile_topbar_background_trans', 'enable' );
$classes        .= 'enable' == $bgtrans ? ' bgtrans-enable' : ' bgnormal';
?>
<div class="penci_mobile_topbar penci-mobile-topbar penci_container <?php echo esc_attr( $classes ); ?>">
    <div class="container">
        <div class="penci_nav_row">
	        <?php
	        $columns = array( 'left', 'center', 'right' );

	        foreach ( $columns as $column ) {
		        $setting_align = "penci_hb_align_mobile_top_{$column}";
		        $align         = penci_get_builder_mod( $setting_align, $column );

		        $setting_element = "penci_hb_element_mobile_top_{$column}";
		        $elements        = penci_get_builder_mod( $setting_element );
		        $elements        = $elements ? explode( ',', $elements ) : '';
		        ?>

                <div class="penci_nav_col penci_nav_<?php echo esc_attr( $column ); ?> penci_nav_align<?php echo esc_attr( $align ); ?>">

			        <?php
			        if ( ! empty( $elements ) ) {
				        foreach ( $elements as $element ) {
					        if ( ! empty( $element ) && file_exists( PENCI_BUILDER_PATH . 'elements/' . $element . '/front-end.php' ) ) {
						        load_template( PENCI_BUILDER_PATH . 'elements/' . $element . '/front-end.php', false );
					        }
				        }
			        }
			        ?>

                </div>

		        <?php
	        }
	        ?>
        </div>
    </div>
</div>
