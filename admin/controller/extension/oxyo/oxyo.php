<?php
class ControllerExtensionOxyoOxyo extends Controller
{

    private $error = array();
    private $store_id = 0;

    public function index()
    {
        $model_module_load = 'setting/module';
        $model_module_path = 'model_setting_module';
        $token_prefix = 'user_token';

        // Set store id first
        if (isset($this->request->get['store_id'])) {
            $data['store_id'] = $this->request->get['store_id'];
        } else if (isset($this->request->post['store_id'])) {
            $data['store_id'] = $this->request->post['store_id'];
        } else {
            $data['store_id'] = 0;
        }

        $this->store_id = $data['store_id'];

        // Check if import demo store
        if (isset($this->request->get['import_demo']) && ($this->request->server['REQUEST_METHOD'] != 'POST') && $this->validate()) {
            $this->load->model('extension/oxyo/demo_stores/' . $this->request->get['import_demo'] . '/installer');
            $selected_store = $this->request->get['import_demo'];
            $path = 'model_extension_oxyo_demo_stores_' . $selected_store . '_installer';
            $this->$path->demoSetup();
        }

        $this->document->addStyle('view/javascript/oxyo/oxyo_panel.css');
        $this->document->addScript('view/javascript/oxyo/js/bootstrap-colorpicker.min.js');
        $this->document->addStyle('view/javascript/oxyo/css/bootstrap-colorpicker.min.css');
        $this->document->addStyle('view/javascript/oxyo/icons_list/fonts/style.css');

        $this->load->language('oxyo/oxyo');

        $this->load->model('setting/setting');
        $this->load->model('extension/oxyo/oxyo');

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['text_confirm'] = $this->language->get('text_confirm');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/oxyo/oxyo', $token_prefix . '=' . $this->session->data[$token_prefix], true)
        );

        $data['token'] = $this->session->data[$token_prefix];

        // Success and Warning messages
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

        // Language strings
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['error_permission'] = $this->language->get('error_permission');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_off'] = $this->language->get('text_off');
        $data['text_on'] = $this->language->get('text_on');

        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');

        $data['text_show_in_header'] = $this->language->get('text_show_in_header');
        $data['text_show_in_footer'] = $this->language->get('text_show_in_footer');

        // Data strings
        $data['oxyo_theme_version'] = $this->getConfig('oxyo_theme_version');
        $data['theme_default_directory'] = $this->getConfig('theme_default_directory');

