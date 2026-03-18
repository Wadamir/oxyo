<?php
namespace yamarket_fusion\Module\models;

class Event extends \Model {
	protected $model;

	public function __construct($registry) {
		parent::__construct($registry);

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->load->model('setting/event');
			$this->model = $this->model_setting_event;
		}
		else if (version_compare(VERSION, '2.1.0', '>=')) {
			$this->load->model('extension/event');
			$this->model = $this->model_extension_event;
		}
	}

	// 2.1
	//	addEvent ($code, $trigger, $action)
	//	deleteEvent ($code)

	// 2.2 
	//	addEvent ($code, $trigger, $action)
	//	deleteEvent ($code)
	// 2.3
	//	addEvent ($code, $trigger, $action, $status = 1)
	//	deleteEvent ($code)
	// 3.0
	//	addEvent ($code, $trigger, $action, $status = 1, $sort_order = 0)
	//	deleteEventByCode ($code)

	public function addEvent($code, $trigger, $action, $status = 1, $sort_order = 0) {
		if (version_compare(VERSION, '3.0.0', '>=')) {
			$result = $this->model->addEvent($code, $trigger, $action, $status, $sort_order);
		}
		else if (version_compare(VERSION, '2.3.0', '>=')) {
			$result = $this->model->addEvent($code, $trigger, $action, $status);
		}
		else {
			$result = $this->model->addEvent($code, $trigger, $action);
		}

		return $result;
	}

	public function deleteEventByCode($code) {
		if (version_compare(VERSION, '3.0.0', '>=')) {
			$result = $this->model->deleteEventByCode($code);
		}
		else {
			$result = $this->model->deleteEvent($code);
		}

		return $result;
	}
}