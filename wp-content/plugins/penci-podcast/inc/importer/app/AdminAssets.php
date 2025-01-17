<?php

namespace PenciPodcast;

class AdminAssets {

	/**
	 * @var AdminAssets;
	 */
	protected static $_instance;

	/**
	 * @return AdminAssets
	 */
	public static function instance(): AdminAssets {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function setup( $hook ) {
		if ( $hook === 'tools_page_' . PENCI_PODCAST_IMPORTER_PREFIX ) {
			wp_enqueue_media();
			wp_enqueue_script( 'media-grid' );
			wp_enqueue_script( 'media' );
			wp_enqueue_style( 'media' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}

		wp_register_style( 'pencipdc_admin_styles', plugins_url( '/assets/css/admin.css', PENCI_PODCAST_IMPORTER_BASE_FILE_PATH ), false, PENCI_PODCAST_IMPORTER_VERSION );
		wp_enqueue_style( 'pencipdc_admin_styles' );

		wp_register_script( 'pencipdc_admin_scripts', plugins_url( '/assets/js/admin.js', PENCI_PODCAST_IMPORTER_BASE_FILE_PATH ), false, PENCI_PODCAST_IMPORTER_VERSION, true );

		wp_localize_script( 'pencipdc_admin_scripts', 'podcast_import_settings', [
			'rest_url'                                => esc_url_raw( rest_url() ),
			'rest_nonce'                              => wp_create_nonce( 'wp_rest' ),
			'import_limit'                            => PENCI_PODCAST_IMPORTER_SETUP_IMPORT_PER_REQUEST,
			'lang_import_progress'                    => __( "Processed %s episodes out of %s", 'penci-podcast' ),
			'lang_import_summary_progress'            => __( 'Success! Re-synced %s previously imported episodes.', 'penci-podcast' ),
			'lang_import_summary_progress_with_skips' => __( 'Success! Re-synced %s previously imported episodes, and skipped %s based on rules.', 'penci-podcast' ),
			'lang_import_summary_progress_skips'      => __( "Skipped %s episode imports, based on rules.", 'penci-podcast' ),
			'lang_import_summary_no_imports'          => __( 'No new episodes imported - all episodes already existing in WordPress!', 'penci-podcast' ) . '<br/>' .
			                                             __( 'If you have existing draft, private or trashed posts with the same title as your episodes, delete those and run the importer again.', 'penci-podcast' ),
			'lang_import_summary_no_episodes'         => __( 'Error! Your feed does not contain any episodes.', 'penci-podcast' ),
			'lang_import_summary_success'             => __( 'Success! Imported %s out of %s episodes', 'penci-podcast' ),
			'lang_import_summary_success_resynced'    => __( "%s previously imported episodes re-synced", 'penci-podcast' )
		] );

		wp_enqueue_script( 'pencipdc_admin_scripts' );
	}

}