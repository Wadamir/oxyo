<?php
namespace yamarket_fusion\Module\models\addon\related_options;

class RelatedOptions extends \Model {

	public function getProductRelatedOptionsVariants($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product 
									WHERE relatedoptions_use = 1 AND product_id = " . (int)$product_id
								);
		return $query->rows;
	}

	public function getProductRelatedOptions($product_id, $ro_variant_id) {
		$query = $this->db->query("SELECT r.*, r_s.price AS special_price FROM " . DB_PREFIX . "relatedoptions AS r
									LEFT JOIN " . DB_PREFIX . "relatedoptions_special AS r_s ON r_s.relatedoptions_id = r.relatedoptions_id
										AND r_s.customer_group_id = " . (int)$this->config->get('config_customer_group_id') . "
									WHERE r.product_id = " . (int)$product_id . " 
										AND r.relatedoptions_variant_product_id = " . (int)$ro_variant_id
										// AND r.quantity > 0
								);

		return $query->rows;
	}

	public function getProductRelatedOptionsValues($related_options_id) {
		$lang = (int)$this->config->get('config_language_id');

		$query = $this->db->query("SELECT r.*, o_d.name AS option_name, o_v_d.name AS option_value, p_o_v.product_option_value_id
									FROM " . DB_PREFIX . "relatedoptions_option AS r
									LEFT JOIN " . DB_PREFIX . "option_description AS o_d ON o_d.option_id = r.option_id 
										AND o_d.language_id = {$lang}
									LEFT JOIN " . DB_PREFIX . "option_value_description AS o_v_d ON o_v_d.option_value_id = r.option_value_id
										AND o_v_d.language_id = {$lang}
									LEFT JOIN " . DB_PREFIX . "product_option_value AS p_o_v ON p_o_v.product_id = r.product_id
										AND p_o_v.option_id = r.option_id
										AND p_o_v.option_value_id = r.option_value_id
									WHERE relatedoptions_id = " . (int)$related_options_id
								);
		return $query->rows;
	}
}