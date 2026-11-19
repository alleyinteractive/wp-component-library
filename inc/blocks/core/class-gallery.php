<?php
/**
 * WP Component Library includes: Gallery Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Gallery block as a WPCL component.
 */
class Gallery extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root = $this->dom_parser->first_child();

		foreach ( $root->get_attributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['items']   = $this->attrs['ids'];
		$this->attrs['caption'] = $root->first( '.blocks-gallery-caption' )?->inner_html() ?? '';

		$this->render_component( $this->attrs );
	}
}
