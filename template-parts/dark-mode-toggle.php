<?php
/**
 * Dark / light toggle.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<button type="button" class="theme-toggle-btn" onclick="__toggleTheme()" aria-label="<?php esc_attr_e('Toggle dark mode', 'toolverse'); ?>" title="<?php esc_attr_e('Toggle dark/light mode', 'toolverse'); ?>">
	<span class="toggle-track" aria-hidden="true">
		<span class="toggle-thumb"></span>
	</span>
	<span class="toggle-icon" aria-hidden="true">🌙</span>
</button>
