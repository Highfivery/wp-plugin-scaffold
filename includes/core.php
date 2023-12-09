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
	add_action( 'admin_menu', $n( 'add_options_page' ) );
	add_action( 'admin_init', $n( 'register_settings' ) );

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

function add_options_page() {
	add_submenu_page(
		'options-general.php',
		__( 'WordPress Plugin Scaffold Settings', 'wp-plugin-scaffold' ),
		__( 'WP Plugin Scaffold', 'wp-plugin-scaffold' ),
		'manage_options',
		'wp-plugin-scaffold',
		__NAMESPACE__ . '\options_page'
	);
}

function options_page() {
	$options = get_option( 'wp_plugin_scaffold' );
	?>
	<div class="wrap">
		<h1><?php echo __( 'WordPress Plugin Scaffold Settings', 'wp-plugin-scaffold' ); ?></h1>
		<form method="post" action="options.php">
		<?php
		settings_fields( 'wp-plugin-scaffold' );
		do_settings_sections( 'wp-plugin-scaffold' );
		submit_button();
		?>
		</form>
	</div>
	<?php
}

function register_settings() {
	$plugin_settings = [
		'general' => [
			'title'    => __( 'General Settings', 'wp-plugin-scaffold' ),
			'settings' => [
				[
					'title' => __( 'Open Name', 'wp-plugin-scaffold' ),
					'field' => [
						'key'         => 'option',
						'type'        => 'text',
						'classes'     => 'regular-text ltr',
						'placeholder' => __( 'Option placeholder', 'wp-plugin-scaffold' ),
					],
				],
			],
		],
	];

	register_setting( 'wp-plugin-scaffold', 'wp_plugin_scaffold' );

	foreach ( $plugin_settings as $section_key => $section ) {
		add_settings_section(
			'wp_plugin_scaffold_' . $section_key,
			$section['title'],
			__NAMESPACE__ . '\settings_section',
			'wp-plugin-scaffold'
		);

		if ( ! empty ( $section['settings'] ) ) {
			foreach ( $section['settings'] as $key => $setting ) {
				add_settings_field(
					$setting['field']['key'],
					$setting['title'],
					__NAMESPACE__ . '\settings_field',
					'wp-plugin-scaffold',
					'wp_plugin_scaffold_' . $section_key,
					$setting['field']
				);
			}
		}
	}
}

function settings_section() {
	?>
	<?php
}

function settings_field( $args ) {
	$value = get_option( 'wp_plugin_scaffold' );

	$setting_name = 'wp_plugin_scaffold[' . $args['key'] . ']';

	switch ( $args['type'] ) {
		case 'url':
		case 'text':
		case 'password':
		case 'number':
		case 'email':
			?>
			<input
				id="<?php echo esc_attr( $args['key'] ); ?>"
				name="<?php echo esc_attr( $setting_name ); ?>"
				type="<?php echo esc_attr( $args['type'] ); ?>"
				<?php if ( ! empty( $args['value'] ) ) : ?>
					value="<?php echo esc_attr( $args['value'] ); ?>"
				<?php endif; ?>
				<?php if ( ! empty( $args['classes'] ) ) : ?>
					class="<?php echo esc_attr( $args['classes'] ); ?>"
				<?php endif; ?>
				<?php if ( ! empty( $args['placeholder'] ) ) : ?>
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				<?php endif; ?>
				<?php if ( ! empty( $args['min'] ) ) : ?>
					min="<?php echo esc_attr( $args['min'] ); ?>"
				<?php endif; ?>
				<?php if ( ! empty( $args['max'] ) ) : ?>
					max="<?php echo esc_attr( $args['max'] ); ?>"
				<?php endif; ?>
				<?php if ( ! empty( $args['step'] ) ) : ?>
					step="<?php echo esc_attr( $args['step'] ); ?>"
				<?php endif; ?>
			/>
			<?php
			break;
	}
}
