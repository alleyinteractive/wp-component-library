<?php
/**
 * WP Component Library includes: Separator Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Separator block as a WPCL component.
 */
class Separator extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root = $this->dom_parser->find( 'hr', 0 );

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->render_component( $this->attrs );
	}
}
