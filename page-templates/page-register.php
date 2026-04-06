<?php
/**
 * Template Name: Registration Page
 *
 * @package toolverse
 */

get_header();
?>
<div class="auth-page auth-page-simple" role="main">
	<div class="container narrow">
		<h1><?php esc_html_e('Create your account', 'toolverse'); ?></h1>
		<p><a href="<?php echo esc_url(home_url('/login/')); ?>"><?php esc_html_e('Already have an account? Sign in', 'toolverse'); ?></a></p>
		<?php
		// Reuse login template part: redirect users can open /login/ for tabbed UI.
		echo do_shortcode('[toolverse_register_form]');
		?>
	</div>
</div>
<?php
get_footer();
