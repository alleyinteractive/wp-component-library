<?php

namespace WP_Component_Library\Blocks;

trait Interacts_With_Blocks {
	/**
	 * The raw block data.
	 *
	 * @var array
	 */
	protected $raw;

	/**
	 * Undocumented function
	 *
	 * @param array $raw_block Raw block data.
	 * @return void
	 */
	public function __construct( array $raw_block ) {
		$this->raw = $raw_block;
	}

	/**
	 * Renders a block by echo'ing it's normal output.
	 *
	 * @return void
	 */
	public function render(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->raw['innerHTML'];
	}
}