        // List Mega Menu Modules
        $this->load->model($model_module_load);
        $data['menu_modules'] = array();
        $menu_modules = $this->$model_module_path->getModulesByCode('oxyo_megamenu');
        foreach ($menu_modules as $menu_module) {
            $data['menu_modules'][] = array(
                'name' => strip_tags($menu_module['name']),
                'module_id' => $menu_module['module_id']
            );
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            // Added to delete oxyo fonts from database when they are completely deleted
            if (empty($this->request->post['settings']['oxyo']['oxyo_fonts'])) {
                $this->request->post['settings']['oxyo']['oxyo_fonts'] = array();
            }
            // Added to delete oxyo footer columns from database when they are completely deleted
            if (empty($this->request->post['settings']['oxyo']['oxyo_footer_columns'])) {
                $this->request->post['settings']['oxyo']['oxyo_footer_columns'] = array();
            }
            // Added to delete oxyo header links from database when they are completely deleted
            if (empty($this->request->post['settings']['oxyo']['oxyo_links'])) {
                $this->request->post['settings']['oxyo']['oxyo_links'] = array();
            }

            if (isset($this->request->post['settings']['oxyo']['main_phone']) && !empty($this->request->post['settings']['oxyo']['main_phone']) && $this->request->post['settings']['oxyo']['main_phone'] != '') {
                $this->model_setting_setting->editSettingValue('config', 'config_telephone', $this->request->post['settings']['oxyo']['main_phone'], $data['store_id']);
            }

            if (isset($this->request->post['settings']['oxyo']['main_email']) && !empty($this->request->post['settings']['oxyo']['main_email']) && $this->request->post['settings']['oxyo']['main_email'] != '') {
                $this->model_setting_setting->editSettingValue('config', 'config_email', $this->request->post['settings']['oxyo']['main_email'], $data['store_id']);
            }

            if (isset($this->request->post['settings']['oxyo']['main_address']) && !empty($this->request->post['settings']['oxyo']['main_address']) && $this->request->post['settings']['oxyo']['main_address'] != '') {
                $this->model_setting_setting->editSettingValue('config', 'config_address', $this->request->post['settings']['oxyo']['main_address'], $data['store_id']);
            }

            if (isset($this->request->post['settings']['oxyo']['working_hours']) && !empty($this->request->post['settings']['oxyo']['working_hours']) && $this->request->post['settings']['oxyo']['working_hours'] != '') {
                $this->model_setting_setting->editSettingValue('config', 'config_open', $this->request->post['settings']['oxyo']['working_hours'], $data['store_id']);
            }

            foreach ($this->request->post['settings'] as $code => $setting_data) {
                foreach ($setting_data as $key => $value) {
                    $setting_value = $this->model_extension_oxyo_oxyo->getSettingValue($key, $data['store_id']);

                    if (!empty($setting_value)) {
                        $this->model_setting_setting->editSettingValue($code, $key, $value, $data['store_id']);
                    } else {
                        $this->db->query("DELETE FROM " . DB_PREFIX . "setting "
                            . "WHERE store_id = '" . (int) $data['store_id'] . "' "
                            . "AND `key` = '" . $key . "' "
                            . "AND `code` = '" . $this->db->escape($code) . "'");

                        if (!is_array($value)) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET "
                                . "store_id = '" . (int) $data['store_id'] . "', "
                                . "`code` = '" . $this->db->escape($code) . "', "
                                . "`key` = '" . $this->db->escape($key) . "', "
                                . "`value` = '" . $this->db->escape($value) . "'");
                        } else {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET "
                                . "store_id = '" . (int) $data['store_id'] . "', "
                                . "`code` = '" . $this->db->escape($code) . "', "
                                . "`key` = '" . $this->db->escape($key) . "', "
                                . "`value` = '" . $this->db->escape(json_encode($value, true)) . "', "
                                . "serialized = '1'");
                        }
                    }
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->cache->delete('oxyo_mandatory_css_store_' . $data['store_id']);
            $this->cache->delete('oxyo_styles_cache_store_' . $data['store_id']);
            $this->cache->delete('oxyo_fonts_cache_store_' . $data['store_id']);

