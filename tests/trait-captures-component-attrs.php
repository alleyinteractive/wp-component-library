<?php
/**
 * WP Component Library Tests: WPCL_Captures_Component_Attrs trait
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

/**
 * Mixed into a block class under test, records the attributes the block would
 * pass to its component instead of rendering anything.
 */
trait WPCL_Captures_Component_Attrs {
	/**
	 * The attributes passed to render_component().
	 *
	 * @var array
	 */
	public array $captured = [];

	/**
	 * Record the component attributes.
	 *
	 * @param array|null  $attrs          The attributes.
	 * @param string|null $component_name The component name, unused.
	 */
	public function render_component( ?array $attrs = null, ?string $component_name = null ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$this->captured = $attrs ?? $this->attrs;
	}
}
