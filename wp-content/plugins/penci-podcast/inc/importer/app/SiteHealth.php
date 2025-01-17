<?php

namespace PenciPodcast;

use PenciPodcast\Helper\Scheduler as Helper_Scheduler;

class SiteHealth {

  /**
   * @var null|SiteHealth;
   */
  protected static $_instance;

  /**
   * @return SiteHealth
   */
  public static function instance(): SiteHealth {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function tests( $tests ) {
    $tests[ 'direct' ][ PENCI_PODCAST_IMPORTER_ALIAS . '_test_scheduler' ] = [
      'label' => __( '%s - Scheduled Imports', 'penci-podcast' ),
      'test'  => [ $this, 'test_action_scheduler_jobs' ],
    ];

    return $tests;
  }

  public function test_action_scheduler_jobs(): array {
    $default = [
      'description' => '<p>' . sprintf( __( "%s runs the imports using the action scheduler.", 'penci-podcast' ), PENCI_PODCAST_IMPORTER_NAME ) . '</p>',
      'test'        => PENCI_PODCAST_IMPORTER_ALIAS . '_test_scheduler',
    ];

    if( Helper_Scheduler::is_everything_scheduled() )
      return [
          'label'   => sprintf( __( "%s - All Actions Scheduled.", 'penci-podcast' ), PENCI_PODCAST_IMPORTER_NAME ),
          'status'  => 'good',
          'badge'       => [
            'label' => __( 'Performance', 'penci-podcast' ),
            'color' => 'green',
          ],
        ] + $default;

    return [
        'label'       => sprintf( __( "%s - Missing Actions in schedule.", 'penci-podcast' ), PENCI_PODCAST_IMPORTER_NAME ),
        'status'      => 'performance',
        'badge'       => [
          'label' => __( 'Performance', 'penci-podcast' ),
          'color' => 'red',
        ],
        'actions'     => sprintf(
          '<p><a href="%s">%s</a></p>',
          esc_url( admin_url( '?' . PENCI_PODCAST_IMPORTER_ALIAS . '-action=reset-scheduled-posts' ) ),
          __( "Reset Schedules", 'penci-podcast' )
        )
      ] + $default;
  }

}