<?php
/**
 * Plugin Name:       WordPress Plugin Scaffold
 * Plugin URI:        https://github.com/Highfivery/wp-plugin-scaffold
 * Description:       A plugin WordPress scaffold to help you get started quickly.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Highfivery
 * Author URI:        https://highfivery.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-plugin-scaffold
 *
 * @package  WPPluginScaffold
 */

// Useful global constants.
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_VERSION', '0.1.0' );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_DIST_PATH', WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/' );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_DIST_URL', WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/' );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_INC', WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'includes/' );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_BLOCK_DIR', WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'blocks/' );
define( 'WP_PLUGIN_SCAFFOLD_PLUGIN_BLOCK_DIST_DIR', WP_PLUGIN_SCAFFOLD_PLUGIN_PATH . 'dist/blocks/' );

require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'core.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'overrides.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'template-tags.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'utility.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'blocks.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'helpers.php';
require_once WP_PLUGIN_SCAFFOLD_PLUGIN_INC . 'database.php';

// Run the setup functions.
WPPluginScaffold\Core\setup();
//WPPluginScaffold\Database\setup();
//WPPluginScaffold\Blocks\setup();

