<?php
/**
 * WP Component Library Components: List
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<<?php echo 'ordered' === $args['type'] ? 'ol' : 'ul'; ?>
	<?php wpcl_class( $args['class'] ); ?>
	<?php wpcl_id( $args['id'] ); ?>
>
	<?php foreach ( $args['items'] as $item ) : ?>
		<li>
			<?php echo wp_kses_post( $item ); ?>
		</li>
	<?php endforeach; ?>
</<?php echo 'ordered' === $args['type'] ? 'ol' : 'ul'; ?>>
