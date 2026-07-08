<?php
class ControllerExtensionModuleSchemaMerchantPro extends Controller {
    const LICENSE_KEY_HASH = '875f48b123bfdc8c621c21c3001f60554e053468b89ecb7aa47d204782bd6f7f';
    const DOMAIN_KEY_PREFIX = 'SMP-';

    public function index() {
        if (!$this->config->get('module_schema_merchant_pro_status')) {
            return '';
        }

        if (!$this->hasValidLicense()) {
            return '';
        }

        if (!class_exists('SchemaMerchantProGenerator')) {
            require_once(DIR_SYSTEM . 'library/schema_merchant_pro/generator.php');
        }

        $generator = new SchemaMerchantProGenerator($this->registry);
        $product_context = $this->registry->get('schema_merchant_pro_product_context');

        return $generator->renderPageMarkup(array(
            'route' => isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home',
            'site' => $this->getSiteContext(),
            'product' => is_array($product_context) ? $product_context : array(),
            'native_open_graph' => $this->getNativeOpenGraphContext()
        ));
    }

    public function product($context = array()) {
        if (!is_array($context)) {
            return '';
        }

        $product_info = isset($context['product_info']) && is_array($context['product_info']) ? $context['product_info'] : array();
        $data = isset($context['data']) && is_array($context['data']) ? $context['data'] : array();

        if (empty($product_info) || empty($data['product_id'])) {
            return '';
        }

        $this->registry->set('schema_merchant_pro_product_context', $this->normalizeProductContext($product_info, $data));

        return '';
    }

