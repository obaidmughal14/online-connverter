<?php
/**
 * Blog card for loops.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<article <?php post_class('blog-card-home'); ?>>
	<a href="<?php the_permalink(); ?>" class="blog-card-thumb">
		<?php
		if (has_post_thumbnail()) {
			the_post_thumbnail(
				'blog-card',
				[
					'loading' => 'lazy',
					'alt'     => esc_attr(get_the_title()),
				]
			);
		}
		?>
	</a>
	<div class="blog-card-body">
		<div class="blog-card-meta">
			<span class="blog-category"><?php echo get_the_category_list(', '); ?></span>
			<span class="blog-date"><?php echo esc_html(get_the_date()); ?></span>
		</div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php the_excerpt(); ?></p>
		<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More →', 'toolverse'); ?></a>
	</div>
</article>
