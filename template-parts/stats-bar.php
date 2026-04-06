<?php
/**
 * Animated stats row.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<section class="stats-section" aria-label="<?php esc_attr_e('Platform stats', 'toolverse'); ?>">
	<div class="container">
		<div class="stats-row">
			<div class="stat-item">
				<span class="stat-number" data-count="200">0</span><span>+</span>
				<span class="stat-label"><?php esc_html_e('Free Tools', 'toolverse'); ?></span>
			</div>
			<div class="stat-divider" aria-hidden="true"></div>
			<div class="stat-item">
				<span class="stat-number" data-count="2000000">0</span><span>+</span>
				<span class="stat-label"><?php esc_html_e('Users Worldwide', 'toolverse'); ?></span>
			</div>
			<div class="stat-divider" aria-hidden="true"></div>
			<div class="stat-item">
				<span class="stat-number" data-count="50000000">0</span><span>+</span>
				<span class="stat-label"><?php esc_html_e('Files Processed', 'toolverse'); ?></span>
			</div>
			<div class="stat-divider" aria-hidden="true"></div>
			<div class="stat-item">
				<span class="stat-number" data-count="99">0</span><span>%</span>
				<span class="stat-label"><?php esc_html_e('Uptime', 'toolverse'); ?></span>
			</div>
		</div>
	</div>
</section>
