<?php
namespace yamarket_fusion\Module\models\addon\podarki;

use yamarket_fusion\Helpers\ArrayHelper;

class Podarki extends \Model {

	public function getPromos($filter_data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "podarki_image";
		$language_id = isset($filter_data['language_id']) ? (int)$filter_data['language_id'] : (int)$this->config->get('config_language_id');
		$implode = array();

		if (isset($filter_data['product_id'])) {
			if (is_array($filter_data['product_id'])) {
				$implode[] = "product_id IN(" . implode(',', array_map('intval', $filter_data['product_id'])) . ")";
			}
			else {
				$implode[] = "product_id = " . (int)$filter_data['product_id'];
			}
		}

		if ($implode)
			$sql .= " WHERE " . implode(' AND ', $implode);

		$promos = $this->db->query($sql);

		$sql_products = "SELECT * FROM " . DB_PREFIX . "podarki";

		if ($implode)
			$sql_products .= " WHERE " . implode(' AND ', $implode);

		$query_products = $this->db->query($sql_products);
		$product_relation = array();
		$product_ids = array();

		foreach ($query_products->rows as $result) {
			$product_relation[$result['product_id']][] = $result['podarok_id'];
			$product_ids[] = $result['podarok_id'];
		}

		$product_ids = array_unique($product_ids);

		$products = $this->db->query("SELECT p.product_id, p.image, pd.name FROM " . DB_PREFIX . "product p
										LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id
										LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON p.product_id = p2s.product_id
										WHERE pd.language_id = {$language_id}
											AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
											AND p.status = 1
											AND p.product_id IN(" . implode(',', $product_ids) . ")"
								);

		$products = ArrayHelper::index($products->rows, 'product_id');

		$output = array();

		foreach ($promos->rows as $promo) {
			$promo_products = array();

			if (isset($product_relation[$promo['product_id']])) {
				foreach ($product_relation[$promo['product_id']] as $rel) {
					$promo_products[] = $products[$rel];
				}
			}
			
			$output[] = array(
				'product_id' => $promo['product_id'],
				'image' => $promo['image_podarok'],
				'minimum_quntity' => $promo['radiopod'] == 'quantpod' ? $promo['input_quant_prod'] : 0,
				'name' => $promo['input_botname'],
				'description' => $promo['input_desc'],
				'products' => $promo_products,
			);
		}

		return $output;
	}

}