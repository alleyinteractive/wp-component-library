<?php
/**
 * WP Component Library includes: Paragraph Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a paragraph block as a WPCL component.
 */
class Paragraph extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$el = $this->dom_parser->root->find( 'p', 0 );

		if ( is_null( $el ) ) {
			$this->render_fallback();
		}

		foreach ( $el->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}
		$this->attrs['content'] = $el->innerHtml();

		$this->render_component( $this->attrs );
	}
}
