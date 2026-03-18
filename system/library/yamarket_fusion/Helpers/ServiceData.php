<?php
namespace yamarket_fusion\Helpers;

class ServiceData {
	protected $errors = array();
	protected $data;

	// maybe need set result status

	public function __construct($data = null) {
		if ($data !== null)
			$this->setData($data);
	}

	public function getErrorsString() {
		return implode(PHP_EOL, $this->errors);
	}

	public function getErrors() {
		return $this->errors;
	}

	public function addErrors($errors) {
		$this->errors = array_merge($this->errors, $errors);
	}
	
	public function addError($error) {
		$this->errors[] = $error;
	}

	public function setData($data) {
		$this->data = $data;
	}
	
	public function getData() {
		return $this->data;
	}
}