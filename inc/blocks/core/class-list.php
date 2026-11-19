<?php // phpcs:ignore WordPress.Files.Filename.InvalidClassFileName
/**
 * WP Component Library includes: List Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Html_Node;
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
		$root = $this->dom_parser->first_child();

		foreach ( $root->get_attributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['type']  = $root->tag_name() === 'ol' ? 'ordered' : 'unordered';
		$this->attrs['items'] = $this->get_children( $root );

		$this->render_component( $this->attrs );
	}

	/**
	 * Get the children of an Html_Node, with/out the surrounding tag.
	 *
	 * @param Html_Node $el The HTML node to retrieve children from.
	 * @param bool      $include_tag Include the outer tag of the input element's children.
	 * @return array
	 */
	private function get_children( Html_Node $el, $include_tag = false ): array {
		$children = [];

		foreach ( $el->get_children() as $child ) {
			$children[] = $include_tag ? $child->outer_html() : $child->inner_html();
		}

		return $children;
	}
}
