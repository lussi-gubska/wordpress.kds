<?php

/**
 * Template For Follow Term Wrapper
 *
 * Handles to return design follow term wrapper
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$class = isset( $args['html_class'] ) && $args['html_class'] ? ' ' . $args['html_class'] : '';
?>

<div class="penci-bf-follow-term-wrapper penci-bf-follow-btn-wrapper<?php echo $class; ?>">
	<?php
	do_action( 'penci_bl_follow_term_content', $args );
	?>
</div><!-- penci-bf-follow-term-wrapper -->