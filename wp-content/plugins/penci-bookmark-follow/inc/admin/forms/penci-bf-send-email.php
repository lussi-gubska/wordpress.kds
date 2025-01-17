<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Page
 *
 * The code for the send emails to followers
 *
 * @package Penci Bookmark Follow
 * @since 1.5.0
 */
?>
<div class="wrap">

	<?php
	global $wpdb, $penci_bl_model, $penci_bl_message; // call globals to use them in this page

	// model class
	$model = $penci_bl_model;

	// message class
	$message = $penci_bl_message;

	$prefix = PENCI_BL_META_PREFIX;

	// get all custom post types
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	if ( isset( $post_types['attachment'] ) ) { // Check attachment post type exists
		unset( $post_types['attachment'] );
	}


	$followers_msg = get_transient( get_current_user_id() . '_penci_bl_sent_mail_message' );

	//Get message after sent email to followers
	if ( ! empty( $followers_msg ) ) {
		delete_transient( get_current_user_id() . '_penci_bl_sent_mail_message' );
	}

	?>
    <!-- plugin name -->
    <h2><?php print apply_filters( 'penci_bl_send_email_page_heading', esc_html__( 'Send Emails', 'penci-bookmark-follow' ) ); ?></h2>
    <br/>

	<?php
	if ( ! empty( $followers_msg ) ) {
		?>
        <div class="updated fade below-h2" id="message"><p><strong><?php echo $followers_msg; ?></strong></p></div>
		<?php
	}
	?>
    <!-- beginning of the general settings meta box -->
    <div id="penci-bf-general" class="post-box-container">
        <div class="metabox-holder">
            <div class="meta-box-sortables ui-sortable">
                <div id="general" class="postbox">
                    <div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'penci-bookmark-follow' ); ?>">
                        <br/></div>
                    <!-- general settings box title -->
                    <h3 class="hndle">
                        <span class='wps_fmbp_common_vertical_align'><?php print apply_filters( 'penci_bl_send_email_page_sub_heading', esc_html__( 'Send Emails', 'penci-bookmark-follow' ) ); ?></span>
                    </h3>
                    <div class="inside">
                        <form name="send-email" id="pencibf_send_mail" method="POST">
                            <table class="form-table">
                                <tbody>
                                <tr valign="top">
                                    <th scope="row">
                                        <label for="followed_type"><?php esc_html_e( 'Followed Type', 'penci-bookmark-follow' ); ?></label>
                                    </th>
                                    <td>
                                        <input type="radio" name="followed_type" id="followed_post"
                                               value="followed_post" class="followed_type" checked/><label
                                                for="followed_post"><?php esc_html_e( 'Followed Posts', 'penci-bookmark-follow' ); ?></label>
                                        <input type="radio" name="followed_type" id="followed_authors"
                                               value="followed_authors" class="followed_type"/><label
                                                for="followed_authors"><?php esc_html_e( 'Followed Authors', 'penci-bookmark-follow' ); ?></label><br/>
                                        <span class="description"><?php esc_html_e( 'Select followed type.', 'penci-bookmark-follow' ); ?></span>
                                    </td>
                                </tr>

                                <!-- All Post Types -->
                                <tr valign="top" class="followed_type_post">
                                    <th scope="row">
                                        <label for="followed_type_post"><?php esc_html_e( 'Select Post Type', 'penci-bookmark-follow' ); ?>
                                            <span class="penci_bl_email_error">*</span></label>
                                    </th>
                                    <td>
                                        <div class="penci-bf-post-select-wrap">
                                            <select name="followed_type_post" id="followed_type_post"
                                                    class="chosen-select">
                                                <option value=""><?php esc_html_e( '-- Select --', 'penci-bookmark-follow' ); ?></option>
												<?php

												foreach ( $post_types as $post_key => $post_type ) {
													$args['post_type'] = $post_key;
													$post_name         = $this->model->penci_bl_get_follow_post_data( $args );

													if ( ! empty( $post_name ) ) {//check if not empty post name
														?>
                                                        <option value="<?php echo $post_type->labels->name; ?>"
                                                                data-posttype="<?php echo $post_key; ?>">
															<?php echo $post_type->labels->name; ?>
                                                        </option>
														<?php
													}
												}
												?>
                                            </select>
                                        </div>
                                        <span class="penci-bf-follow-loader penci-bf-post-follow-loader"><img
                                                    src="<?php echo esc_url( PENCI_BL_IMG_URL ) . '/loader.gif'; ?>"
                                                    alt="..."/></span>
                                        <div class="clear"></div>
                                        <span class="description"><?php esc_html_e( 'Select post type.', 'penci-bookmark-follow' ); ?></span>
                                        <div class="followed_type_post_error penci_bl_email_error"></div>
                                    </td>
                                </tr>

                                <!-- Post Name -->
                                <tr class="penci-bf-display-none penci-bf-post-tr">
                                    <th scope="row">
                                        <label for="followed_type_post_name"><?php esc_html_e( 'Select Post Name', 'penci-bookmark-follow' ); ?>
                                            <span class="penci_bl_email_error">*</span></label>
                                    </th>
                                    <td>
                                        <select id="followed_type_post_name" name="followed_type_post_name"
                                                data-placeholder="<?php esc_html_e( '-- Select --', 'penci-bookmark-follow' ); ?>"
                                                class="chosen-select" tabindex="2">
                                        </select><br/>
                                        <span class="description"><?php esc_html_e( 'select post name', 'penci-bookmark-follow' ); ?></span>
                                        <div class="followed_type_post_name_error penci_bl_email_error"></div>
                                    </td>
                                </tr>

                                <!-- All Authors -->
                                <tr valign="top" class="followed_type_author penci-bf-display-none">
                                    <th scope="row">
                                        <label for="followed_type_author"><?php esc_html_e( 'Select Author', 'penci-bookmark-follow' ); ?>
                                            <span class="penci_bl_email_error">*</span></label>
                                    </th>
                                    <td>
                                        <select name="followed_type_author" id="followed_type_author"
                                                class="chosen-select">
                                            <option value=""><?php esc_html_e( '-- Select --', 'penci-bookmark-follow' ); ?></option>
											<?php
											$all_authors = $this->model->penci_bl_get_follow_author_data();
											foreach ( $all_authors as $key => $value ) {
												$authordata = get_user_by( 'id', $value['post_parent'] );
												?>
                                                <option value="<?php echo $value['post_parent']; ?>">
													<?php echo $authordata->display_name; ?>
                                                </option>
												<?php
											}
											?>
                                        </select><br/>
                                        <span class="description"><?php esc_html_e( 'Select author name', 'penci-bookmark-follow' ); ?></span>
                                        <div class="followed_type_author_error penci_bl_email_error"></div>
                                    </td>
                                </tr>

                                <!-- Email Subject -->
                                <tr valign="top">
                                    <th scope="row">
                                        <label for="followed_email_subject"><?php esc_html_e( 'Email Subject', 'penci-bookmark-follow' ); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="followed_email_subject" class="followed_email_subject"
                                               id="followed_email_subject" value="" size="76"/></br>
                                        <span class="description"><?php esc_html_e( 'This is the subject of the email that will be sent to the followers', 'penci-bookmark-follow' ); ?></span>

                                    </td>
                                </tr>

                                <!-- Email Body -->
                                <tr valign="top" class="followed_email_body">
                                    <th scope="row">
                                        <label for="followed_email_body"><?php esc_html_e( 'Email Body', 'penci-bookmark-follow' ); ?>
                                            <span class="penci_bl_email_error">*</span></label>
                                    </th>
                                    <td>
										<?php
										$settings = array( 'teeny' => true );
										wp_editor( '', 'followed_email_body', $settings );
										?></br>
                                        <span class="description"><?php esc_html_e( 'This is the body, main content of the email that will be sent to the followers.', 'penci-bookmark-follow' ); ?></span>
                                        <div class="followed_email_body_error penci_bl_email_error"></div>
                                    </td>
                                </tr>
								<?php do_action( 'penci_bl_after_admin_send_email' ); ?>
                                <!-- Terms Follow Settings Start -->
                                <tr>
                                    <th></th>
                                    <td>
                                        <input type="hidden" name="penci_bl_send_email_submit" value="1"/>
										<?php
										echo apply_filters( 'penci_bl_send_email_submit_button', '<input class="button-primary penci-bf-send-email-submit" type="submit" name="penci_bl_send_email_button" value="' . apply_filters( 'penci_bl_send_email_submit_button_text', esc_html__( 'Send Email', 'penci-bookmark-follow' ) ) . '" />' );
										?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div><!-- .inside -->
                </div><!-- #general -->
            </div><!-- .meta-box-sortables ui-sortable -->
        </div><!-- .metabox-holder -->
    </div><!-- #penci-bf-general -->
</div><!--end .wrap-->