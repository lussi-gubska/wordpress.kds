<?php

/**
 * The Class.
 */
class Penci_LiveBlog_Add_Custom_Metabox_Class {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array( 'post' );     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'pcliveblog_meta'
				, esc_html__( 'Live Blog Events', 'soledad' )
				, array( $this, 'render_meta_box_content' )
				, $post_type
			);
		}
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Display the form, using the current value.
		$pcliveblog_enable = get_post_meta( $post->ID, PENCI_LB_STATUS_KEY, true );
		?>

        <p>To display the live event update on this post, please use the following shortcode: <span
                    class="penci-review-shortcode">[penci_liveblog]</span><br>If you do not need this feature, please go
            to <strong>Plugins > Installed Plugins > and deactivate the "Penci Live Blog"</strong> plugin</p>
        <p>

        <p>
            <label for="<?php echo esc_attr( 'pcliveblog_enable' ); ?>"
                   class="penci-format-row penci-format-row2"><?php _e( 'Event Status', 'penci-liveblog' ); ?></label>
            <select data-event-id="<?php echo $post->ID; ?>" name="<?php echo esc_attr( 'pcliveblog_enable' ); ?>"
                    id="<?php echo esc_attr( 'pcliveblog_enable' ); ?>">
                <option value="" <?php selected( $pcliveblog_enable, '' ) ?>><?php esc_html_e( 'Not set' ); ?></option>
                <option value="disable" <?php selected( $pcliveblog_enable, 'disable' ) ?>><?php esc_html_e( 'The event has ended' ); ?></option>
                <option value="enable" <?php selected( $pcliveblog_enable, 'enable' ) ?>><?php esc_html_e( 'The event is happening' ); ?></option>
            </select>
        <div class="desc"><?php _e( 'Please remember to select the "the event has ended" option when the live event ends.', 'penci-liveblog' ); ?></div>
        </p>

        <div class="pcliveblog-events-wrapper">
            <div class="pcliveblog-event">
                <div class="pcliveblog-event-field live-events-listing" data-event-id="<?php echo $post->ID; ?>">
					<?php
					$all_live_events = get_post_meta( $post->ID, PENCI_LB_META_KEY, true );
					if ( ! empty( $all_live_events ) ) {
						foreach ( $all_live_events as $number => $event ) {
							if ( ! empty( $event ) && isset( $event['time'] ) && isset( $event['title'] ) && isset( $event['content'] ) ) {
								?>
                                <div class="live-events-listing-item item-<?php echo $number; ?>"
                                     data-item="<?php echo $number; ?>">
									<?php if ( isset( $event['time'] ) ): ?>
                                        <div class="date">
                                            <span><?php echo is_numeric( $event['time'] ) ? date_i18n( 'H:i:s', $event['time'] ) : $event['time']; ?></span>
                                        </div>
									<?php endif; ?>
									<?php if ( isset( $event['title'] ) ): ?>
                                        <div class="live-events-listing-ct post-title">
                                            <span><?php echo $event['title']; ?></span>
                                        </div>
									<?php endif; ?>
									<?php if ( isset( $event['content'] ) ): ?>
                                        <div class="live-events-listing-ct post-entry">
											<?php echo html_entity_decode( stripslashes( $event['content'] ) ); ?>
                                        </div>
									<?php endif; ?>
                                    <div class="live-events-listing-action"><a class="pcliveblog-event-edit"
                                                                               href="#"><?php _e( 'Edit', 'penci-liveblog' ); ?></a><a
                                                class="pcliveblog-event-delete"
                                                href="#"><?php _e( 'Delete', 'penci-liveblog' ); ?></a></div>
                                </div>
								<?php
							}
						}
					}
					?>
                </div>
                <div class="live-events-new">
                    <div class="pcliveblog-event-field">
                        <div class="items">
                            <label for="pcliveblog_update_time"><?php _e( 'Custom Update Time', 'penci-liveblog' ); ?></label>
                            <p class="not"><?php _e( 'If you leave it empty it will be showing the real time', 'penci-liveblog' ); ?></p>
                            <input type="text" class="text pcliveblog_update_time" title="pcliveblog_update_time"
                                   name="pcliveblog_update_time">
                        </div>
                        <div class="items">
                            <label for="pcliveblog_update_title"><?php _e( 'Title of Update', 'penci-liveblog' ); ?></label>
                            <input type="text" class="text pcliveblog_update_title" title="pcliveblog_update_title"
                                   name="pcliveblog_update_title">
                        </div>
                        <div class="items">
                            <label for="pcliveblog_update"
                                   style="font-weight:bold;display: block;margin: 0 0 20px;"><?php _e( 'Content of Update', 'penci-liveblog' ); ?></label>
							<?php wp_editor( htmlspecialchars_decode( '' ), 'pcliveblog_update', array(
								"media_buttons" => true,
								"textarea_rows" => 5
							) ); ?>
                        </div>
                    </div>
                    <div class="pcliveblog-event-field">
                        <a data-id="<?php echo $post->ID; ?>" href="#"
                           class="button pcliveblog-addnew"><?php _e( 'Add new Update', 'penci-liveblog' ); ?></a>
                        <a class="button pcliveblog-reset hidden"><?php echo penci_livesub_text( 'event_cancel' ); ?></a>
                    </div>
                </div>
            </div>
        </div>

		<?php
	}
}

function Penci_LiveBlog_Add_Custom_Metabox() {
	new Penci_LiveBlog_Add_Custom_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_LiveBlog_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_LiveBlog_Add_Custom_Metabox' );
}