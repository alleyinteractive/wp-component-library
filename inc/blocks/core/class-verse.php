<?php
/**
 * WP Component Library includes: Verse Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Verse block as a WPCL component.
 */
class Verse extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root = $this->dom_parser->firstChild();

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		// We need to preserve whitespace so the <pre> tag works correctly.
		// Using the DOM parser strips out whitespace when building the tree representation of the element.
		$this->attrs['content'] = wp_strip_all_tags( $this->raw['innerHTML'], false );

		$this->render_component( $this->attrs );
	}
}
