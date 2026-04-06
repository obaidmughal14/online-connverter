<?php
/**
 * Template Name: User Dashboard
 *
 * @package toolverse
 */

if (!is_user_logged_in()) {
	wp_safe_redirect(home_url('/login/'));
	exit;
}
$user = wp_get_current_user();
get_header('dashboard');
?>
<main class="dashboard-layout" id="main-content">
	<aside class="dashboard-sidebar" id="dashboard-sidebar">
		<div class="sidebar-user">
			<div class="user-avatar">
				<?php echo get_avatar($user->ID, 64, '', '', ['class' => 'avatar-img']); ?>
				<div class="user-status online" aria-hidden="true"></div>
			</div>
			<div class="user-info">
				<strong><?php echo esc_html($user->display_name); ?></strong>
				<span><?php echo esc_html($user->user_email); ?></span>
			</div>
		</div>
		<nav class="sidebar-nav" aria-label="<?php esc_attr_e('Dashboard', 'toolverse'); ?>">
			<a href="#overview" class="nav-item active" data-panel="overview"><span class="nav-icon" aria-hidden="true">🏠</span> <?php esc_html_e('Overview', 'toolverse'); ?></a>
			<a href="#favorites" class="nav-item" data-panel="favorites"><span class="nav-icon" aria-hidden="true">⭐</span> <?php esc_html_e('Favorites', 'toolverse'); ?></a>
			<a href="#history" class="nav-item" data-panel="history"><span class="nav-icon" aria-hidden="true">🕐</span> <?php esc_html_e('Recent Activity', 'toolverse'); ?></a>
			<a href="#saved" class="nav-item" data-panel="saved"><span class="nav-icon" aria-hidden="true">💾</span> <?php esc_html_e('Saved Results', 'toolverse'); ?></a>
			<a href="#settings" class="nav-item" data-panel="settings"><span class="nav-icon" aria-hidden="true">⚙️</span> <?php esc_html_e('Settings', 'toolverse'); ?></a>
			<a href="#api" class="nav-item" data-panel="api"><span class="nav-icon" aria-hidden="true">🔑</span> <?php esc_html_e('API Access', 'toolverse'); ?></a>
		</nav>
		<div class="sidebar-usage">
			<div class="usage-header">
				<span><?php esc_html_e('Daily Usage', 'toolverse'); ?></span>
				<span class="usage-count" id="daily-count">0 / ∞</span>
			</div>
			<div class="usage-bar" aria-hidden="true"><div class="usage-fill" style="width:15%"></div></div>
			<p class="usage-note"><?php esc_html_e('Unlimited tools on free plan', 'toolverse'); ?></p>
		</div>
		<a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="sidebar-logout"><?php esc_html_e('Sign Out', 'toolverse'); ?></a>
	</aside>
	<div class="dashboard-main">
		<header class="dashboard-topbar">
			<button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-expanded="true" aria-controls="dashboard-sidebar">☰</button>
			<h1 class="dash-greeting">
				<?php
				$hour     = (int) current_time('H');
				$greeting = $hour < 12 ? __('Good morning', 'toolverse') : ($hour < 17 ? __('Good afternoon', 'toolverse') : __('Good evening', 'toolverse'));
				echo esc_html($greeting . ', ' . ($user->first_name ?: $user->display_name) . '!');
				?>
			</h1>
			<div class="topbar-actions">
				<?php toolverse_dark_mode_toggle(); ?>
				<a href="<?php echo esc_url(get_post_type_archive_link('tool')); ?>" class="btn-primary btn-sm"><?php esc_html_e('Browse Tools', 'toolverse'); ?></a>
			</div>
		</header>
		<div id="panel-overview" class="dash-panel active">
			<div class="stats-grid">
				<div class="stat-card">
					<div class="stat-icon" style="background:rgba(180,124,253,.15);color:#B47CFD" aria-hidden="true">🔧</div>
					<div class="stat-info">
						<div class="stat-number" id="total-tools-used">0</div>
						<div class="stat-label"><?php esc_html_e('Tools Used', 'toolverse'); ?></div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-icon" style="background:rgba(225,120,91,.15);color:#E1785B" aria-hidden="true">⭐</div>
					<div class="stat-info">
						<div class="stat-number" id="total-favorites">0</div>
						<div class="stat-label"><?php esc_html_e('Favorites', 'toolverse'); ?></div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-icon" style="background:rgba(167,147,142,.22);color:#A7938E" aria-hidden="true">💾</div>
					<div class="stat-info">
						<div class="stat-number" id="total-saved">0</div>
						<div class="stat-label"><?php esc_html_e('Saved Results', 'toolverse'); ?></div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-icon" style="background:rgba(201,155,252,.18);color:#C99BFC" aria-hidden="true">📅</div>
					<div class="stat-info">
						<div class="stat-number"><?php echo esc_html(human_time_diff(strtotime($user->user_registered), current_time('timestamp'))); ?></div>
						<div class="stat-label"><?php esc_html_e('Member Since', 'toolverse'); ?></div>
					</div>
				</div>
			</div>
			<section class="dash-section">
				<div class="section-header">
					<h2>⚡ <?php esc_html_e('Quick Access', 'toolverse'); ?></h2>
					<a href="<?php echo esc_url(get_post_type_archive_link('tool')); ?>"><?php esc_html_e('Browse all →', 'toolverse'); ?></a>
				</div>
				<div class="quick-tools-grid" id="quick-tools"></div>
			</section>
			<section class="dash-section">
				<div class="section-header"><h2>🕐 <?php esc_html_e('Recent Activity', 'toolverse'); ?></h2></div>
				<div class="activity-list" id="recent-activity"></div>
			</section>
		</div>
		<div id="panel-favorites" class="dash-panel" hidden>
			<h2><?php esc_html_e('Favorites', 'toolverse'); ?></h2>
			<ul id="favorites-list" class="dash-list"></ul>
		</div>
		<div id="panel-history" class="dash-panel" hidden>
			<h2><?php esc_html_e('Recent Activity', 'toolverse'); ?></h2>
			<div id="history-list" class="activity-list"></div>
		</div>
		<div id="panel-saved" class="dash-panel" hidden>
			<h2><?php esc_html_e('Saved Results', 'toolverse'); ?></h2>
			<p class="description"><?php esc_html_e('Save flows can be wired to user_saved_results.', 'toolverse'); ?></p>
		</div>
		<div id="panel-settings" class="dash-panel" hidden>
			<h2><?php esc_html_e('Account Settings', 'toolverse'); ?></h2>
			<div class="settings-grid">
				<div class="settings-card">
					<h3><?php esc_html_e('Profile', 'toolverse'); ?></h3>
					<div class="form-group">
						<label for="set-display-name"><?php esc_html_e('Display Name', 'toolverse'); ?></label>
						<input type="text" id="set-display-name" value="<?php echo esc_attr($user->display_name); ?>">
					</div>
					<div class="form-group">
						<label for="set-email"><?php esc_html_e('Email', 'toolverse'); ?></label>
						<input type="email" id="set-email" value="<?php echo esc_attr($user->user_email); ?>">
					</div>
					<button type="button" class="btn-primary" id="save-profile"><?php esc_html_e('Save Profile', 'toolverse'); ?></button>
				</div>
				<div class="settings-card">
					<h3><?php esc_html_e('Change Password', 'toolverse'); ?></h3>
					<div class="form-group">
						<label for="set-current-pass"><?php esc_html_e('Current Password', 'toolverse'); ?></label>
						<input type="password" id="set-current-pass" autocomplete="current-password">
					</div>
					<div class="form-group">
						<label for="set-new-pass"><?php esc_html_e('New Password', 'toolverse'); ?></label>
						<input type="password" id="set-new-pass" autocomplete="new-password">
					</div>
					<div class="form-group">
						<label for="set-confirm-pass"><?php esc_html_e('Confirm Password', 'toolverse'); ?></label>
						<input type="password" id="set-confirm-pass" autocomplete="new-password">
					</div>
					<button type="button" class="btn-primary" id="change-password"><?php esc_html_e('Update Password', 'toolverse'); ?></button>
				</div>
			</div>
		</div>
		<div id="panel-api" class="dash-panel" hidden>
			<h2><?php esc_html_e('API Access', 'toolverse'); ?></h2>
			<p><?php esc_html_e('Use the REST API with your application password or cookie auth.', 'toolverse'); ?></p>
			<code class="api-snippet"><?php echo esc_html(rest_url('toolverse/v1/tools')); ?></code>
		</div>
	</div>
</main>
<?php
get_footer('dashboard');
