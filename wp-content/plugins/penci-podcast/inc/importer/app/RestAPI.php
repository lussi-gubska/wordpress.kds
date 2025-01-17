<?php

namespace PenciPodcast;

use WP_REST_Server;

class RestAPI {

  /**
   * @var RestAPI;
   */
  protected static $_instance;

  /**
   * @return RestAPI
   */
  public static function instance(): RestAPI {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function setup() {
    register_rest_route(
      PENCI_PODCAST_IMPORTER_REST_API_PREFIX . '/v1',
      '/admin-dismiss-notice',
      [
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'PenciPodcast\RestAPI\Response::admin_dismiss_notice',
        'permission_callback' => 'PenciPodcast\RestAPI\ACL::admin_dismiss_notice',
      ]
    );

    register_rest_route(
      PENCI_PODCAST_IMPORTER_REST_API_PREFIX . '/v1',
      '/get-feed-summary',
      [
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'PenciPodcast\RestAPI\Response::get_feed_summary',
        'permission_callback' => 'PenciPodcast\RestAPI\ACL::get_feed_summary',
      ]
    );

    register_rest_route(
      PENCI_PODCAST_IMPORTER_REST_API_PREFIX . '/v1',
      '/save-feed',
      [
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'PenciPodcast\RestAPI\Response::save_feed',
        'permission_callback' => 'PenciPodcast\RestAPI\ACL::save_feed',
      ]
    );

    register_rest_route(
      PENCI_PODCAST_IMPORTER_REST_API_PREFIX . '/v1',
      '/import-feed',
      [
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'PenciPodcast\RestAPI\Response::import_feed',
        'permission_callback' => 'PenciPodcast\RestAPI\ACL::import_feed',
      ]
    );

    register_rest_route(
      PENCI_PODCAST_IMPORTER_REST_API_PREFIX . '/v1',
      '/sync-feed/(?P<id>\d+)',
      [
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'PenciPodcast\RestAPI\Response::sync_feed',
        'permission_callback' => 'PenciPodcast\RestAPI\ACL::sync_feed',
      ]
    );
  }

}