<?php
namespace yamarket_fusion\Module\controllers\module;

use yamarket_fusion\Module\models\addon\currency_plus\CurrencyPlus as AddonCurrencyPlus;
use yamarket_fusion\Helpers\ArrayHelper;
use yamarket_fusion\Helpers\ServiceData;
use yamarket_fusion\Xml as Xml;
use yamarket_fusion\Module\controllers\service\Yandex\Service as YandexService;
use yamarket_fusion\Module\models\addon\image_option_change\ImageOptionChange as AddonImageOptionChange;

class Feed extends \yamarket_fusion\Base\Controller {
	const code = 'yamarket_fusion';
	const offer_id_option_delemeter = 'c'; // imprint > delimiter, todo replace all
	const offer_id_attribute_delimiter = 'a';
	const offer_id_attribute_value_delimiter = 'v';
	const product_price_decimal_place = 2;

	protected $profile_id, $setting, $currencies, $categories;

	public function __construct($registry) {
		parent::__construct($registry);

		// For correctly loaded models In childs classes first must be set app name and only after that call parent __construct()

		//$this->loadModel('catalog');
		$this->loadModel('setting');
		$this->loadModel('module');
		$this->loadModel('addon/related_options');

		$this->currencies = $this->model_module->getCurrencies();

		try {
			// todo get setting from model and refresh for cahce
			$this->setting = $this->model_setting->getSetting();

			$this->loadModel('addon/currency_plus');
			$this->loadModel('addon/group_price');
			$this->loadModel('addon/image_option_change');
		}
		catch (\Exception $e) {}
	}

	protected static function s($key) {
		return self::code . '_' . $key;
	}

	// #prototype
	//protected function setSetting() {
	//	$this->loadModel('setting');
	//	$this->registry->set(self::s('setting'), $this->model_setting->getSetting());
	//}

	protected function setting($key, $return_value = null, $setting_profile_id = null) {
		$output = $return_value;
		$profile_id = is_null($setting_profile_id) ? $this->profile_id : $setting_profile_id;

		// get from setting profile_id
		if (isset($this->setting[$profile_id][$key])) {
			$output = $this->setting[$profile_id][$key];
		} // get from common setting
		else if (is_null($setting_profile_id) && isset($this->setting[0][$key])) {
			$output = $this->setting[0][$key];
		}

		return $output;
	}

	protected function getModuleStatus($name, $type) {
		$key = (version_compare(VERSION, '3.0.0', '>=') ? $type . '_' : '') . $name . '_status';
		return (bool)$this->config->get($key);
	}

	protected function getOfferCurrencyCode($product_data = array(), $profile_id = null) {
		if ($profile_id === null && !isset($this->profile_id))
			throw new \Exception('Can\'t get currency code, profile id is undefined');

		$profile_id = $profile_id === null ? $this->profile_id : $profile_id;
		$code = '';

		$default_code = $this->setting('market_currency', $this->config->get('config_currency'), $profile_id);

		if ($product_data) {
			if ($this->setting('addon_currency_plus_status') && $this->model_addon_currency_plus->getStatus() && $this->model_addon_currency_plus->getProductField('base_currency_code')) {
				$product_currency = $product_data['base_currency_code'];
				$currency = $this->currencies[$product_currency];
				$code = $currency['status'] ? $product_currency : $default_code;
			}
			else {
				$code = $default_code;
			}
		}
		else {
			$code = $default_code;
		}

		return $code;
	}

	protected function calcOfferPriceMarkup($price, $product_data, $profile_id = null) {
		$profile_id = $profile_id === null ? $this->profile_id : $profile_id;

		if ($this->setting('addon_group_price_status', 0, 0) && $this->model_addon_group_price->getStatus()) {
			$price = $this->model_addon_group_price->calcProductPrice(
				$price,
				$product_data,
				$this->setting('market_customer_group_id', $this->config->get('config_customer_group_id'), $profile_id)
			);
		}

		return $price;
	}

	protected function productField($field) {
		if ($this->setting('addon_currency_plus_status', 0, 0) && $this->model_addon_currency_plus->getStatus()) {
			$addon_field = $this->model_addon_currency_plus->getProductField($field);
			if ($addon_field !== null)
				$field = $addon_field;
		}

		return $field;
	}

