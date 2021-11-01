<?php
/**
 * WP Component Library includes: Embed Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Embed block as a WPCL component.
 */
class Embed extends Anonymous {

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

		$this->attrs['provider'] = $this->attrs['providerNameSlug'] ?? '';
		$this->attrs['raw']      = $this->raw['innerHTML'];

		$this->render_component( $this->attrs );
	}
}
