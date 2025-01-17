<?php

/**
 * Template For Follow Author Wrapper
 *
 * Handles to return design follow author wrapper
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/follow-author/follow-author.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="penci-bf-follow-author-wrapper penci-bf-follow-btn-wrapper">
	<?php
	do_action( 'penci_bl_follow_author_content', $args );
	?>
</div><!-- penci-bf-follow-author-wrapper -->