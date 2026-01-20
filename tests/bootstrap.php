<?php
/**
 * WP Component Library Tests: Bootstrap File
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

\Mantle\Testing\manager()
	->loaded( fn () => require __DIR__ . '/../wp-component-library.php' )
	->without_local_object_cache()
	->install();
