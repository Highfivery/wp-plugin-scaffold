<?php
/**
 * Utility functions for the plugin.
 *
 * This file is for custom helper functions.
 *
 * @link https://developer.wordpress.org/themes/basics/template-tags/
 * @package WPPluginScaffold
 */

namespace WPPluginScaffold\Utility;

/**
 * Get asset info from extracted asset files
 *
 * @param string $slug      Asset slug as defined in build/webpack configuration.
 * @param string $attribute Optional attribute to get. Can be version or dependencies.
 * @return string|array
 */
function get_asset_info( $slug, $attribute = null ) {
	if ( file_exists( WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php' ) ) {
		$asset = require WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/js/' . $slug . '.asset.php';
	} elseif ( file_exists( WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php' ) ) {
		$asset = require WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/css/' . $slug . '.asset.php';
	} else {
		return null;
	}

	if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
		return $asset[ $attribute ];
	}

	return $asset;
}
