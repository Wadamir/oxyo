<?php

namespace yamarket_fusion\Module\controllers\catalog;

use yamarket_fusion\Xml as Xml;
use yamarket_fusion\Module\models\catalog\Catalog as ModelCatalog;
use yamarket_fusion\API\Yandex\Metrica\Helpers\Url as YandexUrlHelper;
use yamarket_fusion\Helpers\StringHelper;
use yamarket_fusion\Helpers\ArrayHelper;
use yamarket_fusion\Helpers\MathHelper;
use yamarket_fusion\Module\models\addon\image_option_change\ImageOptionChange as ModelAddonImageOptionChange;
use yamarket_fusion\Module\models\addon\multistore\Multistore as ModelAddonMultistore;

class ControllerYamarketFusionFeed extends \yamarket_fusion\Module\controllers\module\Feed
{
    private $product_price_decimal_place = 2; // todo move to offer class
    private $shop_currency;
    private $offers_currency;
    private $yml_filepath;

    // temp
    private $mpn_format = true; // формат кол-ва для npm: false - одинаковое количество для всех

    // debug
    private $debug = false;
    private $start;
    private $end;
    private $start_time;
    private $count_product = 0;

    public function __construct($registry)
    {
        //$this->app = 'catalog';
        parent::__construct($registry);

        if (!empty($this->request->get['debug']))
            $this->init_debug();

        $this->load->config(self::code);

        if (isset($this->request->get['profile_id']) && isset($this->setting[$this->request->get['profile_id']]))
            $this->profile_id = $this->request->get['profile_id'];

        $this->yml_filepath = DIR_DOWNLOAD . self::code . "_pricelist_{$this->profile_id}.yml";
    }

    private function init_debug()
    {
        $this->debug = true;
        $this->start = memory_get_usage();
        $this->start_time = microtime(true);
    }

    public function __destruct()
    {
        if ($this->debug) {
            echo 'MAX -' . memory_get_peak_usage() / 1000 . 'кб<br>';
            echo 'DIFF - ' . ($this->end - $this->start) / 1000 . 'кб<br>';
            echo 'TIME - ' . (microtime(true) - $this->start_time) . 'ms';
            exit();
        }
    }

    public function index()
    {
        if (!$this->profile_id)
            return new \Action('error/not_found');

        if (strlen($this->setting('yml_url_key'))) {
            if (!isset($this->request->get['pr_key']) || $this->request->get['pr_key'] != $this->setting('yml_url_key'))
                return new \Action('error/not_found');
        }

        try {
            if ($this->setting('yml_caching')) {
                if (is_file($this->yml_filepath)) {
                    $content = file_get_contents($this->yml_filepath);
                    ob_start();
                    $content_buffer = file_get_contents($this->yml_filepath);
                    echo $content_buffer;
                    $content_buffer_length = ob_get_length();
                    ob_end_clean();
                } else {
                    $content = $this->updateYMLFile();
                    ob_start();
                    $content_buffer = $this->updateYMLFile();
                    echo $content_buffer;
                    $content_buffer_length = ob_get_length();
                    ob_end_clean();
                }
            } else {
                $content = $this->getYML();
                ob_start();
                $content_buffer = $this->getYML();
                echo $content_buffer;
                $content_buffer_length = ob_get_length();
                ob_end_clean();
            }
        } catch (\Exception $e) {
            echo 'Error on line ', $e->getLine(), '; ', $e->getMessage();

            if ($this->config->get('config_error_display')) {
                echo '<pre>', $e->getTraceAsString(), '</pre>';
            }
            exit;
        }

        $this->response->addHeader('Content-Type: application/xml; charset=utf-8');
        $this->response->addHeader('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT');
        $this->response->addHeader('Content-Length: ' . $content_buffer_length);
        $this->response->setOutput($content);
    }

    public function updateYMLFile()
    {
        if (!$this->profile_id)
            return new \Action('error/not_found');

        $content = $this->getYML();
        file_put_contents($this->yml_filepath, $content);
        echo "Всего товаров обработано: " . $this->count_product;

        return $content;
    }

