<?php
/**
 * WP Component Library Components: Button
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

$is_button = empty( $args['href'] );
$html_tag  = $is_button ? 'button' : 'a';

?>

<<?php echo esc_html( $html_tag ); ?>
	<?php wpcl_class( 'button', sprintf( 'button--%s', $args['variant'] ), $args['class'] ); ?>
	<?php if ( ! $is_button ) : ?>
		href="<?php echo esc_url( $args['href'] ); ?>"
	<?php endif; ?>
	<?php wpcl_id( $args['id'] ); ?>
	<?php if ( $is_button ) : ?>
		type="<?php echo esc_attr( $args['type'] ); ?>"
	<?php endif; ?>
><?php echo esc_html( $args['text'] ); ?></<?php echo esc_html( $html_tag ); ?>>
