<?php // phpcs:ignore WordPress.Files.Filename.InvalidClassFileName
/**
 * WP Component Library includes: Heading Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Heading block as a WPCL component.
 */
class Heading extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->dom_parser->loadStr( trim( $this->raw['innerHTML'] ) );
		$root = $this->dom_parser->firstChild();

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['level'] = absint( str_replace( 'h', '', $root->tag->name() ) );
		$this->attrs['text']  = $root->innerHtml();

		$this->render_component( $this->attrs );
	}
}