	protected function makeOfferCombination($offer, $product, $app = null) {
		if ($this->setting('addon_related_options_status', 0, 0)
			&& ($options_variants = $this->model_addon_related_options->getProductRelatedOptionsVariants($product['product_id']))
		){
			return $this->makeOfferCombination__relatedoptions($offer, $product, $options_variants);
		}
		else {
			return $this->makeOfferCombination__default($offer, $product);
		}
	}

	protected function makeOfferCombination__default($offer, $product) {
		//$options_unit = $this->setting('product_options_unit');
		$suboffers = array();

		$arr_group_options = $this->getProductOptionGroups($product['product_id']);
		
		foreach ($arr_group_options as $arr_options) {
			if (!$suboffers) {
				foreach ($arr_options as $option) {
					if (is_object($offer)) {
						$suboffer = clone $offer;
						$this->formatOfferOptions__default_object($suboffer, $product, $option);
					}
					else {
						$suboffer = $offer;
						$this->formatOfferOptions__default_array($suboffer, $product, $option);
					}
					
					$suboffers[] = $suboffer;
				}
			}
			else {
				$temp_suboffers = $suboffers;
				$suboffers = array();

				foreach ($temp_suboffers as $suboffer) {
					foreach ($arr_options as $option) {
						if (is_object($suboffer)) {
							$sec_suboffer = clone $suboffer;
							$this->formatOfferOptions__default_object($sec_suboffer, $product, $option);
						}
						else {
							$sec_suboffer = $suboffer;
							$this->formatOfferOptions__default_array($sec_suboffer, $product, $option);
						}
						
						$suboffers[] = $sec_suboffer;
					}
				}
			}
		}

		if (!$suboffers)
			$suboffers = array($offer);

		return $suboffers;
	}

	private function makeOfferCombination__relatedoptions($offer, $product, $options_variants) {
		$suboffers = array();

		foreach ($options_variants as $option_variant) {
			$related_options = $this->model_addon_related_options->getProductRelatedOptions($product['product_id'], $option_variant['relatedoptions_variant_product_id']);
			
			if (!$related_options) continue;

			foreach ($related_options as $related_option) {
				if (!$related_option['quantity'] && !$this->setting('market_product_options_zero_quantity'))
					continue;
				
				if (is_object($offer)) {
					$suboffer = clone $offer;
					$this->formatOfferOptions__relatedoptions_object($suboffer, $product, $related_option);
				}
				else {
					$suboffer = $offer;
					$this->formatOfferOptions__relatedoptions_array($suboffer, $product, $related_option);
				}
				
				$suboffers[] = $suboffer;
			}
		}

		if (!$suboffers)
			$suboffers = array($offer);

		return $suboffers;
	}

	private function formatOfferOptions__relatedoptions_array(&$suboffer, $product, $related_option) {
		$option_values = $this->model_addon_related_options->getProductRelatedOptionsValues($related_option['relatedoptions_id']);
		$ro_config = $this->config->get('related_options');
		
		$suboffer['id'] .= self::offer_id_option_delemeter . implode(self::offer_id_option_delemeter, array_map(function ($el) {
			return $el['option_value_id'];
		}, $option_values));

		if (!empty($ro_config['spec_price']) || !empty($ro_config['spec_price_special'])) {
			if (!empty($ro_config['spec_price_special']) && (float)$related_option['special_price']) {
					$suboffer['special_price'] = $related_option['special_price'];
			}

			$option_price = $this->recalcOptionPrice($suboffer, $related_option);

			if (!empty($ro_config['spec_price']) && (float)$related_option['price']) {
					$suboffer['price'] = $option_price['price'];
			}

			if (!empty($ro_config['spec_price_special']) && isset($suboffer['special_price'])) {
					$suboffer['special_price'] = $option_price['special_price'];
			}
		}
	}

