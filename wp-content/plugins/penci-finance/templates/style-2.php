<?php
$order = $settings['order_by'] . '_' . $settings['order'];

$api_call = [
	'ids'         => $settings['ids'],
	'per_page'    => $settings['per_page'],
	'vs_currency' => $settings['vs_currency'],
	'order'       => $order,
];

$api_data = Penci_Finance_Crypto::get( $api_call );

if ( empty( $api_data ) || ! $api_data ) {
	echo __('No data found','penci-finance');
} elseif ( isset( $api_data['error'] ) ) {
	echo '<p>' . $api_data['message'] . '</p>';
} elseif ( isset( $api_data['status']['error_message'] ) ) {
	echo '<p>' .$api_data['status']['error_message'] . '</p>';
} else {
	$curcy     = Penci_Finance::convert_symbol( $settings['vs_currency'] );
	$class     = $settings['layout'] == 'style-4' ? 'marquee3k ' : '';
	$data_attr = $settings['layout'] == 'style-4' && $settings['sticker_speed'] ? 'data-speed="' . $settings['sticker_speed'] . '" ' : '';
	?>
    <div
		<?php echo $data_attr; ?>class="<?php echo $class; ?>penci-fncrypto-table-wrapper pcct-<?php echo $settings['layout']; ?>">
		<?php if ( $settings['layout'] == 'style-3' || $settings['layout'] == 'style-4' ) {

			if ( $settings['layout'] == 'style-4' ) {
				echo '<div class="marquee3k-wrapper">';
			}

			foreach ( $api_data as $index => $symbol ):
				$percent_1h  = $symbol['price_change_percentage_1h_in_currency'];
				$percent_24h = $symbol['price_change_percentage_24h_in_currency'];
				$percent_7d  = $symbol['price_change_percentage_7d_in_currency'];
				echo '<div class="penci-fncrypto-item">';
				echo '<div class="penci-fncrypto-content">';
				if ( in_array( 'order', $settings['data_show'] ) ) {
					echo '<div class="pcfic-order">' . ( $index + 1 ) . '</div>';
				}
				if ( in_array( 'name', $settings['data_show'] ) ) {
					echo '<div class="pcfic-name">
                        <img src="' . esc_url( $symbol['image'] ) . '" alt="' . $symbol['name'] . '">
                        <span class="name">' . $symbol['name'] . '</span>
                        <span class="symbol">' . $symbol['symbol'] . '</span>
                    </div>';
				}
				if ( in_array( 'price', $settings['data_show'] ) ) {
					echo '<div class="penci-fncrypto-price penci-fncrypto-di"><span class="penci-fncrypto-label">' . $settings['text_price'] . '</span>' . $curcy . Penci_Finance::number_format( $symbol['current_price'], 3 ) . '</div>';
				}
				if ( in_array( '1h', $settings['data_show'] ) ) {

					echo '<div class="penci-fncrypto-p1h penci-fncrypto-di ' . $this->isupdown( $percent_1h ) . '"><span class="penci-fncrypto-label">' . $settings['text_1h'] . '</span>' . number_format( $percent_1h, 2 ) . '%</div>';
				}
				if ( in_array( '24h', $settings['data_show'] ) ) {

					echo '<div class="penci-fncrypto-p24h penci-fncrypto-di ' . $this->isupdown( $percent_24h ) . '"><span class="penci-fncrypto-label">' . $settings['text_24h'] . '</span>' . number_format( $percent_24h, 2 ) . '%</div>';
				}
				if ( in_array( '7d', $settings['data_show'] ) ) {

					echo '<div class="penci-fncrypto-p7d penci-fncrypto-di ' . $this->isupdown( $percent_7d ) . '"><span class="penci-fncrypto-label">' . $settings['text_7d'] . '</span>' . number_format( $percent_7d, 2 ) . '%</div>';
				}
				if ( in_array( 'market_cap', $settings['data_show'] ) ) {
					echo '<div class="penci-fncrypto-market-cap penci-fncrypto-di"><span class="penci-fncrypto-label">' . $settings['text_market_cap'] . '</span>' . $curcy . number_format( $symbol['market_cap'] ) . '</div>';
				}
				if ( in_array( 'volume', $settings['data_show'] ) ) {
					echo '<div class="penci-fncrypto-volume penci-fncrypto-di"><span class="penci-fncrypto-label">' . $settings['text_volume'] . '</span>' . $curcy . number_format( $symbol['total_volume'] ) . '</div>';
				}
				if ( in_array( 'supply', $settings['data_show'] ) ) {
					echo '<div class="penci-fncrypto-supply penci-fncrypto-di"><span class="penci-fncrypto-label">' . $settings['text_supply'] . '</span>' . $curcy . number_format( $symbol['circulating_supply'] ) . '</div>';
				}
				if ( in_array( 'chart', $settings['data_show'] ) ) {
					echo '<div class="pcfic-chart-container pcfic-total-' . $this->isupdown( $percent_7d ) . '"><canvas class="pcfic-chart grid-item" data-chart="' . implode( ',', $symbol['sparkline_in_7d']['price'] ) . '"></canvas></div>';
				}
				echo '</div>';
				echo '</div>';
			endforeach;
			if ( $settings['layout'] == 'style-4' ) {
				echo '</div>';
			}
		} else { ?>
            <table class="penci-fncrypto-table row-border dataTable">
                <thead>
                <tr>
					<?php
					if ( in_array( 'order', $settings['data_show'] ) ) {
						echo '<th>#</th>';
					}
					if ( in_array( 'name', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_name'] . '</th>';
					}
					if ( in_array( 'price', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_price'] . '</th>';
					}
					if ( in_array( '1h', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_1h'] . '</th>';
					}
					if ( in_array( '24h', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_24h'] . '</th>';
					}
					if ( in_array( '7d', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_7d'] . '</th>';
					}
					if ( in_array( 'market_cap', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_market_cap'] . '</th>';
					}
					if ( in_array( 'volume', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_volume'] . '</th>';
					}
					if ( in_array( 'supply', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_supply'] . '</th>';
					}
					if ( in_array( 'chart', $settings['data_show'] ) ) {
						echo '<th>' . $settings['text_chart'] . '</th>';
					}
					?>
                </tr>
                </thead>
                <tbody>

				<?php
				foreach ( $api_data as $index => $symbol ):
					$percent_1h = $symbol['price_change_percentage_1h_in_currency'];
					$percent_24h = $symbol['price_change_percentage_24h_in_currency'];
					$percent_7d = $symbol['price_change_percentage_7d_in_currency'];
					echo '<tr class="pcfic-total-' . $this->isupdown( $percent_7d ) . '">';
					if ( in_array( 'order', $settings['data_show'] ) ) {
						echo '<td>' . ( $index + 1 ) . '</td>';
					}
					if ( in_array( 'name', $settings['data_show'] ) ) {
						echo '<td class="pcfic-name-wrap"><div class="pcfic-name">
                                <img src="' . esc_url( $symbol['image'] ) . '" alt="' . $symbol['name'] . '">
                                <span class="name">' . $symbol['name'] . '</span>
                                <span class="symbol">' . $symbol['symbol'] . '</span>
                            </div></td>';
					}
					if ( in_array( 'price', $settings['data_show'] ) ) {
						echo '<td>' . $curcy . Penci_Finance::number_format( $symbol['current_price'] ) . '</td>';
					}
					if ( in_array( '1h', $settings['data_show'] ) ) {

						echo '<td class="' . $this->isupdown( $percent_1h ); ?>"><?php echo number_format( $percent_1h, 2 ) . '%</td>';
					}
					if ( in_array( '24h', $settings['data_show'] ) ) {

						echo '<td class="' . $this->isupdown( $percent_24h ); ?>"><?php echo number_format( $percent_24h, 2 ) . '%</td>';
					}
					if ( in_array( '7d', $settings['data_show'] ) ) {

						echo '<td class="' . $this->isupdown( $percent_7d ); ?>"><?php echo number_format( $percent_7d, 2 ) . '%</td>';
					}
					if ( in_array( 'market_cap', $settings['data_show'] ) ) {
						echo '<td>' . $curcy . number_format( $symbol['market_cap'] ) . '</td>';
					}
					if ( in_array( 'volume', $settings['data_show'] ) ) {
						echo '<td>' . $curcy . number_format( $symbol['total_volume'] ) . '</td>';
					}
					if ( in_array( 'supply', $settings['data_show'] ) ) {
						echo '<td>' . $curcy . number_format( $symbol['circulating_supply'] ) . '</td>';
					}
					if ( in_array( 'chart', $settings['data_show'] ) ) {
						echo '<td><div class="pcfic-chart-container"><canvas class="pcfic-chart" data-chart="' . implode( ',', $symbol['sparkline_in_7d']['price'] ) . '"></canvas></div></td>';
					}
					echo '</tr>';
				endforeach; ?>

                </tbody>
            </table>
		<?php } ?>
    </div>
	<?php
}