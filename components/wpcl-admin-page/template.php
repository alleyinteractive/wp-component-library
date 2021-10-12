<?php
/**
 * WP Component Library Components: Admin Page
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

wpcl_component(
	'wpcl-heading',
	[
		'level' => 1,
		'text'  => ! empty( $args['title'] )
			? $args['title']
			: __( 'Components', 'wp-component-library' ),
	]
);

