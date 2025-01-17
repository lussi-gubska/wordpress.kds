<div class="pcfds-preview">
<?php if ($list) : ?>
    <table>
    <?php foreach ($list as $item) : ?>
        <tr>
            <th colspan="2">
                <h2><?php echo esc_html($item['title']); ?></h2>
            </th>
        </tr>

        <?php if (isset($item['thumbnail']) && $item['thumbnail']) : ?>
        <tr>
            <td nowrap>
                <?php esc_html_e('Featured Image', 'penci-feeds'); ?>
            </td>
            <td>
                <img src="<?php echo esc_attr($item['thumbnail']); ?>">
            </td>
        </tr>
        <?php endif; ?>

        <?php if (isset($item['excerpt']) && ($item['excerpt'])) : ?>
        <tr>
            <td>
                <?php esc_html_e('Excerpt', 'penci-feeds'); ?>
            </td>
            <td>
                <p><?php echo nl2br(esc_html($item['excerpt'])); ?></p>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td><?php esc_html_e('Content', 'penci-feeds'); ?></td>
            <td>
                <?php
                    if ($feed->content_wrapper)
                        echo str_replace('%CONTENT%', $item['content'], stripslashes($feed->content_wrapper));
                    else
                        echo $item['content'];
                ?>
                <?php if ($feed->display_readmore) : ?>
                    <?php echo str_replace('%LINK%', $item['url'], $feed->readmore_template); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else: ?>
    <p style="padding: 30px;
  background: #ffe6e6;
  border-radius: 5px;
  color: #b93030;
  font-weight: bold;
  text-align: center;"><?php esc_html_e('Error loading page: The URL you entered is not a valid RSS feed URL.', 'penci-feeds'); ?></p>
<?php endif; ?>
</div>