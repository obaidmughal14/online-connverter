<?php
/**
 * SEO meta, Open Graph, JSON-LD.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter(
	'document_title_parts',
	function ($title) {
		if (is_singular('tool')) {
			$title['title'] = get_the_title() . ' — ' . __('Free Online Tool', 'toolverse');
			$title['site']   = get_bloginfo('name');
		}
		return $title;
	}
);

add_action(
	'wp_head',
	function () {
		global $post;
		$site_name = get_bloginfo('name');
		$url       = (is_singular() || is_front_page()) ? get_permalink() : home_url(add_query_arg([]));
		if (!$url) {
			$url = home_url('/');
		}
		$image = (is_singular() && $post && has_post_thumbnail($post)) ? get_the_post_thumbnail_url($post, 'blog-featured') : TOOLVERSE_URI . '/assets/images/og-default.svg';
		$desc  = '';
		if (is_singular() && $post) {
			$desc = get_the_excerpt($post);
		}
		if ('' === $desc) {
			$desc = get_bloginfo('description');
		}
		$title = wp_get_document_title();

		echo '<meta name="description" content="' . esc_attr(wp_html_excerpt($desc, 155, '…')) . '">' . "\n";
		echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
		echo '<meta name="author" content="' . esc_attr($site_name) . '">' . "\n";

		echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr(wp_html_excerpt($desc, 200, '…')) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
		echo '<meta property="og:locale" content="en_US">' . "\n";

		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr(wp_html_excerpt($desc, 200, '…')) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";

		echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
	},
	5
);

add_action(
	'wp_head',
	function () {
		if (is_front_page() || is_home()) {
			$schema = [
				'@context'        => 'https://schema.org',
				'@type'           => 'WebSite',
				'name'            => get_bloginfo('name'),
				'url'             => home_url('/'),
				'description'     => get_bloginfo('description'),
				'potentialAction' => [
					'@type'       => 'SearchAction',
					'target'      => home_url('/?s={search_term_string}'),
					'query-input' => 'required name=search_term_string',
				],
			];
			echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
		}

		if (is_singular('tool')) {
			global $post;
			$features = get_post_meta($post->ID, '_tool_features', true);
			if (!is_string($features)) {
				$features = '';
			}
			$schema = [
				'@context'              => 'https://schema.org',
				'@type'                 => 'SoftwareApplication',
				'name'                  => get_the_title(),
				'description'           => get_the_excerpt(),
				'url'                   => get_permalink(),
				'applicationCategory'   => 'WebApplication',
				'operatingSystem'       => 'Web Browser',
				'offers'                => [
					'@type'         => 'Offer',
					'price'         => '0',
					'priceCurrency' => 'USD',
				],
				'featureList'           => $features,
			];
			echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
		}

		if (is_singular('post')) {
			global $post;
			$schema = [
				'@context'         => 'https://schema.org',
				'@type'            => 'Article',
				'headline'         => get_the_title(),
				'description'      => get_the_excerpt(),
				'datePublished'    => get_the_date('c'),
				'dateModified'     => get_the_modified_date('c'),
				'author'           => [
					'@type' => 'Person',
					'name'  => get_the_author(),
				],
				'publisher'        => [
					'@type' => 'Organization',
					'name'  => get_bloginfo('name'),
					'logo'  => [
						'@type' => 'ImageObject',
						'url'   => TOOLVERSE_URI . '/assets/images/logo.svg',
					],
				],
				'image'            => get_the_post_thumbnail_url($post, 'blog-featured'),
				'url'              => get_permalink(),
				'mainEntityOfPage' => [
					'@type' => 'WebPage',
					'@id'   => get_permalink(),
				],
			];
			echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
		}
	},
	10
);

add_action(
	'publish_post',
	function () {
		$sitemap_url = home_url('/wp-sitemap.xml');
		$ping_urls   = [
			'https://www.google.com/ping?sitemap=' . rawurlencode($sitemap_url),
			'https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap_url),
		];
		foreach ($ping_urls as $url) {
			wp_remote_get($url, ['blocking' => false, 'timeout' => 3]);
		}
	}
);

add_filter(
	'robots_txt',
	function ($output, $public) {
		$output .= "\nSitemap: " . home_url('/wp-sitemap.xml');
		$output .= "\nUser-agent: GPTBot\nAllow: /\n";
		$output .= "User-agent: Claude-Web\nAllow: /\n";
		$output .= "User-agent: Bingbot\nAllow: /\n";
		return $output;
	},
	10,
	2
);
