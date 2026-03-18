<?php
namespace yamarket_fusion\Module\controllers\admin;

// use yamarket_fusion\API\Yandex\Market\Partner\PartnerClient;
use yamarket_fusion\API\Yandex\OAuth\OAuthClient;
use yamarket_fusion\Helpers\FileHelper;
use yamarket_fusion\Http\HttpClient;
use yamarket_fusion\Module\models\User;
use yamarket_fusion\Helpers\ArrayHelper;

class ControllerYamarketFusionFeed extends \yamarket_fusion\Module\controllers\module\Feed {
	private $error = array();
	private $catalog_options = array();
	private $weight_classes = array();
	private $CATALOG_URL;
	private $module_user;

	public function __construct($registry) {
		$this->app = 'admin';

		parent::__construct($registry);

		try {
			$this->module_user = new User($registry);
		}
		// maybe first install, table user does't exists
		catch (\Exception $e) {}
		
		
		// check if this class called not from admin app
		if (defined('HTTPS_CATALOG')) {
			$this->CATALOG_URL = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
		}
		
		$this->load->language($this->MODULE_PATH . '/yamarket_fusion');
		$this->load->config(self::code);

		if (isset($this->request->get['profile_id'])) {
			$this->profile_id = $this->request->get['profile_id'];
			if (isset($this->setting[$this->profile_id])) {
				$this->setting[0]['current_profile_id'] = $this->profile_id;
				$this->model_setting->editSettingValue('current_profile_id', $this->profile_id, 0);
			}
		}
		else if ($this->setting) {
			if ($this->setting('current_profile_id', false, 0)) {
				$this->profile_id = $this->setting('current_profile_id', false, 0);
			}
			else {
				$this->profile_id = key($this->setting('setting_profiles', false, 0));
			}
		}
	}

	protected function url($path, $params = '', $claer = false) {
		$url = $this->url->link($path, $params . "&{$this->token_name}={$this->token}", true);
		if ($claer)
			$url = str_replace('&amp;', '&', $url);
		return $url;
	}

	private function checkAuth() {

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->document->addScript('view/javascript/jsmodule/prism.js');
			$this->document->addScript('view/javascript/jsmodule/scroll.js');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/main_oc3.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/prism.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/pe-icon-7-stroke.min.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/select2.min.css');
			$this->document->addScript('view/javascript/select2/select2.min.js');
			
		}
		else {
			$this->document->addScript('view/javascript/jsmodule/prism.js');
			$this->document->addScript('view/javascript/jsmodule/scroll.js');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/main.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/prism.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/pe-icon-7-stroke.min.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/select2.min.css');
			$this->document->addScript('view/javascript/select2/select2.min.js');
		}

		if (!$this->config->get(self::s('use_auth')))
			return true;
		
		$result = $this->module_user->authTokenIsValid();

		if (!$result) {
			$token = $this->module_user->getAuthToken();

			if ($token) {
				$client = new HttpClient($this->config->get(self::s('auth_url')));
				try {
					$request = $client->post('/authByToken', array('auth_token' => $token))->getResponse(HttpClient::formatJSON);
					$result = $request['success'];
					if ($result)
						$this->module_user->updateAuthTokenDateAdded();
				}
				catch (\Exception $e) {
					$this->log->write(__CLASS__ . ': ' . $e->getMessage());
				}
			}
		}

