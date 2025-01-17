<?php
add_filter( 'edit_profile_url', function () {
	$account  = \PenciFrontendSubmission\AccountPage::getInstance();
	$endpoint = $account->get_endpoint();
	$item     = $endpoint['edit_account'];

	return esc_url( penci_home_url_multilang( $endpoint['account']['slug'] . '/' . $item['slug'] ) );
} );