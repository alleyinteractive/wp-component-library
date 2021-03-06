<?php
/**
 * WP Component Library includes: Partials
 *
 * @package WP_Component_Library
 */

use League\CommonMark\GithubFlavoredMarkdownConverter;
use WP_Component_Library\Block_Parser;
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
 * Outputs attributes for a tag, properly escaped. Optionally, pass the $args
 * variable as the second parameter to merge arguments with the class and id
 * props.
 *
 * @param array  $attributes An array of key/value pairs for attributes.
 * @param ?array $args       Optional. Props passed to the component. If present, merges class and id props.
 */
function wpcl_attributes( array $attributes, ?array $args = null ): void {
	// Merge class and id from args, if present.
	$attributes['class'] = classNames( $attributes['class'] ?? '', $args['class'] ?? '' );
	$attributes['id']    = $attributes['id'] ?? $args['id'] ?? '';


	// Loop over attributes, escape, and compile the list for output.
	$attribute_list = [];
	foreach ( $attributes as $key => $value ) {
		// Validate the key against the attribute key spec for HTML.
		if ( preg_match( '/^[^\t\n\f\s\/>"\'=]+$/', $key ) ) {
			// Escape according to attribute type.
			switch ( $key ) {
				case 'action':
				case 'cite':
				case 'formaction':
				case 'href':
				case 'poster':
				case 'src':
					$value = esc_url( $value );
					break;
				default:
					$value = esc_attr( $value );
					break;
			}

			// Add the escaped attribute to the list if it has a value.
			if ( '' !== $value ) {
				$attribute_list[] = sprintf( '%s="%s"', $key, $value );
			}
		}
	}

	// Output the (previously escaped) attributes.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo implode( ' ', $attribute_list );
}

/**
 * A helper function for converting Gutenberg blocks to
 * WPCL components and loading them.
 *
 * @param mixed|null|array $blocks An array of blocks to render, or default to blocks from `post_content`.
 *
 * @return void
 */
function wpcl_blocks( ?array $blocks = null ): void {
	global $post;

	/**
	 * Allows for modification of the Gutenberg blocks that will be rendered.
	 *
	 * @param array $blocks The parsed Gutenberg Blocks.
	 */
	$blocks ??= apply_filters( 'wpcl_blocks', parse_blocks( $post->post_content ) );

	foreach ( $blocks as $block ) {
		Block_Parser::factory( $block )->render();
	}
}

/**
 * Load a component from your theme's component library.
 *
 * @param string $name  The name of the component to load.
 * @param array  $props Optional. An array of props for the component. Defaults to empty array.
 * @param bool   $return_output Optional. Whether to echo the output of the component or only return it.
 * @return string Empty if the component template file was found and rendered. If the output was
 */
function wpcl_component( string $name, array $props = [], $return_output = false ): string {
	ob_start();
	$component = new Component( $name, $props );
	$success   = $component->render();
	$output    = ob_get_clean();

	if ( false === $success ) {
		return '';
	}

	if ( false === $return_output ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output; // Already escaped in component template files.
	}

	// Default return can be treated as "truthy" to determine if
	// the rendering of the component was successful or not.
	return $output;
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
	} elseif ( is_object( $value ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
		$ret .= var_export( $value, true );
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

/**
 * Log if logging is enabled.
 *
 * @param string $message The message to log.
 * @return void
 */
function wpcl_log( string $message ): void {
	if (
		defined( 'WP_DEBUG' )
		&& WP_DEBUG
		&& defined( 'WP_DEBUG_LOG' )
		&& WP_DEBUG_LOG
	) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $message );
	}
}