	private function formatOfferOptions__relatedoptions_object(&$suboffer, $product, $related_option) {
		//$options_unit = $this->setting('product_options_unit');
		$options_data = $this->setting('product_options_data', array());
		$option_values = $this->model_addon_related_options->getProductRelatedOptionsValues($related_option['relatedoptions_id']);
		$ro_config = $this->config->get('related_options');

		$suboffer->id .= self::offer_id_option_delemeter . implode(self::offer_id_option_delemeter, array_map(function($el) {return $el['option_value_id'];}, $option_values));
		$suboffer->group_id = $product['product_id'];

		if ($related_option['quantity'] < 1 && $this->setting('market_product_options_quantity_available'))
			$suboffer->available = 'false';

		if ($this->setting('market_product_quantity_from_options'))
			$suboffer->setQuantity($related_option['quantity']);

			if ($this->setting('market_sales_note') == 1 ) {
				if ($this->setting('market_sales_note_q') == 1 ){
					if ($related_option['quantity'] >= 1 && $this->setting('market_product_options_quantity_available')){
						$suboffer->sales_notes = ($this->setting('market_sales_note_text'));
					}
					if ($related_option['quantity'] < 1 && $this->setting('market_product_options_quantity_available')){
						$suboffer->sales_notes = ($this->setting('market_sales_note_text_q'));
					}
				}
				if ($this->setting('market_sales_note_q') == 0 ){
					if ($related_option['quantity'] >= 1 && $this->setting('market_product_options_quantity_available')){
						$suboffer->sales_notes = ($this->setting('market_sales_note_text'));
					}
					if ($related_option['quantity'] < 1 && $this->setting('market_product_options_quantity_available')){
						$suboffer->sales_notes = ($this->setting('market_sales_note_text_q'));
					}
				}
			}

		$url_options_part = array();
		$option_config_data = $this->setting('product_options_data');
		
		foreach ($option_values as $option_value) {
			$key_name = $suboffer->model !== null ? 'model' : 'name';
			$suboffer->{$key_name} .= ', ' . $option_value['option_name'] . ' ' . $option_value['option_value'];
			
			//$url_options_part[] = $option_value['option_value_id'];
			$url_options_part[] = $option_value['product_option_value_id'];
			
			if (!empty($options_data[$option_value['option_id']]['tag'])) {
				$suboffer->setValue(new Xml\Node($options_data[$option_value['option_id']]['tag'], $option_value['option_value']));
			}
			else {
				$option_name = (isset($option_config_data[$option_value['option_id']]['custom_name']) && strlen($option_config_data[$option_value['option_id']]['custom_name'])) ? $option_config_data[$option_value['option_id']]['custom_name'] : $option_value['option_name'];
				
				if (isset($options_data[$option_value['option_id']]['unit']) && utf8_strlen($options_data[$option_value['option_id']]['unit'])) {
					$suboffer->addParam($option_value['option_id'], $option_name, $option_value['option_value'], $options_data[$option_value['option_id']]['unit']);
				}
				else {
					$suboffer->addParam($option_value['option_id'], $option_name, $option_value['option_value']);
				}
			}
		}

		$suboffer->url .= '#' . implode('-', $url_options_part);

		// rewrite default model
		if ($ro_config['spec_model'] > 0 && utf8_strlen($related_option['model'])) {
			$suboffer->model = $related_option['model'];
		}
		
		if (!empty($ro_config['spec_price']) || !empty($ro_config['spec_price_special'])) {
			// rewrite special price from related option
			if (!empty($ro_config['spec_price_special']) && (float)$related_option['special_price'])
					$suboffer->oldprice = $related_option['special_price'];
			
			$option_price = $this->recalcOptionPrice(array(
					'price' => $suboffer->price,
					'special_price' => $suboffer->oldprice
				),
				$related_option
			);

			if (!empty($ro_config['spec_price']) && (float)$related_option['price'])
					$suboffer->price = $option_price['price'];

			// rewrite special price from recalculated option price
			if (!empty($ro_config['spec_price_special']) && !is_null($suboffer->oldprice))
					$suboffer->oldprice = $option_price['special_price'];
		}

		if (!empty($ro_config['spec_weight']) && (float)$related_option['weight']) {
			$offer_weight = $suboffer->getWeight();
			
			if ($related_option['weight_prefix'] == '+') {
				$suboffer->setWeight($offer_weight + $related_option['weight']);
			} elseif ($related_option['weight_prefix'] == '-') {
				$suboffer->setWeight($offer_weight - $related_option['weight']);
			}
			else {
				$suboffer->setWeight((float)$related_option['weight']);
			}
		}

		// Option images
		if ($this->setting('addon_option_image_change_status') && $this->getModuleStatus(AddonImageOptionChange::setting_code, 'module')) {
			$option_values_ids = ArrayHelper::getColumn($option_values, 'option_value_id');
			$images = $this->model_addon_image_option_change->getProductOptionImages($product['product_id'], $option_values_ids);
			
			foreach ($images as $image) {
				$suboffer->addImage(str_replace(array('&amp;', ' '), array('&', '%20'), $this->model_tool_image->resize($image['image'], $suboffer::image_width, $suboffer::image_height)), 'option');
			}
		}
	}
	
