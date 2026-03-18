<?php
namespace yamarket_fusion\Module\models\addon\multistore;

use yamarket_fusion\Helpers\ArrayHelper;

class Multistore extends \Model {
	const setting_code = 'multistore';

	public function getProductsStoresByProductIds($product_id) {
		$store_ids = $this->getActiveStoresIds();
		$sql =
			"SELECT * FROM " . DB_PREFIX . "product_to_multistore
			WHERE product_id IN (" . implode(',', array_map('intval', $product_id)) . ")
				AND multistore_id IN (" . implode(',', array_map('intval', $store_ids)) . ")";

		$query = $this->db->query($sql);

		return ArrayHelper::index($query->rows, 'product_id', true);
	}

	public function getProductsOptionStoresByProductIds($product_id) {
		$store_ids = $this->getActiveStoresIds();
		$sql =
			"SELECT * FROM " . DB_PREFIX . "product_option_value_to_multistore
			WHERE product_id IN (" . implode(',', array_map('intval', $product_id)) . ")
				AND multistore_id IN (" . implode(',', array_map('intval', $store_ids)) . ")";

		$query = $this->db->query($sql);
		$output = [];

		foreach ($query->rows as $row) {
			$output[$row['product_id']][$row['product_option_value_id']][] = $row;
		}

		return $output;
	}

	public function getActiveStores($data = []) {
		$language_id = empty($data['language_id']) ? (int)$this->config->get('config_language_id') : (int)$data['language_id'];
		$sql = "SELECT m.*, md.name
				FROM " . DB_PREFIX . "multistore m
				LEFT JOIN " . DB_PREFIX . "multistore_description md
					ON (md.multistore_id = m.multistore_id AND language_id = {$language_id})
				WHERE m.status = 1
					AND m.multistore_id IN (
						SELECT multistore_id
						FROM " . DB_PREFIX . "multistore_to_store
						WHERE store_id = " . (int)$this->config->get('config_store_id') . "
					)";

		$query = $this->db->query($sql);
		return ArrayHelper::index($query->rows, 'multistore_id');
	}
	
	public function getActiveStoresIds() {
		$sql = "SELECT multistore_id
				FROM " . DB_PREFIX . "multistore
				WHERE status = 1
					AND multistore_id IN (
						SELECT multistore_id
						FROM " . DB_PREFIX . "multistore_to_store
						WHERE store_id = " . (int)$this->config->get('config_store_id') . "
					)";

		$query = $this->db->query($sql);
		return ArrayHelper::getColumn($query->rows, 'multistore_id');
	}

}