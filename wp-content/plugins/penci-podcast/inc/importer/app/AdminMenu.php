<?php

namespace PenciPodcast;

use PenciPodcast\Helper\Embed as PPI_Helper_Embed;
use PenciPodcast\Template as PPI_Template;

class AdminMenu {

	/**
	 * @var AdminMenu;
	 */
	protected static $_instance;

	/**
	 * @return AdminMenu
	 */
	public static function instance(): AdminMenu {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function setup() {

		add_filter( "plugin_action_links_" . plugin_basename( PENCI_PODCAST_IMPORTER_BASE_FILE_PATH ), [
			$this,
			'_register_plugin_action_links'
		] );

		add_submenu_page(
			'edit.php?post_type=podcast',
			PENCI_PODCAST_IMPORTER_NAME_SHORT,
			PENCI_PODCAST_IMPORTER_NAME_SHORT,
			PENCI_PODCAST_IMPORTER_FEED_PERMISSION_CAP,
			PENCI_PODCAST_IMPORTER_PREFIX,
			[ $this, '_display_management_page' ],
		);
	}

	public function _display_management_page( $response ) {
		PPI_Template::load_template( 'tools.php' );
	}

	public function _register_plugin_action_links( $response ): array {
		if ( ! is_array( $response ) ) {
			$response = [];
		}

		$response[] = '<a href="edit.php?post_type=podcast&page=' . PENCI_PODCAST_IMPORTER_PREFIX . '">' . esc_attr__( 'Import Podcast', 'penci-podcast' ) . '</a>';

		return $response;
	}

}