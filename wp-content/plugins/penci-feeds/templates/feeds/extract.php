<?php if ( $page ) : ?>
	<?php echo $page; ?>
<?php else: ?>
    <p style="padding: 30px;
  background: #ffe6e6;
  border-radius: 5px;
  color: #b93030;
  font-weight: bold;
  text-align: center;"><?php esc_html_e( 'Error loading page: The URL you entered is not a valid RSS feed URL.', 'penci-feeds' ); ?></p>
<?php endif; ?>