<?php
/**
 * WP Component Library Components: Admin Notice
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<div
	<?php
		wpcl_attributes(
			[
				'class' => [
					'notice',
					sprintf( 'notice-%s', $args['type'] ),
					[
						'is-dismissible' => $args['dismissible'],
					],
				],
			],
			$args
		);
		?>
><p><?php echo wp_kses_post( $args['text'] ); ?></p></div>
