<?php


namespace PenciDesign;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class Attachment {

	/**
	 * The one true Attachment.
	 *
	 * @var Attachment
	 * @since 1.0.0
	 **/
	private static $instance;

    /**
     * Create media library record and attach it to the post
     * @param $upload_dir
     * @param $post_id
     * @param $format
     * @return void
     */
    public function create_attachment( $upload_dir, $post_id, $format ) {

        /** Add file to the media library */
        $audio_url = $upload_dir[ 'baseurl' ] . '/penci-text-to-speech/post-' . $post_id . '.' . $format;

        /** Create or update record in the media library */
        $audio_attachments = get_attached_media( 'audio', $post_id );
        if ( empty( $audio_attachments ) ) {

            $this->add_audio_attachment( $upload_dir, $post_id, $format );

        } else {

            if ( ! is_array( $audio_attachments ) ) { return; }

            /** Find and update meta */
            $media_updated = false;
            foreach ( $audio_attachments as &$attachment_media ) {

                if ( $attachment_media->guid === $audio_url ) {

                    $this->update_audio_attachment( $attachment_media->ID, $post_id );
                    $media_updated = true;

                    break;

                }

            }

            /** If media is not found add new */
            if ( ! $media_updated ) {

                $this->add_audio_attachment( $upload_dir, $post_id, $format );

            }

        }

    }

    /**
     * Add audio attachment to the media library
     *
     * @param $upload_dir
     * @param $post_id
     * @param $format
     * @return void
     */
    private function add_audio_attachment( $upload_dir, $post_id, $format ) {

        /** Add file to the media library */
        $audio_file = $upload_dir[ 'basedir' ] . '/penci-text-to-speech/post-' . $post_id . '.' . $format;
        $audio_url = $upload_dir[ 'baseurl' ] . '/penci-text-to-speech/post-' . $post_id . '.' . $format;

        /** Store file meta to the post meta */
        $audio_meta = wp_read_audio_metadata( $audio_file );
        if ( ! is_array( $audio_meta ) ) { return; }

        /** Prepare post variables */
        $post_title = preg_replace( '/\.[^.]+$/', '', get_the_title( $post_id ) );
        $post_excerpt = preg_replace( '/\.[^.]+$/', '', get_the_excerpt( $post_id ) );

        /** Prepare attachments options */
        $attachment = array(
            'guid'           => $audio_url,
            'post_mime_type' => $audio_meta['mime_type'],
            'post_title'     => esc_html( $post_title ),
            'post_content'   => esc_html( $post_excerpt ),
            'post_author'    => 1,
            'post_status'    => 'inherit'
        );

        /** Create a new attachment record */
        wp_insert_attachment( $attachment, $audio_file, $post_id );

    }

    /**
     * Update audio attachment data in the media library
     *
     * @param $attachment_id
     * @param $post_id
     * @return void
     */
    private function update_audio_attachment( $attachment_id, $post_id ) {

        /** Prepare post variables */
        $post_title = preg_replace( '/\.[^.]+$/', '', get_the_title( $post_id ) );
        $post_excerpt = preg_replace( '/\.[^.]+$/', '', get_the_excerpt( $post_id ) );

        /** Prepare attachment options */
        $attachment = array(
            'ID'             => $attachment_id,
            'post_title'     => esc_html( $post_title ),
            'post_content'   => esc_html( $post_excerpt ),
            'post_author'    => 1,
        );

        /** Update existing record */
        wp_update_post( wp_slash( $attachment ) );

    }

	/**
	 * Main Attachment Instance.
	 *
	 * Insures that only one instance of Attachment exists in memory at any one time.
	 *
	 * @static
	 * @return Attachment
	 * @since 2.0.0
	 **/
	public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

} // End Class Attachment.
