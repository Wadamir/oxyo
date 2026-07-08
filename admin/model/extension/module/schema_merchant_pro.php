<?php
class ModelExtensionModuleSchemaMerchantPro extends Model {
    public function getDefaultSettings() {
        return array(
            'module_schema_merchant_pro_status' => 0,
            'module_schema_merchant_pro_license_key' => '',
            'module_schema_merchant_pro_debug' => 0,
            'module_schema_merchant_pro_output_jsonld' => 1,
            'module_schema_merchant_pro_output_open_graph' => 1,
            'module_schema_merchant_pro_output_twitter' => 1,
            'module_schema_merchant_pro_compatibility_mode' => 'careful',
            'module_schema_merchant_pro_organization_enabled' => 1,
            'module_schema_merchant_pro_organization_name' => '',
            'module_schema_merchant_pro_organization_url' => '',
            'module_schema_merchant_pro_organization_logo' => '',
            'module_schema_merchant_pro_organization_email' => '',
            'module_schema_merchant_pro_organization_phone' => '',
            'module_schema_merchant_pro_organization_street' => '',
            'module_schema_merchant_pro_organization_city' => '',
            'module_schema_merchant_pro_organization_region' => '',
            'module_schema_merchant_pro_organization_postcode' => '',
            'module_schema_merchant_pro_organization_country' => '',
            'module_schema_merchant_pro_organization_same_as' => '',
            'module_schema_merchant_pro_organization_opening_hours_enabled' => 0,
            'module_schema_merchant_pro_organization_opening_days' => '',
            'module_schema_merchant_pro_organization_opening_opens' => '',
            'module_schema_merchant_pro_organization_opening_closes' => '',
            'module_schema_merchant_pro_organization_geo_enabled' => 0,
            'module_schema_merchant_pro_organization_geo_latitude' => '',
            'module_schema_merchant_pro_organization_geo_longitude' => '',
            'module_schema_merchant_pro_twitter_handle' => '',
            'module_schema_merchant_pro_website_enabled' => 1,
            'module_schema_merchant_pro_website_name' => '',
            'module_schema_merchant_pro_website_url' => '',
            'module_schema_merchant_pro_website_language' => '',
            'module_schema_merchant_pro_search_action_enabled' => 1,
            'module_schema_merchant_pro_breadcrumbs_enabled' => 1,
            'module_schema_merchant_pro_product_enabled' => 1,
            'module_schema_merchant_pro_offer_enabled' => 1,
            'module_schema_merchant_pro_brand_enabled' => 1,
            'module_schema_merchant_pro_identifiers_enabled' => 1,
            'module_schema_merchant_pro_attributes_enabled' => 1,
            'module_schema_merchant_pro_rating_enabled' => 0,
            'module_schema_merchant_pro_reviews_enabled' => 0,
            'module_schema_merchant_pro_shipping_enabled' => 0,
            'module_schema_merchant_pro_shipping_country' => '',
            'module_schema_merchant_pro_shipping_price' => '',
            'module_schema_merchant_pro_shipping_currency' => '',
            'module_schema_merchant_pro_shipping_handling_min_days' => '',
            'module_schema_merchant_pro_shipping_handling_max_days' => '',
            'module_schema_merchant_pro_shipping_transit_min_days' => '',
            'module_schema_merchant_pro_shipping_transit_max_days' => '',
            'module_schema_merchant_pro_returns_enabled' => 0,
            'module_schema_merchant_pro_returns_country' => '',
            'module_schema_merchant_pro_returns_category' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'module_schema_merchant_pro_returns_days' => '',
            'module_schema_merchant_pro_returns_fees' => 'https://schema.org/FreeReturn',
            'module_schema_merchant_pro_returns_method' => 'https://schema.org/ReturnByMail',
            'module_schema_merchant_pro_preview_product_id' => ''
        );
    }

