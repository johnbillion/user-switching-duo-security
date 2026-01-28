<?php
/*
Plugin Name: User Switching for Duo Security
Description: Add-on plugin for User Switching which allows it to play nicely with Duo Security
Version:     1.1
Author:      John Blackbourn
Author URI:  https://johnblackbourn.com/
License:     GPL v2 or later
Network:     true

Copyright © 2015 John Blackbourn

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

/* Actions to allow the plugin to be used with the legacy Duo WordPress plugin. */
function user_switching_duo_set_cookie( $user_id ) {
	if ( function_exists( 'duo_set_cookie' ) ) {
		duo_unset_cookie();
		duo_set_cookie( new WP_User( $user_id ) );
	}
}

add_action( 'switch_to_user',   'user_switching_duo_set_cookie' );
add_action( 'switch_back_user', 'user_switching_duo_set_cookie' );

/* Actions to allow the plugin to be used with Duo Universal. */
add_action( 'switch_to_user',   'user_switching_duo_set_authentication', 1 );
add_action( 'switch_back_user', 'user_switching_duo_set_authentication', 1 );

/**
 * Sets the 'duo_auth_status' user meta on the user we're switching to.
 *
 * Duo Universal, which supplants the duo-wordpress plugin, uses a user meta of
 * 'duo_auth_status' = 'authenticated' to determine if a user has been authenticated
 * by Duo MFA. This sets that meta on the user we're switching to (or switching back
 * to.)
 *
 * @param  int $user_id The user ID we're switching (back) to.
 */
function user_switching_duo_set_authentication( $user_id ) {
	update_user_meta( $user_id, 'duo_auth_status', 'authenticated' );
}
