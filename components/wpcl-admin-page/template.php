<?php
/**
 * WP Component Library Components: Admin Page
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

use WP_Component_Library\Component;

// Get the featured component, if there is one.
$featured_component = ! empty( $args['component'] )
	? new Component( $args['component'], [], 'preview' )
	: null;

// Get examples, if there are any.
$examples       = $featured_component ? $featured_component->get_examples() : [];
$total_examples = count( $examples );

// Fork for listing all components vs. displaying just one.
if ( ! empty( $featured_component ) ) {
	$props = $featured_component->get_props();
	foreach ( $props as $prop ) {
		$prop_errors = $prop->validate_config();
		foreach ( $prop_errors as $prop_error ) {
			wpcl_component(
				'wpcl-admin-notice',
				[
					// translators: Prop config error text.
					'text' => sprintf( __( 'Prop Config Error: %s', 'wp-component-library' ), $prop_error ),
					'type' => 'error',
				]
			);
		}
	}
	echo '<div style="margin-top: 1em">';
	wpcl_component(
		'wpcl-button',
		[
			'href' => wpcl_admin_url( '', $args['dogfooding'] ),
			'text' => __(
				'Back',
				'wp-component-library'
			),
		]
	);
	echo '</div>';
	wpcl_markdown( $featured_component->get_readme() );
	wpcl_component(
		'wpcl-heading',
		[
			'level' => 2,
			'text'  => __(
				'Examples',
				'wp-component-library'
			),
		]
	);
	for ( $i = 0; $i < $total_examples; $i ++ ) {
		$featured_component->load_example_data( $i );
		wpcl_component(
			'wpcl-heading',
			[
				'level' => 3,
				'text'  => $examples[ $i ]->get_title(),
			]
		);

		// Render the component preview.
		$featured_component->render();

		// Construct the sample code and render it.
		$code = sprintf(
			"wpcl_component(\n\t'%s',\n\t%s\n);",
			$featured_component->get_name(),
			wpcl_phpify( $examples[ $i ]->get_props(), 1 )
		);
		wpcl_component(
			'wpcl-heading',
			[
				'level' => 4,
				'text'  => __( 'Code', 'wp-component-library' ),
			]
		);
		wpcl_component(
			'wpcl-code',
			[
				'code'     => $code,
				'language' => 'php',
			]
		);
	}
	wpcl_component(
		'wpcl-heading',
		[
			'level' => 2,
			'text'  => __(
				'Props',
				'wp-component-library'
			),
		]
	);
	$props_header = [
		__( 'Prop', 'wp-component-library' ),
		__( 'Default', 'wp-component-library' ),
		__( 'Required', 'wp-component-library' ),
		__( 'Type', 'wp-component-library' ),
		__( 'Description', 'wp-component-library' ),
	];
	$props_values = [];
	foreach ( $props as $prop ) {
		$props_values[] = [
			$prop->get_name(),
			$prop->get_required() ? '' : wpcl_phpify( $prop->get_default() ),
			$prop->get_required() ? __( 'Yes', 'wp-component-library' ) : __( 'No', 'wp-component-library' ),
			$prop->get_type(),
			$prop->get_description(),
		];
	}
	wpcl_component(
		'wpcl-table',
		[
			'header' => $props_header,
			'values' => $props_values,
		]
	);
} else {
	wpcl_component(
		'wpcl-heading',
		[
			'level' => 1,
			'text'  => __(
				'Components',
				'wp-component-library'
			),
		]
	);
	wpcl_component(
		'wpcl-list',
		[
			'items' => array_map(
				function ( Component $component ) use ( $args ) {
					return sprintf(
						'<a href="%s">%s</a>',
						esc_url( wpcl_admin_url( $component->get_name(), $args['dogfooding'] ) ),
						$component->get_title()
					);
				},
				$args['components']
			),
		]
	);
}
