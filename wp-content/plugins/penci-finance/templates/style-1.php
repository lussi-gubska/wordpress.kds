<?php
$class     = $layout == 'style-3' ? $layout . ' marquee3k' : $layout;
$data_attr = $layout == 'style-3' && isset( $settings['sticker_speed'] ) ? 'data-speed="' . $settings['sticker_speed'] . '" ' : '';
?>
<div <?php echo $data_attr; ?>class="penci-finance-list <?php echo $class; ?>">
	<?php
	if ( $layout == 'style-3' ) {
		echo '<div class="marquee3k-wrapper">';
	}
	foreach ( $finance_data as $index => $symbol ) {
		$class     = $symbol_class[ $index ];
		if ( $symbol->getLongName() ) :
			$change = round( $symbol->getRegularMarketChange(), 2 );
			$class .= $change < 0 ? ' down' : ' up';
			?>
            <div class="penci-fnlt-item <?php echo $class; ?>">
				<?php if ( 'style-3' == $layout ) : ?>
                <div class="penci-fnlt-item-wrap">
					<?php endif; ?>
                    <div class="penci-fnlt-item-head">
						<?php if ( isset( $symbol_img[ $index ]['id'] ) && $symbol_img[ $index ]['id'] ): ?>

							<?php if ( get_theme_mod( 'penci_disable_lazyload_layout' ) ) { ?>
                                <div class="penci-fnlt-logo penci-image-holder"
                                     style="background-image:url(<?php echo esc_url( wp_get_attachment_image_url( $symbol_img[ $index ]['id'], 'thumbnail' ) ); ?>)"></div>
							<?php } else { ?>
                                <div class="penci-fnlt-logo penci-image-holder penci-lazy"
                                     data-bgset="<?php echo esc_url( wp_get_attachment_image_url( $symbol_img[ $index ]['id'], 'thumbnail' ) ); ?>"></div>
							<?php } ?>
						<?php endif; ?>
                        <div class="penci-fnlt-name">
							<?php if ( in_array( 'longname', $data_show ) ) : ?>
                                <h4><?php echo $symbol->getLongName(); ?></h4>
							<?php endif; ?>
                            <div class="penci-fnlt-subname">
								<?php if ( in_array( 'ename', $data_show ) ) : ?>
                                    <span class="pcfnlt-symbol-getExchange"><?php echo $symbol->getFullExchangeName(); ?></span>
								<?php endif; ?>
								<?php if ( in_array( 'sname', $data_show ) ) : ?>
                                    <span class="pcfnlt-symbol-sname"><?php echo $symbol->getSymbol(); ?></span>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="penci-fnlt-item-body">
                        <div class="penci-fnlt-data">
							<?php if ( in_array( 'ask', $data_show ) ) : ?>
                                <span class="pcfnlt-symbol-ask"><?php echo $symbol->getAsk(); ?></span>
							<?php endif; ?>
							<?php if ( in_array( 'cur', $data_show ) ) : ?>
                                <span class="pcfnlt-symbol-cur"><?php echo $symbol->getCurrency(); ?></span>
							<?php endif; ?>
                        </div>
						<?php if ( in_array( 'mkchange', $data_show ) || in_array( 'mkchangep', $data_show ) ) : ?>
                        <div class="penci-fnlt-change">
							<?php if ( in_array( 'mkchange', $data_show ) ) : ?>
                                <span class="pcfnlt-symbol-mkchange"><?php echo $change; ?><?php echo $symbol->getCurrency(); ?></span>
							<?php endif; ?>
							<?php if ( in_array( 'mkchangep', $data_show ) ) : ?>
                                <span class="pcfnlt-symbol-mkchangep">(<?php echo round( $symbol->getRegularMarketChangePercent(), 2 ); ?>%)</span>
							<?php endif; ?>
                        </div>
						<?php endif; ?>
                    </div>
					<?php if ( 'style-3' == $layout ) : ?>
                </div>
			<?php endif; ?>
            </div>
		<?php endif;
	}
	if ( $layout == 'style-3' ) {
		echo '</div>';
	}
	?>
</div>