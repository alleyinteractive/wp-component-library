<?php
/**
 * WP Component Library includes: Button Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Button block as a WPCL component.
 */
class Button extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root = $this->dom_parser->first_child();
		$link = $root->first( 'a' );

		foreach ( $root->get_attributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		foreach ( $link?->get_attributes() ?? [] as $attr => $value ) {
			$this->attrs['linkAttributes'][ $attr ] = $value;
		}

		// Add the link to a prop and unset the href in case all attributes
		// are printed to the <a> tag in the component template.
		$this->attrs['link'] = $this->attrs['linkAttributes']['href'] ?? '';
		unset( $this->attrs['linkAttributes']['href'] );

		$this->attrs['content'] = $link?->inner_html() ?? '';

		$this->render_component( $this->attrs );
	}
}
