<?php
/**
 * Theme footer.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
</main>
<footer class="site-footer">
	<div class="container footer-inner">
		<div class="footer-brand">
			<a class="logo logo--footer" href="<?php echo esc_url(home_url('/')); ?>">
				<span class="logo-mark" aria-hidden="true">⚡</span>
				<span class="logo-text">
					<span class="logo-name"><?php echo esc_html( toolverse_brand_main() ); ?></span>
					<span class="logo-credit"><?php echo esc_html( toolverse_brand_credit() ); ?></span>
				</span>
			</a>
			<p><?php bloginfo('description'); ?></p>
		</div>
		<nav class="footer-nav" aria-label="<?php esc_attr_e('Footer', 'toolverse'); ?>">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => '__return_false',
				]
			);
			?>
		</nav>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<p>© <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'toolverse'); ?></p>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
