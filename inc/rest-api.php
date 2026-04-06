<?php
/**
 * REST API routes.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('rest_api_init', 'toolverse_register_rest_routes');
function toolverse_register_rest_routes(): void {
	register_rest_route(
		'toolverse/v1',
		'/tools',
		[
			'methods'             => 'GET',
			'callback'            => 'toolverse_rest_list_tools',
			'permission_callback' => '__return_true',
			'args'                => [
				'search' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
				'cat'    => ['type' => 'string', 'sanitize_callback' => 'sanitize_title'],
			],
		]
	);

	register_rest_route(
		'toolverse/v1',
		'/tools/(?P<slug>[a-z0-9\-]+)/run',
		[
			'methods'             => 'POST',
			'callback'            => 'toolverse_rest_run_tool',
			'permission_callback' => '__return_true',
			'args'                => [
				'slug' => ['type' => 'string'],
			],
		]
	);

	register_rest_route(
		'toolverse/v1',
		'/favorites',
		[
			[
				'methods'             => 'GET',
				'callback'            => 'toolverse_rest_get_favorites',
				'permission_callback' => 'toolverse_rest_require_login',
			],
			[
				'methods'             => 'POST',
				'callback'            => 'toolverse_rest_toggle_favorite',
				'permission_callback' => 'toolverse_rest_require_login',
			],
		]
	);

	register_rest_route(
		'toolverse/v1',
		'/usage',
		[
			'methods'             => 'GET',
			'callback'            => 'toolverse_rest_get_usage',
			'permission_callback' => 'toolverse_rest_require_login',
		]
	);

	register_rest_route(
		'toolverse/v1',
		'/profile',
		[
			'methods'             => 'POST',
			'callback'            => 'toolverse_rest_update_profile',
			'permission_callback' => 'toolverse_rest_require_login',
		]
	);
}

function toolverse_rest_require_login(): bool {
	return is_user_logged_in();
}

function toolverse_rest_list_tools(WP_REST_Request $req): WP_REST_Response {
	$q   = (string) $req->get_param('search');
	$cat = (string) $req->get_param('cat');
	$all = toolverse_get_all_tools();
	$out = [];
	foreach ($all as $t) {
		if ($cat && ($t['category_slug'] ?? '') !== $cat) {
			continue;
		}
		if ($q) {
			$hay = strtolower($t['name'] . ' ' . ($t['description'] ?? '') . ' ' . $t['slug']);
			if (strpos($hay, strtolower($q)) === false) {
				continue;
			}
		}
		$out[] = [
			'slug'        => $t['slug'],
			'name'        => $t['name'],
			'category'    => $t['category'],
			'category_slug' => $t['category_slug'] ?? '',
			'icon'        => $t['icon'] ?? '🔧',
			'url'         => home_url('/tool/' . $t['slug'] . '/'),
		];
	}
	return new WP_REST_Response($out);
}

function toolverse_rest_run_tool(WP_REST_Request $req): WP_REST_Response {
	$slug = (string) $req->get_param('slug');
	$body = $req->get_json_params();
	if (!is_array($body)) {
		$body = [];
	}

	$tool = toolverse_get_tool_by_slug($slug);
	if (!$tool) {
		return new WP_REST_Response(['message' => __('Unknown tool.', 'toolverse')], 404);
	}

	$settings = toolverse_get_tool_settings($slug);
	if ((int) ($settings['is_enabled'] ?? 1) === 0) {
		return new WP_REST_Response(['message' => __('This tool is disabled.', 'toolverse')], 403);
	}

	$input = isset($body['input']) ? $body['input'] : '';
	$input = toolverse_sanitize_tool_input(is_string($input) ? $input : wp_json_encode($input));

	$result = apply_filters('toolverse_tool_process_' . $slug, null, $input, $body, $slug);
	if (null === $result) {
		$result = apply_filters('toolverse_tool_process', null, $slug, $input, $body);
	}
	if (null === $result) {
		$result = [
			'ok'      => true,
			'message' => __('Received. Connect server-side processing in your environment for full conversion.', 'toolverse'),
			'echo'    => $input,
		];
	}

	$log_payload = is_string($result) ? $result : wp_json_encode($result);
	toolverse_log_tool_usage($slug, is_string($log_payload) ? $log_payload : '');

	return new WP_REST_Response(['success' => true, 'data' => $result]);
}

function toolverse_log_tool_usage(string $slug, string $result_data = ''): void {
	global $wpdb;
	$table = $wpdb->prefix . 'tool_usage';
	$wpdb->insert(
		$table,
		[
			'tool_slug'   => $slug,
			'user_id'     => get_current_user_id(),
			'session_id'  => toolverse_get_session_id(),
			'ip_address'  => toolverse_client_ip(),
			'result_data' => $result_data,
		],
		['%s', '%d', '%s', '%s', '%s']
	);
}

function toolverse_get_session_id(): string {
	if (!empty($_COOKIE['toolverse_sid'])) {
		return sanitize_text_field(wp_unslash($_COOKIE['toolverse_sid']));
	}
	return wp_generate_password(12, false, false);
}

function toolverse_client_ip(): string {
	$ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
	return substr($ip, 0, 45);
}

function toolverse_rest_get_favorites(): WP_REST_Response {
	global $wpdb;
	$uid   = get_current_user_id();
	$table = $wpdb->prefix . 'user_favorites';
	$rows  = $wpdb->get_col($wpdb->prepare("SELECT tool_slug FROM {$table} WHERE user_id = %d ORDER BY saved_at DESC", $uid));
	return new WP_REST_Response(array_values(array_map('strval', $rows ?: [])));
}

function toolverse_rest_toggle_favorite(WP_REST_Request $req): WP_REST_Response {
	global $wpdb;
	$body = $req->get_json_params();
	$slug = isset($body['slug']) ? sanitize_title($body['slug']) : '';
	$on   = !empty($body['on']);
	if (!$slug || !toolverse_get_tool_by_slug($slug)) {
		return new WP_REST_Response(['message' => __('Invalid tool.', 'toolverse')], 400);
	}
	$table = $wpdb->prefix . 'user_favorites';
	$uid   = get_current_user_id();
	if ($on) {
		$wpdb->replace(
			$table,
			[
				'user_id'   => $uid,
				'tool_slug' => $slug,
			],
			['%d', '%s']
		);
	} else {
		$wpdb->delete($table, ['user_id' => $uid, 'tool_slug' => $slug], ['%d', '%s']);
	}
	return new WP_REST_Response(['success' => true, 'slug' => $slug, 'on' => $on]);
}

function toolverse_rest_get_usage(): WP_REST_Response {
	global $wpdb;
	$uid   = get_current_user_id();
	$table = $wpdb->prefix . 'tool_usage';
	$total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d", $uid));
	$recent = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT tool_slug, used_at FROM {$table} WHERE user_id = %d ORDER BY used_at DESC LIMIT 20",
			$uid
		),
		ARRAY_A
	);
	return new WP_REST_Response(
		[
			'total'  => $total,
			'recent' => $recent ?: [],
		]
	);
}

function toolverse_rest_update_profile(WP_REST_Request $req): WP_REST_Response {
	$body = $req->get_json_params();
	if (!is_array($body)) {
		$body = [];
	}
	$uid = get_current_user_id();
	if (isset($body['display_name'])) {
		wp_update_user(
			[
				'ID'           => $uid,
				'display_name' => sanitize_text_field($body['display_name']),
			]
		);
	}
	if (!empty($body['email']) && is_email($body['email'])) {
		wp_update_user(
			[
				'ID'         => $uid,
				'user_email' => sanitize_email($body['email']),
			]
		);
	}
	return new WP_REST_Response(['success' => true]);
}
