<?php
/**
 * Template Name: Super Admin Panel (front-end)
 *
 * @package toolverse
 */

if (!current_user_can('manage_options')) {
	wp_safe_redirect(home_url('/'));
	exit;
}

get_header();
?>

<div class="container admin-fe-wrap" style="padding:3rem 0">
	<h1><?php echo esc_html( '⚡ ' . toolverse_brand_full() . ' — ' . __( 'Admin', 'toolverse' ) ); ?></h1>
	<p><?php echo esc_html( sprintf( __( 'Full controls live in WordPress admin under %s.', 'toolverse' ), toolverse_brand_main() ) ); ?></p>
	<p><a class="btn-primary" href="<?php echo esc_url(admin_url('admin.php?page=toolverse-admin')); ?>"><?php esc_html_e('Open Control Center', 'toolverse'); ?></a></p>
	<?php
	global $wpdb;
	$table = $wpdb->prefix . 'tool_usage';
	$cnt   = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
	?>
	<p><?php printf(esc_html__('Logged tool runs (all time): %s', 'toolverse'), esc_html(number_format_i18n($cnt))); ?></p>
</div>

<?php
get_footer();