	private function recalcOptionPrice($product_data, $option) {
		$field_option_price = $this->productField('option_price');

		// Prevent to default field name if is not standart option
		if (!isset($option[$field_option_price])) {
			$field_option_price = 'price';
		}
		
		if ($option['price_prefix'] == '+') {
			$product_data['price'] += $option[$field_option_price];
			
			if (isset($product_data['special_price']))
				$product_data['special_price'] += $option[$field_option_price];
		}
		elseif ($option['price_prefix'] == '-') {
			$product_data['price'] -= $option[$field_option_price];
			
			if (isset($product_data['special_price']))
				$product_data['special_price'] -= $option[$field_option_price];
		}
		elseif ($option['price_prefix'] == '=') {
			$product_data['price'] = $option[$field_option_price];
		}

		return $product_data;
	}
	
	private function formatOfferOptions__default_array(&$suboffer, $product, $option) {
		$option_price = $this->recalcOptionPrice($suboffer, $option);
		
		$suboffer['id'] = $suboffer['id'] . self::offer_id_option_delemeter . $option['option_value_id'];
			$suboffer['price'] = $option_price['price'];

		if (isset($suboffer['special_price']))
			$suboffer['special_price'] = $option_price['special_price'];
	}
	
	private function formatOfferOptions__default_object(&$suboffer, $product, $option) {
		$options_data = $this->setting('product_options_data', array());

		// Set offer quantity by lowest options quantity
		if ($this->setting('market_product_quantity_from_options')) {
			// rewrite product quantity
			if ($suboffer->group_id === null) {
				$suboffer->setQuantity($option['quantity']);
			} // set quantity with compare previous options
			else if ($suboffer->getQuantity() > $option['quantity']) {
				$suboffer->setQuantity($option['quantity']);
			}
		}
		
		$suboffer->id		= $suboffer->id . self::offer_id_option_delemeter . $option['option_value_id'];
		$suboffer->group_id = $product['product_id'];
		
		$key_name = $suboffer->model !== null ? 'model' : 'name';
		$suboffer->{$key_name} .= ', ' . $option['option_name'] . ' ' . $option['name'];

		if ($option['quantity'] < 1 && $this->setting('market_product_options_quantity_available')) {
			$suboffer->available = 'false';
		}

		if ($this->setting('market_sales_note') == 1 ) {
			if ($this->setting('market_sales_note_q') == 1 ){
				if ($option['quantity'] >= 1 && $this->setting('market_product_options_quantity_available')){
					$suboffer->sales_notes = ($this->setting('market_sales_note_text'));
				}
				if ($option['quantity'] < 1 && $this->setting('market_product_options_quantity_available')){
					$suboffer->sales_notes = ($this->setting('market_sales_note_text_q'));
				}
			}
			if ($this->setting('market_sales_note_q') == 0 ){
				if ($option['quantity'] >= 1 && $this->setting('market_product_options_quantity_available')){
					$suboffer->sales_notes = ($this->setting('market_sales_note_text'));
				}
				if ($option['quantity'] < 1 && $this->setting('market_product_options_quantity_available')){
					$suboffer->sales_notes = ($this->setting('market_sales_note_text_q'));
				}
			}
		}

		if (!empty($options_data[$option['option_id']]['tag'])) {
			$suboffer->setValue(new Xml\Node($options_data[$option['option_id']]['tag'], $option['name']));
		}
		else {
			if (isset($options_data[$option['option_id']]['unit']) && utf8_strlen($options_data[$option['option_id']]['unit'])) {
				$suboffer->addParam($option['option_id'], $option['option_name'], $option['name'], $options_data[$option['option_id']]['unit']);
			}
			else {
				$suboffer->addParam($option['option_id'], $option['option_name'], $option['name']);
			}
		}
		
		if (strpos($suboffer->url, '#') === false) {
			$suboffer->url .= '#' . $option['product_option_value_id'];
		}
		else {
			$suboffer->url .= '-' . $option['product_option_value_id'];
		}

		$option_price = $this->recalcOptionPrice(array(
				'price' => $suboffer->price,
				'special_price' => $suboffer->oldprice
			),
			$option
		);

		
		$suboffer->price = $option_price['price'];

		if (!is_null($suboffer->oldprice))
			$suboffer->oldprice = $option_price['special_price'];
			
			

		if (isset($option['weight']) && isset($option['weight_prefix'])) {
			$offer_weight = $suboffer->getWeight();
			
			if ($option['weight_prefix'] == '+') {
				$suboffer->setWeight($offer_weight + $option['weight']);
			} elseif ($option['weight_prefix'] == '-') {
				$suboffer->setWeight($offer_weight - $option['weight']);
			}
		}

		// Option images
		$option_images = array();

		if ($this->setting('addon_option_image_change_status') && $this->getModuleStatus(AddonImageOptionChange::setting_code, 'module')) {
			$images = $this->model_addon_image_option_change->getProductOptionImages($product['product_id'], $option['option_value_id']);
			foreach ($images as $image) {
				$option_images[] = $image['image'];
			}
		}
		else {
			if ($option['image']) {
				$option_images[] = $option['image'];
			}
		}

		foreach ($option_images as $image) {
			$suboffer->addImage(str_replace(array('&amp;', ' '), array('&', '%20'), $this->model_tool_image->resize($image, $suboffer::image_width, $suboffer::image_height)), 'option');
		}
	}

