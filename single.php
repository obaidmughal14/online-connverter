<?php
/**
 * Single blog post.
 *
 * @package toolverse
 */

get_header();
?>

<div class="container single-post-wrap">
	<?php
	while (have_posts()) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="entry-header">
				<h1><?php the_title(); ?></h1>
				<div class="entry-meta">
					<?php echo esc_html(get_the_date()); ?> · <?php the_author(); ?>
				</div>
			</header>
			<?php if (has_post_thumbnail()) : ?>
				<div class="entry-thumb">
					<?php the_post_thumbnail('blog-featured', ['loading' => 'eager', 'alt' => esc_attr(get_the_title())]); ?>
				</div>
			<?php endif; ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
