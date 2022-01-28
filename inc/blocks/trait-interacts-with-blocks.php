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
	 * The DOM parser instance.
	 *
	 * @var Dom
	 */
	protected object $dom_parser;

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

		// Escape hatch for components that may want to render the original block.
		$this->attrs['raw'] = $raw_block;

		// Load the block's HTML as an initial root for the DOM parser.
		$this->dom_parser->loadStr( trim( $this->raw['innerHTML'] ) );
	}

	/**
	 * Renders a block. Should call `$this->render_component()` in most scenarios.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_component();
	}

	/**
	 * Attempt to render a WPCL component from the theme. If none exists,
	 * fallback to the WordPress renderer.
	 *
	 * @param array       $attrs The attributes to pass to `wpcl_component( $name, $attrs )`.
	 * @param string|null $component_name The name of the component to attempt to render from the theme.
	 * @return void
	 */
	public function render_component( ?array $attrs = null, ?string $component_name = null ): void {
		$attrs          ??= $this->attrs;
		$component_name ??= 'block-' . str_replace( '/', '-', $this->raw['blockName'] );
		$output           = wpcl_component( $component_name, $attrs, true );

		if ( empty( $output ) ) {
			$this->render_fallback();
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $output; // Escaped in component template file.
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
		// Remove autop during render.
		remove_filter( 'the_content', 'wpautop' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		echo apply_filters( 'the_content', render_block( $this->raw ) );
		// Readd autop after echo.
		add_filter( 'the_content', 'wpautop' );
	}
}
