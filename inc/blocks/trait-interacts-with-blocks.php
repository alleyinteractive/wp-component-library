<?php
/**
 * WP Component Library includes: Interacts_With_Blocks trait.
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks;

use PHPHtmlParser\Dom;
trait Interacts_With_Blocks {
	/**
	 * The raw block data.
	 *
	 * @var array
	 */
	protected array $raw;

	/**
	 * The Block/component attributes.
	 *
	 * @var array
	 */
	protected array $attrs;

	/**
	 * Undocumented function
	 *
	 * @param array $raw_block Raw block data.
	 * @return void
	 */
	public function __construct( array $raw_block ) {
		$this->raw        = $raw_block;
		$this->attrs      = $raw_block['attrs'];
		$this->dom_parser = new Dom();

		$this->dom_parser->loadStr( trim( $this->raw['innerHTML'] ) );
	}

	/**
	 * Renders a block. Should call `$this->render_component()` in most scenarios.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_component( [] );
	}

	/**
	 * Attempt to render a WPCL component from the theme. If none exists,
	 * fallback to the WordPress renderer.
	 *
	 * @param array       $attrs The attributes to pass to `wpcl_component( $name, $attrs )`.
	 * @param string|null $component_name The name of the component to attempt to render from the theme.
	 * @return void
	 */
	public function render_component( array $attrs, ?string $component_name = null ): void {
		$component_name ??= 'block-' . str_replace( '/', '-', $this->raw['blockName'] );
		if ( ! wpcl_component( $component_name, $attrs ) ) {
			$this->render_fallback();
		}
	}

	/**
	 * Render the block using native application code.
	 *
	 * @return void
	 */
	public function render_fallback(): void {
		// We need to apply the_content filter to any default
		// content for things like embeds to work properly.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		echo apply_filters( 'the_content', render_block( $this->raw ) );
	}
}
