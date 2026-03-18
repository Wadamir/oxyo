<?php
namespace yamarket_fusion\Module\models\catalog;

use yamarket_fusion\Helpers\ArrayHelper;

class Catalog extends \yamarket_fusion\Base\Model {
	const PRODUCTS_QUERY_LIMIT = 1000;

	public function getCategories($data = array()) {
		$language_id = !empty($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c
								LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
								LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
								WHERE cd.language_id = '{$language_id}'
									AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
									#AND c.status = '1' 
								ORDER BY c.sort_order, LCASE(cd.name)");

		return ArrayHelper::index($query->rows, 'category_id');
	}

	public function getImageSort($product_id, $image){
		$sql = "SELECT `sort_order` FROM " . DB_PREFIX . "product_image WHERE `product_id`='" . (int)$product_id . "' AND `image`='" . $this->db->escape($image) . "'";
		$query = $this->db->query($sql);
		$image_sort = $query->row;
		if (isset($query->row["sort_order"])){
			//echo '<pre>' , var_dump($query->row["sort_order"]) , '</pre>';
			return $query->row["sort_order"];
		}
	}

	//public function getProducts($allowed_categories = array(), $vendor_required = true, $manufacturers = array()) {
	public function getProducts($data = array()) {
		if (isset($data['manufacturer_id'])) {
			$manufacturers_ids = implode(',', array_map('intval', (array)$data['manufacturer_id']));
		}
		else {
			$manufacturers_ids = '';
		}

		$language_id = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');

		$allowed_categories = empty($data['category_id']) ? '' : (array)$data['category_id'];
		$vendor_required = !empty($data['vendor_required']);
		$customer_group_id = isset($data['customer_group_id']) ? (int)$data['customer_group_id'] : (int)$this->config->get('config_customer_group_id');

		//$fields = array('p.*', 'pd.name', 'pd.description', 'pd.marketplace_description', 'm.name AS manufacturer', 'p.price AS price', 'ps.price AS special', 'wcd.unit AS weight_unit');
		$fields = array('pg.*', 'p.*', 'pd.name', 'pd.description', 'm.name AS manufacturer', 'p.price AS price', 'ps.price AS special', 'wcd.unit AS weight_unit');

		$aliases = $this->getTableAliases(array(
			array('table' => 'product', 'prefix' => 'p.'),
			array('table' => 'product_special', 'prefix' => 'ps.'),
		));

		$fields = array_merge($fields, array_map(function($el) {return $el['text'];}, $aliases));

		// todo optimaze join product_special table

		$sql = "SELECT " . implode(',', $fields) . ",
					GROUP_CONCAT(DISTINCT CAST(pr.related_id AS CHAR) SEPARATOR ',') AS rel,
					GROUP_CONCAT(DISTINCT pi.image SEPARATOR ',') AS images,
					(SELECT GROUP_CONCAT(category_id) FROM " . DB_PREFIX . "product_to_category 
						WHERE product_id = p.product_id "
						. ($allowed_categories ? " AND category_id IN (" . implode(',', $allowed_categories) .")" : '') . ") AS categories
				FROM " . DB_PREFIX . "product p 
				JOIN " . DB_PREFIX . "product_to_category AS p2c ON (p.product_id = p2c.product_id)"
				. ($vendor_required ? '' : ' LEFT ') . " JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "product_go pg ON (pg.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				LEFT JOIN " . DB_PREFIX . "product_special ps ON ps.product_special_id =
					(SELECT product_special_id FROM " . DB_PREFIX . "product_special
						WHERE customer_group_id = '" . $customer_group_id . "' 
							AND date_start < NOW() AND (date_end = '0000-00-00' OR date_end > NOW()) 
							AND product_id = p.product_id
						ORDER BY priority DESC
						LIMIT 1
					)
				LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id) 
					AND wcd.language_id='" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "product_related pr ON (p.product_id = pr.product_id 
					AND p.date_available <= NOW() 
					AND p.status = '1')
				LEFT JOIN " . DB_PREFIX . "product_image pi ON (p.product_id = pi.product_id)
				WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				".($allowed_categories ? " AND p2c.category_id IN (" . $this->db->escape(implode(',', $allowed_categories)) . ")" : "")."
					AND pd.language_id = '{$language_id}'
					AND p.status = '1'"
				. ($manufacturers_ids ? " AND p.manufacturer_id IN ({$manufacturers_ids})" : '')
				. " GROUP BY p.product_id";

		if (isset($data['start']) || isset($data['limit'])) {
			$limit = (int)$data['limit'];
			$start = (int)$data['start'];
			
			if ($limit <= 0) $limit = self::PRODUCTS_QUERY_LIMIT;
			if ($start < 0) $start = 0;

			$sql .= " LIMIT {$start},{$limit}";
		}
		
		$query = $this->db->query($sql);

		return ArrayHelper::index($query->rows, 'product_id');
	}

	public function getProductsRelatedMarket($product_id, $data = array()) {
		$fields = array('p.price', 'p.product_id', 'p.tax_class_id', 'p.manufacturer_id', 'ps.price AS special');

		$aliases = $this->getTableAliases(array(
			array('table' => 'product', 'prefix' => 'p.'),
			array('table' => 'product_special', 'prefix' => 'ps.')
		));

		$fields = array_merge($fields, array_map(function($el) {return $el['text'];}, $aliases));

		$allowed_categories = empty($data['filter_allowed_categories']) ? '' : $this->escapeArray($data['filter_allowed_categories']);
		$customer_group_id = isset($data['customer_group_id']) ? (int)$data['customer_group_id'] : (int)$this->config->get('config_customer_group_id');
		
		$query = $this->db->query(
			"SELECT " . implode(',', $fields) . ",
				(SELECT GROUP_CONCAT(category_id) FROM " . DB_PREFIX . "product_to_category 
				WHERE product_id = p.product_id "
				. ($allowed_categories ? " AND category_id IN (" . implode(',', $allowed_categories) .")" : '') . ") AS categories
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_special ps ON ps.product_special_id =
					(SELECT product_special_id FROM " . DB_PREFIX . "product_special
						WHERE customer_group_id = '" . $customer_group_id . "' 
							AND date_start < NOW() AND (date_end = '0000-00-00' OR date_end > NOW()) 
							AND product_id = p.product_id
						ORDER BY priority DESC
						LIMIT 1
					)
			WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
				AND p.status = '1'"
				. ($product_id ? " AND p.product_id IN(" . implode(',', array_map('intval', (array)$product_id)) . ")" : '')
		);

		return ArrayHelper::index($query->rows, 'product_id');
	}

	public function getProductOptions($product_id, $data = array()) {
		// $lang = (int)$this->config->get('config_language_id');
		$lang = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');
		$options_ids = empty($data['option_ids']) ? '' : " AND pov.option_id IN (". implode(',', array_map('intval', $data['option_ids'])) . ")";

		$fields = array('pov.*', 'od.name AS option_name', 'ovd.name');

		$aliases = $this->getTableAliases(array(
			array('table' => 'product_option_value', 'prefix' => 'pov.')
		));

		$fields = array_merge($fields, array_map(function($el) {return $el['text'];}, $aliases));

		$sql = "SELECT " . implode(',', $fields) . "
			FROM " . DB_PREFIX . "product_option_value pov
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id)
			LEFT JOIN " . DB_PREFIX . "option_description od ON (od.option_id = pov.option_id) AND (od.language_id = '$lang')
			WHERE pov.product_id = '" . (int)$product_id . "' AND ovd.language_id = '{$lang}'" . $options_ids;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsAttributes($product_ids, $data = array()) {
		if (!$product_ids) return array();

		$pr_ids = array_map(function ($el) {return (int)$el;}, $product_ids);
		$language_id = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');

		$query = $this->db->query("SELECT pa.attribute_id, pa.product_id, pa.text, ad.name
			FROM " . DB_PREFIX . "product_attribute pa
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id)
			WHERE pa.product_id IN (" . implode(',', $pr_ids) . ")
				AND pa.language_id = '{$language_id}'
				AND ad.language_id = '{$language_id}'"
		);
		
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['product_id']][] = $result;
		}

		return $output;
	}
}