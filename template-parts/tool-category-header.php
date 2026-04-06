<?php
/**
 * Category pills for tools archive.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

$current = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$cats    = [
	['slug' => '', 'label' => __('All', 'toolverse')],
	['slug' => 'pdf-tools', 'label' => __('PDF', 'toolverse')],
	['slug' => 'image-tools', 'label' => __('Image', 'toolverse')],
	['slug' => 'text-tools', 'label' => __('Text', 'toolverse')],
	['slug' => 'seo-tools', 'label' => __('SEO', 'toolverse')],
	['slug' => 'developer-tools', 'label' => __('Developer', 'toolverse')],
	['slug' => 'converter-tools', 'label' => __('Converter', 'toolverse')],
	['slug' => 'ai-tools', 'label' => __('AI', 'toolverse')],
	['slug' => 'utility-tools', 'label' => __('Utility', 'toolverse')],
];
$base    = get_post_type_archive_link('tool');
?>
<div class="tool-cat-pills" role="tablist">
	<?php foreach ($cats as $c) : ?>
		<?php
		$url = $c['slug'] ? add_query_arg('category', $c['slug'], $base) : remove_query_arg('category', $base);
		$on = ($c['slug'] === '' && '' === $current) || ($c['slug'] !== '' && $c['slug'] === $current);
		?>
		<a role="tab" class="cat-pill<?php echo $on ? ' active' : ''; ?>" href="<?php echo esc_url($url); ?>"><?php echo esc_html($c['label']); ?></a>
	<?php endforeach; ?>
</div>
