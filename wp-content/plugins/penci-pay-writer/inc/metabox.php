<?php

class PenciPayWriter_Metabox_Class {

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
				'penci_paywriter_meta'
				, esc_html__( 'Penci Pay Writer', 'soledad' )
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
		if ( ! isset( $_POST['penci_paywriter_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['penci_paywriter_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'penci_paywriter_custom_box' ) ) {
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
			'pencipwt_enable_post_donation',
		];

		foreach ( $options as $option ) {
			if ( isset( $_POST[ $option ] ) && $_POST[ $option ] ) {
				update_post_meta( $post_id, $option, $_POST[ $option ] );
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
		wp_nonce_field( 'penci_paywriter_custom_box', 'penci_paywriter_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$enable_post_donation = get_post_meta( $post->ID, 'pencipwt_enable_post_donation', true );
		// Display the form, using the current value.
		?>

        <div class="penci-table-meta">

            <div class="pcmt-control-wrapper">

                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
                           for="pencipwt_enable_post_donation"
                           class="penci-format-row penci-format-row2"><?php echo esc_html__( 'Enable Post Donation', 'penci-pay-writer' ); ?></label>
                    <p><?php esc_html__( 'Check this option to let your viewer donate to this post', 'penci-pay-writer' ); ?></p>
                </div>
                <div class="pcmt-control">
                    <select name="pencipwt_enable_post_donation" id="pencipwt_enable_post_donation">
                        <option value="" <?php selected( $enable_post_donation, '' ) ?>><?php esc_html_e( 'Default Customizer', 'penci-pay-writer' ) ?></option>
                        <option value="disable" <?php selected( $enable_post_donation, 'disable' ) ?>><?php esc_html_e( 'Disable', 'penci-pay-writer' ) ?></option>
                        <option value="enable" <?php selected( $enable_post_donation, 'enable' ) ?>><?php esc_html_e( 'Enable', 'penci-pay-writer' ) ?></option>
                    </select>
                </div>
            </div>

        </div>
		<?php

	}
}

/**
 * Adds Penci review meta box to the post editing screen
 */
function Penci_PayWriter_Add_Custom_Metabox() {
	new PenciPayWriter_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_PayWriter_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_PayWriter_Add_Custom_Metabox' );
}