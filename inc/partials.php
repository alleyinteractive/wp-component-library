<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

use League\CommonMark\GithubFlavoredMarkdownConverter;
use WP_Component_Library\Component;

/**
 * Builds a URL to the WP Component Library admin page given configuration
 * parameters.
 *
 * @param string $component  The component slug, if any. If an empty string is passed, prints the link to the index page.
 * @param bool   $dogfooding Whether we are dogfooding (previewing internal components) or not.
 *
 * @return string The admin URL.
 */
function wpcl_admin_url( string $component, bool $dogfooding ): string {
	$query_args = [
		'page' => 'wpcl-components',
	];

	// Set component flag, if provided.
	if ( ! empty( $component ) ) {
		$query_args['component'] = $component;
	}

	// Set dogfooding flag, if provided.
	if ( $dogfooding ) {
		$query_args['dogfooding'] = 1;
	}

	return admin_url( sprintf( 'admin.php?%s', http_build_query( $query_args ) ) );
}

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
function wpcl_class( ...$args ): void {
	$classnames = classNames( ...$args );
	if ( ! empty( $classnames ) ) {
		echo 'class="' . esc_attr( $classnames ) . '"';
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
 * Given a string of markdown, safely converts it to HTML and outputs it.
 *
 * @param string $markdown The markdown string to output.
 */
function wpcl_markdown( string $markdown ): void {
	$converter = new GithubFlavoredMarkdownConverter( [ 'allow_unsafe_links' => false ] );
	echo wp_kses_post( $converter->convertToHtml( $markdown ) );
}

/**
 * A function to recursively turn values into a string representing the PHP
 * code used to generate the value. Used for generating previews of PHP code.
 *
 * @param mixed $value The value to convert.
 * @param int   $level The indentation level.
 *
 * @return string A string representation of the PHP code.
 */
function wpcl_phpify( $value, int $level = 0 ): string {
	$ret = '';
	if ( is_array( $value ) ) {
		$ret      .= '[';
		$multiline = false;
		foreach ( $value as $key => $item ) {
			if ( ! is_int( $key ) ) {
				$multiline = true;
				$ret      .= "\n" . wpcl_tab( '\'' . $key . '\' => ', $level + 1 );
			}
			$ret .= wpcl_phpify( $item, $level + 1 ) . ',';
		}
		if ( $multiline ) {
			$ret .= "\n" . wpcl_tab( ']', $level );
		} else {
			$ret .= ' ]';
		}
	} elseif ( is_string( $value ) ) {
		$ret .= '\'' . $value . '\'';
	} elseif ( true === $value ) {
		$ret .= 'true';
	} elseif ( false === $value ) {
		$ret .= 'false';
	} else {
		$ret .= $value;
	}
	return $ret;
}

/**
 * Given a string and a number of tabs to apply, indents the string by the
 * number of specified tabs.
 *
 * @param string $string The string to indent.
 * @param int    $times  The number of tabs to apply.
 *
 * @return string The tabbed string.
 */
function wpcl_tab( string $string, int $times ): string {
	return str_repeat( "\t", $times ) . $string;
}
