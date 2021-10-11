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
	 * A data provider for the test_classnames function. Returns an array of
	 * arrays representing function arguments.
	 *
	 * This list of arguments was copied from the classnames test suite at
	 * https://github.com/JedWatson/classnames/blob/master/tests to ensure
	 * compatibility with the classnames npm package.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_classnames(): array {
		return [
			// It keeps object keys with truthy values.
			[
				'a f',
				[
					[
						// This list was modified from the original to use PHP's falsy values.
						'a' => true,
						'b' => false,
						'c' => 0,
						'd' => null,
						'e' => 0.0,
						'f' => 1,
						'g' => - 0.0,
						'h' => '',
						'i' => '0',
						'j' => [],
					],
				],
			],

			// It joins arrays of class names and ignores falsy values.
			[
				'a 1 b',
				[ 'a', 0, null, 0.0, - 0.0, '', '0', [], true, 1, 'b' ],
			],

			// It supports heterogenous arguments.
			[
				'a b',
				[ [ 'a' => true ], 'b', 0 ],
			],

			// It should be trimmed.
			[
				'b',
				[ '', 'b', [], '' ],
			],

			// It returns an empty string for an empty configuration.
			[
				'',
				[ [] ],
			],

			// It supports an array of class names.
			[
				'a b',
				[ [ 'a', 'b' ] ],
			],

			// It joins array arguments with string arguments.
			[
				'a b c',
				[ [ 'a', 'b' ], 'c' ],
			],
			[
				'c a b',
				[ 'c', [ 'a', 'b' ] ],
			],

			// It handles multiple array arguments.
			[
				'a b c d',
				[ [ 'a', 'b' ], [ 'c', 'd' ] ],
			],

			// It handles arrays that include falsy and true values.
			[
				'a b',
				[ [ 'a', 0, null, 0.0, - 0.0, '', '0', [], false, true, 'b' ] ],
			],

			// It handles arrays that include arrays.
			[
				'a b c',
				[ [ 'a', [ 'b', 'c' ] ] ],
			],

			// It handles arrays that include objects.
			[
				'a b',
				[ [ 'a', [ 'b' => true, 'c' => false ] ] ],
			],

			// It handles deep array recursion.
			[
				'a b c d',
				[ [ 'a', [ 'b', [ 'c', [ 'd' => true ] ] ] ] ],
			],

			// It handles arrays that are empty.
			[
				'a',
				[ 'a', [] ],
			],

			// It handles nested arrays that have empty nested arrays.
			[
				'a',
				[ 'a', [ [] ] ],
			],

			// It should dedupe.
			[
				'foo bar',
				[ 'foo', 'bar', 'foo', 'bar', [ 'foo' => true ] ],
			],

			// It should make sure subsequent objects can remove/add classes.
			[
				'foo bar',
				[ 'foo', [ 'foo' => false ], [ 'foo' => true, 'bar' => true ] ],
			],

			// It should make sure an array with a falsy value wipes out previous classes.
			[
				'1 b',
				[ 'foo foo', 0, null, '', true, 1, 'b', [ 'foo' => false ] ],
			],
			[
				'foobar bar',
				[ 'foo', 'foobar', 'bar', [ 'foo' => false ] ],
			],
			[
				'foo-bar bar',
				[ 'foo', 'foo-bar', 'bar', [ 'foo' => false ] ],
			],
			[
				'-moz-foo-bar bar',
				[ 'foo', '-moz-foo-bar', 'bar', [ 'foo' => false ] ],
			],
		];
	}

	/**
	 * Tests the behavior of the classnames function.
	 *
	 * @dataProvider data_classnames
	 *
	 * @param string $expected The expected result of the function call with the provided arguments.
	 * @param array  $args     Arguments to pass to the wpcl_classnames function.
	 */
	public function test_classnames( string $expected, array $args ) {
		ob_start();
		wpcl_classnames( ...$args );
		$classes = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( $expected, $classes );
	}
}
