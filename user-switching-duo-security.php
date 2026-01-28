<?php
/*
Plugin Name: User Switching for Duo Security
Description: Add-on plugin for User Switching which allows it to play nicely with Duo Security (supports both legacy duo-wordpress and new duo-universal plugins)
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

function user_switching_duo_set_cookie( $user_id ) {
	// Support for the new duo-universal plugin
	global $duoup_plugin;
	if ( isset( $duoup_plugin ) &&
		is_object( $duoup_plugin ) &&
		method_exists( $duoup_plugin, 'clear_user_auth' ) &&
		method_exists( $duoup_plugin, 'update_user_auth_status' ) ) {
		$duoup_plugin->clear_user_auth( $user_id );
		$duoup_plugin->update_user_auth_status( $user_id, 'authenticated' );
		return;
	}

	// Support for the legacy duo-wordpress plugin
	if ( function_exists( 'duo_set_cookie' ) ) {
		duo_unset_cookie();
		duo_set_cookie( new WP_User( $user_id ) );
	}
}

add_action( 'switch_to_user',   'user_switching_duo_set_cookie' );
add_action( 'switch_back_user', 'user_switching_duo_set_cookie' );
