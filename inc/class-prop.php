<?php
/**
 * WP Component Library includes: Prop class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

use stdClass;

/**
 * A class representing a prop for a component.
 *
 * @package WP_Component_Library
 */
class Prop {
	/**
	 * Sets the allowed values for the prop type.
	 *
	 * @type array
	 */
	const TYPES = [ 'array', 'bool', 'enum', 'number', 'object', 'string' ];

	/**
	 * The allowed values for this prop, if the type is 'enum'.
	 *
	 * @var array
	 */
	private array $allowed_values = [];

	/**
	 * The default value for this prop.
	 *
	 * @var mixed
	 */
	private $default = '';

	/**
	 * The human-readable description for this prop.
	 *
	 * @var string
	 */
	private string $description = '';

	/**
	 * The name of this prop.
	 *
	 * @var string
	 */
	private string $name = '';

	/**
	 * Whether this prop is required or not.
	 *
	 * @var bool
	 */
	private bool $required = false;

	/**
	 * The type of this prop. Must be one of TYPES above.
	 *
	 * @var string
	 */
	private string $type = 'string';

	/**
	 * The value for this prop.
	 *
	 * @var mixed
	 */
	private $value = null;

	/**
	 * Constructor. Accepts properties for configuration and applies them.
	 *
	 * @param string $name       The prop name.
	 * @param array  $properties An array of key/value pairs to apply.
	 */
	public function __construct( string $name, array $properties ) {
		$this->name = $name;
		foreach ( $properties as $key => $value ) {
			if ( isset( $this->$key ) ) {
				$this->$key = $value;
			}
		}

		// If a default value was not provided, use the dynamic default.
		if ( ! isset( $properties['default'] ) ) {
			$this->set_default();
		}

		// Validate the configuration.
		$this->validate_config();
	}

	/**
	 * Gets the allowed values for this prop.
	 *
	 * @return array The allowed values.
	 */
	public function get_allowed_values(): array {
		return $this->allowed_values;
	}

	/**
	 * Gets the default value for this prop.
	 *
	 * @return mixed The default value.
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Gets the description for this prop.
	 *
	 * @return string The description.
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Gets the name for this prop.
	 *
	 * @return string The name.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the required flag for this prop.
	 *
	 * @return bool The required flag.
	 */
	public function get_required(): bool {
		return $this->required;
	}

	/**
	 * Gets the type for this prop.
	 *
	 * @return string The type.
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Gets the value for this prop.
	 *
	 * @return mixed The value.
	 */
	public function get_value() {
		return ! is_null( $this->value ) ? $this->value : $this->default;
	}

	/**
	 * Sets the value for this prop.
	 *
	 * @param mixed $value The value to set.
	 *
	 * @return mixed The value.
	 */
	public function set_value( $value ) {
		if ( ( 'array' === $this->type && is_array( $value ) )
			|| ( 'bool' === $this->type && is_bool( $value ) )
			|| ( 'enum' === $this->type && in_array( $value, $this->allowed_values, true ) )
			|| ( 'number' === $this->type && is_numeric( $value ) )
			|| ( 'object' === $this->type && is_object( $value ) )
			|| ( 'string' === $this->type && is_string( $value ) )
		) {
			$this->value = $value;
		}
	}

	/**
	 * Validates the currently loaded configuration for this prop and returns
	 * an array with any errors encountered.
	 *
	 * @return array An array with any errors encountered during validation.
	 */
	public function validate_config(): array {
		$errors = [];

		if ( empty( $this->name ) ) {
			$errors[] = __( 'All props must have a name.', 'wp-component-library' );
		}

		if ( empty( $this->description ) ) {
			$errors[] = __( 'All props must have a description.', 'wp-component-library' );
		}

		if ( empty( $this->type ) ) {
			$errors[] = __( 'All props must have a type.', 'wp-component-library' );
		} elseif ( ! in_array( $this->type, self::TYPES, true ) ) {
			$errors[] = __( 'The prop type must be one of the allowed types.', 'wp-component-library' );
		} elseif ( ( 'array' === $this->type && ! is_array( $this->default ) )
			|| ( 'bool' === $this->type && ! is_bool( $this->default ) )
			|| ( 'number' === $this->type && ! is_numeric( $this->default ) )
			|| ( 'object' === $this->type && ! is_object( $this->default ) )
			|| ( 'string' === $this->type && ! is_string( $this->default ) )
		) {
			$errors[] = __( 'The default value must be of the same type as the prop.', 'wp-component-library' );
		} elseif ( 'enum' === $this->type && ! in_array( $this->default, $this->allowed_values, true ) ) {
			$errors[] = __( 'For a prop type of enum, the default value must be one of the allowed values.', 'wp-component-library' );
		}

		return $errors;
	}

	/**
	 * Sets the default value dynamically based on the specified type.
	 */
	private function set_default(): void {
		switch ( $this->type ) {
			case 'array':
				$this->default = [];
				break;
			case 'bool':
				$this->default = false;
				break;
			case 'enum':
				$this->default = $this->allowed_values[0] ?? '';
				break;
			case 'number':
				$this->default = 0;
				break;
			case 'object':
				$this->default = new stdClass();
				break;
			case 'string':
				$this->default = '';
				break;
		}
	}
}
