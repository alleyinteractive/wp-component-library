<?php
/**
 * WP Component Library Includes: Hooks
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

// Register action hooks.
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\action_admin_enqueue_scripts' );
add_action( 'admin_menu', __NAMESPACE__ . '\\action_admin_menu' );

// Register filter hooks.
add_filter( 'wpcl_component_path', __NAMESPACE__ . '\\filter_wpcl_component_path', 10, 2 );

/**
 * An action hook callback for the admin_enqueue_scripts hook.
 */
function action_admin_enqueue_scripts(): void {
	// Extract the version of highlight.php from composer.lock to auto-cache bust.
	$version  = '';
	$lockfile = sprintf( '%s/composer.lock', dirname( __DIR__ ) );
	if ( file_exists( $lockfile ) ) {
		$json = file_get_contents( $lockfile ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		if ( ! empty( $json ) ) {
			$lock = json_decode( $json, true );
			if ( ! empty( $lock['packages'] ) && is_array( $lock['packages'] ) ) {
				foreach ( $lock['packages'] as $package ) {
					if ( 'scrivo/highlight.php' === $package['name'] ) {
						$version = $package['version'];
					}
				}
			}
		}
	}

	// Enqueue the style for highlight.php.
	wp_enqueue_style(
		'wpcl_highlightjs',
		plugin_dir_url( __DIR__ ) . 'vendor/scrivo/highlight.php/styles/darcula.css',
		[],
		$version
	);
}

/**
 * An action hook callback for the admin_menu hook.
 */
function action_admin_menu(): void {
	add_menu_page(
		__( 'Components', 'wp-component-library' ),
		__( 'Components', 'wp-component-library' ),
		/**
		 * Allows for modification of the capability required in order to view
		 * the Components page in the WordPress admin. Defaults to manage_options.
		 *
		 * @param string $capability The capability required to view the Components page. Defaults to manage_options.
		 */
		apply_filters( 'wpcl_admin_capability', 'manage_options' ),
		'wpcl-components',
		function () {
			wpcl_component(
				'wpcl-admin-page',
				[
					/* phpcs:disable WordPress.Security.NonceVerification.Recommended */
					'component'  => isset( $_GET['component'] ) ? sanitize_text_field( wp_unslash( $_GET['component'] ) ) : '',
					'components' => Component::get_component_list(),
					'dogfooding' => isset( $_GET['dogfooding'] ) && true === (bool) $_GET['dogfooding'],
					/* phpcs:enable */
				]
			);
		},
		'dashicons-block-default',
		2
	);
}

/**
 * A filter callback for the wpcl_component_path filter. Override component
 * paths for components that start with `wpcl-` to load them from this plugin.
 *
 * @param string $path The path computed by the plugin.
 * @param string $name The name of the component.
 *
 * @return string The potentially modified component path.
 */
function filter_wpcl_component_path( $path, $name ) {
	return ( 0 === strpos( $name, 'wpcl-' ) )
		? sprintf( '%s/components/%s', dirname( __DIR__ ), $name )
		: $path;
}
