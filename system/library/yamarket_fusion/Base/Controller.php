<?php
namespace yamarket_fusion\Base;

class Controller extends \Controller {
	protected $MODULE_PATH, $token, $token_name, $app;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->token_name = 'user_token';
		$this->MODULE_PATH = 'extension/feed';

		if (version_compare(VERSION, '3.0.0', '<')) {
			$this->token_name = 'token';
		}
		
		if (version_compare(VERSION, '2.3.0', '<')) {
			$this->MODULE_PATH = 'feed';
		}

		if (isset($this->session->data[$this->token_name]))
			$this->token = $this->session->data[$this->token_name];

		//$this->setSetting();
	}

	//protected abstract function setSetting();

	public function loadModel($name) {
		/*
		if (isset(ClassMap::$models[$this->app][$name])) {
			$class = ClassMap::$models[$this->app][$name];
			$model_name = 'model_' . str_replace('/', '_', $name);
			
			if (!$this->registry->has($model_name))
				$this->registry->set($model_name, new $class($this->registry));
		}
		else {
			$this->load->model($name);
		}
		*/

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
	 * render the view file, based on opencart loader->view
	 */
	public function loadView($route, $data = array()) {
		// Template contents. Not the output!
		$template = '';
		$trigger = $route;

		//array of args to var for compability 2.1
		$args = array(&$route, &$data, &$template);

		$result = $this->registry->get('event')->trigger('view/' . $trigger . '/before', $args);

		if ($result && !$result instanceof Exception) {
			$output = $result;
		} else {
			$template = new Template();
			
			foreach ($data as $key => $value) {
				$template->set($key, $value);
			}

			$output = $template->render($this->app . '/' . $route);
		}

		$result = $this->registry->get('event')->trigger('view/' . $trigger . '/after', $args);
		
		if ($result && !$result instanceof Exception) {
			$output = $result;
		}
		
		return $output;
	}
}