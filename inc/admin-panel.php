<?php
/**
 * WP Admin — Online Converter (Devigon Tech).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('admin_menu', 'toolverse_admin_menu');
function toolverse_admin_menu(): void {
	add_menu_page(
		__( 'Online Converter Control', 'toolverse' ),
		'⚡ ' . toolverse_brand_main(),
		'manage_options',
		'toolverse-admin',
		'toolverse_admin_page',
		'dashicons-admin-tools',
		2
	);

	add_submenu_page('toolverse-admin', __('All Tools', 'toolverse'), __('All Tools', 'toolverse'), 'manage_options', 'toolverse-tools', 'toolverse_tools_page');
	add_submenu_page('toolverse-admin', __('All Users', 'toolverse'), __('All Users', 'toolverse'), 'manage_options', 'toolverse-users', 'toolverse_users_page');
	add_submenu_page('toolverse-admin', __('Analytics', 'toolverse'), __('Analytics', 'toolverse'), 'manage_options', 'toolverse-analytics', 'toolverse_analytics_page');
	add_submenu_page('toolverse-admin', __('API Keys', 'toolverse'), __('API Keys', 'toolverse'), 'manage_options', 'toolverse-apikeys', 'toolverse_apikeys_page');
	add_submenu_page('toolverse-admin', __('Settings', 'toolverse'), __('Settings', 'toolverse'), 'manage_options', 'toolverse-settings', 'toolverse_settings_page');
}

add_action('admin_enqueue_scripts', 'toolverse_admin_assets');
function toolverse_admin_assets(string $hook): void {
	if (strpos($hook, 'toolverse') === false) {
		return;
	}
	wp_enqueue_script('toolverse-admin', TOOLVERSE_URI . '/assets/js/admin-panel.js', ['jquery'], TOOLVERSE_VERSION, true);
	wp_localize_script(
		'toolverse-admin',
		'toolverseAdmin',
		[
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('toolverse_admin_nonce'),
		]
	);
}

function toolverse_admin_page(): void {
	global $wpdb;
	$table = $wpdb->prefix . 'tool_usage';
	$has   = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));

	$total_users = count_users()['total_users'];
	$total_tools = count(toolverse_get_all_tools());
	$total_usage = $has ? (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}") : 0;
	$today_usage = $has ? (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE DATE(used_at) = CURDATE()") : 0;
	$top_tools   = $has ? $wpdb->get_results("SELECT tool_slug, COUNT(*) as uses FROM {$table} GROUP BY tool_slug ORDER BY uses DESC LIMIT 10") : [];

	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php echo esc_html( '⚡ ' . toolverse_brand_full() . ' — ' . __( 'Control Center', 'toolverse' ) ); ?></h1>
		<div class="tv-stats-row">
			<div class="tv-stat-box">
				<div class="tv-stat-num"><?php echo esc_html(number_format_i18n($total_users)); ?></div>
				<div class="tv-stat-lbl"><?php esc_html_e('Total Users', 'toolverse'); ?></div>
			</div>
			<div class="tv-stat-box">
				<div class="tv-stat-num"><?php echo esc_html((string) $total_tools); ?></div>
				<div class="tv-stat-lbl"><?php esc_html_e('Active Tools', 'toolverse'); ?></div>
			</div>
			<div class="tv-stat-box">
				<div class="tv-stat-num"><?php echo esc_html(number_format_i18n($total_usage)); ?></div>
				<div class="tv-stat-lbl"><?php esc_html_e('Total Uses', 'toolverse'); ?></div>
			</div>
			<div class="tv-stat-box">
				<div class="tv-stat-num"><?php echo esc_html(number_format_i18n($today_usage)); ?></div>
				<div class="tv-stat-lbl"><?php esc_html_e("Today's Uses", 'toolverse'); ?></div>
			</div>
		</div>
		<h2><?php esc_html_e('Top 10 Most Used Tools', 'toolverse'); ?></h2>
		<table class="wp-list-table widefat striped">
			<thead><tr><th><?php esc_html_e('Tool', 'toolverse'); ?></th><th><?php esc_html_e('Uses', 'toolverse'); ?></th><th><?php esc_html_e('Action', 'toolverse'); ?></th></tr></thead>
			<tbody>
			<?php if (empty($top_tools)) : ?>
				<tr><td colspan="3"><?php esc_html_e('No usage data yet.', 'toolverse'); ?></td></tr>
			<?php else : ?>
				<?php foreach ($top_tools as $t) : ?>
				<tr>
					<td><?php echo esc_html($t->tool_slug); ?></td>
					<td><?php echo esc_html(number_format_i18n((int) $t->uses)); ?></td>
					<td>
						<a href="<?php echo esc_url(admin_url('admin.php?page=toolverse-tools')); ?>"><?php esc_html_e('Configure', 'toolverse'); ?></a>
						|
						<a href="<?php echo esc_url(home_url('/tool/' . $t->tool_slug . '/')); ?>" target="_blank" rel="noopener"><?php esc_html_e('View', 'toolverse'); ?></a>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function toolverse_tools_page(): void {
	$all_tools = toolverse_get_all_tools();
	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php esc_html_e('⚙️ Manage Tools', 'toolverse'); ?></h1>
		<p><?php esc_html_e('Enable or disable tools, set rate limits, and configure API keys.', 'toolverse'); ?></p>
		<div class="tv-tools-filter">
			<input type="search" id="tool-search" placeholder="<?php esc_attr_e('Search tools…', 'toolverse'); ?>">
			<select id="category-filter">
				<option value=""><?php esc_html_e('All Categories', 'toolverse'); ?></option>
				<option value="pdf-tools"><?php esc_html_e('PDF Tools', 'toolverse'); ?></option>
				<option value="image-tools"><?php esc_html_e('Image Tools', 'toolverse'); ?></option>
				<option value="text-tools"><?php esc_html_e('Text Tools', 'toolverse'); ?></option>
				<option value="seo-tools"><?php esc_html_e('SEO Tools', 'toolverse'); ?></option>
				<option value="developer-tools"><?php esc_html_e('Developer Tools', 'toolverse'); ?></option>
				<option value="converter-tools"><?php esc_html_e('Converter Tools', 'toolverse'); ?></option>
				<option value="ai-tools"><?php esc_html_e('AI Tools', 'toolverse'); ?></option>
				<option value="utility-tools"><?php esc_html_e('Utility Tools', 'toolverse'); ?></option>
			</select>
		</div>
		<table class="wp-list-table widefat striped" id="tools-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Tool Name', 'toolverse'); ?></th>
					<th><?php esc_html_e('Category', 'toolverse'); ?></th>
					<th><?php esc_html_e('Status', 'toolverse'); ?></th>
					<th><?php esc_html_e('Pro Only', 'toolverse'); ?></th>
					<th><?php esc_html_e('Daily Limit', 'toolverse'); ?></th>
					<th><?php esc_html_e('Actions', 'toolverse'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($all_tools as $tool) : ?>
				<?php
				$settings = toolverse_get_tool_settings($tool['slug']);
				$cat_slug = $tool['category_slug'] ?? sanitize_title($tool['category'] ?? '');
				?>
				<tr data-name="<?php echo esc_attr(strtolower($tool['name'])); ?>" data-cat="<?php echo esc_attr($cat_slug); ?>">
					<td>
						<strong><?php echo esc_html($tool['name']); ?></strong>
						<div class="row-actions">
							<a href="<?php echo esc_url(home_url('/tool/' . $tool['slug'] . '/')); ?>" target="_blank" rel="noopener"><?php esc_html_e('View', 'toolverse'); ?></a>
						</div>
					</td>
					<td><?php echo esc_html($tool['category'] ?? ''); ?></td>
					<td>
						<label class="tv-toggle">
							<input type="checkbox" class="tv-tool-enabled" data-slug="<?php echo esc_attr($tool['slug']); ?>" <?php checked(!empty($settings['is_enabled']), true); ?>>
							<span class="tv-slider"></span>
						</label>
					</td>
					<td>
						<label class="tv-toggle">
							<input type="checkbox" class="tv-tool-pro" data-slug="<?php echo esc_attr($tool['slug']); ?>" <?php checked(!empty($settings['is_pro']), true); ?>>
							<span class="tv-slider"></span>
						</label>
					</td>
					<td>
						<input type="number" class="tv-limit-input" data-slug="<?php echo esc_attr($tool['slug']); ?>" value="<?php echo esc_attr((string) (int) ($settings['daily_limit'] ?? 0)); ?>" min="0" title="<?php esc_attr_e('0 = unlimited', 'toolverse'); ?>">
					</td>
					<td><span class="description"><?php esc_html_e('Saved on change', 'toolverse'); ?></span></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function toolverse_users_page(): void {
	$users = get_users(['number' => 50, 'orderby' => 'registered', 'order' => 'DESC']);
	global $wpdb;
	$table = $wpdb->prefix . 'tool_usage';
	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php esc_html_e('👥 Manage Users', 'toolverse'); ?></h1>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e('User', 'toolverse'); ?></th>
					<th><?php esc_html_e('Email', 'toolverse'); ?></th>
					<th><?php esc_html_e('Role', 'toolverse'); ?></th>
					<th><?php esc_html_e('Registered', 'toolverse'); ?></th>
					<th><?php esc_html_e('Tools Used', 'toolverse'); ?></th>
					<th><?php esc_html_e('Actions', 'toolverse'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($users as $u) : ?>
				<?php
				$usage_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d", $u->ID));
				?>
				<tr>
					<td><?php echo get_avatar($u->ID, 32); ?> <strong><?php echo esc_html($u->display_name); ?></strong></td>
					<td><?php echo esc_html($u->user_email); ?></td>
					<td><?php echo esc_html(implode(', ', $u->roles)); ?></td>
					<td><?php echo esc_html(wp_date('M j, Y', strtotime($u->user_registered))); ?></td>
					<td><?php echo esc_html(number_format_i18n($usage_count)); ?></td>
					<td>
						<a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . (int) $u->ID)); ?>" class="button button-small"><?php esc_html_e('Edit', 'toolverse'); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function toolverse_analytics_page(): void {
	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php esc_html_e('Analytics', 'toolverse'); ?></h1>
		<p><?php esc_html_e('Connect Google Analytics or export raw usage from the database table', 'toolverse'); ?> <code>wp_tool_usage</code>.</p>
	</div>
	<?php
}

function toolverse_apikeys_page(): void {
	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php esc_html_e('API Keys', 'toolverse'); ?></h1>
		<p><?php esc_html_e('Store per-tool API keys in the tool_settings table or use wp-config constants for production.', 'toolverse'); ?></p>
	</div>
	<?php
}

function toolverse_settings_page(): void {
	if (!empty($_POST['toolverse_save_sidebar']) && check_admin_referer('toolverse_settings')) {
		update_option('toolverse_sidebar_ad', wp_kses_post(wp_unslash($_POST['toolverse_sidebar_ad'] ?? '')));
		echo '<div class="updated"><p>' . esc_html__('Saved.', 'toolverse') . '</p></div>';
	}
	$ad = get_option('toolverse_sidebar_ad', '');
	?>
	<div class="wrap toolverse-admin-wrap">
		<h1><?php echo esc_html( toolverse_brand_main() . ' — ' . __( 'Settings', 'toolverse' ) ); ?></h1>
		<form method="post">
			<?php wp_nonce_field('toolverse_settings'); ?>
			<table class="form-table">
				<tr>
					<th><label for="toolverse_sidebar_ad"><?php esc_html_e('Tool sidebar ad HTML', 'toolverse'); ?></label></th>
					<td><textarea name="toolverse_sidebar_ad" id="toolverse_sidebar_ad" class="large-text" rows="6"><?php echo esc_textarea($ad); ?></textarea></td>
				</tr>
			</table>
			<?php submit_button(__('Save', 'toolverse'), 'primary', 'toolverse_save_sidebar'); ?>
		</form>
	</div>
	<?php
}

add_action('wp_ajax_toolverse_admin_tool_settings', 'toolverse_ajax_admin_tool_settings');
function toolverse_ajax_admin_tool_settings(): void {
	check_ajax_referer('toolverse_admin_nonce', 'nonce');
	if (!current_user_can('manage_options')) {
		wp_send_json_error(['message' => 'Forbidden'], 403);
	}
	$slug      = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';
	$enabled   = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : 1;
	$pro       = isset($_POST['is_pro']) ? (int) (bool) $_POST['is_pro'] : 0;
	$limit     = isset($_POST['daily_limit']) ? max(0, (int) $_POST['daily_limit']) : 0;
	if (!$slug) {
		wp_send_json_error(['message' => 'Bad slug']);
	}
	global $wpdb;
	$table = $wpdb->prefix . 'tool_settings';
	$wpdb->replace(
		$table,
		[
			'tool_slug'     => $slug,
			'is_enabled'    => $enabled,
			'is_pro'        => $pro,
			'daily_limit'   => $limit,
			'api_key'       => '',
			'settings_json' => '{}',
		],
		['%s', '%d', '%d', '%d', '%s', '%s']
	);
	wp_send_json_success();
}

add_action('admin_head', 'toolverse_admin_inline_css');
function toolverse_admin_inline_css(): void {
	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (!$screen || strpos((string) $screen->id, 'toolverse') === false) {
		return;
	}
	?>
	<style>
		.tv-stats-row { display:flex; gap:16px; flex-wrap:wrap; margin:20px 0; }
		.tv-stat-box { background:#fff; border:1px solid #ccd0d4; padding:20px 24px; border-radius:6px; min-width:140px; }
		.tv-stat-num { font-size:28px; font-weight:700; }
		.tv-stat-lbl { color:#646970; }
		.tv-tools-filter { margin:16px 0; display:flex; gap:12px; align-items:center; }
		.tv-toggle { position:relative; display:inline-block; width:44px; height:24px; }
		.tv-toggle input { opacity:0; width:0; height:0; }
		.tv-slider { position:absolute; cursor:pointer; inset:0; background:#ccc; border-radius:24px; transition:.2s; }
		.tv-toggle input:checked + .tv-slider { background:linear-gradient(90deg,#B47CFD,#E1785B); }
		.tv-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
		.tv-toggle input:checked + .tv-slider:before { transform:translateX(20px); }
	</style>
	<?php
}
