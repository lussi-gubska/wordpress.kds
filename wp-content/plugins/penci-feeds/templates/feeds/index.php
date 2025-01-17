<div class="wrap">
    <h2>
        <?php echo esc_html( __( 'Feeds', 'penci-feeds' ) ); ?> <a href="<?php echo $this->menuUrl('pcfds-feeds', 'add'); ?>" class="add-new-h2"><?php echo esc_html( __( 'Add New', 'penci-feeds' ) ); ?></a>
    </h2>

    <?php
    $list = $grid->getList();
    ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label class="screen-reader-text" for="bulk-action-selector-top"><?php esc_html_e('Select bulk action', 'penci-feeds'); ?></label>
                <select id="bulk-action-selector-top" name="action">
                    <option selected="selected" value=""><?php esc_html_e('Bulk Actions', 'penci-feeds'); ?></option>
                    <option value="delete"><?php esc_html_e('Delete', 'penci-feeds'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'penci-feeds'); ?></option>
                    <option value="enable"><?php esc_html_e('Enable', 'penci-feeds'); ?></option>
                </select>
                <input type="submit" value="<?php esc_html_e('Apply', 'penci-feeds'); ?>" class="button action" id="doaction">
            </div>
            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $list['pagination']['totalItems']; ?> <?php esc_html_e('items'); ?></span>
                <span class="pagination-links"><a href="<?php echo $this->menuUrl('pcfds-feeds'); ?>" title="<?php esc_attr_e('Go to the first page', 'penci-feeds'); ?>" class="first-page <?php if ($list['pagination']['currentPage'] == 1) : ?>disabled<?php endif; ?>">«</a>
                <a href="<?php echo $this->menuUrl('pcfds-feeds', '', array('paged'=>$list['pagination']['currentPage']-1)); ?>" title="<?php esc_attr_e('Go to the previous page', 'penci-feeds'); ?>" class="prev-page <?php if ($list['pagination']['currentPage'] == 1) : ?>disabled<?php endif; ?>">‹</a>
                <span class="paging-input">
                    <label class="screen-reader-text" for="current-page-selector"><?php esc_html_e('Select Page', 'penci-feeds'); ?></label>
                    <input type="text" size="1" value="<?php echo $list['pagination']['currentPage']; ?>" name="paged" title="<?php esc_attr_e('Current page', 'penci-feeds'); ?>" id="current-page-selector" class="current-page"> <?php esc_html_e('of', 'penci-feeds'); ?> <span class="total-pages"><?php echo $list['pagination']['totalPages']; ?></span>
                </span>
                <a href="<?php echo $this->menuUrl('pcfds-feeds', '', array('paged'=>$list['pagination']['currentPage']+1)); ?>" title="<?php esc_attr_e('Go to the next page', 'penci-feeds'); ?>" class="next-page <?php if ($list['pagination']['currentPage'] == $list['pagination']['totalPages']) : ?>disabled<?php endif; ?>">›</a>
                <a href="<?php echo $this->menuUrl('pcfds-feeds', '', array('paged'=>$list['pagination']['totalPages'])); ?>" title="<?php esc_attr_e('Go to the last page', 'penci-feeds'); ?>" class="last-page <?php if ($list['pagination']['currentPage'] == $list['pagination']['totalPages']) : ?>disabled<?php endif; ?>">»</a></span>
            </div>
            <br class="clear">
        </div>

        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e('Select All', 'penci-feeds'); ?></label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th class="manage-column column-title <?php echo $grid->orderByStateHelper('title'); ?>">
                    <a href="<?php echo $grid->orderByHelper('title', $this); ?>">
                        <span><?php esc_html_e('Title', 'penci-feeds'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th class="manage-column">
                    <?php esc_html_e('Automatic updates', 'penci-feeds'); ?>
                </th>
                <th class="manage-column column-stats">
                    <?php esc_html_e('Statistics', 'penci-feeds'); ?>
                </th>
                <th class="manage-column column-author">
                    <?php esc_html_e('Author', 'penci-feeds'); ?>
                </th>
                <th class="manage-column column-date <?php echo $grid->orderByStateHelper('date'); ?>">
                    <a href="<?php echo $grid->orderByHelper('date', $this); ?>">
                        <span><?php esc_html_e('Date', 'penci-feeds'); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            </tr>
            </thead>

            <tbody id="the-list">

            <?php foreach ($list['list'] as $item) : ?>
                <tr class="author-self level-0 post-40 type-page status-publish hentry" id="post-40">
                    <th class="check-column" scope="row">
                        <label for="cb-select-40" class="screen-reader-text"><?php echo esc_html($item->post_title); ?></label>
                        <input type="checkbox" value="<?php echo $item->ID; ?>" name="post[]" id="cb-select-40">
                        <div class="locked-indicator"></div>
                    </th>
                    <td class="post-title page-title column-title">
                        <strong>
                            <a href="<?php echo $this->menuUrl('pcfds-feeds', 'edit', array('post'=>$item->ID)); ?>" class="row-title"><?php echo esc_html($item->post_title); ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="<?php esc_html_e('Run', 'penci-feeds'); ?>" href="<?php echo $this->menuUrl('pcfds-feeds', 'run', array('post'=>$item->ID)); ?>"><?php esc_html_e('Run', 'penci-feeds'); ?></a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo $this->menuUrl('pcfds-feeds', 'removePosts', array('post'=>$item->ID)); ?>" title="<?php esc_attr_e('Remove posts', 'penci-feeds'); ?>" class="submitdelete"><?php esc_html_e('Remove posts', 'penci-feeds'); ?></a> |
                            </span>
                            <span class="edit">
                                <a title="<?php esc_html_e('Edit', 'penci-feeds'); ?>" href="<?php echo $this->menuUrl('pcfds-feeds', 'edit', array('post'=>$item->ID)); ?>"><?php esc_html_e('Edit', 'penci-feeds'); ?></a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo $this->menuUrl('pcfds-feeds', 'delete', array('post'=>$item->ID)); ?>" title="<?php esc_attr_e('Delete', 'penci-feeds'); ?>" class="submitdelete"><?php esc_html_e('Delete', 'penci-feeds'); ?></a>
                            </span>
                        </div>
                    </td>
                    <td class="type column-type">
                        <?php $status = get_post_meta( $item->ID, '_campaign_status', true ); ?>
                        <?php if ($status == 'started') : ?>
                            <?php esc_html_e('Enabled', 'penci-feeds'); ?> | <a href="<?php echo $this->menuUrl('pcfds-feeds', 'changeStatus', array('post'=>$item->ID, 'status'=>'stopped')); ?>" class="pcfds-disable"><?php esc_html_e('Turn Off', 'penci-feeds'); ?></a>
                        <?php else: ?>
                            <?php esc_html_e('Disabled', 'penci-feeds'); ?> | <a href="<?php echo $this->menuUrl('pcfds-feeds', 'changeStatus', array('post'=>$item->ID, 'status'=>'started')); ?>" class="pcfds-enable"><?php esc_html_e('Turn On', 'penci-feeds'); ?></a>
                        <?php endif; ?>
                    </td>
                    <td class="stats column-stats">
                        <?php
                            $addedPosts = get_posts(array(
                                'post_type'         => 'any',
                                'posts_per_page'    => -1,
                                'post_status'       => 'any',
                                'post_parent'       => null,
                                'meta_key'=>'_rss_feed_id',
                                'meta_value'=>$item->ID
                            ));
                        ?>
                        <?php $lastUpdate = get_post_meta ( $item->ID, '_last_update', true); ?>
                        <p><?php esc_html_e('Posts imported:', 'penci-feeds'); ?> <?php echo count($addedPosts); ?></p>
                        <p><?php esc_html_e('Last update:', 'penci-feeds'); ?> <?php echo $lastUpdate?$grid->formatTimeHelper($lastUpdate):esc_html('Never', 'penci-feeds'); ?></p>
                    </td>
                    <td class="author column-author">
                        <?php echo esc_html(the_author_meta( 'display_name', $item->post_author )); ?>
                    </td>
                    <td class="date column-date"><?php echo $grid->formatDateHelper($item->post_date); ?></td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    </form>

    <br class="clear">
</div>