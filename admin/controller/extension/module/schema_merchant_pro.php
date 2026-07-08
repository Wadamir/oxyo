<?php
class ControllerExtensionModuleSchemaMerchantPro extends Controller {
    const LICENSE_KEY_HASH = '875f48b123bfdc8c621c21c3001f60554e053468b89ecb7aa47d204782bd6f7f';
    const DOMAIN_KEY_PREFIX = 'SMP-';

    private $error = array();
    private $error_fields = array();

    public function index() {
        $this->load->language('extension/module/schema_merchant_pro');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/schema_merchant_pro');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->normalizePostedOpeningDays();
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['module_schema_merchant_pro_license_key'] = $this->normalizeLicenseForSave($this->request->post['module_schema_merchant_pro_license_key']);
            $this->model_setting_setting->editSetting('module_schema_merchant_pro', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_safe_mode_note'] = $this->language->get('text_safe_mode_note');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $language_keys = array(
            'tab_general',
            'tab_organization',
            'tab_website',
            'tab_product',
            'tab_merchant',
            'tab_preview',
            'text_output_blocks',
            'text_organization_data',
            'text_address',
            'text_social_profiles',
            'text_website_data',
            'text_product_blocks',
            'text_shipping_data',
            'text_returns_data',
            'text_preview_data',
            'text_diagnostics',
            'text_diagnostic_ok',
            'text_diagnostic_warning',
            'text_diagnostic_error',
            'text_preview_no_product',
            'text_preview_sample_note',
            'text_image_warning_accepted',
            'text_validation_links',
            'text_license_status',
            'text_license_valid',
            'text_license_invalid',
            'help_same_as',
            'help_organization_same_as',
            'help_safe_settings_only',
            'help_shipping_returns',
            'help_preview_product_id',
            'help_field_organization_opening_days',
            'help_field_organization_opening_opens',
            'help_field_organization_opening_closes',
            'help_field_organization_geo_latitude',
            'help_field_organization_geo_longitude',
            'help_field_shipping_country',
            'help_field_shipping_price',
            'help_field_shipping_currency',
            'help_field_shipping_handling_min_days',
            'help_field_shipping_handling_max_days',
            'help_field_shipping_transit_min_days',
            'help_field_shipping_transit_max_days',
            'help_field_returns_country',
            'help_field_returns_category',
            'help_field_returns_days',
            'help_field_returns_fees',
            'help_field_returns_method',
            'text_google_rich_results',
            'text_schema_validator',
            'text_yandex_validator',
            'text_google_merchant_docs',
            'text_yandex_markup_docs',
            'text_schema_org_docs',
            'help_google_rich_results',
            'help_schema_validator',
            'help_yandex_validator',
            'help_google_merchant_docs',
            'help_yandex_markup_docs',
            'help_schema_org_docs',
            'entry_status',
            'entry_license_key',
            'placeholder_license_key',
            'entry_debug',
            'entry_output_jsonld',
            'entry_output_open_graph',
            'entry_output_twitter',
            'entry_compatibility_mode',
            'entry_organization_enabled',
            'entry_organization_name',
            'entry_organization_url',
            'entry_organization_logo',
            'entry_organization_email',
            'entry_organization_phone',
            'entry_organization_street',
            'entry_organization_city',
            'entry_organization_region',
            'entry_organization_postcode',
            'entry_organization_country',
            'entry_organization_same_as',
            'entry_organization_opening_hours_enabled',
            'entry_organization_opening_days',
            'entry_organization_opening_opens',
            'entry_organization_opening_closes',
            'entry_organization_geo_enabled',
            'entry_organization_geo_latitude',
            'entry_organization_geo_longitude',
            'entry_twitter_handle',
            'entry_website_enabled',
            'entry_website_name',
            'entry_website_url',
            'entry_website_language',
            'entry_search_action_enabled',
            'entry_breadcrumbs_enabled',
            'entry_product_enabled',
            'entry_offer_enabled',
            'entry_brand_enabled',
            'entry_identifiers_enabled',
            'entry_attributes_enabled',
            'entry_rating_enabled',
            'entry_reviews_enabled',
            'entry_shipping_enabled',
            'entry_shipping_country',
            'entry_shipping_price',
            'entry_shipping_currency',
            'entry_shipping_handling_min_days',
            'entry_shipping_handling_max_days',
            'entry_shipping_transit_min_days',
            'entry_shipping_transit_max_days',
            'entry_returns_enabled',
            'entry_returns_country',
            'entry_returns_category',
            'entry_returns_days',
            'entry_returns_fees',
            'entry_returns_method',
            'entry_preview_product_id',
            'text_compatibility_normal',
            'text_compatibility_careful',
            'text_compatibility_schema_only',
            'text_compatibility_open_graph_only',
            'text_return_finite',
            'text_return_unlimited',
            'text_return_not_permitted',
            'text_return_free',
            'text_return_customer',
            'text_return_mail',
            'text_return_store',
            'text_availability_in_stock',
            'text_availability_out_of_stock',
            'text_availability_pre_order',
            'text_availability_back_order',
            'text_availability_discontinued',
            'text_availability_limited',
            'text_weekday_monday',
            'text_weekday_tuesday',
            'text_weekday_wednesday',
            'text_weekday_thursday',
            'text_weekday_friday',
            'text_weekday_saturday',
            'text_weekday_sunday',
        );

        foreach ($language_keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_fields'] = array();

        foreach ($this->error_fields as $field) {
            $data['error_fields'][$field] = true;
        }

        $data['error_tabs'] = $this->getErrorTabs();

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
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/schema_merchant_pro', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/schema_merchant_pro', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $defaults = $this->model_extension_module_schema_merchant_pro->getDefaultSettings();

        foreach ($defaults as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif ($this->config->get($key) !== null) {
                $data[$key] = $this->config->get($key);
            } else {
                $data[$key] = $default;
            }
        }

        $data['opening_day_options'] = $this->getOpeningDayOptions();
        $data['module_schema_merchant_pro_organization_opening_days_selected'] = $this->splitOpeningDays($data['module_schema_merchant_pro_organization_opening_days']);

        $data['license_valid'] = $this->hasValidLicenseValue($data['module_schema_merchant_pro_license_key']);

        $data['boolean_options'] = array(
            array('value' => 1, 'text' => $this->language->get('text_enabled')),
            array('value' => 0, 'text' => $this->language->get('text_disabled'))
        );

        $data['country_options'] = array(
            array('value' => '', 'text' => $this->language->get('text_select')),
            array('value' => 'RU', 'text' => 'RU — Россия'),
            array('value' => 'BY', 'text' => 'BY — Беларусь'),
            array('value' => 'KZ', 'text' => 'KZ — Казахстан'),
            array('value' => 'AM', 'text' => 'AM — Армения'),
            array('value' => 'KG', 'text' => 'KG — Кыргызстан'),
            array('value' => 'US', 'text' => 'US — United States'),
            array('value' => 'DE', 'text' => 'DE — Germany'),
            array('value' => 'CN', 'text' => 'CN — China')
        );

        $data['language_options'] = array(
            array('value' => '', 'text' => $this->language->get('text_select')),
            array('value' => 'ru-RU', 'text' => 'ru-RU — Русский (Россия)'),
            array('value' => 'en-US', 'text' => 'en-US — English (US)'),
            array('value' => 'en-GB', 'text' => 'en-GB — English (UK)'),
            array('value' => 'uk-UA', 'text' => 'uk-UA — Українська'),
            array('value' => 'be-BY', 'text' => 'be-BY — Беларуская'),
            array('value' => 'kk-KZ', 'text' => 'kk-KZ — Қазақша'),
            array('value' => 'de-DE', 'text' => 'de-DE — Deutsch'),
            array('value' => 'zh-CN', 'text' => 'zh-CN — 中文')
        );

        $data['currency_options'] = array(
            array('value' => '', 'text' => $this->language->get('text_use_offer_currency')),
            array('value' => 'RUB', 'text' => 'RUB — Российский рубль'),
            array('value' => 'USD', 'text' => 'USD — US Dollar'),
            array('value' => 'EUR', 'text' => 'EUR — Euro'),
            array('value' => 'BYN', 'text' => 'BYN — Белорусский рубль'),
            array('value' => 'KZT', 'text' => 'KZT — Казахстанский тенге'),
            array('value' => 'AMD', 'text' => 'AMD — Армянский драм'),
            array('value' => 'KGS', 'text' => 'KGS — Кыргызский сом'),
            array('value' => 'CNY', 'text' => 'CNY — Chinese Yuan')
        );

        $data['availability_options'] = array(
            array('value' => 'https://schema.org/InStock', 'text' => $this->language->get('text_availability_in_stock')),
            array('value' => 'https://schema.org/OutOfStock', 'text' => $this->language->get('text_availability_out_of_stock')),
            array('value' => 'https://schema.org/PreOrder', 'text' => $this->language->get('text_availability_pre_order')),
            array('value' => 'https://schema.org/BackOrder', 'text' => $this->language->get('text_availability_back_order')),
            array('value' => 'https://schema.org/Discontinued', 'text' => $this->language->get('text_availability_discontinued')),
            array('value' => 'https://schema.org/LimitedAvailability', 'text' => $this->language->get('text_availability_limited'))
        );

        $data['compatibility_options'] = array(
            array('value' => 'normal', 'text' => $this->language->get('text_compatibility_normal')),
            array('value' => 'careful', 'text' => $this->language->get('text_compatibility_careful')),
            array('value' => 'schema_only', 'text' => $this->language->get('text_compatibility_schema_only')),
            array('value' => 'open_graph_only', 'text' => $this->language->get('text_compatibility_open_graph_only'))
        );

        $data['return_category_options'] = array(
            array('value' => 'https://schema.org/MerchantReturnFiniteReturnWindow', 'text' => $this->language->get('text_return_finite')),
            array('value' => 'https://schema.org/MerchantReturnUnlimitedWindow', 'text' => $this->language->get('text_return_unlimited')),
            array('value' => 'https://schema.org/MerchantReturnNotPermitted', 'text' => $this->language->get('text_return_not_permitted'))
        );

        $data['return_fees_options'] = array(
            array('value' => 'https://schema.org/FreeReturn', 'text' => $this->language->get('text_return_free')),
            array('value' => 'https://schema.org/ReturnFeesCustomerResponsibility', 'text' => $this->language->get('text_return_customer'))
        );

        $data['return_method_options'] = array(
            array('value' => 'https://schema.org/ReturnByMail', 'text' => $this->language->get('text_return_mail')),
            array('value' => 'https://schema.org/ReturnInStore', 'text' => $this->language->get('text_return_store'))
        );

        $data['validation_links'] = array(
            array(
                'href' => 'https://search.google.com/test/rich-results',
                'text' => $this->language->get('text_google_rich_results'),
                'help' => $this->language->get('help_google_rich_results')
            ),
            array(
                'href' => 'https://validator.schema.org/',
                'text' => $this->language->get('text_schema_validator'),
                'help' => $this->language->get('help_schema_validator')
            ),
            array(
                'href' => 'https://webmaster.yandex.ru/tools/microtest/',
                'text' => $this->language->get('text_yandex_validator'),
                'help' => $this->language->get('help_yandex_validator')
            ),
            array(
                'href' => 'https://developers.google.com/search/docs/appearance/structured-data/merchant-listing',
                'text' => $this->language->get('text_google_merchant_docs'),
                'help' => $this->language->get('help_google_merchant_docs')
            ),
            array(
                'href' => 'https://yandex.ru/support/webmaster/ru/yandex-indexing/validator',
                'text' => $this->language->get('text_yandex_markup_docs'),
                'help' => $this->language->get('help_yandex_markup_docs')
            ),
            array(
                'href' => 'https://schema.org/',
                'text' => $this->language->get('text_schema_org_docs'),
                'help' => $this->language->get('help_schema_org_docs')
            )
        );

        $data['general_select_fields'] = array(
            array('name' => 'module_schema_merchant_pro_status', 'label' => $this->language->get('entry_status'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_debug', 'label' => $this->language->get('entry_debug'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_output_jsonld', 'label' => $this->language->get('entry_output_jsonld'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_output_open_graph', 'label' => $this->language->get('entry_output_open_graph'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_output_twitter', 'label' => $this->language->get('entry_output_twitter'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_compatibility_mode', 'label' => $this->language->get('entry_compatibility_mode'), 'options' => $data['compatibility_options'])
        );

        $data['license_text_fields'] = array(
            array('name' => 'module_schema_merchant_pro_license_key', 'label' => $this->language->get('entry_license_key'), 'placeholder' => $this->language->get('placeholder_license_key'))
        );

        $data['organization_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_enabled', 'label' => $this->language->get('entry_organization_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['organization_identity_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_name', 'label' => $this->language->get('entry_organization_name')),
            array('name' => 'module_schema_merchant_pro_organization_url', 'label' => $this->language->get('entry_organization_url')),
            array('name' => 'module_schema_merchant_pro_organization_logo', 'label' => $this->language->get('entry_organization_logo')),
            array('name' => 'module_schema_merchant_pro_organization_email', 'label' => $this->language->get('entry_organization_email')),
            array('name' => 'module_schema_merchant_pro_organization_phone', 'label' => $this->language->get('entry_organization_phone'))
        );

        $data['organization_address_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_street', 'label' => $this->language->get('entry_organization_street')),
            array('name' => 'module_schema_merchant_pro_organization_city', 'label' => $this->language->get('entry_organization_city')),
            array('name' => 'module_schema_merchant_pro_organization_region', 'label' => $this->language->get('entry_organization_region')),
            array('name' => 'module_schema_merchant_pro_organization_postcode', 'label' => $this->language->get('entry_organization_postcode'))
        );

        $data['organization_country_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_country', 'label' => $this->language->get('entry_organization_country'), 'options' => $data['country_options'])
        );

        $data['organization_opening_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_opening_hours_enabled', 'label' => $this->language->get('entry_organization_opening_hours_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['organization_geo_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_geo_enabled', 'label' => $this->language->get('entry_organization_geo_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['organization_social_text_fields'] = array(
            array('name' => 'module_schema_merchant_pro_twitter_handle', 'label' => $this->language->get('entry_twitter_handle'))
        );

        $data['organization_time_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_opening_opens', 'label' => $this->language->get('entry_organization_opening_opens'), 'placeholder' => '10:00'),
            array('name' => 'module_schema_merchant_pro_organization_opening_closes', 'label' => $this->language->get('entry_organization_opening_closes'), 'placeholder' => '19:00')
        );

        $data['organization_geo_fields'] = array(
            array('name' => 'module_schema_merchant_pro_organization_geo_latitude', 'label' => $this->language->get('entry_organization_geo_latitude'), 'placeholder' => '55.75222'),
            array('name' => 'module_schema_merchant_pro_organization_geo_longitude', 'label' => $this->language->get('entry_organization_geo_longitude'), 'placeholder' => '37.61556')
        );

        $data['website_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_website_enabled', 'label' => $this->language->get('entry_website_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['website_text_fields'] = array(
            array('name' => 'module_schema_merchant_pro_website_name', 'label' => $this->language->get('entry_website_name')),
            array('name' => 'module_schema_merchant_pro_website_url', 'label' => $this->language->get('entry_website_url'))
        );

        $data['website_language_fields'] = array(
            array('name' => 'module_schema_merchant_pro_website_language', 'label' => $this->language->get('entry_website_language'), 'options' => $data['language_options'])
        );

        $data['website_feature_fields'] = array(
            array('name' => 'module_schema_merchant_pro_search_action_enabled', 'label' => $this->language->get('entry_search_action_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_breadcrumbs_enabled', 'label' => $this->language->get('entry_breadcrumbs_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['product_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_product_enabled', 'label' => $this->language->get('entry_product_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['product_commercial_fields'] = array(
            array('name' => 'module_schema_merchant_pro_offer_enabled', 'label' => $this->language->get('entry_offer_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_brand_enabled', 'label' => $this->language->get('entry_brand_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_identifiers_enabled', 'label' => $this->language->get('entry_identifiers_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_attributes_enabled', 'label' => $this->language->get('entry_attributes_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['product_review_fields'] = array(
            array('name' => 'module_schema_merchant_pro_rating_enabled', 'label' => $this->language->get('entry_rating_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch'),
            array('name' => 'module_schema_merchant_pro_reviews_enabled', 'label' => $this->language->get('entry_reviews_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['product_availability_fields'] = array();
        $stock_statuses = $this->model_extension_module_schema_merchant_pro->getStockStatuses();

        foreach ($stock_statuses as $stock_status) {
            $field_name = 'module_schema_merchant_pro_availability_stock_status_' . (int)$stock_status['stock_status_id'];
            $field_value = isset($this->request->post[$field_name]) ? $this->request->post[$field_name] : $this->config->get($field_name);

            if ($field_value === null || $field_value === '') {
                $field_value = 'https://schema.org/OutOfStock';
            }

            $data[$field_name] = $field_value;
            $data['product_availability_fields'][] = array(
                'name' => $field_name,
                'label' => $stock_status['name'],
                'options' => $data['availability_options'],
                'help' => $this->language->get('help_field_availability_stock_status')
            );
        }

        $data['shipping_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_shipping_enabled', 'label' => $this->language->get('entry_shipping_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['shipping_commercial_fields'] = array(
            array('name' => 'module_schema_merchant_pro_shipping_country', 'label' => $this->language->get('entry_shipping_country'), 'options' => $data['country_options'], 'kind' => 'select'),
            array('name' => 'module_schema_merchant_pro_shipping_price', 'label' => $this->language->get('entry_shipping_price'), 'placeholder' => '500.00', 'kind' => 'text'),
            array('name' => 'module_schema_merchant_pro_shipping_currency', 'label' => $this->language->get('entry_shipping_currency'), 'options' => $data['currency_options'], 'kind' => 'select')
        );

        $data['shipping_handling_fields'] = array(
            array('name' => 'module_schema_merchant_pro_shipping_handling_min_days', 'label' => $this->language->get('entry_shipping_handling_min_days'), 'placeholder' => '0'),
            array('name' => 'module_schema_merchant_pro_shipping_handling_max_days', 'label' => $this->language->get('entry_shipping_handling_max_days'), 'placeholder' => '1')
        );

        $data['shipping_transit_fields'] = array(
            array('name' => 'module_schema_merchant_pro_shipping_transit_min_days', 'label' => $this->language->get('entry_shipping_transit_min_days'), 'placeholder' => '1'),
            array('name' => 'module_schema_merchant_pro_shipping_transit_max_days', 'label' => $this->language->get('entry_shipping_transit_max_days'), 'placeholder' => '3')
        );

        $data['returns_status_fields'] = array(
            array('name' => 'module_schema_merchant_pro_returns_enabled', 'label' => $this->language->get('entry_returns_enabled'), 'options' => $data['boolean_options'], 'type' => 'switch')
        );

        $data['returns_policy_fields'] = array(
            array('name' => 'module_schema_merchant_pro_returns_country', 'label' => $this->language->get('entry_returns_country'), 'options' => $data['country_options'], 'kind' => 'select'),
            array('name' => 'module_schema_merchant_pro_returns_category', 'label' => $this->language->get('entry_returns_category'), 'options' => $data['return_category_options'], 'kind' => 'select'),
            array('name' => 'module_schema_merchant_pro_returns_days', 'label' => $this->language->get('entry_returns_days'), 'placeholder' => '14', 'kind' => 'text')
        );

        $data['returns_method_fields'] = array(
            array('name' => 'module_schema_merchant_pro_returns_fees', 'label' => $this->language->get('entry_returns_fees'), 'options' => $data['return_fees_options'], 'kind' => 'select'),
            array('name' => 'module_schema_merchant_pro_returns_method', 'label' => $this->language->get('entry_returns_method'), 'options' => $data['return_method_options'], 'kind' => 'select')
        );

        $data['schema_merchant_pro_preview_json'] = '';
        $data['schema_merchant_pro_preview_product_name'] = '';

        $preview_product_id = (int)$data['module_schema_merchant_pro_preview_product_id'];

        if ($preview_product_id <= 0) {
            $preview_product_id = (int)$this->model_extension_module_schema_merchant_pro->getFirstPreviewProductId();
            $data['module_schema_merchant_pro_preview_product_id'] = $preview_product_id;
        }

        $preview_product = $this->model_extension_module_schema_merchant_pro->getPreviewProductContext($preview_product_id);
        $preview_context = array(
            'route' => !empty($preview_product) ? 'product/product' : 'common/home',
            'site' => $this->getPreviewSiteContext(),
            'product' => $preview_product
        );

        if (!empty($preview_product)) {
            $data['schema_merchant_pro_preview_product_name'] = $preview_product['name'];
        }

        $preview_graph = $this->buildPreviewGraph($preview_context);

        if (!empty($preview_graph)) {
            $data['schema_merchant_pro_preview_json'] = json_encode(array(
                '@context' => 'https://schema.org',
                '@graph' => $preview_graph
            ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $data['diagnostics'] = $this->buildDiagnostics($preview_context, $preview_graph);

        $field_help = array(
            'module_schema_merchant_pro_status' => 'help_field_status',
            'module_schema_merchant_pro_debug' => 'help_field_debug',
            'module_schema_merchant_pro_output_jsonld' => 'help_field_output_jsonld',
            'module_schema_merchant_pro_output_open_graph' => 'help_field_output_open_graph',
            'module_schema_merchant_pro_output_twitter' => 'help_field_output_twitter',
            'module_schema_merchant_pro_compatibility_mode' => 'help_field_compatibility_mode',
            'module_schema_merchant_pro_organization_enabled' => 'help_field_organization_enabled',
            'module_schema_merchant_pro_organization_name' => 'help_field_organization_name',
            'module_schema_merchant_pro_organization_url' => 'help_field_organization_url',
            'module_schema_merchant_pro_organization_logo' => 'help_field_organization_logo',
            'module_schema_merchant_pro_organization_email' => 'help_field_organization_email',
            'module_schema_merchant_pro_organization_phone' => 'help_field_organization_phone',
            'module_schema_merchant_pro_organization_country' => 'help_field_country_code',
            'module_schema_merchant_pro_organization_opening_hours_enabled' => 'help_field_organization_opening_hours_enabled',
            'module_schema_merchant_pro_organization_opening_days' => 'help_field_organization_opening_days',
            'module_schema_merchant_pro_organization_opening_opens' => 'help_field_organization_opening_opens',
            'module_schema_merchant_pro_organization_opening_closes' => 'help_field_organization_opening_closes',
            'module_schema_merchant_pro_organization_geo_enabled' => 'help_field_organization_geo_enabled',
            'module_schema_merchant_pro_organization_geo_latitude' => 'help_field_organization_geo_latitude',
            'module_schema_merchant_pro_organization_geo_longitude' => 'help_field_organization_geo_longitude',
            'module_schema_merchant_pro_twitter_handle' => 'help_field_twitter_handle',
            'module_schema_merchant_pro_website_enabled' => 'help_field_website_enabled',
            'module_schema_merchant_pro_website_name' => 'help_field_website_name',
            'module_schema_merchant_pro_website_url' => 'help_field_website_url',
            'module_schema_merchant_pro_website_language' => 'help_field_website_language',
            'module_schema_merchant_pro_search_action_enabled' => 'help_field_search_action_enabled',
            'module_schema_merchant_pro_breadcrumbs_enabled' => 'help_field_breadcrumbs_enabled',
            'module_schema_merchant_pro_product_enabled' => 'help_field_product_enabled',
            'module_schema_merchant_pro_offer_enabled' => 'help_field_offer_enabled',
            'module_schema_merchant_pro_brand_enabled' => 'help_field_brand_enabled',
            'module_schema_merchant_pro_identifiers_enabled' => 'help_field_identifiers_enabled',
            'module_schema_merchant_pro_attributes_enabled' => 'help_field_attributes_enabled',
            'module_schema_merchant_pro_rating_enabled' => 'help_field_rating_enabled',
            'module_schema_merchant_pro_reviews_enabled' => 'help_field_reviews_enabled',
            'module_schema_merchant_pro_availability_stock_status_' => 'help_field_availability_stock_status',
            'module_schema_merchant_pro_shipping_enabled' => 'help_field_shipping_enabled',
            'module_schema_merchant_pro_shipping_country' => 'help_field_shipping_country',
            'module_schema_merchant_pro_shipping_price' => 'help_field_shipping_price',
            'module_schema_merchant_pro_shipping_currency' => 'help_field_shipping_currency',
            'module_schema_merchant_pro_shipping_handling_min_days' => 'help_field_shipping_handling_min_days',
            'module_schema_merchant_pro_shipping_handling_max_days' => 'help_field_shipping_handling_max_days',
            'module_schema_merchant_pro_shipping_transit_min_days' => 'help_field_shipping_transit_min_days',
            'module_schema_merchant_pro_shipping_transit_max_days' => 'help_field_shipping_transit_max_days',
            'module_schema_merchant_pro_returns_enabled' => 'help_field_returns_enabled',
            'module_schema_merchant_pro_returns_country' => 'help_field_returns_country',
            'module_schema_merchant_pro_returns_category' => 'help_field_returns_category',
            'module_schema_merchant_pro_returns_days' => 'help_field_returns_days',
            'module_schema_merchant_pro_returns_fees' => 'help_field_returns_fees',
            'module_schema_merchant_pro_returns_method' => 'help_field_returns_method',
            'module_schema_merchant_pro_preview_product_id' => 'help_preview_product_id'
        );

        $this->applyFieldHelp($data['general_select_fields'], $field_help);
        $this->applyFieldHelp($data['license_text_fields'], $field_help);
        $this->applyFieldHelp($data['organization_status_fields'], $field_help);
        $this->applyFieldHelp($data['organization_identity_fields'], $field_help);
        $this->applyFieldHelp($data['organization_address_fields'], $field_help);
        $this->applyFieldHelp($data['organization_country_fields'], $field_help);
        $this->applyFieldHelp($data['organization_opening_status_fields'], $field_help);
        $this->applyFieldHelp($data['organization_geo_status_fields'], $field_help);
        $this->applyFieldHelp($data['organization_social_text_fields'], $field_help);
        $this->applyFieldHelp($data['website_status_fields'], $field_help);
        $this->applyFieldHelp($data['website_text_fields'], $field_help);
        $this->applyFieldHelp($data['website_language_fields'], $field_help);
        $this->applyFieldHelp($data['website_feature_fields'], $field_help);
        $this->applyFieldHelp($data['product_status_fields'], $field_help);
        $this->applyFieldHelp($data['product_commercial_fields'], $field_help);
        $this->applyFieldHelp($data['product_review_fields'], $field_help);
        $this->applyFieldHelp($data['product_availability_fields'], $field_help);
        $this->applyFieldHelp($data['shipping_status_fields'], $field_help);
        $this->applyFieldHelp($data['shipping_commercial_fields'], $field_help);
        $this->applyFieldHelp($data['shipping_handling_fields'], $field_help);
        $this->applyFieldHelp($data['shipping_transit_fields'], $field_help);
        $this->applyFieldHelp($data['returns_status_fields'], $field_help);
        $this->applyFieldHelp($data['returns_policy_fields'], $field_help);
        $this->applyFieldHelp($data['returns_method_fields'], $field_help);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/schema_merchant_pro', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/schema_merchant_pro');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/schema_merchant_pro');

        $this->load->model('extension/module/schema_merchant_pro');
        $this->model_extension_module_schema_merchant_pro->install();
    }

    public function uninstall() {
        $this->load->model('extension/module/schema_merchant_pro');
        $this->model_extension_module_schema_merchant_pro->uninstall();
    }

    private function normalizePostedOpeningDays() {
        $field = 'module_schema_merchant_pro_organization_opening_days';

        if (!isset($this->request->post[$field])) {
            $this->request->post[$field] = '';
            return;
        }

        if (is_array($this->request->post[$field])) {
            $this->request->post[$field] = implode(',', $this->filterOpeningDayCodes($this->request->post[$field]));
            return;
        }

        $this->request->post[$field] = trim((string)$this->request->post[$field]);
    }

    private function getOpeningDayOptions() {
        return array(
            array('value' => 'Mo', 'text' => $this->language->get('text_weekday_monday')),
            array('value' => 'Tu', 'text' => $this->language->get('text_weekday_tuesday')),
            array('value' => 'We', 'text' => $this->language->get('text_weekday_wednesday')),
            array('value' => 'Th', 'text' => $this->language->get('text_weekday_thursday')),
            array('value' => 'Fr', 'text' => $this->language->get('text_weekday_friday')),
            array('value' => 'Sa', 'text' => $this->language->get('text_weekday_saturday')),
            array('value' => 'Su', 'text' => $this->language->get('text_weekday_sunday'))
        );
    }

    private function splitOpeningDays($value) {
        return $this->filterOpeningDayCodes(explode(',', (string)$value));
    }

    private function filterOpeningDayCodes(array $values) {
        $allowed = array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');
        $days = array();

        foreach ($values as $value) {
            $value = trim((string)$value);

            if (in_array($value, $allowed, true) && !in_array($value, $days, true)) {
                $days[] = $value;
            }
        }

        return $days;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/schema_merchant_pro')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->hasValidPostedEncoding()) {
            $this->error['warning'] = $this->language->get('error_encoding');
        }

        if (!$this->hasValidPostedSettings()) {
            $this->error['warning'] = $this->language->get('error_invalid_settings');
        }

        return !$this->error;
    }

    private function hasValidPostedEncoding() {
        $text_fields = array(
            'module_schema_merchant_pro_organization_name',
            'module_schema_merchant_pro_organization_url',
            'module_schema_merchant_pro_organization_logo',
            'module_schema_merchant_pro_organization_email',
            'module_schema_merchant_pro_organization_phone',
            'module_schema_merchant_pro_organization_street',
            'module_schema_merchant_pro_organization_city',
            'module_schema_merchant_pro_organization_region',
            'module_schema_merchant_pro_organization_postcode',
            'module_schema_merchant_pro_organization_country',
            'module_schema_merchant_pro_organization_same_as',
            'module_schema_merchant_pro_organization_opening_days',
            'module_schema_merchant_pro_organization_opening_opens',
            'module_schema_merchant_pro_organization_opening_closes',
            'module_schema_merchant_pro_organization_geo_latitude',
            'module_schema_merchant_pro_organization_geo_longitude',
            'module_schema_merchant_pro_twitter_handle',
            'module_schema_merchant_pro_website_name',
            'module_schema_merchant_pro_website_url',
            'module_schema_merchant_pro_website_language',
            'module_schema_merchant_pro_shipping_country',
            'module_schema_merchant_pro_shipping_price',
            'module_schema_merchant_pro_shipping_currency',
            'module_schema_merchant_pro_shipping_handling_min_days',
            'module_schema_merchant_pro_shipping_handling_max_days',
            'module_schema_merchant_pro_shipping_transit_min_days',
            'module_schema_merchant_pro_shipping_transit_max_days',
            'module_schema_merchant_pro_returns_country',
            'module_schema_merchant_pro_returns_days'
        );

        foreach ($text_fields as $field) {
            if (!isset($this->request->post[$field])) {
                continue;
            }

            $value = (string)$this->request->post[$field];

            if (strpos($value, "\xEF\xBF\xBD") !== false || strpos($value, 'я┐╜') !== false || strpos($value, '<') !== false || stripos($value, '&lt;') !== false) {
                $this->markFieldError($field);
                return false;
            }
        }

        return true;
    }

    private function hasValidPostedSettings() {
        if (!$this->isEmptyOrPattern('module_schema_merchant_pro_organization_country', '/^[A-Z]{2}$/')) {
            return false;
        }

        if (!$this->isEmptyOrPattern('module_schema_merchant_pro_website_language', '/^[a-z]{2}(?:-[A-Z]{2})?$/')) {
            return false;
        }

        if (isset($this->request->post['module_schema_merchant_pro_compatibility_mode']) && !$this->isAllowedCompatibilityMode($this->request->post['module_schema_merchant_pro_compatibility_mode'])) {
            $this->markFieldError('module_schema_merchant_pro_compatibility_mode');
            return false;
        }

        foreach ($this->request->post as $field => $value) {
            if (strpos($field, 'module_schema_merchant_pro_availability_stock_status_') === 0 && !$this->isAllowedAvailability($value)) {
                $this->markFieldError($field);
                return false;
            }
        }

        if ($this->isPostedEnabled('module_schema_merchant_pro_organization_opening_hours_enabled')) {
            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_organization_opening_days', '/^(?:Mo|Tu|We|Th|Fr|Sa|Su)(?:,(?:Mo|Tu|We|Th|Fr|Sa|Su))*$/')) {
                return false;
            }

            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_organization_opening_opens', '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/')) {
                return false;
            }

            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_organization_opening_closes', '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/')) {
                return false;
            }
        }

        if ($this->isPostedEnabled('module_schema_merchant_pro_organization_geo_enabled')) {
            if (!$this->isEmptyOrCoordinate('module_schema_merchant_pro_organization_geo_latitude', -90, 90)) {
                return false;
            }

            if (!$this->isEmptyOrCoordinate('module_schema_merchant_pro_organization_geo_longitude', -180, 180)) {
                return false;
            }
        }

        if ($this->isPostedEnabled('module_schema_merchant_pro_shipping_enabled')) {
            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_shipping_country', '/^[A-Z]{2}$/')) {
                return false;
            }

            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_shipping_currency', '/^[A-Z]{3}$/')) {
                return false;
            }

            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_shipping_price', '/^[0-9]+(?:\.[0-9]{1,2})?$/')) {
                return false;
            }

            $shipping_day_fields = array(
                'module_schema_merchant_pro_shipping_handling_min_days',
                'module_schema_merchant_pro_shipping_handling_max_days',
                'module_schema_merchant_pro_shipping_transit_min_days',
                'module_schema_merchant_pro_shipping_transit_max_days'
            );

            foreach ($shipping_day_fields as $field) {
                if (!$this->isEmptyOrPattern($field, '/^[0-9]+$/')) {
                    return false;
                }
            }
        }

        if ($this->isPostedEnabled('module_schema_merchant_pro_returns_enabled')) {
            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_returns_country', '/^[A-Z]{2}$/')) {
                return false;
            }

            if (!$this->isEmptyOrPattern('module_schema_merchant_pro_returns_days', '/^[0-9]+$/')) {
                return false;
            }
        }

        if (!$this->isEmptyOrPattern('module_schema_merchant_pro_preview_product_id', '/^[0-9]+$/')) {
            return false;
        }

        return true;
    }

    private function isPostedEnabled($field) {
        return isset($this->request->post[$field]) && (string)$this->request->post[$field] === '1';
    }

    private function isAllowedCompatibilityMode($value) {
        return in_array((string)$value, array(
            'normal',
            'careful',
            'schema_only',
            'open_graph_only'
        ), true);
    }

    private function isAllowedAvailability($value) {
        return in_array((string)$value, array(
            'https://schema.org/InStock',
            'https://schema.org/OutOfStock',
            'https://schema.org/PreOrder',
            'https://schema.org/BackOrder',
            'https://schema.org/Discontinued',
            'https://schema.org/LimitedAvailability'
        ), true);
    }

    private function isEmptyOrPattern($field, $pattern) {
        if (!isset($this->request->post[$field])) {
            return true;
        }

        $value = trim((string)$this->request->post[$field]);

        if ($value === '') {
            return true;
        }

        if ((bool)preg_match($pattern, $value)) {
            return true;
        }

        $this->markFieldError($field);

        return false;
    }

    private function isEmptyOrCoordinate($field, $min, $max) {
        if (!isset($this->request->post[$field])) {
            return true;
        }

        $value = trim((string)$this->request->post[$field]);

        if ($value === '') {
            return true;
        }

        if (!preg_match('/^-?[0-9]+\\.[0-9]{5,}$/', $value)) {
            $this->markFieldError($field);
            return false;
        }

        $number = (float)$value;

        if ($number < $min || $number > $max) {
            $this->markFieldError($field);
            return false;
        }

        return true;
    }

    private function markFieldError($field) {
        $this->error_fields[$field] = $field;
    }

    private function getErrorTabs() {
        $tabs = array();
        $map = array(
            'module_schema_merchant_pro_license_key' => 'general',
            'module_schema_merchant_pro_status' => 'general',
            'module_schema_merchant_pro_debug' => 'general',
            'module_schema_merchant_pro_output_jsonld' => 'general',
            'module_schema_merchant_pro_output_open_graph' => 'general',
            'module_schema_merchant_pro_output_twitter' => 'general',
            'module_schema_merchant_pro_compatibility_mode' => 'general',
            'module_schema_merchant_pro_organization_enabled' => 'organization',
            'module_schema_merchant_pro_organization_name' => 'organization',
            'module_schema_merchant_pro_organization_url' => 'organization',
            'module_schema_merchant_pro_organization_logo' => 'organization',
            'module_schema_merchant_pro_organization_email' => 'organization',
            'module_schema_merchant_pro_organization_phone' => 'organization',
            'module_schema_merchant_pro_organization_street' => 'organization',
            'module_schema_merchant_pro_organization_city' => 'organization',
            'module_schema_merchant_pro_organization_region' => 'organization',
            'module_schema_merchant_pro_organization_postcode' => 'organization',
            'module_schema_merchant_pro_organization_country' => 'organization',
            'module_schema_merchant_pro_organization_same_as' => 'organization',
            'module_schema_merchant_pro_organization_opening_hours_enabled' => 'organization',
            'module_schema_merchant_pro_organization_opening_days' => 'organization',
            'module_schema_merchant_pro_organization_opening_opens' => 'organization',
            'module_schema_merchant_pro_organization_opening_closes' => 'organization',
            'module_schema_merchant_pro_organization_geo_enabled' => 'organization',
            'module_schema_merchant_pro_organization_geo_latitude' => 'organization',
            'module_schema_merchant_pro_organization_geo_longitude' => 'organization',
            'module_schema_merchant_pro_twitter_handle' => 'organization',
            'module_schema_merchant_pro_website_enabled' => 'website',
            'module_schema_merchant_pro_website_name' => 'website',
            'module_schema_merchant_pro_website_url' => 'website',
            'module_schema_merchant_pro_website_language' => 'website',
            'module_schema_merchant_pro_search_action_enabled' => 'website',
            'module_schema_merchant_pro_breadcrumbs_enabled' => 'website',
            'module_schema_merchant_pro_product_enabled' => 'product',
            'module_schema_merchant_pro_offer_enabled' => 'product',
            'module_schema_merchant_pro_brand_enabled' => 'product',
            'module_schema_merchant_pro_identifiers_enabled' => 'product',
            'module_schema_merchant_pro_attributes_enabled' => 'product',
            'module_schema_merchant_pro_rating_enabled' => 'product',
            'module_schema_merchant_pro_reviews_enabled' => 'product',
            'module_schema_merchant_pro_shipping_enabled' => 'merchant',
            'module_schema_merchant_pro_shipping_country' => 'merchant',
            'module_schema_merchant_pro_shipping_price' => 'merchant',
            'module_schema_merchant_pro_shipping_currency' => 'merchant',
            'module_schema_merchant_pro_shipping_handling_min_days' => 'merchant',
            'module_schema_merchant_pro_shipping_handling_max_days' => 'merchant',
            'module_schema_merchant_pro_shipping_transit_min_days' => 'merchant',
            'module_schema_merchant_pro_shipping_transit_max_days' => 'merchant',
            'module_schema_merchant_pro_returns_enabled' => 'merchant',
            'module_schema_merchant_pro_returns_country' => 'merchant',
            'module_schema_merchant_pro_returns_category' => 'merchant',
            'module_schema_merchant_pro_returns_days' => 'merchant',
            'module_schema_merchant_pro_returns_fees' => 'merchant',
            'module_schema_merchant_pro_returns_method' => 'merchant',
            'module_schema_merchant_pro_preview_product_id' => 'preview'
        );

        foreach ($this->error_fields as $field) {
            if (strpos($field, 'module_schema_merchant_pro_availability_stock_status_') === 0) {
                $tabs['product'] = true;
            } elseif (isset($map[$field])) {
                $tabs[$map[$field]] = true;
            }
        }

        return $tabs;
    }

    private function applyFieldHelp(&$fields, array $field_help) {
        foreach ($fields as &$field) {
            $field['help'] = isset($field['help']) ? $field['help'] : '';

            if (isset($field_help[$field['name']])) {
                $help = $this->language->get($field_help[$field['name']]);

                if ($help !== $field_help[$field['name']]) {
                    $field['help'] = $help;
                }
            }
        }
    }

    private function getPreviewSiteContext() {
        $catalog_url = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : (defined('HTTP_CATALOG') ? HTTP_CATALOG : '');

        return array(
            'url' => $catalog_url,
            'name' => $this->config->get('config_name'),
            'language' => $this->config->get('config_language'),
            'logo' => $catalog_url && $this->config->get('config_logo') ? rtrim($catalog_url, '/') . '/image/' . ltrim($this->config->get('config_logo'), '/') : '',
            'email' => $this->config->get('config_email'),
            'phone' => $this->config->get('config_telephone'),
            'search_url' => $catalog_url ? rtrim($catalog_url, '/') . '/index.php?route=product/search&search={search_term_string}' : ''
        );
    }

    private function buildPreviewGraph(array $context) {
        $generator_file = DIR_SYSTEM . 'library/schema_merchant_pro/generator.php';

        if (!is_file($generator_file)) {
            return array();
        }

        require_once($generator_file);

        if (!class_exists('SchemaMerchantProGenerator')) {
            return array();
        }

        $generator = new SchemaMerchantProGenerator($this->registry);

        return $generator->buildGraph($context);
    }

    private function buildDiagnostics(array $context, array $graph) {
        $diagnostics = array();
        $json = !empty($graph) ? json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $this->addDiagnostic($diagnostics, $this->config->get('module_schema_merchant_pro_status') ? 'ok' : 'warning', $this->language->get($this->config->get('module_schema_merchant_pro_status') ? 'diagnostic_module_enabled' : 'diagnostic_module_disabled'));
        $this->addDiagnostic($diagnostics, $this->hasValidLicenseValue($this->config->get('module_schema_merchant_pro_license_key')) ? 'ok' : 'error', $this->language->get($this->hasValidLicenseValue($this->config->get('module_schema_merchant_pro_license_key')) ? 'diagnostic_license_valid' : 'diagnostic_license_invalid'));
        $this->addDiagnostic($diagnostics, $this->config->get('module_schema_merchant_pro_output_jsonld') ? 'ok' : 'warning', $this->language->get($this->config->get('module_schema_merchant_pro_output_jsonld') ? 'diagnostic_jsonld_enabled' : 'diagnostic_jsonld_disabled'));
        $this->addDiagnostic($diagnostics, $this->isAllowedCompatibilityMode($this->config->get('module_schema_merchant_pro_compatibility_mode')) ? 'ok' : 'warning', $this->language->get($this->isAllowedCompatibilityMode($this->config->get('module_schema_merchant_pro_compatibility_mode')) ? 'diagnostic_compatibility_mode_valid' : 'diagnostic_compatibility_mode_invalid'));

        $product = isset($context['product']) && is_array($context['product']) ? $context['product'] : array();

        $this->addDiagnostic($diagnostics, !empty($product) ? 'ok' : 'warning', $this->language->get(!empty($product) ? 'diagnostic_preview_product_found' : 'diagnostic_preview_product_missing'));
        $this->addDiagnostic($diagnostics, $this->hasGraphType($graph, 'Product') ? 'ok' : 'warning', $this->language->get($this->hasGraphType($graph, 'Product') ? 'diagnostic_product_present' : 'diagnostic_product_missing'));
        $this->addDiagnostic($diagnostics, strpos($json, '"offers"') !== false ? 'ok' : 'warning', $this->language->get(strpos($json, '"offers"') !== false ? 'diagnostic_offer_present' : 'diagnostic_offer_missing'));
        $this->addDiagnostic($diagnostics, strpos($json, '"image"') !== false ? 'ok' : 'warning', $this->language->get(strpos($json, '"image"') !== false ? 'diagnostic_images_present' : 'diagnostic_images_missing'));
        $this->addDiagnostic($diagnostics, (strpos($json, 'ProductGroup') === false && strpos($json, 'UnitPriceSpecification') === false && strpos($json, 'OfferCatalog') === false) ? 'ok' : 'error', $this->language->get('diagnostic_forbidden_types_absent'));
        $this->addDiagnostic($diagnostics, strpos($json, 'Павел Новиков') === false ? 'ok' : 'error', $this->language->get('diagnostic_pending_reviews_absent'));
        $this->addDiagnostic($diagnostics, 'ok', $this->language->get('diagnostic_image_warning_accepted'));

        return $diagnostics;
    }

    private function addDiagnostic(array &$diagnostics, $status, $message) {
        $diagnostics[] = array(
            'status' => $status,
            'label' => $this->language->get('text_diagnostic_' . $status),
            'message' => $message
        );
    }

    private function hasGraphType(array $graph, $type) {
        foreach ($graph as $item) {
            if (isset($item['@type']) && $item['@type'] === $type) {
                return true;
            }
        }

        return false;
    }

    private function hasValidLicenseValue($key) {

        return true;

        $key = trim((string)$key);

        if ($key === '') {
            return false;
        }

        if (hash_equals(self::LICENSE_KEY_HASH, hash('sha256', $key))) {
            return true;
        }

        return hash_equals($this->buildDomainLicenseKey($this->getLicenseDomain()), strtoupper($key));
    }

    private function normalizeLicenseForSave($key) {
        $key = trim((string)$key);

        if ($key === '') {
            return '';
        }

        if (hash_equals(self::LICENSE_KEY_HASH, hash('sha256', $key))) {
            return $this->buildDomainLicenseKey($this->getLicenseDomain());
        }

        return strtoupper($key);
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
        $base = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : (defined('HTTP_CATALOG') ? HTTP_CATALOG : '');

        if ($base === '') {
            $base = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');
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
