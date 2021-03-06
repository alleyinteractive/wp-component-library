<?php
/**
 * A browsable component library in the WordPress admin and helper functions
 * for writing templates using a components-first approach. Inspired by
 * component composition in JavaScript frameworks, most notably React.
 *
 * @package WP_Component_Library
 */

/*
 * Plugin Name: WP Component Library
 * Plugin URI: https://github.com/alleyinteractive/wp-component-library
 * Description: Component library functionality for your WordPress theme.
 * Version: 1.0.0
 * Author: Alley
 */

// If the autoloader doesn't exist yet (no composer install was performed) bail out.
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}

// Composer autoloader.
// Also generates our classmap since we need external packages regardless.
require_once __DIR__ . '/vendor/autoload.php';

// Helpers & kick off plugin hooks.
require_once __DIR__ . '/inc/hooks.php';
require_once __DIR__ . '/inc/partials.php';
