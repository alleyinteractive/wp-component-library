<?php
/**
 * WP Component Library includes: The interface that defines a block to be used with WPCL.
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks;

interface Block {
	/**
	 * Setup attributes for the block.
	 *
	 * @param array $raw_block The raw block attributes.
	 */
	public function __construct( array $raw_block );

	/**
	 * Echos a WPCL component from a block.
	 *
	 * @return void
	 */
	public function render(): void;
}
