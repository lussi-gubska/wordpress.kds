<?php

namespace PenciPodcast;

use PenciPodcast\Helper\Scheduler as PPI_Helper_Scheduler;

class Controller {

  /**
   * @var Controller;
   */
  protected static $_instance;

  /**
   * @return Controller
   */
  public static function instance(): Controller {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function setup() {
    load_plugin_textdomain( 'pencipdc-podcast-importer', false, PENCI_PODCAST_IMPORTER_LANGUAGE_DIRECTORY );

    if( isset( $_GET[ PENCI_PODCAST_IMPORTER_ALIAS . '-action' ] )
        && $_GET[ PENCI_PODCAST_IMPORTER_ALIAS . '-action' ] === 'reset-scheduled-posts' ) {
      add_action( "init", function() {
        if( !current_user_can( PENCI_PODCAST_IMPORTER_SETTINGS_PERMISSION_CAP ) )
          return;

        PPI_Helper_Scheduler::schedule_posts();

        pencipdc_importer_redirect( get_admin_url( null, 'site-health.php'), 302 );
        exit;
      });
    }
  }

}