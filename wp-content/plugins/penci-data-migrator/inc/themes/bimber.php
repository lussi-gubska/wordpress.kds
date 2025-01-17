<?php
$mess = array();
// Count views
$view_count = (int) get_post_meta( $post_id, '_bimber_fake_view_count', true );
if ( ! empty( $view_count ) ) {
	$views_all_updated = update_post_meta( $post_id, 'penci_post_views_count', $view_count );

	if ( $views_all_updated ) {
		$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'count_views' );
	}
}

// Sidebar
$post_layout     = get_post_meta( $post_id, '_bimber_single_options', true );
$post_layout_pre = isset( $post_layout['sidebar_location'] ) && 'left' == $post_layout['sidebar_location'] ? 'left' : '';

$single_sidebar_pos = update_post_meta( $post_id, 'penci_post_sidebar_display', $post_layout_pre );
if ( $single_sidebar_pos ) {
	$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'post_layout' );
}

$mess[] = Penci_Soledad_MG_Helper::get_mess_by_type( $post_id, 'success', timer_stop() );

return ( $mess ? implode( '<br>', $mess ) : '' );