<?php
/**
 * WP Component Library Test Components: Button
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

$is_button = empty( $args['href'] );
$tag       = $is_button ? 'button' : 'a';

?>

<<?php echo esc_html( $tag ); ?>
	class="<?php wpcl_classnames( 'button', sprintf( 'button--%s', $args['variant'] ), $args['class'] ); ?>"
	<?php if ( ! $is_button ) : ?>
		href="<?php echo esc_url( $args['href'] ); ?>"
	<?php endif; ?>
	<?php if ( ! empty( $args['id'] ) ) : ?>
		id="<?php echo esc_url( $args['id'] ); ?>"
	<?php endif; ?>
	<?php if ( $is_button ) : ?>
		type="<?php echo esc_attr( $args['type'] ); ?>"
	<?php endif; ?>
><?php echo esc_html( $args['text'] ); ?></<?php echo esc_html( $tag ); ?>>
