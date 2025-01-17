<?php

/**
 * Template For Unsubscribe Form
 *
 * Handles to return design unsubscribe form
 *
 * Override this template by copying it to yourtheme/follow-my-blog-post/unsubscribe/content.php
 *
 * @package Penci Bookmark Follow
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $penci_bl_message;

if ( $penci_bl_message->size( 'penci-bf-unsubscribe' ) > 0 ) { //make success message
	echo $penci_bl_message->output( 'penci-bf-unsubscribe' );
}
?>
<div class="penci-bf-unsubscribe-email-error"></div>
<form method="post" action="" class="penci-bf-unsubscribe-form">
    <div class="penci-bf-unsubscribe-table">
        <div class="penci-bf-email-field">
            <input type="text" name="penci_bl_unsubscribe_email" id="penci_bl_unsubscribe_email"
                   value="<?php if ( isset( $_POST['penci_bl_unsubscribe_email'] ) && ! empty( $_POST['penci_bl_unsubscribe_email'] ) ) {
			           echo $_POST['penci_bl_unsubscribe_email'];
		           } ?>" placeholder="<?php esc_html_e( 'Enter email address...', 'penci-bookmark-follow' ) ?>"/>
        </div>
        <div class="penci-bf-submit-field">
            <input type="submit" class="penci-bf-btn" id="penci_bl_unsubscribe_submit"
                   name="penci_bl_unsubscribe_submit"
                   value="<?php esc_html_e( 'Unsubscribe', 'penci-bookmark-follow' ) ?>">
        </div>
    </div>
</form>