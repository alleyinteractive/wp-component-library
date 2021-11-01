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
		$this->dom_parser->loadStr( $this->raw['innerHTML'] );
		$el = $this->dom_parser->find( 'p', 0 );

		if ( is_null( $el ) ) {
			$this->render_fallback();
		}

		$attrs = $this->raw['attrs'];
		foreach ( $el->getAttributes() as $attr => $value ) {
			$attrs[ $attr ] = $value;
		}
		$attrs['content'] = $el->innerHtml();

		$this->render_component( $attrs );
	}
}