    private function getYML()
    {
        // Correct the default opencart timezone
        if ($timezone = $this->setting('timezone'))
            date_default_timezone_set($timezone);

        $language_id = $this->setting('market_language_id') ?: $this->config->get('config_language_id');

        $yml_class = '\\yamarket_fusion\\Xml\\Catalog\\' . $this->setting('market_type') . '\\Yml';

        $YML = new $yml_class();

        $YML->shop_name = $this->setting('market_shopname');
        $YML->company = $this->config->get('config_name');
        $YML->url = $this->config->get('config_url');

        $currencies = $this->currencies;

        $this->shop_currency = $this->setting('store_currency', $this->config->get('config_currency'));
        $this->offers_currency = $this->getOfferCurrencyCode();
        $currency_default = isset($currencies[$this->offers_currency]) ? $currencies[$this->offers_currency] : null;

        if ($YML::allowed_currencies)
            $currencies = array_intersect_key($currencies, array_flip($YML::allowed_currencies));


        if (!isset($currencies[$this->offers_currency]) || !$currencies[$this->offers_currency]['status'])
            throw new \Exception("Currency {$this->offers_currency} not allowed for {$this->setting('market_type')}");

        if ($this->setting('market_all_currencies')) { // old allcurrencies
            foreach ($currencies as $currency) {
                if (!$currency['status']) continue;

                $currency['rate'] = $currency_default['value'] / $currency['value'];

                $YML->addCurrency($currency);
            }
        } else {
            $currency = $currency_default;
            $currency['rate'] = $currency_default['value'] / $currency['value'];

            $YML->addCurrency($currency);
        }

        // Categories
        $this->loadModel('catalog/catalog');

        $this->categories = $this->categoriesNesting($this->model_catalog_catalog->getCategories(array(['language_id' => $language_id])));
        $categories = array_filter($this->categories, function ($el) {
            return (bool)$el['status'];
        });

        if (!empty($this->setting('market_categories')) || $this->setting('market_all_categories')) { // old categories all_categories
            $ids_cat = $this->setting('market_all_categories') ? '' : $this->setting('market_categories');
        } else {
            throw new \Exception('Need select categories');
        }

        $category_additional_data = ArrayHelper::index($this->setting('market_category_additional', array()), 'category_id');

        $category_names = array();

        foreach ($categories as $category) {
            //$category_name = isset($custom_category_names[$category['category_id']]) ? $custom_category_names[$category['category_id']] : $category['name'];
            $category_name = empty($category_additional_data[$category['category_id']]['name']) ? $category['name'] : $category_additional_data[$category['category_id']]['name'];

            $category_names[$category['category_id']] = $category_name;

            if (!$this->setting('market_all_categories') && !in_array($category['category_id'], $this->setting('market_categories', array())))
                continue;

            $category['name'] = $category_name;

            $YML->addCategory($category);
        }

        // Global delivery-options
        if ($this->setting('market_store_delivery_options')) {
            $local_shipping_сost = array_filter(explode(';', $this->setting('market_localcoast')), function ($el) {
                return (bool)strlen(trim($el));
            }); // old localcoast
            $local_shipping_days = array_filter(explode(';', $this->setting('market_localdays')), function ($el) {
                return (bool)strlen(trim($el));
            }); // old localdays
            $local_shipping_times = explode(';', $this->setting('market_localtimes')); // old localtimes
            if (count($local_shipping_сost) != count($local_shipping_days))
                throw new \Exception("'Стоимость доставки в домашнем регионе' и/или 'Срок доставки в домашнем регионе' заполнены с ошибкой"); // to do locale error

            $shop_deliveryOptionsNode = new Xml\Node('delivery-options');

            foreach ($local_shipping_сost as $key => $value) {
                $shop_deliveryOption = new Xml\Node('option', null, false);
                $shop_deliveryOption->cost = $value;
                $shop_deliveryOption->days = $local_shipping_days[$key];

                if (isset($local_shipping_times[$key]) && $local_shipping_times[$key] != '') {
                    $shop_deliveryOption->{'order-before'} = $local_shipping_times[$key];
                }

                $shop_deliveryOptionsNode->setValue($shop_deliveryOption);
            }

            if ($local_shipping_сost)
                $YML->setValue($shop_deliveryOptionsNode);
        }

        // Global pickup options
        if ($this->setting('market_store_pickup_options')) {
            $pickup_сost = array_filter(explode(';', $this->setting('market_pickupcoast')), function ($el) {
                return (bool)strlen(trim($el));
            });
            $pickup_days = array_filter(explode(';', $this->setting('market_pickupdays')), function ($el) {
                return (bool)strlen(trim($el));
            });
            $pickup_times = explode(';', $this->setting('market_pickuptimes'));

            if (count($pickup_сost) != count($pickup_days))
                throw new \Exception("'Стоимость самовывоза в домашнем регионе' и/или 'Срок самовывоза в домашнем регионе' заполнены с ошибкой"); // to do locale error

            $shop_deliveryOptionsNode = new Xml\Node('pickup-options');

            foreach ($pickup_сost as $key => $value) {
                $shop_pickupOption = new Xml\Node('option', null, false);
                $shop_pickupOption->cost = $value;
                $shop_pickupOption->days = $pickup_days[$key];

                if (isset($pickup_times[$key]) && $pickup_times[$key] != '') {
                    $shop_pickupOption->{'order-before'} = $pickup_times[$key];
                }

                $shop_deliveryOptionsNode->setValue($shop_pickupOption);
            }

            if ($pickup_сost)
                $YML->setValue($shop_deliveryOptionsNode);
        }

        // Global shipment-options
        if ($this->setting('market_store_shipment_options')) {
            $local_shipment_times = array_filter(explode(';', $this->setting('market_shipmenttimes')), function ($el) {
                return (bool)strlen(trim($el));
            }); // old localcoast
            $local_shipment_days = array_filter(explode(';', $this->setting('market_shipmentdays')), function ($el) {
                return (bool)strlen(trim($el));
            }); // old localdays
            $local_shipment_id = explode(';', $this->setting('market_shipmentid')); // old localtimes
            if (count($local_shipment_times) != count($local_shipment_days))
                throw new \Exception("'Время работы доставки в домашнем регионе' и/или 'Срок доставки в домашнем регионе' заполнены с ошибкой"); // to do locale error

            $shop_shipmentOptionsNode = new Xml\Node('shipment-options');

            foreach ($local_shipment_times as $key => $value) {
                $shop_shipmentOption = new Xml\Node('option', null, false);
                $shop_shipmentOption->{'order-before'} = $value;
                $shop_shipmentOption->days = $local_shipment_days[$key];

                if (isset($local_shipment_id[$key]) && $local_shipment_id[$key] != '') {
                    $shop_shipmentOption->id = $local_shipment_id[$key];
                }

                $shop_shipmentOptionsNode->setValue($shop_shipmentOption);
            }

            if ($local_shipment_times)
                $YML->setValue($shop_shipmentOptionsNode);
        }

        // temp custom content
        if ($this->setting('market_custom_content', false))
            $YML->custom_content = html_entity_decode($this->setting('market_custom_content'));

        $isCatalogOzon = $this->setting('market_type', '', $this->profile_id) == 'Ozon'; // futurefix

        $use_multistore_addon =
            $isCatalogOzon
            && $this->setting('addon_multistore_status')
            && $this->getModuleStatus(ModelAddonMultistore::setting_code, 'module');

        if ($use_multistore_addon) {
            $this->loadModel('addon/multistore');
            $multistores = $this->model_addon_multistore->getActiveStores(['language_id' => $language_id]);
        }


        // Offers

        // names of used standart field can be owerride by other modules
        $field_price = $this->productField('price');
        $field_special = $this->productField('special');

        $products_query_data = array(
            'category_id' => $ids_cat,
            'manufacturer_id' => $this->setting('market_manufacturers'), // old manufacturers
            'customer_group_id' => $this->setting('market_customer_group_id', $this->config->get('config_customer_group_id')),
            'language_id' => $language_id,
            'start' => 0,
            'limit' => ModelCatalog::PRODUCTS_QUERY_LIMIT,
        );

        while ($products = $this->model_catalog_catalog->getProducts($products_query_data)) {
            $products_query_data['start'] += ModelCatalog::PRODUCTS_QUERY_LIMIT;

            $product_ids = array_keys($products);

            // Get all products attributes
            if ($this->setting('market_product_attributes')) {
                $attributes = $this->model_catalog_catalog->getProductsAttributes($product_ids, array('language_id' => $language_id));
            }

            // Temporaly cache module "image option change" data for current products
            if ($this->setting('addon_option_image_change_status') && $this->getModuleStatus(ModelAddonImageOptionChange::setting_code, 'module')) { // todo validate getModuleStatus param type on opencart version 3
                $this->model_addon_image_option_change->storeProductsOptionsImages($product_ids);
            }

            // Temporaly cache module "multistore" data for current products
            if ($use_multistore_addon) {
                $product_multistores = $this->model_addon_multistore->getProductsStoresByProductIds($product_ids);

                if ($this->setting('market_product_options_combination'))
                    $product_options_multistores = $this->model_addon_multistore->getProductsOptionStoresByProductIds($product_ids);
            }

            foreach ($products as $product) {
                $this->count_product  += 1;
                if (
                    $this->setting('market_product_available')
                    && !$this->setting('market_product_options_combination')
                    && !$this->setting('market_product_quantity_from_options')
                    && $product['quantity'] < 1
                ) {
                    continue;
                }

                // Special custom db fields filtering
                if ($this->setting('market_raz') == 1) {
                    $db_filter_field = $this->config->get(self::s('db_filter'));
                    $field_name = $db_filter_field[$this->setting('market_type')];
                    if (!is_null($product[$field_name]) && !$product[$field_name]) {
                        continue;
                    }
                }

                $category_id = $this->getProductMaxNestingCategoryId(explode(',', $product['categories']), $categories);

                if (!$category_id)
                    continue;

                // Filter by category price
                if (!empty($category_additional_data[$category_id]['price_filter_sign']) && !empty($category_additional_data[$category_id]['price_filter_value'])) {
                    if (!MathHelper::comparison(
                        htmlspecialchars_decode($category_additional_data[$category_id]['price_filter_sign']),
                        $product[$field_price],
                        $category_additional_data[$category_id]['price_filter_value']
                    )) {
                        continue;
                    }
                }

                $available = 'true';

                $offer_class = '\\yamarket_fusion\\Xml\\Catalog\\' . $this->setting('market_type') . '\\Offer';
                $offerNode = new $offer_class();

                $offerNode->id = $product['product_id'];
                $offerNode->product_id = $product['product_id'];
                $offerNode->categoryId = $category_id;

                if ($this->setting('market_type') == 'Promua') {
                    $offerNode->presence_sure = $available;
                }

                $offer_currency = $this->getOfferCurrencyCode($product, $this->profile_id);

                if (!isset($currencies[$offer_currency]))
                    throw new \Exception("Offer with product id {$product['product_id']} has currency {$offer_currency} not allowed for this catalog");

                // $offerNode->currencyId = $currency_default['code'];
                $offerNode->currencyId = $offer_currency;

                $offerNode->setQuantity($product['quantity']);

                $url_params = array(
                    'product_id' => $product['product_id'],
                );

                if ($this->setting('market_product_url_add_to_cart'))
                    $url_params['add_cart'] = 1;

                $offerNode->url = htmlspecialchars_decode($this->url->link('product/product', $url_params, true));
                $price = $product[$field_price];
                $price_special = $product[$field_special];



                if ($this->setting('market_type') == 'YandexLite') {
                    $offerNode->price = round(floatval($price), $this->product_price_decimal_place);
                    $offerNode->setValue(new Xml\Node('oldprice', number_format(ceil($price * 1.3), 2, '.', '')));
                    /* $offerNode->oldprice = round(floatval($price), $this->product_price_decimal_place); */

                    /* if ($this->setting('market_oldprice') == 1) {
						if ($product[$field_special] && $price_special < $price) {
							$offerNode->oldprice = $offerNode->price;
							$offerNode->price = round(floatval($price_special), $this->product_price_decimal_place);
						}
					} */
                } else {
                    $offerNode->price = round(floatval($price), $this->product_price_decimal_place);
                    if ($this->setting('market_oldprice') == 1) {
                        if ($product[$field_special] && $price_special < $price) {
                            $offerNode->oldprice = $offerNode->price;
                            $offerNode->price = round(floatval($price_special), $this->product_price_decimal_place);
                        }
                    }
                }


                if ($this->setting('market_var_1') == 1) {
                    $offerNode->pickup = ($this->setting('market_pickup') && $product['pickup'] == '1') ? 'true' : 'false';
                } else {
                    $offerNode->pickup = ($this->setting('market_pickup') ? 'true' : 'false'); // old pickup
                }
                if ($this->setting('market_var_2') == 1) {
                    $offerNode->store = ($this->setting('market_store') && $product['store'] == '1') ? 'true' : 'false';
                } else {
                    $offerNode->store = ($this->setting('market_store') ? 'true' : 'false'); // old store
                }
                if ($this->setting('market_var_3') == 1) {
                    $offerNode->delivery = ($this->setting('market_delivery') && $product['shipping'] == '1') ? 'true' : 'false';
                } else {
                    $offerNode->delivery = ($this->setting('market_delivery')  ? 'true' : 'false');
                }
                if ($this->setting('manufacturer_warranty_var') == 1) {
                    if ($this->setting('market_var_4') == 1) {
                        $offerNode->manufacturer_warranty = ($this->setting('market_manufacturer_warranty') && $product['garanty'] == '1') ? 'true' : 'false';
                    } else {
                        $offerNode->manufacturer_warranty = ($this->setting('market_manufacturer_warranty')  ? 'true' : 'false');
                    }
                }
                if ($this->setting('adult_var') == 1) {
                    if ($this->setting('market_var_5') == 1) {
                        $offerNode->adult = ($this->setting('market_adult') && $product['adult'] == '1') ? 'true' : 'false';
                    } else {
                        $offerNode->adult = ($this->setting('market_adult')  ? 'true' : 'false');
                    }
                }

                if (strlen($product['manufacturer'])) {
                    $offerNode->vendor = $product['manufacturer'];
                }

                $offerNode->setCategoryName($category_names[$offerNode->categoryId]);


                if ($this->setting('market_extra_description') == 1) {
                    $desc = $product['description'] . ' ' . $this->setting('market_description_template');
                } else {
                    $desc = $product['description'];
                }

                $new_desc = preg_replace('|<br>(.*)|Uis', '\n$1', $desc);

                if ($this->setting('market_description') == 1) {
                } else {
                    if ($this->setting('market_preg_rep') == 0) {
                        $offerNode->setDescription(html_entity_decode(html_entity_decode($desc, ENT_QUOTES)));
                    } else {
                        $offerNode->setDescription(html_entity_decode(html_entity_decode($new_desc, ENT_QUOTES)));
                    }
                }

                $marketplace_desc = '';

                if ($this->setting('market_extra_description') == 1) {
                    $marketplace_desc = $product['marketplace_description'] . ' ' . $this->setting('market_description_template');
                } else {
                    //$marketplace_desc = $product['marketplace_description'];
                }

                if ($this->setting('market_marketplace_description') == 0) {
                } else {
                    $offerNode->setDescription(html_entity_decode(html_entity_decode($marketplace_desc, ENT_QUOTES)));
                }



                // Оutlets: todo move to custom tags and do depends for other custom tags

                if ($this->setting('market_type') == 'Goods') {
                    $id_stok = '001';
                    $id_instock = $product['quantity'];

                    $stocks = explode(',', $id_stok);
                    $instocks = explode(',', $id_instock);

                    // todo validate this count from globaly config
                    if (count($instocks) != count($stocks) || !$this->mpn_format) {
                        $instocks = array_fill(0, count($stocks), $product['quantity']);
                    }

                    $outletsNode = new Xml\Node('outlets');

                    foreach ($stocks as $i => $value) {
                        $outletNode = new Xml\Node('outlet', null, true);
                        $outletNode->id = $value;
                        $outletNode->instock = $instocks[$i];

                        $outletsNode->setValue($outletNode);
                    }

                    $offerNode->setValue($outletsNode);
                }

                /* Kaspi.kz */
                if ($this->setting('market_type') == 'Kaspikz') {
                    $kaspi_outlets = new Xml\Node('availabilities');
                    $kaspi_outlet = new Xml\Node('availability', null, false);
                    if ($product['quantity'] <= 0) {
                        $kaspi_outlet->available = 'no';
                    } else {
                        $kaspi_outlet->available = 'yes';
                    }
                    $kaspi_outlet->storeId = 'id_market';
                    $kaspi_outlets->setValue($kaspi_outlet);

                    $offerNode->setValue($kaspi_outlets);
                }

                if ($this->setting('market_condition_likenew') == 1) {
                    if ($product['likenew'] == 1) {
                        $conditionNode = new Xml\Node('condition');
                        $conditionNode->type = 'likenew';

                        $conditionNode->setValue(new Xml\Node('reason', $product['reason']));
                        //$conditionNode->setValue($reasonNode);

                        $offerNode->setValue($conditionNode);
                    }
                }

                if ($this->setting('market_condition_used') == 1) {
                    if ($product['used'] == 1) {
                        $conditionNode = new Xml\Node('condition');
                        $conditionNode->type = 'used';

                        $conditionNode->setValue(new Xml\Node('reason', $product['reason']));
                        //$conditionNode->setValue($reasonNode);

                        $offerNode->setValue($conditionNode);
                    }
                }

                // changed cost from out of stock to stock
                // local_delivery_cost For mail.ru price
                /*if ($this->setting('market_localcost_delivery')) { // old local_cost_delivery
					$stock_id = $product['stock_status_id'];
					
					// todo fix it
					if (strlen($delivery_cost_params[0])) {
						$offerNode->setValue(new Xml\Node('local_delivery_cost', $delivery_cost_params[0]));
					}
				}*/

                // Pictures
                $product_images = array();

                if ($product['image']) {
                    $product_images[] = $product['image'];
                }

                if ($product['images']  && !$this->setting('market_product_only_main_image')) {
                    $product_images = array_merge($product_images, explode(',', $product['images']));
                }

                $this->load->model('tool/image');

                $this->loadModel('image');

                $image_ozon = '';
                $image_datas = [];
                $image_rows = [];

                foreach ($product_images as $key => $product_image) {
                    if ($key == 0) {
                        $image_rows['main_image'] = [
                            'image' => $product_image,
                            'sort' => 'main'
                        ];
                    } else {
                        $sort = $this->model_catalog_catalog->getImageSort($product['product_id'], $product_image);
                        $image_rows['additional_image'][$sort] = $product_image;
                    }
                }

                if (!empty($image_rows['additional_image'])) {
                    ksort($image_rows['additional_image']);
                }

                foreach ($image_rows as $key => $value) {
                    // Fixed 2025.04.26 
                    if ($key == 'main_image') {
                        if ($this->setting('market_picture') == 1) {
                            $image = HTTPS_SERVER . 'image/' . $value['image'];
                        } else {
                            $image = $this->model_tool_image->resize($value['image'], $offerNode::image_width, $offerNode::image_height);
                        }

                        if ($image) {
                            if ($this->setting('market_product_watermark')) {
                                $img_path = $this->model_image->extractImagePathFromUrl($image);

                                // var_dump($img_path);
                                $image = $this->model_image->watermark(
                                    $img_path,
                                    $this->setting('market_product_watermark_image'),
                                    $this->setting('market_product_watermark_position')
                                );
                            }
                            $image = str_replace(array('&amp;', ' '), array('&', '%20'), $image);
                            $offerNode->addImage($image);
                        }
                    } else {
                        foreach ($value as $i => $product_image) {
                            $i = $this->model_catalog_catalog->getImageSort($product['product_id'], $product_image);
                            // Fixed 2025.04.26 
                            if ($i == "0") {
                                //     $image = $this->model_tool_image->crop($product_image, 0, 0, 450, 450);
                                $image = $this->model_tool_image->resize($product_image, $offerNode::image_width, $offerNode::image_height);
                                $image_ozon = $image;
                            } else {
                                if ($this->setting('market_picture') == 1) {
                                    $image = HTTPS_SERVER . 'image/' . $product_image;
                                } else {
                                    $image = $this->model_tool_image->resize($product_image, $offerNode::image_width, $offerNode::image_height);
                                }
                            }

                            if ($image) {

                                if ($this->setting('market_product_watermark')) {
                                    $img_path = $this->model_image->extractImagePathFromUrl($image);

                                    // var_dump($img_path);
                                    $image = $this->model_image->watermark(
                                        $img_path,
                                        $this->setting('market_product_watermark_image'),
                                        $this->setting('market_product_watermark_position')
                                    );
                                }


                                $image = str_replace(array('&amp;', ' '), array('&', '%20'), $image);

                                $offerNode->addImage($image);
                            }
                        }
                    }
                }


                // if ($product['product_id'] == 10531){
                //     $this->log->write($image_rows);
                //     $this->log->write($product_images);
                // }

                // $image_datas = [];

                // foreach ($image_datas as $i => $product_image) {

                // 	$i = $this->model_catalog_catalog->getImageSort($product['product_id'], $product_image);
                // 	if ($i == "0"){
                // 		$image = $this->model_tool_image->crop($product_image, 0, 0, 450, 450);
                // 		$image_ozon = $image;

                // 	} else {
                // 		if ($this->setting('market_picture') == 1) {
                // 			$image = HTTPS_SERVER . 'image/'. $product_image;
                // 		} else {
                // 			$image = $this->model_tool_image->resize($product_image, $offerNode::image_width, $offerNode::image_height);
                // 		}
                // 	}

                // 	if ($image) {

                // 		if ($this->setting('market_product_watermark')) {
                // 			$img_path = $this->model_image->extractImagePathFromUrl($image);

                // 			// var_dump($img_path);
                // 			$image = $this->model_image->watermark(
                // 				$img_path,
                // 				$this->setting('market_product_watermark_image'),
                // 				$this->setting('market_product_watermark_position')
                // 			);
                // 		}	


                // 		$image = str_replace(array('&amp;', ' '), array('&', '%20'), $image);

                // 		$offerNode->addImage($image);
                // 	}
                // }

                if ($this->setting('market_type') == 'YandexTurbo') {
                    if (!empty($image_ozon)) {
                        $offerNode->setValue(new Xml\Node('mainpicture', $image_ozon));
                    }
                }

                if ($this->setting('market_weight') == 1) {
                    $offerNode->setWeight((float)$product['weight']);
                }

                if ($this->setting('market_simple_pricelist')) { // prostoy
                    $offerNode->name = $product['name'];
                    #$offerNode->available = $available;
                    if ($this->setting('market_product_dimensions') && (float)$product['length'] && (float)$product['width'] && (float)$product['height']) { // old dimensions
                        $offerNode->dimensions = number_format($product['length'], 1, '.', '')
                            . '/' . number_format($product['width'], 1, '.', '')
                            . '/' . number_format($product['height'], 1, '.', '');
                    }
                } else {
                    $offerNode->model = $product['name'];
                    //$offerNode->downloadable = 'false';
                    $offerNode->type = 'vendor.model';
                    #$offerNode->available = $available;
                    if ($this->setting('market_product_dimensions') && (float)$product['length'] && (float)$product['width'] && (float)$product['height']) { // old dimensions
                        $offerNode->dimensions = number_format($product['length'], 1, '.', '')
                            . '/' . number_format($product['width'], 1, '.', '')
                            . '/' . number_format($product['height'], 1, '.', '');
                    }

                    // rec - related?
                    // to do validate: add only if exists in current products
                    /*if ($product['rel'])
						$offerNode->rec = $product['rel'];*/
                }

                // Attributes
                $attributes_grouped = $this->divideProductAttriburtes((isset($attributes[$product['product_id']])) ? $attributes[$product['product_id']] : array());

                if ($this->setting('market_product_attributes')) { // old features
                    $date_limit = false;
                    $date_time = '';
                    foreach ($attributes_grouped['standart'] as $attribute) {
                        $param_data = isset($this->setting('product_attribute_data', array())[$attribute['attribute_id']]) ? $this->setting('product_attribute_data')[$attribute['attribute_id']] : array();

                        if (!empty($param_data) && utf8_strlen($param_data['tag'])) { // param like solo tag
                            $offerNode->setValue(new Xml\Node($param_data['tag'], $attribute['text']));
                        } else {
                            $param_unit = (!empty($param_data) && utf8_strlen($param_data['unit'])) ? $param_data['unit'] : null;
                            $param_name = (!empty($param_data) && utf8_strlen($param_data['custom_name'])) ? $param_data['custom_name'] : $attribute['name'];

                            $offerNode->addParam($attribute['attribute_id'] . $attribute['attribute_sub_id'], $param_name, $attribute['text'], $param_unit);

                            if ($attribute['name'] == 'Срок годности') {
                                $date_limit = true;
                                $date_time = $attribute['text'];
                            }
                        }
                    }
                    if ($this->setting('market_type') == 'YandexLite') {
                        if ($date_limit) {
                            $validity_str = '';
                            $validity_days = $date_time;
                            $validity_str .= 'P';
                            switch ($validity_days) {
                                case (mb_stripos($validity_days, 'дн') !== false):
                                    $result = intval(preg_replace('/[^\d]+/', '', $validity_days));
                                    $years = intval($result / 365);
                                    if ($years > 0) $validity_str .= $years . 'Y';
                                    $days = $result % 365;
                                    $months = intval($days / 30);
                                    if ($months > 0) $validity_str .= $months . 'M';
                                    $days = $days % 30;
                                    if ($days > 0) $validity_str .= $days . 'D';
                                    break;

                                case (mb_stripos($validity_days, 'мес') !== false):
                                    $result = intval(preg_replace('/[^\d]+/', '', $validity_days));
                                    $years = intval($result / 12);
                                    if ($years > 0) $validity_str .= $years . 'Y';
                                    $months = $result % 12;
                                    if ($months > 0) $validity_str .= $months . 'M';
                                    break;

                                case (mb_stripos($validity_days, 'год') !== false):
                                    $years = intval(preg_replace('/[^\d]+/', '', $validity_days));
                                    if ($years > 0) $validity_str .= $years . 'Y';
                                    break;

                                case (mb_stripos($validity_days, 'лет') !== false):
                                    $years = intval(preg_replace('/[^\d]+/', '', $validity_days));
                                    if ($years > 0) $validity_str .= $years . 'Y';
                                    break;

                                default:
                                    $validity_str .= '1Y6M';
                            }
                            $offerNode->setValue(new Xml\Node('period-of-validity-days', $validity_str));
                        } else {
                            $offerNode->setValue(new Xml\Node('period-of-validity-days', 'P1Y6M'));
                        }
                    }
                }

                $suboffers = array($offerNode);

                $is_option_type = $this->setting('market_product_option_type') == 'option';

                // Combination of options or attributes like options
                if ($this->setting('market_product_option_type') == 'option' && $this->setting('market_product_options_combination')) {
                    // Offer options combination
                    $suboffers = $this->makeOfferCombination($offerNode, $product);
                } else if ($this->setting('market_product_option_type') == 'attribute' && $attributes_grouped['attribute_option'] && $this->setting('market_product_attributes')) {
                    // Offer attribute-options combination
                    $suboffers = $this->makeOfferAttributeCombination($offerNode, $attributes_grouped['attribute_option']);
                }

                $price_markup_data = $this->makeUpOfferMarkupData($product, $offerNode);

                foreach ($suboffers as $offer) {
                    if ($this->setting('market_product_available') && $offer->getQuantity() <= 0)
                        continue;

                    if ($this->setting('market_product_set_available') == 1) { // old set_available
                        $offer->available = 'true';
                    } else if ($this->setting('market_product_set_available') == 2) {
                        if ($offer->getQuantity() == 0) {
                            $offer->available = 'false';
                        } else {
                            $offer->available = 'true';
                        }
                    } elseif ($this->setting('market_product_set_available') == 3) {
                        if ($offer->getQuantity() == 0) continue;
                    } elseif ($this->setting('market_product_set_available') == 4) {
                        $offer->available = 'false';
                    }

                    if ($this->setting('market_type') == 'Promua') {
                        if ($this->setting('market_product_set_available') == 1) { // old set_available
                            $offer->available = 'true';
                            $offer->presence_sure = 'true';
                        } else if ($this->setting('market_product_set_available') == 2) {
                            if ($offer->getQuantity() == 0) {
                                $offer->available = ' ';
                                $offer->presence_sure = ' ';
                            } else {
                                $offer->available = 'true';
                                $offer->presence_sure = 'true';
                            }
                        } elseif ($this->setting('market_product_set_available') == 3) {
                            if ($offer->getQuantity() == 0) $offer->available = ' ';
                            if ($offer->getQuantity() == 0) $offer->presence_sure = ' ';
                        } elseif ($this->setting('market_product_set_available') == 4) {
                            $offer->available = ' ';
                            $offer->presence_sure = ' ';
                        }
                    }
                    if ($this->setting('market_sales_note') == 1) {
                        if ($this->setting('market_sales_note_q') == 1) {
                            if ($offer->getQuantity() > 0) {
                                $offerNode->sales_notes = ($this->setting('market_sales_note_text'));
                            }
                            if ($offer->getQuantity() == 0) {
                                $offerNode->sales_notes = ($this->setting('market_sales_note_text_q'));
                            }
                        }
                        if ($this->setting('market_sales_note_q') == 0) {
                            if ($offer->getQuantity() > 0) {
                                $offerNode->sales_notes = ($this->setting('market_sales_note_text'));
                            }
                            if ($offer->getQuantity() == 0) {
                                $offerNode->sales_notes = ($this->setting('market_sales_note_text_q'));
                            }
                        }
                    }

                    if ($this->setting('market_product_images_from_option') && $offer->getTotalImages('option')) {
                        $offer->deleteImages('image');
                    }

                    if ($this->setting('market_weight') == 1) {
                        $offer->addParam('weight', 'Вес', number_format($offer->getWeight(), $offer::weight_precision, '.', ''), $product['weight_unit']); //to do need to locale?
                    }

                    // Modules markup
                    $offer->price = $this->calcOfferPriceMarkup($offer->price, $price_markup_data);

                    if (!is_null($offer->oldprice))
                        $offer->oldprice = $this->calcOfferPriceMarkup($offer->oldprice, $price_markup_data);

                    // Price with minimum quantity
                    if ($this->setting('market_product_price_with_minumun')) {
                        $offer->price *= $product['minimum'];
                        if (!is_null($offer->oldprice))
                            $offer->oldprice *= $product['minimum'];
                    }

                    // Purchase Price Markup (цена закупки) only for pricelist; Replaced original price
                    if ($this->setting('market_purchase_price_markup_options'))
                        $this->addPurchasePriceMarkup($offer, $product);

                    $offer->price = number_format($this->currency->convert($this->tax->calculate($offer->price, $product['tax_class_id'], $this->config->get('config_tax')), $this->shop_currency, $offer->currencyId), $this->product_price_decimal_place, '.', '');

                    if (!is_null($offer->oldprice)) {
                        $offer->oldprice = number_format($this->currency->convert($this->tax->calculate($offer->oldprice, $product['tax_class_id'], $this->config->get('config_tax')), $this->shop_currency, $offer->currencyId), $this->product_price_decimal_place, '.', '');
                    }

                    if ($offer->price > 0) {
                        if ($this->setting('market_yandex_utm_status'))
                            $offer->url = $this->addUrlUtm($offer, $category_names[$offer->categoryId]);

                        $this->addOfferDeliveryOptions($offer, $product);

                        $this->addOfferCustomTags($offer, $product);

                        //if (!$this->setting('market_simple_pricelist') && $offer->getWeight() > 0)
                        if ($this->setting('market_weight') == 1) {
                            $weight = $offer->getWeight();
                            if ($this->setting('market_weight_class_id'))
                                $weight = $this->weight->convert($weight, $product['weight_class_id'], $this->setting('market_weight_class_id'));

                            $offer->setValue(new Xml\Node($offer::tagWeight, number_format($weight, $offer::weight_precision, '.', '')));
                        }


                        // For addon multistore
                        if ($isCatalogOzon) {
                            $current_offer_multistores = [];

                            if ($offer->group_id && $is_option_type && !empty($product_options_multistores[$product['product_id']])) {
                                // todo: временно-постоянное решение, берем только одну опцию для складов, больше не планируестя и не ясно как сопоставлять кол-во из нескольких опций
                                $current_offer_multistores = current($product_options_multistores[$product['product_id']]);
                            } else if (!empty($product_multistores[$product['product_id']])) {
                                $current_offer_multistores = $product_multistores[$product['product_id']];
                            }

                            if ($use_multistore_addon) {
                                $this->addOfferOzonOutlets($offer, $current_offer_multistores, $multistores);
                            } else {
                                $this->addOfferOzonOutlets($offer, [], [], false);
                            }
                        }

                        $YML->addOffer($offer);

                        // list of exported products to yandex.market
                        $market_products[$offer->id] = array(
                            'offer_id' => $offer->id,
                            'offer_id_alias' => $offer->getIdAlias(),
                            'product_id' => $product['product_id'],
                            //'price' => $product[$field_price],
                            'price' => $offer->price,
                            'special_price' => (float)$offer->oldprice,
                            'tax_class_id' => $product['tax_class_id'],
                        );
                    }
                }
            }
        }

        if ($this->setting('addon_podarki_status')) {
            $this->addGifts($YML);
        }

        // revome check for yandex if will more api work catalog
        if (!empty($market_products) && $this->setting('market_type') == 'Yandex') {
            $this->loadModel('module');

            $this->model_module->addMarketOffers($market_products, $this->profile_id);
        }

        if ($this->debug)
            $this->end = memory_get_usage();

        return $YML->getXml();
    }

