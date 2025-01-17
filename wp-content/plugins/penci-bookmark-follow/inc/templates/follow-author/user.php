<?php

/**
 * Template For Follow Author Register User Content
 *
 * Handles to return design follow author register user content
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/follow-author/user.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<button type="button" class="penci-bf-follow-btn penci-bf-button <?php echo $follow_class; ?>"
        data-thumb="<?php echo get_avatar_url( $author_id, 'post-thumbnail' ); ?>"
        data-status="<?php echo $follow_status; ?>" data-author-id="<?php echo $author_id; ?>"
        data-current-postid="<?php echo $current_post_id; ?>" data-follow-text="<?php echo $follow_text; ?>"
        data-following-text="<?php echo $following_text; ?>" data-unfollow-text="<?php echo $unfollow_text; ?>">
    <span class="pencibf-following-text"><?php echo $follow_label; ?></span>
</button>
<?php

do_action( 'penci_bl_follow_author_after_post_count_box', $follow_class, $follow_status, $author_id, $current_post_id, $follow_text, $following_text, $unfollow_text, $follow_label );
?>
	