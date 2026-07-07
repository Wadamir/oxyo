<?php
class ModelExtensionModuleMassUpdate extends Model {
	
	/**
	 * Update SKU for selected products
	 * 
	 * @param array $product_ids Array of product IDs
	 * @param string $sku New SKU value
	 * @return int Number of updated products
	 */
	public function updateSkuForProducts($product_ids, $sku) {
		if (empty($product_ids) || !is_array($product_ids)) {
			return 0;
		}
		
		$updated_count = 0;
		
		foreach ($product_ids as $product_id) {
			$product_id = (int)$product_id;
			
			if ($product_id > 0) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $this->db->escape($sku) . "' WHERE product_id = '" . $product_id . "'");
				$updated_count++;
			}
		}
		
		return $updated_count;
	}
	
	/**
	 * Update SKU for products using their product IDs as SKU values
	 * 
	 * @param array $product_ids Array of product IDs
	 * @return int Number of updated products
	 */
	public function updateSkuWithProductIds($product_ids) {
		if (empty($product_ids) || !is_array($product_ids)) {
			return 0;
		}
		
		$updated_count = 0;
		
		foreach ($product_ids as $product_id) {
			$product_id = (int)$product_id;
			
			if ($product_id > 0) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $product_id . "' WHERE product_id = '" . $product_id . "'");
				$updated_count++;
			}
		}
		
		return $updated_count;
	}
	
	/**
	 * Get products without SKU
	 * 
	 * @return array Array of product IDs without SKU
	 */
	public function getProductsWithoutSku() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE sku = '' OR sku IS NULL");
		
		$product_ids = array();
		foreach ($query->rows as $row) {
			$product_ids[] = $row['product_id'];
		}
		
		return $product_ids;
	}
	
	/**
	 * Get products with SKU
	 * 
	 * @return array Array of product IDs with SKU
	 */
	public function getProductsWithSku() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE sku != '' AND sku IS NOT NULL");
		
		$product_ids = array();
		foreach ($query->rows as $row) {
			$product_ids[] = $row['product_id'];
		}
		
		return $product_ids;
	}
	
	/**
	 * Get all products
	 * 
	 * @return array Array of all product IDs
	 */
	public function getAllProducts() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product");
		
		$product_ids = array();
		foreach ($query->rows as $row) {
			$product_ids[] = $row['product_id'];
		}
		
		return $product_ids;
	}
}
