<?php
/**
 * WP Component Library Components: Code
 *
 * @package WP_Component_Library
 * @global array $args Props passed to this component.
 */

use Highlight\Highlighter;

$highlighter = new Highlighter();

try {
	$highlighted    = $highlighter->highlight( $args['language'], $args['code'] );
	$args['class'] .= ' hljs ' . $highlighted->language;
	$code           = $highlighted->value;
} catch ( DomainException $e ) {
	$code = esc_html( $args['code'] );
}

?>

<pre
	<?php
		wpcl_attributes(
			[
				'style' => 'width: 50%',
			],
			$args
		);
		?>
><code><?php echo wp_kses_post( $code ); ?></code></pre>
