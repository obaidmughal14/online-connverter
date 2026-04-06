<?php
/**
 * Theme header.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="ai-content-summary" content="Online Converter by Devigon Tech — 200+ free tools: PDF, images, text, SEO, developers, and more.">
	<meta name="format-detection" content="telephone=no">
	<link rel="alternate" type="application/rss+xml" title="<?php echo esc_attr(get_bloginfo('name')); ?> Blog" href="<?php echo esc_url(get_bloginfo('rss2_url')); ?>">
	<link rel="sitemap" type="application/xml" href="<?php echo esc_url(home_url('/wp-sitemap.xml')); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e('Skip to content', 'toolverse'); ?></a>
<header class="site-nav" role="banner">
	<div class="container nav-inner">
		<a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
			<span class="logo-mark" aria-hidden="true">⚡</span>
			<span class="logo-text">
				<span class="logo-name"><?php echo esc_html( toolverse_brand_main() ); ?></span>
				<span class="logo-credit"><?php echo esc_html( toolverse_brand_credit() ); ?></span>
			</span>
		</a>
		<button type="button" class="hamburger" id="hamburger" aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e('Menu', 'toolverse'); ?>">
			<span></span><span></span><span></span>
		</button>
		<nav class="nav-links" aria-label="<?php esc_attr_e('Primary', 'toolverse'); ?>">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-menu',
					'fallback_cb'    => 'toolverse_fallback_menu',
				]
			);
			?>
		</nav>
		<div class="nav-actions">
			<?php toolverse_dark_mode_toggle(); ?>
			<?php if (is_user_logged_in()) : ?>
				<a class="btn-primary btn-sm" href="<?php echo esc_url(home_url('/dashboard/')); ?>"><?php esc_html_e('Dashboard', 'toolverse'); ?></a>
			<?php else : ?>
				<a class="btn-outline btn-sm" href="<?php echo esc_url(home_url('/login/')); ?>"><?php esc_html_e('Sign In', 'toolverse'); ?></a>
				<a class="btn-primary btn-sm" href="<?php echo esc_url(home_url('/register/')); ?>"><?php esc_html_e('Sign Up', 'toolverse'); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="mobile-nav" id="mobile-nav" aria-hidden="true">
		<?php
		wp_nav_menu(
			[
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mobile-nav-menu',
				'fallback_cb'    => 'toolverse_fallback_menu',
			]
		);
		?>
	</div>
</header>
<main id="main-content" class="site-main">
