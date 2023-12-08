<?php
/**
 * Gutenberg Blocks setup
 *
 * @package WPPluginScaffold
 */

namespace WPPluginScaffold\Blocks;

use WPPluginScaffold\Utility;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $func ) {
		return __NAMESPACE__ . "\\$func";
	};

	add_action( 'enqueue_block_editor_assets', $n( 'blocks_editor_styles' ) );

	add_action( 'init', $n( 'register_plugin_blocks' ) );

	add_action( 'init', $n( 'register_block_pattern_categories' ) );
}

/**
 * Automatically registers all blocks that are located within the includes/blocks directory
 *
 * @return void
 */
function register_plugin_blocks() {
	// Register all the blocks in the plugin.
	if ( file_exists( WP_PLUGIN_SCAFFOLD_PLUGIN_BLOCK_DIST_DIR ) ) {
		$block_json_files = glob( WP_PLUGIN_SCAFFOLD_PLUGIN_BLOCK_DIST_DIR . '*/block.json' );

		// auto register all blocks that were found.
		foreach ( $block_json_files as $filename ) {

			$block_folder = dirname( $filename );

			$block_options = [];

			$markup_file_path = $block_folder . '/markup.php';
			if ( file_exists( $markup_file_path ) ) {

				// Only add the render callback if the block has a file called markup.php in it's directory.
				$block_options['render_callback'] = function( $attributes, $content, $block ) use ( $block_folder ) {

					// Create helpful variables that will be accessible in markup.php file.
					$context = $block->context;

					// Get the actual markup from the markup.php file.
					ob_start();
					include $block_folder . '/markup.php';
					return ob_get_clean();
				};
			}

			register_block_type_from_metadata( $block_folder, $block_options );
		}
	}
}

/**
 * Enqueue editor-only JavaScript/CSS for blocks.
 *
 * @return void
 */
function blocks_editor_styles() {
	wp_enqueue_style(
		'editor-style-overrides',
		WP_PLUGIN_SCAFFOLD_PLUGIN_URL . '/dist/css/editor-style-overrides.css',
		[],
		Utility\get_asset_info( 'editor-style-overrides', 'version' )
	);
}

/**
 * Register block pattern categories
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/
 *
 * @return void
 */
function register_block_pattern_categories() {

	// Register a block pattern category.
	register_block_pattern_category(
		'wp-plugin-scaffold',
		[ 'label' => __( 'WordPress Plugin Scaffold', 'wp-plugin-scaffold' ) ]
	);
}
