<?php
class ControllerExtensionModuleOxyoSorting extends Controller
{
    public function index()
    {
        $this->load->language('extension/module/oxyo_sorting');

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['sorts'] = array();

        // $data['sorts'][] = array(
        //     'text'  => $this->language->get('text_default'),
        //     'value' => 'p.sort_order-ASC',
        //     'href'  =>  . '&sort=p.sort_order&order=ASC' . $url)
        // );

        $link = 'product/category';
        $path = isset($this->request->get['path']) ? 'path=' . $this->request->get['path'] : '';
        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/special') {
            $link = 'product/special';
            $path = '';
        }

        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/special') {
            $link = 'product/special';
            $path = '';
        }
        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/search') {
            $link = 'product/search';
            $path = '';
        }
        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/manufacturer') {
            $link = 'product/manufacturer';
            $path = '';
        }
        if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/manufacturer/info') {
            $link = 'product/manufacturer/info';
            $path = isset($this->request->get['manufacturer_id']) ? 'manufacturer_id=' . $this->request->get['manufacturer_id'] : '';
        }

        $data['sorts'][] = array(
            'text'  => $this->language->get('text_name_asc'),
            'value' => 'pd.name-ASC',
            'href'  => $this->url->link($link, $path . '&sort=pd.name&order=ASC' . $url),
            'active' => (isset($this->request->get['sort']) && $this->request->get['sort'] == 'pd.name' && isset($this->request->get['order']) && $this->request->get['order'] == 'ASC') ? true : false
            // 'active' => false
        );

        // $data['sorts'][] = array(
        //     'text'  => $this->language->get('text_name_desc'),
        //     'value' => 'pd.name-DESC',
        //     'href'  => $this->url->link($link, $path . '&sort=pd.name&order=DESC' . $url)
        // );

        $data['sorts'][] = array(
            'text'  => $this->language->get('text_price_asc'),
            'value' => 'p.price-ASC',
            'href'  => $this->url->link($link, $path . '&sort=p.price&order=ASC' . $url),
            'active' => (isset($this->request->get['sort']) && $this->request->get['sort'] == 'p.price' && isset($this->request->get['order']) && $this->request->get['order'] == 'ASC') ? true : false
        );

        $data['sorts'][] = array(
            'text'  => $this->language->get('text_price_desc'),
            'value' => 'p.price-DESC',
            'href'  => $this->url->link($link, $path . '&sort=p.price&order=DESC' . $url),
            'active' => (isset($this->request->get['sort']) && $this->request->get['sort'] == 'p.price' && isset($this->request->get['order']) && $this->request->get['order'] == 'DESC') ? true : false
        );

        if ($this->config->get('config_review_status')) {
            $data['sorts'][] = array(
                'text'  => $this->language->get('text_rating_desc'),
                'value' => 'rating-DESC',
                'href'  => $this->url->link($link, $path . '&sort=rating&order=DESC' . $url),
                'active' => (isset($this->request->get['sort']) && $this->request->get['sort'] == 'rating' && isset($this->request->get['order']) && $this->request->get['order'] == 'DESC') ? true : false
            );

            // $data['sorts'][] = array(
            //     'text'  => $this->language->get('text_rating_asc'),
            //     'value' => 'rating-ASC',
            //     'href'  => $this->url->link($link, $path . '&sort=rating&order=ASC' . $url)
            // );
        }

        $data['sorts'][] = array(
            'text'  => $this->language->get('text_sale'),
            'value' => 'p.sale',
            'href'  => $this->url->link($link, $path . '&sort=p.sale' . $url),
            'active' => (isset($this->request->get['sort']) && $this->request->get['sort'] == 'p.sale') ? true : false
        );

        // $data['sorts'][] = array(
        //     'text'  => $this->language->get('text_model_asc'),
        //     'value' => 'p.model-ASC',
        //     'href'  => $this->url->link($link, $path . '&sort=p.model&order=ASC' . $url)
        // );

        // $data['sorts'][] = array(
        //     'text'  => $this->language->get('text_model_desc'),
        //     'value' => 'p.model-DESC',
        //     'href'  => $this->url->link($link, $path . '&sort=p.model&order=DESC' . $url)
        // );
        $data['active_sort'] = $this->language->get('text_name_asc');
        foreach ($data['sorts'] as $sort) {
            if ($sort['active']) {
                $data['active_sort'] = $sort['text'];
            }
        }

        return $this->load->view('extension/module/oxyo_sorting', $data);
    }
}