    protected function addUrlUtm($offer, $category_name = '')
    {
        $url = parse_url($offer->url);

        $url_params['utm_source'] = $this->setting('market_yandex_utm_source');
        $url_params['utm_medium'] = $this->setting('market_yandex_utm_medium');
        $url_params['utm_content'] = YandexUrlHelper::encodeUtmContent(StringHelper::transliterate($category_name));
        $url_params['utm_campaign'] = $offer->id;
        $url_params['utm_term'] = $offer->id;
        // $url_params['utm_term'] = $offer->getIdAlias();

        $url['query'] = (isset($url['query']) ? $url['query'] . '&' : '') . http_build_query($url_params);

        return $url['scheme'] . '://' . $url['host'] . $url['path'] . '?' . $url['query'] . (isset($url['fragment']) ? '#' . $url['fragment'] : '');
    }

    private function makeUpOfferMarkupData($product, $offer)
    {
        $output = array(
            'category_id' => $offer->categoryId,
            'manufacturer_id' => $product['manufacturer_id'],
        );

        return $output;
    }

    private function isParentCategory($category_id, $parent_id, $categories)
    {
        if ($categories[$category_id]['parent_id'] == $parent_id)
            return true;

        $current_category = $categories[$category_id];

        while ($current_category['parent_id'] && $categories[$current_category['parent_id']]['category_id'] != $parent_id) {
            $current_category = $categories[$current_category['parent_id']];
        }

        return $current_category['category_id'] == $parent_id;
    }

