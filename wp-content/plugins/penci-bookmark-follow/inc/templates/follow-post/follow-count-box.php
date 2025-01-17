<?php 

/**
 * Template For Follow Post Count Box
 * 
 * Handles to return design follow post count box
 * 
 * Override this template by copying it to yourtheme/follow-my-blog-post/follow-post/follow-count-box.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
	<div class="penci_bl_followers_message">
		<div class="penci-bf-tooltip-inner"><?php echo $follow_message; ?></div>
	</div><!--penci_bl_followers_message-->