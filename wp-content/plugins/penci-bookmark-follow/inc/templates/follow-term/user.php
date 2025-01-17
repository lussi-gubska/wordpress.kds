<?php

/**
 * Template For Follow Term Register User Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<button type="button" class="penci-bf-follow-btn penci-bf-button <?php echo $follow_class; ?>"
        data-follow-text="<?php echo $follow_text; ?>" data-following-text="<?php echo $following_text; ?>"
        data-unfollow-text="<?php echo $unfollow_text; ?>"
        data-status="<?php echo $follow_status; ?>" data-posttype="<?php echo $follow_posttype; ?>"
        data-taxonomy-slug="<?php echo $follow_taxonomy_slug; ?>" data-term-id="<?php echo $follow_term_id; ?>"
        data-current-postid="<?php echo $current_post_id; ?>"
        data-thumb="<?php echo penci_bl_get_term_thumb_url( $follow_term_id ); ?>">
    <span class="pencibf-following-text"><?php echo $follow_text; ?></span>
</button>