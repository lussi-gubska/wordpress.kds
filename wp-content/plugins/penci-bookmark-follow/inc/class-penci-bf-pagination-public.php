<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Penci_Bf_Pagination_Public {

	/*Default values*/
	var $total_pages = - 1;//items
	var $limit = null;
	var $target = "";
	var $page = 1;
	var $adjacents = 2;
	var $showCounter = false;
	var $className = "penci-pagination";
	var $parameterName = "page";
	var $urlF = false;//urlFriendly

	/*Buttons next and previous*/
	var $nextT = "";
	var $nextI = "&#187;";
	var $prevT = "";
	var $prevI = "&#171;";

	function __construct( $ajaxpagination = 'penci_bl_ajax_pagination' ) {
		$this->nextT          = esc_html__( "Next", 'penci-bookmark-follow' );
		$this->prevT          = esc_html__( "Previous", 'penci-bookmark-follow' );
		$this->ajaxpagination = $ajaxpagination;
	}

	/*****/
	var $calculate = false;

	#Total items
	function items( $value ) {
		$this->total_pages = (int) $value;
	}

	#how many items to show per page
	function limit( $value ) {
		$this->limit = (int) $value;
	}

	#Page to sent the page value
	function target( $value ) {
		$this->target = $value;
	}

	#Current page
	function currentPage( $value ) {
		$this->page = (int) $value;
	}

	#How many adjacent pages should be shown on each side of the current page?
	function adjacents( $value ) {
		$this->adjacents = (int) $value;
	}

	#show counter?
	function showCounter( $value = "" ) {
		$this->showCounter = ( $value === true ) ? true : false;
	}

	#to change the class name of the pagination div
	function changeClass( $value = "" ) {
		$this->className = $value;
	}

	function nextLabel( $value ) {
		$this->nextT = $value;
	}

	function nextIcon( $value ) {
		$this->nextI = $value;
	}

	function prevLabel( $value ) {
		$this->prevT = $value;
	}

	function prevIcon( $value ) {
		$this->prevI = $value;
	}

	#to change the class name of the pagination div
	function parameterName( $value = "" ) {
		$this->parameterName = $value;
	}

	var $pagination;

	function pagination() {
	}

	function show() {
		if ( ! $this->calculate ) {
			if ( $this->calculate() ) {
				echo "<div class=\"$this->className\">$this->pagination</div>\n";
			}
		}
	}

	function getOutput($class='') {
		if ( ! $this->calculate ) {
			if ( $this->calculate($class) ) {
				return "<div class=\"$this->className\">$this->pagination</div>\n";
			}
		}
	}

	function is_render() {
		return $this->pagination ? true : false;
	}

	function get_pagenum_link( $id ) {
		if ( strpos( $this->target, '?' ) === false ) {
			if ( $this->urlF ) {
				return "javascript:void(0);";
			} else {
				return "javascript:void(0);";
			}
		} else {
			$addpar = '';
			if ( isset( $_GET['search_action_name'] ) && ! empty( $_GET['search_action_name'] ) ) {
				$addpar .= 'search_action_name=' . $_GET['search_action_name'] . '&';
			}
			if ( isset( $_GET['search_action_email'] ) && $_GET['search_action_email'] != '' ) {
				$addpar .= 'search_action_email=' . $_GET['search_action_email'] . '&';
			}
			if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
				$addpar .= 'orderby=' . $_GET['orderby'] . '&';
			}
			if ( isset( $_GET['order'] ) && ! empty( $_GET['order'] ) ) {
				$addpar .= 'order=' . $_GET['order'] . '&';
			}

			return "javascript:void(0);";
		}
	}

	function calculate($class='') {

		$this->pagination = "";
		$this->calculate == true;
		$error = false;
		if ( $this->urlF and $this->urlF != '%' and strpos( $this->target, $this->urlF ) === false ) {
			//Wildcard to replace one you specified, but does not exist in the target
			echo "Wildcard to replace one you specified, but does not exist in the target<br />";
			$error = true;
		} elseif ( $this->urlF and $this->urlF == '%' and strpos( $this->target, $this->urlF ) === false ) {
			echo "You must specify the target in the wildcard % to replace the page number<br />";
			$error = true;
		}

		if ( $this->total_pages < 0 ) {
			echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
			$error = true;
		}

		if ( $this->limit == null ) {
			echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
			$error = true;
		}

		if ( $error ) {
			return false;
		}

		/* Setup page vars for display. */
		$next     = $this->page + 1;                            //next page is page + 1
		$lastpage = ceil( $this->total_pages / $this->limit );        //lastpage is = total pages / items per page, rounded up.

		/*
			Now we apply our rules and draw the pagination object.
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
		if ( $lastpage > 1 && function_exists( 'penci_get_setting' ) ) {	
			$btn_text = penci_get_setting( 'penci_trans_load_more_posts' );
			$btn_alt = penci_get_setting( 'penci_trans_no_more_posts' );
			if ( $class == 'author'){
				$btn_text = pencibf_get_text('moreauthors');
				$btn_alt = pencibf_get_text( 'smoreauthors' );
			}
			if ( $class == 'term'){
				$btn_text = pencibf_get_text('moreterms');
				$btn_alt = pencibf_get_text( 'smoreterms' );
			}


			$this->pagination .= "<a class='penci-pf-ajx-loadmore page-numbers1' data-nomore='" . $btn_alt . "' href='#' data-paged='" . $next . "'><span class='ajax-more-text'>" . $btn_text . "</span><span
                                            class='ajaxdot'></span>" . penci_icon_by_ver( 'fas fa-sync' ) . "</a>";

		}

		return true;
	}
}