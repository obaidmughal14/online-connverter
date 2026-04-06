<?php
/**
 * Custom tables for Online Converter.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Create custom DB tables on theme activation.
 */
function toolverse_create_tables(): void {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset_collate = $wpdb->get_charset_collate();
	$p               = $wpdb->prefix;

	$sql_usage = "CREATE TABLE {$p}tool_usage (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  tool_slug varchar(100) NOT NULL,
  user_id bigint(20) unsigned DEFAULT 0,
  session_id varchar(64) DEFAULT NULL,
  used_at datetime DEFAULT CURRENT_TIMESTAMP,
  ip_address varchar(45) DEFAULT NULL,
  result_data longtext,
  PRIMARY KEY  (id),
  KEY idx_tool_slug (tool_slug),
  KEY idx_user_id (user_id)
) $charset_collate;";
	dbDelta($sql_usage);

	$sql_fav = "CREATE TABLE {$p}user_favorites (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL,
  tool_slug varchar(100) NOT NULL,
  saved_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
  UNIQUE KEY uniq_user_tool (user_id,tool_slug)
) $charset_collate;";
	dbDelta($sql_fav);

	$sql_saved = "CREATE TABLE {$p}user_saved_results (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL,
  tool_slug varchar(100) NOT NULL,
  result_data longtext,
  label varchar(255) DEFAULT NULL,
  saved_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
  KEY idx_user (user_id)
) $charset_collate;";
	dbDelta($sql_saved);

	$sql_settings = "CREATE TABLE {$p}tool_settings (
  tool_slug varchar(100) NOT NULL,
  is_enabled tinyint(1) DEFAULT 1,
  is_pro tinyint(1) DEFAULT 0,
  daily_limit int DEFAULT 0,
  api_key text,
  settings_json longtext,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (tool_slug)
) $charset_collate;";
	dbDelta($sql_settings);
}

add_action('after_switch_theme', 'toolverse_create_tables');
