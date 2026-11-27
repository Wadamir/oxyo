<?php
class ControllerExtensionModuleXdReview extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/xd_review');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['tab']) && $this->request->post['tab'] == 'reviews') {
			if ($this->validateReviews()) {
				if (empty($this->request->post['selected']) || !is_array($this->request->post['selected'])) {
					$this->error['warning'] = $this->language->get('error_no_reviews_selected');
				} elseif (!isset($this->request->post['action']) || !in_array($this->request->post['action'], array('approve', 'reject', 'delete'), true)) {
					$this->error['warning'] = $this->language->get('error_action_required');
				} else {
					$this->load->model('catalog/review');

					foreach ($this->request->post['selected'] as $review_id) {
						if ($this->request->post['action'] == 'approve') {
							$this->db->query("UPDATE `" . DB_PREFIX . "review` SET `status` = '1' WHERE `review_id` = '" . (int)$review_id . "'");
						} elseif ($this->request->post['action'] == 'reject') {
							$this->db->query("UPDATE `" . DB_PREFIX . "review` SET `status` = '0' WHERE `review_id` = '" . (int)$review_id . "'");
						} else {
							$this->model_catalog_review->deleteReview($review_id);
						}
					}

					$this->session->data['success'] = $this->language->get('text_success');

					$url = '';

					if (isset($this->request->get['filter_author'])) {
						$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
					}

					if (isset($this->request->get['filter_status'])) {
						$url .= '&filter_status=' . $this->request->get['filter_status'];
					}

					if (isset($this->request->get['sort'])) {
						$url .= '&sort=' . $this->request->get['sort'];
					}

					if (isset($this->request->get['order'])) {
						$url .= '&order=' . $this->request->get['order'];
					}

					if (isset($this->request->get['page'])) {
						$url .= '&page=' . $this->request->get['page'];
					}

					$this->response->redirect($this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'] . '&tab=reviews' . $url, true));
				}
			}
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && (!isset($this->request->post['tab']) || $this->request->post['tab'] != 'reviews') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_xd_review', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
		unset($this->session->data['success']);

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
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
			'href' => $this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['user_token'] = $this->session->data['user_token'];
		$data['active_tab'] = (isset($this->request->get['tab']) && $this->request->get['tab'] == 'reviews') || (isset($this->request->post['tab']) && $this->request->post['tab'] == 'reviews') ? 'reviews' : 'settings';
		$data['selected_reviews'] = isset($this->request->post['selected']) && is_array($this->request->post['selected']) ? $this->request->post['selected'] : array();
		$data['selected_action'] = isset($this->request->post['action']) && is_string($this->request->post['action']) ? $this->request->post['action'] : '';
		$data['header'] = $this->load->controller('common/header');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['tab_settings'] = $this->language->get('tab_settings');
		$data['tab_reviews'] = $this->language->get('tab_reviews');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_moderation'] = $this->language->get('entry_moderation');
		$data['entry_guest'] = $this->language->get('entry_guest');
		$data['entry_min_length'] = $this->language->get('entry_min_length');
		$data['entry_panel_title'] = $this->language->get('entry_panel_title');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_carousel'] = $this->language->get('entry_carousel');
		$data['help_min_length'] = $this->language->get('help_min_length');
		$data['help_widget_layout'] = $this->language->get('help_widget_layout');

		$fields = array('status', 'moderation', 'guest', 'min_length', 'panel_title', 'limit', 'carousel');
		foreach ($fields as $field) {
			$key = 'module_xd_review_' . $field;
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$defaults = array(
					'min_length' => 10,
					'panel_title' => '',
					'limit' => 5,
					'carousel' => 0,
					'status' => 1,
					'moderation' => 1,
					'guest' => 0
				);
				$data[$key] = ($value === null) ? $defaults[$field] : $value;
			}
		}

		$this->load->model('catalog/review');

		if (isset($this->request->get['filter_author'])) {
			$data['filter_author'] = $this->request->get['filter_author'];
		} else {
			$data['filter_author'] = '';
		}

		if (isset($this->request->get['filter_product']) && is_string($this->request->get['filter_product'])) {
			$data['filter_product'] = $this->request->get['filter_product'];
		} else {
			$data['filter_product'] = '';
		}

		if (isset($this->request->get['filter_rating']) && is_scalar($this->request->get['filter_rating']) && in_array((string)$this->request->get['filter_rating'], array('1', '2', '3', '4', '5'), true)) {
			$data['filter_rating'] = (int)$this->request->get['filter_rating'];
		} else {
			$data['filter_rating'] = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$data['filter_status'] = $this->request->get['filter_status'];
		} else {
			$data['filter_status'] = '';
		}

		if (isset($this->request->get['sort'])) {
			$data['sort'] = $this->request->get['sort'];
		} else {
			$data['sort'] = 'date_added';
		}

		if (isset($this->request->get['order'])) {
			$data['order'] = $this->request->get['order'];
		} else {
			$data['order'] = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$data['page'] = (int)$this->request->get['page'];
		} else {
			$data['page'] = 1;
		}

		if ($data['page'] < 1) {
			$data['page'] = 1;
		}

		$limit = (int)$this->config->get('config_limit_admin');
		if ($limit < 1) {
			$limit = 20;
		}

		$data['start'] = ($data['page'] - 1) * $limit;
		$data['limit'] = $limit;

		$data['reviews'] = array();

		$sql = "SELECT r.review_id, r.product_id, pd.name AS product_name, r.author, r.text, r.rating, r.status, r.date_added FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
		}

		if ($data['filter_rating'] !== '') {
			$sql .= " AND r.rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		$sort_data = array('r.review_id', 'r.product_id', 'pd.name', 'r.author', 'r.rating', 'r.status', 'r.date_added');

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$data['reviews'][] = array(
				'review_id'   => $result['review_id'],
				'product_id'  => $result['product_id'],
				'product_name'=> $result['product_name'] ? $result['product_name'] : $this->language->get('text_deleted'),
				'author'      => htmlspecialchars($result['author'], ENT_QUOTES, 'UTF-8'),
				'text'        => strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8')),
				'rating'      => $result['rating'],
				'status'      => $result['status'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'approve'     => $this->url->link('extension/module/xd_review/approve', 'user_token=' . $this->session->data['user_token'] . '&review_id=' . $result['review_id'], true),
				'reject'      => $this->url->link('extension/module/xd_review/reject', 'user_token=' . $this->session->data['user_token'] . '&review_id=' . $result['review_id'], true),
				'delete'      => $this->url->link('extension/module/xd_review/delete', 'user_token=' . $this->session->data['user_token'] . '&review_id=' . $result['review_id'], true)
			);
		}
		$data['column_left'] = $this->load->controller('common/column_left');

		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
		}

		if ($data['filter_rating'] !== '') {
			$sql .= " AND r.rating = '" . (int)$data['filter_rating'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		$review_total = $query->row['total'];

		$pagination = new Pagination($this->registry);
		$pagination->total = $review_total;
		$pagination->page = $data['page'];
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'] . '&filter_author=' . urlencode(html_entity_decode($data['filter_author'], ENT_QUOTES, 'UTF-8')) . '&filter_product=' . urlencode(html_entity_decode($data['filter_product'], ENT_QUOTES, 'UTF-8')) . '&filter_rating=' . $data['filter_rating'] . '&filter_status=' . $data['filter_status'] . '&sort=' . $data['sort'] . '&order=' . $data['order'] . '&page={page}&tab=reviews', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($data['page'] - 1) * $limit) + 1 : 0, ((($data['page'] - 1) * $limit) > ($review_total - $limit)) ? $review_total : (($data['page'] - 1) * $limit) + $limit, $review_total, ceil($review_total / $limit));

		$data['filter_status_options'] = array(
			array('', $this->language->get('text_all')),
			array('1', $this->language->get('text_approved')),
			array('0', $this->language->get('text_pending'))
		);
		$data['filter_rating_options'] = array(array('', $this->language->get('text_all')));
		for ($rating = 1; $rating <= 5; $rating++) {
			$data['filter_rating_options'][] = array((string)$rating, (string)$rating);
		}

		$data['text_list'] = $this->language->get('text_list');
		$data['text_all'] = $this->language->get('text_all');
		$data['text_approved'] = $this->language->get('text_approved');
		$data['text_pending'] = $this->language->get('text_pending');
		$data['text_deleted'] = $this->language->get('text_deleted');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_pagination'] = $this->language->get('text_pagination');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_filter_product'] = $this->language->get('entry_filter_product');
		$data['entry_filter_rating'] = $this->language->get('entry_filter_rating');
		$data['entry_filter_status'] = $this->language->get('entry_filter_status');
		$data['button_approve'] = $this->language->get('button_approve');
		$data['button_reject'] = $this->language->get('button_reject');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_action'] = $this->language->get('button_action');
		$data['column_product'] = $this->language->get('column_product');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_text'] = $this->language->get('column_text');
		$data['column_rating'] = $this->language->get('column_rating');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/xd_review', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/xd_review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$min_length = isset($this->request->post['module_xd_review_min_length']) ? (int)$this->request->post['module_xd_review_min_length'] : 0;
		if ($min_length < 1 || $min_length > 1000) {
			$this->error['warning'] = $this->language->get('error_min_length');
		}

		$limit = isset($this->request->post['module_xd_review_limit']) ? (int)$this->request->post['module_xd_review_limit'] : 0;
		if ($limit < 1 || $limit > 100) {
			$this->error['warning'] = $this->language->get('error_limit');
		}

		return !$this->error;
	}

	public function approve() {
		$this->load->language('extension/module/xd_review');

		if (isset($this->request->get['review_id']) && $this->user->hasPermission('modify', 'extension/module/xd_review')) {
			$this->db->query("UPDATE `" . DB_PREFIX . "review` SET `status` = '1' WHERE `review_id` = '" . (int)$this->request->get['review_id'] . "'");

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function reject() {
		$this->load->language('extension/module/xd_review');

		if (isset($this->request->get['review_id']) && $this->user->hasPermission('modify', 'extension/module/xd_review')) {
			$this->db->query("UPDATE `" . DB_PREFIX . "review` SET `status` = '0' WHERE `review_id` = '" . (int)$this->request->get['review_id'] . "'");

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function delete() {
		$this->load->language('extension/module/xd_review');

		if (isset($this->request->get['review_id']) && $this->user->hasPermission('modify', 'extension/module/xd_review')) {
			$this->load->model('catalog/review');
			$this->model_catalog_review->deleteReview($this->request->get['review_id']);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('extension/module/xd_review', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	protected function validateReviews() {
		if (!$this->user->hasPermission('modify', 'extension/module/xd_review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
