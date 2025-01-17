<?php

/**
 * Template For Follow Post Guest User Content
 *
 * Handles to return design follow post guest user content
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/follow-post/guest.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

    <button data-status="1" data-thumb="<?php echo get_the_post_thumbnail_url( $post_id, 'post-thumbnail' ); ?>"
            data-posttitle="<?php echo esc_attr( wp_strip_all_tags( get_the_title( $post_id ) ) ); ?>"
            data-postid="<?php echo $post_id; ?>" data-current-postid="<?php echo $current_post_id; ?>"
            data-follow-text="<?php echo $follow_text; ?>" data-following-text="<?php echo $following_text; ?>"
            data-unfollow-text="<?php echo $unfollow_text; ?>"
            class="penci-bf-follow-button penci-bf-button penci-bf-guest-btn <?php echo $extra_classes; ?>">
        <span class="pencibf-following-text"><?php echo $follow_text; ?></span>
    </button>

<?php

// Check follow message is not empty from meta or settings
if ( ! empty( $follow_message ) ) {

	do_action( 'penci_bl_follow_post_count_box', $follow_message, $post_id );

}
	