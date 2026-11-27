<?php
class ControllerExtensionModuleXdReview extends Controller {
	public function index($setting = array()) {
		$setting = is_array($setting) ? $setting : array();

		if (!$this->isReviewEnabled()) {
			return '';
		}

		if ($this->config->get('module_xd_review_carousel')) {
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
			$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.min.js');
		}

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		$this->load->language('extension/module/xd_review');
		$this->load->model('extension/module/xd_review');

		if ($product_id) {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			if (!$product_info) {
				return '';
			}

			$default_title = sprintf($this->language->get('heading_title'), htmlspecialchars($product_info['name'], ENT_QUOTES, 'UTF-8'));
		} else {
			$default_title = $this->language->get('text_all_reviews');
		}

		$panel_title = trim((string)$this->config->get('module_xd_review_panel_title'));
		$data['heading_title'] = $panel_title !== '' ? htmlspecialchars($panel_title, ENT_QUOTES, 'UTF-8') : $default_title;

		$data['error_request'] = $this->language->get('error_request');
		$data['review_list'] = $this->load->view('extension/module/xd_review_list', $this->getReviewListData($product_id, 1));
		return $this->load->view('extension/module/xd_review', $data);
	}

	public function reviews() {
		$this->load->language('extension/module/xd_review');

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		if (!$this->isReviewEnabled()) {
			$this->response->setOutput('');
			return;
		}

		$this->load->model('extension/module/xd_review');

		if ($product_id) {
			$this->load->model('catalog/product');

			if (!$this->model_catalog_product->getProduct($product_id)) {
				$this->response->setOutput('');
				return;
			}
		}

		$page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
		$this->response->setOutput($this->load->view('extension/module/xd_review_list', $this->getReviewListData($product_id, $page)));
	}

	private function getReviewListData($product_id, $page) {
		$carousel = (bool)$this->config->get('module_xd_review_carousel');
		$limit = (int)$this->config->get('module_xd_review_limit');
		$limit = ($limit >= 1 && $limit <= 100) ? $limit : 5;
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$store_logo = $this->config->get('config_logo');
		$store_logo = $store_logo && is_file(DIR_IMAGE . $store_logo)
			? $this->model_tool_image->resize($store_logo, 80, 80)
			: '';

		if ($product_id) {
			$total = $this->model_extension_module_xd_review->getTotalReviewsByProductId($product_id);
			$results = $carousel
				? $this->model_extension_module_xd_review->getReviewsByProductId($product_id, 0, 0)
				: $this->model_extension_module_xd_review->getReviewsByProductId($product_id, ($page - 1) * $limit, $limit);
			$review_url = $this->url->link('extension/module/xd_review/reviews', 'product_id=' . (int)$product_id . '&page={page}');
		} else {
			$total = $this->model_extension_module_xd_review->getTotalReviews();
			$results = $carousel
				? $this->model_extension_module_xd_review->getReviews(0, 0)
				: $this->model_extension_module_xd_review->getReviews(($page - 1) * $limit, $limit);
			$review_url = $this->url->link('extension/module/xd_review/reviews', 'page={page}');
		}
		$reviews = array();

		foreach ($results as $index => $result) {
			$plain_text = trim(html_entity_decode(strip_tags($result['text']), ENT_QUOTES, 'UTF-8'));
			$excerpt = $this->truncateReview($plain_text, 280);
			$product_info = $this->model_catalog_product->getProduct((int)$result['product_id']);
			$product_images = array();

			if ($product_info) {
				if (!empty($product_info['image']) && is_file(DIR_IMAGE . $product_info['image'])) {
					$product_images[] = $this->model_tool_image->resize($product_info['image'], 80, 80);
				}

				$additional_images = $this->model_catalog_product->getProductImages((int)$result['product_id']);
				foreach ($additional_images as $image) {
					if (count($product_images) >= 2) {
						break;
					}
					if (!empty($image['image']) && is_file(DIR_IMAGE . $image['image'])) {
						$product_images[] = $this->model_tool_image->resize($image['image'], 80, 80);
					}
				}
			}

			while (count($product_images) < 2) {
				$product_images[] = $store_logo;
			}

			$reviews[] = array(
				'id' => 'xd-review-modal-' . (int)$result['review_id'] . '-' . (int)$index,
				'author' => htmlspecialchars($result['author'], ENT_QUOTES, 'UTF-8'),
				'author_initial' => mb_strtoupper(mb_substr(trim($result['author']), 0, 1, 'UTF-8'), 'UTF-8'),
				'text' => nl2br(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8')),
				'full_text' => nl2br(htmlspecialchars($plain_text, ENT_QUOTES, 'UTF-8')),
				'rating' => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'product_url' => $product_info ? $this->url->link('product/product', 'product_id=' . (int)$result['product_id'], true) : '',
				'product_images' => $product_images
			);
		}

		$pagination = new Pagination();
		$pagination->total = $carousel ? 0 : $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $review_url;

		return array(
			'reviews' => $reviews,
			'pagination' => $pagination->render(),
			'carousel' => $carousel,
			'text_no_reviews' => $this->language->get('text_no_reviews')
		);
	}

	private function isReviewEnabled() {
		$status = $this->config->get('module_xd_review_status');

		return $status === null ? true : (bool)$status;
	}

	private function truncateReview($text, $length) {
		$text = preg_replace('/\s+/u', ' ', trim($text));
		if (mb_strlen($text, 'UTF-8') <= $length) {
			return $text;
		}

		$text = mb_substr($text, 0, $length, 'UTF-8');
		$sentence_end = max(mb_strrpos($text, '.', 0, 'UTF-8'), mb_strrpos($text, '!', 0, 'UTF-8'), mb_strrpos($text, '?', 0, 'UTF-8'));
		if ($sentence_end !== false && $sentence_end > $length * 0.45) {
			return rtrim(mb_substr($text, 0, $sentence_end + 1, 'UTF-8'), ' .!?') . '...';
		}

		$space = mb_strrpos($text, ' ', 0, 'UTF-8');
		if ($space !== false) {
			$text = mb_substr($text, 0, $space, 'UTF-8');
		}

		return rtrim($text, ' .,!?') . '...';
	}

}