    private function addOfferDeliveryOptions($offerNode, $product)
    {
        // Delivery options
        $market_delivery_options = $this->setting('market_delivery_options', array());
        $delivery_types = array();

        foreach ($market_delivery_options as $market_option) {
            if (!$this->setting("market_product_{$market_option['type']}_options"))
                continue;

            // filter by stock status
            if (!empty($market_option['stock_status']) && $market_option['stock_status'] != $product['stock_status_id'])
                continue;

            // filter by weight
            $weight_from_len = strlen(trim($market_option['weight_from']));
            $weight_to_len = strlen(trim($market_option['weight_to']));

            if ($weight_from_len || $weight_to_len) {
                $weight_from = $weight_from_len ? $market_option['weight_from'] : -INF;
                $weight_to = $weight_to_len ? $market_option['weight_to'] : INF;

                if (!MathHelper::inRange(
                    $this->weight->convert($offerNode->getWeight(), $product['weight_class_id'], $market_option['weight_class']),
                    $weight_from,
                    $weight_to
                )) {
                    continue;
                }
            }

            // filter by category
            if (!empty($market_option['category'])) {
                if (empty($market_option['category_inherit']) && $offerNode->categoryId != $market_option['category']) {
                    continue;
                } else if (
                    $offerNode->categoryId != $market_option['category']
                    && !$this->isParentCategory($offerNode->categoryId, $market_option['category'], $this->categories)
                ) {
                    continue;
                }
            }

            // filter by quantity
            if (!empty($market_option['stock_in']) || !empty($market_option['stock_out'])) {
                if (!empty($market_option['stock_in']) && empty($market_option['stock_out']) && $offerNode->getQuantity() <= 0) {
                    continue;
                }

                if (!empty($market_option['stock_out']) && empty($market_option['stock_in']) && $offerNode->getQuantity() > 0) {
                    continue;
                }
            }
            if ($this->setting('market_type') == 'Goods') {
                // explode is a excess functional
                $delivery_option_cost = explode(',', $market_option['cost']);
                $delivery_option_days = explode(',', $market_option['days']);
                $delivery_option_times = array_pad(explode(',', $market_option['times']), count($delivery_option_days), ''); // times is optional value

                if (count($delivery_option_times) != count($delivery_option_days))
                    throw new \Exception("Time and days for \"{$market_option['type']}-options\" are not match");

                foreach ($delivery_option_times as $i => $value) {
                    $uniq_key = $value . $delivery_option_days[$i] . $delivery_option_cost[$i]; // remove dublicates


                    $delivery_types[$market_option['type'] . '-options'][$uniq_key] = array(
                        'times' => $value,
                        'days' => $delivery_option_days[$i],
                        //'cost' => $delivery_option_cost[$i],
                    );
                }
            } else {
                // explode is a excess functional
                $delivery_option_cost = explode(',', $market_option['cost']);
                $delivery_option_days = explode(',', $market_option['days']);
                $delivery_option_times = array_pad(explode(',', $market_option['times']), count($delivery_option_cost), ''); // times is optional value

                if (count($delivery_option_cost) != count($delivery_option_days))
                    throw new \Exception("Day and cost for \"{$market_option['type']}-options\" are not match");

                foreach ($delivery_option_cost as $i => $value) {
                    $uniq_key = $value . $delivery_option_days[$i] . $delivery_option_times[$i]; // remove dublicates


                    $delivery_types[$market_option['type'] . '-options'][$uniq_key] = array(
                        'cost' => $value,
                        'days' => $delivery_option_days[$i],
                        'times' => $delivery_option_times[$i],
                    );
                }
            }
        }
        if ($this->setting('market_type') == 'Goods') {
            foreach ($delivery_types as $type_name => $data) {
                $deliveryOptionsNode = new Xml\Node($type_name);

                foreach ($data as $delivery_option) {
                    $deliveryOptionNode = new Xml\Node('option', null, false);

                    $deliveryOptionNode->{'order-before'} = $delivery_option['times'];
                    $deliveryOptionNode->days = $delivery_option['days'];

                    /*if (strlen($delivery_option['cost'])) {
						$deliveryOptionNode->cost = $delivery_option['cost'];
					}*/

                    $deliveryOptionsNode->setValue($deliveryOptionNode);
                }

                $offerNode->setValue($deliveryOptionsNode);
            }
        } else {
            foreach ($delivery_types as $type_name => $data) {
                $deliveryOptionsNode = new Xml\Node($type_name);

                foreach ($data as $delivery_option) {
                    $deliveryOptionNode = new Xml\Node('option', null, false);

                    $deliveryOptionNode->cost = $delivery_option['cost'];
                    $deliveryOptionNode->days = $delivery_option['days'];

                    if (strlen($delivery_option['times'])) {
                        $deliveryOptionNode->{'order-before'} = $delivery_option['times'];
                    }

                    $deliveryOptionsNode->setValue($deliveryOptionNode);
                }

                $offerNode->setValue($deliveryOptionsNode);
            }
        }
    }

