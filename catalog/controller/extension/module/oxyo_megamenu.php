<?php
class ControllerExtensionModuleOxyoMegamenu extends Controller
{
    public function index($setting)
    {
        $this->load->model('extension/oxyo/oxyo_megamenu');

        $module_id = (isset($setting['moduleid']) && $setting['moduleid']) ? $setting['moduleid'] : 0;
        $data['menu'] = $this->model_extension_oxyo_oxyo_megamenu->getMenu($module_id, $mobile = false);

        $lang_id = $this->config->get('config_language_id');
        $data['lang_id'] = $this->config->get('config_language_id');

        $this->load->language('oxyo/oxyo');
        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
        $data['oxyo_button_quickview'] = $this->language->get('oxyo_button_quickview');
        $data['oxyo_text_sale'] = $this->language->get('oxyo_text_sale');
        $data['oxyo_text_new'] = $this->language->get('oxyo_text_new');
        $data['oxyo_text_days'] = $this->language->get('oxyo_text_days');
        $data['oxyo_text_hours'] = $this->language->get('oxyo_text_hours');
        $data['oxyo_text_mins'] = $this->language->get('oxyo_text_mins');
        $data['oxyo_text_secs'] = $this->language->get('oxyo_text_secs');
        $data['countdown_status'] = $this->config->get('countdown_status');
        $data['salebadge_status'] = $this->config->get('salebadge_status');

        $this->load->language('extension/module/category');
        $data['heading_title'] = $this->language->get('heading_title');

        if ($this->config->get('theme_default_directory') == 'oxyo')
            return $this->load->view('extension/module/oxyo_megamenu', $data);
    }
}
