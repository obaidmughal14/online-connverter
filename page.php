<?php
/**
 * Generic page.
 *
 * @package toolverse
 */

get_header();
?>

<div class="container" style="padding:48px 0;max-width:800px">
	<?php
	while (have_posts()) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
