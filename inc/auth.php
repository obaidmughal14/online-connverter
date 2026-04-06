<?php
/**
 * Authentication: shortcodes, AJAX, redirects.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_shortcode('toolverse_login_form', 'toolverse_render_login');
add_shortcode('toolverse_register_form', 'toolverse_render_register');

function toolverse_render_login(): string {
	ob_start();
	echo '<div class="toolverse-shortcode-login">';
	echo '<p>' . esc_html__('Use the full-screen login template for the best experience.', 'toolverse') . '</p>';
	echo '</div>';
	return (string) ob_get_clean();
}

function toolverse_render_register(): string {
	ob_start();
	echo '<div class="toolverse-shortcode-register">';
	echo '<p>' . esc_html__('Use the registration template for the full form.', 'toolverse') . '</p>';
	echo '</div>';
	return (string) ob_get_clean();
}

add_action('wp_ajax_nopriv_toolverse_login', 'toolverse_ajax_login');
add_action('wp_ajax_nopriv_toolverse_register', 'toolverse_ajax_register');

function toolverse_ajax_login(): void {
	check_ajax_referer('toolverse_auth_nonce', 'nonce');

	$username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
	$password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';

	if ('' === $username || '' === $password) {
		wp_send_json_error(['message' => __('Please fill in all fields.', 'toolverse')]);
	}

	$user = wp_signon(
		[
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => !empty($_POST['remember']),
		],
		is_ssl()
	);

	if (is_wp_error($user)) {
		wp_send_json_error(['message' => __('Invalid username or password.', 'toolverse')]);
	}

	wp_send_json_success(
		[
			'message'  => sprintf(
				/* translators: %s: display name */
				__('Welcome back, %s!', 'toolverse'),
				$user->display_name
			),
			'redirect' => home_url('/dashboard/'),
		]
	);
}

function toolverse_ajax_register(): void {
	check_ajax_referer('toolverse_auth_nonce', 'nonce');

	$username = isset($_POST['username']) ? sanitize_user(wp_unslash($_POST['username']), true) : '';
	$email    = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	$password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';
	$name     = isset($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : $username;

	if ('' === $username || '' === $email || '' === $password) {
		wp_send_json_error(['message' => __('All fields are required.', 'toolverse')]);
	}
	if (!is_email($email)) {
		wp_send_json_error(['message' => __('Please enter a valid email address.', 'toolverse')]);
	}
	if (strlen($password) < 8) {
		wp_send_json_error(['message' => __('Password must be at least 8 characters.', 'toolverse')]);
	}
	if (username_exists($username)) {
		wp_send_json_error(['message' => __('Username already taken.', 'toolverse')]);
	}
	if (email_exists($email)) {
		wp_send_json_error(['message' => __('An account with this email already exists.', 'toolverse')]);
	}

	$user_id = wp_create_user($username, $password, $email);
	if (is_wp_error($user_id)) {
		wp_send_json_error(['message' => $user_id->get_error_message()]);
	}

	wp_update_user(
		[
			'ID'           => $user_id,
			'display_name' => $name,
			'role'         => 'subscriber',
		]
	);

	wp_set_auth_cookie((int) $user_id, true);

	wp_mail(
		$email,
		__( 'Welcome to Online Converter!', 'toolverse' ),
		sprintf(
			"Hi %s,\n\nYour account is ready.\n\nStart using tools at %s\n\n— Devigon Tech",
			$name,
			home_url()
		)
	);

	wp_send_json_success(
		[
			'message'  => __('Account created! Redirecting...', 'toolverse'),
			'redirect' => home_url('/dashboard/'),
		]
	);
}

add_action(
	'template_redirect',
	function () {
		if (is_page_template('page-templates/page-login.php') && is_user_logged_in()) {
			wp_safe_redirect(home_url('/dashboard/'));
			exit;
		}
		if (is_page_template('page-templates/page-register.php') && is_user_logged_in()) {
			wp_safe_redirect(home_url('/dashboard/'));
			exit;
		}
		if (is_page_template('page-templates/page-dashboard.php') && !is_user_logged_in()) {
			$path   = '/dashboard/';
			if (!empty($_SERVER['REQUEST_URI'])) {
				$path = strtok(wp_unslash($_SERVER['REQUEST_URI']), '?');
			}
			$target = home_url($path);
			$login  = get_page_by_path('login');
			$url    = $login ? add_query_arg('redirect_to', rawurlencode($target), get_permalink($login)) : wp_login_url($target);
			wp_safe_redirect($url);
			exit;
		}
	}
);
