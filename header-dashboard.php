<?php
/**
 * Minimal header for dashboard shell.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class('toolverse-dashboard'); ?>>
<?php wp_body_open(); ?>
