<?php
namespace yamarket_fusion\Module\models\addon\currency_plus;

class CurrencyPlus extends \yamarket_fusion\Base\Model {
	const code = 'currency_plus';

	private $module_status;
	private $module_setting;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->loadModel('setting');

		$setting = $this->model_setting->getSetting();
		$this->module_setting = isset($setting[0]['addon_currency_plus']) ? $setting[0]['addon_currency_plus'] : array();
	}

	public function getProductField($field_name) {
		$name = $field_name;

		/*
		switch ($field_name) {
			case 'price':
				if (!empty($this->module_setting['base_price'])) {
					$name = 'base_price'; break;
				}
			//case 'base_currency_code':
			//	$alias = $this->getAlias('product', 'base_currency_code');
			//	$name = $alias['alias']; break;
			case 'special':
				if (!empty($this->module_setting['base_price'])) {
					$alias = $this->getAlias('product_special', 'price');
					$name = $alias['alias']; break;
				}
			case 'option_price':
				$alias = $this->getAlias('product_option_value', 'price');
				$name = $alias['alias']; break;
			default:
				$name = $field_name;
		}
		*/

		if ($field_name == 'price' && !empty($this->module_setting['base_price'])) {
			$name = 'base_price';
		}
		else if ($field_name == 'special' && !empty($this->module_setting['special_base'])) {
			$alias = $this->getAlias('product_special', 'price');
			$name = $alias['alias'];
		}else if ($field_name == 'option_price' && !empty($this->module_setting['option_base_price'])) {
			$alias = $this->getAlias('product_option_value', 'price');
			$name = $alias['alias'];
		}
		// todo + discount
		else if ($field_name == 'base_currency_code' && empty($this->module_setting['base_currency_code'])) {
			$name = null;
		}
		else {
			$name = $field_name;
		}

		return $name;
	}

	public function getStatus() {
		if ($this->module_status === null) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE type = 'module' AND code = '" . self::code . "'");
			$this->module_status = (bool)$query->num_rows;
		}
		
		return $this->module_status;
	}

	public function getAlias($table, $field, $field_prefix = '') {
		//$alias_field = '';
		//$alias_name = '';
		//$sql_text = '';
		$output = array();

		if ($field == 'price' || $field == 'option_price') {
			$alias_field = 'base_price';
		}
		else {
			$alias_field = $field;
		}

		//$field_setting = !empty($this->module_setting[$alias_field]);

		//if ($field_setting) {
			$alias_name = '';
			
			switch ($table) {
				case 'product':
					if ($alias_field == 'base_price' && !empty($this->module_setting['base_price'])) {
						$alias_name = 'base_price';
						$sql_text = 'base_price';
					}
					else if (!empty($this->module_setting['base_currency_code'])) {
						$alias_name = 'base_currency_code';
						$sql_text = 'base_currency_code';
					}
					break;
				case 'product_special':
					if (!empty($this->module_setting['special_base'])) {
						$alias_name = 'special_base';
						$sql_text = 'base_price AS special_base';
					}
					break;
				case 'product_discount':
					if (!empty($this->module_setting['discount_base'])) {
						$alias_name = 'discount_base';
						$sql_text = 'base_price AS discount_base';
					}
					break;
				case 'product_option_value':
					if (!empty($this->module_setting['option_base_price'])) {
						$alias_name = 'option_base_price';
						$sql_text = 'base_price AS option_base_price';
					}
					break;
				//default:
				//	$alias_name = $field;
			}

			if ($alias_name) {
				$output['field'] = $field_prefix . $alias_field;
				$output['alias'] = $alias_name;
				$output['text'] = $field_prefix . $sql_text;
			}
		//}

		return $output;
	}

	public function getTableAliases($table, $table_prefix = '') {
		$aliases = array();

		switch($table) {
			case 'product_special':
			case 'product_discount':
			case 'product_option_value':
				$aliases[] = $this->getAlias($table, 'price', $table_prefix);
				break;
			case 'product':
				$aliases[] = $this->getAlias($table, 'price', $table_prefix);
				$aliases[] = $this->getAlias($table, 'base_currency_code', $table_prefix);
				//$aliases[] = array(
				//	'field' => $table_prefix . 'base_currency_code',
				//	'alias' => 'base_currency_code',
				//	'text' => $table_prefix . 'base_currency_code'
				//);
				break;
		}

		return array_filter($aliases, function($el) {return !empty($el);});
	}
}