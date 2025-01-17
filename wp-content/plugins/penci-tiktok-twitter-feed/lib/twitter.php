<?php
/* implement getTweets */
require_once __DIR__ . '/PenciTwitterFeed.class.php';
function penci_getTweets( $count = 20, $username = false, $options = false ) {

	$options_data = get_option( 'penci_options[penci_twitter]' );

	if ( empty( $options_data ) ) {
		return false;
	}

	$config['key']          = 'FPYSYWIdyUIQ76Yz5hdYo5r7y';
	$config['secret']       = 'GqPj9BPgJXjRKIGXCULJljocGPC62wN2eeMSnmZpVelWreFk9z';
	$config['token']        = $options_data['oauth_token'];
	$config['token_secret'] = $options_data['oauth_token_secret'];
	$config['screenname']   = $options_data['screen_name'];
	$config['cache_expire'] = intval( 60 * 60 * 24 );
	if ( $config['cache_expire'] < 1 ) {
		$config['cache_expire'] = 3600;
	}
	$config['directory'] = plugin_dir_path( __FILE__ );

	$obj = new PenciTwitterFeed( $config );
	$res = $obj->getTweets( $username, $count, $options );
	update_option( 'tdf_last_error', $obj->st_last_error );

	return $res;

}