    private function addPurchasePriceMarkup($offerNode, $product)
    {
        $price_field = $this->config->get(self::s('product_purchase_markup_field'));

        if (!strlen($product[$price_field]))
            return;

        $markup_options = $this->setting('market_purchase_price_markup_options', array());

        foreach ($markup_options as $markup_option) {
            $price_from = empty($markup_option['price_from']) ? -INF : $markup_option['price_from'];
            $price_to = empty($markup_option['price_to']) ? INF : $markup_option['price_to'];

            //$price = empty($offerNode->oldprice) ? $offerNode->price : $offerNode->oldprice;
            $price = (float)$product[$price_field];

            if (!MathHelper::inRange($price, $price_from, $price_to)) {
                continue;
            }

            // filter by category
            if (!empty($markup_option['category'])) {
                if (empty($markup_option['category_inherit']) && $offerNode->categoryId != $markup_option['category']) {
                    continue;
                } else if (
                    $offerNode->categoryId != $markup_option['category']
                    && !$this->isParentCategory($offerNode->categoryId, $markup_option['category'], $this->categories)
                ) {
                    continue;
                }
            }

            if ($markup_option['markup_type'] == 'percent') {
                $offerNode->price = MathHelper::operation($markup_option['markup_sign'], $price, MathHelper::percent($price, (float)$markup_option['markup_value']));
            } else if ($markup_option['markup_type'] == 'sum') {
                $offerNode->price = MathHelper::operation($markup_option['markup_sign'], $price, (float)$markup_option['markup_value']);
            }
        }
    }

