<?php
/**
 * WP Component Library Components: Layouts/Sidebar-Content
 *
 * @package WP_Component_Library
 * @global array $args Arguments passed to this template part.
 */

?>
<div style="display: grid; grid-gap: 1rem; grid-template-columns: minmax(min-content, 300px) 1fr; max-width: calc(100% - 15px);">
	<div style="max-width: 300px; max-height: 85vh; overflow-y: scroll;">
		<?php echo $args['sidebar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div>
		<?php echo $args['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
