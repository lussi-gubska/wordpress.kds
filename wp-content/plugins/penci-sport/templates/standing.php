<?php
$standing_data = \Penci_Football_API::get( [
	'token'  => $settings['token'],
	'league' => $settings['league'],
	'type'   => 'standing',
] );
$layout        = isset( $settings['layout'] ) && $settings['layout'] ? $settings['layout'] : 'style-1';
$total_table   = count( $standing_data['standings'] );
$table_class   = $total_table > 1 ? 'multi-table' : 'single-table';
if ( ! empty( $standing_data ) && isset( $standing_data['standings'] ) ) :
	echo '<div class="pcspt-tb '.$table_class.'">';
	foreach ( $standing_data['standings'] as $standing_index => $standing_table ):
		?>
        <div class="penci-football-standing-wrap">
			<?php if ( isset($standing_table['group']) && $standing_table['group'] ) : ?>
				<h4 class="penci-football-ghead"><?php echo esc_html( $standing_table['group'] ); ?></h4>
			<?php endif; ?>
            <table class="penci-football-standing <?php echo $layout; ?>">
                <thead>
                <tr>
					<?php foreach ( $settings['data_show'] as $index => $data ) {
						echo '<td>' . $settings[ 'text_' . $data ] . '</td>';
					} ?>
                </tr>
                </thead>
                <tbody>
				<?php foreach ( $standing_table['table'] as $index => $team_data ):
					$logo_img      = '<img src="' . esc_url( $team_data['team']['crest'] ) . '" alt="" />';
					$team_data_arr = [
						'position' => $team_data['position'],
						'club'     => $logo_img . $team_data['team']['shortName'],
						'played'   => $team_data['playedGames'],
						'won'      => $team_data['won'],
						'drawn'    => $team_data['draw'],
						'lost'     => $team_data['lost'],
						'gf'       => $team_data['goalsFor'],
						'ga'       => $team_data['goalsAgainst'],
						'gd'       => $team_data['goalDifference'],
						'points'   => $team_data['points'],
					];
					echo '<tr>';
					foreach ( $settings['data_show'] as $index => $data ) {
						echo '<td class="pcteam_meta pcteam_' . $data . '">' . $team_data_arr[ $data ] . '</td>';
					}
					echo '</tr>';
				endforeach; ?>

                </tbody>
            </table>
        </div>
	<?php
	endforeach;
	echo '</div>';
endif;