<?php
function penci_sport_list_league() {
	$default_league = array(
		'WC'  => 'FIFA World Cup',
		'CL'  => 'UEFA Champions League',
		'BL1' => 'Bundesliga',
		'DED' => 'Eredivisie',
		'BSA' => 'Campeonato Brasileiro Série A',
		'PD'  => 'Primera Division',
		'FL1' => 'Ligue 1',
		'ELC' => 'Championship',
		'PPL' => 'Primeira Liga',
		'EC'  => 'European Championship',
		'SA'  => 'Serie A',
		'PL'  => 'Premier League',
		'CLI' => 'Copa Libertadores',
	);

	return apply_filters( 'penci_sport_list_league', $default_league );
}

function penci_sport_mtime( $utc_time ) {
	// Convert the UTC time to a timestamp
	$timestamp = strtotime( $utc_time );

	// Try to get the site's timezone from WordPress settings
	$timezone_string = get_option( 'timezone_string' );
	if ( empty( $timezone_string ) ) {
		// Fallback to the offset if the timezone string is empty
		$gmt_offset = get_option( 'gmt_offset' );
		$timezone   = new DateTimeZone( 'UTC' );
		$datetime   = new DateTime( "@$timestamp", $timezone );
		$datetime->modify( "{$gmt_offset} hours" );
	} else {
		// Use the site's timezone if set
		$timezone = new DateTimeZone( $timezone_string );
		$datetime = new DateTime( "@$timestamp" );
		$datetime->setTimezone( $timezone );
	}

	// Format the time as needed (example: 'Y-m-d H:i:s')
	return $datetime->format( 'Y-m-d H:i:s' );

}

function penci_sport_api_help() {
	return __( 'You can obtain the free API key by following <a target="_blank" href="https://www.football-data.org/client/register">this link</a>.', 'penci-sport' );
}