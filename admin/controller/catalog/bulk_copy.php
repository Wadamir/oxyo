<?php
class ControllerCatalogBulkCopy extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('catalog/bulk_copy');

        $this->document->addStyle('view/stylesheet/bulk_copy.css');

        $data['user_token'] = $this->session->data['user_token'];

        // Load other language strings and data as needed
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Load view template
        return $this->load->view('catalog/bulk_copy', $data);
    }

    public function getProductInfo()
    {
        $this->load->language('catalog/bulk_copy');
        $json = [];

        if (!$this->user->hasPermission('access', 'catalog/bulk_copy')) {
            $json['error'] = $this->language->get('error_permission');
        } elseif (!isset($this->request->get['product_id'])) {
            $json['error'] = 'Missing product_id';
        } else {
            $product_id = (int)$this->request->get['product_id'];

            $this->load->model('catalog/product');

            $product = $this->model_catalog_product->getProduct($product_id);
            if (!$product) {
                $json['error'] = 'Product not found';
            } else {
                // Get product description
                $product_description = $this->model_catalog_product->getProductDescriptions($product_id);
                $name = isset($product_description[$this->config->get('config_language_id')]['name'])
                    ? $product_description[$this->config->get('config_language_id')]['name']
                    : $product['name'];

                // Get product attributes
                $this->load->model('catalog/attribute');
                $attributes_raw = $this->model_catalog_product->getProductAttributes($product_id);
                $product_attributes = [];

                foreach ($attributes_raw as $attribute) {
                    $attribute_info = $this->model_catalog_attribute->getAttribute($attribute['attribute_id']);

                    if ($attribute_info && $attribute_info['type'] == 'select') {
                        $product_attribute_values = $this->model_catalog_attribute->getAttributeValueDescriptions($attribute['attribute_id']);
                        // var_dump($product_attribute_values); // Debugging line, can be removed later

                        $product_attributes[] = array(
                            'attribute_id'                  => $attribute['attribute_id'],
                            'name'                          => $attribute_info['name'],
                            'type'                          => $attribute_info['type'],
                            'product_attribute_description' => $attribute['product_attribute_description'],
                            'product_attribute_values'      => $product_attribute_values
                        );
                    }
                }

                $json['product'] = [
                    'product_id' => $product_id,
                    'name' => $name,
                    'attributes' => $product_attributes
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getAttributeValues()
    {
        $this->load->language('catalog/bulk_copy');
        $json = [];

        if (!$this->user->hasPermission('access', 'catalog/bulk_copy')) {
            $json['error'] = $this->language->get('error_permission');
        } elseif (!isset($this->request->get['attribute_id'])) {
            $json['error'] = 'Missing attribute_id';
        } else {
            $attribute_id = (int)$this->request->get['attribute_id'];

            $this->load->model('catalog/attribute');
            $attribute_values = $this->model_catalog_attribute->getAttributeValueDescriptions($attribute_id);

            if (!$attribute_values) {
                $json['error'] = 'No attribute values found';
            } else {
                foreach ($attribute_values as $value) {
                    $json['attribute_values'][] = [
                        'value_id' => $value['attribute_value_id'],
                        'name'     => $value['attribute_value_description'][$this->config->get('config_language_id')]['name']
                    ];
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function bulkCopyProduct()
    {
        $this->load->language('catalog/bulk_copy');
        $json = [];

        if (!$this->user->hasPermission('modify', 'catalog/bulk_copy')) {
            $json['error'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        if (!$product_id) {
            $json['error'] = $this->language->get('error_missing_product_id');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        $attribute_id = isset($this->request->post['attribute_id']) ? (int)$this->request->post['attribute_id'] : 0;
        if (!$attribute_id) {
            $json['error'] = $this->language->get('error_missing_attribute_id');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        $attribute_values = isset($this->request->post['attribute_values']) ? json_decode($this->request->post['attribute_values']) : [];
        if (empty($attribute_values)) {
            $json['error'] = $this->language->get('error_no_attribute_values');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $json['data'] = [
            'product_id' => $product_id,
            'attribute_id' => $attribute_id,
            'attribute_values' => $attribute_values
        ];

        $this->load->model('catalog/bulk_copy');

        foreach ($attribute_values as $value) {
            $copy_product_result = $this->model_catalog_bulk_copy->copyProduct($product_id, $attribute_id, $value);
            $json['data']['copy_product_result'][] = $copy_product_result;
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'catalog/bulk_copy')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // Additional validation logic
        // ...

        return !$this->error;
    }
}
