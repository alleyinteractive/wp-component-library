<?php
/**
 * WP Component Library includes: Core Block Parser
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

use WP_Component_Library\Blocks\Block;
use WP_Component_Library\Blocks\Anonymous;

/**
 * A helper class to parse various core blocks into WPCL components.
 */
class Block_Parser {

	/**
	 * Parses a block and returns relevant components if they exist.
	 *
	 * @param array $raw_block The raw representation of a block.
	 * @return array
	 */
	public static function factory( array $raw_block ): Block {
		$class_to_load = self::get_class_for_block( $raw_block['blockName'] );

		try {
			// This will attempt to use any registered autoloader.
			// If no class is found, we'll fallabck to an Anonymous block.
			$block = new $class_to_load( $raw_block );
		} catch ( \Throwable $e ) {
			$block = new Anonymous( $raw_block );
			// translators: class name.
			! empty( $class_to_load ) && wpcl_log( sprintf( __( '%s was not found. You may need to dump your autoloader.', 'wp-component-library' ), $class_to_load ) );
		}

		return $block;
	}

	/**
	 * Get the PHP class for the given block.
	 *
	 * @param string|null $block_name The block name. Example: `core/paragraph`.
	 * @return string Class string or an empty string if not found.
	 */
	private static function get_class_for_block( ?string $block_name ): string {
		// Goots™ can insert null blocks for some reason.
		if ( empty( $block_name ) ) {
			return '';
		}

		[$dir, $file_name] = explode( '/', $block_name, 2 );
		return self::build_class_string( $dir, $file_name );
	}

	/**
	 * Construct a full namespaced PHP class string.
	 *
	 * @param string $directory The sub-directory/namespace.
	 * @param string $file_name The final class name.
	 * @return string The class string.
	 */
	private static function build_class_string( string $directory, string $file_name ): string {
		return '\\' . __NAMESPACE__ . '\\Blocks\\' . ucfirst( $directory ) . '\\' . ucfirst( $file_name );
	}
}
