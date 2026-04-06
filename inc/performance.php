<?php
/**
 * Performance tweaks (Core Web Vitals oriented).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action(
	'init',
	function () {
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wp_shortlink_wp_head');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'rest_output_link_wp_head');
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		wp_deregister_script('wp-embed');
	},
	20
);

add_filter(
	'script_loader_tag',
	function ($tag, $handle, $src) {
		if (in_array($handle, ['toolverse-app', 'toolverse-search', 'toolverse-tool-runner', 'toolverse-auth', 'toolverse-dashboard'], true)) {
			return str_replace(' src', ' defer src', $tag);
		}
		if ('toolverse-dark-mode' === $handle) {
			return str_replace(' src', ' async src', $tag);
		}
		return $tag;
	},
	10,
	3
);

add_action(
	'wp_head',
	function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	},
	1
);

add_action(
	'wp_head',
	function () {
		$critical = TOOLVERSE_DIR . '/assets/css/critical.css';
		if (file_exists($critical)) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static theme CSS file.
			echo '<style id="critical-css">' . file_get_contents($critical) . '</style>' . "\n";
		}
	},
	3
);

add_filter(
	'upload_mimes',
	function ($mimes) {
		$mimes['webp'] = 'image/webp';
		return $mimes;
	}
);

add_filter(
	'the_content',
	function ($content) {
		if (strpos($content, 'loading=') !== false) {
			return $content;
		}
		return preg_replace('/<img((?![^>]*\bloading=)[^>]*)>/i', '<img loading="lazy"$1>', $content);
	}
);

add_filter(
	'wp_resource_hints',
	function ($hints, $relation_type) {
		if ('dns-prefetch' === $relation_type) {
			$hints[] = '//fonts.googleapis.com';
			$hints[] = '//fonts.gstatic.com';
		}
		return $hints;
	},
	10,
	2
);
