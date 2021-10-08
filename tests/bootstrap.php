<?php
/**
 * WP Component Library Tests: Bootstrap File
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

// Map path to phpunit-polyfills for WordPress >= 5.9.
const WP_TESTS_PHPUNIT_POLYFILLS_PATH = __DIR__ . '/../vendor/yoast/phpunit-polyfills'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound

// Load Core's test suite.
$wp_component_library_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $wp_component_library_tests_dir ) {
	$wp_component_library_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $wp_component_library_tests_dir . '/includes/functions.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable

/**
 * Setup our environment.
 */
function wp_component_library_manually_load_environment() {
	/*
	 * Tests won't start until the uploads directory is scanned, so use the
	 * lightweight directory from the test install.
	 *
	 * @see https://core.trac.wordpress.org/changeset/29120.
	 */
	add_filter(
		'pre_option_upload_path',
		function () {
			return ABSPATH . 'wp-content/uploads';
		}
	);

	// Load this plugin.
	require_once dirname( __DIR__ ) . '/index.php';
}
tests_add_filter( 'muplugins_loaded', 'wp_component_library_manually_load_environment' );

// Disable the emoji detection script, because it throws unnecessary errors.
tests_add_filter(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}
);

// Include core's bootstrap.
require $wp_component_library_tests_dir . '/includes/bootstrap.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
