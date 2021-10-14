<?php
/**
 * WP Component Library Components: Table
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

?>

<table
	<?php
		wpcl_attributes(
			[
				'style' => 'background: #000',
			],
			$args
		);
		?>
>
	<?php if ( ! empty( $args['header'] ) ) : ?>
		<thead>
			<tr>
				<?php foreach ( $args['header'] as $cell ) : ?>
					<th style="background: #fff; padding: 0.5em"><?php echo wp_kses_post( $cell ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
	<?php endif; ?>
	<tbody>
		<?php foreach ( $args['values'] as $row ) : ?>
			<tr>
				<?php foreach ( $row as $cell ) : ?>
					<td style="background: #fff; padding: 0.5em"><?php echo wp_kses_post( $cell ); ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
