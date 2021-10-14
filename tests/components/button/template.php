<?php
/**
 * WP Component Library Test Components: Button
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

$is_button = empty( $args['href'] );
$html_tag  = $is_button ? 'button' : 'a';

?>

<<?php echo esc_html( $html_tag ); ?>
	<?php
		wpcl_attributes(
			[
				'class' => [
					'button',
					sprintf( 'button--%s', $args['variant'] ),
				],
				'href'  => ! $is_button ? $args['href'] : '',
				'type'  => $is_button ? $args['type'] : '',
			],
			$args
		);
		?>
><?php echo esc_html( $args['text'] ); ?></<?php echo esc_html( $html_tag ); ?>>
