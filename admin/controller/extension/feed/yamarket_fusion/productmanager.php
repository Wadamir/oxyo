<?php
\yamarket_fusion\Autoload::register();

class ControllerExtensionFeedYamarketFusionProductmanager extends yamarket_fusion\Base\Controller {
	private $fields = array('price', 'tax_class', 'specials', 'specials_bulk');
	private $PM_PATH;

	const event_code = 'yamarket_fusion__productmanager_productupdate';

	public function __construct($registry) {
		parent::__construct($registry);
		$this->app = 'admin';
		$PM_PATH = 'extension/module';
		
		if (version_compare(VERSION, '2.3.0', '<')) {
			$PM_PATH = 'module';
		}

		$this->PM_PATH = 'admin/controller/' . $PM_PATH . '/productmanager';
	}

	public function install() {
		$this->loadModel('event');

		$this->model_event->addEvent(
			self::event_code,
			$this->PM_PATH . '/updateproduct/before',
			$this->MODULE_PATH . '/yamarket_fusion/productmanager/before_updateproduct'
		);
		$this->model_event->addEvent(
			self::event_code,
			$this->PM_PATH . '/updateproduct/after',
			$this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_after'
		);
		$this->model_event->addEvent(
			self::event_code,
			$this->PM_PATH . '/updateproductbulk/before',
			$this->MODULE_PATH . '/yamarket_fusion/productmanager/before_updateproductbulk'
		);
		$this->model_event->addEvent(
			self::event_code,
			$this->PM_PATH . '/updateproductbulk/after',
			$this->MODULE_PATH . '/ymarket_fusion/productmanager/after_updateproductbulk'
		);
	}

	public function uninstall() {
		$this->loadModel('event');
		$this->model_event->deleteEventByCode(self::event_code);
	}

	private function formatArgs() {
		$data = array();

		if ($this->request->post['pdata'] == 'price') {
			$data['price'] = $this->request->post['pvalue'];
		}
		
		if ($this->request->post['pdata'] == 'tax_class') {
			$data['tax_class_id'] = $this->request->post['pvalue'];
		}

		if (isset($this->request->post['product_special']))
			$data['product_special'] = $this->request->post['product_special'];

		//todo
		//if (isset($this->request->post['product_option']))
		//	$data['product_option'] = $this->request->post['product_special'];

		return $data;
	}

	public function before_updateproduct() {
		if (!in_array($this->request->post['pdata'], $this->fields))
			return;

		if ($this->request->post['pdata'] == 'specials') {
			register_shutdown_function(array($this, 'after_updateproduct_shutdown'));
		}

		$this->load->controller($this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_before', array(
			$this->request->post['pid'],
			$this->formatArgs()
		));
	}

	public function before_updateproductbulk() {
		if (!in_array($this->request->post['pdata'], $this->fields)) 
			return;	

		$raw_ids = $this->request->post['pdata'] == 'specials_bulk' ? $this->request->post['product_ids'] : $this->request->post['pid'];

		$ids = explode(',', $raw_ids);
		$products_data = array();

		foreach ($ids as $id) {
			$products_data[$id] = array($id, $this->formatArgs());
		}

		$this->session->data[self::event_code] = $products_data;
	}

	public function after_updateproductbulk() {
		if (empty($this->session->data[self::event_code]))
			return;

		foreach ($this->session->data[self::event_code] as $product_data) {
			$this->load->controller($this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_before', $product_data);
			$this->load->controller($this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_after');
		}

		unset($this->session->data[self::event_code]);
	}

	public function after_updateproduct_shutdown() {
		$this->load->controller($this->MODULE_PATH . '/yamodule_fusion/price_autoupdate_after');
	}
}