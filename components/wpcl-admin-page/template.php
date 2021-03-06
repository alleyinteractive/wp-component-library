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

// Print any admin notices from erroring components.
do_action( 'wpcl_admin_notices' );

// phpcs:disable WordPressVIPMinimum.UserExperience.AdminBarRemoval.HidingDetected
if ( ! empty( $_GET['inline'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
	<style>
		#wpadminbar, #adminmenumain, #wpfooter {
			/* phpcs:ignore WordPressVIPMinimum.UserExperience.AdminBarRemoval.HidingDetected */
			display: none !important;
		}
		html.wp-toolbar, #wpcontent {
			padding: 0 !important;
			margin: 0 !important;
		}
	</style>
<?php else : ?>
	<script type="text/javascript">
		jQuery('body').on('click', 'a[data-wpcl-preview]', function(e){
			e.preventDefault();
			jQuery('#wpcl-component-preview-iframe').attr('src', e.target.dataset.wpclPreview);
		});
	</script>
	<?php
endif;
// phpcs:enable WordPressVIPMinimum.UserExperience.AdminBarRemoval.HidingDetected

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
	if ( empty( $_GET['inline'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
	}
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
		$success = $featured_component->render();

		if ( ! $success ) {
			wpcl_component(
				'wpcl-admin-notice',
				[
					'type' => 'error',
					'text' => __( 'The component could not be rendered.', 'wp-component-library' ),
				]
			);
		}

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
	// Sort components array.
	sort( $args['components'] );
	$grouped_components = Component::group_by_subdirectory( $args['components'] );
	wpcl_component(
		'wpcl-layouts/sidebar-content',
		[
			'sidebar' => wpcl_component(
				'wpcl-list',
				[
					'items' => array_map(
						function ( $key ) use ( $args, $grouped_components ) {
							ob_start();
							// Subgroup Title.
							wpcl_component(
								'wpcl-heading',
								[
									'level' => 2,
									'text'  => esc_html( ucfirst( $key ) ),
								]
							);
							// Components.
							wpcl_component(
								'wpcl-list',
								[
									'items' => array_map(
										function ( Component $component ) use ( $args ) {
											return sprintf(
												'<a href="%s" data-wpcl-preview="%s">%s</a>',
												esc_url( wpcl_admin_url( $component->get_name(), $args['dogfooding'] ) ),
												esc_url( add_query_arg( [ 'inline' => 'true' ], wpcl_admin_url( $component->get_name(), $args['dogfooding'] ) ) ),
												$component->get_title()
											);
										},
										$grouped_components[ $key ]
									),
								],
							);
							return ob_get_clean();
						},
						array_keys( $grouped_components )
					),
				],
				true
			),
			'content' => wpcl_component( 'wpcl-iframe-preview', [ 'id' => 'wpcl-component-preview-iframe' ], true ),
		]
	);

}
