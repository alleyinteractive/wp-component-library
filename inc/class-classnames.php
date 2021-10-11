<?php
/**
 * WP Component Library includes: Classnames class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

/**
 * A class to represent a dynamic list of classes created using the classnames
 * function.
 *
 * @pacakge WP_Component_Library
 */
class Classnames {
	/**
	 * An array of class names parsed by this class.
	 *
	 * @var array
	 */
	private $classnames = [];

	/**
	 * Constructor function. Accepts a variable-length list of arguments to be
	 * parsed and converted into classnames.
	 *
	 * @param array ...$args An array of arguments passed to this function.
	 */
	public function __construct( ...$args ) {
		foreach ( $args as $arg ) {
			$this->parse_arg( $arg );
		}
	}

	/**
	 * Returns the array of classes that were parsed via the constructor.
	 *
	 * @return array An array of class names.
	 */
	public function get_classes(): array {
		return $this->classnames;
	}

	/**
	 * Returns a space-separated list of classnames based on what was parsed via
	 * the constructor.
	 *
	 * @return string A list of class names.
	 */
	public function get_classlist(): string {
		return implode( ' ', $this->classnames );
	}

	/**
	 * Adds a classname to the classnames array.
	 *
	 * @param string $classname The classname to add.
	 */
	private function add_classname( string $classname ) {
		$classname = trim( $classname );
		if ( ! empty( $classname ) && ! in_array( $classname, $this->classnames, true ) ) {
			$this->classnames[] = $classname;
		}
	}

	/**
	 * Removes a classname from the classnames array.
	 *
	 * @param string $classname The classname to remove.
	 */
	private function remove_classname( string $classname ) {
		$classname        = trim( $classname );
		$this->classnames = array_values(
			array_filter(
				$this->classnames,
				function ( $check ) use ( $classname ) {
					return $check !== $classname;
				}
			)
		);
	}

	/**
	 * Recursively parses a single argument, which could be a string or an array.
	 *
	 * @param string|array $arg The argument to parse.
	 */
	private function parse_arg( $arg ) {
		if ( is_array( $arg ) ) {
			foreach ( $arg as $key => $value ) {
				if ( is_numeric( $key ) ) {
					if ( is_string( $value ) ) {
						$this->add_classname( $value );
					} elseif ( is_array( $value ) ) {
						$this->parse_arg( $value );
					}
				} elseif ( ! empty( $value ) ) {
					$this->add_classname( $key );
				} else {
					$this->remove_classname( $key );
				}
			}
		} elseif ( is_string( $arg ) ) {
			if ( false !== strpos( $arg, ' ' ) ) {
				$this->parse_arg( explode( ' ', $arg ) );
			} else {
				$this->add_classname( $arg );
			}
		} elseif ( is_numeric( $arg ) && ! empty( $arg ) ) {
			$this->add_classname( (string) $arg );
		}
	}
}
