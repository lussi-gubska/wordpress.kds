<?php
$mess =  array();

// Count views
$views_all = (int) get_post_meta( $post_id, '_count-views_all', true );
if( ! empty( $views_all ) ){
	$current_count = (int) get_post_meta( $post_id, 'penci_post_views_count' );
	$views_all = $views_all + $current_count;
	$views_all_updated = update_post_meta( $post_id, 'penci_post_views_count', $views_all  );

	if( $views_all_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'count_views' );
	}
}

$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );

return ( $mess ? implode( '<br>', $mess ) : '' );