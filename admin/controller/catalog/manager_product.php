<?php
class ControllerCatalogManagerProduct extends Controller {
	public function index() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');	

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addStyle('view/stylesheet/manager_product/style.css');		
		$this->document->addStyle('view/javascript/manager_product/css/popover.css');
		$this->document->addScript('view/javascript/manager_product/js/popover.min.js');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		} else {
			$filter_sku = null;
		}
		
		if (isset($this->request->get['filter_attribute_value'])) {
			$filter_attribute_value = $this->request->get['filter_attribute_value'];
		} else {
			$filter_attribute_value = null;
		}
		
		if (isset($this->request->get['filter_location'])) {
			$filter_location = $this->request->get['filter_location'];
		} else {
			$filter_location = null;
		}
		
		if (isset($this->request->get['filter_attribute_id'])) {
			$filter_attribute_id = $this->request->get['filter_attribute_id'];
		} else {
			$filter_attribute_id = null;
		}
		
		if (isset($this->request->get['filter_filter_id'])) {
			$filter_filter_id = $this->request->get['filter_filter_id'];
		} else {
			$filter_filter_id = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$filter_price_min = $this->request->get['filter_price_min'];
		} else {
			$filter_price_min = null;
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$filter_price_max = $this->request->get['filter_price_max'];
		} else {
			$filter_price_max = null;
		}
		
		if (isset($this->request->get['filter_special'])) {
			$filter_special = $this->request->get['filter_special'];
		} else {
			$filter_special = null;
		}
		
		if (isset($this->request->get['filter_special_price'])) {
			$filter_special_price = $this->request->get['filter_special_price'];
		} else {
			$filter_special_price = null;
		}
		
		if (isset($this->request->get['filter_special_price_min'])) {
			$filter_special_price_min = $this->request->get['filter_special_price_min'];
		} else {
			$filter_special_price_min = null;
		}
		
		if (isset($this->request->get['filter_special_price_max'])) {
			$filter_special_price_max = $this->request->get['filter_special_price_max'];
		} else {
			$filter_special_price_max = null;
		}
		
		if (isset($this->request->get['filter_discount'])) {
			$filter_discount = $this->request->get['filter_discount'];
		} else {
			$filter_discount = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$filter_quantity_min = $this->request->get['filter_quantity_min'];
		} else {
			$filter_quantity_min = null;
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$filter_quantity_max = $this->request->get['filter_quantity_max'];
		} else {
			$filter_quantity_max = null;
		}
		
		if (isset($this->request->get['filter_sort_order'])) {
			$filter_sort_order = $this->request->get['filter_sort_order'];
		} else {
			$filter_sort_order = null;
		}
		
		if (isset($this->request->get['filter_sort_order_min'])) {
			$filter_sort_order_min = $this->request->get['filter_sort_order_min'];
		} else {
			$filter_sort_order_min = null;
		}
		
		if (isset($this->request->get['filter_sort_order_max'])) {
			$filter_sort_order_max = $this->request->get['filter_sort_order_max'];
		} else {
			$filter_sort_order_max = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/manager_product', 'user_token=' . $this->session->data['user_token'], true)
		);	
		
		$data['filter_name'] 			  = $filter_name;
		$data['filter_category_id'] 	  = $filter_category_id;
		$data['filter_manufacturer_id']   = $filter_manufacturer_id;
		$data['filter_model'] 			  = $filter_model;
		$data['filter_sku'] 			  = $filter_sku;
		$data['filter_attribute_value']   = $filter_attribute_value;
		$data['filter_attribute_id'] 	  = $filter_attribute_id;
		$data['filter_filter_id'] 		  = $filter_filter_id;
		$data['filter_location'] 		  = $filter_location;
		$data['filter_price'] 			  = $filter_price;
		$data['filter_price_min'] 		  = $filter_price_min;
		$data['filter_price_max'] 		  = $filter_price_max;
		$data['filter_special'] 		  = $filter_special;
		$data['filter_special_price'] 	  = $filter_special_price;
		$data['filter_special_price_min'] = $filter_special_price_min;
		$data['filter_special_price_max'] = $filter_special_price_max;
		$data['filter_discount'] 		  = $filter_discount;
		$data['filter_quantity'] 		  = $filter_quantity;
		$data['filter_quantity_min'] 	  = $filter_quantity_min;
		$data['filter_quantity_max'] 	  = $filter_quantity_max;
		$data['filter_sort_order'] 		  = $filter_sort_order;
		$data['filter_sort_order_min'] 	  = $filter_sort_order_min;
		$data['filter_sort_order_max'] 	  = $filter_sort_order_max;
		$data['filter_status'] 			  = $filter_status;
		$data['filter_image'] 			  = $filter_image;
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$data['presence_column_meta_h1'] = $this->model_catalog_manager_product->getProductPresenceMetaH1();
		
		$data['error_select_product'] = $this->language->get('error_select_product');
		
		// Mass Setting
		$data['manager_product_mass_name'] = $this->config->get('manager_product_mass_name');
		$data['manager_product_mass_description'] = $this->config->get('manager_product_mass_description');
		$data['manager_product_mass_meta_title'] = $this->config->get('manager_product_mass_meta_title');
		$data['manager_product_mass_meta_h1'] = $this->config->get('manager_product_mass_meta_h1');
		$data['manager_product_mass_meta_description'] = $this->config->get('manager_product_mass_meta_description');
		$data['manager_product_mass_meta_keyword'] = $this->config->get('manager_product_mass_meta_keyword');
		$data['manager_product_mass_tag'] = $this->config->get('manager_product_mass_tag');
		$data['manager_product_mass_image'] = $this->config->get('manager_product_mass_image');
		$data['manager_product_mass_price'] = $this->config->get('manager_product_mass_price');
		$data['manager_product_mass_quantity'] = $this->config->get('manager_product_mass_quantity');
		$data['manager_product_mass_minimum'] = $this->config->get('manager_product_mass_minimum');
		$data['manager_product_mass_model'] = $this->config->get('manager_product_mass_model');
		$data['manager_product_mass_seo_url'] = $this->config->get('manager_product_mass_seo_url');
		$data['manager_product_mass_code'] = $this->config->get('manager_product_mass_code');
		$data['manager_product_mass_date_available'] = $this->config->get('manager_product_mass_date_available');
		$data['manager_product_mass_shipping'] = $this->config->get('manager_product_mass_shipping');
		$data['manager_product_mass_size'] = $this->config->get('manager_product_mass_size');
		$data['manager_product_mass_sort_order'] = $this->config->get('manager_product_mass_sort_order');
		$data['manager_product_mass_manufacturer'] = $this->config->get('manager_product_mass_manufacturer');
		$data['manager_product_mass_category'] = $this->config->get('manager_product_mass_category');
		$data['manager_product_mass_filter'] = $this->config->get('manager_product_mass_filter');
		$data['manager_product_mass_store'] = $this->config->get('manager_product_mass_store');
		$data['manager_product_mass_download'] = $this->config->get('manager_product_mass_download');
		$data['manager_product_mass_related'] = $this->config->get('manager_product_mass_related');
		$data['manager_product_mass_add_discount'] = $this->config->get('manager_product_mass_add_discount');
		$data['manager_product_mass_edit_discount'] = $this->config->get('manager_product_mass_edit_discount');
		$data['manager_product_mass_add_special'] = $this->config->get('manager_product_mass_add_special');
		$data['manager_product_mass_edit_special'] = $this->config->get('manager_product_mass_edit_special');
		$data['manager_product_mass_attribute'] = $this->config->get('manager_product_mass_attribute');
		$data['manager_product_mass_option'] = $this->config->get('manager_product_mass_option');
		$data['manager_product_mass_images'] = $this->config->get('manager_product_mass_images');
		$data['manager_product_mass_reward'] = $this->config->get('manager_product_mass_reward');
		$data['manager_product_mass_design'] = $this->config->get('manager_product_mass_design');
		
		if ($data['manager_product_mass_name'] || $data['manager_product_mass_description'] || $data['manager_product_mass_meta_title'] || $data['manager_product_mass_meta_h1'] || $data['manager_product_mass_meta_description'] || $data['manager_product_mass_meta_keyword'] || $data['manager_product_mass_tag'] || $data['manager_product_mass_image'] || $data['manager_product_mass_price'] || $data['manager_product_mass_quantity'] || $data['manager_product_mass_minimum'] || $data['manager_product_mass_model'] || $data['manager_product_mass_seo_url'] || $data['manager_product_mass_code'] || $data['manager_product_mass_date_available'] || $data['manager_product_mass_shipping'] || $data['manager_product_mass_size'] || $data['manager_product_mass_sort_order'] || $data['manager_product_mass_manufacturer'] || $data['manager_product_mass_category'] || $data['manager_product_mass_filter'] || $data['manager_product_mass_store'] || $data['manager_product_mass_download'] || $data['manager_product_mass_related'] || $data['manager_product_mass_add_discount'] || $data['manager_product_mass_edit_discount'] || $data['manager_product_mass_add_special'] || $data['manager_product_mass_edit_special'] || $data['manager_product_mass_attribute'] || $data['manager_product_mass_option'] || $data['manager_product_mass_images'] || $data['manager_product_mass_reward'] || $data['manager_product_mass_design']) {
			$data['product_mass'] = true;
		} else {
			$data['product_mass'] = false;
		}
		
		if ($data['manager_product_mass_name'] || $data['manager_product_mass_description'] || $data['manager_product_mass_meta_title'] || $data['manager_product_mass_meta_h1'] || $data['manager_product_mass_meta_description'] || $data['manager_product_mass_meta_keyword'] || $data['manager_product_mass_tag']) {
			$data['product_general'] = true;
		} else {
			$data['product_general'] = false;
		}
		
		if ($data['manager_product_mass_image'] || $data['manager_product_mass_price'] || $data['manager_product_mass_quantity'] || $data['manager_product_mass_minimum'] || $data['manager_product_mass_model'] || $data['manager_product_mass_seo_url'] || $data['manager_product_mass_code'] || $data['manager_product_mass_date_available'] || $data['manager_product_mass_shipping'] || $data['manager_product_mass_size'] || $data['manager_product_mass_sort_order']) {
			$data['product_data'] = true;
		} else {
			$data['product_data'] = false;
		}
		
		if ($data['manager_product_mass_manufacturer'] || $data['manager_product_mass_category'] || $data['manager_product_mass_filter'] || $data['manager_product_mass_store'] || $data['manager_product_mass_download'] || $data['manager_product_mass_related']) {
			$data['product_link'] = true;
		} else {
			$data['product_link'] = false;
		}
		
		if ($data['manager_product_mass_add_discount'] || $data['manager_product_mass_edit_discount'] || $data['manager_product_mass_add_special'] || $data['manager_product_mass_edit_special']) {
			$data['product_sale'] = true;
		} else {
			$data['product_sale'] = false;
		}
		
		if ($data['manager_product_mass_attribute'] || $data['manager_product_mass_option']) {
			$data['product_attribute_option'] = true;
		} else {
			$data['product_attribute_option'] = false;
		}
		
		if ($data['manager_product_mass_images'] || $data['manager_product_mass_reward'] || $data['manager_product_mass_design']) {
			$data['product_other'] = true;
		} else {
			$data['product_other'] = false;
		}
		
		// Mass Delete Setting
		$data['manager_product_mass_delete_name'] = $this->config->get('manager_product_mass_delete_name');
		$data['manager_product_mass_delete_description'] = $this->config->get('manager_product_mass_delete_description');
		$data['manager_product_mass_delete_meta_title'] = $this->config->get('manager_product_mass_delete_meta_title');
		$data['manager_product_mass_delete_meta_h1'] = $this->config->get('manager_product_mass_delete_meta_h1');
		$data['manager_product_mass_delete_meta_description'] = $this->config->get('manager_product_mass_delete_meta_description');
		$data['manager_product_mass_delete_meta_keyword'] = $this->config->get('manager_product_mass_delete_meta_keyword');
		$data['manager_product_mass_delete_tag'] = $this->config->get('manager_product_mass_delete_tag');
		$data['manager_product_mass_delete_image'] = $this->config->get('manager_product_mass_delete_image');
		$data['manager_product_mass_delete_model'] = $this->config->get('manager_product_mass_delete_model');
		$data['manager_product_mass_delete_sku'] = $this->config->get('manager_product_mass_delete_sku');
		$data['manager_product_mass_delete_upc'] = $this->config->get('manager_product_mass_delete_upc');
		$data['manager_product_mass_delete_ean'] = $this->config->get('manager_product_mass_delete_ean');
		$data['manager_product_mass_delete_jan'] = $this->config->get('manager_product_mass_delete_jan');
		$data['manager_product_mass_delete_isbn'] = $this->config->get('manager_product_mass_delete_isbn');
		$data['manager_product_mass_delete_mpn'] = $this->config->get('manager_product_mass_delete_mpn');
		$data['manager_product_mass_delete_location'] = $this->config->get('manager_product_mass_delete_location');
		$data['manager_product_mass_delete_price'] = $this->config->get('manager_product_mass_delete_price');
		$data['manager_product_mass_delete_tax_class'] = $this->config->get('manager_product_mass_delete_tax_class');
		$data['manager_product_mass_delete_quantity'] = $this->config->get('manager_product_mass_delete_quantity');
		$data['manager_product_mass_delete_minimum'] = $this->config->get('manager_product_mass_delete_minimum');
		$data['manager_product_mass_delete_date_available'] = $this->config->get('manager_product_mass_delete_date_available');
		$data['manager_product_mass_delete_size'] = $this->config->get('manager_product_mass_delete_size');
		$data['manager_product_mass_delete_weight'] = $this->config->get('manager_product_mass_delete_weight');
		$data['manager_product_mass_delete_sort_order'] = $this->config->get('manager_product_mass_delete_sort_order');
		$data['manager_product_mass_delete_manufacturer'] = $this->config->get('manager_product_mass_delete_manufacturer');
		$data['manager_product_mass_delete_category'] = $this->config->get('manager_product_mass_delete_category');
		$data['manager_product_mass_delete_filter'] = $this->config->get('manager_product_mass_delete_filter');
		$data['manager_product_mass_delete_store'] = $this->config->get('manager_product_mass_delete_store');
		$data['manager_product_mass_delete_download'] = $this->config->get('manager_product_mass_delete_download');
		$data['manager_product_mass_delete_related'] = $this->config->get('manager_product_mass_delete_related');
		$data['manager_product_mass_delete_attribute'] = $this->config->get('manager_product_mass_delete_attribute');		
		$data['manager_product_mass_delete_option'] = $this->config->get('manager_product_mass_delete_option');
		$data['manager_product_mass_delete_discount'] = $this->config->get('manager_product_mass_delete_discount');
		$data['manager_product_mass_delete_special'] = $this->config->get('manager_product_mass_delete_special');
		$data['manager_product_mass_delete_images'] = $this->config->get('manager_product_mass_delete_images');
		$data['manager_product_mass_delete_reward'] = $this->config->get('manager_product_mass_delete_reward');
		$data['manager_product_mass_delete_seo_url'] = $this->config->get('manager_product_mass_delete_seo_url');
		$data['manager_product_mass_delete_design'] = $this->config->get('manager_product_mass_delete_design');
		
		if ($data['manager_product_mass_delete_name'] || $data['manager_product_mass_delete_description'] || $data['manager_product_mass_delete_meta_title'] || $data['manager_product_mass_delete_meta_h1'] || $data['manager_product_mass_delete_meta_description'] || $data['manager_product_mass_delete_meta_keyword'] || $data['manager_product_mass_delete_tag'] || $data['manager_product_mass_delete_image'] || $data['manager_product_mass_delete_model'] || $data['manager_product_mass_delete_sku'] || $data['manager_product_mass_delete_upc'] || $data['manager_product_mass_delete_ean'] || $data['manager_product_mass_delete_jan'] || $data['manager_product_mass_delete_isbn'] || $data['manager_product_mass_delete_mpn'] || $data['manager_product_mass_delete_location'] || $data['manager_product_mass_delete_price'] || $data['manager_product_mass_delete_tax_class'] || $data['manager_product_mass_delete_quantity'] || $data['manager_product_mass_delete_minimum'] || $data['manager_product_mass_delete_date_available'] || $data['manager_product_mass_delete_size'] || $data['manager_product_mass_delete_weight'] || $data['manager_product_mass_delete_sort_order'] || $data['manager_product_mass_delete_manufacturer'] || $data['manager_product_mass_delete_category'] || $data['manager_product_mass_delete_filter'] || $data['manager_product_mass_delete_store'] || $data['manager_product_mass_delete_download'] || $data['manager_product_mass_delete_related'] || $data['manager_product_mass_delete_attribute'] || $data['manager_product_mass_delete_option'] || $data['manager_product_mass_delete_discount'] || $data['manager_product_mass_delete_special'] || $data['manager_product_mass_delete_images']  || $data['manager_product_mass_delete_reward'] || $data['manager_product_mass_delete_seo_url'] || $data['manager_product_mass_delete_design']) {
			$data['product_mass_delete'] = true;
		} else {
			$data['product_mass_delete'] = false;
		}
		
		if ($data['manager_product_mass_delete_name'] || $data['manager_product_mass_delete_description'] || $data['manager_product_mass_delete_meta_title'] || $data['manager_product_mass_delete_meta_h1'] || $data['manager_product_mass_delete_meta_description'] || $data['manager_product_mass_delete_meta_keyword'] || $data['manager_product_mass_delete_tag']) {
			$data['product_delete_general'] = true;
		} else {
			$data['product_delete_general'] = false;
		}
		
		if ($data['manager_product_mass_delete_image'] || $data['manager_product_mass_delete_model'] || $data['manager_product_mass_delete_sku'] || $data['manager_product_mass_delete_upc'] || $data['manager_product_mass_delete_ean'] || $data['manager_product_mass_delete_jan'] || $data['manager_product_mass_delete_isbn'] || $data['manager_product_mass_delete_mpn'] || $data['manager_product_mass_delete_location'] || $data['manager_product_mass_delete_price'] || $data['manager_product_mass_delete_tax_class'] || $data['manager_product_mass_delete_quantity'] || $data['manager_product_mass_delete_minimum'] || $data['manager_product_mass_delete_date_available'] || $data['manager_product_mass_delete_size'] || $data['manager_product_mass_delete_weight'] || $data['manager_product_mass_delete_sort_order']) {
			$data['product_delete_data'] = true;
		} else {
			$data['product_delete_data'] = false;
		}
		
		if ($data['manager_product_mass_delete_manufacturer'] || $data['manager_product_mass_delete_category'] || $data['manager_product_mass_delete_filter'] || $data['manager_product_mass_delete_store'] || $data['manager_product_mass_delete_download'] || $data['manager_product_mass_delete_related']) {
			$data['product_delete_link'] = true;
		} else {
			$data['product_delete_link'] = false;
		}
		
		if ($data['manager_product_mass_delete_discount'] || $data['manager_product_mass_delete_special']) {
			$data['product_delete_sale'] = true;
		} else {
			$data['product_delete_sale'] = false;
		}
		
		if ($data['manager_product_mass_delete_attribute'] || $data['manager_product_mass_delete_option']) {
			$data['product_delete_attribute_option'] = true;
		} else {
			$data['product_delete_attribute_option'] = false;
		}
		
		if ($data['manager_product_mass_delete_images'] || $data['manager_product_mass_delete_reward'] || $data['manager_product_mass_delete_seo_url'] || $data['manager_product_mass_delete_design']) {
			$data['product_delete_other'] = true;
		} else {
			$data['product_delete_other'] = false;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/manager_product/product', $data));	
	}
	
	public function product() {
		$this->load->language('catalog/manager_product');
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		} else {
			$filter_sku = null;
		}
		
		if (isset($this->request->get['filter_attribute_value'])) {
			$filter_attribute_value = $this->request->get['filter_attribute_value'];
		} else {
			$filter_attribute_value = null;
		}
		
		if (isset($this->request->get['filter_location'])) {
			$filter_location = $this->request->get['filter_location'];
		} else {
			$filter_location = null;
		}
		
		if (isset($this->request->get['filter_attribute_id'])) {
			$filter_attribute_id = $this->request->get['filter_attribute_id'];
		} else {
			$filter_attribute_id = null;
		}
		
		if (isset($this->request->get['filter_filter_id'])) {
			$filter_filter_id = $this->request->get['filter_filter_id'];
		} else {
			$filter_filter_id = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$filter_price_min = $this->request->get['filter_price_min'];
		} else {
			$filter_price_min = null;
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$filter_price_max = $this->request->get['filter_price_max'];
		} else {
			$filter_price_max = null;
		}
		
		if (isset($this->request->get['filter_special'])) {
			$filter_special = $this->request->get['filter_special'];
		} else {
			$filter_special = null;
		}
		
		if (isset($this->request->get['filter_special_price'])) {
			$filter_special_price = $this->request->get['filter_special_price'];
		} else {
			$filter_special_price = null;
		}
		
		if (isset($this->request->get['filter_special_price_min'])) {
			$filter_special_price_min = $this->request->get['filter_special_price_min'];
		} else {
			$filter_special_price_min = null;
		}
		
		if (isset($this->request->get['filter_special_price_max'])) {
			$filter_special_price_max = $this->request->get['filter_special_price_max'];
		} else {
			$filter_special_price_max = null;
		}
		
		if (isset($this->request->get['filter_discount'])) {
			$filter_discount = $this->request->get['filter_discount'];
		} else {
			$filter_discount = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$filter_quantity_min = $this->request->get['filter_quantity_min'];
		} else {
			$filter_quantity_min = null;
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$filter_quantity_max = $this->request->get['filter_quantity_max'];
		} else {
			$filter_quantity_max = null;
		}
		
		if (isset($this->request->get['filter_sort_order'])) {
			$filter_sort_order = $this->request->get['filter_sort_order'];
		} else {
			$filter_sort_order = null;
		}
		
		if (isset($this->request->get['filter_sort_order_min'])) {
			$filter_sort_order_min = $this->request->get['filter_sort_order_min'];
		} else {
			$filter_sort_order_min = null;
		}
		
		if (isset($this->request->get['filter_sort_order_max'])) {
			$filter_sort_order_max = $this->request->get['filter_sort_order_max'];
		} else {
			$filter_sort_order_max = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if ($this->config->get('manager_product_default_limit')) {
			$limit = $this->config->get('manager_product_default_limit');
		} else {
			$limit = 20;
		}	
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
            $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
        }
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_attribute_value'])) {
			$url .= '&filter_attribute_value=' . urlencode(html_entity_decode($this->request->get['filter_attribute_value'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_attribute_id'])) {
			$url .= '&filter_attribute_id=' . urlencode(html_entity_decode($this->request->get['filter_attribute_id'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_filter_id'])) {
			$url .= '&filter_filter_id=' . urlencode(html_entity_decode($this->request->get['filter_filter_id'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
		}
		
		if (isset($this->request->get['filter_special'])) {
			$url .= '&filter_special=' . $this->request->get['filter_special'];
		}
		
		if (isset($this->request->get['filter_special_price'])) {
			$url .= '&filter_special_price=' . $this->request->get['filter_special_price'];
		}
		
		if (isset($this->request->get['filter_special_price_min'])) {
			$url .= '&filter_special_price_min=' . $this->request->get['filter_special_price_min'];
		}
		
		if (isset($this->request->get['filter_special_price_max'])) {
			$url .= '&filter_special_price_max=' . $this->request->get['filter_special_price_max'];
		}
		
		if (isset($this->request->get['filter_discount'])) {
			$url .= '&filter_discount=' . $this->request->get['filter_discount'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
		}
		
		if (isset($this->request->get['filter_sort_order'])) {
			$url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
		}
		
		if (isset($this->request->get['filter_sort_order_min'])) {
			$url .= '&filter_sort_order_min=' . $this->request->get['filter_sort_order_min'];
		}
		
		if (isset($this->request->get['filter_sort_order_max'])) {
			$url .= '&filter_sort_order_max=' . $this->request->get['filter_sort_order_max'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  		   => $filter_name,
			'filter_category_id' 	   => $filter_category_id,
			'filter_manufacturer_id'   => $filter_manufacturer_id,
			'filter_model'	  		   => $filter_model,
			'filter_sku'	  		   => $filter_sku,
			'filter_attribute_value'   => $filter_attribute_value,
			'filter_location'	  	   => $filter_location,
			'filter_attribute_id'	   => $filter_attribute_id,
			'filter_price'	  		   => $filter_price,
			'filter_price_min'	  	   => $filter_price_min,
			'filter_price_max'	  	   => $filter_price_max,
			'filter_special'	  	   => $filter_special,
			'filter_special_price'	   => $filter_special_price,
			'filter_special_price_min' => $filter_special_price_min,
			'filter_special_price_max' => $filter_special_price_max,
			'filter_discount'	  	   => $filter_discount,
			'filter_quantity' 		   => $filter_quantity,
			'filter_quantity_min' 	   => $filter_quantity_min,
			'filter_quantity_max' 	   => $filter_quantity_max,
			'filter_sort_order'  	   => $filter_sort_order,
			'filter_sort_order_min'    => $filter_sort_order_min,
			'filter_sort_order_max'    => $filter_sort_order_max,
			'filter_status'   		   => $filter_status,
			'filter_image'   		   => $filter_image,
			'filter_filter_id'   	   => $filter_filter_id,
			'sort'           		   => $sort,
			'order'          		   => $order,
			'start'          		   => ($page - 1) * $limit,
			'limit'          		   => $limit
		);
		
		$this->load->model('tool/image');

		$this->load->model('catalog/manager_product');	
		$this->load->model('catalog/category');

		$product_total = $this->model_catalog_manager_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_manager_product->getProducts($filter_data);
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		foreach ($results as $result) {
			$product_info = $this->model_catalog_manager_product->getProduct($result['product_id']);
			
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
				$img = $product_info['image'];
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				$img = '';
			}
			
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$thumb = $this->model_tool_image->resize($product_info['image'], 100, 100);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
			}
			
			$placeholder = $this->model_tool_image->resize('no_image.png', 100, 100);
			
			$special = false;

			$product_specials = $this->model_catalog_manager_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}
			
			$categories = $this->model_catalog_manager_product->getProductCategories($result['product_id']);
			
			$product_categories = array();

			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$product_categories[] = array(
						'category_id' => $category_info['category_id'],
						'name' 		  => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					);
				}
			}
			
			if ($special) { 
				$price = '<del>' . $result['price'] . '</del>';
			} else {
				$price = $result['price'];
			}
			
			$status = $product_info['status'];
			$subtract = $product_info['subtract'];
			$shipping = $product_info['shipping'];
			
			$data['products'][] = array(
				'product_id' 		 => $result['product_id'],
				'name'        		 => $result['name'],
				'image'       		 => $image,
				'thumb'       		 => $thumb,
				'img'         		 => $img,
				'categories' 		 => $product_categories,
				'manufacturer' 		 => $result['manufacturer'],
				'manufacturer_id' 	 => $result['manufacturer_id'],
				'model'      	     => $result['model'],
				'sku'      	     	 => $result['sku'],
				'upc'      	     	 => $result['upc'],
				'ean'      	     	 => $result['ean'],
				'jan'      	     	 => $result['jan'],
				'isbn'      	     => $result['isbn'],
				'mpn'      	     	 => $result['mpn'],
				'location'      	 => $result['location'],
				'price'      		 => $price,
				'price_not_formated' => $result['price'],
				'special'    		 => $special,
				'tax_class_id'		 => $result['tax_class_id'],
				'tax_title'			 => $result['tax_title'],
				'quantity'   		 => $result['quantity'],
				'minimum'   		 => $result['minimum'],
				'subtractes'    	 => $result['subtract'],
				'subtract'  	  	 => $subtract ? $this->language->get('text_on') : $this->language->get('text_off'),
				'no_subtract'   	 => $subtract ? '<span class="text-success">' . $this->language->get('text_yes') . '</span>' : '<span class="text-danger">' . $this->language->get('text_no') . '</span>',
				'stock_status_id'	 => $result['stock_status_id'],
				'stock_name'		 => $result['stock_name'],
				'shippinges'    	 => $result['shipping'],
				'shipping'  	  	 => $shipping ? $this->language->get('text_on') : $this->language->get('text_off'),
				'no_shipping'   	 => $shipping ? '<span class="text-success">' . $this->language->get('text_yes') . '</span>' : '<span class="text-danger">' . $this->language->get('text_no') . '</span>',
				'date_available'     => $result['date_available'],
				'length'     		 => $result['length'],
				'width'     		 => $result['width'],
				'height'     		 => $result['height'],
				'length_class_id'	 => $result['length_class_id'],
				'length_title'		 => $result['length_title'],
				'weight'     		 => $result['weight'],
				'weight_class_id'	 => $result['weight_class_id'],
				'weight_title'		 => $result['weight_title'],
				'sort_order'  		 => $result['sort_order'],
				'statuses'    		 => $result['status'],
				'status'  	  		 => $status ? $this->language->get('text_on') : $this->language->get('text_off'),
				'no_status'   		 => $status ? '<span class="text-success">' . $this->language->get('text_enabled') . '</span>' : '<span class="text-danger">' . $this->language->get('text_disabled') . '</span>',
				'view'       		 => $server . 'index.php?route=product/product&product_id=' . $result['product_id'],
			);
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$this->load->model('localisation/length_class');
		$this->load->model('localisation/weight_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		$url = '';
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_name'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_category'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p2c.category_id' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=m.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_sku'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku' . $url, true);
		$data['sort_upc'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.upc' . $url, true);
		$data['sort_ean'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.ean' . $url, true);
		$data['sort_jan'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.jan' . $url, true);
		$data['sort_isbn'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.isbn' . $url, true);
		$data['sort_mpn'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.mpn' . $url, true);
		$data['sort_location'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.location' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_tax'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tax_class_id' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_minimum'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.minimum' . $url, true);
		$data['sort_subtract'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.subtract' . $url, true);
		$data['sort_stock_status_id'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.stock_status_id' . $url, true);
		$data['sort_shipping'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.shipping' . $url, true);
		$data['sort_date_available'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date_available' . $url, true);
		$data['sort_length_class_id'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.length_class_id' . $url, true);
		$data['sort_weight'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.weight' . $url, true);
		$data['sort_weight_class_id'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.weight_class_id' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
					
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('catalog/manager_product/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		// Show Column
		$data['image_view'] = $this->config->get('manager_product_image_view');
		$data['category_view'] = $this->config->get('manager_product_category_view');
		$data['manufacturer_view'] = $this->config->get('manager_product_manufacturer_view');
		$data['model_view'] = $this->config->get('manager_product_model_view');
		$data['sku_view'] = $this->config->get('manager_product_sku_view');
		$data['upc_view'] = $this->config->get('manager_product_upc_view');
		$data['ean_view'] = $this->config->get('manager_product_ean_view');
		$data['jan_view'] = $this->config->get('manager_product_jan_view');
		$data['isbn_view'] = $this->config->get('manager_product_isbn_view');
		$data['mpn_view'] = $this->config->get('manager_product_mpn_view');
		$data['location_view'] = $this->config->get('manager_product_location_view');
		$data['tax_view'] = $this->config->get('manager_product_tax_view');
		$data['price_view'] = $this->config->get('manager_product_price_view');
		$data['quantity_view'] = $this->config->get('manager_product_quantity_view');
		$data['minimum_view'] = $this->config->get('manager_product_minimum_view');
		$data['subtract_view'] = $this->config->get('manager_product_subtract_view');
		$data['subtract_view'] = $this->config->get('manager_product_subtract_view');
		$data['stock_status_view'] = $this->config->get('manager_product_stock_status_id_view');
		$data['shipping_view'] = $this->config->get('manager_product_shipping_view');
		$data['date_available_view'] = $this->config->get('manager_product_date_available_view');
		$data['dimension_view'] = $this->config->get('manager_product_dimension_view');
		$data['length_class_view'] = $this->config->get('manager_product_length_class_view');
		$data['weight_view'] = $this->config->get('manager_product_weight_view');
		$data['weight_class_view'] = $this->config->get('manager_product_weight_class_view');
		$data['sort_order_view'] = $this->config->get('manager_product_sort_order_view');
		$data['status_view'] = $this->config->get('manager_product_status_view');
		
		// Show Edit Item Data
		$data['manager_product_image'] = $this->config->get('manager_product_image');
		$data['manager_product_name'] = $this->config->get('manager_product_name');
		$data['manager_product_category'] = $this->config->get('manager_product_category');
		$data['manager_product_manufacturer'] = $this->config->get('manager_product_manufacturer');
		$data['manager_product_model'] = $this->config->get('manager_product_model');
		$data['manager_product_sku'] = $this->config->get('manager_product_sku');
		$data['manager_product_upc'] = $this->config->get('manager_product_upc');
		$data['manager_product_ean'] = $this->config->get('manager_product_ean');
		$data['manager_product_jan'] = $this->config->get('manager_product_jan');
		$data['manager_product_isbn'] = $this->config->get('manager_product_isbn');
		$data['manager_product_mpn'] = $this->config->get('manager_product_mpn');
		$data['manager_product_location'] = $this->config->get('manager_product_location');
		$data['manager_product_price'] = $this->config->get('manager_product_price');
		$data['manager_product_quantity'] = $this->config->get('manager_product_quantity');
		$data['manager_product_minimum'] = $this->config->get('manager_product_minimum');
		$data['manager_product_subtract'] = $this->config->get('manager_product_subtract');
		$data['manager_product_stock_status'] = $this->config->get('manager_product_stock_status_id');
		$data['manager_product_shipping'] = $this->config->get('manager_product_shipping');
		$data['manager_product_dimension'] = $this->config->get('manager_product_dimension');
		$data['manager_product_length_class'] = $this->config->get('manager_product_length_class');
		$data['manager_product_weights'] = $this->config->get('manager_product_weights');
		$data['manager_product_weight_class'] = $this->config->get('manager_product_weight_class');
		$data['manager_product_sort_order'] = $this->config->get('manager_product_sort_order');
		$data['manager_product_status'] = $this->config->get('manager_product_status');
		
		// Show Edit More Data
		$data['manager_product_general'] = $this->config->get('manager_product_general');
		$data['manager_product_code'] = $this->config->get('manager_product_code');
		$data['manager_product_tax'] = $this->config->get('manager_product_tax');
		$data['manager_product_stock'] = $this->config->get('manager_product_stock');
		$data['manager_product_seo_url'] = $this->config->get('manager_product_seo_url');
		$data['manager_product_date'] = $this->config->get('manager_product_date');
		$data['manager_product_weight'] = $this->config->get('manager_product_weight');
		$data['manager_product_filter'] = $this->config->get('manager_product_filter');
		$data['manager_product_store'] = $this->config->get('manager_product_store');
		$data['manager_product_download'] = $this->config->get('manager_product_download');
		$data['manager_product_related'] = $this->config->get('manager_product_related');
		$data['manager_product_attribute'] = $this->config->get('manager_product_attribute');
		$data['manager_product_option'] = $this->config->get('manager_product_option');
		$data['manager_product_discount'] = $this->config->get('manager_product_discount');
		$data['manager_product_special'] = $this->config->get('manager_product_special');
		$data['manager_product_images'] = $this->config->get('manager_product_images');
		$data['manager_product_reward'] = $this->config->get('manager_product_reward');
		$data['manager_product_design'] = $this->config->get('manager_product_design');
		$data['manager_product_view'] = $this->config->get('manager_product_view');
		
		if ($data['manager_product_general'] || $data['manager_product_code'] || $data['manager_product_tax'] || $data['manager_product_stock'] || $data['manager_product_seo_url'] || $data['manager_product_date'] || $data['manager_product_weight'] || $data['manager_product_filter'] || $data['manager_product_store'] || $data['manager_product_download'] || $data['manager_product_related'] || $data['manager_product_attribute'] || $data['manager_product_option'] || $data['manager_product_discount'] || $data['manager_product_special'] || $data['manager_product_images'] || $data['manager_product_reward'] || $data['manager_product_design'] || $data['manager_product_view']) {
			$data['product_more'] = true;
		} else {
			$data['product_more'] = false;
		}
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->response->setOutput($this->load->view('catalog/manager_product/product_list', $data));
	}
	
	public function getForm() {
		$this->load->language('catalog/manager_product');
		
		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		$this->load->model('catalog/manager_product');	
		
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_manager_product->getProduct($this->request->get['product_id']);
		}
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['meta_title_required'] = $this->config->get('manager_product_meta_title_required') ? ' required' : '';
		$data['model_required'] = $this->config->get('manager_product_model_required') ? ' required' : '';
		
		$data['presence_column_meta_h1'] = $this->model_catalog_manager_product->getProductPresenceMetaH1();
		$data['presence_column_main_category'] = $this->model_catalog_manager_product->getProductPresenceMainCategory();

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_manager_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}
		
		if (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
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
		
		if (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
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
			$data['shipping'] = 1;
		}
		
		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_catalog_manager_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
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
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
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

		$this->load->model('catalog/manufacturer');

		if (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		if (isset($product_info)) {
			$data['main_category_id'] = $this->model_catalog_manager_product->getProductMainCategoryId($this->request->get['product_id']);
		} else {
			$data['main_category_id'] = 0;
		}
		
		$data['main_category'] = $this->model_catalog_manager_product->getMainCategoryName($data['main_category_id']);
		
		$this->load->model('catalog/category');

		if (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_manager_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
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
		
		// Image
		if (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
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

		if (!$this->config->get('manager_product_model_required')) {
			$data['hide_model'] = $this->config->get('manager_product_hide_model') ? ' hidden' : '';
		} else {
			$data['hide_model'] = '';
		}
		
		$data['hide_codes'] = $this->config->get('manager_product_hide_codes') ? ' hidden' : '';
		$data['hide_price'] = $this->config->get('manager_product_hide_price') ? ' hidden' : '';
		$data['hide_tax'] = $this->config->get('manager_product_hide_tax_class') ? ' hidden' : '';
		$data['hide_quantity'] = $this->config->get('manager_product_hide_quantity') ? ' hidden' : '';
		$data['hide_minimum'] = $this->config->get('manager_product_hide_minimum') ? ' hidden' : '';
		$data['hide_subtract'] = $this->config->get('manager_product_hide_subtract') ? ' hidden' : '';
		$data['hide_stock_status'] = $this->config->get('manager_product_hide_stock_status') ? ' hidden' : '';
		$data['hide_shipping'] = $this->config->get('manager_product_hide_shipping') ? ' hidden' : '';
		$data['hide_date_available'] = $this->config->get('manager_product_hide_date_available') ? ' hidden' : '';
		$data['hide_dimension'] = $this->config->get('manager_product_hide_dimension') ? ' hidden' : '';
		$data['hide_length_class'] = $this->config->get('manager_product_hide_length_class') ? ' hidden' : '';
		$data['hide_weight'] = $this->config->get('manager_product_hide_weight') ? ' hidden' : '';
		$data['hide_weight_class'] = $this->config->get('manager_product_hide_weight_class') ? ' hidden' : '';
		$data['hide_status'] = $this->config->get('manager_product_hide_status') ? ' hidden' : '';
		$data['hide_sort_order'] = $this->config->get('manager_product_hide_sort_order') ? ' hidden' : '';
		$data['hide_manufacturer'] = $this->config->get('manager_product_hide_manufacturer') ? ' hidden' : '';
		$data['hide_filter'] = $this->config->get('manager_product_hide_filter') ? ' hidden' : '';
		$data['hide_store'] = $this->config->get('manager_product_hide_store') ? ' hidden' : '';
		$data['hide_download'] = $this->config->get('manager_product_hide_download') ? ' hidden' : '';
		$data['hide_related'] = $this->config->get('manager_product_hide_related') ? ' hidden' : '';
		$data['hide_attribute'] = $this->config->get('manager_product_hide_attribute') ? ' class="hidden"' : '';
		$data['hide_option'] = $this->config->get('manager_product_hide_option') ? ' class="hidden"' : '';
		$data['hide_recurring'] = $this->config->get('manager_product_hide_recurring') ? ' class="hidden"' : '';
		$data['hide_discount'] = $this->config->get('manager_product_hide_discount') ? ' class="hidden"' : '';
		$data['hide_special'] = $this->config->get('manager_product_hide_special') ? ' class="hidden"' : '';
		$data['hide_images'] = $this->config->get('manager_product_hide_additional_image') ? ' class="hidden"' : '';
		$data['hide_seo_url'] = $this->config->get('manager_product_hide_seo_url') ? ' class="hidden"' : '';
		$data['hide_reward'] = $this->config->get('manager_product_hide_reward') ? ' class="hidden"' : '';
		$data['hide_design'] = $this->config->get('manager_product_hide_layout') ? ' class="hidden"' : '';
		
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

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('catalog/manager_product/saveProduct', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('catalog/manager_product/saveProduct', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'], true);
		}

		$this->response->setOutput($this->load->view('catalog/manager_product/product_form', $data));		
	}
	
	public function saveProduct() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}

			foreach ($this->request->post['product_description'] as $language_id => $value) {
				if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
					$json['error'] = $this->language->get('error_name');
				}
			}
			
			if ($this->config->get('manager_product_meta_title')) {
				foreach ($this->request->post['product_description'] as $language_id => $value) {
					if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
						$json['error'] = $this->language->get('error_meta_title');
					}
				}
			}

			if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
				$json['error'] = $this->language->get('error_model');
			}
			
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
								if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['product_id']) || (($seo_url['query'] != 'product_id=' . $this->request->get['product_id'])))) {
									$json['error'] = $this->language->get('error_keyword');
									
									break;
								}
							}
						}
					}
				}
			}

			if (!isset($json['error'])) {
				if (!isset($this->request->get['product_id'])) {
					$this->model_catalog_manager_product->addProduct($this->request->post);
				} else {
					$this->model_catalog_manager_product->editProduct($this->request->get['product_id'], $this->request->post);
				}
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function copyProduct() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}
			
			if (!isset($this->request->post['selected'])) {
				$json['error'] = $this->language->get('error_select_product');
			}
			
			if (!isset($json['error'])) {
				foreach ($this->request->post['selected'] as $product_id) {
					$new_product_id = $this->model_catalog_manager_product->copyProduct($product_id);
				}
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteProduct() {
		$this->load->language('catalog/manager_product');
		
		$this->load->model('catalog/manager_product');
		
		$json = array();		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {			
			if (!$this->user->hasPermission('modify', 'catalog/manager_product')) {
				$json['error'] = $this->language->get('error_permission');
			}
			
			if (!isset($this->request->post['selected'])) {
				$json['error'] = $this->language->get('error_select_product');
			}
			
			if (!isset($json['error'])) {
				foreach ($this->request->post['selected'] as $product_id) {
					$this->model_catalog_manager_product->deleteProduct($product_id);
				}
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/manager_product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 15;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_manager_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_manager_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_category() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 15
			);

			$results = $this->model_catalog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_manufacturer() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 15
			);

			$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_model() {
		$json = array();

		if (isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/manager_product');

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			$filter_data = array(
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => 15
			);

			$results = $this->model_catalog_manager_product->getTotalModelProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'model'      => $result['model']
				);
			}
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_sku() {
		$json = array();

		if (isset($this->request->get['filter_sku'])) {
			$this->load->model('catalog/manager_product');
			
			if (isset($this->request->get['filter_sku'])) {
				$filter_sku = $this->request->get['filter_sku'];
			} else {
				$filter_sku = '';
			}

			$filter_data = array(
				'filter_sku'   => $filter_sku,
				'start'        => 0,
				'limit'        => 15
			);

			$results = $this->model_catalog_manager_product->getTotalSkuProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'sku'        => $result['sku']
				);
			}
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_attribute() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/attribute');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 15
			);

			$results = $this->model_catalog_attribute->getAttributes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_attribute_value() {
		$json = array();

		if (isset($this->request->get['filter_attribute_id']) || isset($this->request->get['filter_attribute_value'])) {
			$this->load->model('catalog/manager_product');
			
			if (isset($this->request->get['filter_attribute_id'])) {
				$filter_attribute_id = $this->request->get['filter_attribute_id'];
			} else {
				$filter_attribute_id = '';
			}
			
			if (isset($this->request->get['filter_attribute_value'])) {
				$filter_attribute_value = $this->request->get['filter_attribute_value'];
			} else {
				$filter_attribute_value = '';
			}

			$filter_data = array(
				'filter_attribute_id' 	 => $filter_attribute_id,
				'filter_attribute_value' => $filter_attribute_value,
				'start'        			 => 0,
				'limit'        			 => 15
			);

			$results = $this->model_catalog_manager_product->getTotalAttributeValuesProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'attribute_id' => $result['attribute_id'],
					'text'         => $result['text']
				);
			}
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_location() {
		$json = array();

		if (isset($this->request->get['filter_location'])) {
			$this->load->model('catalog/manager_product');
			
			if (isset($this->request->get['filter_location'])) {
				$filter_location = $this->request->get['filter_location'];
			} else {
				$filter_location = '';
			}

			$filter_data = array(
				'filter_location'   => $filter_location,
				'start'        		=> 0,
				'limit'        		=> 15
			);

			$results = $this->model_catalog_manager_product->getTotalLocationProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'location'   => $result['location']
				);
			}
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function autocomplete_filter() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/filter');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 15
			);

			$filters = $this->model_catalog_filter->getFilters($filter_data);

			foreach ($filters as $filter) {
				$json[] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}