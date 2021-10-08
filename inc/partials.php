<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

/**
 * Load a component from your theme's component library.
 *
 * @param string $name  The name of the component to load.
 * @param array  $props Optional. An array of props for the component. Defaults to empty array.
 */
function wpcl_component( string $name, array $props = [] ) {
	get_template_part( sprintf( 'components/%s/index', $name ), $props );
}
