<?php
class Penci_AMP_Menu_Walker extends Walker_Nav_Menu {

	protected $__acc_started = false;
	protected $__acc_childs_starte = false;


	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		if ( $this->__acc_childs_starte && $depth == 0 ) {

			$this->end_acc_child_wrapper( $output, $depth );
		}

		if ( $this->__acc_started && $depth == 0 ) {
			$this->end_accordion( $output, $depth );
		}


		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$output .= "$indent</ul>{$n}";


	}

	public function end_acc_child_wrapper( &$output, $depth = 0 ) {

		$output .= "</div>\n";

		$this->__acc_childs_starte = false;
	}

	public function end_accordion( &$item_output, $depth = 0 ) {

		$item_output .= "</section></amp-accordion>";

		$this->__acc_started = false;
	}


	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();

		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = $args->before;

		if ( $this->has_children && $depth == 0 ) {
			$this->start_accordion( $item_output, $depth );

			$item_output .= '<h6 class="penci-amphtml-accordion-header">';
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . $title . $args->link_after;
			$item_output .= '</a>';
			$item_output .= '<span class="dropdown-toggle fa fa-angle-down"></span></h6>';

			$this->begin_accordion_child_wrapper( $item_output, $depth );

		}else{
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . $title . $args->link_after;
			$item_output .= '</a>';
		}

		$item_output .= $args->after;


		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	}


	public function start_accordion( &$item_output, $depth = 0 ) {

		$item_output .= "<amp-accordion><section>";

		$this->__acc_started = true;
		$this->enqueue_accordion = true;
	}




	public function begin_accordion_child_wrapper( &$item_output, $depth = 0 ) {

		$item_output .= "\n<div>\n";

		$this->__acc_childs_starte = true;
	}




	public function get_anchor_tag( $item, $depth, $args, $id ) {

		$current_el = '';

		parent::start_el( $current_el, $item, $depth, $args, $id );

		if ( preg_match( '#<\s*li\s* [^>]* > (.+) #ix', $current_el, $matched ) ) {
			return isset( $matched[1] ) ? $matched[1] : '';
		}

		return $this->__anchor_tag( $item, $args, $depth );
	}


	protected function __anchor_tag( $_item, $args, $depth ) {

		$atts           = array();
		$atts['title']  = ! empty( $_item->attr_title ) ? $_item->attr_title : '';
		$atts['target'] = ! empty( $_item->target ) ? $_item->target : '';
		$atts['rel']    = ! empty( $_item->xfn ) ? $_item->xfn : '';
		$atts['href']   = ! empty( $_item->url ) ? $_item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $_item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			if( 'href' === $attr )  {
				$value = esc_url( $value );
			}else{
				$value = esc_attr( $value );
			}

			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $_item->title, $_item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $_item, $args, $depth );

		$_output = $args->before;
		$_output .= '<a' . $attributes . '>';
		$_output .= $args->link_before . $title . $args->link_after;
		$_output .= '</a>';
		$_output .= $args->after;

		return $_output;
	}
}