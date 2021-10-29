<?php

namespace WP_Component_Library\Blocks\Core;

use DOMDocument;
use PHPHtmlParser\Dom;
use WP_Component_Library\Block;
use WP_Component_Library\Blocks\Anonymous;

class Paragraph extends Anonymous {

	/**
	 * @inheritDoc
	 */
	public function render(): void {
		$dom = new Dom();
		$dom->loadStr( $this->raw['innerHTML'] );
		$el = $dom->find( 'p', 0 );

		if ( is_null( $el ) ) {
			echo wp_kses_post( $this->raw['innerHTML'] );
		}

		$attrs = $this->raw['attrs'];
		foreach ( $el->getAttributes() as $attr => $value ) {
			$attrs[ $attr ] = $value;
		}
		$attrs['content'] = $el->innerHtml();

		$success = wpcl_component( 'core-paragraph', $attrs );

		if ( ! $success ) {
			echo wp_kses_post( $this->raw['innerHTML'] );
		}
	}
}
