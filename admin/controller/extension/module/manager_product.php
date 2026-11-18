<?php
class ControllerExtensionModuleManagerProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/manager_product');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addStyle('view/stylesheet/manager_product/style.css');
		
		$this->response->redirect($this->url->link('extension/module/manager_product', 'user_token=' . $this->session->data['user_token'], true));
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/manager_product', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['user_token'] = $this->session->data['user_token'];

		$data['manager'] = $this->url->link('catalog/manager_product', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/manager_product', $data));
	}
	
	public function edit() {
			
			if ($this->request->get['type'] == 'manager_setting') {
				$this->editSetting();
			} elseif ($this->request->get['type'] == 'product_list_data') {
				$this->editProductListData();
			} elseif ($this->request->get['type'] == 'product_item_data') {
				$this->editProductItemData();
			} elseif ($this->request->get['type'] == 'product_mass_edit') {
				$this->editProductMassData();
			} elseif ($this->request->get['type'] == 'mass_delete') {
				$this->massDelete();
			}
	
	}
	
	//*********************************************************************************//
	//*************************** Product Setting Edit ********************************//
	//*********************************************************************************//
	
	public function editSetting() {
		$this->load->language('extension/module/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$data['presence_column_meta_h1'] = $this->model_catalog_manager_product->getProductPresenceMetaH1();

		$data['user_token'] = $this->session->data['user_token'];

		$setting_data = array(
			// Products General Setting
			'manager_product_default_limit',
			'manager_product_default_quantity',
			'manager_product_default_min_quantity',
			'manager_product_model_required',
			'manager_product_meta_title_required',
			'manager_product_default_stock',
			'manager_product_default_shipping',
			'manager_product_view_message',
			// Products Item Edit
			'manager_product_image',
			'manager_product_name',
			'manager_product_category',
			'manager_product_manufacturer',
			'manager_product_model',
			'manager_product_sku',
			'manager_product_upc',
			'manager_product_ean',
			'manager_product_jan',
			'manager_product_isbn',
			'manager_product_mpn',
			'manager_product_location',
			'manager_product_price',
			'manager_product_quantity',
			'manager_product_minimum',
			'manager_product_subtract',
			'manager_product_stock_status_id',
			'manager_product_shipping',
			'manager_product_dimension',
			'manager_product_weights',
			'manager_product_weight_class',
			'manager_product_sort_order',
			'manager_product_status',
			'manager_product_general',
			'manager_product_code',
			'manager_product_tax',
			'manager_product_stock',
			'manager_product_seo_url',
			'manager_product_date',
			'manager_product_weight',
			'manager_product_filter',
			'manager_product_store',
			'manager_product_download',
			'manager_product_related',
			'manager_product_attribute',
			'manager_product_option',
			'manager_product_discount',
			'manager_product_special',
			'manager_product_images',
			'manager_product_reward',
			'manager_product_design',
			'manager_product_view',
			'manager_product_option_signs',
			// Products Mass Edit
			'manager_product_mass_name',
			'manager_product_mass_description',
			'manager_product_mass_meta_title',
			'manager_product_mass_meta_h1',
			'manager_product_mass_meta_description',
			'manager_product_mass_meta_keyword',
			'manager_product_mass_tag',
			'manager_product_mass_image',
			'manager_product_mass_price',
			'manager_product_mass_quantity',
			'manager_product_mass_minimum',
			'manager_product_mass_model',
			'manager_product_mass_seo_url',
			'manager_product_mass_code',
			'manager_product_mass_date_available',
			'manager_product_mass_shipping',
			'manager_product_mass_size',
			'manager_product_length_class',
			'manager_product_mass_sort_order',
			'manager_product_mass_manufacturer',
			'manager_product_mass_category',
			'manager_product_mass_filter',
			'manager_product_mass_store',
			'manager_product_mass_download',
			'manager_product_mass_related',
			'manager_product_mass_add_discount',
			'manager_product_mass_edit_discount',
			'manager_product_mass_add_special',
			'manager_product_mass_edit_special',
			'manager_product_mass_attribute',
			'manager_product_mass_option',
			'manager_product_mass_images',
			'manager_product_mass_reward',
			'manager_product_mass_design',
			// Products Mass Delete
			'manager_product_mass_delete_name',
			'manager_product_mass_delete_description',
			'manager_product_mass_delete_meta_title',
			'manager_product_mass_delete_meta_h1',
			'manager_product_mass_delete_meta_description',
			'manager_product_mass_delete_meta_keyword',
			'manager_product_mass_delete_tag',
			'manager_product_mass_delete_image',
			'manager_product_mass_delete_model',
			'manager_product_mass_delete_sku',
			'manager_product_mass_delete_upc',
			'manager_product_mass_delete_ean',
			'manager_product_mass_delete_jan',
			'manager_product_mass_delete_isbn',
			'manager_product_mass_delete_mpn',
			'manager_product_mass_delete_location',
			'manager_product_mass_delete_price',
			'manager_product_mass_delete_tax_class',
			'manager_product_mass_delete_quantity',
			'manager_product_mass_delete_minimum',
			'manager_product_mass_delete_seo_url',
			'manager_product_mass_delete_date_available',
			'manager_product_mass_delete_size',
			'manager_product_mass_delete_weight',
			'manager_product_mass_delete_sort_order',
			'manager_product_mass_delete_manufacturer',
			'manager_product_mass_delete_category',
			'manager_product_mass_delete_filter',
			'manager_product_mass_delete_store',
			'manager_product_mass_delete_download',
			'manager_product_mass_delete_related',
			'manager_product_mass_delete_attribute',
			'manager_product_mass_delete_option',
			'manager_product_mass_delete_discount',
			'manager_product_mass_delete_special',
			'manager_product_mass_delete_images',
			'manager_product_mass_delete_reward',
			'manager_product_mass_delete_design',
			// Products Hide Data
			'manager_product_hide_codes',
			'manager_product_hide_model',
			'manager_product_hide_price',
			'manager_product_hide_tax_class',
			'manager_product_hide_quantity',
			'manager_product_hide_minimum',
			'manager_product_hide_subtract',
			'manager_product_hide_stock_status',
			'manager_product_hide_shipping',
			'manager_product_hide_seo_url',
			'manager_product_hide_date_available',
			'manager_product_hide_dimension',
			'manager_product_hide_length_class',
			'manager_product_hide_weight',
			'manager_product_hide_weight_class',
			'manager_product_hide_status',
			'manager_product_hide_sort_order',
			'manager_product_hide_manufacturer',
			'manager_product_hide_filter',
			'manager_product_hide_store',
			'manager_product_hide_download',
			'manager_product_hide_related',
			'manager_product_hide_attribute',
			'manager_product_hide_option',
			'manager_product_hide_recurring',
			'manager_product_hide_discount',
			'manager_product_hide_special',
			'manager_product_hide_additional_image',
			'manager_product_hide_reward',
			'manager_product_hide_layout',
			// Column
			'manager_product_image_view',
			'manager_product_category_view',
			'manager_product_manufacturer_view',
			'manager_product_model_view',
			'manager_product_sku_view',
			'manager_product_upc_view',
			'manager_product_ean_view',
			'manager_product_jan_view',
			'manager_product_isbn_view',
			'manager_product_mpn_view',
			'manager_product_location_view',
			'manager_product_price_view',
			'manager_product_tax_view',
			'manager_product_quantity_view',
			'manager_product_minimum_view',
			'manager_product_subtract_view',
			'manager_product_stock_status_id_view',
			'manager_product_shipping_view',
			'manager_product_date_available_view',
			'manager_product_dimension_view',
			'manager_product_length_class_view',
			'manager_product_weight_view',
			'manager_product_weight_class_view',
			'manager_product_sort_order_view',
			'manager_product_status_view',
		);
		
		foreach ($setting_data as $setting_value) {
            if (isset($this->request->post[$setting_value])) {
                $data[$setting_value] = $this->request->post[$setting_value];
			} else {
                $data[$setting_value] = $this->config->get($setting_value);
			}
        }
		
		$data['action'] = $this->url->link('extension/module/manager_product/updateSettingModule', 'user_token=' . $this->session->data['user_token'], true);
			
		$this->response->setOutput($this->load->view('extension/module/manager_product_setting', $data));
	}
	
	public function updateSettingModule() {
		$this->load->language('extension/module/manager_product');
		
		$this->load->model('setting/setting');
		
		$json = array();				
			
		if (!$this->user->hasPermission('modify', 'extension/module/manager_product')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($json['error'])) {
			$this->model_setting_setting->editSetting('manager_product', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	// Get Product Name
	public function getProductName($product_id) {
		$row = $this->db->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if (isset($row->row['name'])) {
			return $row->row['name'];
		}
	}
	
	//*********************************************************************************//
	//************************* Product List Data Edit ********************************//
	//*********************************************************************************//
	
	// Save Image, Model, SKU, Price, Quantity, Sort Order, Status
	public function editProductListData() {
		$this->load->language('catalog/manager_product');
		
		$value_data = $this->request->get['value_data'];
		$type_data = $this->request->get['type_data'];
		
		if (($type_data == 'dimension')) {
			$length_data = $this->request->get['length_data'];
			$width_data = $this->request->get['width_data'];
			$height_data = $this->request->get['height_data'];
		}
		
		$json = array();		
				
		if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if ($type_data == 'model') {
			if ($this->config->get('manager_product_model_required')) {
				if ((utf8_strlen($value_data) < 1) || (utf8_strlen($value_data) > 64)) {
						$json['error'] = $this->language->get('error_model');
				}
			}
		}

		if (!isset($json['error'])) {
			if (($type_data == 'image') || ($type_data == 'model') || ($type_data == 'sku') || ($type_data == 'upc') || ($type_data == 'ean') || ($type_data == 'jan') || ($type_data == 'isbn') || ($type_data == 'mpn') || ($type_data == 'location') || ($type_data == 'date_available')) {	
				$this->db->query("UPDATE " . DB_PREFIX . "product SET " . $type_data . " = '" . $this->db->escape($value_data) . "', date_modified = NOW() WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
			} elseif (($type_data == 'price') || ($type_data == 'quantity') || ($type_data == 'minimum') || ($type_data == 'weight')) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET " . $type_data . " = '" . (float)$value_data . "', date_modified = NOW()  WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
			} elseif (($type_data == 'dimension')) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET length = '" . (float)$length_data . "', width = '" . (float)$width_data . "', height = '" . (float)$height_data . "', date_modified = NOW()  WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
			} elseif (($type_data == 'manufacturer_id') || ($type_data == 'sort_order') || ($type_data == 'subtract') || ($type_data == 'shipping') || ($type_data == 'status') || ($type_data == 'tax_class_id') || ($type_data == 'stock_status_id') || ($type_data == 'length_class_id') || ($type_data == 'weight_class_id')) {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET " . $type_data . " = '" . (int)$value_data . "', date_modified = NOW()  WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
			}	
			
			$this->cache->delete('product');
			
			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	//*********************************************************************************//
	//************************* Product Item Data Edit ********************************//
	//*********************************************************************************//
	
	// Product Main Category
	public function getMainCategoryName($category_id) {
		$row = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int) $category_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if(isset($row->row['name'])){
			return $row->row['name'];
		}
	}
	
	public function editProductItemData() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		$this->load->model('catalog/category');
		
		$data['type_data'] = $this->request->get['type_data'];
		
		$data['product_id'] = $this->request->get['product_id'];
		
		$data['product_name'] = $this->getProductName($this->request->get['product_id']);
		
		$data['text_data'] = $this->language->get('text_' . $this->request->get['type_data']);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_manager_product->getProduct($this->request->get['product_id']);
		}
		
		$data['required_meta_title'] = $this->config->get('manager_product_meta_title_required') ? ' required' : '';
		
		$data['presence_column_meta_h1'] = $this->model_catalog_manager_product->getProductPresenceMetaH1();
		$data['presence_column_main_category'] = $this->model_catalog_manager_product->getProductPresenceMainCategory();
		$data['main_category_id'] = $this->model_catalog_manager_product->getProductMainCategoryId($this->request->get['product_id']);
		
		if (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_manager_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}
		
		$data['main_category'] = $this->getMainCategoryName($data['main_category_id']);
		
		$categories = $this->model_catalog_manager_product->getProductCategories($this->request->get['product_id']);
		
		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
		
		if (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}
		
		if (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}
		
		if (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}
		
		if (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}
		
		if (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}
		
		if (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}
		
		if (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}
		
		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_manager_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}
		
		if (!empty($product_info)) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = $this->config->get('manager_product_default_shipping') ? $this->config->get('manager_product_default_shipping') : 0;
		}
		
		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}
		
		if (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = $this->config->get('manager_product_default_min_quantity') ? $this->config->get('manager_product_default_min_quantity') : 1;
		}
		
		if (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = $this->config->get('manager_product_default_stock') ? $this->config->get('manager_product_default_stock') : 0;
		}
		
		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}
		
		if (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}
		
		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->get['product_id'])) {
			$filters = $this->model_catalog_manager_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
		
		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_catalog_manager_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}
		
		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->get['product_id'])) {
			$product_options = $this->model_catalog_manager_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}
		
		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_catalog_manager_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_catalog_manager_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}
		
		// Images
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if (isset($this->request->get['product_id'])) {
			$product_images = $this->model_catalog_manager_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}
		
		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_catalog_manager_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}
		
		// Relateds
		if (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_manager_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_manager_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}
		
		if (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_catalog_manager_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->get['product_id'])) {
			$data['product_seo_url'] = $this->model_catalog_manager_product->getProductSeoUrls($this->request->get['product_id']);
		} else {
			$data['product_seo_url'] = array();
		}

		if (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_catalog_manager_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();
		
		$data['option_signs'] = array();
		
		$option_signs = $this->config->get('manager_product_option_signs');
		
		if (isset($option_signs)) {
			foreach ($option_signs as $option_sign) {
				$data['option_signs'][] = array(
					'name'  => $option_sign['name'],
					'value' => $option_sign['value']
				);
			}
		}
		
		$data['action'] = $this->url->link('extension/module/manager_product/saveProductItemData', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . '&type_data=' . $this->request->get['type_data'], true);

		$this->response->setOutput($this->load->view('catalog/manager_product/product_data', $data));
	}
	
	public function saveProductItemData() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}

			if ($this->request->get['type_data'] == 'name') {
				foreach ($this->request->post['product_description'] as $language_id => $value) {
					if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
						$json['error'] = $this->language->get('error_name');
					}
				}
			}
			
			if ($this->request->get['type_data'] == 'general') {
				if ($this->config->get('manager_product_meta_title_required')) {
					foreach ($this->request->post['product_description'] as $language_id => $value) {
						if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
							$json['error'] = $this->language->get('error_meta_title');
						}
					}
				}
			} elseif ($this->request->get['type_data'] == 'seo_url') {				
				if ($this->request->post['product_seo_url']) {
					$this->load->model('design/seo_url');
					
					foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
						foreach ($language as $language_id => $keyword) {
							if (!empty($keyword)) {
								if (count(array_keys($language, $keyword)) > 1) {
									$json['error'] = $this->language->get('error_unique');
								}

								$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
			
								foreach ($seo_urls as $seo_url) {
									if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['product_id']) || ($seo_url['query'] != 'product_id=' . $this->request->get['product_id']))) {		
										$json['error'] = $this->language->get('error_keyword');
						
										break;
									}
								}
							}
						}
					}
				}
			}

			if (!isset($json['error'])) {
				if ($this->request->get['type_data'] == 'name') {
					foreach ($this->request->post['product_description'] as $language_id => $value) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($value['name']) . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND language_id = '" . (int)$language_id . "'");
					}
				} elseif ($this->request->get['type_data'] == 'categories') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_category'])) {
						foreach ($this->request->post['product_category'] as $category_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$this->request->get['product_id'] . "', category_id = '" . (int)$category_id . "'");
						}
					}
					
					$result = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_to_category' AND COLUMN_NAME = 'main_category'");
					
					if ($result->num_rows) {		
						if (isset($this->request->post['main_category_id']) && $this->request->post['main_category_id'] > 0) {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND category_id = '" . (int)$this->request->post['main_category_id'] . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$this->request->get['product_id'] . "', category_id = '" . (int)$this->request->post['main_category_id'] . "', main_category = 1");
						} elseif (isset($data['product_category'])) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND category_id = '" . (int)$this->request->post['product_category'][0] . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'general') {
					$result_column = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'meta_h1'");
		
					if ($result_column->num_rows) {
						foreach ($this->request->post['product_description'] as $language_id => $value) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}
					} else {
						foreach ($this->request->post['product_description'] as $language_id => $value) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'code') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $this->db->escape($this->request->post['sku']) . "', upc = '" . $this->db->escape($this->request->post['upc']) . "', ean = '" . $this->db->escape($this->request->post['ean']) . "', jan = '" . $this->db->escape($this->request->post['jan']) . "', isbn = '" . $this->db->escape($this->request->post['isbn']) . "', mpn = '" . $this->db->escape($this->request->post['mpn']) . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
				} elseif ($this->request->get['type_data'] == 'tax') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET location = '" . $this->db->escape($this->request->post['location']) . "', tax_class_id = '" . (int)$this->request->post['tax_class_id'] . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
				} elseif ($this->request->get['type_data'] == 'stock') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET minimum = '" . (int)$this->request->post['minimum'] . "', subtract = '" . (int)$this->request->post['subtract'] . "', stock_status_id = '" . (int)$this->request->post['stock_status_id'] . "', shipping = '" . (int)$this->request->post['shipping'] . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
				} elseif ($this->request->get['type_data'] == 'date_available') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET date_available = '" . $this->db->escape($this->request->post['date_available']) . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
				} elseif ($this->request->get['type_data'] == 'size') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET weight = '" . (float)$this->request->post['weight'] . "', weight_class_id = '" . (int)$this->request->post['weight_class_id'] . "', length = '" . (float)$this->request->post['length'] . "', width = '" . (float)$this->request->post['width'] . "', height = '" . (float)$this->request->post['height'] . "', length_class_id = '" . (int)$this->request->post['length_class_id'] . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
				} elseif ($this->request->get['type_data'] == 'filter') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_filter'])) {
						foreach ($this->request->post['product_filter'] as $filter_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$this->request->get['product_id'] . "', filter_id = '" . (int)$filter_id . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'store') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_store'])) {
						foreach ($this->request->post['product_store'] as $store_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$this->request->get['product_id'] . "', store_id = '" . (int)$store_id . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'download') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_download'])) {
						foreach ($this->request->post['product_download'] as $download_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$this->request->get['product_id'] . "', download_id = '" . (int)$download_id . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'related') {
					$this->editProductRelated($this->request->get['product_id'], $this->request->post);
				} elseif ($this->request->get['type_data'] == 'attribute') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (!empty($this->request->post['product_attribute'])) {
						foreach ($this->request->post['product_attribute'] as $product_attribute) {
							if ($product_attribute['attribute_id']) {
								$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$this->request->get['product_id'] . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

								foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$this->request->get['product_id'] . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
								}
							}
						}
					}
				} elseif ($this->request->get['type_data'] == 'option') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_option'])) {
						foreach ($this->request->post['product_option'] as $product_option) {
							if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
								if (isset($product_option['product_option_value'])) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$this->request->get['product_id'] . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

									$product_option_id = $this->db->getLastId();

									foreach ($product_option['product_option_value'] as $product_option_value) {
										$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$this->request->get['product_id'] . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
									}
								}
							} else {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$this->request->get['product_id'] . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
							}
						}
					}
				} elseif ($this->request->get['type_data'] == 'discount') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_discount'])) {
						foreach ($this->request->post['product_discount'] as $product_discount) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$this->request->get['product_id'] . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'special') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_special'])) {
						foreach ($this->request->post['product_special'] as $product_special) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$this->request->get['product_id'] . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'images') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_image'])) {
						foreach ($this->request->post['product_image'] as $product_image) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$this->request->get['product_id'] . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'reward') {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET points = '" . (int)$this->request->post['points'] . "' WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");
		
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_reward'])) {
						foreach ($this->request->post['product_reward'] as $customer_group_id => $value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$this->request->get['product_id'] . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
						}
					}
				} elseif ($this->request->get['type_data'] == 'seo_url') {
					$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'product_id=" . (int)$this->request->get['product_id'] . "'");
					
					if ($this->request->post['auto_keyword_product']) {
						if (isset($this->request->post['product_seo_url'])) {
							foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
								foreach ($language as $language_id => $keyword) {
									if (!empty($keyword)) {
										$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$this->request->get['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
									} else {
										$this->generateAutoSeoUrl($this->request->get['product_id']);
									}									
								}
							}
						}
					} else {
						if (isset($this->request->post['product_seo_url'])) {
							foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
								foreach ($language as $language_id => $keyword) {
									if (!empty($keyword)) {
										$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$this->request->get['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
									}
								}
							}
						}
					}
				} elseif ($this->request->get['type_data'] == 'design') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

					if (isset($this->request->post['product_layout'])) {
						foreach ($this->request->post['product_layout'] as $store_id => $layout_id) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$this->request->get['product_id'] . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
						}
					}
				}
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$this->request->get['product_id'] . "'");

				$this->cache->delete('product');
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function editProductRelated($product_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	//*********************************************************************************//
	//******************************* Product Mass Edit *******************************//
	//*********************************************************************************//
	
	// Edit Product Mass Data
	public function editProductMassData() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$data['type_data'] = $this->request->get['type_data'];
		
		$data['text_rewrite'] = $this->language->get('text_rewrite_' . $data['type_data']);
		$data['text_meta_tags'] = $this->language->get('text_tags_' . $data['type_data']);
		
		$data['entry_data'] = $this->language->get('entry_' . $data['type_data']);
		
		$data['manager_product_seo_template_meta_title'] = $this->config->get('manager_product_seo_template_meta_title');
		$data['manager_product_seo_template_meta_h1'] = $this->config->get('manager_product_seo_template_meta_h1');
		$data['manager_product_seo_template_meta_description'] = $this->config->get('manager_product_seo_template_meta_description');
		$data['manager_product_seo_template_meta_keyword'] = $this->config->get('manager_product_seo_template_meta_keyword');
		$data['manager_product_seo_template_tag'] = $this->config->get('manager_product_seo_template_tag');
		
		$data['presence_column_meta_h1'] = $this->model_catalog_manager_product->getProductPresenceMetaH1();
		$data['presence_column_main_category'] = $this->model_catalog_manager_product->getProductPresenceMainCategory();
		
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
		
		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['manager_product_meta_title_required'] = $this->config->get('manager_product_meta_title_required');		
		$data['manager_product_model_required'] = $this->config->get('manager_product_model_required');
		
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		
		$this->load->model('localisation/length_class');
		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();	
		
		$this->load->model('localisation/weight_class');	
		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
		
		$this->load->model('customer/customer_group');		
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		
		$data['option_signs'] = array();
		
		$option_signs = $this->config->get('manager_product_option_signs');
		
		if (isset($option_signs)) {
			foreach ($option_signs as $option_sign) {
				$data['option_signs'][] = array(
					'name'  => $option_sign['name'],
					'value' => $option_sign['value']
				);
			}
		}
		
		$data['action'] = $this->url->link('extension/module/manager_product/updateProductMassData', 'user_token=' . $this->session->data['user_token'] . '&type_data=' . $data['type_data'], true);
		
		$this->response->setOutput($this->load->view('catalog/manager_product/product_mass', $data));
	}
	
	public function updateProductMassData() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$type_data = $this->request->get['type_data'];
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}

			if ($type_data == 'name') {
				foreach ($this->request->post['product_description'] as $language_id => $value) {
					if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
						$json['error'] = $this->language->get('error_name');
					}
				}
			} elseif ($type_data == 'meta_title' || $type_data == 'meta_h1' || $type_data == 'meta_description' || $type_data == 'meta_keyword' || $type_data == 'tag') {
				if (!$this->request->post['generator_meta_data']) {
					if ($type_data == 'meta_title') {
						if ($this->config->get('manager_product_meta_title_required')) {
							foreach ($this->request->post['product_description'] as $language_id => $value) {
								if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
									$json['error'] = $this->language->get('error_meta_title');
								}
							}
						}
					}
				} else {
					if (empty($this->request->post['manager_product_seo_template_' . $type_data])) {
						$json['error'] = $this->language->get('error_template');
					}
				}
			} elseif ($type_data == 'price') {
				if ($this->request->post['price_method'] == 'divide' && $this->request->post['price_value'] <= 0) {
					$json['error'] = $this->language->get('error_by_zero');
				}
			} elseif ($type_data == 'quantity') {
				if ($this->request->post['quantity_method'] == 'divide' && $this->request->post['quantity_value'] <= 0) {
					$json['error'] = $this->language->get('error_by_zero');
				}
			} elseif ($type_data == 'minimum') {
				if (empty($this->request->post['minimum']) || $this->request->post['minimum'] <= 0) {
					$json['error'] = $this->language->get('error_min_quantity');
				}
			} elseif ($type_data == 'mass_model') {
				if (isset($this->request->post['status_model'])) {
					if ($this->config->get('manager_product_model_required')) {
						if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
							$json['error'] = $this->language->get('error_model');
						}
					}
				}
			} elseif ($type_data == 'edit_discount') {
				if ($this->request->post['ds_quantity_method'] == 'divide' && $this->request->post['discount_quantity'] <= 0) {
					$json['error']['warning'] = $this->language->get('error_by_zero');
				}
				
				if ($this->request->post['discount_quantity'] != '') {
					if ($this->request->post['ds_quantity_method'] == '') {
						$json['error']['discount_quantity'] = $this->language->get('error_request_method');
					}
				}
				
				if ($this->request->post['discount_priority'] != '') {
					if ($this->request->post['ds_priority_method'] == '') {
						$json['error']['discount_priority'] = $this->language->get('error_request_method');
					}
				}
				
				if ($this->request->post['ds_price_method'] == 'divide' && $this->request->post['discount_price_value'] <= 0) {
					$json['error']['warning'] = $this->language->get('error_by_zero');
				}
				
				if ($this->request->post['discount_price_value'] != '') {
					if ($this->request->post['ds_price_method'] == '') {
						$json['error']['price_value'] = $this->language->get('error_request_method');
					}
					if ($this->request->post['ds_price_type'] == '') {
						$json['error']['price_info'] = $this->language->get('error_request_type');
					}
				}
			} elseif ($type_data == 'edit_special') {
				if ($this->request->post['special_priority'] != '') {
					if ($this->request->post['sp_priority_method'] == '') {
						$json['error']['special_priority'] = $this->language->get('error_request_method');
					}
				}
				
				if ($this->request->post['sp_price_method'] == 'divide' && $this->request->post['special_price_value'] <= 0) {
					$json['error']['warning'] = $this->language->get('error_by_zero');
				}
				
				if ($this->request->post['special_price_value'] != '') {
					if ($this->request->post['sp_price_method'] == '') {
						$json['error']['price_value'] = $this->language->get('error_request_method');
					}
					if ($this->request->post['sp_price_type'] == '') {
						$json['error']['price_info'] = $this->language->get('error_request_type');
					}
				}
			}

			if (!isset($json['error'])) {			
				if (isset($this->request->post['selected'])) {
					$sort_order = false;
					$sku_start = false;
					$upc_start = false;
					$ean_start = false;
					$jan_start = false;
					$isbn_start = false;
					$mpn_start = false;
					
					if ($type_data == 'sort_order') {
						$index = 0;
					}
					
					if ($type_data == 'code') {
						$sku = !empty($this->request->post['sku_start']) ? $this->request->post['sku_start'] : '100000';
						$upc = !empty($this->request->post['upc_start']) ? $this->request->post['upc_start'] : '100000';
						$ean = !empty($this->request->post['ean_start']) ? $this->request->post['ean_start'] : '100000';
						$jan = !empty($this->request->post['jan_start']) ? $this->request->post['jan_start'] : '100000';
						$isbn = !empty($this->request->post['isbn_start']) ? $this->request->post['isbn_start'] : '100000';
						$mpn = !empty($this->request->post['mpn_start']) ? $this->request->post['mpn_start'] : '100000';
					}
					
					foreach ($this->request->post['selected'] as $product_id) {
						if ($type_data == 'sort_order') {
							if ($this->request->post['sort_order_method'] == 'start') {
								$sort_order = (int)$this->request->post['sort_order'] + $index++;
							} else {
								$sort_order = (int)$this->request->post['sort_order'];
							}
						}
						
						if ($type_data == 'code') {							
							$sku_start = $sku++;
							$upc_start = $upc++;
							$ean_start = $ean++;
							$jan_start = $jan++;
							$isbn_start = $isbn++;
							$mpn_start = $mpn++;
						}
								
						$this->saveProductMassData($product_id, $this->request->post, $type_data, $sort_order, $sku_start, $upc_start, $ean_start, $jan_start, $isbn_start, $mpn_start);
					}
				}

				$json['success'] = $this->language->get('text_success_data');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function saveProductMassData($product_id, $data, $type_data, $sort_order, $sku_start, $upc_start, $ean_start, $jan_start, $isbn_start, $mpn_start){
		$this->load->model('catalog/manager_product');
		
		if (($type_data == 'name') || ($type_data == 'description')) {
			$this->model_catalog_manager_product->editProductGeneralData($product_id, $data, $type_data);
		} elseif (($type_data == 'meta_title') || ($type_data == 'meta_h1') || ($type_data == 'meta_description') || ($type_data == 'meta_keyword') || ($type_data == 'tag')) {
			if ($data['generator_meta_data']) {
				$this->load->model('setting/setting');
				
				$this->model_setting_setting->editSetting('manager_product_seo_template', $data);
				
				$this->generateMassMetaData($product_id, $type_data, $this->request->post['manager_product_seo_template_' . $type_data]);
			} else {
				$this->model_catalog_manager_product->editProductGeneralData($product_id, $data, $type_data);
			}
		} elseif ($type_data == 'image') {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		} elseif ($type_data == 'price') {
			$this->saveMassProductPrice($product_id, $data);
		} elseif ($type_data == 'quantity') {
			$this->saveMassProductQuantity($product_id, $data);
		} elseif ($type_data == 'minimum') {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET minimum = '" . (float)$data['minimum'] . "' WHERE product_id = '" . (int)$product_id . "'");
		} elseif ($type_data == 'mass_model') {
			$this->model_catalog_manager_product->saveMassProductModel($product_id, $data);
		} elseif ($type_data == 'keyword') {		
			$this->massGnerateSeoUrl($product_id, $data);
		} elseif ($type_data == 'code') {
			$this->saveMassProductCode($product_id, $data, $sku_start, $upc_start, $ean_start, $jan_start, $isbn_start, $mpn_start);
		} elseif ($type_data == 'mass_status') {
			$this->model_catalog_manager_product->saveMassProductDateAvailable($product_id, $data);
		} elseif ($type_data == 'mass_shipping') {
			$this->model_catalog_manager_product->saveMassProductStock($product_id, $data);
		} elseif ($type_data == 'size') {
			$this->model_catalog_manager_product->saveMassProductWeight($product_id, $data);
		} elseif ($type_data == 'sort_order') {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET sort_order = '" . (int)$sort_order . "' WHERE product_id = '" . (int)$product_id . "'");
		} elseif ($type_data == 'manufacturer') {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET manufacturer_id = '" . (int)$data['manufacturer_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
		} elseif ($type_data == 'product_category') {
			$this->saveMassProductCategories($product_id, $data);
		} elseif ($type_data == 'filter') {
			$this->saveMassProductFilter($product_id, $data);
		} elseif ($type_data == 'store') {
			$this->saveMassProductStore($product_id, $data);
		} elseif ($type_data == 'download') {
			$this->saveMassProductDownload($product_id, $data);
		} elseif ($type_data == 'related') {
			$this->saveMassProductRelated($product_id, $data);
		} elseif ($type_data == 'add_discount') {
			$this->saveMassProductAddDiscount($product_id, $data);
		} elseif ($type_data == 'edit_discount') {
			$this->saveMassProductEditDiscount($product_id, $data);
		} elseif ($type_data == 'add_special') {
			$this->saveMassProductAddSpecial($product_id, $data);
		} elseif ($type_data == 'edit_special') {
			$this->saveMassProductEditSpecial($product_id, $data);
		} elseif ($type_data == 'attributes') {
			$this->saveMassProductAttributes($product_id, $data);
		} elseif ($type_data == 'options') {
			$this->saveMassProductOptions($product_id, $data);
		} elseif ($type_data == 'additional_image') {
			$this->saveMassProductImages($product_id, $data);
		} elseif ($type_data == 'reward') {
			$this->saveMassProductReward($product_id, $data);
		} elseif ($type_data == 'layout') {
			$this->saveMassProductLayout($product_id, $data);
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
	}
	
	// Save Mass Product Price
	public function getProductCurrentPrice($product_id) {
		$row = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'");
		
		if(isset($row->row['price'])){
			return $row->row['price'];
		}
	}
	
	public function saveMassProductPrice($product_id, $data) {
		$current_price = $this->getProductCurrentPrice($product_id);
		
		if ($data['price_method'] == 'minus') {
			if ($data['price_type'] == 'percent') {
				$price = $current_price - ($current_price * (float)$data['price_value'] / 100);
			} else {
				$price = $current_price - (float)$data['price_value'];
			}
		} elseif ($data['price_method'] == 'plus') {
			if ($data['price_type'] == 'percent') {
				$price = $current_price + ($current_price * (float)$data['price_value'] / 100);
			} else {
				$price = $current_price + (float)$data['price_value'];
			}
		} elseif ($data['price_method'] == 'multiply') {
			if ($data['price_type'] == 'percent') {
				$price = $current_price * ($current_price * (float)$data['price_value'] / 100);
			} else {
				$price = $current_price * (float)$data['price_value'];
			}
		} elseif ($data['price_method'] == 'divide') {
			if ($data['price_type'] == 'percent') {
				$price = $current_price / ($current_price * (float)$data['price_value'] / 100);
			} else {
				$price = $current_price / (float)$data['price_value'];
			}
		} elseif ($data['price_method'] == 'equality') {
			$price = (float)$data['price_value'];
		}

		$this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)$price . "' WHERE product_id = '" . (int)$product_id . "'");
		
		$this->load->model('catalog/manager_product');
		
		if (isset($data['apply_special'])) {			
			$product_specials = $this->model_catalog_manager_product->getProductSpecials($product_id);

			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

			foreach ($product_specials as $product_special) {
				$current_price_special = $product_special['price'];
				
				if ($data['price_method'] == 'minus') {
					if ($data['price_type'] == 'percent') {
						$price_special = $current_price_special - ($current_price_special * (float)$data['price_value'] / 100);
					} else {
						$price_special = $current_price_special - (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'plus') {
					if ($data['price_type'] == 'percent') {
						$price_special = $current_price_special + ($current_price_special * (float)$data['price_value'] / 100);
					} else {
						$price_special = $current_price_special + (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'multiply') {
					if ($data['price_type'] == 'percent') {
						$price_special = $current_price_special * ($current_price_special * (float)$data['price_value'] / 100);
					} else {
						$price_special = $current_price_special * (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'divide') {
					if ($data['price_type'] == 'percent') {
						$price_special = $current_price_special / ($current_price_special * (float)$data['price_value'] / 100);
					} else {
						$price_special = $current_price_special / (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'equality') {
					$price_special = (float)$data['price_value'];
				}
					
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$price_special . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			} 
		}
		
		if (isset($data['apply_discount'])) {			
			$product_discounts = $this->model_catalog_manager_product->getProductDiscounts($product_id);
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

			foreach ($product_discounts as $product_discount) {
				$current_price_discount = $product_discount['price'];
				
				if ($data['price_method'] == 'minus') {
					if ($data['price_type'] == 'percent') {
						$price_discount = $current_price_discount - ($current_price_discount * (float)$data['price_value'] / 100);
					} else {
						$price_discount = $current_price_discount - (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'plus') {
					if ($data['price_type'] == 'percent') {
						$price_discount = $current_price_discount + ($current_price_discount * (float)$data['price_value'] / 100);
					} else {
						$price_discount = $current_price_discount + (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'multiply') {
					if ($data['price_type'] == 'percent') {
						$price_discount = $current_price_discount * ($current_price_discount * (float)$data['price_value'] / 100);
					} else {
						$price_discount = $current_price_discount * (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'divide') {
					if ($data['price_type'] == 'percent') {
						$price_discount = $current_price_discount / ($current_price_discount * (float)$data['price_value'] / 100);
					} else {
						$price_discount = $current_price_discount / (float)$data['price_value'];
					}
				} elseif ($data['price_method'] == 'equality') {
					$price_discount = (float)$data['price_value'];
				}
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$price_discount . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	// Save Mass Product Quantity
	public function getProductCurrentQuantity($product_id) {
		$row = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'");
		if(isset($row->row['quantity'])){
			return $row->row['quantity'];
		}
	}
	
	public function saveMassProductQuantity($product_id, $data) {
		$current_quantity = $this->getProductCurrentQuantity($product_id);
		
		if ($data['quantity_method'] == 'minus') {
			$quantity = $current_quantity - (float)$data['quantity_value'];
		} elseif ($data['quantity_method'] == 'plus') {
			$quantity = $current_quantity + (float)$data['quantity_value'];
		} elseif ($data['quantity_method'] == 'multiply') {
			$quantity = $current_quantity * (float)$data['quantity_value'];
		} elseif ($data['quantity_method'] == 'divide') {
			$quantity = $current_quantity / (float)$data['quantity_value'];
		} elseif ($data['quantity_method'] == 'equality') {
			$quantity = (float)$data['quantity_value'];
		}

		$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (float)$quantity . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	public function saveMassProductCode($product_id, $data, $sku_start, $upc_start, $ean_start, $jan_start, $isbn_start, $mpn_start) {
		$this->load->model('catalog/manager_product');
		
		$product_info = $this->model_catalog_manager_product->getProduct($product_id);
		
		if ($data['code_generator'] == 'custom') {
			$sku = isset($data['status_sku']) ? $data['sku'] : $product_info['sku'];
			$upc = isset($data['status_upc']) ? $data['upc'] : $product_info['upc'];
			$ean = isset($data['status_ean']) ? $data['ean'] : $product_info['ean'];
			$jan = isset($data['status_jan']) ? $data['jan'] : $product_info['jan'];
			$mpn = isset($data['status_mpn']) ? $data['mpn'] : $product_info['mpn'];
			$isbn = isset($data['status_isbn']) ? $data['isbn'] : $product_info['isbn'];

			$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $this->db->escape($sku) . "', upc = '" . $this->db->escape($upc) . "', ean = '" . $this->db->escape($ean) . "', jan = '" . $this->db->escape($jan) . "', isbn = '" . $this->db->escape($isbn) . "', mpn = '" . $this->db->escape($mpn) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		} else {
			if(isset($data['status_generate_sku'])) {			
				$sku_prefix = !empty($data['sku_prefix']) ? $data['sku_prefix'] : 'SKU';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = CONCAT_WS('-', '" . $sku_prefix . "', REPLACE(FORMAT('" . $this->db->escape($sku_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $this->db->escape($product_info['sku']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
			
			if(isset($data['status_generate_upc'])) {			
				$upc_prefix = !empty($data['upc_prefix']) ? $data['upc_prefix'] : 'UPC';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = CONCAT_WS('-', '" . $upc_prefix . "', REPLACE(FORMAT('" . $this->db->escape($upc_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = '" . $this->db->escape($product_info['upc']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
			
			if(isset($data['status_generate_ean'])) {			
				$ean_prefix = !empty($data['ean_prefix']) ? $data['ean_prefix'] : 'EAN';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET ean = CONCAT_WS('-', '" . $ean_prefix . "', REPLACE(FORMAT('" . $this->db->escape($ean_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET ean = '" . $this->db->escape($product_info['ean']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
			
			if(isset($data['status_generate_jan'])) {			
				$jan_prefix = !empty($data['jan_prefix']) ? $data['jan_prefix'] : 'JAN';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET jan = CONCAT_WS('-', '" . $jan_prefix . "', REPLACE(FORMAT('" . $this->db->escape($jan_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '" . $this->db->escape($product_info['jan']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
			
			if(isset($data['status_generate_isbn'])) {			
				$isbn_prefix = !empty($data['isbn_prefix']) ? $data['isbn_prefix'] : 'ISBN';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET isbn = CONCAT_WS('-', '" . $isbn_prefix . "', REPLACE(FORMAT('" . $this->db->escape($isbn_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET isbn = '" . $this->db->escape($product_info['isbn']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
			
			if(isset($data['status_generate_mpn'])) {			
				$mpn_prefix = !empty($data['mpn_prefix']) ? $data['mpn_prefix'] : 'MPN';
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET mpn = CONCAT_WS('-', '" . $mpn_prefix . "', REPLACE(FORMAT('" . $this->db->escape($mpn_start) . "', 0), ',','-')) WHERE product_id = '" . (int)$product_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "product SET mpn = '" . $this->db->escape($product_info['mpn']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
			}
		}
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Categories
	public function saveMassProductCategories($product_id, $data) {
		if ($data['category_method_edit'] == 'all_new') {		
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_category'])) {
				foreach ($data['product_category'] as $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
				}
			}
			
			$result_main_category = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_to_category' AND COLUMN_NAME = 'main_category'");
		
			if ($result_main_category->num_rows) {		
				if (isset($data['main_category_id']) && $data['main_category_id'] > 0) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 1");
				} elseif (isset($data['product_category'])) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['product_category'][0] . "'");
				}
			}
		}
		
		if ($data['category_method_edit'] == 'add_selected' || $data['category_method_edit'] == 'delete_selected') {
			if (isset($data['product_category'])) {
				foreach ($data['product_category'] as $key => $category_id) {
					if ($data['category_method_edit'] == 'add_selected') {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$category_id . "'");
					
						if (!$query->num_rows) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
						
							$result_main_category = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_to_category' AND COLUMN_NAME = 'main_category'");
		
							if ($result_main_category->num_rows) {		
								if (isset($data['main_category_id']) && $data['main_category_id'] > 0) {
									$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 1");
								}
							}
						}
					}
					
					if ($data['category_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$category_id . "'");
					
						$result_main_category = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_to_category' AND COLUMN_NAME = 'main_category'");
		
						if ($result_main_category->num_rows) {		
							if (isset($data['main_category_id']) && $data['main_category_id'] > 0) {
								$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 0");
							}
						}
					}
				}
			}			
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	// Save Mass Product Filter
	public function saveMassProductFilter($product_id, $data) {
		if ($data['filter_method_edit'] == 'all_new') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}
			}
		}
		
		if ($data['filter_method_edit'] == 'add_selected' || $data['filter_method_edit'] == 'delete_selected') {
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					if ($data['filter_method_edit'] == 'add_selected') {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "' AND filter_id = '" . (int)$filter_id . "'");
					
						if (!$query->num_rows) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
						}
					}
					
					if ($data['filter_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "' AND filter_id = '" . (int)$filter_id . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Store
	public function saveMassProductStore($product_id, $data) {
		if ($data['store_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_store'])) {
				foreach ($data['product_store'] as $store_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
				}
			}
		}
		
		if ($data['store_method_edit'] == 'add_selected' || $data['store_method_edit'] == 'delete_selected') {
			if (isset($data['product_store'])) {
				foreach ($data['product_store'] as $store_id) {
					if ($data['store_method_edit'] == 'add_selected') {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$store_id . "'");
					
						if (!$query->num_rows) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
						}
					}
					
					if ($data['store_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$store_id . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Download
	public function saveMassProductDownload($product_id, $data) {
		if ($data['download_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_download'])) {
				foreach ($data['product_download'] as $download_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				}
			}
		}
		
		if ($data['download_method_edit'] == 'add_selected' || $data['download_method_edit'] == 'delete_selected') {
			if (isset($data['product_download'])) {
				foreach ($data['product_download'] as $download_id) {
					if ($data['download_method_edit'] == 'add_selected') {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "' AND download_id = '" . (int)$download_id . "'");
					
						if (!$query->num_rows) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
						}
					}
					
					if ($data['download_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "' AND download_id = '" . (int)$download_id . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Related
	public function saveMassProductRelated($product_id, $data) {
		if ($data['related_method_edit'] == 'all_new') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

			if (isset($data['product_related'])) {
				foreach ($data['product_related'] as $related_id) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
				}
			}
		}
		
		if ($data['related_method_edit'] == 'add_selected' || $data['related_method_edit'] == 'delete_selected') {
			if (isset($data['product_related'])) {
				foreach ($data['product_related'] as $related_id) {
					if ($data['related_method_edit'] == 'add_selected') {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "' AND related_id = '" . (int) $related_id . "'");
						if (!$query->num_rows) {
							if ($product_id != $related_id) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int) $product_id . "', related_id = '" . (int) $related_id . "'");
							}
						}
						
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $related_id . "' AND related_id = '" . (int) $product_id . "'");
						if (!$query->num_rows) {
							if ($related_id != $product_id) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int) $related_id . "', related_id = '" . (int) $product_id . "'");
							}
						}
					}
					
					if ($data['related_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
					}
				}
			}
		}
			
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Add Discount
	public function saveMassProductAddDiscount($product_id, $data) {
		$this->load->model('catalog/manager_product');
					
		$product_info = $this->model_catalog_manager_product->getProduct($product_id);
						
		if ($data['discount_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_discount'])) {
				foreach ($data['product_discount'] as $product_discount) {
					if ($product_discount['add_type'] == 'amount') {					
						$product_discount_price = $product_discount['price'];
					}
					
					if (($product_discount['add_type'] == 'amount_plus_price') || ($product_discount['add_type'] == 'amount_minus_price') || ($product_discount['add_type'] == 'amount_plus_percent') || ($product_discount['add_type'] == 'amount_minus_percent')) {
						
						if ($product_discount['add_type'] == 'amount_plus_price') {
							$product_discount_price = $product_info['price'] + $product_discount['price'];
						} elseif ($product_discount['add_type'] == 'amount_minus_price') {
							$product_discount_price = $product_info['price'] - $product_discount['price'];
						} elseif ($product_discount['add_type'] == 'amount_plus_percent') {
							$product_discount_price = $product_info['price'] + ($product_info['price'] * $product_discount['price'] / 100);
						} elseif ($product_discount['add_type'] == 'amount_minus_percent') {
							$product_discount_price = $product_info['price'] - ($product_info['price'] * $product_discount['price'] / 100);
						}
					}
							
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount_price . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
				}
			}
		}
		
		if ($data['discount_method_edit'] == 'add_selected' || $data['discount_method_edit'] == 'customer_group' || $data['discount_method_edit'] == 'quantity' || $data['discount_method_edit'] == 'priority' || $data['discount_method_edit'] == 'price' || $data['discount_method_edit'] == 'date_start' || $data['discount_method_edit'] == 'date_end') {
			if (isset($data['product_discount'])) {
				foreach ($data['product_discount'] as $product_discount) {
					if ($data['discount_method_edit'] == 'add_selected') {						
						if ($product_discount['add_type'] == 'amount') {					
							$product_discount_price = $product_discount['price'];
						} elseif ($product_discount['add_type'] == 'amount_plus_price') {
							$product_discount_price = $product_info['price'] + $product_discount['price'];
						} elseif ($product_discount['add_type'] == 'amount_minus_price') {
							$product_discount_price = $product_info['price'] - $product_discount['price'];
						} elseif ($product_discount['add_type'] == 'amount_plus_percent') {
							$product_discount_price = $product_info['price'] + ($product_info['price'] * $product_discount['price'] / 100);
						} elseif ($product_discount['add_type'] == 'amount_minus_percent') {
							$product_discount_price = $product_info['price'] - ($product_info['price'] * $product_discount['price'] / 100);
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount_price . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
					}
					
					if ($data['discount_method_edit'] == 'customer_group') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$product_discount['customer_group_id'] . "'");
					} elseif ($data['discount_method_edit'] == 'quantity') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND quantity = '" . (int)$product_discount['quantity'] . "'");
					} elseif ($data['discount_method_edit'] == 'priority') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND priority = '" . (int)$product_discount['priority'] . "'");
					} elseif ($data['discount_method_edit'] == 'price') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND price = '" . (float)$product_discount['price'] . "'");
					} elseif ($data['discount_method_edit'] == 'date_start') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND date_start = '" . $this->db->escape($product_discount['date_start']) . "'");
					} elseif ($data['discount_method_edit'] == 'date_end') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
					}					
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Edit Discount
	public function saveMassProductEditDiscount($product_id, $data) {
		$this->load->model('catalog/manager_product');
					
		$product_discounts = $this->model_catalog_manager_product->getProductDiscounts($product_id);
			
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		foreach ($product_discounts as $product_discount) {			
			if (!empty($data['discount_customer_group_id'])) {
				$customer_group_id = $data['discount_customer_group_id'];
			} else {
				$customer_group_id = $product_discount['customer_group_id'];
			}			
			
			$current_quantity_discount = $product_discount['quantity'];
			
			if($data['discount_quantity'] != '') {
				if ($data['ds_quantity_method'] == 'minus') {
					$quantity_discount = $current_quantity_discount - $data['discount_quantity'];
				} elseif ($data['ds_quantity_method'] == 'plus') {
					$quantity_discount = $current_quantity_discount + $data['discount_quantity'];
				} elseif ($data['ds_quantity_method'] == 'multiply') {
					$quantity_discount = $current_quantity_discount * $data['discount_quantity'];
				} elseif ($data['ds_quantity_method'] == 'divide') {
					$quantity_discount = $current_quantity_discount / $data['discount_quantity'];
				} elseif ($data['ds_quantity_method'] == 'equality') {
					$quantity_discount = $data['discount_quantity'];
				}
			} else {
				$quantity_discount = $current_quantity_discount;
			}

			$current_priority_discount = $product_discount['priority'];
			
			if($data['discount_priority'] != '') {
				if ($data['ds_priority_method'] == 'minus') {
					$priority_discount = $current_priority_discount - $data['discount_priority'];
				} elseif ($data['ds_priority_method'] == 'plus') {
					$priority_discount = $current_priority_discount + $data['discount_priority'];
				} elseif ($data['ds_priority_method'] == 'equality') {
					$priority_discount = $data['discount_priority'];
				}
			} else {
				$priority_discount = $current_priority_discount;
			}
			
			$current_price_discount = $product_discount['price'];
				
			if($data['discount_price_value'] != '') {
				if ($data['ds_price_method'] == 'minus') {
					if ($data['ds_price_type'] == 'percent') {
						$price_discount = $current_price_discount - ($current_price_discount * $data['discount_price_value'] / 100);
					} else {
						$price_discount = $current_price_discount - $data['discount_price_value'];
					}
				} elseif ($data['ds_price_method'] == 'plus') {
					if ($data['ds_price_type'] == 'percent') {
						$price_discount = $current_price_discount + ($current_price_discount * $data['discount_price_value'] / 100);
					} else {
						$price_discount = $current_price_discount + $data['discount_price_value'];
					}
				} elseif ($data['ds_price_method'] == 'multiply') {
					if ($data['ds_price_type'] == 'percent') {
						$price_discount = $current_price_discount * ($current_price_discount * $data['discount_price_value'] / 100);
					} else {
						$price_discount = $current_price_discount * $data['discount_price_value'];
					}
				} elseif ($data['ds_price_method'] == 'divide') {
					if ($data['ds_price_type'] == 'percent') {
						$price_discount = $current_price_discount / ($current_price_discount * $data['discount_price_value'] / 100);
					} else {
						$price_discount = $current_price_discount / $data['discount_price_value'];
					}
				} elseif ($data['ds_price_method'] == 'equality') {
					$price_discount = $data['discount_price_value'];
				}
			} else {
				$price_discount = $current_price_discount;
			}
			
			if(!empty($data['discount_date_start'])) {
				$discount_date_start = $data['discount_date_start'];
			} else {
				$discount_date_start = $product_discount['date_start'];
			}	

			if(!empty($data['discount_date_end'])) {
				$discount_date_end = $data['discount_date_end'];
			} else {
				$discount_date_end = $product_discount['date_end'];
			}
				
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', quantity = '" . (int)$quantity_discount . "', priority = '" . (int)$priority_discount . "', price = '" . (float)$price_discount . "', date_start = '" . $this->db->escape($discount_date_start) . "', date_end = '" . $this->db->escape($discount_date_end) . "'");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	// Save Mass Product Add Special
	public function saveMassProductAddSpecial($product_id, $data) {
		$this->load->model('catalog/manager_product');
					
		$product_info = $this->model_catalog_manager_product->getProduct($product_id);
						
		if ($data['special_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_special'])) {
				foreach ($data['product_special'] as $product_special) {
					if ($product_special['add_type'] == 'amount') {					
						$product_special_price = $product_special['price'];
					}
					
					if (($product_special['add_type'] == 'amount_plus_price') || ($product_special['add_type'] == 'amount_minus_price') || ($product_special['add_type'] == 'amount_plus_percent') || ($product_special['add_type'] == 'amount_minus_percent')) {
						
						if ($product_special['add_type'] == 'amount_plus_price') {
							$product_special_price = $product_info['price'] + $product_special['price'];
						} elseif ($product_special['add_type'] == 'amount_minus_price') {
							$product_special_price = $product_info['price'] - $product_special['price'];
						} elseif ($product_special['add_type'] == 'amount_plus_percent') {
							$product_special_price = $product_info['price'] + ($product_info['price'] * $product_special['price'] / 100);
						} elseif ($product_special['add_type'] == 'amount_minus_percent') {
							$product_special_price = $product_info['price'] - ($product_info['price'] * $product_special['price'] / 100);
						}
					}
							
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special_price . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
				}
			}
		}
		
		if ($data['special_method_edit'] == 'add_selected' || $data['special_method_edit'] == 'customer_group' || $data['special_method_edit'] == 'priority' || $data['special_method_edit'] == 'price' || $data['special_method_edit'] == 'date_start' || $data['special_method_edit'] == 'date_end') {
			if (isset($data['product_special'])) {
				foreach ($data['product_special'] as $product_special) {
					if ($data['special_method_edit'] == 'add_selected') {						
						if ($product_special['add_type'] == 'amount') {					
							$product_special_price = $product_special['price'];
						} elseif ($product_special['add_type'] == 'amount_plus_price') {
							$product_special_price = $product_info['price'] + $product_special['price'];
						} elseif ($product_special['add_type'] == 'amount_minus_price') {
							$product_special_price = $product_info['price'] - $product_special['price'];
						} elseif ($product_special['add_type'] == 'amount_plus_percent') {
							$product_special_price = $product_info['price'] + ($product_info['price'] * $product_special['price'] / 100);
						} elseif ($product_special['add_type'] == 'amount_minus_percent') {
							$product_special_price = $product_info['price'] - ($product_info['price'] * $product_special['price'] / 100);
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special_price . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
					}
					
					if ($data['special_method_edit'] == 'customer_group') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$product_special['customer_group_id'] . "'");
					} elseif ($data['special_method_edit'] == 'priority') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND priority = '" . (int)$product_special['priority'] . "'");
					} elseif ($data['special_method_edit'] == 'price') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND price = '" . (float)$product_special['price'] . "'");
					} elseif ($data['special_method_edit'] == 'date_start') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND date_start = '" . $this->db->escape($product_special['date_start']) . "'");
					} elseif ($data['special_method_edit'] == 'date_end') {	
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND date_end = '" . $this->db->escape($product_special['date_end']) . "'");
					}					
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Edit Special
	public function saveMassProductEditSpecial($product_id, $data) {
		$this->load->model('catalog/manager_product');
					
		$product_specials = $this->model_catalog_manager_product->getProductSpecials($product_id);
			
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		foreach ($product_specials as $product_special) {			
			if (!empty($data['special_customer_group_id'])) {
				$customer_group_id = $data['special_customer_group_id'];
			} else {
				$customer_group_id = $product_special['customer_group_id'];
			}			

			$current_priority_special = $product_special['priority'];
			
			if($data['special_priority'] != '') {
				if ($data['sp_priority_method'] == 'minus') {
					$priority_special = $current_priority_special - $data['special_priority'];
				} elseif ($data['sp_priority_method'] == 'plus') {
					$priority_special = $current_priority_special + $data['special_priority'];
				} elseif ($data['sp_priority_method'] == 'equality') {
					$priority_special = $data['special_priority'];
				}
			} else {
				$priority_special = $current_priority_special;
			}
			
			$current_price_special = $product_special['price'];
				
			if($data['special_price_value'] != '') {
				if ($data['sp_price_method'] == 'minus') {
					if ($data['sp_price_type'] == 'percent') {
						$price_special = $current_price_special - ($current_price_special * $data['special_price_value'] / 100);
					} else {
						$price_special = $current_price_special - $data['special_price_value'];
					}
				} elseif ($data['sp_price_method'] == 'plus') {
					if ($data['sp_price_type'] == 'percent') {
						$price_special = $current_price_special + ($current_price_special * $data['special_price_value'] / 100);
					} else {
						$price_special = $current_price_special + $data['special_price_value'];
					}
				} elseif ($data['sp_price_method'] == 'multiply') {
					if ($data['sp_price_type'] == 'percent') {
						$price_special = $current_price_special * ($current_price_special * $data['special_price_value'] / 100);
					} else {
						$price_special = $current_price_special * $data['special_price_value'];
					}
				} elseif ($data['sp_price_method'] == 'divide') {
					if ($data['sp_price_type'] == 'percent') {
						$price_special = $current_price_special / ($current_price_special * $data['special_price_value'] / 100);
					} else {
						$price_special = $current_price_special / $data['special_price_value'];
					}
				} elseif ($data['sp_price_method'] == 'equality') {
					$price_special = $data['special_price_value'];
				}
			} else {
				$price_special = $current_price_special;
			}
			
			if(!empty($data['special_date_start'])) {
				$special_date_start = $data['special_date_start'];
			} else {
				$special_date_start = $product_special['date_start'];
			}	

			if(!empty($data['special_date_end'])) {
				$special_date_end = $data['special_date_end'];
			} else {
				$special_date_end = $product_special['date_end'];
			}
				
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '" . (int)$priority_special . "', price = '" . (float)$price_special . "', date_start = '" . $this->db->escape($special_date_start) . "', date_end = '" . $this->db->escape($special_date_end) . "'");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}
	
	// Save Mass Product Attributes
	public function saveMassProductAttributes($product_id, $data) {
		if ($data['attribute_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

			if (!empty($data['product_attribute'])) {
				foreach ($data['product_attribute'] as $product_attribute) {
					if ($product_attribute['attribute_id']) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

						foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
						}
					}
				}
			}
		}
		
		if ($data['attribute_method_edit'] == 'add_selected' || $data['attribute_method_edit'] == 'delete_selected') {
			if (!empty($data['product_attribute'])) {
				foreach ($data['product_attribute'] as $product_attribute) {
					if ($product_attribute['attribute_id']) {
						foreach ($product_attribute['product_attribute_description'] as $language_id => $attribute_description) {
							if ($data['attribute_method_edit'] == 'add_selected') {
								$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id  . "'");
							
								if (!$query->num_rows) {
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id  . "', text = '" . $this->db->escape($attribute_description['text']) . "'");
								} else {
									$this->db->query("UPDATE " . DB_PREFIX . "product_attribute SET text = '" . $this->db->escape($attribute_description['text']) . "' WHERE attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND product_id = '" . (int)$product_id . "' AND language_id  = '" . (int)$language_id . "'");
								}
							}
					
							if ($data['attribute_method_edit'] == 'delete_selected') {
								$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
							}
						}
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Options
	public function saveMassProductOptions($product_id, $data) {
		if ($data['option_method_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

			if (isset($data['product_option'])) {
				foreach ($data['product_option'] as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						if (isset($product_option['product_option_value'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

							$product_option_id = $this->db->getLastId();

							foreach ($product_option['product_option_value'] as $product_option_value) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						}
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
					}
				}
			}
		}
		
		if ($data['option_method_edit'] == 'add_selected' || $data['option_method_edit'] == 'delete_selected') {
			if (isset($data['product_option'])) {
				foreach ($data['product_option'] as $product_option) {
					if ($data['option_method_edit'] == 'add_selected') {
						if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
							
							$product_option_id = $this->db->getLastId();

							foreach ($product_option['product_option_value'] as $product_option_value) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						} else {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
						}
					}
					
					if ($data['option_method_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$product_option['option_id'] . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$product_option['option_id'] . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Product Images
	public function saveMassProductImages($product_id, $data) {
		if ($data['option_images_edit'] == 'all_new') {	
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

			if (isset($data['product_image'])) {
				foreach ($data['product_image'] as $product_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
				}
			}
		}
		
		if ($data['option_images_edit'] == 'add_selected' || $data['option_images_edit'] == 'delete_selected') {
			if (isset($data['product_image'])) {
				foreach ($data['product_image'] as $product_image) {
					if ($data['option_images_edit'] == 'add_selected') {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
					}
					
					if ($data['option_images_edit'] == 'delete_selected') {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND image = '" . $this->db->escape($product_image['image']) . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Reward
	public function saveMassProductReward($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET points = '" . (int)$data['points'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Save Mass Layout
	public function saveMassProductLayout($product_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		$this->cache->delete('product');
	}
	
	// Product Mass Delete Data
	public function massDelete() {
		$this->load->language('extension/module/manager_product');
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'extension/module/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}

			if (!isset($json['error'])) {			
				if (isset($this->request->post['selected'])) {
					foreach ($this->request->post['selected'] as $product_id) {
						if ($this->request->get['type_data'] == 'name') {					
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'description') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'meta_title') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_title = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'meta_h1') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_h1 = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'meta_description') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_description = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'meta_keyword') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_keyword = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'tag') {
							$this->db->query("UPDATE " . DB_PREFIX . "product_description SET tag = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'image') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'model') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'sku') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'upc') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'ean') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET ean = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'jan') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'isbn') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET isbn = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'mpn') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET mpn = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'location') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET location = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'price') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET price = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'tax_class') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET tax_class_id = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'quantity') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'minimum') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET minimum = '1' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'seo_url') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'date_available') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET date_available = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'dimension') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET length = '', width = '', height = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'weight') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET weight = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'sort_order') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET sort_order = '0' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'manufacturer') {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET manufacturer_id = '' WHERE product_id = '" . (int)$product_id . "'");	
						} elseif ($this->request->get['type_data'] == 'category') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'filter') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'store') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'download') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");				
						} elseif ($this->request->get['type_data'] == 'related') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'attribute') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'option') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'discount') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'special') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");			
						} elseif ($this->request->get['type_data'] == 'images') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'reward') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
							$this->db->query("UPDATE " . DB_PREFIX . "product SET points = '' WHERE product_id = '" . (int)$product_id . "'");
						} elseif ($this->request->get['type_data'] == 'layout') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
						}
						
						$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
							
						$this->cache->delete('product');
					}
				}

				$json['success'] = $this->language->get('text_success_data');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		
	// Auto Generate SEO URL
	public function generateAutoSeoUrl($product_id) {	
		$results = $this->getSeoUrlById($product_id);
		
		$template = '[product]';
		
		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
        
		foreach ($results as $result) {			
			$existing_keywords = $this->getExistingKeywords($result['store_id'], $result['language_id']);
			
			unset($existing_keywords['product_id=' . $result['product_id']]);
			
			$language_info = $this->model_localisation_language->getLanguage($result['language_id']);

			if (count($languages) > 1) {
				$tags = array(
					'[product]' => $result['name'] . '-' . substr($language_info['code'], 0, 2)
				);
			} else {
				$tags = array(
					'[product]' => $result['name']
				);
			}
				
			$existing_keyword = $keyword = $this->makeSlugs(strtr($template, $tags), 0, true);
				
			$index = 1;
				
			while (in_array($keyword, $existing_keywords)) {
				$keyword = $existing_keyword . '-' . $index++;
			}
				
			$existing_keywords[] = $keyword;
				
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND store_id = '" . (int)$result['store_id'] . "' AND language_id = '" . (int)$result['language_id'] . "'");
				
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$result['store_id'] . "', language_id = '" . (int)$result['language_id'] . "', query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
		}
			
		$this->cache->delete('seo_pro');
	}
	
	// Mass Generate SEO URL
	public function massGnerateSeoUrl($product_id, $data) {	
		$results = $this->getSeoUrlById($product_id);
		
		$template = '[product]';
		
		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
        
		foreach ($results as $result) {
            $existing_keywords = $this->getExistingKeywords($result['store_id'], $result['language_id']);
			
			unset($existing_keywords['product_id=' . $result['product_id']]);
			
			$language_info = $this->model_localisation_language->getLanguage($result['language_id']);

			if (count($languages) > 1) {
				if ($data['seo_url_generator'] == 'generator') {
					$tags = array(
						'[product]' => $result['name'] . '-' . substr($language_info['code'], 0, 2)
					);
				} else {
					$tags = array(
						'[product]' => $data['seo_url'] . '-' . substr($language_info['code'], 0, 2)
					);
				}
			} else {
				if ($data['seo_url_generator'] == 'generator') {
					$tags = array(
						'[product]' => $result['name']
					);
				} else {
					$tags = array(
						'[product]' => $data['seo_url']
					);
				}
			}
				
			$existing_keyword = $keyword = $this->makeSlugs(strtr($template, $tags), 0, true);
				
			$index = 1;
				
			while (in_array($keyword, $existing_keywords)) {
				$keyword = $existing_keyword . '-' . $index++;
			}
				
			$existing_keywords[] = $keyword;
		
			if ($data['type_seo_url'] == 'yes') {		
				$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND store_id = '" . (int)$result['store_id'] . "' AND language_id = '" . (int)$result['language_id'] . "'");
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$result['store_id'] . "', language_id = '" . (int)$result['language_id'] . "', query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
			} else {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND store_id = '" . (int)$result['store_id'] . "' AND language_id = '" . (int)$result['language_id'] . "'");
				
				if (!$query->num_rows) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND store_id = '" . (int)$result['store_id'] . "' AND language_id = '" . (int)$result['language_id'] . "'");
				
					$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$result['store_id'] . "', language_id = '" . (int)$result['language_id'] . "', query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
				}
			}
		}
			
		$this->cache->delete('seo_pro');
	}
	
	// Mass Generate Meta Title, Meta H1, Meta Description, Meta Keyword, Tag
	public function generateMassMetaData($product_id, $type_data, $template) {
		$results = $this->getMetaDataById($product_id);
		
        foreach ($results as $result) {
			$product_category = array();
			
			$product_categories = $this->getProductCategories($result['product_id'], $result['language_id']);
				
			foreach ($product_categories as $product_category) {
				$product_category[] = $product_category['name'];
			}

			$tags = array(
				'[product]'  	 => $result['name'],
				'[model]' 		 => $result['model'],
				'[manufacturer]' => $result['manufacturer'],
				'[category]' 	 => implode(',', $product_category),
				'[price]'	 	 => number_format((float)$result['price'], 2, '.', ''),
				'[sku]'			 => $result['sku'],
				'[upc]'			 => $result['upc'],
				'[ean]'			 => $result['ean'],
				'[jan]'			 => $result['jan'],
				'[isbn]'		 => $result['isbn'],
				'[mpn]'			 => $result['mpn'],
				'[location]'	 => $result['location'],
				'[description]'  => $result['description']
				
            );
			
            $seo_data = array();
			
            $keywords = explode(',', strtr($template, $tags));
            
            foreach ($keywords as $keyword) {
                $seo_data[] = $this->makeSlugss(trim($keyword), 0, false);
            }
			
            $seo_data = array_filter(array_unique($seo_data));
            $seo_data = implode(', ', $seo_data);
			
			if ($type_data != 'tag') {
				$seo_data = utf8_substr(strip_tags(html_entity_decode($seo_data, ENT_QUOTES, 'UTF-8')), 0, 255);
			} else {
				$seo_data = utf8_substr(strip_tags(html_entity_decode($seo_data, ENT_QUOTES, 'UTF-8')), 0, 5000);
			}
			
            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET " . $type_data . " = '" . $this->db->escape($seo_data) . "' WHERE product_id = " . (int)$result['product_id'] . " AND language_id = " . (int)$result['language_id']);
        }
		
		$this->cache->delete('product');
    }
	
	// Get Products Meta Data By Id
	private function getMetaDataById($product_id) {
		$result_table = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_description' AND COLUMN_NAME = 'meta_h1'");
		
		if ($result_table->num_rows) {
			$query = $this->db->query("SELECT p.product_id, pd.name, p.model, p.price, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.location, m.name as manufacturer, pd.description, pd.meta_keyword, pd.meta_description, pd.tag, pd.meta_title, pd.meta_h1, pd.language_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' ORDER BY pd.name ASC");
		} else {
			$query = $this->db->query("SELECT p.product_id, pd.name, p.model, p.price, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.location, m.name as manufacturer, pd.description, pd.meta_keyword, pd.meta_description, pd.tag, pd.meta_title, pd.language_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' ORDER BY pd.name ASC");
		}
        
		return $query->rows;
    }
	
	// Get Products Categories	
	private function getProductCategories($product_id, $language_id) {
        $query = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) INNER JOIN " . DB_PREFIX . "product_to_category pc ON (pc.category_id = c.category_id) WHERE cd.language_id = " . (int)$language_id . " AND pc.product_id = " . (int)$product_id . " ORDER BY c.sort_order, cd.name ASC");
        
		return $query->rows;
    }
	
	// Get Seo Url By Id Data
	private function getSeoUrlById($product_id) {
		$query = $this->db->query("SELECT p.product_id, pd.name, pts.store_id, pd.language_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store pts ON (p.product_id = pts.product_id) LEFT JOIN " . DB_PREFIX . "seo_url su ON (CONCAT('product_id=', p.product_id) = su.query) WHERE p.product_id = '" . (int)$product_id . "' ORDER BY p.sort_order, pd.name ASC");
		
		return $query->rows;
    }
	
	/**************************************************************************************************/
	/**************************************** SEO DATA ************************************************/
	/**************************************************************************************************/
	public function getExistingKeywords($store_id, $language_id) {
        $existing_keywords = array();
        
		$query = $this->db->query("SELECT query, keyword FROM " . DB_PREFIX . "seo_url WHERE store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "' ORDER BY query ASC");
       
	    foreach ($query->rows as $row) {
            $existing_keywords[$row['query']] = $row['keyword'];
        }
		
        return $existing_keywords;
    }

    private function my_str_split($string) {
        $slen = strlen($string);
        for ($i = 0; $i < $slen; $i++) {
            $sArray[$i] = $string[$i];
        }
        return $sArray;
    }

    function _transliteration_process($string, $unknown = '?', $source_langcode = NULL) {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
        static $tailBytes;

        if (!isset($tailBytes)) {
            $tailBytes = array();
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                } elseif ($n < 0xe0) {
                    $remaining = 1;
                } elseif ($n < 0xf0) {
                    $remaining = 2;
                } elseif ($n < 0xf8) {
                    $remaining = 3;
                } elseif ($n < 0xfc) {
                    $remaining = 4;
                } elseif ($n < 0xfe) {
                    $remaining = 5;
                } else {
                    $remaining = 0;
                }
                $tailBytes[chr($n)] = $remaining;
            }
        }
        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = '';
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {
                $result .= $str;
                continue;
            }
            $head = '';
            $chunk = strlen($str);
            $len = $chunk + 1;

            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $tailBytes[$c]) {
                    $sequence = $head = $c;
                    do {
                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {
                            $sequence .= $c;
                        } else {
                            if ($len == 0) {
                                $result .= $unknown;
                                break 2;
                            } else {
                                $result .= $unknown;
                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    } while (--$remaining);

                    $n = ord($head);
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    } elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    } elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64 + (ord($sequence[3]) - 128);
                    } elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128) * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    } elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2]) - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64 + (ord($sequence[5]) - 128);
                    }
                    $result .= $this->_transliteration_replace($ord, $unknown, $source_langcode);
                    $head = '';
                } elseif ($c < "\x80") {
                    $result .= $c;
                    $head = '';
                } elseif ($c < "\xc0") {
                    if ($head == '') {
                        $result .= $unknown;
                    }
                } else {
                    $result .= $unknown;
                    $head = '';
                }
            }
        }
        return $result;
    }

    function _transliteration_replace($ord, $unknown = '?', $langcode = NULL) {
        static $map = array();

        $bank = $ord >> 8;

        if (!isset($map[$bank][$langcode])) {
            $file = dirname(__FILE__) . '/manager_product/' . sprintf('x%02x', $bank) . '.php';
            if (file_exists($file)) {
                include $file;
                if ($langcode != 'en' && isset($variant[$langcode])) {
                    $map[$bank][$langcode] = $variant[$langcode] + $base;
                } else {
                    $map[$bank][$langcode] = $base;
                }
            } else {
                $map[$bank][$langcode] = array();
            }
        }

        $ord = $ord & 255;

        return isset($map[$bank][$langcode][$ord]) ? $map[$bank][$langcode][$ord] : $unknown;
    }
	
	private function makeSlugs($string, $maxlen = 0, $noSpace = true, $source_langcode = null) {
        global $session;
        $newStringTab = array();
        $string = strtolower($this->_transliteration_process(trim(html_entity_decode($string, ENT_QUOTES, "UTF-8")), '-', $source_langcode));
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = $this->my_str_split($string);
        }
        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
        foreach ($stringTab as $letter) {
            if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
                $newStringTab[] = $letter;
            } elseif ($letter == " ") {
                if ($noSpace) {
                    $newStringTab[] = "-";
                } else {
                    $newStringTab[] = " ";
                }
            }
        }
        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }
            $newString = $this->removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }
        return $newString;
    }
	
	private function makeSlugss($string, $maxlen = 0, $noSpace = true, $source_langcode = null) {
        global $session;
        $newStringTab = array();
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = $this->my_str_split($string);
        }
        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
        foreach ($stringTab as $letter) {
            $newStringTab[] = $letter;
        }
        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }
            $newString = $this->removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }
        return $newString;
    }

    private function removeDuplicates($sSearch, $sReplace, $sSubject) {
        $i = 0;
        do {
            $sSubject = str_replace($sSearch, $sReplace, $sSubject);
            $pos = strpos($sSubject, $sSearch);
            $i++;
            if ($i > 100) {
                die('removeDuplicates() loop error');
            }
        } while ($pos !== false);
        return $sSubject;
    }	
	
	// Unistall Module	
	public function uninstall() {
		//*************************************** Send Mail Uninstall ****************************************//
		if ($_SERVER['SERVER_ADDR'] != '127.0.0.1') {
			$subject = html_entity_decode('Удаление модуля "Массовое редактирование товаров" v.3.0.3.7', ENT_QUOTES, 'UTF-8');

			$message = html_entity_decode('Уведомляем вас о том, что было произведено удаление модуля "Массовое редактирование товаров" v.3.0.3.7', ENT_QUOTES, 'UTF-8') . "\n\n";

			$message .= 'Номер заказа: ' . $this->config->get('manager_product_license_order_id') . "\n\n";
			$message .= 'Код товара: ' . $this->config->get('manager_product_license_product_code') . "\n\n";
			$message .= 'Удаление произведено на домене: ' . $_SERVER['HTTP_HOST'] . "\n\n";
			$message .= 'Модуль был активирован ключом: ' . $this->config->get('manager_product_license_key') . "\n\n";
			$message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo('license@maxystore.com');
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();
		}
		//******************************************************************************************//
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->deleteSetting('module_manager_product');
		$this->model_setting_setting->deleteSetting('manager_product_license');
		$this->model_setting_setting->deleteSetting('manager_product');
		$this->model_setting_setting->deleteSetting('manager_product_seo_template');
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/manager_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}