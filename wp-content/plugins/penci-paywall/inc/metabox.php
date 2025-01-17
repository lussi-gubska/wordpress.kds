<?php

class PenciPaywall_Metabox_Class {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array( 'post' );     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'penci_paywall_meta'
				, esc_html__( 'Penci Paywall', 'soledad' )
				, array( $this, 'render_meta_box_content' )
				, $post_type
				, 'advanced'
				, 'default'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['penci_paywall_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['penci_paywall_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'penci_paywall_custom_box' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$options = [
			'enable_premium_post',
			'enable_free_post',
			'paragraph_limit',
			'preview_textbox',
			'enable_guest_mode_post',
			'video_preview_url',
		];

		foreach ( $options as $option ) {
			if ( isset( $_POST[ $option ] ) ) {
				update_post_meta( $post_id, 'pencipw_' . $option, $_POST[ $option ] );
			}
		}
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'penci_paywall_custom_box', 'penci_paywall_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$enable_premium_post = get_post_meta( $post->ID, 'pencipw_enable_premium_post', true );
		$enable_free_post    = get_post_meta( $post->ID, 'pencipw_enable_free_post', true );
		$paragraph_limit     = get_post_meta( $post->ID, 'pencipw_paragraph_limit', true );
		$guest_mode     	 = get_post_meta( $post->ID, 'pencipw_enable_guest_mode_post', true );
		$preview_textbox     = get_post_meta( $post->ID, 'pencipw_preview_textbox', true );
		$video_preview_url   = get_post_meta( $post->ID, 'pencipw_video_preview_url', true );
		$penci_block_all     = get_theme_mod( 'pencipw_block_all', false );
		// Display the form, using the current value.
		?>

        <div class="penci-table-meta">

			<div class="pcmt-control-wrapper">

				<div class="pcmt-title">
					<label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
							for="enable_guest_mode_post"
							class="penci-format-row penci-format-row2"><?php echo esc_html__( 'Is login required to view the full content?', 'penci-paywall' ); ?></label>
				</div>
				<div class="pcmt-control">
					<select name="enable_guest_mode_post" id="enable_guest_mode_post">
						<option value="" <?php selected( $guest_mode, 'disable' ) ?>><?php esc_html_e( 'Default', 'penci-paywall' ) ?></option>
						<option value="disable" <?php selected( $guest_mode, 'disable' ) ?>><?php esc_html_e( 'Disable', 'penci-paywall' ) ?></option>
						<option value="enable" <?php selected( $guest_mode, 'enable' ) ?>><?php esc_html_e( 'Enable', 'penci-paywall' ) ?></option>
					</select>
				</div>
			</div>

			<?php if ( ! $penci_block_all ) { ?>

                <div class="pcmt-control-wrapper">

                    <div class="pcmt-title">
                        <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
                               for="enable_premium_post"
                               class="penci-format-row penci-format-row2"><?php echo esc_html__( 'Set as Premium Post', 'penci-paywall' ); ?></label>
                    </div>
                    <div class="pcmt-control">
                        <select name="enable_premium_post" id="enable_premium_post">
                            <option value="" <?php selected( $enable_premium_post, '' ) ?>><?php esc_html_e( 'Default Customizer Setting', 'penci-paywall' ) ?></option>
                            <option value="disable" <?php selected( $enable_premium_post, 'disable' ) ?>><?php esc_html_e( 'Disable', 'penci-paywall' ) ?></option>
                            <option value="enable" <?php selected( $enable_premium_post, 'enable' ) ?>><?php esc_html_e( 'Enable', 'penci-paywall' ) ?></option>
                        </select>
                    </div>

                </div>

			<?php } else { ?>

                <div class="pcmt-control-wrapper">

                    <div class="pcmt-title">
                        <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
                               for="enable_free_post"
                               class="penci-format-row penci-format-row2"><?php echo esc_html__( 'Set as Free Post', 'penci-paywall' ); ?></label>
                    </div>
                    <div class="pcmt-control">
                        <select name="enable_free_post" id="enable_free_post">
                            <option value="disable" <?php selected( $enable_free_post, 'disable' ) ?>><?php esc_html_e( 'Disable', 'penci-paywall' ) ?></option>
                            <option value="enable" <?php selected( $enable_free_post, 'enable' ) ?>><?php esc_html_e( 'Enable', 'penci-paywall' ) ?></option>
                        </select>
                    </div>
                </div>

			<?php } ?>

            <div class="pcmt-control-wrapper">

                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;" for="paragraph_limit"
                           class="penci-format-row"><?php echo esc_html__( 'Custom paragraph limit for free users.', 'penci-paywall' ); ?></label>
                    <p class="description"><?php echo esc_html__( 'Total number of paragraphs that will be shown to free users.', 'penci-paywall' ); ?></p>

                </div>
                <div class="pcmt-control">
                    <input style="width:100px;" type="number" name="paragraph_limit" id="paragraph_limit"
                           value="<?php if ( isset( $paragraph_limit ) ): echo $paragraph_limit; endif; ?>">
                </div>
            </div>

            <div class="pcmt-control-wrapper">

                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;" for="preview_textbox"
                           class="penci-format-row"><?php echo esc_html__( 'Custom Content Preview.', 'penci-paywall' ); ?></label>
                    <p class="description"><?php echo esc_html__( 'Text preview that will be shown to free users.', 'penci-paywall' ); ?></p>

                </div>
                <div class="pcmt-control">
            <textarea style="width:100%; height:120px;" name="preview_textbox"
                      id="preview_textbox"><?php if ( isset( $preview_textbox ) ): echo $preview_textbox; endif; ?></textarea>
                </div>
            </div>

            <div class="pcmt-control-wrapper">
                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
                           for="video_preview_url"
                           class="penci-format-row"><?php echo esc_html__( 'Video Preview URL', 'penci-paywall' ); ?></label>
                    <p class="description"><?php echo esc_html__( 'Please enter the URL of the video preview that will be shown to free users.', 'penci-paywall' ); ?></p>

                </div>
                <div class="pcmt-control">
                    <input style="width:100%;" type="text" name="video_preview_url" id="video_preview_url"
                           value="<?php if ( isset( $video_preview_url ) ): echo $video_preview_url; endif; ?>">
                </div>
            </div>

        </div>
		<?php

	}
}

/**
 * Adds Penci review meta box to the post editing screen
 */
function Penci_Paywall_Add_Custom_Metabox() {
	new PenciPaywall_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_Paywall_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_Paywall_Add_Custom_Metabox' );
}