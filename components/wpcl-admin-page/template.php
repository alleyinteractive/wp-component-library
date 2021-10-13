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

// Fork for listing all components vs. displaying just one.
if ( ! empty( $featured_component ) ) {
	?>
	<div style="margin-top: 1em">
		<?php 
		wpcl_component(
			'wpcl-button',
			[
				'href' => admin_url( 'admin.php?page=wpcl-components' ),
				'text' => __(
					'Back',
					'wp-component-library' 
				),
			] 
		); 
		?>
	</div>
	<?php wpcl_markdown( $featured_component->get_readme() ); ?>
	<h2>TODO: EXAMPLES</h2>
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
				function ( Component $component ) {
					return sprintf(
						'<a href="%s">%s</a>',
						esc_url( admin_url( sprintf( 'admin.php?page=wpcl-components&component=%s', $component->get_name() ) ) ),
						$component->get_title()
					);
				},
				$args['components']
			),
		]
	);
}
