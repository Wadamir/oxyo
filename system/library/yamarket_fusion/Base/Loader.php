<?php
namespace yamarket_fusion\Base;

class Loader {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function model($nane) {
		if (isset(ClassMap::$models[$this->app][$name])) {
			$class = ClassMap::$models[$this->app][$name];
			$model_name = 'model_' . str_replace('/', '_', $name);
			
			if (!$this->registry->has($model_name))
				$this->registry->set($model_name, new $class($this->registry));
		}
		else {
			$this->registry->load->model($name);
		}
	}
}