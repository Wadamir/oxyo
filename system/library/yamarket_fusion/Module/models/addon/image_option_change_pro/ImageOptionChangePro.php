<?php
namespace yamarket_fusion\Module\models\addon\image_option_change_pro;

use yamarket_fusion\Helpers\ArrayHelper;

class ImageOptionChangePro extends \yamarket_fusion\Base\Model {
	const setting_code = 'image_option_pro';
	
	private $store = array();

	public function storeProductsOptionsImagesPro($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "poip_option_image WHERE product_id";

		if (is_array($product_id)) {
			$sql .= " IN (" . implode(',', array_map('intval', $product_id)) . ")";
		}
		else {
			$sql .= " = " . (int)$product_id;
		}
		
		$query = $this->db->query($sql);

		$this->store = ArrayHelper::index($query->rows, 'product_id', true);
	}

	public function getProductOptionImagesPro($product_id, $product_option_id = array(), $product_option_value_id = array(), $from_cache = true) {
		$output = array();
		$query = null;
		$sql = "SELECT * FROM " . DB_PREFIX . "poip_option_image WHERE product_id = " . (int)$product_id;

		if ($product_option_id) {
			if (is_array($product_option_id)) {
				$sql .= " AND product_option_id IN(" . implode(',', $this->escapeArray($product_option_id, 'intval')) . ")";
			}
			else {
				$sql .= " AND product_option_id = " . (int)$product_option_id;
			}
		}

		if ($product_option_value_id) {
			if (is_array($product_option_value_id)) {
				$sql .= " AND product_option_value_id IN(" . implode(',', $this->escapeArray($product_option_value_id, 'intval')) . ")";
			}
			else {
				$sql .= " AND product_option_value_id = " . (int)$product_option_value_id;
			}
		}
		
		if ($from_cache) {
			if (isset($this->store[$product_id])) {
				foreach ($this->store[$product_id] as $row) {
					if ($product_option_id) {
						if ($product_option_id == $row['product_option_id'])
							$output[] = $row;
					}
					else {
						$output[] = $row;
					}
				}
			}
			else {
				$query = $this->db->query($sql);
			}
		}
		else {
			$query = $this->db->query($sql);
		}

		if ($query !== null && $query->rows) {
			$output = $query->rows;
		}

		return $output;
	}
}