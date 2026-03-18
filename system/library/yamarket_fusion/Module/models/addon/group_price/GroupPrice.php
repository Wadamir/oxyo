<?php
namespace yamarket_fusion\Module\models\addon\group_price;

use yamarket_fusion\Base\Model;
use yamarket_fusion\Helpers\ArrayHelper;

class GroupPrice extends Model {
	const code = 'group_price';
	const MARKUP_TYPE_PERCENT = 1;
	const MARKUP_TYPE_SUM = 2;
	const BASE_CATEGORY_MARKUP_ID = 0;
	
	private $module_category_data = array();
	private $module_manufacturer_data = array();
	private $module_setting;
	private $module_status;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->loadModel('setting');

		$setting = $this->model_setting->getSetting();
		$this->module_setting = isset($setting[0]['addon_group_price']) ? $setting[0]['addon_group_price'] : array();

		if ($this->getStatus()) {
			$this->module_category_data = $this->getModuleCategoryData();
			$this->module_manufacturer_data = $this->getModuleManufacturerData();
		}
	}

	protected function getModuleCategoryData() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_price");
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['customer_group_id']][$result['category_id']] = $result;
		}
		
		return $output;
	}
	
	protected function getModuleManufacturerData() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_group_price");
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['customer_group_id']][$result['manufacturer_id']] = $result;
		}
		
		return $output;
	}

	public function getStatus() {
		if ($this->module_status === null) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE type = 'module' AND code = '" . self::code . "'");
			$this->module_status = (bool)$query->num_rows;
		}
		
		return $this->module_status;
	}

	public function calcProductPrice($price, $product, $customer_group_id) {
		$output = $price;
		
		if ($this->getStatus()) {
			if (!empty($this->module_category_data[$customer_group_id])) {
				$data = $this->module_category_data[$customer_group_id];

				if (!empty($this->module_setting['markup_base_price'])) {
					$output = $this->calcMarkup($output, $data[self::BASE_CATEGORY_MARKUP_ID]);
				}
				
				if (!empty($this->module_setting['markup_category']) && !empty($data[$product['category_id']])) {
					$output = $this->calcMarkup($output, $data[$product['category_id']]);
				}
			}
			
			if (!empty($this->module_setting['markup_manufacturer'])) {
				$output = $this->calcManufacturerMarkup($output, $product['manufacturer_id'], $customer_group_id);
			}
		}

		return $output;
	}

	/*
	private function calcCategoryMarkup($price, $product_category_id, $customer_group_id) {
		$category_level_type = $this->module_setting['category_level_type'];
		$output = $price;
		
		if (!empty($this->module_category_data[$customer_group_id])) {
			$data = $this->module_category_data[$customer_group_id];
			
			if ($category_level_type == 'base_price' && !empty($data[self::BASE_CATEGORY_MARKUP_ID])) {
				$output = $this->calcMarkup($price, $data[self::BASE_CATEGORY_MARKUP_ID]);
			}
			else if ($category_level_type == 'category' && !empty($data[$product_category_id])) {
				$output = $this->calcMarkup($price, $data[$product_category_id]);
			}
			else if ($category_level_type == 'all') {
				if (!empty($data[self::BASE_CATEGORY_MARKUP_ID]))
					$output = $this->calcMarkup($output, $data[self::BASE_CATEGORY_MARKUP_ID]);
				
				if (!empty($data[$product_category_id]))
					$output = $this->calcMarkup($output, $data[$product_category_id]);
			}
		}

		return $output;
	}
	*/

	private function calcManufacturerMarkup($price, $manufacturer_id, $customer_group_id) {
		$output = $price;
		
		if (!empty($this->module_manufacturer_data[$customer_group_id][$manufacturer_id])) {
			$output = $this->calcMarkup($price, $this->module_manufacturer_data[$customer_group_id][$manufacturer_id]);
		}

		return $output;
	}

	private function calcMarkup($price, $data) {
		if ($data['type'] == self::MARKUP_TYPE_PERCENT) {
			$price += $price * $data['price'] / 100;
		}
		else if ($data['type'] == self::MARKUP_TYPE_SUM) {
			$price += $data['price'];
		}

		return $price;
	}
}