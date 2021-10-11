<?php
/**
 * WP Component Library Tests: Test_Classnames class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Classnames;

/**
 * A class to test the behavior of the Classnames class.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Classnames extends WP_UnitTestCase {
	/**
	 * A data provider for the test_get_classlist function. Returns an array of
	 * arrays representing function arguments.
	 *
	 * This list of arguments was copied from the classnames test suite at
	 * https://github.com/JedWatson/classnames/blob/master/tests to ensure
	 * compatibility with the classnames npm package. However, some of the lists
	 * were modified from the original to use PHP's falsy values instead of
	 * JavaScript's (e.g., removing "undefined" and adding 0.0, -0.0, '', '0',
	 * and []).
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_get_classlist(): array {
		return [
			'It keeps object keys with truthy values' => [
				'a f',
				[
					[
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
			'It joins arrays of class names and ignores falsy values' => [
				'a 1 b',
				[ 'a', 0, null, 0.0, - 0.0, '', '0', [], true, 1, 'b' ],
			],
			'It supports heterogenous arguments'      => [
				'a b',
				[ [ 'a' => true ], 'b', 0 ],
			],
			'It should be trimmed'                    => [
				'b',
				[ '', 'b', [], '' ],
			],
			'It returns an empty string for an empty configuration' => [
				'',
				[ [] ],
			],
			'It supports an array of class names'     => [
				'a b',
				[ [ 'a', 'b' ] ],
			],
			'It joins array arguments with string arguments, example 1' => [
				'a b c',
				[ [ 'a', 'b' ], 'c' ],
			],
			'It joins array arguments with string arguments, example 2' => [
				'c a b',
				[ 'c', [ 'a', 'b' ] ],
			],
			'It handles multiple array arguments'     => [
				'a b c d',
				[ [ 'a', 'b' ], [ 'c', 'd' ] ],
			],
			'It handles arrays that include falsy and true values' => [
				'a b',
				[ [ 'a', 0, null, 0.0, - 0.0, '', '0', [], false, true, 'b' ] ],
			],
			'It handles arrays that include arrays'   => [
				'a b c',
				[ [ 'a', [ 'b', 'c' ] ] ],
			],
			'It handles arrays that include objects'  => [
				'a b',
				[
					[
						'a',
						[
							'b' => true,
							'c' => false,
						],
					],
				],
			],
			'It handles deep array recursion'         => [
				'a b c d',
				[ [ 'a', [ 'b', [ 'c', [ 'd' => true ] ] ] ] ],
			],
			'It handles arrays that are empty'        => [
				'a',
				[ 'a', [] ],
			],
			'It handles nested arrays that have empty nested arrays' => [
				'a',
				[ 'a', [ [] ] ],
			],
			'It should dedupe'                        => [
				'foo bar',
				[ 'foo', 'bar', 'foo', 'bar', [ 'foo' => true ] ],
			],
			'It should make sure subsequent objects can remove/add classes' => [
				'foo bar',
				[
					'foo',
					[ 'foo' => false ],
					[
						'foo' => true,
						'bar' => true,
					],
				],
			],
			'It should make sure an array with a falsy value wipes out previous classes, example 1' => [
				'1 b',
				[ 'foo foo', 0, null, '', true, 1, 'b', [ 'foo' => false ] ],
			],
			'It should make sure an array with a falsy value wipes out previous classes, example 2' => [
				'foobar bar',
				[ 'foo', 'foobar', 'bar', [ 'foo' => false ] ],
			],
			'It should make sure an array with a falsy value wipes out previous classes, example 3' => [
				'foo-bar bar',
				[ 'foo', 'foo-bar', 'bar', [ 'foo' => false ] ],
			],
			'It should make sure an array with a falsy value wipes out previous classes, example 4' => [
				'-moz-foo-bar bar',
				[ 'foo', '-moz-foo-bar', 'bar', [ 'foo' => false ] ],
			],
		];
	}

	/**
	 * Tests the behavior of the get_classlist function.
	 *
	 * @dataProvider data_get_classlist
	 *
	 * @param string $expected The expected space-separated list of classes.
	 * @param array  $args     Arguments to pass to the Classnames constructor.
	 */
	public function test_get_classlist( string $expected, array $args ) {
		$classnames = new Classnames( ...$args );
		$this->assertEquals( $expected, $classnames->get_classlist() );
	}
}
