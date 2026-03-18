<?php
namespace yamarket_fusion\Helpers;

class ArrayHelper {
	
	public static function index($array, $key, $group_by_key = false) {
		$output = array();

		foreach ($array as $val) {
			$group_by_key ? $output[$val[$key]][] = $val : $output[$val[$key]] = $val;
		}

		return $output;
	}

	public static function map($array, $needle_key, $save_keys = true) {
		if ($save_keys) {
			$output = array_map(function ($el) use($needle_key) {return $el[$needle_key];}, $array);
		}
		else {
			$output = array();
			foreach ($array as $key => $value) {
				$output[] = isset($value[$needle_key]) ? $value[$needle_key] : null;
			}
		}

		return $output;
	}

	public static function getColumn($array, $index) {
		$keys = explode('.', $index);
		$output = array();

		foreach ($array as $ar_key => $data) {
			foreach($keys as $key) {
				if (is_array($data) && array_key_exists($key, $data)) {
					$data = $data[$key];
				}
				else {
					$data = null;
					break;
				}
			}

			if ($data !== null) $output[] = $data;
		}

		return $output;
	}

	// features
	/*
	public static function array_unique_recursive($array) {
		return array_intersect_key($array, array_unique(array_map('json_encode', $array)));
	}
	*/
}