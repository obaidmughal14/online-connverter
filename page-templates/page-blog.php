<?php
/**
 * Template Name: Blog
 *
 * @package toolverse
 */

get_header();
?>

<div class="blog-page">
	<div class="blog-hero">
		<div class="container">
			<h1>📚 <?php echo esc_html( sprintf( __( '%s Blog', 'toolverse' ), toolverse_brand_main() ) ); ?></h1>
			<p><?php esc_html_e('Tips, guides, and tutorials to help you work smarter', 'toolverse'); ?></p>
		</div>
	</div>
	<div class="container">
		<div class="blog-layout">
			<div class="blog-categories-filter">
				<?php $blog_page_url = get_queried_object() ? get_permalink(get_queried_object()) : home_url('/blog/'); ?>
				<a href="<?php echo esc_url($blog_page_url); ?>" class="cat-pill active"><?php esc_html_e('All Posts', 'toolverse'); ?></a>
				<?php foreach (get_categories(['hide_empty' => true]) as $cat) : ?>
					<a href="<?php echo esc_url(get_category_link($cat)); ?>" class="cat-pill"><?php echo esc_html($cat->name); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="blog-grid">
				<?php
				$paged = max(1, (int) get_query_var('paged'));
				if ($paged < 1) {
					$paged = 1;
				}
				$query = new WP_Query(
					[
						'post_type'      => 'post',
						'posts_per_page' => 9,
						'paged'          => $paged,
					]
				);
				while ($query->have_posts()) :
					$query->the_post();
					get_template_part('template-parts/blog', 'archive-card');
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<div class="blog-pagination">
				<?php
				echo wp_kses_post(
					paginate_links(
						[
							'total'   => $query->max_num_pages,
							'current' => $paged,
						]
					)
				);
				?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