    private function normalizeProductContext(array $product_info, array $data) {
        $images = array();

        if (!empty($data['popup'])) {
            $images[] = $data['popup'];
        }

        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                if (!empty($image['popup'])) {
                    $images[] = $image['popup'];
                }
            }
        }

        $images = array_values(array_unique($images));

        $stock_status_id = isset($product_info['stock_status_id']) ? (int)$product_info['stock_status_id'] : $this->getProductStockStatusId((int)$data['product_id']);

        return array(
            'route' => 'product/product',
            'product_id' => (int)$data['product_id'],
            'name' => isset($product_info['name']) ? $product_info['name'] : '',
            'heading_title' => isset($data['heading_title']) ? $data['heading_title'] : '',
            'url' => isset($data['share']) ? $data['share'] : '',
            'description' => isset($product_info['description']) ? $product_info['description'] : '',
            'meta_description' => isset($product_info['meta_description']) ? $product_info['meta_description'] : '',
            'images' => $images,
            'manufacturer_id' => isset($product_info['manufacturer_id']) ? (int)$product_info['manufacturer_id'] : 0,
            'manufacturer' => isset($product_info['manufacturer']) ? $product_info['manufacturer'] : '',
            'manufacturer_url' => isset($data['manufacturers']) ? $data['manufacturers'] : '',
            'model' => isset($product_info['model']) ? $product_info['model'] : '',
            'sku' => isset($product_info['sku']) ? $product_info['sku'] : '',
            'upc' => isset($product_info['upc']) ? $product_info['upc'] : '',
            'ean' => isset($product_info['ean']) ? $product_info['ean'] : '',
            'jan' => isset($product_info['jan']) ? $product_info['jan'] : '',
            'isbn' => isset($product_info['isbn']) ? $product_info['isbn'] : '',
            'mpn' => isset($product_info['mpn']) ? $product_info['mpn'] : '',
            'price' => isset($product_info['price']) ? $product_info['price'] : null,
            'special' => isset($product_info['special']) ? $product_info['special'] : null,
            'price_visible' => isset($data['price']) && $data['price'] !== false,
            'tax_class_id' => isset($product_info['tax_class_id']) ? (int)$product_info['tax_class_id'] : 0,
            'currency' => $this->config->get('config_currency'),
            'quantity' => isset($product_info['quantity']) ? (int)$product_info['quantity'] : 0,
            'stock_status_id' => $stock_status_id,
            'stock_status' => isset($product_info['stock_status']) ? $product_info['stock_status'] : '',
            'stock_text' => isset($data['stock']) ? $data['stock'] : '',
            'breadcrumbs' => isset($data['breadcrumbs']) && is_array($data['breadcrumbs']) ? $data['breadcrumbs'] : array(),
            'attribute_groups' => isset($data['attribute_groups']) && is_array($data['attribute_groups']) ? $data['attribute_groups'] : array(),
            'rating' => isset($product_info['rating']) ? (int)$product_info['rating'] : 0,
            'review_count' => isset($product_info['reviews']) ? (int)$product_info['reviews'] : 0,
            'review_summary' => $this->getProductReviewSummary((int)$data['product_id']),
            'reviews' => $this->getProductReviews((int)$data['product_id'])
        );
    }

    private function getProductStockStatusId($product_id) {
        if ($product_id < 1) {
            return 0;
        }

        $query = $this->db->query("SELECT stock_status_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "' LIMIT 1");

        return $query->num_rows && isset($query->row['stock_status_id']) ? (int)$query->row['stock_status_id'] : 0;
    }

    private function getProductReviewSummary($product_id) {
        if (!$this->config->get('config_review_status')) {
            return array();
        }

        $query = $this->db->query("SELECT COUNT(*) AS review_count, AVG(rating) AS rating_average FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "' AND status = '1'");

        $review_count = isset($query->row['review_count']) ? (int)$query->row['review_count'] : 0;
        $rating_average = isset($query->row['rating_average']) ? (float)$query->row['rating_average'] : 0;

        if ($review_count < 1 || $rating_average <= 0) {
            return array();
        }

        return array(
            'review_count' => $review_count,
            'rating_average' => $rating_average
        );
    }

    private function getProductReviews($product_id) {
        if (!$this->config->get('config_review_status')) {
            return array();
        }

        $this->load->model('catalog/review');

        $reviews = $this->model_catalog_review->getReviewsByProductId($product_id, 0, 5);
        $result = array();

        foreach ($reviews as $review) {
            $result[] = array(
                'author' => isset($review['author']) ? $review['author'] : '',
                'text' => isset($review['text']) ? $review['text'] : '',
                'rating' => isset($review['rating']) ? (int)$review['rating'] : 0,
                'date_added' => isset($review['date_added']) ? $review['date_added'] : ''
            );
        }

        return $result;
    }

    private function getSiteContext() {
        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $logo = '';

        if ($this->config->get('config_logo') && is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $logo = rtrim($server, '/') . '/image/' . ltrim($this->config->get('config_logo'), '/');
        }

        return array(
            'url' => $server,
            'name' => $this->config->get('config_name'),
            'language' => $this->config->get('config_language'),
            'logo' => $logo,
            'email' => $this->config->get('config_email'),
            'phone' => $this->config->get('config_telephone'),
            'search_url' => $this->url->link('product/search', 'search={search_term_string}', true)
        );
    }

    private function getNativeOpenGraphContext() {
        $native_open_graph = $this->registry->get('schema_merchant_pro_native_open_graph');

        return is_array($native_open_graph) ? $native_open_graph : array();
    }

    private function hasValidLicense() {
        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '0' AND code = 'module_schema_merchant_pro' AND `key` = 'module_schema_merchant_pro_license_key' LIMIT 1");
        $key = $query->num_rows ? trim((string)$query->row['value']) : '';

        if ($key === '') {
            return false;
        }

        return hash_equals($this->buildDomainLicenseKey($this->getLicenseDomain()), strtoupper($key));
    }

    private function buildDomainLicenseKey($domain) {
        $domain = $this->normalizeDomain($domain);

        if ($domain === '') {
            return '';
        }

        $hash = strtoupper(substr(hash('sha256', 'domain-license|' . self::LICENSE_KEY_HASH . '|' . $domain), 0, 32));

        return self::DOMAIN_KEY_PREFIX . implode('-', str_split($hash, 4));
    }

    private function getLicenseDomain() {
        if (!empty($this->request->server['HTTP_HOST'])) {
            return $this->normalizeDomain($this->request->server['HTTP_HOST']);
        }

        if ($this->request->server['HTTPS']) {
            $base = $this->config->get('config_ssl');
        } else {
            $base = $this->config->get('config_url');
        }

        return $this->normalizeDomain(parse_url($base, PHP_URL_HOST));
    }

    private function normalizeDomain($domain) {
        $domain = strtolower(trim((string)$domain));

        if (strpos($domain, ':') !== false) {
            $domain = explode(':', $domain, 2)[0];
        }

        if (strpos($domain, 'www.') === 0) {
            $domain = substr($domain, 4);
        }

        return preg_match('/^[a-z0-9.-]+$/', $domain) ? $domain : '';
    }
}
