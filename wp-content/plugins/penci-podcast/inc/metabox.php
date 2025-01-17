<?php

class PenciPodcast_Metabox_Class {

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
		$post_types = array( 'podcast' );     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'penci_podcast_meta'
				, esc_html__( 'Penci Podcast', 'soledad' )
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
		if ( ! isset( $_POST['penci_podcast_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['penci_podcast_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'penci_podcast_custom_box' ) ) {
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
			'media_duration',
			'media_url',
		];

		foreach ( $options as $option ) {
			if ( isset( $_POST[ $option ] ) ) {
				update_post_meta( $post_id, 'pencipc_' . $option, $_POST[ $option ] );
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
		wp_nonce_field( 'penci_podcast_custom_box', 'penci_podcast_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$media_duration = get_post_meta( $post->ID, 'pencipc_media_duration', true );
		$media_url      = get_post_meta( $post->ID, 'pencipc_media_url', true );
		// Display the form, using the current value.
		?>

        <div class="penci-table-meta">

            <div class="pcmt-control-wrapper">

                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;" for="media_duration"
                           class="penci-format-row"><?php echo esc_html__( 'Media Duration', 'penci-podcast' ); ?></label>
                    <p class="description"><?php echo esc_html__( 'Human-read time value, ex. mm:ss.', 'penci-podcast' ); ?></p>

                </div>
                <div class="pcmt-control">
                    <input style="width:100px;" type="text" name="media_duration" id="media_duration"
                           value="<?php if ( isset( $media_duration ) ): echo $media_duration; endif; ?>">
                </div>
            </div>

            <div class="pcmt-control-wrapper">
                <div class="pcmt-title">
                    <label style="color: #000;font-weight: 600; font-size: 14px; padding-left: 0;"
                           for="media_url"
                           class="penci-format-row"><?php echo esc_html__( 'Media URL', 'penci-podcast' ); ?></label>
                    <p class="description"><?php echo esc_html__( 'Fill this option with the media url.', 'penci-podcast' ); ?></p>

                </div>
                <div class="pcmt-control">
                    <input style="width:100%;" type="text" name="media_url" id="media_url"
                           value="<?php if ( isset( $media_url ) ): echo $media_url; endif; ?>">
                </div>
            </div>

        </div>
		<?php

	}
}

/**
 * Adds Penci review meta box to the post editing screen
 */
function Penci_Podcast_Add_Custom_Metabox() {
	new PenciPodcast_Metabox_Class();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'Penci_Podcast_Add_Custom_Metabox' );
	add_action( 'load-post-new.php', 'Penci_Podcast_Add_Custom_Metabox' );
}