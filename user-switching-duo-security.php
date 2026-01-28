<?php
/*
Plugin Name: User Switching for Duo Security
Description: Add-on plugin for User Switching which allows it to play nicely with Duo Security and Duo Universal
Version:     1.1.0
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

/**
 * Handles the Duo Security authentication state when switching users.
 *
 * Supports both the legacy Duo Security plugin and the newer Duo Universal plugin.
 *
 * @param int      $user_id     The ID of the user being switched to.
 * @param int|false $old_user_id The ID of the user being switched from, or false if not known.
 */
function user_switching_duo_set_auth( $user_id, $old_user_id = false ) {
	// Support for the legacy Duo Security plugin.
	if ( function_exists( 'duo_set_cookie' ) ) {
		duo_unset_cookie();
		duo_set_cookie( new WP_User( $user_id ) );
		return;
	}

	// Support for the Duo Universal plugin.
	if ( class_exists( 'Duo\DuoUniversalWordpress\DuoUniversal_WordpressPlugin' ) ) {
		global $duoup_plugin;

		if ( ! isset( $duoup_plugin ) ) {
			return;
		}

		// Clear auth state for the old user if known.
		if ( $old_user_id ) {
			$duoup_plugin->clear_user_auth( $old_user_id );
		}

		// Mark the new user as authenticated with Duo.
		$duoup_plugin->update_user_auth_status( $user_id, 'authenticated' );
	}
}

add_action( 'switch_to_user',   'user_switching_duo_set_auth', 10, 2 );
add_action( 'switch_back_user', 'user_switching_duo_set_auth', 10, 2 );
