<?php
/**
 * WP Component Library Tests: Test_Example class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Example;

/**
 * A class to test the behavior of the Example class.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Example extends WP_UnitTestCase {
	/**
	 * Tests the behavior of the getters.
	 */
	public function test_getters() {
		$example = new Example( 'Test Title', [ 'a' => 'b' ] );
		$this->assertEquals( 'Test Title', $example->get_title() );
		$this->assertEquals( [ 'a' => 'b' ], $example->get_props() );
	}
}
