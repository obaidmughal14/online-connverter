<?php
/**
 * Main blog fallback.
 *
 * @package toolverse
 */

get_header();
?>

<div class="container blog-index">
	<?php if (have_posts()) : ?>
		<div class="blog-grid">
			<?php
			while (have_posts()) :
				the_post();
				get_template_part('template-parts/blog', 'archive-card');
			endwhile;
			?>
		</div>
		<div class="pagination">
			<?php the_posts_pagination(); ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e('No posts found.', 'toolverse'); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