	private function getProductOptionGroups($product_id) {
		$arr_group_options = array();

		$this->loadModel('admin/catalog');
		
		$option_products_data = $this->setting('product_options_data');
		$product_options = $this->model_admin_catalog->getProductOptions($product_id, array(), array(
			'language_id' => $this->setting('market_language_id', $this->config->get('config_language_id')),
		));

		if ($product_options) {
			foreach ($this->setting('market_product_options', array()) as $option_data) {
				$options = array();

				foreach ($product_options as $product_option) {
					if (!$this->setting('market_product_options_zero_quantity') && !$product_option['quantity'])
						continue;

					// rewrite original option name
					if (isset($option_products_data[$product_option['option_id']]['custom_name']) 
						&& strlen($option_products_data[$product_option['option_id']]['custom_name'])
					) {
						$product_option['option_name'] = $option_products_data[$product_option['option_id']]['custom_name'];
					}

					if (in_array($product_option['option_id'], $option_data['options']))
						$options[] = $product_option;
				}

				if ($options)
					$arr_group_options[] = $options;
			}
		}

		return $arr_group_options;
	}

	protected function divideProductAttriburtes($attributes) {
		$standart = array();
		$attr_options = array();
		$price_delimiter = $this->config->get(self::s('attribute_price_delimiter'));

		$fn_Chek = function($attr) use($price_delimiter) {
			$grouped = explode($price_delimiter, $attr);
			return (count($grouped) > 1) && is_numeric($grouped[1]);
		};

		foreach ($attributes as $attribute) {
			$param_data = isset($this->setting('product_attribute_data', array())[$attribute['attribute_id']]) ? $this->setting('product_attribute_data')[$attribute['attribute_id']] : array();

			if (!empty($param_data['disabled']))
				continue;
			
			$new_attribute = $attribute;
			$new_attribute['attribute_sub_id'] = '';
			
			if ($param_data && isset($param_data['delimiter']) && utf8_strlen($param_data['delimiter'])) {
				$subattrs = explode($param_data['delimiter'], $attribute['text']);

				foreach ($subattrs as $i => $subattr) {
					$new_attribute['text'] = $subattr;
					if (count($subattrs) > 1)
						$new_attribute['attribute_sub_id'] = self::offer_id_attribute_value_delimiter . $i;

					if ($this->setting('market_product_option_type') == 'attribute') {
						$fn_Chek($subattr) ? $attr_options[] = $new_attribute : $standart[] = $new_attribute;
					}
					else {
						$standart[] = $new_attribute;
					}
					
				}
			}
			else {
				if ($this->setting('market_product_option_type') == 'attribute') {
					$fn_Chek($attribute['text']) ? $attr_options[] = $new_attribute : $standart[] = $new_attribute;
				}
				else {
					$standart[] = $new_attribute;
				}
			}
		}

		return array('standart' => $standart, 'attribute_option' => $attr_options);
	}
	
