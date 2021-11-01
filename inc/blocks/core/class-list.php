<?php // phpcs:ignore WordPress.Files.Filename.InvalidClassFileName
/**
 * WP Component Library includes: List Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use PHPHtmlParser\Dom\Node\HtmlNode;
use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a List block as a WPCL component.
 * Needs a special class name because "list" is a reserved word in PHP.
 */
class ListBlock extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->dom_parser->loadStr( trim( $this->raw['innerHTML'] ) );
		$root = $this->dom_parser->firstChild();

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['type']  = $root->tag->name() === 'ol' ? 'ordered' : 'unordered';
		$this->attrs['items'] = $this->get_children( $root );

		$this->render_component( $this->attrs );
	}

	/**
	 * Get the children of an HtmlNode, with/out the surrounding tag.
	 *
	 * @param HtmlNode $el The HTML node to retrieve children from.
	 * @param bool     $include_tag Include the outer tag of the input element's children.
	 * @return array
	 */
	private function get_children( HtmlNode $el, $include_tag = false ): array {
		$children = [];

		foreach ( $el->getChildren() as $child ) {
			$children[] = $include_tag ? $child->outerHtml() : $child->innerHtml();
		}

		return $children;
	}
}
