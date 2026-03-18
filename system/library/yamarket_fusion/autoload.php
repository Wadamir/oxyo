<?php
namespace yamarket_fusion;

class Autoload {
	public static function register() {
		spl_autoload_register(__CLASS__ . '::autoload');
	}

	public static function autoload($class) {
		$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', $class) . '.php';

		if (is_file($file)) {
			include_once(modification($file));

			return true;
		} else {
			return false;
		}
	}
}