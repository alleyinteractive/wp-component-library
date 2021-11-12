<?php
/**
 * WP Component Library Components: iFrame Preview
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

?>
<iframe
	<?php wpcl_attributes( [ 'src' => $args['src'] ], $args ); ?>
	style="
		width: <?php echo esc_attr( $args['width'] ); ?>;
		height: <?php echo esc_attr($args['height']); ?>;
		background: rgba(0,0,0,0.25);
	"
></iframe>
