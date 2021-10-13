<?php
/**
 * WP Component Library Components: Code
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<pre
	<?php wpcl_class( $args['class'] ); ?>
	<?php wpcl_id( $args['id'] ); ?>
><code><?php echo esc_html( $args['code'] ); ?></code></pre>
