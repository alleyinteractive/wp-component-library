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
	?>
	<div style="margin-top: 1em">
		<?php
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
		?>
	</div>
	<?php wpcl_markdown( $featured_component->get_readme() ); ?>
	<?php
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
	?>
	<?php for ( $i = 0; $i < $total_examples; $i ++ ) : ?>
		<?php $featured_component->load_example_data( $i ); ?>
		<?php
		wpcl_component(
			'wpcl-heading',
			[
				'level' => 3,
				'text'  => $examples[ $i ]->get_title(),
			]
		);
		?>
		<?php wpcl_component( 'wpcl-code', [ 'code' => 'text-color: #00f;' ] ); ?>
		<?php $featured_component->render(); ?>
	<?php endfor; ?>
	<h2>TODO: PROPS</h2>
	<?php
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
