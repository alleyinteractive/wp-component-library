<?php
/**
 * WP Component Library Tests: Test_Partials class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

/**
 * A class to test the behavior of loading partials.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Partials extends WP_UnitTestCase {
	/**
	 * Tests the behavior of parsing props.
	 */
	public function test_wpcl_classes() {
		// Test combination of default classes with additional classes from a component.
		ob_start();
		wpcl_classes( [ 'a', 'b', 'c d' ] );
		$classes = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'a b c d', $classes );

		// Test default classes only.
		ob_start();
		wpcl_classes( [ 'a', 'b', '' ] );
		$classes = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'a b', $classes );
	}
}
