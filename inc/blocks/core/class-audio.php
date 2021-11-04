<?php
/**
 * WP Component Library includes: Audio Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Audio block as a WPCL component.
 */
class Audio extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		$root  = $this->dom_parser->firstChild();
		$audio = $root->find( 'audio', 0 );

		foreach ( $root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		// Attributes from the audio element for loop/controls/autoplay/etc.
		foreach ( optional( $audio )->getAttributes() ?? [] as $attr => $value ) {
			$this->attrs[ $attr ] = $value ?? true;
		}

		$this->render_component( $this->attrs );
	}
}
