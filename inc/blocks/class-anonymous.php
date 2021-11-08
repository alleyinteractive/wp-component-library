<?php
/**
 * WP Component Library includes: Anonymous block class.
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks;

/**
 * Renders all other blocks that have not been defined by WPCL.
 */
class Anonymous implements Block {
	use Interacts_With_Blocks;
}
