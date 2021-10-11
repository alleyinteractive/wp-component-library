<?php
/**
 * WP Component Library Tests: Test_Prop class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Prop;

/**
 * A class to test the behavior of the Prop class.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class Test_Prop extends WP_UnitTestCase {
	/**
	 * A helper function for creating a test prop with a given type.
	 *
	 * @param string $type The type of prop to create.
	 *
	 * @return Prop The newly created prop.
	 */
	private function create_test_prop( string $type ): Prop {
		$properties = [
			'description' => 'Test description',
			'type'        => $type,
		];
		if ( 'enum' === $type ) {
			$properties['allowed_values'] = ['foo', 'bar'];
		}

		return new Prop( 'test', $properties );
	}

	/**
	 * A data provider for the test_set_default function. Returns an array of
	 * arrays representing function arguments.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_set_default(): array {
		return [
			[ 'array', [] ],
			[ 'bool', false ],
			[ 'enum', 'foo' ],
			[ 'number', 0 ],
			[ 'object', new stdClass() ],
			[ 'string', '' ],
		];
	}

	/**
	 * A data provider for the test_set_value function. Returns an array of
	 * arrays representing function arguments.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_set_value(): array {
		return [
			[ 'array', [], [ 'foo', 'bar' ] ],
			[ 'bool', false, true ],
			[ 'enum', 'foo', 'bar' ],
			[ 'number', 0, 2 ],
			[ 'object', new stdClass(), (object) [ 'a' => 'b' ] ],
			[ 'string', '', 'test string' ],
		];
	}

	/**
	 * A data provider for the test_validate_config function. Returns an array
	 * of arrays representing function arguments.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_validate_config(): array {
		return [
			[
				'',
				[],
				[
					'All props must have a name.',
					'All props must have a description.',
				],
			],
			[
				'test',
				[
					'description' => 'Test description',
					'type'        => '',
				],
				[
					'All props must have a type.',
				],
			],
			[
				'test',
				[
					'description' => 'Test description',
					'type'        => 'foo',
				],
				[
					'The prop type must be one of the allowed types.',
				],
			],
			[
				'test',
				[
					'default'     => '',
					'description' => 'Test description',
					'type'        => 'array',
				],
				[
					'The default value must be of the same type as the prop.',
				],
			],
			[
				'test',
				[
					'default'     => '',
					'description' => 'Test description',
					'type'        => 'bool',
				],
				[
					'The default value must be of the same type as the prop.',
				],
			],
			[
				'test',
				[
					'default'     => '',
					'description' => 'Test description',
					'type'        => 'number',
				],
				[
					'The default value must be of the same type as the prop.',
				],
			],
			[
				'test',
				[
					'default'     => '',
					'description' => 'Test description',
					'type'        => 'object',
				],
				[
					'The default value must be of the same type as the prop.',
				],
			],
			[
				'test',
				[
					'default'     => 0,
					'description' => 'Test description',
					'type'        => 'string',
				],
				[
					'The default value must be of the same type as the prop.',
				],
			],
			[
				'test',
				[
					'allowed_values' => ['a', 'b', 'c'],
					'default'        => 'd',
					'description'    => 'Test description',
					'type'           => 'enum',
				],
				[
					'For a prop type of enum, the default value must be one of the allowed values.',
				],
			],
			[
				'test',
				[
					'default'     => [ 'test' ],
					'description' => 'Test description',
					'type'        => 'array',
				],
				[],
			],
			[
				'test',
				[
					'default'     => true,
					'description' => 'Test description',
					'type'        => 'bool',
				],
				[],
			],
			[
				'test',
				[
					'allowed_values' => ['a', 'b', 'c'],
					'default'        => 'a',
					'description'    => 'Test description',
					'type'           => 'enum',
				],
				[],
			],
			[
				'test',
				[
					'default'     => 2,
					'description' => 'Test description',
					'type'        => 'number',
				],
				[],
			],
			[
				'test',
				[
					'default'     => (object) [ 'a' => 'b' ],
					'description' => 'Test description',
					'type'        => 'object',
				],
				[],
			],
			[
				'test',
				[
					'default'     => 'default',
					'description' => 'Test description',
					'type'        => 'string',
				],
				[],
			],
		];
	}

	/**
	 * Tests the behavior of the getter functions.
	 */
	public function test_getters() {
		$prop = new Prop(
			'test',
			[
				'allowed_values' => [ 'foo', 'bar' ],
				'default'        => 'bar',
				'description'    => 'Test description.',
				'required'       => true,
				'type'           => 'enum',
			]
		);
		$prop->set_value( 'foo' );
		$this->assertEquals( [ 'foo', 'bar' ], $prop->get_allowed_values() );
		$this->assertEquals( 'bar', $prop->get_default() );
		$this->assertEquals( 'Test description.', $prop->get_description() );
		$this->assertEquals( 'test', $prop->get_name() );
		$this->assertEquals( true, $prop->get_required() );
		$this->assertEquals( 'enum', $prop->get_type() );
		$this->assertEquals( 'foo', $prop->get_value() );
	}

	/**
	 * Tests the behavior of dynamic default values based on type.
	 *
	 * @dataProvider data_set_default
	 *
	 * @param string $type     The type to test.
	 * @param mixed  $expected The expected default value.
	 */
	public function test_set_default( string $type, $expected ) {
		$prop = $this->create_test_prop( $type );
		$this->assertEquals( $expected, $prop->get_default() );
	}

	/**
	 * Tests the behavior of the set value function, ensuring that it correctly
	 * sets the value and replaces the default value.
	 *
	 * @dataProvider data_set_value
	 *
	 * @param string $type    The data type to use when registering the prop.
	 * @param mixed  $default The default value to check against before setting the value.
	 * @param mixed  $value   The value to set and check against.
	 */
	public function test_set_value( string $type, $default, $value ) {
		$prop = $this->create_test_prop( $type );
		$this->assertEquals( $default, $prop->get_value() );
		$prop->set_value( $value );
		$this->assertEquals( $value, $prop->get_value() );
	}

	/**
	 * Tests the behavior of the validate_config function.
	 *
	 * @dataProvider data_validate_config
	 *
	 * @param string $name       The prop name.
	 * @param array  $properties An array of key/value pairs to apply.
	 * @param array  $errors     Expected errors when validating the config.
	 */
	public function test_validate_config( string $name, array $properties, array $errors ) {
		$prop = new Prop( $name, $properties );
		$this->assertEquals( $errors, $prop->validate_config() );
	}
}
