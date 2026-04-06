<?php
/**
 * Tool card.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

$tool = isset($args['toolverse_tool']) && is_array($args['toolverse_tool']) ? $args['toolverse_tool'] : null;
if (!$tool) {
	return;
}

$p = get_posts(
	[
		'post_type'              => 'tool',
		'name'                   => $tool['slug'],
		'posts_per_page'         => 1,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
	]
);
$link = $p ? get_permalink($p[0]) : home_url('/tool/' . rawurlencode($tool['slug']) . '/');
?>
<a href="<?php echo esc_url($link); ?>" class="tool-card" data-name="<?php echo esc_attr(strtolower($tool['name'])); ?>" data-slug="<?php echo esc_attr($tool['slug']); ?>">
	<span class="tool-card-icon" aria-hidden="true"><?php echo esc_html($tool['icon'] ?? '🔧'); ?></span>
	<span class="tool-card-body">
		<span class="tool-card-title"><?php echo esc_html($tool['name']); ?></span>
		<span class="tool-card-desc"><?php echo esc_html(wp_html_excerpt($tool['description'] ?? '', 100, '…')); ?></span>
	</span>
	<span class="tool-card-cat"><?php echo esc_html($tool['category'] ?? ''); ?></span>
</a>
