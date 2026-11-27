<?php
class ModelExtensionModuleXdReview extends Model {
	public function getReviews($start = 0, $limit = 5) {
		$start = max(0, (int)$start);
		$limit = (int)$limit;
		$limit = $limit > 0 ? max(1, $limit) : 0;

		$sql = "SELECT `review_id`, `product_id`, `author`, `text`, `rating`, `date_added` FROM `" . DB_PREFIX . "review` WHERE `status` = '1' ORDER BY `date_added` DESC";
		if ($limit > 0) {
			$sql .= " LIMIT " . $start . "," . $limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviews() {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` WHERE `status` = '1'");

		return (int)$query->row['total'];
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 5) {
		$start = max(0, (int)$start);
		$limit = (int)$limit;
		$limit = $limit > 0 ? max(1, $limit) : 0;

		$sql = "SELECT `review_id`, `product_id`, `author`, `text`, `rating`, `date_added` FROM `" . DB_PREFIX . "review` WHERE `product_id` = '" . (int)$product_id . "' AND `status` = '1' ORDER BY `date_added` DESC";
		if ($limit > 0) {
			$sql .= " LIMIT " . $start . "," . $limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` WHERE `product_id` = '" . (int)$product_id . "' AND `status` = '1'");

		return (int)$query->row['total'];
	}

	public function addReview($product_id, $data, $moderation) {
		$status = $moderation ? 0 : 1;

		$this->db->query("INSERT INTO `" . DB_PREFIX . "review` SET `author` = '" . $this->db->escape($data['name']) . "', `customer_id` = '" . (int)$this->customer->getId() . "', `product_id` = '" . (int)$product_id . "', `text` = '" . $this->db->escape($data['text']) . "', `rating` = '" . (int)$data['rating'] . "', `status` = '" . $status . "', `date_added` = NOW()");

		if (!in_array('review', (array)$this->config->get('config_mail_alert'))) {
			return;
		}

		$this->load->language('mail/review');
		$this->load->language('extension/module/xd_review');
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (!$product_info) {
			return;
		}

		$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$message = $this->language->get($status ? 'text_email_published' : 'text_waiting') . "\n";
		$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
		$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
		$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
		$message .= $this->language->get('text_review') . "\n";
		$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		$mail->setTo($this->config->get('config_email'));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();

		$emails = explode(',', $this->config->get('config_mail_alert_email'));

		foreach ($emails as $email) {
			if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$mail->setTo($email);
				$mail->send();
			}
		}
	}
}
