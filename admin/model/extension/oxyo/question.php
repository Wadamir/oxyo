<?php
class ModelExtensionOxyoQuestion extends Model
{
    public function addQuestion($data)
    {
        // $this->event->trigger('pre.admin.question.add', $data);

        $this->db->query("INSERT INTO " . DB_PREFIX . "question SET 
		author = '" . $this->db->escape($data['author']) . "',
		email = '" . $this->db->escape($data['email']) . "', 
		product_id = '" . (int)$data['product_id'] . "', 
		text = '" . $this->db->escape(strip_tags($data['text'])) . "', 
		answer = '" . $this->db->escape(strip_tags($data['answer'])) . "', 
		status = '" . (int)$data['status'] . "', 
		date_added = NOW()");

        $question_id = $this->db->getLastId();

        $this->cache->delete('product');

        // $this->event->trigger('post.admin.question.add', $question_id);

        return $question_id;
    }

    public function editQuestion($question_id, $data)
    {
        // $this->event->trigger('pre.admin.question.edit', $data);

        $this->db->query("UPDATE " . DB_PREFIX . "question SET 
		author = '" . $this->db->escape($data['author']) . "', 
		email = '" . $this->db->escape($data['email']) . "', 
		product_id = '" . (int)$data['product_id'] . "', 
		text = '" . $this->db->escape(strip_tags($data['text'])) . "', 
		answer = '" . $this->db->escape(strip_tags($data['answer'])) . "', 
		status = '" . (int)$data['status'] . "', 
		notify = '" . (int)$data['notify'] . "', 
		date_modified = NOW() WHERE question_id = '" . (int)$question_id . "'");

        $this->cache->delete('product');

        // $this->event->trigger('post.admin.question.edit', $question_id);


        if ($data['notify']) {
            $this->load->language('oxyo/question');

            $message  = $this->language->get('text_intro') . "\n";
            $message .= $this->db->escape(strip_tags($data['answer'])) . "\n\n";
            $message .= $this->language->get('text_further_questions') . "\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            if ((float)VERSION >= 3.0) {
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_host');
            } else {
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            }
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->db->escape($data['email']));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject($this->language->get('text_subject'));
            $mail->setText($message);
            $mail->send();
        }
    }

    public function deleteQuestion($question_id)
    {
        // $this->event->trigger('pre.admin.question.delete', $question_id);

        $this->db->query("DELETE FROM " . DB_PREFIX . "question WHERE question_id = '" . (int)$question_id . "'");

        $this->cache->delete('product');

        // $this->event->trigger('post.admin.question.delete', $question_id);
    }

    public function getQuestion($question_id)
    {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "product_description pd WHERE pd.product_id = r.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS product FROM " . DB_PREFIX . "question r WHERE r.question_id = '" . (int)$question_id . "'");

        return $query->row;
    }

    public function getQuestions($data = array())
    {
        $sql = "SELECT r.question_id, pd.name, r.author, r.email, r.answer, r.text, r.status, r.date_added FROM " . DB_PREFIX . "question r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
        }

        if (!empty($data['filter_author'])) {
            $sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        $sort_data = array(
            'pd.name',
            'r.author',
            'r.status',
            'r.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY r.date_added";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
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

        return $query->rows;
    }

    public function getTotalQuestions($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "question r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
        }

        if (!empty($data['filter_author'])) {
            $sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
        }

        if (!empty($data['filter_status'])) {
            $sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalQuestionsAwaitingApproval()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "question WHERE status = '0'");

        return $query->row['total'];
    }
}
