<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

use WP_Component_Library\Component;

/**
 * Conditionally output classes as part of a class attribute on an HTML element.
 * Handles negotiating the class list and wrapping it in the class="" attribute
 * along with proper escaping. Does not output anything if there are no classes
 * to print.
 *
 * Pass a variable-length number of arguments to this function to output a
 * class list. Strings and arrays of strings are added to the class list, and
 * associative arrays with non-numerical keys are assumed to be in the form of
 * classname => condition, where condition is evaluated, and if truthy, the
 * classname is included in the list.
 *
 * @param mixed ...$args A variable number of arguments that can be strings, arrays, or associative arrays.
 */
function wpcl_classnames( ...$args ): void {
	$classnames = classNames( ...$args );
	if ( ! empty( $classnames ) ) {
		echo 'class="' . esc_attr( $classnames ) . '"';
	}
}

/**
 * Conditionally outputs an `id` attribute for an HTML element, given an ID as
 * a string. If the ID is empty, does not output the attribute.
 *
 * @param string $id The ID to print, or an empty string to not output an ID.
 */
function wpcl_id( string $id ): void {
	if ( ! empty( $id ) ) {
		echo 'id="' . esc_attr( $id ) . '"';
	}
}

/**
 * Load a component from your theme's component library.
 *
 * @param string $name  The name of the component to load.
 * @param array  $props Optional. An array of props for the component. Defaults to empty array.
 */
function wpcl_component( string $name, array $props = [] ): void {
	$component = new Component( $name, $props );
	$component->render();
}
