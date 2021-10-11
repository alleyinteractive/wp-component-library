<?php
/**
 * WP Component Library Tests: Test_Component class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Component;
use WP_Component_Library\Example;
use WP_Component_Library\Prop;

/**
 * A class to test the behavior of the Component class.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Component extends WP_UnitTestCase {
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
		$component = new Component( 'button' );
		$examples  = $component->get_examples();
		$props     = $component->get_props();
		$this->assertEquals( 'button', $component->get_name() );
		$this->assertEquals( 'Button', $component->get_title() );
		$this->assertEquals( 'A link that is styled to look like a button.', $examples[0]->get_title() );
		$this->assertEquals(
			[
				'href'    => 'https://www.example.org/newsletter',
				'text'    => 'Sign Up for Our Newsletter',
				'variant' => 'primary',
			],
			$examples[0]->get_props()
		);
		$this->assertEquals( 'A button with a custom class that will be targeted by JavaScript.', $examples[1]->get_title() );
		$this->assertEquals(
			[
				'class'   => 'newsletter-signup',
				'text'    => 'Sign Up for Our Newsletter',
				'variant' => 'secondary',
			],
			$examples[1]->get_props()
		);
		$this->assertEquals( 'href', $props[0]->get_name() );
		$this->assertEquals( '', $props[0]->get_default() );
		$this->assertEquals( 'If provided, renders `a` instead of `button`.', $props[0]->get_description() );
		$this->assertEquals( false, $props[0]->get_required() );
		$this->assertEquals( 'string', $props[0]->get_type() );
		$this->assertEquals( 'text', $props[1]->get_name() );
		$this->assertEquals( '', $props[1]->get_default() );
		$this->assertEquals( 'Displays the given text inside the button.', $props[1]->get_description() );
		$this->assertEquals( true, $props[1]->get_required() );
		$this->assertEquals( 'string', $props[1]->get_type() );
		$this->assertEquals( 'type', $props[2]->get_name() );
		$this->assertEquals( ['button', 'submit', 'reset'], $props[2]->get_allowed_values() );
		$this->assertEquals( 'button', $props[2]->get_default() );
		$this->assertEquals( 'If rendering a button element, the type to use.', $props[2]->get_description() );
		$this->assertEquals( false, $props[2]->get_required() );
		$this->assertEquals( 'enum', $props[2]->get_type() );
		$this->assertEquals( 'variant', $props[3]->get_name() );
		$this->assertEquals( ['primary', 'secondary'], $props[3]->get_allowed_values() );
		$this->assertEquals( 'primary', $props[3]->get_default() );
		$this->assertEquals( 'Specifies the button\'s style.', $props[3]->get_description() );
		$this->assertEquals( false, $props[3]->get_required() );
		$this->assertEquals( 'enum', $props[3]->get_type() );
	}
}