		return $result;
	}
	
	public function index() {
		if (!$this->checkAuth()) {
			$this->authScreen();
			return;
		}
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['form_type'])) {
			$validate_method = 'validateForm' . $this->request->get['form_type'];
			$validate_result = true;
			
			if (method_exists($this, $validate_method) && !$this->{$validate_method}()) {
				$validate_result = false;
			}
			
			if ($validate_result) {
				foreach ($this->request->post['setting'] as $profile_id => $setting) {
					$this->model_setting->editSetting($setting, $profile_id);
				}
				
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url($this->MODULE_PATH . '/yamarket_fusion'));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('view/javascript/jquery.total-storage.js');
		$this->document->addStyle('view/stylesheet/yamarket_fusion/module.css?v=' . $this->config->get(self::s('module_version')));
		$this->document->addStyle('view/javascript/yamarket_fusion/fontello/css/fontello.css?v=' . $this->config->get(self::s('module_version')));

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->document->addScript('view/javascript/jsmodule/prism.js');
			$this->document->addScript('view/javascript/jsmodule/scroll.js');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/main_oc3.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/prism.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/pe-icon-7-stroke.min.css');
		}
		else {
			$this->document->addScript('view/javascript/jsmodule/prism.js');
			$this->document->addScript('view/javascript/jsmodule/scroll.js');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/main.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/prism.css');
			$this->document->addStyle('view/stylesheet/yamarket_fusion/pe-icon-7-stroke.min.css');
		}

		$data = $this->language->all();

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$extensions_url = $this->url('marketplace/extension', 'type=feed');
		}
		else if (version_compare(VERSION, '2.3.0', '>=')) {
			$extensions_url = $this->url('extension/extension', 'type=feed');
		}
		else {
			$extensions_url = $this->url('extension/feed');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url('common/dashboard')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $extensions_url
		);

		$current_url = '';
		$module_query = $this->request->get;
		unset($module_query['route'], $module_query[$this->token_name]);

		foreach ($module_query as $key => $value) {
			$current_url .= "&{$key}={$value}";
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url($this->MODULE_PATH . '/yamarket_fusion', $current_url)
		);

		// get globaly product options
		$this->load->model('catalog/option');
		$this->catalog_options = $this->model_catalog_option->getOptions(array('sort' => 'name'));

		$this->load->model('localisation/weight_class');
		$this->weight_classes = $this->model_localisation_weight_class->getWeightClasses();

		$data['tab_product_param'] = $this->tabProductParam();
		$data['tab_market'] = $this->tabMarket();
		$data['tab_market_category'] = $this->tabCategory();
		$data['tab_market_addon'] = $this->tabAddon();
		$data['tab_products_api'] = $this->tabProductsApi();
		$data['tab_list'] = $this->tabList();
		$data['tab_help'] = $this->tabHelp();
		$data['tab_news'] = $this->tabNews();

		$data['module_version'] = $this->config->get(self::s('module_version'));
		$data['api_offers_list_status'] = $this->config->get(self::s('api_offers_list_status'));

		$data['setting_profiles'] = $this->setting('setting_profiles');
		$data['action_setting_profiles'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/manageSettingProfiles');
		$data['action_export_setting_profiles'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/exportSettingProfiles', '', true);
		$data['action_import_setting_profiles'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/importSettingProfiles', '', true);
		$data['module_url'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', '', true);
		$data['profile_id'] = $this->profile_id;

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['cancel'] = $extensions_url;

		$data['module_user'] = $this->module_user;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->loadView('extension/feed/yamarket_fusion/module', $data));
	}

	public function auth() {
		$output = array('success' => false);

		$this->load->language($this->MODULE_PATH . '/yamarket_fusion');
		
		if ($this->validateAuth()) {
			$client = new HttpClient($this->config->get(self::s('auth_url')));
			try {
				$data = array(
					'domain' => parse_url(HTTPS_CATALOG, PHP_URL_HOST),
					'email' => $this->request->post['email'],
					'password' => $this->request->post['password'],
				);

				$response = $client->post('/auth', $data)->getResponse(HttpClient::formatJSON);
				$output['success'] = $response['success'];
				
				if ($response['success']) {
					$this->module_user->setPermissions($response['data']['permissions']);
					if (isset($response['data']['auth_token']))
						$this->module_user->setAuthToken($response['data']['auth_token']);

						$this->module_user->updateUser();
				}
				else if (!empty($response['error'])) {
					$output['error'] = $response['error'];
				}
			}
			catch (\Exception $e) {
				$this->log->write(__CLASS__ . ': ' . $e->getMessage());
				$output['error'][] = 'Error request'; // todo locale
				$output['error'][] = $e->getMessage();
			}
		}
		
		$this->response->setOutput(json_encode($output));
	}
	
	protected function tabProductParam() {
		$data = $this->language->all();
		
		$options = $this->setting('product_options_data', array());
		$data['product_options_data'] = array();

		foreach ($this->catalog_options as $opt_val) {
			$data['product_options_data'][$opt_val['option_id']] = array(
				'name' => $opt_val['name'],
				'id' => $opt_val['option_id'],
				'unit' => (isset($options[$opt_val['option_id']]) ? $options[$opt_val['option_id']]['unit'] : ''),
				'tag' => (isset($options[$opt_val['option_id']]) ? $options[$opt_val['option_id']]['tag'] : ''),
				'custom_name' => (isset($options[$opt_val['option_id']]['custom_name']) ? $options[$opt_val['option_id']]['custom_name'] : ''),
			);
		}

		$this->loadModel('admin/catalog');

		$data['attribute_groups'] = $this->model_admin_catalog->getAttributesByGroups();

		$data['product_attribute_data'] = $this->setting('product_attribute_data', array());

		$data['profile_id'] = $this->profile_id;

		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', "form_type=ProductParam");

		$data['error'] = empty($this->error['product_param']) ? array() : $this->error['product_param'];
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/product_options', $data);
	}
	
	protected function tabMarket() {
		$data = $this->language->all();
		
		$data['market_shopname'] = $this->setting('market_shopname');
		$data['market_simple_pricelist'] = $this->setting('market_simple_pricelist');
		$data['market_product_available'] = $this->setting('market_product_available');
		$data['market_product_options_combination'] = $this->setting('market_product_options_combination');
		$data['market_product_options_zero_quantity'] = $this->setting('market_product_options_zero_quantity');
		$data['market_product_options_quantity_available'] = $this->setting('market_product_options_quantity_available');
		$data['market_product_quantity_from_options'] = $this->setting('market_product_quantity_from_options');
		$data['market_product_attributes'] = $this->setting('market_product_attributes');
		$data['market_product_dimensions'] = $this->setting('market_product_dimensions');
		$data['market_all_currencies'] = $this->setting('market_all_currencies');
		$data['market_store'] = $this->setting('market_store');
		$data['market_delivery'] = $this->setting('market_delivery');
		$data['market_pickup'] = $this->setting('market_pickup');
		$data['market_store_delivery_options'] = $this->setting('market_store_delivery_options');
		$data['market_store_pickup_options'] = $this->setting('market_store_pickup_options');
		$data['market_store_shipment_options'] = $this->setting('market_store_shipment_options');
		$data['market_product_set_available'] = $this->setting('market_product_set_available');
		$data['market_localcoast'] = $this->setting('market_localcoast');
		$data['market_localdays'] = $this->setting('market_localdays');
		$data['market_localtimes'] = $this->setting('market_localtimes');
		$data['market_pickupcoast'] = $this->setting('market_pickupcoast');
		$data['market_pickupdays'] = $this->setting('market_pickupdays');
		$data['market_pickuptimes'] = $this->setting('market_pickuptimes');
		$data['market_shipmenttimes'] = $this->setting('market_shipmenttimes');
		$data['market_shipmentdays'] = $this->setting('market_shipmentdays');
		$data['market_shipmentid'] = $this->setting('market_shipmentid');

		$data['market_var_1'] = $this->setting('market_var_1');
		$data['market_var_2'] = $this->setting('market_var_2');
		$data['market_var_3'] = $this->setting('market_var_3');
		$data['market_var_4'] = $this->setting('market_var_4');
		$data['market_var_5'] = $this->setting('market_var_5');
		
		$data['manufacturer_warranty_var'] = $this->setting('manufacturer_warranty_var');
		$data['adult_var'] = $this->setting('adult_var');
		$data['market_manufacturer_warranty'] = $this->setting('market_manufacturer_warranty');
		$data['market_adult'] = $this->setting('market_adult');
		$data['market_delivery_ind'] = $this->setting('market_delivery_ind');
		$data['market_pickup_ind'] = $this->setting('market_pickup_ind');
		$data['market_oldprice'] = $this->setting('market_oldprice');
		$data['market_weight'] = $this->setting('market_weight');
		$data['market_picture'] = $this->setting('market_picture');
		$data['market_description'] = $this->setting('market_description');
		$data['market_marketplace_description'] = $this->setting('market_marketplace_description');
		$data['market_extra_description'] = $this->setting('market_extra_description', '');
		$data['market_product_price_with_minumun'] = $this->setting('market_product_price_with_minumun');
		$data['market_product_only_main_image'] = $this->setting('market_product_only_main_image');
		$data['market_raz'] = $this->setting('market_raz');
		$data['market_condition_likenew'] = $this->setting('market_condition_likenew');
		$data['market_condition_used'] = $this->setting('market_condition_used');

		$data['market_sales_note'] = $this->setting('market_sales_note');
		$data['market_sales_note_q'] = $this->setting('market_sales_note_q');
		$data['market_sales_note_text'] = $this->setting('market_sales_note_text');
		$data['market_sales_note_text_q'] = $this->setting('market_sales_note_text_q');
		
		$data['market_product_delivery_options'] = $this->setting('market_product_delivery_options');
		$data['market_product_pickup_options'] = $this->setting('market_product_pickup_options');
		$data['market_product_shipment_options'] = $this->setting('market_product_shipment_options');
		$data['market_localcost_delivery'] = $this->setting('market_localcost_delivery');
		$data['market_preg_rep'] = $this->setting('market_preg_rep');
		
		$data['market_custom_content'] = $this->setting('market_custom_content', '');
		$data['market_description_template'] = $this->setting('market_description_template', '');
		$data['market_yandex_metrika'] = $this->setting('market_yandex_metrika', '');
		$data['market_google_analytics'] = $this->setting('market_google_analytics', '');

		$data['market_product_option_type'] = $this->setting('market_product_option_type');
		$data['market_product_images_from_option'] = $this->setting('market_product_images_from_option');
		
		$data['market_type'] = $this->setting('market_type');
		$data['yml_url_key'] = $this->setting('yml_url_key', '');

		$data['attribute_option_type_price_delimiter'] = $this->config->get(self::s('attribute_price_delimiter'));

		$data['market_purchase_price_markup_options'] = $this->setting('market_purchase_price_markup_options', array());
		$data['market_product_purchase_markup_field'] = $this->config->get(self::s('product_purchase_markup_field'));

		//module status
		$this->load->model('setting/setting');

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->model_setting_setting->editSetting('feed_' . self::code, array('feed_' . self::code . '_status' => '1','feed_' . self::code . '_metrika' => $data['market_yandex_metrika'],'feed_' . self::code . '_analytics' => $data['market_google_analytics']));
		}
		else {
			$this->model_setting_setting->editSetting(self::code, array(self::code . '_status' => '1', self::code . '_metrika' => $data['market_yandex_metrika'], self::code . '_analytics' => $data['market_google_analytics']));
		}

		// Delivery and Pickup options
		$this->load->model('localisation/stock_status');
		
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		$data['weight_classes'] = $this->weight_classes;
		$data['market_delivery_options'] = $this->setting('market_delivery_options', array());

		// Options to combinations
		$data['catalog_options'] = $this->catalog_options;
		
		$data['market_product_options'] = $this->setting('market_product_options', array());   // !

		// Custom tags
		$data['custom_market_tags'] = $this->setting('custom_market_tags', array());
		$data['custom_market_tags_product_fields'] = array(
			'model' => "Model",
			'sku'=> "SKU",
			'upc'=> "UPC",
			'ean'=> "EAN",
			'jan'=> "JAN",
			'isbn'=> "ISBN",
			'mpn'=> "MPN",
			'location'=> "Location",
			'quantity'=> "Количество",
			'minimum'=> "Мин. кол-во",
			'shipping'=> "Необходима доставка",
			'date_available'=> "Date Available",
			'date_modified' => "Дата обновления",
			'length' => "Длина",
			'width'=> "Ширина",
			'height'=> "Высота",
			'weight'=> "Вес",
			'sort_order'=> "Порядок сортировки",
			'sales'=> "Индивидуальный sales",
			'typeprefix'=> "Индивидуальный typeprefix",
		);

		// Currencies
		$this->load->model('localisation/currency');
		$data['market_currency'] = $this->setting('market_currency');
		$data['store_currency'] = $this->setting('store_currency');
		$data['config_store_currency'] = $this->config->get('config_currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['market_category_additional'] = array_map(function($el) {
			$el['price_filter_sign'] = htmlspecialchars_decode($el['price_filter_sign']);
			return $el;
		}, $this->setting('market_category_additional'));

		$data['cat_tree_select'] = $this->treeCatSelect(0);

		$data['yml_caching'] = $this->setting('yml_caching', 0);
		
		$price_url_param = strlen($this->setting('yml_url_key')) ? '&pr_key=' . $this->setting('yml_url_key') : '';
		$module_public_url = $this->CATALOG_URL . '?route=' . $this->MODULE_PATH . '/yamarket_fusion';
		
		$data['pricelist_url'] = $module_public_url . $price_url_param . '&profile_id=' . $this->profile_id;
		$data['pricelist_update_url'] = $module_public_url . '/updateYMLFile&profile_id=' . $this->profile_id;
		$data['pricelist_update_cron_url'] = $module_public_url . '/updateChangedProducts';
		$data['yandex_api_list_update'] = $module_public_url . '/updateUpdatedPriceOffers';
		$data['yandex_api_delete_out_stock_products'] = $module_public_url . '/deleteOutOfStockOffers';

		$data['profile_id'] = $this->profile_id;
		$data['module_user'] = $this->module_user ;
		
		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=Market');

		$data['error'] = empty($this->error['market']) ? array() : $this->error['market'];
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/market', $data);
	}

	protected function tabCategory() {
		$data = $this->language->all();
		
		$data['market_all_categories'] = $this->setting('market_all_categories');
		$data['market_categories'] = $this->setting('market_categories');

		$data['cat_tree_html'] = $this->treeCatHtml(0);

		$this->load->model('catalog/manufacturer');

		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		$data['market_manufacturers'] = $this->setting('market_manufacturers', array());

		$data['profile_id'] = $this->profile_id;

		$data['error'] = empty($this->error['category']) ? array() : $this->error['category'];

		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=Category');
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/category', $data);
	}
	
	protected function tabAddon() {
		$data = $this->language->all();

		$data['market_api_client'] = $this->setting('market_api_client');
		$data['market_api_secret'] = $this->setting('market_api_secret');
		$data['market_shop_id'] = $this->setting('market_shop_id');
		$data['market_shop_feed_id'] = $this->setting('market_shop_feed_id');
		
		$data['url_api_callback'] = $this->CATALOG_URL . '?route=' . $this->MODULE_PATH . '/yamarket_fusion/apiTokenCallback&type=market';
		$data['url_refresh_token'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/refreshToken', 'type=market');
		$data['url_toggle_addons'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/toggleAddon', '', true);
		
		$OAuthClient = new OAuthClient($this->setting('market_api_client'), $this->setting('market_api_secret'));
		
		$data['url_api_token'] = $OAuthClient->getAuthUrl(urlencode($data['url_api_callback']), $this->profile_id);

		$data['has_market_api_token'] = (bool)strlen($this->setting('market_api_token'));

		if ($data['has_market_api_token']) {
			$now = new \DateTime();
			$date = \DateTime::createFromFormat("Y-m-d H:i:s", $this->setting('market_api_token_expires_date'));
			$interval = $now->diff($date);
			
			$data['market_api_token_expired'] = $interval->m * 30 + $interval->d;
		}
		else {
			$data['market_api_token_expired'] = '';
		}

		$data['products_price_autoupdate'] = $this->setting('products_price_autoupdate');
		$data['products_price_autoupdate_cron'] = $this->setting('products_price_autoupdate_cron');
		$data['products_price_autoupdate_cron_type_direct'] = $this->setting('products_price_autoupdate_cron_type_direct');

		$data['timezones'] = timezone_identifiers_list();
		$data['market_timezone'] = $this->setting('timezone');

		$this->load->model('customer/customer_group');

		$data['market_customer_group_id'] = $this->setting('market_customer_group_id');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['weight_classes'] = $this->weight_classes;
		$data['market_weight_class_id'] = $this->setting('market_weight_class_id');
		array_walk($data['weight_classes'], function(&$el) {
			if ($this->config->get('config_weight_class_id') == $el['weight_class_id'])
				$el['title'] .= ' (' . $this->language->get('text_default') . ')';
		});

		// Languges
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['market_language_id'] = $this->setting('market_language_id');

		// Yandex UTM
		$data['market_yandex_utm_status'] = $this->setting('market_yandex_utm_status');
		$data['market_yandex_utm_source'] = $this->setting('market_yandex_utm_source');
		$data['market_yandex_utm_medium'] = $this->setting('market_yandex_utm_medium');

		// Addons
		$data['addon_productmanager_status'] = $this->setting('addon_productmanager_status');
		$data['addon_related_options_status'] = $this->setting('addon_related_options_status');
		$data['addon_currency_plus_status'] = $this->setting('addon_currency_plus_status');
		$data['addon_group_price_status'] = $this->setting('addon_group_price_status');
		$data['addon_option_image_change_status'] = $this->setting('addon_option_image_change_status');
		$data['addon_podarki_status'] = $this->setting('addon_podarki_status');
		$data['addon_multistore_status'] = $this->setting('addon_multistore_status');
		
		$data['addon_currency_plus'] = $this->setting('addon_currency_plus', array());
		$data['addon_group_price'] = $this->setting('addon_group_price', array());

		$data['market_product_url_add_to_cart'] = $this->setting('market_product_url_add_to_cart');

		$data['market_product_watermark'] = $this->setting('market_product_watermark');
		$data['market_product_watermark_position'] = $this->setting('market_product_watermark_position');
		$data['market_product_watermark_image'] = $this->setting('market_product_watermark_image', '');
		$data['product_watermark_positions'] = array(
			'bottomright' => $this->language->get('position_bottom_right'),
			'bottomleft' => $this->language->get('position_bottom_left'),
			'topright' => $this->language->get('position_top_right'),
			'topleft' => $this->language->get('position_top_left'),
		);

		if (version_compare(VERSION, '2.3.0', '>=')) {
			$data['product_watermark_positions']['bottomcenter'] = $this->language->get('position_bottom_center');
			$data['product_watermark_positions']['topcenter'] = $this->language->get('position_top_center');
			$data['product_watermark_positions']['middleleft'] = $this->language->get('position_middle_left');
			$data['product_watermark_positions']['middleright'] = $this->language->get('position_middle_right');
			$data['product_watermark_positions']['middlecenter'] = $this->language->get('position_middle_center');
		}

		$this->load->model('tool/image');

		if (is_file(DIR_IMAGE . $this->setting('market_product_watermark_image'))) {
			$data['product_watermark_thumb'] = $this->model_tool_image->resize($this->setting('market_product_watermark_image'), 100, 100);
		}
		else {
			$data['product_watermark_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['profile_id'] = $this->profile_id;
		$data['module_user'] = $this->module_user ;
		
		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=Addon');

		$data['error'] = empty($this->error['addon']) ? array() : $this->error['addon'];
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/addon', $data);
	}

	protected function tabList() {
		$data = $this->lang;

		$data['error'] = empty($this->error['list']) ? array() : $this->error['list'];

		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=List');
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/list', $data);
	}
	
	protected function tabNews() {
		$data = $this->lang;
		
		$data['error'] = empty($this->error['news']) ? array() : $this->error['news'];

		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=News');
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/news', $data);
	}
		
	protected function tabHelp() {
		$data = $this->lang;
		
		$data['error'] = empty($this->error['help']) ? array() : $this->error['help'];

		$data['action'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', 'form_type=Help');
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/help', $data);
	}
	
	
	protected function tabProductsApi() {
		$data = $this->language->all();
		$page = 1;
		$filter_name = null;
		$filter_model = null;
		$filter_status = null;
		$filter_quantity = null;
		$filter_quantity_sign = '=';
		$url = '';
		$url_limit = '';
		$url_page = '';

		// check the additional url var, because can't change url page param name
		if (isset($this->request->get['list_type']) && $this->request->get['list_type'] == 'products_api') {
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				$url_page .= '&list_type=products_api&page=' . $page;
			}
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
			$url .= '&filter_name=' . urlencode(html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
			$url .= '&filter_model=' . urlencode(html_entity_decode($filter_model, ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
			$filter_quantity_sign = htmlspecialchars_decode($this->request->get['filter_quantity_sign']);
			$url .= '&filter_quаntity=' . urlencode(html_entity_decode($filter_quantity, ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
			$url .= '&filter_status=' . urlencode(html_entity_decode($filter_status, ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['updated_offers_limit'])) {
			$offers_limit = $this->request->get['updated_offers_limit'];
			$url_limit .= '&updated_offers_limit=' . $offers_limit;
		}
		else {
			$offers_limit = $this->config->get('config_limit_admin');
		}
		
		if ($offers_limit < 1)
			$offers_limit = 1;

		$data['offers_limit'] = $offers_limit;
		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_status'] = $filter_status;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_quantity_sign'] = $filter_quantity_sign;

		$filter_data = array(
			'filter_name' => $filter_name,
			'filter_model' => $filter_model,
			'filter_status' => $filter_status,
			'filter_quantity' => $filter_quantity,
			'filter_quantity_sign' => $filter_quantity_sign,
			'start' => ($page - 1) * $offers_limit,
			'limit' => $offers_limit,
		);

		$this->loadModel('module');
		$this->loadModel('admin/catalog');

		$updated_offers = $this->model_module->getUpdatedOffers($filter_data, $this->profile_id);
		$total_updated_offers = $this->model_module->getTotalUpdatedOffers($filter_data, $this->profile_id);

		$data['updated_offers'] = array();

		$this->load->model('tool/image');

		//$offers_aliases = $this->model_module->getOffersAliases(array('filter_alias' => ArrayHelper::getColumn($updated_offers, 'offer_id')));
		//$offers_aliases = ArrayHelper::index($offers_aliases, 'alias');

		$options_value_ids = array();

		$offer_id_option_delemeter = self::offer_id_option_delemeter;
		// add additional data to offers and get all options ids
		$updated_offers = array_map(function($el) use (&$options_value_ids, $offer_id_option_delemeter) {
			$offer_id = isset($el['real_offer_id']) ? $el['real_offer_id'] : $el['offer_id'];
			if ($offer_id != $el['product_id']) {
				$product_options_values = explode($offer_id_option_delemeter, $offer_id);
				array_shift($product_options_values);
				$options_value_ids = array_merge($options_value_ids, $product_options_values);

				$el['product_options_values'] = $product_options_values;
			}
			return $el;
		}, $updated_offers);

		if ($options_value_ids) {
			$formated_options_values = $this->model_admin_catalog->getOptionValuesByValueId($options_value_ids);
		}

		$timezone = $this->setting('timezone', date_default_timezone_get());

		foreach ($updated_offers as $offer) {
			$offer_date = \DateTime::createFromFormat('Y-m-d H:i:s', $offer['date_modified'], new \DateTimeZone('UTC'));
			$offer_date->setTimezone(new \DateTimeZone($timezone));
			
			$product_data = array(
				'product_id' => $offer['product_id'],
				'offer_id' => $offer['offer_id'],
				'product_name' => $offer['name'],
				'href' => $this->url('catalog/product/edit', 'product_id=' . $offer['product_id']),
				'price' => $offer['price'],
				'special_price' => $offer['special_price'],
				'date_modified' => $offer_date->format("Y-m-d H:i:s"),
				'thumb' => $this->model_tool_image->resize($offer['image'], 50, 50),
			);

			if (isset($offer['product_options_values'])) {
				$options_text = array();
				foreach ($offer['product_options_values'] as $opt_val_id) {
					$options_text[] = $formated_options_values[$opt_val_id]['option_name'] . "({$formated_options_values[$opt_val_id]['option_value']})";
				}

				$product_data['name_compounded'] = $offer['name'] . ' ' . implode(', ', $options_text);
			}
		
			$data['updated_offers'][$offer['product_id']][] = $product_data;
		}

		$pagination = new \Pagination();
		$pagination->total = $total_updated_offers;
		$pagination->page = $page;
		$pagination->limit = $offers_limit;
		$pagination->url = $this->url($this->MODULE_PATH . '/yamarket_fusion', $url . $url_limit . '&page={page}&list_type=products_api');

		$data['pagination'] = $pagination->render();

		$data['url_get_updated_offers'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/getUpdatedOffers');
		$data['url_delete_updated_offers'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/deleteApiPrices');

		if (isset($this->request->post['selected_offers'])) {
			$data['selected_offers'] = $this->request->post['selected_offers'];
		}

		$data['products_api_last_updated'] = 0;
		if ($this->setting('products_api_last_updated')) {
			$products_api_updated = \DateTime::createFromFormat('Y-m-d H:i:s', $this->setting('products_api_last_updated'), new \DateTimeZone('UTC'));
			$products_api_updated->setTimezone(new \DateTimeZone($timezone));

			$data['products_api_last_updated'] = $products_api_updated->format('Y-m-d H:i:s');
		}

		$url_module = $this->url($this->MODULE_PATH . '/yamarket_fusion', $url_page . $url_limit);
		
		$data['action_delete'] = $this->url($this->MODULE_PATH . '/yamarket_fusion/deleteApiPrices', $url . $url_page . $url_limit);
		$data['action_filter'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', $url_page . $url_limit);
		$data['url_module'] = $this->url($this->MODULE_PATH . '/yamarket_fusion', $url, true);

		$data['error'] = empty($this->error['products_api']) ? array() : $this->error['products_api'];
		
		return $this->loadView('extension/feed/yamarket_fusion/tab/products_api_list', $data);
	}

	private function authScreen() {
		$data = $this->load->language($this->MODULE_PATH . '/yamarket_fusion');

		if (version_compare(VERSION, '3.0.0', '>=')) {
			$extensions_url = $this->url('marketplace/extension', 'type=feed');
		}
		else if (version_compare(VERSION, '2.3.0', '>=')) {
			$extensions_url = $this->url('extension/extension', 'type=feed');
		}
		else {
			$extensions_url = $this->url('extension/feed');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url('common/dashboard')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $extensions_url
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url($this->MODULE_PATH . '/yamarket_fusion')
		);

		$data['action'] =$this->url($this->MODULE_PATH . '/yamarket_fusion/auth');
		$data['cancel'] = $extensions_url;

		$url = parse_url($this->config->get(self::s('auth_url')));
		$domain = $url['host'] . rtrim($url['path'], '/');
		$data['text_auth_description'] = sprintf($this->language->get('text_auth_description'), $domain);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->loadView('extension/feed/yamarket_fusion/auth', $data));
	}

	public function treeCatSelect($id_cat, $sep = '') {
		$html = '';
		$categories = $this->getCategories($id_cat);

		foreach ($categories as $category) {
			$children = $this->getCategories($category['category_id']);
			
			$html .= '<option value="' . $category['category_id'] . '">' . $sep . ' ' . htmlentities($category['name'], ENT_QUOTES) . '</option>';
			
			if (count($children)) {
				$html .= $this->treeCatSelect($category['category_id'], '--' . $sep);
			}
		}

		return $html;
	}

	public function treeCatHtml($id_cat = 0) {
		$html = '';
		$categories = $this->getCategories($id_cat);
		foreach ($categories as $category) {
			$children = $this->getCategories($category['category_id']);
			$name =  htmlentities($category['name'], ENT_QUOTES);
			if (count($children)) {
				$html .= $this->treeFolder($category['category_id'], $name);
			}
			else {
				$html .= $this->treeItem($category['category_id'], $name);
			}
		}

		return $html;
	}

	public function treeItem($id, $name) {
		$checked = in_array($id, $this->setting('market_categories', array())) ? 'checked' : '';
		$active_class = $checked ? ' tree-selected' : '';
		
		$html = '<li class="tree-item">
						<span class="tree-item-name' . $active_class . '">
							<input type="checkbox" name="setting[' . $this->profile_id . '][market_categories][]" value="' . $id . '"' . $checked . '>
							<i class="tree-dot"></i>
							<label class="">' . $name . '</label>
						</span>
					</li>';
		return $html;
	}

	public function treeFolder($id, $name) {
		$checked = in_array($id, $this->setting('market_categories', array())) ? 'checked' : '';
		$active_class = $checked ? ' tree-selected' : '';
		
		$html = '<li class="tree-folder">
					<span class="tree-folder-name' . $active_class . '">
						<input type="checkbox" name="setting[' . $this->profile_id . '][market_categories][]" value="' . $id . '"' . $checked . '>
						<i class="icon-folder-open"></i>
						<label class="tree-toggler">' . $name . '</label>
					</span>
					<ul class="tree" style="display: block;">' . $this->treeCatHtml($id) . '</ul>
				</li>';
		return $html;
	}

	// todo to model catalog
	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	private function postDefaultValues($keys, $profile_id, $default_profile_id) {
		foreach ($keys as $key) {
			if (!isset($this->request->post['setting'][$profile_id][$key])) {
				$this->request->post['setting'][$profile_id][$key] = $this->getDefaultSetting($key, $default_profile_id);
			}
		}
	}

	protected function validateFormMarket() {
		$setting = $this->request->post['setting'];
		
		if (!isset($setting[$this->profile_id]['market_shopname']) || !utf8_strlen($setting[$this->profile_id]['market_shopname'])) {
			$this->error['market'][]= $this->language->get('error_market_shopname_required');
		}
		
		if (empty($setting[$this->profile_id]['market_currency']))
			unset($this->request->post['setting'][$this->profile_id]['market_currency']);

		if (empty($setting[$this->profile_id]['store_currency']))
			unset($this->request->post['setting'][$this->profile_id]['store_currency']);


		// to do validate localcoast

		// if not has some fields - add in post their default values
		$array_check = array(
			'market_product_available',
			'market_product_options_combination',
			'market_product_attributes',
			'market_product_dimensions',
			'market_all_currencies',
			'market_store',
			'market_delivery',
			'market_pickup',
			'market_oldprice',
			'market_description',
			'market_marketplace_description',
			'market_product_price_with_minumun',
			'market_weight',
			'market_picture',
			'market_var_1',
			'market_var_2',
			'market_var_3',
			'market_var_4',
			'market_var_5',
			'market_adult',
			'market_delivery_ind',
			'market_pickup_ind',
			'market_manufacturer_warranty',
			'market_raz',
			'market_preg_rep',
			'market_sales_note',
			'market_sales_note_text',
			'market_sales_note_q',
			'market_sales_note_text_q',
			'market_delivery_options',
			'market_localcost_delivery',
			'market_product_delivery_options',
			'market_product_options',
			'market_category_additional',
			'market_purchase_price_markup_options',
			'custom_market_tags',
			'yml_caching',
			'market_currency',
		);

		$this->postDefaultValues($array_check, $this->profile_id, 1);

		return empty($this->error['market']);
	}

	protected function validateFormCategory() {
		$setting = $this->request->post['setting'];
		
		if (!$setting[$this->profile_id]['market_all_categories'] && !isset($setting[$this->profile_id]['market_categories'])) {
			$this->error['category'][] = $this->language->get('error_not_selected_categories');
		}

		$array_check = array(
			'market_manufacturers',
		);

		$this->postDefaultValues($array_check, $this->profile_id, 1);

		return empty($this->error['category']);
	}

	protected function validateFormAddon() {
		$setting = $this->request->post['setting'];
		
		if (strlen($setting[$this->profile_id]['market_api_client']) || strlen($setting[$this->profile_id]['market_api_secret'])) {
			if (!$setting[$this->profile_id]['market_api_client']) {
				$this->error['addon'][] = sprintf($this->language->get('error_field_required'), 'API Client ID');
			}
			if (!$setting[$this->profile_id]['market_api_secret']) {
				$this->error['addon'][] = sprintf($this->language->get('error_field_required'), 'API Client Secret');
			}
		}

		$arr_check_common_profile = array(
			'addon_currency_plus',
			'addon_group_price',
		);

		// if (!isset($setting[0]['addon_currency_plus'])) {
		// 	$this->request->post['setting'][0]['addon_currency_plus'] = $this->getDefaultSetting('addon_currency_plus', 0);
		// }
		
		if (!$setting[0]['timezone']) {
			unset($this->request->post['setting'][0]['timezone']);
		}
		
		if (!$setting[$this->profile_id]['market_customer_group_id']) {
			unset($this->request->post['setting'][$this->profile_id]['market_customer_group_id']);
		}

		$this->postDefaultValues($arr_check_common_profile, 0, 0);

		$array_check = array(
			'market_yandex_utm_source',
			'market_yandex_utm_medium',
		);

		$this->postDefaultValues($array_check, $this->profile_id, 1);

		return empty($this->error['addon']);
	}
	
	protected function validateFormSettingProfiles() {
		if (!isset($this->request->post['profiles'])) {
			$this->error['setting_profiles'][] = $this->language->get('error_empty_setting_profiles');
		}
		else {
			foreach ($this->request->post['profiles'] as $profile) {
				if (!utf8_strlen($profile)) {
					$this->error['setting_profiles'][] = $this->language->get('error_setting_profiles_name_required');
					break;
				}
			}
		}

		return empty($this->error['setting_profiles']);
	}

	protected function validateAuth() {
		if (empty($this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_auth_email_required');
		}
		
		if (empty($this->request->post['password'])) {
			$this->error['password'] = $this->language->get('error_auth_password_required');
		}

		return !$this->error;
	}

	public function price_autoupdate_before($arg1 /* route */, $args = array()) {
		if (!$this->getModuleStatus(self::code, 'feed'))
			return null;

		$default_exec = $this->request->get['route'] == 'catalog/product/edit';
		$product_new_data = array();
		$product_id = null;

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		}
		
		if ($default_exec) {
			$product_new_data = $this->request->post;
		}
		else if ($args && version_compare(VERSION, '2.2', '>=')) {
			$product_id = $args[0];
			$product_new_data = $args[1];
		}

		// manualy call from custom trigger; $this->load->controller()
		if (is_array($arg1) && count($arg1) >= 2 && !$default_exec) {
			$product_id = $arg1[0];
			$product_new_data = $arg1[1];
		}

		if (!$product_id || !$product_new_data)
			return null;

		$this->load->model('catalog/product');
		$this->loadModel('admin/catalog');
		
		$product_data = $this->model_admin_catalog->getProduct($product_id);
		$product_categories = $product_data['categories'];
		$setting_profiles = $this->setting('setting_profiles', array(), 0);
		$profiles_to_update = array();

		foreach ($setting_profiles as $profile_id => $val) {
			if ($this->setting('market_type', '', $profile_id) == 'Yandex'
				&& !empty($this->setting('market_api_client', '', $profile_id))
				&& $this->setting('products_price_autoupdate', '', $profile_id)
			) {
				if (!$this->setting('market_all_categories', '', $profile_id) 
					&& !array_intersect(explode(',', $product_categories), $this->setting('market_categories', array(), $profile_id))
				) {
					continue;
				}
				
				$profiles_to_update[] = $profile_id;
			}
		}

		if (!$profiles_to_update)
			return null;

		$has_changes = false;
		$updated_data = array();

		$field_price = $this->productField('price');
		
		if (!isset($product_new_data[$field_price])) {
			// prevent to default if $product_new_data not from default exec
			$field_price = 'price';
		}

		if (isset($product_new_data[$field_price]) && (float)$product_new_data[$field_price] != (float)$product_data[$field_price]) {
			$has_changes = true;
		}

		if (isset($product_new_data['tax_class_id']) && $product_data['tax_class_id'] != $product_new_data['tax_class_id']) {
			$has_changes = true;
		}

		if ($default_exec || isset($product_new_data['product_special'])) {
			$updated_data['special_data'] = $this->model_catalog_product->getProductSpecials($product_id);
		}

		if ($has_changes || !empty($updated_data)) {
			$updated_data['product_id'] = $product_id;
			$updated_data['has_changes'] = $has_changes;
			$this->session->data[self::code]['last_updated_product'] = $updated_data;
		}
	}

	public function price_autoupdate_after() {
		if (!isset($this->session->data[self::code]['last_updated_product']))
			return null;

		$updated_product = $this->session->data[self::code]['last_updated_product'];

		$this->load->model('catalog/product');

		if (!$updated_product['has_changes'] && isset($updated_product['special_data'])) {
			$new_special_data = $this->model_catalog_product->getProductSpecials($updated_product['product_id']);

			if (serialize($updated_product['special_data']) === serialize($new_special_data))
				return;
		}
		
		$this->loadModel('module');
		$this->loadModel('admin/catalog');

		$product = $this->model_admin_catalog->getProduct($updated_product['product_id']);

		$profiles = $this->setting('setting_profiles', array(), 0);

		foreach ($profiles as $profile_id => $name) {
			if ($this->setting('products_price_autoupdate_cron', '', $profile_id)
				//|| !$this->setting('products_price_autoupdate', '', $profile_id)
				//|| $this->setting('market_type', '', $profile_id) != 'Yandex'
			) {
				continue;
			}
			
			$this->updateMarketPrice(array($product['product_id'] => $product), $profile_id);
		}

		unset($this->session->data[self::code]['last_updated_product']);
	}

	// todo
	public function event_product_delete_after() {}

	#work 2
	public function getUpdatedOffers() {
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['updated_offers_limit'])) {
			$url.= '&updated_offers_limit=' . $this->request->get['updated_offers_limit'];
		}

		$result = $this->requestGetUpdatedOffers($this->profile_id);

		if ($result->getErrors()) {
			$this->error['products_api'] = $result->getErrors();
			$this->index();
		}
		else {
			$this->response->redirect($this->url($this->MODULE_PATH . '/yamarket_fusion', $url));
		}
	}

	// todo
	//public function refreshToken() {}
	
	#work 2
	public function deleteApiPrices() {
		$offers_ids = empty($this->request->post['selected_offers']) ? array() : $this->request->post['selected_offers'];

		$url = '';

		if (isset($this->request->get['list_type']) && $this->request->get['list_type'] == 'products_api' && isset($this->request->get['page'])) {
			$url .= '&list_type=products_api&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['updated_offers_limit'])) {
			$url.= '&updated_offers_limit=' . $this->request->get['updated_offers_limit'];
		}

		$redirect = $this->url($this->MODULE_PATH . '/yamarket_fusion', $url);
		
		if ($offers_ids) {
			$offers = array_map(function($el) {return array('offer_id' => $el);}, $offers_ids);

			$result = $this->requestDeleteOffersPrice($offers, $this->profile_id);

			if ($result->getErrors()) {
				$this->error['products_api'][] = $result->getErrors();
				$this->index();
			}
			else {
				$this->session->data['success'] = 'Офферы успешно удалены из маркета!'; // todo locale
				$this->response->redirect($redirect);
			}
		}
		else {
			$this->response->redirect($redirect);
		}
	}

	public function toggleAddon($addon_name = null, $status = null) {
		$json = array('success' => false);
		
		if (isset($this->request->get['addon_name']))
			$addon_name = $this->request->get['addon_name'];

		if (isset($this->request->get['addon_status']))
			$status = $this->request->get['addon_status'];

		if ($addon_name) {
			$method = $status == 1 ? 'install' : 'uninstall';
			$this->load->controller($this->MODULE_PATH . '/yamarket_fusion/' . $addon_name . '/' . $method);

			$this->loadModel('setting');
			$this->model_setting->editSetting(array("addon_{$addon_name}_status" => $status), 0);
			$json['success'] = true;
		}

		// ajax call
		if ($addon_name !== null) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function manageSettingProfiles() {
		$json = array('success' => false);
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateFormSettingProfiles()) {
			$current_profiles = $this->setting('setting_profiles', null, 0);
			$deleted_profiles = array();

			foreach ($current_profiles as $profile_id => $v) {
				if (!isset($this->request->post['profiles'][$profile_id]))
					$deleted_profiles[] = $profile_id;
			}

			if ($deleted_profiles) {
				$this->model_setting->deleteSettingByProfileId($deleted_profiles);
			}

			// Add default setting
			foreach ($this->request->post['profiles'] as $profile_id => $name) {
				if (!isset($this->setting[$profile_id])) {
					$setting_values = $this->getDefaultSetting();
					# 1 is default first profile id
					$this->model_setting->editSetting($setting_values[1], $profile_id);
				}
			}

			$this->model_setting->editSettingValue('setting_profiles', $this->request->post['profiles'], 0);

			if (in_array($this->setting('current_profile_id'), $deleted_profiles)) {
				$this->model_setting->editSettingValue('current_profile_id', key($this->request->post['profiles']), 0);
			}

			$json['success'] = true;
			$this->session->data['success'] = $this->language->get('text_success_edit_setting_profiles');
		}

		empty($this->error['setting_profiles']) ?: $json['error'] = $this->error['setting_profiles'];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function exportSettingProfiles() {
		$json = array('success' => false);

		if (isset($this->request->post['selected'])) {
			$json['success'] = true;
			$json['export'] = array();
			//$json['filename'] = 'yamarket_fusion_setting.json';
			$setting_profiles = $this->setting('setting_profiles', array(), 0);
			
			foreach ($this->request->post['selected'] as $profile_id) {
				$json['export'][] = array(
					$setting_profiles[$profile_id] => $this->setting[$profile_id],
				);
			}
			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function importSettingProfiles() {
		$json = array('success' => false);
		
		if (isset($this->request->files['import'])) {
			$this->loadModel('setting');

			$imported = json_decode(file_get_contents($this->request->files['import']['tmp_name']), true);
			
			if ($imported !== null) {
				$profiles = $this->setting('setting_profiles', array(), 0);
				$profile_id = max(array_keys($profiles));
				
				foreach ($imported[0] as $name => $value) {
					$profile_id++;
					$profiles[$profile_id] = $name;
					$this->model_setting->editSetting(array_replace($this->getDefaultSetting('', 1), $value), $profile_id);
				}

				$this->model_setting->editSettingValue('setting_profiles', $profiles, 0);

				$json['success'] = true;
				$this->session->data['success'] = sprintf($this->language->get('text_success_imprted_profiles'), count($imported));
			}
			else {
				$json['error'][] = $this->language->get('error_import_setting_profile_file_invalid');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function getDefaultSetting($key = '', $profile_id = 0) {
		$setting = array(
			//	'yamarket_fusion_______________________________________________64' // limit db key length symbols

			# 0 is common setting profile_id

			0 => array(
				'setting_profiles' => array(1 => 'Default'),
				'current_profile_id' => 1,
				'product_attribute_data' => array(),
				'timezone' => null,
				'market_yandex_api_refresh_time' => array(), // todo to 1 profile
				'addon_productmanager_status' => 0,
				'addon_currency_plus' => array(),
				'addon_group_price' => array(),
			),
			1 => array(
				'market_type' => 'Yandex',
				'market_all_categories' => 1,			// catall
				'market_categories' => array(),		// color_options
				'market_manufacturers' => array(),	// manufacturers
				'market_simple_pricelist' => 0,		// prostoy
				'market_product_set_available' => 2,	// set_available
				'market_product_available' => 1,		// available
				'market_product_options_combination' => 1, // combination
				'market_product_options_zero_quantity' => 1,
				'market_product_options_quantity_available' => 1,
				'market_product_price_with_minumun' => 0,
				'market_product_quantity_from_options' => 0,
				'market_product_attributes' => 1, 		 // features
				'market_product_dimensions' => 1,		// dimensions
				'market_product_only_main_image' => 0,
				'market_all_currencies' => 1,			// allcurrencies
				'market_store' => 1,					// store
				'market_delivery' => 1,				// delivery
				'market_pickup' => 0,					// pickup
				'market_store_delivery_options' => 0,
				'market_store_pickup_options' => 0,
				'market_shopname' => '',				// shopname
				'market_product_delivery_options' => 0, // delivery_options
				'market_product_pickup_options' => 0,
				'market_localcost_delivery' => 0,		// local_cost_delivery // Включить local_cost_delivery (addon)
				'market_localcoast' => '',			// localcoast
				'market_localdays' => '',				// localdays
				'market_localtimes' => '',			// localtimes
				'market_pickupcoast' => '',			// --global
				'market_pickupdays' => '',			// --global
				'market_pickuptimes' => '',			// --global
				'yml_url_key' => '',
				'yml_caching' => 0,
				'market_product_option_type' => 'option',
				'market_currency' => null,
				'store_currency' => null,
				'market_customer_group_id' => null,
				'market_weight_class_id' => null,
				'market_language_id' => null,
				'market_option_type' => 'option',
				'market_product_images_from_option' => 0,

				'market_delivery_options' => array(),
				'market_purchase_price_markup_options' => array(),

				'market_product_options' => array(),
				'market_category_additional' => array(),
				'custom_market_tags' => array(),
				'product_options_data' => array(),

				'market_shop_id' => '',				// shop_id
				'market_shop_feed_id' => '',		// feed_id
				'products_price_autoupdate' => 0, 	// update on market via api
				'products_price_autoupdate_cron' => 0,
				'products_price_autoupdate_cron_type_direct' => 0,
				
				'market_api_client' => '',
				'market_api_secret' => '',
				'market_api_token' => '',
				'market_api_token_expires_date' => '',
				'market_api_token_refresh' => '',

				'market_yandex_utm_status' => 0,
				'market_yandex_utm_source' => '',
				'market_yandex_utm_medium' => '',

				'market_product_url_add_to_cart' => 0,
				'market_product_watermark' => 0,
				'market_product_watermark_position' => 'bottomright',
			)
		);

		if (strlen($key))
			return isset($setting[$profile_id][$key]) ? $setting[$profile_id][$key] : null;
		else
			return $setting;
	}

	public function install() {
		$this->loadModel('event');
		$this->loadModel('setting');

		// Custom db tables
		$this->loadModel('module');

		$this->model_module->createProductGoTable();

		if (!$this->model_module->checkTables() || $this->config->get(self::s('delete_setting_on_install'))) {
			$this->model_module->install();
		
			$defualt_setting = $this->getDefaultSetting();

			foreach ($defualt_setting as $profile_id => $setting) {
				$this->model_setting->editSetting($setting, $profile_id);
			}
		}


		//$this->setModuleStatus(1);
		
		// todo validate on diff version core auto add permissions
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $this->MODULE_PATH . '/yamarket_fusion');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->MODULE_PATH . '/yamarket_fusion');
		
		// Event autoupdate price; todo enable/add event only when changed module event setting
		if (version_compare(VERSION, "2.3.0", '>=')) {
			$this->model_event->addEvent(self::code, 'admin/model/catalog/product/editProduct/before', $this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_before');
			$this->model_event->addEvent(self::code, 'admin/model/catalog/product/editProduct/after', $this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_after');
		} elseif (version_compare(VERSION, "2.1.0", '>=')) {
			$this->model_event->addEvent(self::code, 'pre.admin.product.edit', $this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_before');
			$this->model_event->addEvent(self::code, 'post.admin.product.edit', $this->MODULE_PATH . '/yamarket_fusion/price_autoupdate_after');
		}


	}
	
	public function uninstall() {
		$this->loadModel('event');
		$this->loadModel('module');
		
		$this->model_event->deleteEventByCode(self::code);

		$this->toggleAddon('productmanager', 0); // delete addon events
		
		if ($this->config->get(self::s('delete_setting_on_uninstall'))) {
			$this->model_module->uninstall();
		}

		//$this->setModuleStatus(0);
		
		if ($this->config->get(self::s('delete_files_on_uninstall'))) {
			// if version < 3.0
			// todo
		}

		try {
			FileHelper::removeDirectory(DIR_IMAGE . 'cache/yamarket_fusion');
		}
		catch (\Exception $e) {
			// nothing
		}
	}

	/*private function setModuleStatus($status) {
		$status = (int)$status;
		
		$this->load->model('setting/setting');
		
		if (version_compare(VERSION, '3.0.0', '>=')) {
			$this->model_setting_setting->editSetting('feed_' . self::code, array('feed_' . self::code . '_status' => $status,'feed_' . self::code . '_metrika' => 'qweqwewqe'));
		}
		else {
			$this->model_setting_setting->editSetting(self::code, array(self::code . '_status' => $status, self::code . '_metrika' => 'q1231232'));
		}
	}*/
}