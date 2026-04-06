<?php
/**
 * Single tool template.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

$tool_slug = get_post_field('post_name', get_the_ID());
$tool_data = toolverse_get_tool_by_slug((string) $tool_slug);
$settings  = toolverse_get_tool_settings((string) $tool_slug);

if (!$tool_data) {
	$tool_data = [
		'name'            => get_the_title(),
		'icon'            => '🔧',
		'category'        => '',
		'category_slug'   => '',
		'color_start'     => '#B47CFD',
		'color_end'       => '#E1785B',
		'description'     => get_the_excerpt(),
	];
}

if ((int) ($settings['is_enabled'] ?? 1) === 0) {
	wp_safe_redirect(home_url('/tools/?disabled=1'));
	exit;
}

get_header();
?>

<div class="tool-page-wrap">
	<div class="tool-header" style="background: linear-gradient(135deg, <?php echo esc_attr($tool_data['color_start']); ?>, <?php echo esc_attr($tool_data['color_end']); ?>)">
		<div class="container">
			<nav class="breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'toolverse'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'toolverse'); ?></a>
				<span class="bc-sep">→</span>
				<a href="<?php echo esc_url(get_post_type_archive_link('tool')); ?>"><?php esc_html_e('Tools', 'toolverse'); ?></a>
				<?php if (!empty($tool_data['category_slug'])) : ?>
					<span class="bc-sep">→</span>
					<a href="<?php echo esc_url(home_url('/tools/?category=' . rawurlencode($tool_data['category_slug']))); ?>"><?php echo esc_html($tool_data['category']); ?></a>
				<?php endif; ?>
				<span class="bc-sep">→</span>
				<span><?php the_title(); ?></span>
			</nav>
			<div class="tool-header-inner">
				<div class="tool-icon-xl" aria-hidden="true"><?php echo esc_html($tool_data['icon']); ?></div>
				<div class="tool-header-text">
					<h1><?php the_title(); ?></h1>
					<p class="tool-description"><?php echo esc_html(get_the_excerpt()); ?></p>
					<div class="tool-meta-tags">
						<span class="meta-tag free">✅ <?php esc_html_e('Free', 'toolverse'); ?></span>
						<span class="meta-tag fast">⚡ <?php esc_html_e('No signup required', 'toolverse'); ?></span>
						<span class="meta-tag secure">🔒 <?php esc_html_e('Privacy-first', 'toolverse'); ?></span>
					</div>
				</div>
				<div class="tool-actions-header">
					<button type="button" class="btn-favorite" data-favorite="<?php echo esc_attr($tool_slug); ?>" title="<?php esc_attr_e('Add to favorites', 'toolverse'); ?>">
						⭐ <span class="fav-text"><?php esc_html_e('Favorite', 'toolverse'); ?></span>
					</button>
					<button type="button" class="btn-share" data-share-url="<?php echo esc_url(get_permalink()); ?>" title="<?php esc_attr_e('Share this tool', 'toolverse'); ?>">
						🔗 <?php esc_html_e('Share', 'toolverse'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="tool-content-wrap">
		<div class="container">
			<div class="tool-layout">
				<div class="tool-main">
					<div class="tool-interface" id="tool-interface-<?php echo esc_attr($tool_slug); ?>">
						<?php do_action('toolverse_render_tool', $tool_slug); ?>
					</div>
				</div>
				<aside class="tool-sidebar">
					<div class="sidebar-card">
						<h3>📖 <?php esc_html_e('How to Use', 'toolverse'); ?></h3>
						<ol class="how-to-steps">
							<?php
							$steps = get_post_meta(get_the_ID(), '_tool_steps', true);
							if (is_string($steps) && $steps !== '') {
								foreach (preg_split('/\r\n|\r|\n/', $steps) as $step) {
									$step = trim($step);
									if ($step !== '') {
										echo '<li>' . esc_html($step) . '</li>';
									}
								}
							}
							?>
						</ol>
					</div>
					<div class="sidebar-card">
						<h3>🔗 <?php esc_html_e('Related Tools', 'toolverse'); ?></h3>
						<div class="related-tools-list">
							<?php
							$terms = wp_get_post_terms(get_the_ID(), 'tool_category', ['fields' => 'ids']);
							$related = get_posts(
								[
									'post_type'      => 'tool',
									'posts_per_page' => 5,
									'post__not_in'   => [get_the_ID()],
									'tax_query'      => !empty($terms) && !is_wp_error($terms) ? [
										[
											'taxonomy' => 'tool_category',
											'field'    => 'term_id',
											'terms'    => $terms,
										],
									] : [],
								]
							);
							foreach ($related as $rel_tool) {
								echo '<a href="' . esc_url(get_permalink($rel_tool)) . '" class="related-tool-link">' . esc_html(get_the_title($rel_tool)) . '</a>';
							}
							?>
						</div>
					</div>
					<div class="sidebar-card ad-slot" id="sidebar-ad">
						<?php echo wp_kses_post(get_option('toolverse_sidebar_ad', '')); ?>
					</div>
				</aside>
			</div>
			<div class="tool-description-full">
				<h2><?php
					/* translators: %s: tool title */
					printf(esc_html__('About %s', 'toolverse'), esc_html(get_the_title()));
				?></h2>
				<?php the_content(); ?>
			</div>
			<div class="tool-faq">
				<h2><?php esc_html_e('Frequently Asked Questions', 'toolverse'); ?></h2>
				<?php
				$faqs = get_post_meta(get_the_ID(), '_tool_faqs', true);
				if (is_string($faqs) && $faqs !== '') {
					$faq_list = json_decode($faqs, true);
					if (is_array($faq_list)) {
						foreach ($faq_list as $faq) {
							if (empty($faq['q']) || empty($faq['a'])) {
								continue;
							}
							?>
							<div class="faq-item" itemscope itemtype="https://schema.org/Question">
								<button type="button" class="faq-question" data-faq-toggle aria-expanded="false">
									<span itemprop="name"><?php echo esc_html($faq['q']); ?></span>
									<span class="faq-arrow" aria-hidden="true">▼</span>
								</button>
								<div class="faq-answer" itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer" hidden>
									<p itemprop="text"><?php echo esc_html($faq['a']); ?></p>
								</div>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
