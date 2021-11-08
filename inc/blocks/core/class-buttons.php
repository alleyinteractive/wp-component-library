<?php
/**
 * WP Component Library includes: Buttons Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Buttons block as a WPCL component.
 */
class Buttons extends Anonymous {

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

		$this->attrs['buttons'] = $this->raw['innerBlocks'];

		$this->render_component( $this->attrs );
	}
}