	protected function makeOfferAttributeCombination($offer, $attributes) {
		$output = array();

		$grouped_attributes = ArrayHelper::index($attributes, 'attribute_id', true);

		foreach ($grouped_attributes as $attr_id => $attr_group) {
			$param_data = isset($this->setting('product_attribute_data')[$attr_id]) ? $this->setting('product_attribute_data')[$attr_id] : array();
			$unit = (isset($param_data['unit']) && utf8_strlen($param_data['unit'])) ? $param_data['unit'] : null;
			
			$offers = $output ? $output : array($offer);
			$pre_result = array();
			
			foreach ($attr_group as $attribute) {
				$attr_val = explode($this->config->get(self::s('attribute_price_delimiter')), $attribute['text']);
				$sign = substr($attr_val[1], 0, 1);
				in_array($sign, array('+', '-')) ?: $sign = false;

				foreach ($offers as $item) {
					if (is_object($item)) {
						$suboffer = clone $item;
						if (count($attributes) > 1) {
							$suboffer->group_id = $suboffer->product_id;
							$suboffer->id .= self::offer_id_attribute_delimiter . $attribute['attribute_id'] . $attribute['attribute_sub_id'];
						}

						if ($this->setting('market_simple_pricelist')) {
							$suboffer->name .= ', ' .$attribute['name'] . ' ' . $attr_val[0];
						}
						else {
							$suboffer->model .= ', ' .$attribute['name'] . ' ' . $attr_val[0];
						}
						
						if (!empty($param_data) && strlen($param_data['tag'])) {
							$suboffer->setValue(new Xml\Node($param_data['tag'], $attr_val[0]));
						}
						else {
							$attr_name = (isset($param_data['custom_name']) && strlen($param_data['custom_name'])) ? $param_data['custom_name'] : $attribute['name'];
							$suboffer->addParam($attribute['attribute_id'] . $attribute['attribute_sub_id'], $attr_name, $attr_val[0], $unit);
						}

						$sign ? $suboffer->price += (float)$attr_val[1] : $suboffer->price = (float)$attr_val[1];

						if (!is_null($suboffer->oldprice) && $sign) {
							$suboffer->oldprice += (float)$attr_val[1];
						}
						
						$pre_result[] = $suboffer;
					}
					else {
						$suboffer = $item;

						if (count($attributes) > 1)
							$suboffer['id'] .= self::offer_id_attribute_delimiter . $attribute['attribute_id'] . $attribute['attribute_sub_id'];

						$sign ? $suboffer['price'] += (float)$attr_val[1] :  $suboffer['price'] = (float)$attr_val[1];

						if (isset($suboffer['special_price']) && $sign)
							$suboffer['special_price'] += (float)$attr_val[1];

						$pre_result[] = $suboffer;
					}

				}
			}

			$output = array_merge($output, $pre_result);
		}

		return $output;
	}

	protected function categoriesNesting($categories) {
		$nesting = $categories;

		foreach ($nesting as &$category) {
			$total = 0;
			
			if (!isset($category['parents'])) {
				$current_category = $category;
				// save progrees to var $loop to can stop for optimisation on early calculated categories
				$loop = $current_category['parent_id'] != 0;

				while ($loop) {
					$current_category = $nesting[$current_category['parent_id']];
					$loop = $current_category['parent_id'] != 0;
					$total++;
					
					if (isset($current_category['parents'])) {
						$total += $current_category['parents'];
						$loop = false;
					}
				}
			}

			$category['parents'] = $total;
		}

		return $nesting;
	}

