<?php
/**
 * WP Component Library Components: Admin Notice
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<div
	<?php wpcl_class( 'notice', sprintf( 'notice-%s', $args['type'] ), [ 'is-dismissible' => $args['dismissible'] ], $args['class'] ); ?>
	<?php wpcl_id( $args['id'] ); ?>
><p><?php echo wp_kses_post( $args['text'] ); ?></p></div>
