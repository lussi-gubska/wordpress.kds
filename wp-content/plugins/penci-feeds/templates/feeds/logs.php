<div class="wrap pcfds-form">
    <h2>
        <?php echo esc_html( __( 'Logs', 'penci-feeds' ) ); ?> <a href="<?php echo $this->menuUrl('pcfds-feeds', 'clearLogs'); ?>" class="add-new-h2"><?php echo esc_html( __( 'Clear logs', 'penci-feeds' ) ); ?></a>
    </h2>

    <?php echo nl2br(esc_html($content)); ?>
    <br class="clear">
</div>