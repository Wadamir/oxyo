<?php
namespace yamarket_fusion\Base;

/**
 * @ class Template analog opencart/system/library/template/Template
 * rewriting because in render method cant't change root template directory
 **/
final class Template {
	protected $data = array();

	public function __construct($dir_template = '') {
		if (strlen($dir_template)) {
			$this->dir_template = $dir_template;
		}
		else {
			$this->dir_template = DIR_SYSTEM . 'library/yamarket_fusion/Module/views/';
		}
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function render($template) {
		$file = $this->dir_template . $template . '.tpl';

		if (is_file($file)) {
			extract($this->data);

			ob_start();

			require($file);

			return ob_get_clean();
		}

		throw new \Exception('Error: Could not load template ' . $file . '!');
		exit();
	}	
}