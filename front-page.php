<?php
/**
 * Homepage.
 *
 * @package toolverse
 */

get_header();
?>

<section class="hero-section">
	<div class="container">
		<div class="hero-inner">
			<div class="hero-text">
				<div class="hero-badge">⚡ <?php esc_html_e('200+ Free Online Tools', 'toolverse'); ?></div>
				<h1 class="hero-title">
					<?php esc_html_e('Every Tool You', 'toolverse'); ?><br>
					<span class="gradient-text"><?php esc_html_e('Need in One Place', 'toolverse'); ?></span>
				</h1>
				<p class="hero-desc">
					<?php esc_html_e('Convert PDFs, edit images, write with AI, analyze SEO, and run 200+ tools — all free, all fast, all right here.', 'toolverse'); ?>
				</p>
				<div class="hero-cta">
					<a href="<?php echo esc_url(get_post_type_archive_link('tool')); ?>" class="btn-primary btn-xl"><?php esc_html_e('Explore All Tools →', 'toolverse'); ?></a>
					<a href="<?php echo esc_url(home_url('/register/')); ?>" class="btn-outline btn-xl"><?php esc_html_e('Create Free Account', 'toolverse'); ?></a>
				</div>
				<div class="hero-trust">
					<div class="trust-item">✅ <?php esc_html_e('No signup required for most tools', 'toolverse'); ?></div>
					<div class="trust-item">🔒 <?php esc_html_e('Privacy-first processing', 'toolverse'); ?></div>
					<div class="trust-item">⚡ <?php esc_html_e('Results in seconds', 'toolverse'); ?></div>
				</div>
			</div>
			<div class="hero-visual">
				<?php get_template_part('template-parts/hero', 'animation'); ?>
			</div>
		</div>
	</div>
</section>

<?php get_template_part('template-parts/stats', 'bar'); ?>

<section class="search-section">
	<div class="container">
		<div class="search-wrapper">
			<div class="search-bar-xl">
				<span class="search-icon-xl" aria-hidden="true">🔍</span>
				<input type="search" id="homepage-search" placeholder="<?php esc_attr_e("Search for a tool… e.g. 'PDF', 'compress', 'QR'", 'toolverse'); ?>" autocomplete="off" aria-label="<?php esc_attr_e('Search tools', 'toolverse'); ?>">
				<button type="button" class="search-btn-xl"><?php esc_html_e('Search', 'toolverse'); ?></button>
			</div>
			<div class="search-suggestions">
				<span class="suggestion-label"><?php esc_html_e('Popular:', 'toolverse'); ?></span>
				<a href="<?php echo esc_url(home_url('/tool/pdf-to-word/')); ?>">PDF to Word</a>
				<a href="<?php echo esc_url(home_url('/tool/image-compressor/')); ?>"><?php esc_html_e('Image Compressor', 'toolverse'); ?></a>
				<a href="<?php echo esc_url(home_url('/tool/bg-remover/')); ?>"><?php esc_html_e('Background Remover', 'toolverse'); ?></a>
				<a href="<?php echo esc_url(home_url('/tool/grammar-checker/')); ?>"><?php esc_html_e('Grammar Checker', 'toolverse'); ?></a>
				<a href="<?php echo esc_url(home_url('/tool/qr-generator/')); ?>"><?php esc_html_e('QR Generator', 'toolverse'); ?></a>
			</div>
			<div id="search-results-dropdown" class="search-dropdown" hidden></div>
		</div>
	</div>
</section>

