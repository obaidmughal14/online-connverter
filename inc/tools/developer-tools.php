<?php
/**
 * Developer tools — register server processors here.
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter(
	'toolverse_tool_process_json-formatter',
	static function ($result, $input) {
		$decoded = json_decode((string) $input, true);
		if (JSON_ERROR_NONE !== json_last_error()) {
			return ['error' => json_last_error_msg()];
		}
		return ['formatted' => wp_json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)];
	},
	10,
	2
);
