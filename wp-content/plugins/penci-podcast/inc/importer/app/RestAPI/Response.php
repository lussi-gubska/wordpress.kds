<?php

namespace PenciPodcast\RestAPI;

use WP_Error;
use PenciPodcast\Settings as PPI_Settings;
use PenciPodcast\Helper\FeedForm as PPI_Helper_FeedForm;
use PenciPodcast\Helper\Importer as PPI_Helper_Importer;
use PenciPodcast\Helper\Scheduler as PPI_Helper_Scheduler;

class Response {

  public static function admin_dismiss_notice() {
    $current_notice_dismiss_map = PPI_Settings::instance()->get( '_admin_notice_dismissed_map' );

    $current_notice_dismiss_map[ get_current_user_id() ] = time();

    PPI_Settings::instance()->update( '_admin_notice_dismissed_map', $current_notice_dismiss_map );

    return rest_ensure_response( true );
  }

  public static function get_feed_summary( $request ) {
    $request_data = $request->get_params();
    $importer = PPI_Helper_Importer::from_meta_map( PPI_Helper_FeedForm::request_data_to_meta_map( $request_data ) );

    return [
      'episode_count' => $importer->get_current_feed_episode_count()
    ];
  }

  public static function save_feed( $request ) {
    $request_data = $request->get_params();
    $messages = [];

    $meta_map = PPI_Helper_FeedForm::request_data_to_meta_map( $request_data );

    if( isset( $request_data[ 'post_id' ] ) ) {
      foreach( $meta_map as $k => $v )
        update_post_meta( intval( $request_data[ 'post_id' ] ), $k, $v );

      PPI_Helper_Scheduler::schedule_post_id( intval( $request_data[ 'post_id' ] ) );

      $messages[] = [
        'type'    => 'success',
        'message' => __( 'Updated settings.', 'penci-podcast' )
      ];
    } else if( isset( $meta_map[ 'pencipdc_import_continuous' ] ) && $meta_map[ 'pencipdc_import_continuous' ] == 'on' ) {
      $importer = PPI_Helper_Importer::from_meta_map( $meta_map );

      $title = $importer->get_feed_title();

      if( 0 === post_exists($title , "", "", PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT ) ) {
        $import_post = [
          'post_title'   => $title,
          'post_type'    => PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT,
          'post_status'  => 'publish',
        ];
        $post_import_id = wp_insert_post( $import_post );

        foreach( $meta_map as $k => $v )
          update_post_meta( $post_import_id, $k, $v );

        PPI_Helper_Scheduler::schedule_post_id( intval( $post_import_id ) );

        $messages[] = [
          'type'    => 'success',
          'message' => __( 'Saved podcast feed for continuous import.', 'penci-podcast' )
        ];
      } else {
        $messages[] = [
          'type'    => 'danger',
          'message' => __('This podcast is already scheduled for import. Delete the previous schedule to create a new one.', 'penci-podcast' )
        ];
      }
    }

    return rest_ensure_response( [
      'messages'  => $messages
    ] );
  }

  public static function import_feed( $request ) {
    $request_data = $request->get_params();
    $messages = [];

    $meta_map = PPI_Helper_FeedForm::request_data_to_meta_map( $request_data );

    $importer = PPI_Helper_Importer::from_meta_map( $meta_map );

    if( $request->has_param( 'episode_offset' ) )
      $importer->import_segmentation_offset = intval( $request->get_param( 'episode_offset' ) );

    if( $request->has_param( 'episode_limit' ) )
      $importer->import_segmentation_limit = intval( $request->get_param( 'episode_limit' ) );

    $import_current_feed = $importer->import_current_feed();



    if( isset( $import_current_feed[ 'additional_errors' ] ) && is_array( $import_current_feed[ 'additional_errors' ] ) )
      foreach( $import_current_feed[ 'additional_errors' ] as $additional_error )
        $messages[] = [
          'type'    => 'danger',
          'message' => $additional_error
        ];

    unset( $import_current_feed[ 'additional_errors' ] );

    return rest_ensure_response( [
      'messages'  => $messages,
      'summary'   => $import_current_feed
    ] );
  }

  public static function sync_feed( $request ) {
    $all_meta = get_post_meta( intval( $request[ 'id' ] ) );
    $meta_map = [];

    foreach( $all_meta as $k => $v ) {
      if( is_array( $v ) && count( $v ) === 1 )
        $v = maybe_unserialize( $v[ 0 ] );

      $meta_map[ $k ] = $v;
    }

    // Maybe deleted after queued, need to ensure it's fine.
    if( isset( $meta_map[ 'pencipdc_rss_feed' ] ) ) {
      $importer = PPI_Helper_Importer::from_meta_map( $meta_map );
      $response = $importer->import_current_feed();
    }

    return true;
  }

}