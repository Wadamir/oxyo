<?php
namespace yamarket_fusion\Module\models\addon\image_option_change;

use yamarket_fusion\Helpers\ArrayHelper;

class ImageOptionChange extends \yamarket_fusion\Base\Model {
	const setting_code = 'image_option';
	
	private $store = array();

	public function storeProductsOptionsImages($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image_option WHERE product_id";

		if (is_array($product_id)) {
			$sql .= " IN (" . implode(',', array_map('intval', $product_id)) . ")";
		}
		else {
			$sql .= " = " . (int)$product_id;
		}
		
		$query = $this->db->query($sql);

		$this->store = ArrayHelper::index($query->rows, 'product_id', true);
	}

	public function getProductOptionImages($product_id, $option_value_id = array(), $from_cache = true) {
		$output = array();
		$query = null;
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image_option WHERE product_id = " . (int)$product_id;

		if ($option_value_id) {
			if (is_array($option_value_id)) {
				$sql .= " AND option_value_id IN(" . implode(',', $this->escapeArray($option_value_id, 'intval')) . ")";
			}
			else {
				$sql .= " AND option_value_id = " . (int)$option_value_id;
			}
		}
		
		if ($from_cache) {
			if (isset($this->store[$product_id])) {
				foreach ($this->store[$product_id] as $row) {
					if ($option_value_id) {
						if ($option_value_id == $row['option_value_id'])
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