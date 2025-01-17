<?php

/**
 * Template For Follow Post Register User Content
 *
 * Handles to return design follow post register user content
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/follow-post/user.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo apply_filters(
	'penci_bl_follow_post_button',
	'<button type="button" class="penci-bf-follow-btn penci-bf-button ' . $follow_class . '" data-thumb="' . get_the_post_thumbnail_url( $post_id, 'thumbnail' ) . '"
            data-posttitle="' . esc_attr( wp_strip_all_tags( get_the_title( $post_id ) ) ) . '" data-status="' . $follow_status . '" data-postid="' . $post_id . '" data-current-postid="' . $current_post_id . '" data-follow-text="' . $follow_text . '" data-following-text="' . $following_text . '" data-unfollow-text="' . $unfollow_text . '" >
				<span class="pencibf-following-text">' . $follow_label . '</span>
			</button>', $follow_class, $follow_status, $post_id, $current_post_id, $follow_text, $following_text, $unfollow_text, $follow_label
);
?>

<?php

// Check follow message is not empty from meta or settings
if ( ! empty( $follow_message ) ) {

	do_action( 'penci_bl_follow_post_count_box', $follow_message, $post_id );

}
?>
<?php

do_action( 'penci_bl_follow_after_post_count_box', $follow_class, $follow_status, $post_id, $current_post_id, $follow_text, $following_text, $unfollow_text, $follow_label );
