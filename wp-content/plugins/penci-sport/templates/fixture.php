<?php
$matches = \Penci_Football_API::get( [
    'token'  => $settings['token'],
    'type'   => 'matches',
    'league' => $settings['league'],
    'query'  => [
        'status' => $settings['status'],
    ],
] );
if ( ! empty( $matches['matches'] ) ) :
    $matches_data = array_slice( $matches['matches'], 0, $settings['limit'] );
    ?>
    <div class="penci-football-matches-wrap">
        <div class="penci-football-matches">
            <?php foreach ( $matches_data as $index => $match ): ?>
                <div class="penci-football-match">

                    <div class="pcfm-item pcfm-home">
                        <div class="pcfm-item-logo">
                            <img src="<?php echo $match['homeTeam']['crest']; ?>"
                                 alt="<?php echo $match['homeTeam']['shortName']; ?>">
                        </div>
                        <h4 class="pcfm-item-title">
                            <?php echo $match['homeTeam']['shortName']; ?>
                        </h4>
                    </div>
                    <?php if ( ! empty( $match['score']['fullTime'] ) && isset( $match['score']['fullTime']['home'] ) && isset( $match['score']['fullTime']['away'] ) ): ?>
                        <div class="penci-matche-score">
                            <span class="home"><?php echo $match['score']['fullTime']['home']; ?></span>
                            <span class="away"><?php echo $match['score']['fullTime']['away']; ?></span>
                        </div>
                    <?php else: ?>
                        <div class="penci-matche-time">
                            <time datetime="<?php echo $match['utcDate']; ?>"><?php echo penci_sport_mtime( $match['utcDate'] ); ?></time>
                        </div>
                    <?php endif; ?>
                    <div class="pcfm-item pcfm-away">
                        <div class="pcfm-item-logo">
                            <img src="<?php echo $match['awayTeam']['crest']; ?>"
                                 alt="<?php echo $match['awayTeam']['shortName']; ?>">
                        </div>
                        <h4 class="pcfm-item-title">
                            <?php echo $match['awayTeam']['shortName']; ?>
                        </h4>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php
endif;