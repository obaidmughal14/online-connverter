<?php
/**
 * Template Name: Login Page
 *
 * @package toolverse
 */

get_header();
?>

<div class="auth-page" role="main">
	<div class="auth-split">
		<div class="auth-branding">
			<div class="auth-brand-inner">
				<div class="auth-logo">
					<span>⚡ <?php echo esc_html( toolverse_brand_main() ); ?></span>
					<span class="auth-logo-credit"><?php echo esc_html( toolverse_brand_credit() ); ?></span>
				</div>
				<h1><?php esc_html_e('200+ Free Tools', 'toolverse'); ?><br><?php esc_html_e('at Your Fingertips', 'toolverse'); ?></h1>
				<p><?php esc_html_e('Convert PDFs, edit images, extract text, analyze SEO — all in one place.', 'toolverse'); ?></p>
				<div class="auth-tools-preview">
					<div class="tool-pill">📄 PDF</div>
					<div class="tool-pill">🖼️ Image</div>
					<div class="tool-pill">📝 Text</div>
					<div class="tool-pill">🔍 SEO</div>
					<div class="tool-pill">💻 Dev</div>
					<div class="tool-pill">🤖 AI</div>
				</div>
				<div class="auth-stats">
					<div class="stat"><span>2M+</span><?php esc_html_e('Users', 'toolverse'); ?></div>
					<div class="stat"><span>200+</span><?php esc_html_e('Tools', 'toolverse'); ?></div>
					<div class="stat"><span>∞</span><?php esc_html_e('Free', 'toolverse'); ?></div>
				</div>
			</div>
		</div>
		<div class="auth-form-panel">
			<div class="auth-form-inner">
				<div class="auth-tabs" role="tablist">
					<button type="button" class="auth-tab active" data-tab="login"><?php esc_html_e('Sign In', 'toolverse'); ?></button>
					<button type="button" class="auth-tab" data-tab="register"><?php esc_html_e('Create Account', 'toolverse'); ?></button>
				</div>
				<div id="tab-login" class="auth-tab-content active">
					<h2><?php esc_html_e('Welcome back!', 'toolverse'); ?></h2>
					<p class="auth-subtitle"><?php esc_html_e('Sign in to access your dashboard and saved tools.', 'toolverse'); ?></p>
					<div id="login-message" class="auth-message" hidden></div>
					<div class="form-group">
						<label for="login-username"><?php esc_html_e('Username or Email', 'toolverse'); ?></label>
						<div class="input-wrapper">
							<span class="input-icon" aria-hidden="true">👤</span>
							<input type="text" id="login-username" name="log" autocomplete="username" required>
						</div>
					</div>
					<div class="form-group">
						<label for="login-password">
							<?php esc_html_e('Password', 'toolverse'); ?>
							<a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-link"><?php esc_html_e('Forgot password?', 'toolverse'); ?></a>
						</label>
						<div class="input-wrapper">
							<span class="input-icon" aria-hidden="true">🔒</span>
							<input type="password" id="login-password" name="pwd" autocomplete="current-password" required>
							<button type="button" class="toggle-password" aria-label="<?php esc_attr_e('Show password', 'toolverse'); ?>">👁️</button>
						</div>
					</div>
					<label class="checkbox-label">
						<input type="checkbox" id="login-remember" name="rememberme" value="forever">
						<span><?php esc_html_e('Keep me signed in for 30 days', 'toolverse'); ?></span>
					</label>
					<button type="button" id="login-btn" class="btn-primary btn-full btn-xl">
						<span class="btn-text"><?php esc_html_e('Sign In', 'toolverse'); ?></span>
						<span class="btn-loader hidden" aria-hidden="true">⏳</span>
					</button>
				</div>
				<div id="tab-register" class="auth-tab-content">
					<h2><?php esc_html_e('Create your account', 'toolverse'); ?></h2>
					<p class="auth-subtitle"><?php esc_html_e('Join and access your personal dashboard.', 'toolverse'); ?></p>
					<div id="register-message" class="auth-message" hidden></div>
					<div class="form-row">
						<div class="form-group">
							<label for="reg-name"><?php esc_html_e('Full Name', 'toolverse'); ?></label>
							<div class="input-wrapper">
								<span class="input-icon" aria-hidden="true">✨</span>
								<input type="text" id="reg-name" autocomplete="name" required>
							</div>
						</div>
						<div class="form-group">
							<label for="reg-username"><?php esc_html_e('Username', 'toolverse'); ?></label>
							<div class="input-wrapper">
								<span class="input-icon" aria-hidden="true">@</span>
								<input type="text" id="reg-username" autocomplete="username" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="reg-email"><?php esc_html_e('Email Address', 'toolverse'); ?></label>
						<div class="input-wrapper">
							<span class="input-icon" aria-hidden="true">📧</span>
							<input type="email" id="reg-email" autocomplete="email" required>
						</div>
					</div>
					<div class="form-group">
						<label for="reg-password"><?php esc_html_e('Password', 'toolverse'); ?></label>
						<div class="input-wrapper">
							<span class="input-icon" aria-hidden="true">🔒</span>
							<input type="password" id="reg-password" autocomplete="new-password" required>
							<button type="button" class="toggle-password" aria-label="<?php esc_attr_e('Show password', 'toolverse'); ?>">👁️</button>
						</div>
						<div class="password-strength" id="password-strength" aria-live="polite"></div>
					</div>
					<label class="checkbox-label">
						<input type="checkbox" id="reg-terms" required>
						<span><?php esc_html_e('I agree to the Terms and Privacy Policy.', 'toolverse'); ?></span>
					</label>
					<button type="button" id="register-btn" class="btn-primary btn-full btn-xl">
						<span class="btn-text"><?php esc_html_e('Create Free Account', 'toolverse'); ?></span>
						<span class="btn-loader hidden" aria-hidden="true">⏳</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
