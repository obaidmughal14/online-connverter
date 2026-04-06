<?php
/**
 * Dashboard AJAX handlers.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('wp_ajax_toolverse_save_profile', 'toolverse_ajax_save_profile');
function toolverse_ajax_save_profile(): void {
	check_ajax_referer('toolverse_auth_nonce', 'nonce');
	if (!is_user_logged_in()) {
		wp_send_json_error(['message' => __('Not logged in.', 'toolverse')], 401);
	}
	$uid = get_current_user_id();
	$display = isset($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : '';
	$email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	if ($display) {
		wp_update_user(['ID' => $uid, 'display_name' => $display]);
	}
	if ($email && is_email($email)) {
		wp_update_user(['ID' => $uid, 'user_email' => $email]);
	}
	wp_send_json_success(['message' => __('Profile saved.', 'toolverse')]);
}

add_action('wp_ajax_toolverse_change_password', 'toolverse_ajax_change_password');
function toolverse_ajax_change_password(): void {
	check_ajax_referer('toolverse_auth_nonce', 'nonce');
	if (!is_user_logged_in()) {
		wp_send_json_error(['message' => __('Not logged in.', 'toolverse')], 401);
	}
	$uid      = get_current_user_id();
	$current  = isset($_POST['current']) ? wp_unslash($_POST['current']) : '';
	$new      = isset($_POST['new']) ? wp_unslash($_POST['new']) : '';
	$confirm  = isset($_POST['confirm']) ? wp_unslash($_POST['confirm']) : '';
	$user     = get_userdata($uid);
	if (!$user || !wp_check_password($current, $user->user_pass, $uid)) {
		wp_send_json_error(['message' => __('Current password is incorrect.', 'toolverse')]);
	}
	if (strlen($new) < 8) {
		wp_send_json_error(['message' => __('New password must be at least 8 characters.', 'toolverse')]);
	}
	if ($new !== $confirm) {
		wp_send_json_error(['message' => __('Passwords do not match.', 'toolverse')]);
	}
	wp_set_password($new, $uid);
	wp_set_auth_cookie($uid, true);
	wp_send_json_success(['message' => __('Password updated.', 'toolverse')]);
}
