<?php

$post_list = get_posts( [
  'post_type'    	 => PENCI_PODCAST_IMPORTER_POST_TYPE_IMPORT,
  'posts_per_page' => 299,
] );

?>
<div data-pencipdc-import-notification="info"><?php echo esc_html__('Scheduled imports are set for sync once every hour by default.', 'penci-podcast' );?></div>

<?php if( !empty( $post_list ) ) : ?>
  <table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
      <tr>
        <th><?php echo esc_html__( 'Title', 'penci-podcast' );?></th>
        <th><?php echo esc_html__( 'Feed Link', 'penci-podcast' );?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $post_list as $post ) : ?>
        <tr>
          <td><?php echo get_the_title( $post );?></td>
          <td><?php echo get_post_meta($post->ID, 'pencipdc_rss_feed', true);?></td>
          <td class="pencipdc_import_buttons_container">
            <?php do_action( PENCI_PODCAST_IMPORTER_ALIAS . '_before_feed_item_operations', $post ); ?>
            <a href="edit.php?post_type=podcast&page=<?php echo PENCI_PODCAST_IMPORTER_PREFIX; ?>&tab=edit&post_id=<?php echo esc_attr($post->ID); ?>" class="button button-primary">
              <?php echo esc_html__('Edit Import', 'penci-podcast' );?>
            </a>
            <a href="<?php echo get_delete_post_link( $post->ID, '', true );?>" class="button button-secondary button-delete">
              <?php echo esc_html__('Delete Import', 'penci-podcast' );?>
            </a>
            <?php do_action( PENCI_PODCAST_IMPORTER_ALIAS . '_after_feed_item_operations', $post ); ?>
          </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif; ?>