	/**
	 * @var array $product_category_ids - product categories
	 * @var array $categories - all categories
	 * @return int - max nesting category id
	**/
	protected function getProductMaxNestingCategoryId($product_category_ids, $categories) {
		$category_id = null;
		
		if (count($product_category_ids) > 1) {
			$level = 0;
			$categories = array_intersect_key($categories, array_flip($product_category_ids));
			
			array_walk($categories, function ($el) use (&$category_id, &$level) {
				if ($el['parents'] > $level) {
					$level = $el['parents'];
					$category_id = $el['category_id'];
				}
			});
		}

		if (!$category_id && isset($categories[$product_category_ids[0]]))
			$category_id = $product_category_ids[0];

		return $category_id;
	}

	private function makeUpOfferMarkupData($product, $categories) {
		// todo define required field and check it; some validation for $product is object or array
		
		$product_categories = explode(',', $product['categories']);
		
		if (!$this->setting('market_all_categories') && $this->setting('market_categories')) {
			$product_categories = array_intersect($product_categories, $this->setting('market_categories'));
		}
		
		$output = array(
			'category_id' => $this->getProductMaxNestingCategoryId($product_categories, $categories),
			'manufacturer_id' => $product['manufacturer_id'],
		);

		return $output;
	}


	// ======= Yndex APi Wrapper methods =========

	protected function prepareOffers($products, $setting_profile_id) {
		$output = array();
		$pre_result = array();

		$namespace = version_compare(VERSION, '2.2.0', '>=') ? 'Cart\\Tax' : 'Tax';

		if (!$this->registry->has('tax')) {
			$this->registry->set('tax', new $namespace($this->registry));
		}

		$this->loadModel('admin/catalog');

		if ($this->setting('market_product_attributes', false, $setting_profile_id)) {
			//$product_ids = array_map(function($el) {return $el['product_id'];}, $products);
			$product_ids = ArrayHelper::getColumn($products, 'product_id');
			$attributes = $this->model_admin_catalog->getProductsAttributes($product_ids, array(
				'language_id' => $this->setting('market_language_id', $this->config->get('config_language_id')),
			));
		}

		$field_price = $this->productField('price');
		$field_special = $this->productField('special');

		foreach ($products as $product) {
			if ($product[$field_special] && $product[$field_special] < $product[$field_price]) {
				$product_price = floatval($product[$field_special]);
				$special_price = $product[$field_price];
			}
			else {
				$product_price = $product[$field_price];
				$special_price = null;
			}

			$offer = array(
				'price' => $product_price,
				'id' => $product['product_id'],
				'product_id' => $product['product_id'],
				'group_id' => $product['product_id'],
				'tax_class_id' => $product['tax_class_id'],
				'manufacturer_id' => $product['manufacturer_id'],
				'categories' => $product['categories'],
			);

			if ($special_price) {
				$offer['special_price'] = $special_price;
			}

			if ($this->setting('market_option_type', null, $setting_profile_id) == 'option' && $this->setting('market_product_options_combination', null, $setting_profile_id)) {
				$pre_result = array_merge($pre_result, $this->makeOfferCombination($offer, $product));
			}
			else if ($this->setting('market_option_type', null, $setting_profile_id) == 'attribute' && $this->setting('market_product_attributes', null, $setting_profile_id)) {
				if (isset($attributes[$product['product_id']])) {
					$product_attributes = $this->divideProductAttriburtes($attributes[$product['product_id']]);
					$pre_result = array_merge($pre_result, $this->makeOfferAttributeCombination($offer, $product_attributes['attribute_option']));
				}
				else {
					$pre_result[] = $offer;
				}
			}
			else {
				$pre_result[] = $offer;
			}
		}

		if (!$this->categories) {
			$categories = $this->categoriesNesting($this->model_admin_catalog->getCategories());
			$this->categories = array_filter($categories, function($el) {return (bool)$el['status'];});
		}

		$shop_currency = $this->setting('store_currency', $this->config->get('config_currency'), $setting_profile_id);
		$customer_group_id = $this->setting('marker_customer_group_id', $this->config->get('config_customer_group_id'), $setting_profile_id);

		$offer_class = '\\yamarket_fusion\\Xml\\Catalog\\' . $this->setting('market_type', null, $setting_profile_id) . '\\Offer';
		
		foreach ($pre_result as &$offer) {
			$markup_data = $this->makeUpOfferMarkupData($offer, $this->categories);
			
			$offer['price'] = $this->calcOfferPriceMarkup($offer['price'], $markup_data, $customer_group_id);
			
			if ($offer['price'] <= 0)
				continue;
			
			$offer['offer_id'] = $offer_class::generateIdAlias($offer['id']);
			$offer['price'] = number_format($this->currency->convert($this->tax->calculate($offer['price'], $offer['tax_class_id'], $this->config->get('config_tax')), $shop_currency, $this->getOfferCurrencyCode($products[$offer['product_id']], $setting_profile_id)), self::product_price_decimal_place, '.', '');
			
			if (isset($offer['special_price'])) {
				$offer['special_price'] = $this->calcOfferPriceMarkup($offer['special_price'], $markup_data, $customer_group_id);
				
				$offer['special_price'] = number_format($this->currency->convert($this->tax->calculate($offer['special_price'], $offer['tax_class_id'], $this->config->get('config_tax')), $shop_currency, $this->getOfferCurrencyCode($products[$offer['product_id']], $setting_profile_id)), self::product_price_decimal_place, '.', '');
			}
			
			$output[$offer['offer_id']] = $offer;
		}

		return $output;
	}