    /**
     * Customm market tags from self product fields
     */
    private function addOfferCustomTags($offer, $product)
    {
        foreach ($this->setting('custom_market_tags', array()) as $ctag) {
            if (!strlen($ctag['tag']))
                throw new \Exception('Error tag name in one of the custom tags');

            // filter by category
            if (!empty($ctag['filter_category_id'])) {
                if (empty($ctag['category_inherit']) && $offer->categoryId != $ctag['filter_category_id']) {
                    continue;
                } else if (
                    $offer->categoryId != $ctag['filter_category_id']
                    && !$this->isParentCategory($offer->categoryId, $ctag['filter_category_id'], $this->categories)
                ) {
                    continue;
                }
            }

            // filter by stock
            if (!empty($ctag['stock_in']) || !empty($ctag['stock_out'])) {
                if (!empty($ctag['stock_in']) && empty($ctag['stock_out']) && $offer->getQuantity() <= 0) {
                    continue;
                }

                if (!empty($ctag['stock_out']) && empty($ctag['stock_in']) && $offer->getQuantity() > 0) {
                    continue;
                }
            }

            // filter by stock status
            if (!empty($ctag['stock_status']) && $ctag['stock_status'] != $product['stock_status_id'])
                continue;

            $product_values = array();

            if (utf8_strlen($ctag['value'])) {
                $product_values = array($ctag['value']);
            } else if ($ctag['field']) {
                if (strlen($ctag['delimiter'])) {
                    $product_values = explode($ctag['delimiter'], $product[$ctag['field']]);
                } else {
                    $product_values = array($product[$ctag['field']]);
                }

                $product_values = array_filter($product_values, 'trim');
            }

            $ctag_unit = strlen($ctag['unit']) ? $ctag['unit'] : null;

            foreach ($product_values as $product_value) {
                $ctagNode = new Xml\Node($ctag['tag'], $product_value);

                if ($ctag_unit !== null)
                    $ctagNode->unit = $ctag_unit;

                $offer->setValue($ctagNode);
            }
        }
    }

