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
	 * The reason(s) the current component is invalid.
	 *
	 * @var string[]
	 */
	private array $error_reasons = [];

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
	 * Stores the contents of the component's README file.
	 *
	 * @var string
	 */
	private string $readme = '';

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
	 * A factory method for getting an array of component objects dynamically.
	 *
	 * @return array An array of Component objects.
	 */
	public static function get_component_list(): array {
		$components = [];

		// Try to find the directory that contains components.
		$components_dir = '';
		foreach ( self::try_dirs() as $check ) {
			$check_dir = sprintf( '%s/components', $check );
			if ( is_dir( $check_dir ) ) {
				$components_dir = $check_dir;
				break;
			}
		}

		// If we don't have a valid components directory, bail out.
		if ( empty( $components_dir ) ) {
			return [];
		}

		// Scan the components directory to build a list of components.
		$slugs = array_filter(
			scandir( $components_dir ),
			function ( $dir ) use ( $components_dir ) {
				return '.' !== $dir
					&& '..' !== $dir
					&& is_dir( sprintf( '%s/%s', $components_dir, $dir ) );
			}
		);
		foreach ( $slugs as $slug ) {
			$components[] = new self( $slug );
		}

		return $components;
	}

	/**
	 * Constructor. Accepts the name of the component.
	 *
	 * @param string $name    The name of the component.
	 * @param array  $props   Values for props for this component.
	 * @param string $context Optional. Controls which properties are populated. Possible values are 'usage' and 'preview'. Defaults to 'usage'.
	 */
	public function __construct( string $name, array $props = [], string $context = 'usage' ) {
		$this->name = $name;
		$this->locate_component();
		$this->load_config( 'preview' === $context );
		$this->load_props( $props );
		$this->maybe_show_errors();
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
	 * Gets the text of the README for this component.
	 *
	 * @return string The contents of README.md.
	 */
	public function get_readme(): string {
		return $this->readme;
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
	 * Loads example data from the example at the given index.
	 *
	 * @param int $index The index of the example data to load.
	 */
	public function load_example_data( int $index ) {
		// Reset prop values.
		foreach ( $this->props as $prop ) {
			$prop->set_value( null );
		}

		// Load props from examples.
		if ( isset( $this->examples[ $index ] ) ) {
			$this->load_props( $this->examples[ $index ]->get_props() );
		}
	}

	/**
	 * Show errors on WPCL admin screens only.
	 *
	 * @return void
	 */
	private function maybe_show_errors(): void {
		if ( empty( $this->error_reasons ) || ! is_admin() ) {
			return;
		}

		/**
		 * Whether to show component errors or not.
		 *
		 * @param bool $hide_notices Flag to hide/show notices.
		 */
		if ( apply_filters( 'wpcl_hide_component_errors_as_notices', false ) ) {
			return;
		}

		foreach ( $this->error_reasons as $error ) {
			// @todo Refactor this to use named hooks.
			add_action(
				'wpcl_admin_notices',
				function() use ( $error ) {
					$error = "$this->name - $error";
					wpcl_component(
						'wpcl-admin-notice',
						[
							'type' => 'error',
							'text' => $error,
						]
					);
				}
			);
		}
	}

	/**
	 * Renders this template part, using the props system to validate values
	 * and provide fallbacks. Mimics the behavior of get_template_part, but
	 * uses the negotiated path established by locate_component instead of
	 * WP's built-in template locator. Fires the same hooks as get_template_part
	 * for increased compatibility with existing hooks and workflows.
	 *
	 * @return bool True if the component template exists. False otherwise.
	 */
	public function render() : bool {
		try {
			// Ensure the template file exists before attempting to render it.
			if ( ! file_exists( sprintf( '%s/template.php', $this->path ) ) ) {
				$this->set_error_reason( __( 'No template found.', 'wp-component-library' ) );
				return false;
			}

			// Populate values for all props, using defaults if no value is provided.
			$props = [];
			foreach ( $this->props as $prop_name => $prop ) {
				$props[ $prop_name ] = $prop->get_value();
			}

			/* phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound */
			$slug = sprintf( 'components/%s/template', $this->name );
			do_action( "get_template_part_{$slug}", $slug, null, $props );
			do_action( 'get_template_part', $slug, null, [ "{$slug}.php" ], $props );
			/* phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound */

			load_template( sprintf( '%s/template.php', $this->path ), false, $props );
		} catch ( \Throwable $e ) {
			wpcl_log(
				sprintf(
					// translators: the component name and the returned error message.
					__( 'There was a problem rendering the component: %1$s. Error: %2$s', 'wp-component-library' ),
					$this->name,
					$e->getMessage()
				)
			);
			$this->set_error_reason( __( 'There was a throwable error during rendering.', 'wp-component-library' ) );
			return false;
		}
		return true;
	}

	/**
	 * Set's the reason the component cannot be rendered/parsed.
	 *
	 * @param string $reason The Reason™.
	 * @return void
	 */
	public function set_error_reason( string $reason ): void {
		$this->error_reasons[] = $reason;
		$this->error_reasons   = array_unique( $this->error_reasons );
	}

	/**
	 * Returns an array of directories to look in for the components directory.
	 * Uses the same logic as the `locate_template` function.
	 *
	 * @return array An array of directory paths to look in for the components directory.
	 */
	private static function try_dirs(): array {
		// If we're dogfooding, look in our own component dir.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['dogfooding'] ) && true === (bool) $_GET['dogfooding'] ) {
			return [ dirname( __DIR__ ) ];
		}

		return [
			get_stylesheet_directory(),
			get_template_directory(),
			ABSPATH . WPINC . '/theme-compat/',
		];
	}

	/**
	 * Loads configuration from a component.json file in the path.
	 *
	 * @param bool $load_metadata Whether to load metadata (examples and readme) or not.
	 */
	private function load_config( bool $load_metadata ): void {
		// Ensure we have a path and the component file exists.
		$filepath = sprintf( '%s/component.json', $this->path );
		if ( empty( $this->path ) || ! file_exists( $filepath ) ) {
			$this->set_error_reason( __( 'Component JSON configuration not found.', 'wp-component-library' ) );
			return;
		}

		// Load the contents of the configuration.
		$config = json_decode( file_get_contents( $filepath ), true ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown

		if ( is_null( $config ) ) {
			// translators: the invalid file's name and the name of the current component.
			wpcl_log( sprintf( __( 'The %1$s config file is invalid for the %2$s component.', 'wp-component-library' ), 'component.json', $this->name ) );
			$this->set_error_reason( __( 'Component JSON configuration is invalid.', 'wp-component-library' ) );
		}

		// Set the title.
		$this->title = $config['title'] ?? '';

		// Loop over props and add them to this component's definition.
		foreach ( $config['props'] ?? [] as $prop_name => $prop_definition ) {
			$this->props[ $prop_name ] = new Prop( $prop_name, $prop_definition );
		}

		// Add props for style, class and id, which are supported on all components.
		$this->props['class'] = new Prop( 'class', [ 'description' => __( 'Additional classes to apply to the element.', 'wp-component-library' ) ] );
		$this->props['id']    = new Prop( 'id', [ 'description' => __( 'The HTML ID to apply to the element.', 'wp-component-library' ) ] );

		// If we aren't loading metadata, then stop here.
		if ( ! $load_metadata ) {
			return;
		}

		// Loop over example data and add it to this component's definition.
		foreach ( $config['examples'] ?? [] as $example ) {
			$example_title    = $example['title'] ?? '';
			$example_props    = $example['props'] ?? [];
			$this->examples[] = new Example( $example_title, $example_props );
		}

		// See if there is a README.
		$readmepath = sprintf( '%s/README.md', $this->path );
		if ( ! file_exists( $readmepath ) ) {
			return;
		}

		// Get the contents of the README and store them.
		$this->readme = file_get_contents( $readmepath ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
	}

	/**
	 * Loads prop values with what is provided.
	 *
	 * @param array $props An associative array of prop names to values.
	 */
	private function load_props( array $props ) {
		foreach ( $props as $prop_name => $prop_value ) {
			if ( isset( $this->props[ $prop_name ] ) ) {
				$this->props[ $prop_name ]->set_value( $prop_value );
			}
		}
	}

	/**
	 * Based on the name, locates the component directory using the same logic
	 * as the `locate_template` function, and saves the path to the path
	 * property.
	 */
	private function locate_component(): void {
		foreach ( self::try_dirs() as $base ) {
			if ( file_exists( sprintf( '%s/components/%s/template.php', $base, $this->name ) ) ) {
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
