<?php
/**
 * WP Component Library includes: Component class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library;

/**
 * A class representing a component.
 *
 * @package WP_Component_Library
 */
class Component {
	/**
	 * An array of Examples for this component.
	 *
	 * @var Example[]
	 */
	private array $examples = [];

	/**
	 * Stores the name of the component.
	 *
	 * @var string
	 */
	private string $name = '';

	/**
	 * Stores the path to the component.
	 *
	 * @var string
	 */
	private string $path = '';

	/**
	 * An array of Props for this component.
	 *
	 * @var Prop[]
	 */
	private array $props = [];

	/**
	 * The title of this component.
	 *
	 * @var string
	 */
	private string $title = '';

	/**
	 * Constructor. Accepts the name of the component.
	 *
	 * @param string $name The name of the component.
	 */
	public function __construct( string $name ) {
		$this->name = $name;
		$this->locate_component();
		$this->load_config();
	}

	/**
	 * Gets the array of examples for this component.
	 *
	 * @return Example[] An array of Example objects.
	 */
	public function get_examples(): array {
		return $this->examples;
	}

	/**
	 * Gets the name of this component.
	 *
	 * @return string The name of this component.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the array of props for this component.
	 *
	 * @return Prop[] An array of Prop objects.
	 */
	public function get_props(): array {
		return $this->props;
	}

	/**
	 * Gets the title for this component.
	 *
	 * @return string The title for this component.
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Loads configuration from a component.json file in the path.
	 */
	private function load_config(): void {
		// Ensure we have a path and the component file exists.
		$filepath = sprintf( '%s/component.json', $this->path );
		if ( empty( $this->path ) || ! file_exists( $filepath ) ) {
			return;
		}

		// Load the contents of the configuration.
		$config = json_decode( file_get_contents( $filepath ), true );

		// Set the title.
		$this->title = $config['title'] ?? '';

		// Loop over example data and add it to this component's definition.
		foreach ( $config['examples'] ?? [] as $example ) {
			$example_title = $example['_title'] ?? '';
			unset( $example['_title'] );
			$this->examples[] = new Example( $example_title, $example );
		}

		// Loop over props and add them to this component's definition.
		foreach ( $config['props'] ?? [] as $prop_name => $prop_definition ) {
			$this->props[] = new Prop( $prop_name, $prop_definition );
		}
	}

	/**
	 * Based on the name, locates the component directory using the same logic
	 * as the `locate_template` function, and saves the path to the path
	 * property.
	 */
	private function locate_component(): void {
		$try = [
			STYLESHEETPATH,
			TEMPLATEPATH,
			ABSPATH . WPINC . '/theme-compat/',
		];
		foreach ( $try as $base ) {
			if ( file_exists( sprintf( '%s/components/%s/template', $base, $this->name ) ) ) {
				$this->path = sprintf( '%s/components/%s', $base, $this->name );
			}
		}

		/**
		 * Allow the component path to be filtered.
		 *
		 * @param string $path The path computed by the plugin.
		 * @param string $name The name of the component.
		 */
		$this->path = apply_filters( 'wpcl_component_path', $this->path, $this->name );
	}
}