    private function addGifts($YML)
    {
        $offers = array();

        foreach ($YML->getOffers() as $offer) {
            $offers[$offer->getIdAlias()] = $offer->product_id;
        }

        $this->loadModel('addon/podarki');

        $promos = $this->model_addon_podarki->getPromos($offers);
        $server = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;

        foreach ($promos as $promo) {
            if (!$promo['products'] || !in_array($promo['product_id'], $offers))
                continue;

            $promoNode = new xml\PromoOffer($promo['product_id']);
            $promo_offers = array_intersect($offers, [$promo['product_id']]);

            foreach ($promo_offers as $id => $val) {
                $promoNode->addOffer($id);
            }

            if ($promo['description'])
                $promoNode->description = html_entity_decode($promo['description'], ENT_QUOTES);

            if ($promo['minimum_quntity'])
                $promoNode->required_quantity = $promo['minimum_quntity'];

            $product_ids = array_map(function ($el) {
                return $el['product_id'];
            }, $promo['products']);

            if ($exists_offers = array_intersect($offers, $product_ids)) {
                foreach ($exists_offers as $offer_id => $product_id) {
                    $promoNode->addGift($offer_id);
                }
            } else {
                foreach ($promo['products'] as $product) {
                    $img = empty($product['image']) ? null : $server . 'image/' . rawurlencode($product['image']);
                    $YML->addGift($product['product_id'], $product['name'], $img);
                    $promoNode->addGift($product['product_id']);
                }
            }

            $YML->addPromo($promoNode);
        }
    }

    private function addOfferOzonOutlets($offerNode, $product_stores, $stores, $use_addon = true)
    {
        if ($use_addon) {
            foreach ($product_stores as $store) {
                $offerNode->addOutlet([
                    'instock' => $store['quantity'],
                    'warehouse_name' => $stores[$store['multistore_id']]['name'],
                ]);
            }
        } else {
            $offerNode->addOutlet([
                'instock' => $offerNode->getQuantity(),
                'warehouse_name' => 'Sklad 1',
            ]);
        }
    }


    public function apiTokenCallback()
    {
        if (!isset($this->request->get['type']) || !isset($this->request->get['code']))
            return new \Action('error/not_found');

        $setting_profile_id = (int)$this->request->get['state'];

        $client = new \yamarket_fusion\API\Yandex\OAuth\OAuthClient($this->setting('market_api_client', '', $setting_profile_id), $this->setting('market_api_secret', '', $setting_profile_id));

        $code = $this->request->get['code'];

        if ($this->request->get['type'] == 'market') {
            try {
                $token = $client->requestAccessToken($code);

                $this->loadModel('setting');

                $this->model_setting->editSettingValue('market_api_token', $token->access_token, $setting_profile_id);
                $this->model_setting->editSettingValue('market_api_token_expires_date', $token->token_expires_in, $setting_profile_id);
                $this->model_setting->editSettingValue('market_api_token_refresh', $token->refresh_token, $setting_profile_id);

                exit('Token успешно получен'); // todo locale
            } catch (\Exception $e) {
                exit($e->getMessage());
            }
        }
    }