            $this->response->redirect($this->url->link('extension/oxyo/oxyo', $token_prefix . '=' . $this->session->data[$token_prefix] . '&store_id=' . (int) $data['store_id'], true));
        }

        // Fonts
        $oxyo_fonts_database = $this->getConfig('oxyo_fonts');

        if (isset($this->request->post['oxyo_fonts'])) {
            $data['oxyo_fonts'] = $this->request->post['oxyo_fonts'];
        } elseif (!empty($oxyo_fonts_database)) {
            $oxyo_fonts = $oxyo_fonts_database;
        } else {
            $oxyo_fonts = array();
        }

        $data['oxyo_fonts'] = array();
        if ($oxyo_fonts) {
            foreach ($oxyo_fonts as $oxyo_font) {
                $data['oxyo_fonts'][] = array(
                    'import' => $oxyo_font['import'],
                    'name' => $oxyo_font['name']
                );
            }
        }

        // System Fonts //
        $data['system_fonts'][] = array();
        $data['system_fonts'] = array(
            "Arial, Helvetica Neue, Helvetica, sans-serif" => "Arial",
            "Comic Sans MS, Comic Sans MS, cursive" => "Comic sans",
            "Courier New, Courier New, monospace" => "Courier New",
            "Georgia, Times, Times New Roman, serif" => "Georgia",
            "Impact, Charcoal, sans-serif" => "Impact",
            "Lucida Sans Typewriter, Lucida Console, Monaco, Bitstream Vera Sans Mono, monospace" => "Lucida Sans Typewriter",
            "Palatino, Palatino Linotype, Palatino LT STD, Book Antiqua, Georgia, serif" => "Palatino Linotype",
            "Tahoma, Verdana, Segoe, sans-serif" => "Tahoma",
            "Times New Roman, Times, Baskerville, Georgia, serif" => "Times New Roman",
            "Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif" => "Trebuchet",
            "Verdana, Geneva, sans-serif" => "Verdana"
        );


        // Images
        $this->load->model('tool/image');

        if (isset($this->request->post['oxyo_popup_note_img']) && is_file(DIR_IMAGE . $this->request->post['oxyo_popup_note_img'])) {
            $data['popup_thumb'] = $this->model_tool_image->resize($this->request->post['oxyo_popup_note_img'], 100, 100);
        } elseif ($this->getConfig('oxyo_popup_note_img') && is_file(DIR_IMAGE . $this->getConfig('oxyo_popup_note_img'))) {
            $data['popup_thumb'] = $this->model_tool_image->resize($this->getConfig('oxyo_popup_note_img'), 100, 100);
        } else {
            $data['popup_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['oxyo_bc_bg_img']) && is_file(DIR_IMAGE . $this->request->post['oxyo_bc_bg_img'])) {
            $data['bc_thumb'] = $this->model_tool_image->resize($this->request->post['oxyo_bc_bg_img'], 100, 100);
        } elseif ($this->getConfig('oxyo_bc_bg_img') && is_file(DIR_IMAGE . $this->getConfig('oxyo_bc_bg_img'))) {
            $data['bc_thumb'] = $this->model_tool_image->resize($this->getConfig('oxyo_bc_bg_img'), 100, 100);
        } else {
            $data['bc_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['oxyo_body_bg_img']) && is_file(DIR_IMAGE . $this->request->post['oxyo_body_bg_img'])) {
            $data['body_thumb'] = $this->model_tool_image->resize($this->request->post['oxyo_body_bg_img'], 100, 100);
        } elseif ($this->getConfig('oxyo_body_bg_img') && is_file(DIR_IMAGE . $this->getConfig('oxyo_body_bg_img'))) {
            $data['body_thumb'] = $this->model_tool_image->resize($this->getConfig('oxyo_body_bg_img'), 100, 100);
        } else {
            $data['body_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['oxyo_payment_img']) && is_file(DIR_IMAGE . $this->request->post['oxyo_payment_img'])) {
            $data['payment_thumb'] = $this->model_tool_image->resize($this->request->post['oxyo_popup_note_img'], 100, 100);
        } elseif ($this->getConfig('oxyo_payment_img') && is_file(DIR_IMAGE . $this->getConfig('oxyo_payment_img'))) {
            $data['payment_thumb'] = $this->model_tool_image->resize($this->getConfig('oxyo_payment_img'), 100, 100);
        } else {
            $data['payment_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


        // Variables
        $codes = array();

        $codes['config'] = array(
            'config_theme'
        );

        $codes['theme_default'] = array(
            'theme_default_directory',
            'theme_default_product_limit',
            'theme_default_product_description_length',
            'theme_default_image_category_width',
            'theme_default_image_category_height',
            'theme_default_image_thumb_width',
            'theme_default_image_thumb_height',
            'theme_default_image_popup_width',
            'theme_default_image_popup_height',
            'theme_default_image_product_width',
            'theme_default_image_product_height',
            'theme_default_image_additional_width',
            'theme_default_image_additional_height',
            'theme_default_image_related_width',
            'theme_default_image_related_height',
            'theme_default_image_compare_width',
            'theme_default_image_compare_height',
            'theme_default_image_wishlist_width',
            'theme_default_image_wishlist_height',
            'theme_default_image_cart_width',
            'theme_default_image_cart_height',
            'theme_default_image_location_width',
            'theme_default_image_location_height',
        );

        $codes['oxyo_version'] = array(
            'oxyo_theme_version'
        );

        $codes['oxyo'] = array(
            'main_phone',
            'main_phone_header',
            'whatsapp_phone',
            'whatsapp_phone_header',
            'telegram',
            'telegram_header',
            'vkontakte',
            'vkontakte_header',
            'callback',
            'callback_header',
            'main_email',
            'main_address',
            'working_hours',
            'avito',
            'instagram',
            'tiktok',
            'oxyo_header',
            'use_custom_links',
            'oxyo_list_style',
            'oxyo_promo',
            'primary_menu',
            'secondary_menu',
            'oxyo_links',
            'oxyo_promo',
            'oxyo_promo2',
            'top_line_style',
            'top_line_width',
            'top_line_height',
            'main_header_width',
            'main_header_height',
            'main_header_height_mobile',
            'main_header_height_sticky',
            'menu_height_normal',
            'menu_height_sticky',
            'logo_maxwidth',
            'main_menu_align',
            'header_login',
            'header_search',
            'oxyo_breadcrumbs_full_path',
            'oxyo_titles_listings',
            'oxyo_titles_product',
            'oxyo_titles_account',
            'oxyo_titles_checkout',
            'oxyo_titles_contact',
            'oxyo_titles_blog',
            'oxyo_titles_default',
            'oxyo_back_btn',
            'product_layout',
            'meta_description_status',
            'oxyo_hover_zoom',
            'full_width_tabs',
            'product_tabs_style',
            'oxyo_share_btn',
            'catalog_mode',
            'oxyo_cart_action',
            'wishlist_status',
            'oxyo_wishlist_action',
            'compare_status',
            'oxyo_compare_action',
            'quickview_status',
            'ex_tax_status',
            'oxyo_list_style',
            'countdown_status',
            'items_mobile_fw',
            'category_thumb_status',
            'category_subs_status',
            'oxyo_subs_grid',
            'oxyo_prod_grid',
            'newlabel_status',
            'stock_badge_status',
            'salebadge_status',
            'oxyo_map_style',
            'oxyo_map_lon',
            'oxyo_map_lat',
            'oxyo_map_api',
            'product_question_status',
            'questions_per_page',
            'questions_new_status',
            'oxyo_rel_prod_grid',
            'product_page_countdown',
            'oxyo_cut_names',
            'overwrite_footer_links',
            'footer_block_1',
            'footer_block_2',
            'footer_infoline_1',
            'footer_infoline_2',
            'footer_infoline_3',
            'oxyo_payment_img',
            'footer_block_title',
            'oxyo_footer_columns',
            'oxyo_copyright',
            'oxyo_popup_note_status',
            'oxyo_popup_note_once',
            'oxyo_popup_note_home',
            'oxyo_popup_note_img',
            'oxyo_popup_note_title',
            'oxyo_popup_note_block',
            'oxyo_popup_note_delay',
            'oxyo_popup_note_w',
            'oxyo_popup_note_h',
            'oxyo_popup_note_m',
            'oxyo_cookie_bar_status',
            'oxyo_cookie_bar_url',
            'oxyo_top_promo_status',
            'oxyo_top_promo_width',
            'oxyo_top_promo_close',
            'oxyo_top_promo_align',
            'oxyo_top_promo_text',
            'oxyo_design_status',
            'oxyo_primary_accent_color',
            'oxyo_top_note_bg',
            'oxyo_top_note_color',
            'oxyo_top_line_bg',
            'oxyo_top_line_color',
            'oxyo_header_bg',
            'oxyo_header_color',
            'oxyo_header_accent',
            'oxyo_header_menu_bg',
            'oxyo_header_menu_color',
            'oxyo_header_menu_accent',
            'oxyo_search_scheme',
            'oxyo_vertical_menu_bg',
            'oxyo_vertical_menu_bg_hover',
            'oxyo_menutag_sale_bg',
            'oxyo_menutag_new_bg',
            'oxyo_bc_color',
            'oxyo_bc_bg_color',
            'oxyo_bc_bg_img',
            'oxyo_bc_bg_img_pos',
            'oxyo_bc_bg_img_repeat',
            'oxyo_bc_bg_img_size',
            'oxyo_bc_bg_img_att',
            'oxyo_body_bg_color',
            'oxyo_body_bg_img',
            'oxyo_body_bg_img_pos',
            'oxyo_body_bg_img_repeat',
            'oxyo_body_bg_img_size',
            'oxyo_body_bg_img_att',
            'oxyo_default_btn_bg',
            'oxyo_default_btn_color',
            'oxyo_default_btn_bg_hover',
            'oxyo_default_btn_color_hover',
            'oxyo_contrast_btn_bg',
            'oxyo_salebadge_bg',
            'oxyo_salebadge_color',
            'oxyo_newbadge_bg',
            'oxyo_newbadge_color',
            'oxyo_price_color',
            'oxyo_footer_bg',
            'oxyo_footer_color',
            'oxyo_footer_h5_sep',
            'oxyo_sticky_columns',
            'oxyo_sticky_columns_offset',
            'oxyo_main_layout',
            'oxyo_content_width',
            'oxyo_cart_icon',
            'oxyo_typo_status',
            'oxyo_fonts',
            'body_font_fam',
            'body_font_italic_status',
            'body_font_bold_weight',
            'contrast_font_fam',
            'body_font_size_16',
            'body_font_size_15',
            'body_font_size_14',
            'body_font_size_13',
            'body_font_size_12',
            'headings_fam',
            'headings_weight',
            'headings_size_sm',
            'headings_size_lg',
            'h1_inline_fam',
            'h1_inline_size',
            'h1_inline_weight',
            'h1_inline_trans',
            'h1_inline_ls',
            'h1_breadcrumb_fam',
            'h1_breadcrumb_size',
            'h1_breadcrumb_weight',
            'h1_breadcrumb_trans',
            'h1_breadcrumb_ls',
            'widget_sm_fam',
            'widget_sm_size',
            'widget_sm_weight',
            'widget_sm_trans',
            'widget_sm_ls',
            'widget_lg_fam',
            'widget_lg_size',
            'widget_lg_weight',
            'widget_lg_trans',
            'widget_lg_ls',
            'menu_font_fam',
            'menu_font_size',
            'menu_font_weight',
            'menu_font_trans',
            'menu_font_ls',
            'oxyo_sticky_header',
            'oxyo_home_overlay_header',
            'oxyo_widget_title_style',
            'quickview_popup_image_width',
            'quickview_popup_image_height',
            'subcat_image_width',
            'subcat_image_height',
            'oxyo_custom_css_status',
            'oxyo_custom_css',
            'oxyo_custom_js_status',
            'oxyo_custom_js',
            'oxyo_thumb_swap',
            'oxyo_price_update',
            'oxyo_sharing_style',
            // Stickers
            'sticker_sale',
            'sticker_new',
        );

        foreach ($codes as $code => $variables) {
            foreach ($variables as $variable) {
                if (isset($this->request->post[$variable])) {
                    $data[$variable] = $this->request->post[$variable];
                } else {
                    $data[$variable] = $this->getConfig($variable);
                }
            }
        }

        // var_dump($data['sticker_sale']);

        // Stickers
        // $data['oxyo']['sticker_sale'];

        // var_dump($this->request->post['main_phone']);
        // exit;

        // if (isset($this->request->post['main_phone'])) {
        //     $this->model_setting_setting->editSetting('config', array('config_telephone' => $this->request->post['oxyo']['main_phone']), $data['store_id']);
        // }

        // Footer links
        $oxyo_footer_columns = $data['oxyo_footer_columns'];
        $data['oxyo_footer_columns'] = array();
        if (!empty($oxyo_footer_columns)) {
            foreach ($oxyo_footer_columns as $oxyo_footer_column) {
                $links = array();
                $i = 0;
                if (isset($oxyo_footer_column['links'])) {
                    foreach ($oxyo_footer_column['links'] as $link) {
                        $links[$i] = $link;
                        $i++;
                    }
                    usort($links, function ($a, $b) {
                        return $a['sort'] - $b['sort'];
                    });
                }
                $data['oxyo_footer_columns'][] = array(
                    'title' => $oxyo_footer_column['title'],
                    'sort' => $oxyo_footer_column['sort'],
                    'links' => $links,
                );
            }
            usort($data['oxyo_footer_columns'], function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
        }

        // Header static links
        if (!empty($data['oxyo_links'])) {
            usort($data['oxyo_links'], function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
        }

        // Theme content start
        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_save'] = $this->language->get('button_save');

        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_enabled'] = $this->language->get('text_enabled');

        $data['action'] = $this->url->link('extension/oxyo/oxyo', $token_prefix . '=' . $this->session->data[$token_prefix], true);


        // Stores
        $data['stores'] = array();

        $data['stores'][] = array(
            'name' => 'Default',
            'href' => '',
            'store_id' => 0
        );

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = $store;
        }

        // One Click Installer
        $data['demo_import_url'] = $this->url->link('extension/oxyo/oxyo', $token_prefix . '=' . $this->session->data[$token_prefix] . '&import_demo=', true);

        $data['demos'] = array();
        $demos = glob(DIR_APPLICATION . 'model/extension/oxyo/demo_stores/*');
        if ($demos) {
            natsort($demos);
            foreach ($demos as $demo) {
                $data['demos'][] = array(
                    'demo_id' => basename($demo),
                    'name' => file_get_contents(DIR_APPLICATION . 'model/extension/oxyo/demo_stores/' . basename($demo) . '/name.txt')
                );
            }
        }

        // Fallback values for the first time
        if (is_null($this->getConfig('theme_default_directory'))) $data['theme_default_directory'] = 'default';
        if (is_null($this->getConfig('oxyo_header'))) $data['oxyo_header'] = 'header1';
        if (is_null($this->getConfig('top_line_style'))) $data['top_line_style'] = '1';
        if (is_null($this->getConfig('top_line_height'))) $data['top_line_height'] = '41';
        if (is_null($this->getConfig('main_header_height'))) $data['main_header_height'] = '104';
        if (is_null($this->getConfig('main_header_height_mobile'))) $data['main_header_height_mobile'] = '70';
        if (is_null($this->getConfig('main_header_height_sticky'))) $data['main_header_height_sticky'] = '70';
        if (is_null($this->getConfig('menu_height_normal'))) $data['menu_height_normal'] = '50';
        if (is_null($this->getConfig('menu_height_sticky'))) $data['menu_height_sticky'] = '47';
        if (is_null($this->getConfig('logo_maxwidth'))) $data['logo_maxwidth'] = '250';
        if (is_null($this->getConfig('oxyo_sticky_header'))) $data['oxyo_sticky_header'] = '1';
        if (is_null($this->getConfig('oxyo_home_overlay_header'))) $data['oxyo_home_overlay_header'] = '0';
        if (is_null($this->getConfig('header_login'))) $data['header_login'] = '1';
        if (is_null($this->getConfig('header_search'))) $data['header_search'] = '1';
        if (is_null($this->getConfig('primary_menu'))) $data['primary_menu'] = 'oc';
        if (is_null($this->getConfig('use_custom_links'))) $data['use_custom_links'] = '0';
        if (is_null($this->getConfig('oxyo_back_btn'))) $data['oxyo_back_btn'] = '0';
        if (is_null($this->getConfig('oxyo_hover_zoom'))) $data['oxyo_hover_zoom'] = '1';
        if (is_null($this->getConfig('meta_description_status'))) $data['meta_description_status'] = '1';
        if (is_null($this->getConfig('product_page_countdown'))) $data['product_page_countdown'] = '0';
        if (is_null($this->getConfig('oxyo_share_btn'))) $data['oxyo_share_btn'] = '1';
        if (is_null($this->getConfig('ex_tax_status'))) $data['ex_tax_status'] = '0';
        if (is_null($this->getConfig('product_question_status'))) $data['product_question_status'] = '0';
        if (is_null($this->getConfig('questions_new_status'))) $data['questions_new_status'] = '0';
        if (is_null($this->getConfig('oxyo_rel_prod_grid'))) $data['oxyo_rel_prod_grid'] = '4';
        if (is_null($this->getConfig('category_thumb_status'))) $data['category_thumb_status'] = '0';
        if (is_null($this->getConfig('category_subs_status'))) $data['category_subs_status'] = '1';
        if (is_null($this->getConfig('oxyo_subs_grid'))) $data['oxyo_subs_grid'] = '5';
        if (is_null($this->getConfig('oxyo_prod_grid'))) $data['oxyo_prod_grid'] = '3';
        if (is_null($this->getConfig('catalog_mode'))) $data['catalog_mode'] = '0';
        if (is_null($this->getConfig('oxyo_cut_names'))) $data['oxyo_cut_names'] = '1';
        if (is_null($this->getConfig('items_mobile_fw'))) $data['items_mobile_fw'] = '1';
        if (is_null($this->getConfig('quickview_status'))) $data['quickview_status'] = '1';
        if (is_null($this->getConfig('salebadge_status'))) $data['salebadge_status'] = '1';
        if (is_null($this->getConfig('stock_badge_status'))) $data['stock_badge_status'] = '1';
        if (is_null($this->getConfig('countdown_status'))) $data['countdown_status'] = '1';
        if (is_null($this->getConfig('wishlist_status'))) $data['wishlist_status'] = '1';
        if (is_null($this->getConfig('compare_status'))) $data['compare_status'] = '1';
        if (is_null($this->getConfig('overwrite_footer_links'))) $data['overwrite_footer_links'] = '0';
        if (is_null($this->getConfig('oxyo_top_promo_status'))) $data['oxyo_top_promo_status'] = '0';
        if (is_null($this->getConfig('oxyo_top_promo_close'))) $data['oxyo_top_promo_close'] = '0';
        if (is_null($this->getConfig('oxyo_cookie_bar_status'))) $data['oxyo_cookie_bar_status'] = '0';
        if (is_null($this->getConfig('oxyo_popup_note_status'))) $data['oxyo_popup_note_status'] = '0';
        if (is_null($this->getConfig('oxyo_popup_note_once'))) $data['oxyo_popup_note_once'] = '0';
        if (is_null($this->getConfig('oxyo_popup_note_home'))) $data['oxyo_popup_note_home'] = '0';
        if (is_null($this->getConfig('oxyo_popup_note_m'))) $data['oxyo_popup_note_m'] = '767';
        if (is_null($this->getConfig('oxyo_cart_icon'))) $data['oxyo_cart_icon'] = 'global-cart-basket';
        if (is_null($this->getConfig('oxyo_main_layout'))) $data['oxyo_main_layout'] = '0';
        if (is_null($this->getConfig('product_tabs_style'))) $data['product_tabs_style'] = 'nav-tabs-lg text-center';
        if (is_null($this->getConfig('oxyo_sticky_columns'))) $data['oxyo_sticky_columns'] = '1';
        if (is_null($this->getConfig('oxyo_design_status'))) $data['oxyo_design_status'] = '0';
        if (is_null($this->getConfig('oxyo_typo_status'))) $data['oxyo_typo_status'] = '0';
        if (is_null($this->getConfig('oxyo_custom_css_status'))) $data['oxyo_custom_css_status'] = '0';
        if (is_null($this->getConfig('oxyo_custom_js_status'))) $data['oxyo_custom_js_status'] = '0';
        if (is_null($this->getConfig('theme_default_product_limit'))) $data['theme_default_product_limit'] = '12';
        if (is_null($this->getConfig('body_font_italic_status'))) $data['body_font_italic_status'] = '1';
        if (is_null($this->getConfig('quickview_popup_image_width'))) $data['quickview_popup_image_width'] = '465';
        if (is_null($this->getConfig('quickview_popup_image_height'))) $data['quickview_popup_image_height'] = '590';
        if (is_null($this->getConfig('subcat_image_width'))) $data['subcat_image_width'] = '200';
        if (is_null($this->getConfig('subcat_image_height'))) $data['subcat_image_height'] = '264';
        if (is_null($this->getConfig('oxyo_thumb_swap'))) $data['oxyo_thumb_swap'] = '1';
        if (is_null($this->getConfig('oxyo_price_update'))) $data['oxyo_price_update'] = '1';
        if (is_null($this->getConfig('oxyo_sharing_style'))) $data['oxyo_sharing_style'] = 'small';

        if (empty($data['oxyo_theme_version'])) {
            $data['theme_default_image_category_width'] = '335';
            $data['theme_default_image_category_height'] = '425';
            $data['theme_default_image_thumb_width'] = '406';
            $data['theme_default_image_thumb_height'] = '516';
            $data['theme_default_image_popup_width'] = '910';
            $data['theme_default_image_popup_height'] = '1155';
            $data['theme_default_image_product_width'] = '262';
            $data['theme_default_image_product_height'] = '334';
            $data['theme_default_image_additional_width'] = '130';
            $data['theme_default_image_additional_height'] = '165';
            $data['theme_default_image_related_width'] = '262';
            $data['theme_default_image_related_height'] = '334';
            $data['theme_default_image_compare_width'] = '130';
            $data['theme_default_image_compare_height'] = '165';
            $data['theme_default_image_wishlist_width'] = '55';
            $data['theme_default_image_wishlist_height'] = '70';
            $data['theme_default_image_cart_width'] = '100';
            $data['theme_default_image_cart_height'] = '127';
            $data['theme_default_image_location_width'] = '268';
            $data['theme_default_image_location_height'] = '50';
            $data['quickview_popup_image_width'] = '465';
            $data['quickview_popup_image_height'] = '590';
            $data['subcat_image_width'] = '200';
            $data['subcat_image_height'] = '264';
        }

        // Breadcrumbs
        $data['bc_titles'] = [
            'listings' => $this->language->get('bc_title_listings'),
            'product'  => $this->language->get('bc_title_product'),
            'account'  => $this->language->get('bc_title_account'),
            'checkout' => $this->language->get('bc_title_checkout'),
            'contact'  => $this->language->get('bc_title_contact'),
            'blog'     => $this->language->get('bc_title_blog'),
            'default'  => $this->language->get('bc_title_default'),
        ];

        $data['bc_tooltips'] = array(
            'listings' => $this->language->get('bc_tooltip_listings'),
            'product' => $this->language->get('bc_tooltip_product'),
            'account' => $this->language->get('bc_tooltip_account'),
            'checkout' => $this->language->get('bc_tooltip_checkout'),
            'contact' => $this->language->get('bc_tooltip_contact'),
            'blog' => $this->language->get('bc_tooltip_blog'),
            'default' => $this->language->get('bc_tooltip_default')
        );

        // Render page
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/oxyo/oxyo', $data));

        unset($this->session->data['permission_error']);
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/oxyo/oxyo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    private function getConfig($key)
    {
        $this->load->model('extension/oxyo/oxyo');
        $value = $this->model_extension_oxyo_oxyo->getSettingValue($key, $this->store_id);
        return $value;
    }
}
