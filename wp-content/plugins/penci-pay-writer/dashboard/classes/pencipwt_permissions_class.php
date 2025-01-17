<?php

include_once( 'pencipwt_general_functions_class.php' );

class PenciPWT_Permissions {

	/**
	 * Checks whether the requested permission is available for the given user ID.
	 *
	 * @access   public
	 *
	 * @param    $permission string the permission to check
	 * @param     $args function args: the user is to check the permission against
	 *
	 * @return   bool whether user has permission
	 * @since    2.0
	 */

	function __call( $permission, $args ) {
		global $current_user;

		//Admins override permissions, if they don't have any specific settings.
		if ( pencipwt_get_setting( 'admins_override_permissions' ) and pencipwt_get_setting( 'userid' ) == 'general' and in_array( 'administrator', (array) $current_user->roles ) ) {
			return true;
		}

		if ( pencipwt_get_setting( $permission ) == 1 ) {
			return true;
		} else {
			return false;
		}
	}
}
