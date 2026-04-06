<?php
/**
 * Hero visual (inline SVG).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="hero-orbit" aria-hidden="true">
	<svg viewBox="0 0 400 360" class="hero-svg" xmlns="http://www.w3.org/2000/svg" role="img">
		<title><?php esc_html_e('Abstract tools illustration', 'toolverse'); ?></title>
		<defs>
			<linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%">
				<stop offset="0%" stop-color="#B47CFD"/>
				<stop offset="100%" stop-color="#E1785B"/>
			</linearGradient>
		</defs>
		<rect x="40" y="40" width="320" height="280" rx="28" fill="var(--bg-card)" stroke="var(--border-strong)" stroke-width="2"/>
		<circle cx="200" cy="120" r="48" fill="url(#g1)" opacity="0.9" class="hero-pulse"/>
		<rect x="90" y="200" width="220" height="16" rx="8" fill="var(--border)"/>
		<rect x="90" y="232" width="160" height="16" rx="8" fill="var(--border)"/>
		<rect x="90" y="264" width="200" height="16" rx="8" fill="var(--border)"/>
		<g class="hero-float">
			<circle cx="320" cy="80" r="14" fill="#E1785B"/>
			<circle cx="90" cy="100" r="10" fill="#B47CFD"/>
			<circle cx="330" cy="240" r="12" fill="#A7938E"/>
		</g>
	</svg>
</div>