    public function getPreviewProductContext($product_id) {
        $product_id = (int)$product_id;

        if ($product_id <= 0) {
            return array();
        }

        $language_id = (int)$this->config->get('config_language_id');

        $query = $this->db->query("SELECT p.*, pd.name, pd.description, pd.meta_description, m.name AS manufacturer FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "') LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . $product_id . "' LIMIT 1");

        if (!$query->num_rows) {
            return array();
        }

        $product = $query->row;
        $url = $this->catalogUrl('index.php?route=product/product&product_id=' . $product_id);
        $images = array();

        if (!empty($product['image'])) {
            $images[] = $this->catalogUrl('image/' . ltrim($product['image'], '/'));
        }

        $image_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id = '" . $product_id . "' ORDER BY sort_order ASC");

        foreach ($image_query->rows as $image) {
            if (!empty($image['image'])) {
                $images[] = $this->catalogUrl('image/' . ltrim($image['image'], '/'));
            }
        }

        $attribute_groups = $this->getPreviewAttributeGroups($product_id, $language_id);

        return array(
            'product_id' => $product_id,
            'name' => $product['name'],
            'heading_title' => $product['name'],
            'description' => $product['description'],
            'meta_description' => $product['meta_description'],
            'url' => $url,
            'images' => array_values(array_unique($images)),
            'manufacturer' => $product['manufacturer'],
            'model' => $product['model'],
            'sku' => $product['sku'],
            'upc' => $product['upc'],
            'ean' => $product['ean'],
            'jan' => $product['jan'],
            'isbn' => $product['isbn'],
            'mpn' => $product['mpn'],
            'price' => $product['price'],
            'special' => $this->getPreviewSpecialPrice($product_id),
            'tax_class_id' => $product['tax_class_id'],
            'currency' => $this->config->get('config_currency'),
            'quantity' => $product['quantity'],
            'stock_status_id' => isset($product['stock_status_id']) ? (int)$product['stock_status_id'] : 0,
            'price_visible' => true,
            'breadcrumbs' => array(
                array(
                    'text' => $this->language->get('text_home') !== 'text_home' ? $this->language->get('text_home') : 'Главная',
                    'href' => $this->catalogUrl('index.php?route=common/home')
                ),
                array(
                    'text' => $product['name'],
                    'href' => $url
                )
            ),
            'attribute_groups' => $attribute_groups,
            'review_summary' => $this->getPreviewReviewSummary($product_id),
            'reviews' => $this->getPreviewReviews($product_id, $language_id)
        );
    }

    public function getFirstPreviewProductId() {
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER BY product_id ASC LIMIT 1");

        return $query->num_rows ? (int)$query->row['product_id'] : 0;
    }

    public function getStockStatuses() {
        $language_id = (int)$this->config->get('config_language_id');
        $query = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . $language_id . "' ORDER BY name");

        return $query->rows;
    }

    private function getPreviewAttributeGroups($product_id, $language_id) {
        $groups = array();
        $query = $this->db->query("SELECT agd.name AS group_name, ad.name AS attribute_name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id AND ad.language_id = '" . (int)$language_id . "') LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (a.attribute_group_id = agd.attribute_group_id AND agd.language_id = '" . (int)$language_id . "') WHERE pa.product_id = '" . (int)$product_id . "' AND pa.language_id = '" . (int)$language_id . "' ORDER BY a.attribute_group_id, a.sort_order, ad.name");

        foreach ($query->rows as $row) {
            $group_name = $row['group_name'] !== '' ? $row['group_name'] : 'Attributes';

            if (!isset($groups[$group_name])) {
                $groups[$group_name] = array(
                    'name' => $group_name,
                    'attribute' => array()
                );
            }

            $groups[$group_name]['attribute'][] = array(
                'name' => $row['attribute_name'],
                'text' => $row['text']
            );
        }

        return array_values($groups);
    }

    private function getPreviewSpecialPrice($product_id) {
        $customer_group_id = (int)$this->config->get('config_customer_group_id');

        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

        return $query->num_rows ? $query->row['price'] : null;
    }

    private function getPreviewReviewSummary($product_id) {
        $query = $this->db->query("SELECT COUNT(*) AS review_count, AVG(rating) AS rating_average FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "' AND status = '1'");

        return array(
            'review_count' => (int)$query->row['review_count'],
            'rating_average' => $query->row['rating_average'] !== null ? (float)$query->row['rating_average'] : 0
        );
    }

    private function getPreviewReviews($product_id, $language_id) {
        $query = $this->db->query("SELECT author, text, rating, date_added FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "' AND status = '1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

    private function catalogUrl($path) {
        $base = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : (defined('HTTP_CATALOG') ? HTTP_CATALOG : '');

        if ($base === '') {
            return '';
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    public function install() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_schema_merchant_pro', $this->getDefaultSettings());
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_schema_merchant_pro');
    }
}
