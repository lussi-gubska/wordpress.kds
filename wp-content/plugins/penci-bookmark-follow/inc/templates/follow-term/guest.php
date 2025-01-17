<?php

/**
 * Template For Follow Term Guest User Content
 *
 * Handles to return design follow term guest user content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<button type="button" class="penci-bf-follow-button penci-bf-button penci-bf-guest-btn <?php echo $extra_classes; ?>"
        data-status="<?php echo $follow_status; ?>" data-posttype="<?php echo $follow_posttype; ?>"
        data-taxonomy-slug="<?php echo $follow_taxonomy_slug; ?>" data-term-id="<?php echo $follow_term_id; ?>"
        data-posttitle="<?php echo $follow_term_name; ?>" data-current-postid="<?php echo $current_post_id; ?>"
        data-follow-text="<?php echo $follow_text; ?>" data-following-text="<?php echo $following_text; ?>"
        data-unfollow-text="<?php echo $unfollow_text; ?>"
        data-thumb="<?php echo penci_bl_get_term_thumb_url( $follow_term_id ); ?>">
    <span class="pencibf-following-text"><?php echo $follow_text; ?></span>
</button>