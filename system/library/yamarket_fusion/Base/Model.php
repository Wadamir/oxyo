<?php
namespace yamarket_fusion\Base;

class Model extends \Model {
	//protected $model_path;
	
	const module_table_prefix = 'yamf_';
	const cache_prefix = 'yamarket_fusion_';

	protected $table_name, $profile_id;

	//public function __construct($registry, $setting_profile_id) {
	//	parent::__construct($registry);
	//	$this->profile_id = $setting_profile_id;
	//}

	/*
	public function __get($key) {
		if ($key == 'table_name') {
			return DB_PREFIX . self::module_table_prefix . self::table_name;
		}
		else {
			return parent::__get($key);
		}
	}
	*/

	protected function tableName() {
		// return DB_PREFIX . self::module_table_prefix . $this->table_name;
		return self::prefix($this->table_name);
	}

	public static function prefix($table) {
		return DB_PREFIX . self::module_table_prefix . $table;
	}

	public function loadModel($name) {
		if (isset(ClassMap::$models['aliases'][$name])) {
			$class = ClassMap::$models['aliases'][$name];
			$model_name = 'model_' . str_replace('/', '_', $name);
			
			if (!$this->registry->has($model_name))
				$this->registry->set($model_name, new $class($this->registry));
		}
		else {
			$this->load->model($name);
		}
	}

	/**
	 * @param $tables array()
	 */
	protected function getTableAliases($tables) {
		$aliases = array();
		$addons = array('currency_plus\\CurrencyPlus');

		foreach ($addons as $addon) {
			$class = '\\yamarket_fusion\\Module\\models\\addon\\' . $addon;
			$addon_model = new $class($this->registry);
			
			if ($addon_model->getStatus()) {
				foreach ($tables as $table) {
					$aliases = array_merge($aliases, $addon_model->getTableAliases($table['table'], $table['prefix']));
				}
			}
		}

		return $aliases;
	}

	public function escapeArray($array, $escape_fn = null) {
		$db = $this->db;
		$output = $array;
		array_walk_recursive($output, function($el) use($db, $escape_fn) {
			if (is_callable($escape_fn)) {
				return $escape_fn($el);
			}
			else {
				return $db->escape($el);
			}
		});
		return $output;
	}

	/*
	public function __call($method, $args) {
		// compability for event system oc 2.3.x
		if (version_compare(VERSION, '2.3.0', '>=')) {
			if (method_exists(//...)) {
				$this->event->trigger('model/' . $this->model_path . '/before', $args);
				
				// to do check result;

				// do validate args
				$result = $this->{$method}();

				// to do validate events

				$this->event->trigger('model/' . $this->model_path . '/after', $args);
			}
		}
	}
	*/
}