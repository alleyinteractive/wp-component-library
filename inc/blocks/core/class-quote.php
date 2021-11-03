<?php
/**
 * WP Component Library includes: Quote Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Quote block as a WPCL component.
 */
class Quote extends Anonymous {

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

		$this->attrs['citation'] = $root->find( 'cite', 0 )->innerHtml();
		$this->attrs['content']  = $root->find( 'p', 0 )->innerHtml();

		$this->render_component( $this->attrs );
	}
}
