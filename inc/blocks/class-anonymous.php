<?php

namespace WP_Component_Library\Blocks;

use PHPHtmlParser\Dom;

/**
 * Renders all other blocks that have not been defined by WPCL.
 */
class Anonymous implements Block {
	use Interacts_With_Blocks;

	public function render(): void {
		$dom = new Dom();
		$dom->loadStr( trim( $this->raw['innerHTML'] ) );
		$root_el = $dom->hasChildren() ? $dom->firstChild() : null;

		if ( is_null( $root_el ) || is_null( $this->raw['blockName'] ) ) {
			echo $this->raw['innerHTML'];
			return;
		}

		$attrs = $this->raw['attrs'];
		foreach ( $root_el->getAttributes() as $attr => $value ) {
			$attrs[ $attr ] = $value;
		}

		$attrs['content']  = $root_el->innerHTML();
		$attrs['html_tag'] = $root_el->getTag()->name();
		$name              = str_replace( '/', '-', $this->raw['blockName'] );
		// Try and render a component template.
		$success = wpcl_component( $name, $attrs );

		if ( ! $success ) {
			echo $this->raw['innerHTML'];
		}
	}
}
