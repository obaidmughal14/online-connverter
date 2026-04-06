<?php
/**
 * Security helpers and hardening.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter(
	'login_url',
	function ($login_url, $redirect, $force_reauth) {
		$page = get_page_by_path('login');
		if ($page) {
			$url = get_permalink($page);
			if ($redirect) {
				$url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
			}
			return $url;
		}
		return $login_url;
	},
	10,
	3
);

add_action(
	'template_redirect',
	function () {
		if (is_author()) {
			wp_safe_redirect(home_url('/'), 301);
			exit;
		}
	}
);

add_action(
	'wp_login_failed',
	function () {
		$ip  = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0';
		$key = 'login_fail_' . md5($ip);
		$attempts = (int) get_transient($key);
		set_transient($key, $attempts + 1, 15 * MINUTE_IN_SECONDS);
	}
);

add_filter('authenticate', 'toolverse_block_bruteforce_authenticate', 30, 3);
function toolverse_block_bruteforce_authenticate($user, $username, $password) {
	if (is_wp_error($user) || $user instanceof WP_User) {
		return $user;
	}
	$ip  = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0';
	$key = 'login_fail_' . md5($ip);
	if ((int) get_transient($key) >= 5) {
		return new WP_Error('too_many_attempts', __('Too many login attempts. Please try again in 15 minutes.', 'toolverse'));
	}
	return $user;
}

add_action(
	'send_headers',
	function () {
		if (is_admin()) {
			return;
		}
		header('X-Content-Type-Options: nosniff');
		header('X-Frame-Options: SAMEORIGIN');
		header('X-XSS-Protection: 1; mode=block');
		header('Referrer-Policy: strict-origin-when-cross-origin');
		header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
	},
	1
);

add_filter('xmlrpc_enabled', '__return_false');

/**
 * Sanitize tool input by type.
 *
 * @param mixed  $input Raw input.
 * @param string $type  text|html|url|int|float|email.
 * @return mixed
 */
function toolverse_sanitize_tool_input($input, string $type = 'text') {
	switch ($type) {
		case 'html':
			return is_string($input) ? wp_kses_post($input) : '';
		case 'url':
			return is_string($input) ? esc_url_raw($input) : '';
		case 'int':
			return absint($input);
		case 'float':
			return is_numeric($input) ? (float) $input : 0.0;
		case 'email':
			return is_string($input) ? sanitize_email($input) : '';
		default:
			return is_string($input) ? sanitize_textarea_field($input) : '';
	}
}

/**
 * Verify AJAX nonce or send JSON error.
 */
function toolverse_verify_nonce(string $action = 'toolverse_nonce'): void {
	if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), $action)) {
		wp_send_json_error(['message' => __('Security check failed.', 'toolverse')], 403);
	}
}

add_filter(
	'upload_mimes',
	function ($mimes) {
		$allowed = [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
			'webp'         => 'image/webp',
			'pdf'          => 'application/pdf',
			'svg'          => 'image/svg+xml',
		];
		return array_merge($mimes, $allowed);
	}
);
