<?php

$data['full_width_tabs'] = $this->config->get('full_width_tabs');
$data['full_width_tabs'] = 1; // Force full width tabs for OXYO theme
$data['product_tabs_style'] = $this->config->get('product_tabs_style');
$data['img_w'] = $this->config->get('theme_default_image_thumb_width');
$data['img_h'] = $this->config->get('theme_default_image_thumb_height');
$data['img_a_w'] = $this->config->get('theme_default_image_additional_width');
$data['img_a_h'] = $this->config->get('theme_default_image_additional_height');
$data['meta_description_status'] = $this->config->get('meta_description_status');
$data['product_page_countdown'] = $this->config->get('product_page_countdown');
$data['meta_description'] = $product_info['meta_description'];
$data['product_layout'] = $this->config->get('product_layout');
$data['qty'] = $product_info['quantity'];
$data['oxyo_price_update'] = $this->config->get('oxyo_price_update');
$data['oxyo_sharing_style'] = $this->config->get('oxyo_sharing_style');
$data['review_qty'] = $product_info['reviews'];
$data['currency_code'] = $this->session->data['currency'];
$data['button_reviews'] = $this->language->get('button_reviews');
$data['oxyo_text_share'] = $this->language->get('oxyo_text_share');
$data['currency_code'] = $this->session->data['currency'];
$data['hover_zoom'] = $this->config->get('oxyo_hover_zoom');
$data['current_href'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);
$this->document->addLink($data['thumb'], 'image');
$data['oxyo_share_btn'] = $this->config->get('oxyo_share_btn');
$data['oxyo_rel_prod_grid'] = $this->config->get('oxyo_rel_prod_grid');
$data['items_mobile_fw'] = $this->config->get('items_mobile_fw');
if (strtotime($product_info['date_available']) > strtotime('-' . $this->config->get('newlabel_status') . ' day')) $data['is_new'] = true;
$data['oxyo_text_offer_ends'] = $this->language->get('oxyo_text_offer_ends');
$price_snippet = preg_replace("/[^0-9,.]/", "", $data['price']);
$data['price_snippet'] = str_replace(',', '.', $price_snippet);
if ((float)$product_info['special']) {
    $date_end = $this->model_extension_oxyo_oxyo->getSpecialEndDate($product_info['product_id']);
    $data['sale_end_date'] = ($date_end) ? $date_end['date_end'] : false;
    $special_snippet = preg_replace("/[^0-9,.]/", "", $data['special']);
    $data['special_snippet'] = str_replace(',', '.', $special_snippet);
}
if ($this->config->get('product_layout') == 'full-width') $this->document->addScript('catalog/view/theme/oxyo/js/theia-sticky-sidebar.min.js');

if ((float)$product_info['special'] && ($this->config->get('salebadge_status'))) {
    if ($this->config->get('salebadge_status') == '2') {
        $data['sale_badge'] = '-' . number_format(((($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')))) / (($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) / 100)), 0, ',', '.') . '%';
    } else {
        $data['sale_badge'] = $this->language->get('oxyo_text_sale');
    }
} else {
    $data['sale_badge'] = false;
}

$current_language_id = $this->config->get('config_language_id');
$data['sale_badge'] = false;
if ((float)$product_info['special'] && ($this->config->get('sticker_sale'))) {
    $sticker_sale = $this->config->get('sticker_sale');
    if (isset($sticker_sale['status']) && $sticker_sale['status'] == 1) {
        if ($sticker_sale['discount_status'] == 1) {
            $data['sale_badge'] = '-' . number_format(((($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')))) / (($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) / 100)), 0, ',', '.') . '%';
        } else {
            $data['sale_badge'] = $sticker_sale['text'][$current_language_id];
        }
    }
}
$data['sale_badge'] = 'SALE!';

// RTL support
$data['direction'] = $this->language->get('direction');

// Product Questions and Answers
$data['question_status'] = $this->config->get('product_question_status');
$data['product_questions'] = $this->load->controller('extension/oxyo/question');
$data['oxyo_tab_questions'] = $this->language->get('oxyo_tab_questions');
$data['oxyo_button_ask'] = $this->language->get('oxyo_button_ask');
$this->load->model('extension/oxyo/question');
$data['questions_total'] = $this->model_extension_oxyo_question->getTotalQuestionsByProductId($this->request->get['product_id']);

// Product Tabs
$this->load->model('extension/oxyo/product_tabs');
$data['product_tabs'] = $this->model_extension_oxyo_product_tabs->getExtraTabsProduct($this->request->get['product_id']);

// SEO Reviews
$data['text_no_reviews'] = $this->language->get('text_no_reviews');
$this->load->model('catalog/review');
if (isset($this->request->get['page'])) {
    $page = $this->request->get['page'];
} else {
    $page = 1;
}
$data['seo_reviews'] = array();
$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);
foreach ($results as $result) {
    $data['seo_reviews'][] = array(
        'author'     => $result['author'],
        'text'       => nl2br($result['text']),
        'rating'     => (int)$result['rating'],
        'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
    );
}
$pagination = new Pagination();
$pagination->total = $review_total;
$pagination->page = $page;
$pagination->limit = 5;
$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');
$data['pagination'] = $pagination->render();
$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));
