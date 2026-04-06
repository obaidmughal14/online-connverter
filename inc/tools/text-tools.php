<?php
/**
 * Text tool server-side helpers (extend via filters).
 *
 * @package toolverse
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter(
	'toolverse_tool_process_word-counter',
	static function ($result, $input) {
		$text = wp_strip_all_tags((string) $input);
		return [
			'words'       => str_word_count($text),
			'characters'  => strlen($text),
			'lines'       => substr_count($text, "\n") + ($text !== '' ? 1 : 0),
		];
	},
	10,
	2
);

add_filter(
	'toolverse_tool_process_text-case-converter',
	static function ($result, $input, $body = []) {
		$mode = isset($body['mode']) ? sanitize_key($body['mode']) : 'upper';
		$t    = (string) $input;
		switch ($mode) {
			case 'lower':
				$out = strtolower($t);
				break;
			case 'title':
				$out = ucwords(strtolower($t));
				break;
			case 'camel':
				$out = lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-z0-9]+/i', ' ', $t))));
				break;
			default:
				$out = strtoupper($t);
		}
		return ['text' => $out];
	},
	10,
	3
);
