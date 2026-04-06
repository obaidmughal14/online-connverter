<?php
/**
 * Blog card for archive / index.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<article <?php post_class('blog-card'); ?>>
	<a href="<?php the_permalink(); ?>" class="blog-card-image">
		<?php
		if (has_post_thumbnail()) {
			the_post_thumbnail('blog-card', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]);
		}
		?>
		<span class="blog-card-category"><?php echo get_the_category_list(', '); ?></span>
	</a>
	<div class="blog-card-content">
		<div class="blog-card-meta">
			<span><?php esc_html_e('By', 'toolverse'); ?> <?php the_author(); ?></span>
			<span>·</span>
			<span><?php echo esc_html(get_the_date()); ?></span>
		</div>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php the_excerpt(); ?></p>
		<a href="<?php the_permalink(); ?>" class="blog-read-more"><?php esc_html_e('Read Article →', 'toolverse'); ?></a>
	</div>
</article>
