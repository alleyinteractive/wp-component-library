<?php
/**
 * WP Component Library includes: Image Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Image block as a WPCL component.
 */
class Image extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root = $this->dom_parser->firstChild()->find( 'img', 0 );

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['image_id'] = $this->attrs['id'];
		$this->attrs['size']     = $this->attrs['sizeSlug'] ?? '';
		$this->attrs['link']     = 'none' !== $this->attrs['linkDestination'] ? $this->get_link() : '';
		$this->attrs['caption']  = $this->get_caption();

		$this->render_component( $this->attrs );
	}

	/**
	 * Extract the anchor href from an image block HTML.
	 *
	 * @param string $fallback The fallback url.
	 * @return string
	 */
	private function get_link( string $fallback = '' ): string {
		$maybe_url = optional( $this->dom_parser->find( 'a', 0 ) )->getAttribute( 'href' );
		return $maybe_url ?? $fallback;
	}

	/**
	 * Get the caption if it exists.
	 *
	 * @return string
	 */
	private function get_caption(): string {
		$caption = optional( $this->dom_parser->find( 'figcaption', 0 ) )->innerHtml() ?? '';
		return $caption;
	}
}
