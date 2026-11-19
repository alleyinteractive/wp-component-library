<?php
/**
 * WP Component Library includes: Video Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Video block as a WPCL component.
 */
class Video extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {
		// Videos are wrapped in a <figure> element.
		$root  = $this->dom_parser->first_child();
		$video = $root->first( 'video' );

		foreach ( $root->get_attributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		// Attributes from the Video element for loop/controls/autoplay/etc.
		foreach ( $video?->get_attributes() ?? [] as $attr => $value ) {
			$this->attrs[ $attr ] = $value ?? true;
		}

		$this->render_component( $this->attrs );
	}
}
