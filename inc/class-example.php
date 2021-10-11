<?php
/**
 * WP Component Library includes: Example class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

/**
 * A class representing data for an example usage of a component.
 *
 * @package WP_Component_Library
 */
class Example {
	/**
	 * An array of key/value pairs representing props for this example.
	 *
	 * @var array
	 */
	private array $props = [];

	/**
	 * The title for this example.
	 *
	 * @var string
	 */
	private string $title = '';

	/**
	 * Constructor. Sets the title and the props for this example.
	 *
	 * @param string $title The title for this example.
	 * @param array  $props An array of key/value pairs representing props for this example.
	 */
	public function __construct( string $title, array $props ) {
		$this->title = $title;
		$this->props = $props;
	}

	/**
	 * Gets the array of props for this example.
	 *
	 * @return array The array of props.
	 */
	public function get_props(): array {
		return $this->props;
	}

	/**
	 * Gets the title for this example.
	 *
	 * @return string The title.
	 */
	public function get_title(): string {
		return $this->title;
	}
}
