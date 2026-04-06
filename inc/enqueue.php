<?php
/**
 * Scripts and styles.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('wp_enqueue_scripts', 'toolverse_enqueue_assets');
function toolverse_enqueue_assets(): void {
	$uri  = TOOLVERSE_URI;
	$ver  = TOOLVERSE_VERSION;
	$deps = [];

	wp_enqueue_style(
		'toolverse-fonts',
		'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
		[],
		null
	);
	$deps[] = 'toolverse-fonts';

	wp_enqueue_style('toolverse-main', $uri . '/assets/css/main.css', $deps, $ver);
	wp_enqueue_style('toolverse-dark', $uri . '/assets/css/dark-mode.css', ['toolverse-main'], $ver);

	if (is_page_template('page-templates/page-dashboard.php') || is_page_template('page-templates/page-admin-panel.php')) {
		wp_enqueue_style('toolverse-dashboard', $uri . '/assets/css/dashboard.css', ['toolverse-main'], $ver);
	}

	if (is_post_type_archive('tool') || is_singular('tool') || is_tax('tool_category') || is_page_template('page-templates/page-tools.php')) {
		wp_enqueue_style('toolverse-tools', $uri . '/assets/css/tools.css', ['toolverse-main'], $ver);
	}

	if (is_home() || is_archive() || is_singular('post') || is_page_template('page-templates/page-blog.php')) {
		wp_enqueue_style('toolverse-blog', $uri . '/assets/css/blog.css', ['toolverse-main'], $ver);
	}

	wp_enqueue_script('toolverse-dark-mode', $uri . '/assets/js/dark-mode.js', [], $ver, false);
	wp_enqueue_script('toolverse-app', $uri . '/assets/js/app.js', [], $ver, true);
	wp_enqueue_script('toolverse-search', $uri . '/assets/js/search.js', ['toolverse-app'], $ver, true);
	wp_enqueue_script('toolverse-tool-runner', $uri . '/assets/js/tool-runner.js', ['toolverse-app'], $ver, true);

	wp_localize_script(
		'toolverse-app',
		'toolverseData',
		[
			'restUrl'   => esc_url_raw(rest_url('toolverse/v1/')),
			'nonce'     => wp_create_nonce('wp_rest'),
			'authNonce' => wp_create_nonce('toolverse_auth_nonce'),
			'ajaxUrl'   => admin_url('admin-ajax.php'),
			'homeUrl'   => home_url('/'),
			'isLogged'  => is_user_logged_in(),
			'userId'    => get_current_user_id(),
		]
	);

	if (is_page_template('page-templates/page-login.php') || is_page_template('page-templates/page-register.php')) {
		wp_enqueue_script('toolverse-auth', $uri . '/assets/js/auth.js', ['toolverse-app'], $ver, true);
	}

	if (is_page_template('page-templates/page-dashboard.php')) {
		wp_enqueue_script('toolverse-dashboard', $uri . '/assets/js/dashboard.js', ['toolverse-app'], $ver, true);
	}
}

add_action('wp_footer', 'toolverse_register_sw', 99);
function toolverse_register_sw(): void {
	if (is_admin()) {
		return;
	}
	$sw = TOOLVERSE_URI . '/assets/js/sw.js';
	?>
	<script>
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('<?php echo esc_url($sw); ?>').catch(function () {});
	}
	</script>
	<?php
}
