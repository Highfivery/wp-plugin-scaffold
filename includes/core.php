<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package WPPluginScaffold
 */

namespace WPPluginScaffold\Core;

use WPPluginScaffold\Utility;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $func ) {
		return __NAMESPACE__ . "\\$func";
	};

	add_action( 'after_setup_theme', $n( 'i18n' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'enqueue_block_editor_assets', $n( 'core_block_overrides' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );

	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );
}

/**
 * Makes plugin available for translation.
 *
 * Translations can be added to the /languages directory.
 *
 * @return void
 */
function i18n() {
	load_theme_textdomain( 'wp-plugin-scaffold', WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . '/languages' );
}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'frontend',
		WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/js/frontend.js',
		Utility\get_asset_info( 'frontend', 'dependencies' ),
		Utility\get_asset_info( 'frontend', 'version' ),
		true
	);
}

/**
 * Enqueue scripts for admin
 *
 * @return void
 */
function admin_scripts() {
	wp_enqueue_script(
		'admin',
		WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/js/admin.js',
		Utility\get_asset_info( 'admin', 'dependencies' ),
		Utility\get_asset_info( 'admin', 'version' ),
		true
	);
}

/**
 * Enqueue core block filters, styles and variations.
 *
 * @return void
 */
function core_block_overrides() {
	$overrides = WP_PLUGIN_SCAFFOLD_PLUGIN_DIST_PATH . 'js/core-block-overrides.asset.php';
	if ( file_exists( $overrides ) ) {
		$dep = require_once $overrides;
		wp_enqueue_script(
			'core-block-overrides',
			WP_PLUGIN_SCAFFOLD_PLUGIN_URL . 'js/core-block-overrides.js',
			$dep['dependencies'],
			$dep['version'],
			true
		);
	}
}

/**
 * Enqueue styles for admin
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'admin-style',
		WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/css/admin.css',
		[],
		Utility\get_asset_info( 'admin-style', 'version' )
	);
}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'styles',
		WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/css/frontend.css',
		[],
		Utility\get_asset_info( 'frontend', 'version' )
	);
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}