    // Сron update yandex.market products
    #work 2
    public function updateChangedProducts()
    {
        $this->loadModel('module');
        $this->loadModel('catalog/catalog');

        $offers_to_update = array();
        $profiles_to_update = array();

        $profiles = $this->setting('setting_profiles', array(), 0);

        if (!empty($this->request->get['profile_id']) && array_key_exists($this->request->get['profile_id'], $profiles)) {
            $profiles = array(($this->request->get['profile_id']) => '');
        }

        foreach ($profiles as $profile_id => $name) {
            if (
                $this->setting('market_type', '', $profile_id) == 'Yandex'
                && !empty($this->setting('market_api_client', '', $profile_id))
                //&& $this->setting('products_price_autoupdate', '', $profile_id)
                && $this->setting('products_price_autoupdate_cron', '', $profile_id)
            ) {
                $profiles_to_update[] = $profile_id;
            }
        }

        if (!$profiles_to_update)
            return;


        foreach ($profiles_to_update as $profile_id) {
            // todo check this namespace
            $market_service = '\yamarket_fusion\Module\controllers\service\\' . $this->setting('market_type', '', $profile_id) . '\Service';

            $offers_query_data = array('start' => 0, 'limit' => $market_service::REQUEST_UPDATE_OFFERS_LIMIT);

            while ($market_products = $this->model_module->getMarketOffers($profile_id, $offers_query_data)) {
                $offers_query_data['start'] += $market_service::REQUEST_UPDATE_OFFERS_LIMIT;

                if ($market_products) {
                    $params = array(
                        'customer_group_id' => $this->setting('market_customer_group_id', $this->config->get('config_customer_group_id'), $profile_id),
                        'filter_allowed_categories' => $this->setting('market_all_categories') ? array() : $this->setting('market_categories', array(), $profile_id),
                    );

                    // xak; todo rewrite all feed methods to profile_id arguments
                    $this->profile_id = $profile_id;

                    $products = $this->model_catalog_catalog->getProductsRelatedMarket(array_unique(ArrayHelper::getColumn($market_products, 'product_id')), $params);
                    $offers = $this->prepareOffers($products, $profile_id);

                    $compare_offers = array_filter($offers, function ($el) use ($market_products) {
                        return isset($market_products[$el['offer_id']]);
                    });

                    foreach ($compare_offers as $offer) {
                        $market_product = $market_products[$offer['offer_id']];

                        if (!(float)$offer['price'])
                            continue;

                        if (
                            (float)$offer['price'] != (float)$market_product['price']
                            || (isset($offer['special_price']) && (float)$offer['special_price'] != (float)$market_product['special'])
                        ) {
                            //$products_to_update[$profile_id][$offer['product_id']] = $product;
                            $offers_to_update[$profile_id][] = $offer;
                        }
                    }
                }
            }
        }

        if ($offers_to_update) {
            foreach ($offers_to_update as $profile_id => $value) {
                if ($this->setting('products_price_autoupdate_cron_type_direct', null, $profile_id) == 0) {
                    $result = $this->requestRefreshPricelist($profile_id);

                    if (!$result->getErrors()) {
                        echo 'Sent request to update pricelist, profile ID ' . $profile_id . PHP_EOL;
                    } else {
                        echo 'Error Sending request to update pricelist, profile ID ' . $profile_id . PHP_EOL;
                        echo $result->getErrorsString() . PHP_EOL;
                    }
                } else {
                    $market_service = '\yamarket_fusion\Module\controllers\service\\' . $this->setting('market_type', '', $profile_id) . '\Service';

                    $all_offers = array_chunk($value, $market_service::REQUEST_UPDATE_OFFERS_LIMIT);
                    $total_updated = 0;

                    foreach ($all_offers as $offers) {
                        $result = $this->requestUpdateOffersPrice($offers, $profile_id);

                        if (!$result->getErrors()) {
                            $updated = array_uintersect($offers, $result->getData(), function ($a, $b) {
                                return $a['product_id'] == $b['product_id'] ? 0 : -1;
                            });
                            //$updated = array_intersect_key($offers, ArrayHelper::index($result->getData(), 'offer_id'));

                            $this->model_module->updateMarketOffers($updated, $profile_id);

                            $total_updated += count($result->getData());
                        }
                    }

                    if ($total_updated) {
                        echo $total_updated . ' Products updated on market, profile id ' . $profile_id . PHP_EOL;
                    } else {
                        echo 'Products has been not updated on market, profile id ' . $profile_id . PHP_EOL;
                    }
                }
            }
        } else {
            echo 'No Products to update';
        }
    }

    #work 2
    /**
     * @description Get updated offers from market and save
     */
    public function updateUpdatedPriceOffers()
    {
        $profiles = $this->setting('setting_profiles', array(), 0);
        $profiles_to_update = array();

        foreach ($profiles as $profile_id => $name) {
            if (
                $this->setting('market_type', '', $profile_id) == 'Yandex'
                && !empty($this->setting('market_api_client', '', $profile_id))
                && !empty($this->setting('market_api_token', '', $profile_id))
            ) {
                $profiles_to_update[] = $profile_id;
            }
        }

        foreach ($profiles_to_update as $profile_id) {
            $result = $this->requestGetUpdatedOffers($profile_id);

            if ($result->getErrors()) {
                echo $result->getErrorsString();
            } else {
                echo "Updated " . count($result->getData()) . " offers from Market, profile ID {$profile_id} " . PHP_EOL;
            }
        }
    }

    # work 2
    public function deleteOutOfStockOffers()
    {
        $this->loadModel('module');

        $profiles = $this->setting('setting_profiles', array(), 0);
        $profiles_to_update = array();

        foreach ($profiles as $profile_id => $name) {
            if (
                $this->setting('market_type', '', $profile_id) == 'Yandex'
                && !empty($this->setting('market_api_client', '', $profile_id))
                //&& !empty($this->setting('market_api_token', '', $profile_id))
            ) {
                $profiles_to_update[] = $profile_id;
            }
        }

        foreach ($profiles_to_update as $profile_id) {
            $products_query_data = array(
                'status' => 0,
                'quantity' => array(
                    'value' => 0,
                    'sign' => '<=',
                )
            );

            $total_products = $this->model_module->getTotalMarketProductIds($profile_id, $products_query_data);

            if ($total_products) {
                if ($this->setting('products_price_autoupdate_cron_type_direct', null, $profile_id) == 0) {
                    $result = $this->requestRefreshPricelist($profile_id);

                    if (!$result->getErrors()) {
                        echo 'Sent request to update pricelist, profile ID ' . $profile_id . PHP_EOL;
                    } else {
                        echo 'Error sending request to update pricelist, profile ID ' . $profile_id . PHP_EOL;
                    }
                } else {
                    $market_service = '\yamarket_fusion\Module\controllers\service\\' . $this->setting('market_type', '', $profile_id) . '\Service';
                    $total_updated = 0;

                    $products_query_data['start'] = 0;
                    $products_query_data['limit'] = $market_service::REQUEST_DELETE_OFFERS_LIMIT;

                    while ($products = $this->model_module->getMarketProductIds($profile_id, $products_query_data)) {
                        // todo event or other for deleted product from store

                        $products_query_data['start'] += $market_service::REQUEST_DELETE_OFFERS_LIMIT;

                        if ($products) {
                            // todo look todo #1
                            $updated_market_products = $this->model_module->getUpdatedOffersByProductId($products, $profile_id);
                            $result = $this->requestDeleteOffersPrice($updated_market_products, $profile_id);

                            if (!$result->getErrors()) {
                                $total_updated += count($result->getData());
                            }
                        }
                    }

                    echo "Deleted " . count($total_updated) . " offers prices from Market, profile ID {$profile_id} " . PHP_EOL;
                }
            }
        }
    }
}
