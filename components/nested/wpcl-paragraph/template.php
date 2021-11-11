<?php
/**
 * WP Component Library Components: Nested Paragaraph
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

?>
<p><?php echo wp_kses( $args['text'], 'data' ); ?></p>
