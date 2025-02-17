<?php

namespace PenciPodcast;

class PostTypes {

  /**
   * @var PostTypes;
   */
  protected static $_instance;

  /**
   * @return PostTypes
   */
  public static function instance(): PostTypes {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function setup() {
    register_post_type(
      PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT,
      [
        'labels' => [
          'name'          => __( 'Podcast Imports', 'penci-podcast' ),
          'singular_name' => __( 'Podcast Import', 'penci-podcast' )
        ],
        'public'              => true,
        'has_archive'         => false,
        'supports'            => [ 'title' ],
        'can_export'          => false,
        'exclude_from_search' => true,
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'publicly_queryable'  => false,
      ]
    );

    add_action( 'admin_menu', [ $this, '_admin_menu' ] );
  }

  public function _admin_menu() {
    if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'trash' )
      return;

    if( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT )
      return;

    remove_menu_page( 'edit.php?post_type=' . PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT );
  }

}