<?php
/**
 * Template Name: All Tools Directory
 *
 * @package toolverse
 */

get_header();

$cat_filter = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$tools = toolverse_get_all_tools();
if ($cat_filter) {
	$tools = array_values(
		array_filter(
			$tools,
			static function ($t) use ($cat_filter) {
				return ($t['category_slug'] ?? '') === $cat_filter;
			}
		)
	);
}
?>
<div class="tools-archive-hero">
	<div class="container">
		<h1><?php esc_html_e('All Tools', 'toolverse'); ?></h1>
		<p><?php esc_html_e('Browse the full catalog.', 'toolverse'); ?></p>
		<?php get_template_part('template-parts/tool', 'category-header'); ?>
		<div class="tools-toolbar">
			<label class="screen-reader-text" for="tools-filter-input"><?php esc_html_e('Filter tools', 'toolverse'); ?></label>
			<input type="search" id="tools-filter-input" class="tools-filter-input" placeholder="<?php esc_attr_e('Type to filter…', 'toolverse'); ?>" autocomplete="off">
		</div>
	</div>
</div>
<div class="container tools-archive-body">
	<div class="tools-grid" id="tools-archive-grid">
		<?php foreach ($tools as $tool) : ?>
			<?php get_template_part('template-parts/tool', 'card', ['toolverse_tool' => $tool]); ?>
		<?php endforeach; ?>
	</div>
</div>
<?php
get_footer();
