<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

use WP_Component_Library\Classnames;
use WP_Component_Library\Component;

/**
 * A PHP implementation of the `classnames` npm package. Makes it easy to both
 * combine lists of classes and conditionally include classes.
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
	$classnames = new Classnames( ...$args );
	echo esc_attr( $classnames->get_classlist() );
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
