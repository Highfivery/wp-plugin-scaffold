<?php
/**
 * Database handlers.
 *
 * @package WPPluginScaffold
 */

namespace WPPluginScaffold\Database;

/**
 * Set up the database
 *
 * @return void
 */
function setup() {
	$n = function( $func ) {
		return __NAMESPACE__ . "\\$func";
	};

	add_action( 'init', $n( 'update' ), 11 );
}

/**
 * Run functions on plugin update/activation
 *
 * @return void
 */
function update() {
	$previous_version = get_option( 'wp_plugin_scaffold_version', 0 );

	if ( version_compare( $previous_version, '0.0.1', '<=' ) ) {
		update_database();
	}

	update_option( 'wp_plugin_scaffold_version', WP_PLUGIN_SCAFFOLD_PLUGIN_VERSION );
}

function update_database() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'wp_plugin_scaffold';

	$sql = "CREATE TABLE $table_name (

	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
