<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

/**
 * Given an array of classes, combines them into a classes string and safely
 * outputs them.
 *
 * @param array $classes An array of classes to apply to an element.
 */
function wpcl_classes( array $classes ): void {
	// Combine into a class list string.
	$class_list = implode( ' ', $classes );

	// Re-explode to separate classes.
	$class_list = explode( ' ', $class_list );

	// Sanitize each class name.
	$class_list = array_map( 'sanitize_html_class', $class_list );

	// Remove any empty items.
	$class_list = array_filter( $class_list );

	// Combine back into a string.
	$class_list = implode( ' ', $class_list );

	echo esc_attr( $class_list );
}

/**
 * Load a component from your theme's component library.
 *
 * @param string $name  The name of the component to load.
 * @param array  $props Optional. An array of props for the component. Defaults to empty array.
 */
function wpcl_component( string $name, array $props = [] ): void {
	get_template_part( sprintf( 'components/%s/index', $name ), $props );
}