<section class="categories-section">
	<div class="container">
		<div class="section-header-center">
			<h2><?php esc_html_e('Browse by Category', 'toolverse'); ?></h2>
			<p><?php esc_html_e('Find the right tool for your task', 'toolverse'); ?></p>
		</div>
		<div class="categories-grid">
			<?php
			$categories = [
				['icon' => '📄', 'name' => 'PDF Tools', 'count' => 25, 'slug' => 'pdf-tools', 'color' => '#E1785B'],
				['icon' => '🖼️', 'name' => 'Image Tools', 'count' => 30, 'slug' => 'image-tools', 'color' => '#B47CFD'],
				['icon' => '📝', 'name' => 'Text Tools', 'count' => 25, 'slug' => 'text-tools', 'color' => '#A7938E'],
				['icon' => '🔍', 'name' => 'SEO Tools', 'count' => 20, 'slug' => 'seo-tools', 'color' => '#C99BFC'],
				['icon' => '💻', 'name' => 'Developer Tools', 'count' => 30, 'slug' => 'developer-tools', 'color' => '#7B9ECC'],
				['icon' => '🔄', 'name' => 'Converter Tools', 'count' => 20, 'slug' => 'converter-tools', 'color' => '#5EB89A'],
				['icon' => '🤖', 'name' => 'AI Tools', 'count' => 20, 'slug' => 'ai-tools', 'color' => '#B47CFD'],
				['icon' => '🛠️', 'name' => 'Utility Tools', 'count' => 30, 'slug' => 'utility-tools', 'color' => '#A7938E'],
			];
			foreach ($categories as $cat) :
				$url = add_query_arg('category', $cat['slug'], get_post_type_archive_link('tool'));
				?>
				<a href="<?php echo esc_url($url); ?>" class="category-card">
					<div class="cat-icon" style="background: <?php echo esc_attr($cat['color']); ?>20; color: <?php echo esc_attr($cat['color']); ?>">
						<?php echo esc_html($cat['icon']); ?>
					</div>
					<div class="cat-info">
						<h3><?php echo esc_html($cat['name']); ?></h3>
						<span><?php echo esc_html((string) $cat['count']); ?> <?php esc_html_e('tools', 'toolverse'); ?></span>
					</div>
					<span class="cat-arrow" aria-hidden="true">→</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="featured-tools-section">
	<div class="container">
		<div class="section-header">
			<h2>🔥 <?php esc_html_e('Most Popular Tools', 'toolverse'); ?></h2>
			<a href="<?php echo esc_url(get_post_type_archive_link('tool')); ?>"><?php esc_html_e('View all →', 'toolverse'); ?></a>
		</div>
		<div class="tools-grid-home">
			<?php
			$popular = ['pdf-to-word', 'image-compressor', 'bg-remover', 'grammar-checker', 'qr-generator', 'json-formatter', 'word-counter', 'url-shortener', 'password-generator', 'color-picker', 'ai-writer', 'merge-pdf'];
			foreach ($popular as $slug) {
				$t = toolverse_get_tool_by_slug($slug);
				if ($t) {
					get_template_part('template-parts/tool', 'card', ['toolverse_tool' => $t]);
				}
			}
			?>
		</div>
	</div>
</section>

<section class="why-section">
	<div class="container">
		<div class="why-grid">
			<div class="why-text">
				<h2><?php echo esc_html( sprintf( __( 'Why choose %s?', 'toolverse' ), toolverse_brand_main() ) ); ?></h2>
				<div class="why-features">
					<div class="why-feature">
						<div class="why-icon" aria-hidden="true">⚡</div>
						<div>
							<h3><?php esc_html_e('Blazing Fast', 'toolverse'); ?></h3>
							<p><?php esc_html_e('Optimized flows deliver results quickly in the browser and via API.', 'toolverse'); ?></p>
						</div>
					</div>
					<div class="why-feature">
						<div class="why-icon" aria-hidden="true">🔒</div>
						<div>
							<h3><?php esc_html_e('Built for Privacy', 'toolverse'); ?></h3>
							<p><?php esc_html_e('We design flows to minimize retention; configure your stack for compliance.', 'toolverse'); ?></p>
						</div>
					</div>
					<div class="why-feature">
						<div class="why-icon" aria-hidden="true">🆓</div>
						<div>
							<h3><?php esc_html_e('Always Free Core', 'toolverse'); ?></h3>
							<p><?php esc_html_e('A huge free catalog with optional Pro tiers when you need them.', 'toolverse'); ?></p>
						</div>
					</div>
					<div class="why-feature">
						<div class="why-icon" aria-hidden="true">📱</div>
						<div>
							<h3><?php esc_html_e('Works Everywhere', 'toolverse'); ?></h3>
							<p><?php esc_html_e('Responsive layouts down to 320px with touch-friendly controls.', 'toolverse'); ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="why-visual" aria-hidden="true">
				<div class="why-blob"></div>
			</div>
		</div>
	</div>
</section>

<section class="blog-preview-section">
	<div class="container">
		<div class="section-header">
			<h2>📚 <?php esc_html_e('From the Blog', 'toolverse'); ?></h2>
			<a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Read more →', 'toolverse'); ?></a>
		</div>
		<div class="blog-grid-home">
			<?php
			$posts = get_posts(['numberposts' => 3, 'post_status' => 'publish']);
			foreach ($posts as $post) {
				setup_postdata($post);
				get_template_part('template-parts/blog', 'card');
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="cta-strip">
	<div class="container">
		<div class="cta-strip-inner">
			<div>
				<h2><?php esc_html_e('Ready to boost your productivity?', 'toolverse'); ?></h2>
				<p><?php esc_html_e('Create a free account and unlock your personal dashboard.', 'toolverse'); ?></p>
			</div>
			<a href="<?php echo esc_url(home_url('/register/')); ?>" class="btn-cta btn-xl"><?php esc_html_e('Get Started Free — No Credit Card', 'toolverse'); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
