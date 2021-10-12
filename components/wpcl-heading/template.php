<?php
/**
 * WP Component Library Components: Heading
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<h<?php echo absint( $args['level'] ); ?>
	<?php wpcl_classnames( $args['class'] ); ?>
	<?php wpcl_id( $args['id'] ); ?>
><?php echo wp_kses_post( $args['text'] ); ?></h<?php echo absint( $args['level'] ); ?>>
