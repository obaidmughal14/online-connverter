<?php
/**
 * Custom post types and taxonomies.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', 'toolverse_register_cpts');
function toolverse_register_cpts(): void {
	register_post_type(
		'tool',
		[
			'labels'       => [
				'name'          => __('Tools', 'toolverse'),
				'singular_name' => __('Tool', 'toolverse'),
			],
			'public'       => true,
			'has_archive'  => 'tools',
			'rewrite'      => [
				'slug'       => 'tool',
				'with_front' => false,
			],
			'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
			'menu_icon'    => 'dashicons-admin-tools',
			'show_in_rest' => true,
		]
	);

	register_taxonomy(
		'tool_category',
		'tool',
		[
			'labels'       => [
				'name'          => __('Tool Categories', 'toolverse'),
				'singular_name' => __('Category', 'toolverse'),
			],
			'hierarchical' => true,
			'rewrite'      => [
				'slug'         => 'tool-category',
				'with_front'   => false,
			],
			'show_in_rest' => true,
		]
	);
}

add_filter('use_block_editor_for_post_type', 'toolverse_filter_block_editor_post_type', 10, 2);
function toolverse_filter_block_editor_post_type(bool $use_block_editor, string $post_type): bool {
	return 'tool' === $post_type ? false : $use_block_editor;
}

/**
 * Ensure tool categories exist and sync registry to tool posts (idempotent).
 */
function toolverse_sync_tools_from_registry(): void {
	$cats = [];
	foreach (toolverse_get_all_tools() as $t) {
		$slug = $t['category_slug'] ?? sanitize_title($t['category'] ?? 'general');
		$name = $t['category'] ?? 'General';
		if (!isset($cats[ $slug ])) {
			$term = term_exists($slug, 'tool_category');
			if (!$term) {
				$term = wp_insert_term($name, 'tool_category', ['slug' => $slug]);
			}
			$cats[ $slug ] = is_wp_error($term) ? 0 : (int) (is_array($term) ? $term['term_id'] : $term);
		}
	}

	foreach (toolverse_get_all_tools() as $t) {
		$slug = $t['slug'];
		$existing = get_posts(
			[
				'post_type'              => 'tool',
				'name'                   => $slug,
				'posts_per_page'         => 1,
				'post_status'            => 'any',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);
		if ($existing) {
			$pid = (int) $existing[0];
			wp_update_post(
				[
					'ID'           => $pid,
					'post_title'   => $t['name'],
					'post_excerpt' => $t['description'],
					'post_status'  => 'publish',
				]
			);
		} else {
			$pid = wp_insert_post(
				[
					'post_type'    => 'tool',
					'post_title'   => $t['name'],
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_excerpt' => $t['description'],
				],
				true
			);
			if (is_wp_error($pid) || !$pid) {
				continue;
			}
			$pid = (int) $pid;
		}

		$cslug = $t['category_slug'] ?? sanitize_title($t['category'] ?? '');
		if (!empty($cats[ $cslug ])) {
			wp_set_object_terms($pid, [(int) $cats[ $cslug ]], 'tool_category', false);
		}

		update_post_meta($pid, '_tool_features', wp_strip_all_tags($t['description']));
		$steps = "Upload or paste your file or text\nProcess with one click\nDownload or copy the result";
		update_post_meta($pid, '_tool_steps', $steps);
		$faqs  = wp_json_encode(
			[
				['q' => __('Is this tool free?', 'toolverse'), 'a' => __('Yes. Core processing is free on Online Converter.', 'toolverse')],
				['q' => __('Are my files stored?', 'toolverse'), 'a' => __('Files are processed and removed according to our privacy policy.', 'toolverse')],
			]
		);
		update_post_meta($pid, '_tool_faqs', $faqs);
	}
}

add_action('after_switch_theme', 'toolverse_sync_tools_from_registry', 20);
