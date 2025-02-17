<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Penci_Bf_Message_Stack {

	var $messageToStack, $messages = array();

	// class constructor
	function __construct() {

	}

	public function init() {

		$this->messages = array();

		$pencibf_message_stack = get_transient( 'pencibf_message_stack' );

		if ( empty( $pencibf_message_stack ) ) {
			$pencibf_message_stack = array( 'messageToStack' => array() );
			set_transient( 'pencibf_message_stack', $pencibf_message_stack );
		}

		$this->messageToStack =& $pencibf_message_stack['messageToStack'];

		for ( $i = 0, $n = sizeof( $this->messageToStack ); $i < $n; $i ++ ) {
			$this->add( $this->messageToStack[ $i ]['class'], $this->messageToStack[ $i ]['text'], $this->messageToStack[ $i ]['type'] );
		}

		$this->messageToStack = array();
	}

	// class methods
	function add( $class, $message, $type = '' ) {

		if ( $type == 'error' ) {
			$this->messages[] = array(
				'params' => 'class="message_stack_error"',
				'class'  => $class,
				'text'   => '&nbsp;' . $message
			);
		} elseif ( $type == 'multierror' ) {
			$this->messages[] = array(
				'params' => 'class="message_stack_multierror"',
				'class'  => $class,
				'text'   => '&nbsp;' . $message
			);
		} elseif ( $type == 'success' ) {
			$this->messages[] = array(
				'params' => 'class="message_stack_success"',
				'class'  => $class,
				'text'   => '&nbsp;' . $message
			);
		} else {
			$this->messages[] = array(
				'params' => 'class="message_stack_error"',
				'class'  => $class,
				'text'   => '' . $message
			);
		}
	}

	function add_session( $class, $message, $type = '' ) {
		if ( $type == 'error' ) {
			$this->messageToStack[] = array(
				'params' => 'class="message_stack_error"',
				'class'  => $class,
				'text'   => '' . $message,
				'type'   => $type
			);
		} elseif ( $type == 'multierror' ) {
			$this->messageToStack[] = array(
				'params' => 'class="message_stack_multierror"',
				'class'  => $class,
				'text'   => '&nbsp;' . $message,
				'type'   => $type
			);
		} else {
			$this->messageToStack[] = array(
				'params' => 'class="message_stack_success"',
				'class'  => $class,
				'text'   => '' . $message,
				'type'   => $type
			);
		}

		$pencibf_message_stack['messageToStack'] = $this->messageToStack;
		set_transient( 'pencibf_message_stack', $pencibf_message_stack );
	}

	function reset() {
		$this->messages = array();
	}

	function output( $class ) {

		$str    = '';
		$output = array();
		for ( $i = 0, $n = count( $this->messages ); $i < $n; $i ++ ) {
			if ( $this->messages[ $i ]['class'] == $class ) {
				$output[] = $this->messages[ $i ];
			}
		}

		$len = count( $output );
		for ( $ii = 0; $ii < $len; $ii ++ ) {
			$str .= '<div ' . $output[ $ii ]['params'] . '>' . $output[ $ii ]['text'] . '</div>';
		}

		$pencibf_message_stack = array( 'messageToStack' => array() );
		set_transient( 'pencibf_message_stack', $pencibf_message_stack );

		return $str;
	}

	function size( $class ) {

		$count = 0;

		for ( $i = 0, $n = sizeof( $this->messages ); $i < $n; $i ++ ) {
			if ( $this->messages[ $i ]['class'] == $class ) {
				$count ++;
			}
		}

		return $count;
	}
}