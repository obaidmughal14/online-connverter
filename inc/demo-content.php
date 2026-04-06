<?php
/**
 * One-time demo categories and blog posts.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Seed demo blog content after theme switch.
 */
function toolverse_install_demo_content(): void {
	if (get_option('toolverse_demo_installed')) {
		return;
	}

	$cats = [
		'PDF Tips & Tricks',
		'Image Editing Guides',
		'SEO & Marketing',
		'Developer Resources',
		'AI Tools & Productivity',
		'Tool Updates & News',
	];
	$cat_ids = [];
	foreach ($cats as $name) {
		$existing = get_term_by('name', $name, 'category');
		if ($existing && !is_wp_error($existing)) {
			$cat_ids[] = (int) $existing->term_id;
			continue;
		}
		$ins = wp_insert_term($name, 'category');
		$cat_ids[] = is_wp_error($ins) ? 1 : (int) $ins['term_id'];
	}

	$demo_posts = [
		[
			'title'    => '10 Ways to Compress a PDF Without Losing Quality (2024 Guide)',
			'excerpt'  => 'Learn professional techniques to reduce PDF file size while keeping text sharp and images clear.',
			'category' => 0,
			'tags'     => 'pdf, compress, tools',
			'content'  => toolverse_demo_html(
				'Large PDFs are frustrating in email and on the web. This guide walks through practical ways to shrink files without ruining quality.',
				[
					'Why PDF size matters for storage, uploads, and Core Web Vitals.',
					'Use lossless optimization first: remove unused objects and compress streams.',
					'Downsample images only when print quality is not required.',
					'Split long documents when only a section needs sharing.',
				]
			),
		],
		[
			'title'    => 'How to Remove Background from Any Photo in 3 Seconds (AI-Powered)',
			'excerpt'  => 'AI background removal for product shots, headshots, and creative projects — what to watch for and how to get clean edges.',
			'category' => 1,
			'tags'     => 'background remover, AI, images',
			'content'  => toolverse_demo_html(
				'Background removal used to mean meticulous masking. Modern models can separate subjects in seconds.',
				[
					'Start with good lighting and contrast between subject and backdrop.',
					'Export PNG with alpha for overlays and presentations.',
					'For ecommerce, keep colors accurate and watch for hair detail.',
				]
			),
		],
		[
			'title'    => 'The 15 Best AI Writing Tools in 2024 (Free vs Paid Comparison)',
			'excerpt'  => 'A practical comparison of assistants and generators — and how to pick the right stack for your workflow.',
			'category' => 4,
			'tags'     => 'AI writing, productivity',
			'content'  => toolverse_demo_html(
				'AI writing tools range from quick email polishers to long-form blog assistants.',
				[
					'Compare pricing, context windows, and privacy policies.',
					'Pair generators with human editing for tone and fact checking.',
					'Use Online Converter text utilities for formatting, counting, and cleanup.',
				]
			),
		],
		[
			'title'    => 'JSON vs XML vs CSV: Which Data Format Should You Use?',
			'excerpt'  => 'A developer-friendly overview of trade-offs, tooling, and performance.',
			'category' => 3,
			'tags'     => 'JSON, XML, CSV',
			'content'  => toolverse_demo_html(
				'Choosing a data format affects schemas, interoperability, and parsing cost.',
				[
					'JSON: ubiquitous on the web, great with JavaScript, weak on rich schemas without extras.',
					'XML: strong ecosystem for enterprise and document-style data.',
					'CSV: simplest for tabular exports; fragile with nested structures.',
				]
			),
		],
		[
			'title'    => 'Core Web Vitals Explained: How to Score High on PageSpeed',
			'excerpt'  => 'LCP, INP, and CLS in plain language with fixes you can ship this week.',
			'category' => 2,
			'tags'     => 'SEO, performance, CWV',
			'content'  => toolverse_demo_html(
				'Core Web Vitals summarize real-user experience signals Google uses alongside other ranking factors.',
				[
					'Improve LCP by optimizing hero images, fonts, and server response time.',
					'Reduce INP by breaking up long tasks and deferring non-critical scripts.',
					'Fix CLS by reserving space for ads, embeds, and dynamic UI.',
				]
			),
		],
		[
			'title'    => 'Introducing AI Face Swap — Swap Faces in Photos Instantly',
			'excerpt'  => 'What is new, how to use it responsibly, and tips for natural-looking results.',
			'category' => 5,
			'tags'     => 'face swap, AI, release',
			'content'  => toolverse_demo_html(
				'Our new face swap experience focuses on speed and clear consent boundaries.',
				[
					'Use high-resolution sources aligned for similar head poses.',
					'Respect likeness rights and local regulations.',
					'Combine with our image tools for color correction and export formats.',
				]
			),
		],
	];

	foreach ($demo_posts as $post_data) {
		$cid = $cat_ids[ $post_data['category'] ] ?? $cat_ids[0];
		$pid = wp_insert_post(
			[
				'post_title'    => $post_data['title'],
				'post_content'  => $post_data['content'],
				'post_excerpt'  => $post_data['excerpt'],
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_category' => [$cid],
				'tags_input'    => $post_data['tags'],
			],
			true
		);
		if (is_wp_error($pid)) {
			continue;
		}
	}

	update_option('toolverse_demo_installed', true);
}

add_action('after_switch_theme', 'toolverse_install_demo_content', 25);

/**
 * @param string   $intro Intro paragraph.
 * @param string[] $bullets Bullet points.
 */
function toolverse_demo_html(string $intro, array $bullets): string {
	$html = '<p>' . esc_html($intro) . '</p>';
	foreach ($bullets as $b) {
		$html .= '<p>' . esc_html($b) . '</p>';
	}
	$html .= '<h2>' . esc_html__('Take action', 'toolverse') . '</h2><p>' . esc_html__('Explore matching tools on Online Converter and bookmark your favorites in the dashboard.', 'toolverse') . '</p>';
	return $html;
}