	public function updateMarketPrice($products, $setting_profile_id) {
		$this->loadModel('admin/catalog');

		$offers = $this->prepareOffers($products, $setting_profile_id);
		$result = $this->requestUpdateOffersPrice($offers, $setting_profile_id);

		if ($result->getData()) {
			$updated = array_uintersect($offers, $result->getData(), function($a, $b) {
				return $a['product_id'] == $b['product_id'] ? 0 : -1;
			});
			
			$this->model_module->updateMarketOffers($updated, $setting_profile_id);
			
			return new ServiceData($updated);
		}
		else {
			return $result;
		}
	}

	// method body code moved to yandex service
	//protected function requestUpdatePrice($offers, $setting_profile_id) {
	protected function requestUpdateOffersPrice($offers, $setting_profile_id) {
		$service = new YandexService($this->registry);
		$result = $service->updateOffersPrice($offers, $setting_profile_id);

		return $result;
	}
	
	/**
	 * @description Reset price setted by requestUpdatePrice
	 */
	protected function requestDeleteOffersPrice($updated_market_products, $setting_profile_id) {
		$service = new YandexService($this->registry);
		$result = $service->deleteOffersPrice($updated_market_products, $setting_profile_id);

		if (!$result->getErrors())
			$this->model_module->deleteUpdatedOffers(ArrayHelper::getColumn($updated_market_products, 'offer_id'), $setting_profile_id);

		return $result;
	}
	
	/**
	 * @description Request for update current pricelist to catalog
	 */
	//protected function requestDeletePrice($updated_market_products, $setting_profile_id) {
	protected function requestRefreshPricelist($setting_profile_id) {
		$service = new YandexService($this->registry);
		$result = $service->refreshPricelist($setting_profile_id);

		return $result;
	}

	# work 2
	/**
	 * @description Request for get updated offers by requestUpdateOffersPrice
	 */
	protected function requestGetUpdatedOffers($setting_profile_id) {
		$service = new YandexService($this->registry);
		$output = $service->getUpdatedOffers($setting_profile_id);

		if (!$output->getErrors()) {
			try {
				$service->saveUpdatedOffers($output->getData(), $setting_profile_id);

				$date = new \DateTime('now', new \DateTimeZone('UTC'));
				$this->model_setting->editSetting(array('products_api_last_updated' => $date->format('Y-m-d H:i:s')), $setting_profile_id);
			}
			catch (\Exception $e) {
				// todo log error
				$output = new ServiceData();
				$output->addError('Error save to db updated offers from market, profile ID ' . $setting_profile_id);
			}
		}

		return $output;
	}
}