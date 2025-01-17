<?php
/**
 * Show button shortcode
 *
 * @param array  $atts
 * @param string $content
 *
 * @return string
 */
if ( ! function_exists( 'penci_penci_button_shortcode' ) ) {
	function penci_penci_button_shortcode( $atts, $content ) {
		$link = $color = $size = $icon = $icon_position = '';
		$id = $nofollow = $background = $text_color = $target  = $align = '';
		$full = $class = $margin_bottom = $text_hover_color = $hover_bgcolor =  '';

		$atts = shortcode_atts( array(
			'link'          => '#',
			'color'         => '',
			'size'          => '',
			'icon'          => '',
			'icon_position' => '',
			'radius'		=> '',
			'id'               => '',
			'nofollow'         => '',
			'background'       => '',
			'text_color'       => '',
			'hover_bgcolor'    => '',
			'text_hover_color' => '',
			'target'           => '',
			'align'            => '',
			'full'             => '',
			'class'            => '',
			'margin_bottom'    => '',
			'margin_top'       => '',
		), $atts, 'button' );

		extract( $atts );
		
		$unique_id = 'pencisc-button' . '__' . rand( 1000, 100000000 );
		
		$classes = array( 'pencisc-button', $unique_id );

		if ( $full ): $classes[] = 'pencisc-full'; endif;
		if ( 'right' == $icon_position ): $classes[] = 'pencisc-icon-right'; endif;
		if ( $color ): $classes[] = "pencisc-background-$color"; endif;
		if ( $align ): $classes[] = "pencisc-align-$align"; endif;
		if ( $size ): $classes[] = "pencisc-$size"; endif;
		if ( $class ): $classes[] = $class; endif;
		$classes = implode( ' ', $classes );
		$style = '';
		if ( $background ){
			$style .= "background-color:$background;";
			$style .= "border-color:$background;";
		}
		if ( $radius ){
			$style .= "border-radius:$radius;";
		}
		if ( $text_color ){
			$style .= "color:$text_color;";
		}
		if ( $margin_bottom ) {
			$style .= "margin-bottom:$margin_bottom;";
		}
		if ( $margin_top ) {
			$style .= "margin-top:$margin_top;";
		}

		$content = do_shortcode( $content );

		$html = "<a href='$link' class='$classes'" .
		        ( $id ? " id='$id'" : '' ) .
		        ( $nofollow ? " rel='nofollow'" : '' ) .
		        ( $target ? " target='$target'" : '' ) .
		        ( $style ? " style='$style'" : '' ) .
		        '>';
		if ( $icon ) {
			$icon = '<i class="' . $icon . '"></i>';
			$content = $icon_position == 'right' ? ( $content . $icon ) : ( $icon . $content );
		}
		$html .= $content . '</a>';
		if ( $align == 'center' ){
			$html = '<div style="text-align:center">' . $html . '</div>';
		}

		if( $hover_bgcolor || $text_hover_color ) {
			$html .= '<style>a.pencisc-button.'. $unique_id . ':hover{ ';

			if ( $hover_bgcolor ){
				$html .= "background-color:$hover_bgcolor !important;";
				$html .= "border-color:$hover_bgcolor !important;";
			}

			if ( $text_hover_color ) {
				$html .= "color:$text_hover_color !important;";
			}

			$html .= '}</style>';
		}

		return apply_filters( 'pencisc_shortcode_button', $html, $atts, $content );
	}
}