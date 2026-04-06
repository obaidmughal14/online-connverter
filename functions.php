<?php
/**
 * Online Converter theme functions (Devigon Tech).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

define('TOOLVERSE_VERSION', '1.0.2');
define('TOOLVERSE_DIR', get_template_directory());
define('TOOLVERSE_URI', get_template_directory_uri());

/**
 * Branding: aligns with Devigon Tech (https://www.devigontech.com/).
 */
function toolverse_brand_main(): string {
	return __( 'Online Converter', 'toolverse' );
}

function toolverse_brand_credit(): string {
	return __( 'By Devigon Tech', 'toolverse' );
}

function toolverse_brand_full(): string {
	return __( 'Online Converter (By Devigon Tech)', 'toolverse' );
}

$toolverse_includes = [
	'/inc/database.php',
	'/inc/security.php',
	'/inc/enqueue.php',
	'/inc/custom-post-types.php',
	'/inc/rest-api.php',
	'/inc/auth.php',
	'/inc/dashboard.php',
	'/inc/admin-panel.php',
	'/inc/seo.php',
	'/inc/performance.php',
	'/inc/demo-content.php',
	'/inc/tools/text-tools.php',
	'/inc/tools/pdf-tools.php',
	'/inc/tools/image-tools.php',
	'/inc/tools/seo-tools.php',
	'/inc/tools/developer-tools.php',
	'/inc/tools/converter-tools.php',
	'/inc/tools/ai-tools.php',
	'/inc/tools/utility-tools.php',
];

foreach ($toolverse_includes as $file) {
	$path = TOOLVERSE_DIR . $file;
	if (file_exists($path)) {
		require_once $path;
	}
}

add_action('widgets_init', 'toolverse_widgets_init');
function toolverse_widgets_init(): void {
	register_sidebar(
		[
			'name'          => __('Sidebar', 'toolverse'),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]
	);
}

add_action('after_setup_theme', 'toolverse_setup');
function toolverse_setup(): void {
	load_theme_textdomain('toolverse', TOOLVERSE_DIR . '/languages');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('responsive-embeds');
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
	add_theme_support('wp-block-styles');
	add_image_size('tool-thumb', 400, 300, true);
	add_image_size('blog-featured', 1200, 630, true);
	add_image_size('blog-card', 600, 400, true);
	register_nav_menus(
		[
			'primary'   => __('Primary Navigation', 'toolverse'),
			'dashboard' => __('Dashboard Menu', 'toolverse'),
			'footer'    => __('Footer Links', 'toolverse'),
		]
	);
}

/**
 * Output dark mode toggle.
 */
function toolverse_dark_mode_toggle(): void {
	get_template_part('template-parts/dark-mode-toggle');
}

/**
 * All registered tools (200).
 *
 * @return array<int, array<string, mixed>>
 */
function toolverse_get_all_tools(): array {
	static $tools = null;
	if (null === $tools) {
		$registry = TOOLVERSE_DIR . '/inc/tools-registry.php';
		$tools    = file_exists($registry) ? include $registry : [];
		if (!is_array($tools)) {
			$tools = [];
		}
		$tools = apply_filters('toolverse_tools_list', $tools);
	}
	return $tools;
}

/**
 * Get one tool by slug.
 *
 * @return array<string, mixed>|null
 */
function toolverse_get_tool_by_slug(string $slug): ?array {
	foreach (toolverse_get_all_tools() as $tool) {
		if (!empty($tool['slug']) && $tool['slug'] === $slug) {
			return $tool;
		}
	}
	return null;
}

/**
 * Tool settings row from DB.
 *
 * @return array<string, mixed>
 */
function toolverse_get_tool_settings(string $slug): array {
	global $wpdb;
	$table = $wpdb->prefix . 'tool_settings';
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is fixed.
	$exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
	if (!$exists) {
		return [
			'is_enabled'    => 1,
			'is_pro'        => 0,
			'daily_limit'   => 0,
			'api_key'       => '',
			'settings_json' => '{}',
		];
	}
	$row = $wpdb->get_row(
		$wpdb->prepare("SELECT * FROM {$table} WHERE tool_slug = %s", $slug),
		ARRAY_A
	);
	return $row ?: [
		'is_enabled'    => 1,
		'is_pro'        => 0,
		'daily_limit'   => 0,
		'api_key'       => '',
		'settings_json' => '{}',
	];
}

/**
 * Render tool UI (per-tool file or default).
 */
function toolverse_render_tool_interface(string $slug): void {
	$safe = preg_replace('/[^a-z0-9\-]/', '', $slug);
	$file = TOOLVERSE_DIR . '/inc/tools-ui/' . $safe . '.php';
	if ($safe && file_exists($file)) {
		include $file;
		return;
	}
	echo '<div class="tool-generic" data-tool-slug="' . esc_attr($slug) . '">';
	echo '<p class="tool-lead">' . esc_html__('This tool runs in your browser or via the API. Use the controls below or connect your own backend.', 'toolverse') . '</p>';
	echo '<label class="tool-label" for="tool-input-' . esc_attr($slug) . '">' . esc_html__('Input', 'toolverse') . '</label>';
	echo '<textarea id="tool-input-' . esc_attr($slug) . '" class="tool-textarea" rows="8" placeholder="' . esc_attr__('Paste input here…', 'toolverse') . '"></textarea>';
	echo '<div class="tool-actions-row">';
	echo '<button type="button" class="btn-primary" data-tool-run="' . esc_attr($slug) . '">' . esc_html__('Run tool', 'toolverse') . '</button>';
	echo '</div>';
	echo '<label class="tool-label" for="tool-output-' . esc_attr($slug) . '">' . esc_html__('Output', 'toolverse') . '</label>';
	echo '<textarea id="tool-output-' . esc_attr($slug) . '" class="tool-textarea tool-output" rows="8" readonly placeholder="' . esc_attr__('Result appears here…', 'toolverse') . '"></textarea>';
	echo '</div>';
}
add_action('toolverse_render_tool', 'toolverse_render_tool_interface');

/** Hide WP admin bar for non-admins */
add_action(
	'after_setup_theme',
	function () {
		if (!current_user_can('manage_options')) {
			show_admin_bar(false);
		}
	}
);

add_filter(
	'show_admin_bar',
	function ($show) {
		return current_user_can('manage_options') ? $show : false;
	}
);

add_action(
	'wp_head',
	function () {
		if (!current_user_can('manage_options')) {
			echo '<style id="toolverse-hide-adminbar">#wpadminbar{display:none!important}html{margin-top:0!important}</style>';
		}
	},
	100
);

add_action(
	'admin_init',
	function () {
		if (is_admin() && !current_user_can('manage_options') && !wp_doing_ajax()) {
			wp_safe_redirect(home_url('/dashboard/'));
			exit;
		}
	}
);

/**
 * Fallback menu when no menu assigned.
 */
function toolverse_fallback_menu(): void {
	echo '<ul class="nav-menu">';
	echo '<li><a href="' . esc_url(home_url('/tools/')) . '">' . esc_html__('Tools', 'toolverse') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/blog/')) . '">' . esc_html__('Blog', 'toolverse') . '</a></li>';
	echo '</ul>';
}
