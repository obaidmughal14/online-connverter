<?php
/**
 * User nav fragment (dashboard).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!is_user_logged_in()) {
	return;
}
$user = wp_get_current_user();
?>
<div class="user-nav-chip">
	<?php echo get_avatar($user->ID, 32, '', '', ['class' => 'user-nav-avatar']); ?>
	<span class="user-nav-name"><?php echo esc_html($user->display_name); ?></span>
</div>
