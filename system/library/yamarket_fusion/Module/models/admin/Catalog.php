<?php
namespace yamarket_fusion\Module\models\admin;

use yamarket_fusion\Helpers\ArrayHelper;

class Catalog extends \yamarket_fusion\Base\Model {

	public function getProduct($product_id) {
		$fields = array('p.*', 'ps.price AS special');

		$aliases = $this->getTableAliases(array(
			array('table' => 'product', 'prefix' => 'p.'),
			array('table' => 'product_special', 'prefix' => 'ps.'),
		));

		$fields = array_merge($fields, array_map(function($el) {return $el['text'];}, $aliases));
		
		$query = $this->db->query("SELECT " . implode(',', $fields) . ",
									(SELECT GROUP_CONCAT(category_id) FROM " . DB_PREFIX . "product_to_category 
										WHERE product_id = p.product_id
									) AS categories
									FROM " . DB_PREFIX . "product AS p
									LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ps.date_start < NOW() AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
									LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
									WHERE p.product_id = '" . (int)$product_id . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'"
								);
		return $query->row;
	}

	public function getProductOptions($product_id, $option_ids = array(), $data = array()) {
		// $lang = (int)$this->config->get('config_language_id');
		$lang = !empty($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');
		$options_ids = empty($option_ids) ? '' : " AND pov.option_id IN (". implode(',', array_map('intval', $option_ids)) . ")";
		
		$fields = array('pov.*', 'od.name AS option_name', 'ovd.name', 'ov.image');

		$aliases = $this->getTableAliases(array(
			array('table' => 'product_option_value', 'prefix' => 'pov.')
		));

		$fields = array_merge($fields, array_map(function($el) {return $el['text'];}, $aliases));

		$query = $this->db->query("SELECT " . implode(',', $fields) . "
			FROM " . DB_PREFIX . "product_option_value pov
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id)
			LEFT JOIN " . DB_PREFIX . "option_description od ON (od.option_id = pov.option_id) AND (od.language_id = '$lang')
			LEFT JOIN " . DB_PREFIX . "option_value ov ON (ov.option_value_id = pov.option_value_id)
			WHERE pov.product_id = '" . (int)$product_id . "' AND ovd.language_id = '{$lang}'" . $options_ids);
		
		return $query->rows;
	}

	public function getOptionValuesByValueId($id, $data = array()) {
		// $language_id = (int)$this->config->get('config_language_id');
		$language_id = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');
		
		$sql = "SELECT o_v.option_value_id, o_d.name AS option_name, o_v_d.name AS option_value FROM " . DB_PREFIX . "option_value AS o_v
				LEFT JOIN " . DB_PREFIX . "option_value_description AS o_v_d ON o_v_d.option_value_id = o_v.option_value_id
				LEFT JOIN " . DB_PREFIX . "option_description AS o_d ON o_d.option_id = o_v.option_id
				WHERE o_d.language_id = {$language_id} AND o_v_d.language_id = " . $language_id;

		if (is_array($id)) {
			$sql .= " AND o_v.option_value_id IN(" . implode(',', array_map('intval', $id)) . ")";
		}
		else {
			$sql .= " AND o_v.option_value_id = " . (int)$id;
		}

		$query = $this->db->query($sql);
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['option_value_id']] = $result;
		}

		return $output;
	}

	public function getAttributesByGroups() {
		$query = $this->db->query("SELECT a_g.*, a_g_d.name FROM " . DB_PREFIX . "attribute_group AS a_g 
									LEFT JOIN " . DB_PREFIX . "attribute_group_description AS a_g_d ON (a_g_d.attribute_group_id = a_g.attribute_group_id) 
										AND (a_g_d.language_id = " . (int)$this->config->get('config_language_id') . ")
									ORDER BY a_g.sort_order ASC"
								);

		$groups = array();

		foreach ($query->rows as $result) {
			$groups[$result['attribute_group_id']] = array(
				'name' => $result['name'],
				'attributes' => array()
			);
		}

		$query = $this->db->query("SELECT a.*, a_d.name FROM " . DB_PREFIX . "attribute AS a
									LEFT JOIN " . DB_PREFIX . "attribute_description AS a_d ON (a_d.attribute_id = a.attribute_id) 
									AND (a_d.language_id = " . (int)$this->config->get('config_language_id') . ")"
								);

		foreach ($query->rows as $result) {
			$groups[$result['attribute_group_id']]['attributes'][] = $result;
		}

		return $groups;
	}

	public function getProductsAttributes($product_ids, $data = array()) {
		if (!$product_ids) return array();

		$pr_ids = array_map(function ($el) {return (int)$el;}, $product_ids);
		$lang = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');

		$query = $this->db->query("SELECT pa.attribute_id, pa.product_id, pa.text, ad.name
			FROM " . DB_PREFIX . "product_attribute pa
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id)
			WHERE pa.product_id IN (" . implode(',', $pr_ids) . ")
				AND pa.language_id = '{$lang}'
				AND ad.language_id = '{$lang}'"
		);
		
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['product_id']][] = $result;
		}

		return $output;
	}

	public function getCategories() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c
									LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
									WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
									ORDER BY c.sort_order"
								);

		return ArrayHelper::index($query->rows, 'category_id');
	}
}