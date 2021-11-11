<?php
/**
 * WP Component Library Tests: Test_Component class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Component;

/**
 * A class to test the behavior of the Component class.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Component extends WP_UnitTestCase {
	/**
	 * A data provider for the test_render function. Returns an array of arrays
	 * representing function arguments.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_render(): array {
		return [
			'Test Button example 1' => [
				'button',
				0,
				'<a class="button button--primary" href="https://www.example.org/newsletter">Sign Up for Our Newsletter</a>',
			],
			'Test button example 2' => [
				'button',
				1,
				'<button class="button button--secondary newsletter-signup" type="button" id="newsletter-signup-footer">Sign Up for Our Newsletter</button>',
			],
			'Test nested component' => [
				'forms/input',
				0,
				'<input type="text">',
			],
			'Test nested component with props' => [
				'forms/input',
				1,
				'<input type="checkbox">',
			],
			'Test deeply nested component' => [
				'forms/complex/textarea',
				0,
				'<textarea></textarea>',
			],
			'Test deeply nested component with dashes in filename' => [
				'forms/deeply-nested/simple-label',
				0,
				'<label></label>',
			],
		];
	}

	/**
	 * A callback function for the wpcl_component_path filter. Overrides the
	 * default location, which assumes we're looking for a component in the
	 * active theme, to instead use the test components defined in this plugin.
	 *
	 * @param string $path The path that the plugin wants to use, which will be empty.
	 * @param string $name The name of the component we're trying to locate.
	 *
	 * @return string The path to the test component with the given name in the tests directory of this plugin.
	 */
	public function filter_wpcl_component_path( string $path, string $name ): string {
		return sprintf( '%s/components/%s', __DIR__, $name );
	}

	/**
	 * Actions to be run before every test.
	 */
	public function setUp() {
		parent::setUp();
		add_filter( 'wpcl_component_path', [ $this, 'filter_wpcl_component_path' ], 10, 2 );
	}

	/**
	 * Actions to be run after every test.
	 */
	public function tearDown() {
		parent::tearDown();
		remove_filter( 'wpcl_component_path', [ $this, 'filter_wpcl_component_path' ] );
	}

	/**
	 * Tests the load function for the component to ensure that it loads the
	 * configuration data from the JSON file correctly.
	 */
	public function test_load() {
		$component = new Component( 'button', [], 'preview' );
		$examples  = $component->get_examples();
		$props     = $component->get_props();
		$this->assertEquals( 'button', $component->get_name() );
		$this->assertEquals( 'Button', $component->get_title() );
		$this->assertEquals( 'A link that is styled to look like a button', $examples[0]->get_title() );
		$this->assertEquals(
			[
				'href'    => 'https://www.example.org/newsletter',
				'text'    => 'Sign Up for Our Newsletter',
				'variant' => 'primary',
			],
			$examples[0]->get_props()
		);
		$this->assertEquals( 'A button with a custom class that will be targeted by JavaScript', $examples[1]->get_title() );
		$this->assertEquals(
			[
				'class'   => 'newsletter-signup',
				'id'      => 'newsletter-signup-footer',
				'text'    => 'Sign Up for Our Newsletter',
				'variant' => 'secondary',
			],
			$examples[1]->get_props()
		);
		$this->assertEquals( 'href', $props['href']->get_name() );
		$this->assertEquals( '', $props['href']->get_default() );
		$this->assertEquals( 'If provided, renders `a` instead of `button`.', $props['href']->get_description() );
		$this->assertEquals( false, $props['href']->get_required() );
		$this->assertEquals( 'string', $props['href']->get_type() );
		$this->assertEquals( 'text', $props['text']->get_name() );
		$this->assertEquals( '', $props['text']->get_default() );
		$this->assertEquals( 'Displays the given text inside the button.', $props['text']->get_description() );
		$this->assertEquals( true, $props['text']->get_required() );
		$this->assertEquals( 'string', $props['text']->get_type() );
		$this->assertEquals( 'type', $props['type']->get_name() );
		$this->assertEquals( [ 'button', 'submit', 'reset' ], $props['type']->get_allowed_values() );
		$this->assertEquals( 'button', $props['type']->get_default() );
		$this->assertEquals( 'If rendering a button element, the type to use.', $props['type']->get_description() );
		$this->assertEquals( false, $props['type']->get_required() );
		$this->assertEquals( 'enum', $props['type']->get_type() );
		$this->assertEquals( 'variant', $props['variant']->get_name() );
		$this->assertEquals( [ 'primary', 'secondary' ], $props['variant']->get_allowed_values() );
		$this->assertEquals( 'primary', $props['variant']->get_default() );
		$this->assertEquals( 'Specifies the button\'s style.', $props['variant']->get_description() );
		$this->assertEquals( false, $props['variant']->get_required() );
		$this->assertEquals( 'enum', $props['variant']->get_type() );
	}

	/**
	 * Tests the component by loading example data for the example component and
	 * rendering it.
	 *
	 * @dataProvider data_render
	 *
	 * @param string $component The name of the sample component to load.
	 * @param int    $index     The index of the example to load.
	 * @param string $expected  The expected HTML output, minus newlines, and with whitespace trimmed.
	 */
	public function test_render( string $component, int $index, string $expected ) {
		// Render the component's output.
		$component = new Component( $component, [], 'preview' );
		$component->load_example_data( $index );
		ob_start();
		$component->render();
		$output = ob_get_contents();
		ob_end_clean();

		// Clean up the component's output by running it through DOMDocument to strip whitespace.
		libxml_use_internal_errors( true );
		$doc = new DOMDocument();
		$doc->loadHTML( mb_convert_encoding( $output, 'HTML-ENTITIES', 'UTF-8' ) );
		$xpath  = new DOMXPath( $doc );
		$node   = $xpath->query( '//body' )->item( 0 )->childNodes->item( 0 );
		$output = $node->ownerDocument->saveHTML( $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		libxml_clear_errors();

		$this->assertEquals( $expected, $output );
	}
}
