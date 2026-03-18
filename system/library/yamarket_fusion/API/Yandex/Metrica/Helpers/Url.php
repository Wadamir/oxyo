<?php
namespace yamarket_fusion\API\Yandex\Metrica\Helpers;

class Url {

	public static function encodeUtmContent($content) {
		$output = utf8_strtolower($content);
		$output = preg_replace('/\s+/', '_', $output);
		$output = preg_replace('/&amp;|&/', '-', $output);
		$output = preg_replace('/[^\da-zA-Z_-]/i', '', $output);
		
		return $output;
	}
}