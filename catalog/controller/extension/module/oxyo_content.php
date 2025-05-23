<?php
class ControllerExtensionModuleOxyoContent extends Controller
{
    public function index($setting)
    {
        static $module = 1;

        $data['module'] = $module;

        $this->load->language('oxyo/oxyo_theme');

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        // Language variables
        $data['oxyo_subscribe_email'] = $this->language->get('oxyo_subscribe_email');
        $data['oxyo_subscribe_btn'] = $this->language->get('oxyo_subscribe_btn');
        $data['oxyo_unsubscribe_btn'] = $this->language->get('oxyo_unsubscribe_btn');
        $data['oxyo_text_name'] = $this->language->get('oxyo_text_name');
        $data['oxyo_text_email'] = $this->language->get('oxyo_text_email');
        $data['oxyo_text_message'] = $this->language->get('oxyo_text_message');
        $data['oxyo_text_captcha'] = $this->language->get('oxyo_text_captcha');
        $data['oxyo_text_submit'] = $this->language->get('oxyo_text_submit');

        // Google map widget
        $data['oxyo_map_lat'] = $this->config->get('oxyo_map_lat');
        $data['oxyo_map_lon'] = $this->config->get('oxyo_map_lon');

        // Video background
        if ($setting['b_setting']['block_bgv']) {
            $this->document->addScript('catalog/view/theme/oxyo/js/jquery.tuber.js');
        }

        // Equal Height
        if ($setting['c_setting']['eh']) {
            $this->document->addScript('catalog/view/theme/oxyo/js/jquery.matchHeight.min.js');
        }

        // Carousel
        if (isset($setting['c_setting']['carousel'])) {
            $data['carousel'] = $setting['c_setting']['carousel'];
        } else {
            $data['carousel'] = false;
        }

        // RTL support
        $data['direction'] = $this->language->get('direction');

        // Block
        $data['block_id'] = (isset($setting['b_setting']['id']) && $setting['b_setting']['id']) ? $setting['b_setting']['id'] : 'oxyo_block_' . $module;
        // var_dump($data['block_id']);
        $data['block_title'] = $setting['b_setting']['title'];
        $data['title_preline'] = false;
        $data['title'] = false;
        $data['title_subline'] = false;

        if (!empty($setting['b_setting']['title_pl'][$this->config->get('config_language_id')])) {
            $data['title_preline'] = html_entity_decode($setting['b_setting']['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($setting['b_setting']['title_m'][$this->config->get('config_language_id')])) {
            $data['title'] = html_entity_decode($setting['b_setting']['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($setting['b_setting']['title_b'][$this->config->get('config_language_id')])) {
            $data['title_subline'] = html_entity_decode($setting['b_setting']['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }

        $data['module_margins'] = '';

        if ($setting['b_setting']['custom_m']) {
            $data['module_margins'] .= "margin-top:" . $setting['b_setting']['mt'] . "px;";
            $data['module_margins'] .= "margin-right:" . $setting['b_setting']['mr'] . "px;";
            $data['module_margins'] .= "margin-bottom:" . $setting['b_setting']['mb'] . "px;";
            $data['module_margins'] .= "margin-left:" . $setting['b_setting']['ml'] . "px;";
        }

        $data['block_style'] = '';

        if ($setting['b_setting']['block_bg']) {
            $data['block_style'] .= "background-color:" . $setting['b_setting']['bg_color'] . ";";
        }

        if ($setting['b_setting']['block_css']) {
            $data['block_style'] .= $setting['b_setting']['css'];
        }

        if ($setting['b_setting']['block_bgi']) {
            $data['block_style'] .= "background-image: url(" . $server . 'image/' . $setting['bg_image'] . ");";
            $data['block_style'] .= "background-position:" . $setting['b_setting']['bg_pos'] . ";";
            $data['block_style'] .= "background-repeat:" . $setting['b_setting']['bg_repeat'] . ";";
            if (isset($setting['b_setting']['bg_size'])) {
                $data['block_style'] .= "background-size:" . $setting['b_setting']['bg_size'] . ";";
            } else {
                $data['block_style'] .= "background-size:auto;";
            }
            if ($setting['b_setting']['bg_par']) {
                $data['block_style'] .= "background-attachment:fixed;";
                $this->document->addScript('catalog/view/theme/oxyo/js/parallax.min.js');
            }
        }

        $data['para_status'] = $setting['b_setting']['bg_par'];
        $data['block_full_width'] = $setting['b_setting']['fw'];

        // Content
        $data['content_style'] = '';
        if ($setting['c_setting']['block_css']) {
            $data['content_style'] .= $setting['c_setting']['css'];
        }

        $data['content_full_width'] = $setting['c_setting']['fw'];
        $data['content_no_margin'] = $setting['c_setting']['nm'];
        $data['equal_height'] = $setting['c_setting']['eh'];
        $data['bg_video'] = $setting['b_setting']['block_bgv'];
        $data['video_id'] = $setting['b_setting']['bg_video'];
        $data['bg_img'] = $setting['b_setting']['block_bgi'];

        // Columns
        if (isset($setting['columns'])) {

            $data['columns'] = $setting['columns'];
            $data['columns'] = array();

            foreach ($setting['columns'] as $column) {
                $this->load->model('tool/image');

                if ($column['type'] != "testimonial") {

                    $this->load->model('extension/oxyo/testimonial');
                    $results = $this->model_extension_oxyo_testimonial->getTestimonials($column['data1']);
                    foreach ($results as $result) {

                        if ($result['image']) {
                            $image = $server . 'image/' . $result['image'];
                        } else {
                            $image = '';
                        }

                        $data['testimonials'][] = array(
                            'description'    => $result['description'],
                            'name'        => $result['name'],
                            'image'        => $image,
                            'org'        => $result['org']
                        );
                    }
                }

                if ($column['w'] == "custom") {
                    // $column_class = $column['w_sm'] . ' ' . $column['w_md'] . ' ' . $column['w_lg'] . ' ' . $column['w_xl'] . ' ' . $column['w_xxl'];
                    $column_class = '';
                    if (isset($column['w_sm']) && $column['w_sm'] != '') {
                        $column_class .= $column['w_sm'] . ' ';
                    }
                    if (isset($column['w_md']) && $column['w_md'] != '') {
                        if ($column['w_sm'] == 'd-sm-none' && $column['w_md'] !== 'd-md-none') {
                            $column_class .= 'd-md-block ' . $column['w_md'] . ' ';
                        } else {
                            $column_class .= $column['w_md'] . ' ';
                        }
                    }
                    if (isset($column['w_lg']) && $column['w_lg'] != '') {
                        if ($column['w_md'] == 'd-md-none' && $column['w_lg'] !== 'd-lg-none') {
                            $column_class .= 'd-lg-block ' . $column['w_lg'] . ' ';
                        } else {
                            $column_class .= $column['w_lg'] . ' ';
                        }
                    }
                    if (isset($column['w_xl']) && $column['w_xl'] != '') {
                        if ($column['w_lg'] == 'd-lg-none' && $column['w_xl'] !== 'd-xl-none') {
                            $column_class .= 'd-xl-block ' . $column['w_xl'] . ' ';
                        } else {
                            $column_class .= $column['w_xl'] . ' ';
                        }
                    }
                    if (isset($column['w_xxl']) && $column['w_xxl'] != '') {
                        if ($column['w_xl'] == 'd-xl-none' && $column['w_xxl'] !== 'd-xxl-none') {
                            $column_class .= 'd-xxl-block ' . $column['w_xxl'] . ' ';
                        } else {
                            $column_class .= $column['w_xxl'] . ' ';
                        }
                    }
                    $column_class = trim($column_class);
                } else {
                    $column_class = $column['w'];
                }

                if (isset($column['data1'][$this->config->get('config_language_id')])) {

                    if (strpos($column['data1'][$this->config->get('config_language_id')], '[map]') !== false) {
                        $this->document->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.24&key=' . $this->config->get('oxyo_map_api') . '');
                        $this->document->addScript('catalog/view/theme/oxyo/js/maplace.min.js');
                    }

                    $find = array(
                        '[subscribe_field]',
                        '[unsubscribe_btn]',
                        '[contact_form]',
                        '[map]'
                    );

                    $replace = array(
                        $this->load->view('extension/module/content_widgets/subscribe_field', $data),
                        $this->load->view('extension/module/content_widgets/unsubscribe_btn', $data),
                        $this->load->view('extension/module/content_widgets/contact_form', $data),
                        $this->load->view('extension/module/content_widgets/map', $data)
                    );

                    $data1 = html_entity_decode(str_replace($find, $replace, $column['data1'][$this->config->get('config_language_id')]), ENT_QUOTES, 'UTF-8');
                } else {
                    $data1 = false;
                }

                if (isset($column['data2'])) {
                    $data2 = $server . 'image/' . $column['data2'];
                } else {
                    $data2 = false;
                }

                if (isset($column['data3'][$this->config->get('config_language_id')])) {
                    $data3 = html_entity_decode($column['data3'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
                } else {
                    $data3 = false;
                }

                if (isset($column['data4'])) {
                    $data4 = $server . 'image/' . $column['data4'];
                } else {
                    $data4 = false;
                }

                if (isset($column['data5'])) {
                    $data5 = $column['data5'];
                } else {
                    $data5 = false;
                }

                if (isset($column['data6'])) {
                    $data6 = $column['data6'];
                } else {
                    $data6 = false;
                }

                if (isset($column['data7'])) {
                    $data7 = $column['data7'];
                } else {
                    $data7 = false;
                }

                if (isset($column['data8'])) {
                    $data8 = $column['data8'];
                } else {
                    $data8 = false;
                }

                $data['columns'][] = array(
                    'column_class' => $column_class,
                    'type' => $column['type'],
                    'data1' => $data1,
                    'data2' => $data2,
                    'data3' => $data3,
                    'data4' => $data4,
                    'data5' => $data5,
                    'data6' => $data6,
                    'data7' => $data7,
                    'data8' => $data8
                );
            }
        }

        $data['module'] = $module++;

        if ($this->config->get('theme_default_directory') == 'oxyo')
            return $this->load->view('extension/module/oxyo_content', $data);
    }
}
