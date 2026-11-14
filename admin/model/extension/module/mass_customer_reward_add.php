<?php
/*
* v1.4.2 2026-09-22
*/

class ModelExtensionModuleMassCustomerRewardAdd extends Model {

  public function install() {
    $this->createTables();
  }

  public function updateVersion() {
    $sql = "SHOW TABLES LIKE '" . DB_PREFIX . "mass_customer_reward_newsletter'";

    $query = $this->db->query($sql);

    if (!$query->num_rows) {
      $this->createTables();

      $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "mass_customer_reward_mail");
    }

    $sql = "SELECT COLUMN_NAME, COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "customer_reward' AND COLUMN_NAME = 'description'";

    $query = $this->db->query($sql);

    if ($query->num_rows && mb_strtolower($query->row['COLLATION_NAME']) != 'utf8mb4_unicode_ci') {
      $sql = "ALTER TABLE " . DB_PREFIX . "customer_reward MODIFY COLUMN `description` TEXT COLLATE utf8mb4_unicode_ci";

      $this->db->query($sql);
    }

  }

  public function uninstall() {
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mass_customer_reward_newsletter`");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mass_customer_reward_template`");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mass_customer_reward_template_language`");
  }

  private function createTables() {
    $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mass_customer_reward_newsletter` (
	            `newsletter_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `customer_id` INT(11) NULL DEFAULT NULL,
	            `template_id` INT(11) NULL DEFAULT NULL,
              `sent` TINYINT(1) NOT NULL DEFAULT 0,
              `date_time_send` DATETIME DEFAULT NULL,
              `error` VARCHAR(254) DEFAULT NULL,
	            PRIMARY KEY (`newsletter_id`),
              INDEX idx_sent (`sent`))
              COLLATE = 'utf8mb4_unicode_ci';";

    $this->db->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mass_customer_reward_template` (
	            `template_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `store_id` INT(11) NULL DEFAULT NULL,
	            `reward` DECIMAL(15,4)	 NULL DEFAULT NULL,
	            `date_valid` DATE NULL DEFAULT NULL,
              `date_added` DATETIME NOT NULL DEFAULT 0,
	            PRIMARY KEY (`template_id`))
              COLLATE = 'utf8mb4_unicode_ci';";

    $this->db->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mass_customer_reward_template_language` (
	            `template_id` INT(11) NOT NULL,
	            `language_id` INT(11) NOT NULL,
	            `description` VARCHAR(255) NOT NULL,
	            `subject` VARCHAR(255) NOT NULL,
              `body` TEXT NOT NULL,
	            INDEX idx_template_id (`template_id`))
              COLLATE = 'utf8mb4_unicode_ci';";

    $this->db->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sv_mail_template` (
	            `mail_template_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `module` CHAR(18) NOT NULL,
	            `name` VARCHAR(128) NOT NULL,
              PRIMARY KEY (`mail_template_id`),
	            INDEX idx_module (`module`))
              COLLATE = 'utf8mb4_general_ci';";

    $this->db->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sv_mail_template_language` (
	            `mail_template_language_id` INT(11) NOT NULL AUTO_INCREMENT,
              `mail_template_id` INT(11) NOT NULL,
              `language_id` INT(11) NOT NULL,
	            `subject` VARCHAR(255) NOT NULL,
              `body` TEXT NOT NULL,
              PRIMARY KEY (`mail_template_language_id`),
	            INDEX idx_mail_template_id (`mail_template_id`),
              INDEX idx_language_id (`language_id`))
              COLLATE = 'utf8mb4_unicode_ci';";

    $this->db->query($sql);

    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sv_mail_template WHERE `module` = 'mass_reward'";

    $query = $this->db->query($sql);

    if (!$query->row['total']) {
      $this->load->model('localisation/language');
      $languages = $this->model_localisation_language->getLanguages();

      $default = DIR_APPLICATION . 'controller/extension/module/mass_customer_sv_en-gb.txt';

      $sql = "INSERT INTO " . DB_PREFIX . "sv_mail_template SET `module` = 'mass_reward', `name` = 'Default'";
      $this->db->query($sql);

      $mail_template_id = $this->db->getLastId();

      foreach ($languages as $language) {
        $file_name = DIR_APPLICATION . 'controller/extension/module/mass_customer_sv_' . $language['code'] . '.txt';

        if (file_exists($file_name)) {
          $body = file_get_contents($file_name);
        } else {
          $body = file_get_contents($default);
        }

        switch ($language['code']) {
          case 'ru-ru' : $subject = 'Вам начислены баллы'; break;
          default: $subject = 'Bonus points for you';
        }

        $sql = "INSERT INTO " . DB_PREFIX . "sv_mail_template_language SET mail_template_id = " . (int)$mail_template_id . ", language_id = " . (int)$language['language_id'] . ", subject = '" . $this->db->escape($subject) . "', body = '" . $this->db->escape($body) . "'";

        $this->db->query($sql);
      }

      //unlink($default);
      //unlink(DIR_APPLICATION . 'controller/extension/module/mass_customer_sv_ru-ru.txt');
    }
  }


	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT IFNULL(SUM(balance), 0) FROM " . DB_PREFIX . "customer_reward WHERE customer_id = " . (int)$customer_id . " AND balance > 0 AND (DATE(date_valid) >= DATE(NOW()) OR date_valid = '0000-00-00')) AS reward FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomers($data = array()) {
    $invert = (int)$data['filter_inversion'];
  
		$sql = "SELECT c.*, 
        CONCAT(c.firstname, ' ', c.lastname) AS `name`, 
        cgd.name AS `customer_group`, 
        IFNULL(SUM(cr.balance), 0) AS reward, 
        IFNULL(COUNT(o.order_id), 0) AS order_count,
        IFNULL(SUM(o.total), 0) AS orders_total
      FROM `" . DB_PREFIX . "customer` c 
      LEFT JOIN " . DB_PREFIX . "customer_group_description cgd
        ON c.customer_group_id  = cgd.customer_group_id 
        AND cgd.language_id = " . (int)$this->config->get('config_language_id') . "
      LEFT JOIN " . DB_PREFIX . "customer_reward cr 
        ON c.customer_id = cr.customer_id 
        AND cr.balance > 0
        AND (cr.date_valid >= CURDATE() OR cr.date_valid = '0000-00-00')
      LEFT JOIN `" . DB_PREFIX . "order` o
        ON c.customer_id = o.customer_id";
    
    if (empty($data['filter_order_status_id'])) {
      $sql .= " AND o.order_status_id > 0";
    } else {
      if (!$invert) {
        $sql .= " AND o.order_status_id = " . (int)$data['filter_order_status_id'];
      } else {
        $sql .= " AND o.order_status_id > 0 AND o.order_status_id != " . (int)$data['filter_order_status_id'];
      }
    }
  
    if (!empty($data['filter_order_date_added_min']) || !empty($data['filter_order_date_added_max'])) {
      if (!$invert) {
        if (!empty($data['filter_order_date_added_min']) && !empty($data['filter_order_date_added_max'])) {
          $sql .= " AND o.date_added BETWEEN '" . $this->db->escape($data['filter_order_date_added_min']) . "' AND '" . $this->db->escape($data['filter_order_date_added_max']) . "'";
        } elseif (!empty($data['filter_order_date_added_min'])) {
          $sql .= " AND o.date_added >= '" . $this->db->escape($data['filter_order_date_added_min']) . "'";
        } else {
          $sql .= " AND o.date_added <= '" . $this->db->escape($data['filter_order_date_added_max']) . "'";
        }
      } else {
        if (!empty($data['filter_order_date_added_min']) && !empty($data['filter_order_date_added_max'])) {
          $sql .= " AND (o.date_added < '" . $this->db->escape($data['filter_order_date_added_min']) . "' OR o.date_added > '" . $this->db->escape($data['filter_order_date_added_max']) . "')";
        } elseif (!empty($data['filter_order_date_added_min'])) {
          $sql .= " AND o.date_added < '" . $this->db->escape($data['filter_order_date_added_min']) . "'";
        } else {
          $sql .= " AND o.date_added > '" . $this->db->escape($data['filter_order_date_added_max']) . "'";
        }
      }
    }
  
    $where = [];
  
    if (!empty($data['filter_name'])) {
      if (!$invert) {
        $where[] = "`name` LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
      } else {
        $where[] = "`name` NOT LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
      }
    }

    if (!empty($data['filter_telephone'])) {
      if (!$invert) {
        $where[] = "c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
      } else {
        $where[] = "c.telephone NOT LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
      }
    }
  
    if (!empty($data['filter_email'])) {
      if (!$invert) {
        $where[] = "c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
      } else {
        $where[] = "c.email NOT LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
      }
    }
  
    if (!empty($data['filter_customer_group_id'])) {
      if (!$invert) {
        $where[] = "c.customer_group_id = " . (int)$data['filter_customer_group_id'];
      } else {
        $where[] = "c.customer_group_id != " . (int)$data['filter_customer_group_id'];
      }
    }
  
    if (!empty($data['filter_date_registered_min']) || !empty($data['filter_date_registered_max'])) {
      if (!$invert) {
        if (!empty($data['filter_date_registered_min']) && !empty($data['filter_date_registered_max'])) { 
          $where[] = "c.date_added BETWEEN '" . $this->db->escape($data['filter_date_registered_min']) . "' AND '" . $this->db->escape($data['filter_date_registered_max']) . "'";
        } elseif (!empty($data['filter_date_registered_min'])) {
          $where[] = "c.date_added >= '" . $this->db->escape($data['filter_date_registered_min']) . "'";
        } else {
          $where[] = "c.date_added <= '" . $this->db->escape($data['filter_date_registered_max']) . "'";
        }
      } else {
        if (!empty($data['filter_date_registered_min']) && !empty($data['filter_date_registered_max'])) { 
          $where[] = "(c.date_added < '" . $this->db->escape($data['filter_date_registered_min']) . "' AND c.date_added > '" . $this->db->escape($data['filter_date_registered_max']) . "')";
        } elseif (!empty($data['filter_date_registered_min'])) {
          $where[] = "c.date_added < '" . $this->db->escape($data['filter_date_registered_min']) . "'";
        } else {
          $where[] = "c.date_added > '" . $this->db->escape($data['filter_date_registered_max']) . "'";
        }
      }
    }
    
    

    if (!empty($data['filter_country_id']) || !empty($data['filter_city'])) {
      $sql .= " LEFT JOIN " . DB_PREFIX . "address a ON c.customer_id = a.customer_id";

      if (!$invert) {
        if (!empty($data['filter_country_id'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
        }

        if (!empty($data['filter_zone_id'])) {
          $sql .= " AND a.zone_id = " . (int)$data['filter_zone_id'];
        }

        if (!empty($data['filter_city'])) {
          $sql .= " AND a.city = '" . $this->db->escape($data['filter_city']) . "'";
        }
      } else {
        if (!empty($data['filter_country_id']) && !empty($data['filter_zone_id']) && !empty($data['filter_city'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.zone_id = " . (int)$data['filter_zone_id'];
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        } elseif (!empty($data['filter_country_id']) && !empty($data['filter_zone_id'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.zone_id != " . (int)$data['filter_zone_id'];
        } elseif (!empty($data['filter_country_id']) && !empty($data['filter_city'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        } elseif (!empty($data['filter_city'])) {
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        }
      }

      $where[] = "a.address_id IS NOT NULL";
    }

    if ($where) {
      $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " GROUP BY c.customer_id";

    $having = [];

    if (!is_null($data['filter_point_total_min']) || !is_null($data['filter_point_total_max'])) {
      if (!$invert) {
        if (!is_null($data['filter_point_total_min']) && !is_null($data['filter_point_total_max'])) { 
          $having[] = "reward BETWEEN " . (float)$data['filter_point_total_min'] . " AND " . (float)$data['filter_point_total_max'];
        } elseif(!is_null($data['filter_point_total_min'])) {
          $having[] = "reward >= " . (float)$data['filter_point_total_min'];
        } else {
          $having[] = "reward <= " . (float)$data['filter_point_total_max'];
        }
      } else {
        if (!is_null($data['filter_point_total_min']) && !is_null($data['filter_point_total_max'])) { 
          $having[] = "(reward < " . (float)$data['filter_point_total_min'] . " OR reward > " . (float)$data['filter_point_total_max'] . ")";
        } elseif(!is_null($data['filter_point_total_min'])) {
          $having[] = "reward < " . (float)$data['filter_point_total_min'];
        } else {
          $having[] = "reward > " . (float)$data['filter_point_total_max'];
        }
      }
    }

    if (!is_null($data['filter_order_count_min']) || !is_null($data['filter_order_count_max'])) {
      if (!$invert) {
        if (!is_null($data['filter_order_count_min']) && !is_null($data['filter_order_count_max'])) { 
          $having[] = "order_count BETWEEN " . (int)$data['filter_order_count_min'] . " AND " . (int)$data['filter_order_count_max'];
        } elseif(!is_null($data['filter_order_count_min'])) {
          $having[] = "order_count >= " . (int)$data['filter_order_count_min'];
        } else {
          $having[] = "order_count <= " . (int)$data['filter_order_count_min'];
        }
      } else {
        if (!is_null($data['filter_order_count_min']) && !is_null($data['filter_order_count_max'])) { 
          $having[] = "(order_count < " . (int)$data['filter_order_count_min'] . " OR cr.balance > " . (int)$data['filter_order_count_max'] . ")";
        } elseif(!is_null($data['filter_order_count_min'])) {
          $having[] = "order_count < " . (int)$data['filter_order_count_min'];
        } else {
          $having[] = "order_count > " . (int)$data['filter_order_count_max'];
        }
      }
    }

    if (!is_null($data['filter_orders_total_min']) || !is_null($data['filter_orders_total_max'])) {
      if (!$invert) {
        if (!is_null($data['filter_orders_total_min']) && !is_null($data['filter_orders_total_max'])) { 
          $having[] = "orders_total BETWEEN " . (float)$data['filter_orders_total_min'] . " AND " . (float)$data['filter_orders_total_max'];
        } elseif(!is_null($data['filter_orders_total_min'])) {
          $having[] = "orders_total >= " . (float)$data['filter_orders_total_min'];
        } else {
          $having[] = "orders_total <= " . (float)$data['filter_orders_total_max'];
        }
      } else {
        if (!is_null($data['filter_orders_total_min']) && !is_null($data['filter_orders_total_max'])) { 
          $having[] = "(orders_total < " . (float)$data['filter_orders_total_min'] . " OR cr.balance > " . (float)$data['filter_orders_total_max'] . ")";
        } elseif(!is_null($data['filter_orders_total_min'])) {
          $having[] = "orders_total < " . (float)$data['filter_orders_total_min'];
        } else {
          $having[] = "orders_total > " . (float)$data['filter_orders_total_max'];
        }
      }
    }

    if (!empty($having)) {
      $sql .= " HAVING " . implode(" AND ", $having);
    }

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `name`";
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

    //return array();
    //debug($sql, false, true);


		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCustomers($data = array()) {
    $invert = (int)$data['filter_inversion'];
    
    $nested_query = false; //Вложенный SELECT


    if (!is_null($data['filter_point_total_min']) || !is_null($data['filter_point_total_max']) || !is_null($data['filter_order_count_min']) || !is_null($data['filter_order_count_max']) || !is_null($data['filter_orders_total_min']) || !is_null($data['filter_orders_total_max']) || !empty($data['filter_country_id']) || !empty($data['filter_city'])) {
      $nested_query = true;
      $sql = "SELECT COUNT(*) AS total FROM (SELECT c.customer_id";
    } else {
      $sql = "SELECT COUNT(*) AS total ";
    }

    $sql .= " FROM `" . DB_PREFIX . "customer` c";

    //Заполнен фильтр по сумме баланса
    if (!is_null($data['filter_point_total_min']) || !is_null($data['filter_point_total_max'])) {
      $sql .= " LEFT JOIN `" . DB_PREFIX . "customer_reward` cr ON c.customer_id = cr.customer_id AND cr.balance > 0 AND (cr.date_valid >= CURDATE() OR cr.date_valid = '0000-00-00')";
    }

    //Заполнен фильтр по кол-ву, сумме или статусу заказа
    if (!is_null($data['filter_order_count_min']) || !is_null($data['filter_order_count_max']) || !is_null($data['filter_orders_total_min']) || !is_null($data['filter_orders_total_max']) || !empty($data['filter_order_status_id']) || !empty($data['filter_order_date_added_min']) || !empty($data['filter_order_date_added_max'])) {
      $sql .= " LEFT JOIN `" . DB_PREFIX . "order` o ON c.customer_id = o.customer_id";

        if (empty($data['filter_order_status_id'])) {
          $sql .= " AND o.order_status_id > 0";
        } else {
          if (!$invert) {
            $sql .= " AND o.order_status_id = " . (int)$data['filter_order_status_id'];
          } else {
            $sql .= " AND o.order_status_id != " . (int)$data['filter_order_status_id'] . " AND o.order_status_id != 0";
          }
        }

        if (!$invert) {
          if (!empty($data['filter_order_date_added_min'])) {
            $sql .= " AND o.date_added >= '" . $this->db->escape($data['filter_order_date_added_min']) . "'";
          }

          if (!empty($data['filter_order_date_added_max'])) {
            $sql .= " AND o.date_added <= '" . $this->db->escape($data['filter_order_date_added_max']) . "'";
          }
        } else {
          if (!empty($data['filter_order_date_added_min']) && !empty($data['filter_order_date_added_max'])) {
            $sql .= " AND (o.date_added < '" . $this->db->escape($data['filter_order_date_added_min']) . "' OR o.date_added > '" . $this->db->escape($data['filter_order_date_added_max']) . "')";
          } elseif (!empty($data['filter_order_date_added_min'])) {
            $sql .= " AND o.date_added < '" . $this->db->escape($data['filter_order_date_added_min']) . "'";
          } elseif (!empty($data['filter_order_date_added_max'])) {
            $sql .= " AND o.date_added > '" . $this->db->escape($data['filter_order_date_added_max']) . "'";
          }
        }
    }

    $where = [];
    //Заполнен фильтр по адресу
    if (!empty($data['filter_country_id']) || !empty($data['filter_zone_id']) || !empty($data['filter_city'])) {
      $sql .= " LEFT JOIN `" . DB_PREFIX . "address` a ON c.customer_id = a.customer_id";

      if (!$invert) {
        if (!empty($data['filter_country_id'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
        }

        if (!empty($data['filter_zone_id'])) {
          $sql .= " AND a.zone_id = " . (int)$data['filter_zone_id'];
        }

        if (!empty($data['filter_city'])) {
          $sql .= " AND a.city = '" . $this->db->escape($data['filter_city']) . "'";
        }
      } else {
        if (!empty($data['filter_country_id']) && !empty($data['filter_zone_id']) && !empty($data['filter_city'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.zone_id = " . (int)$data['filter_zone_id'];
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        } elseif (!empty($data['filter_country_id']) && !empty($data['filter_zone_id'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.zone_id != " . (int)$data['filter_zone_id'];
        } elseif (!empty($data['filter_country_id']) && !empty($data['filter_city'])) {
          $sql .= " AND a.country_id = " . (int)$data['filter_country_id'];
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        } elseif (!empty($data['filter_city'])) {
          $sql .= " AND a.city != '" . $this->db->escape($data['filter_city']) . "'";
        }
      }

      $where[] = "a.address_id IS NOT NULL";
    }

    if (!empty($data['filter_name'])) {
      if (!$invert) {
        $where[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
      } else {
        $where[] = "CONCAT(c.firstname, ' ', c.lastname) NOT LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
      }
    }

    if (!empty($data['filter_telephone'])) {
      if (!$invert) {
        $where[] = "c.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
      } else {
        $where[] = "c.telephone NOT LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
      }
    }

    if (!empty($data['filter_customer_group_id'])) {
      if (!$invert) {
        $where[] = "c.customer_group_id = " . (int)$data['filter_customer_group_id'];
      } else {
        $where[] = "c.customer_group_id != " . (int)$data['filter_customer_group_id'];
      }
    }

    if (!$invert) {
      if (!empty($data['filter_date_registered_min'])) {
        if (!$invert) {
          $where[] = "c.date_added >= '" . $this->db->escape($data['filter_date_registered_min']) . "'";
        } else {
          $where_or[] = "c.date_added < '" . $this->db->escape($data['filter_date_registered_min']) . "'";
        }
      }

      if (!empty($data['filter_date_registered_max'])) {
        if (!$invert) {
          $where[] = "c.date_added <= '" . $this->db->escape($data['filter_date_registered_max']) . "'";
        } else {
          $where_or[] = "c.date_added > '" . $this->db->escape($data['filter_date_registered_max']) . "'";
        }
      }
    } else {
      if (!empty($data['filter_date_registered_min']) && !empty($data['filter_date_registered_max'])) {
        $where[] = "(c.date_added < '" . $this->db->escape($data['filter_date_registered_min']) . "' OR c.date_added > '" . $this->db->escape($data['filter_date_registered_min']) . "')";
      } elseif (!empty($data['filter_date_registered_min'])) {
        $where[] = "c.date_added < '" . $this->db->escape($data['filter_date_registered_min']) . "'";
      } elseif (!empty($data['filter_date_registered_max'])) {
        $where[] = "c.date_added > '" . $this->db->escape($data['filter_date_registered_min']) . "'";
      }
    }


    if ($where) {
      $sql .= " WHERE " . implode(" AND ", $where);
    }

    //Вложенный SELECT необходимо группировать по customer_id и делать подзапрос
    if ($nested_query) {
      $sql .= " GROUP BY c.customer_id";
      $having = [];

      //Баланс баллов
      if (!$invert) {
        if (!is_null($data['filter_point_total_min'])) {
          $having[] = "SUM(cr.balance) >= " . (int)$data['filter_point_total_min'];
        }

        if (!is_null($data['filter_point_total_min'])) {
          $having[] = "SUM(cr.balance) <= " . (int)$data['filter_point_total_max'];
        }
      } else {
        if (!is_null($data['filter_point_total_min']) && !is_null($data['filter_point_total_min'])) {
          $having[] = "(SUM(cr.balance) < " . (int)$data['filter_point_total_min'] . " OR SUM(cr.balance) > " . (int)$data['filter_point_total_max'] . ")";
        }
      }

      //Количество заказов
      if (!$invert) {
        if (!is_null($data['filter_order_count_min']) && !is_null($data['filter_order_count_max'])) {
          $having[] = "COUNT(o.order_id) BETWEEN " . (int)$data['filter_order_count_min'] . " AND " . (int)$data['filter_order_count_max'];
        } elseif (!is_null($data['filter_order_count_min'])) {
          $having[] = "COUNT(o.order_id) >= " . (int)$data['filter_order_count_min'];
        } elseif (!is_null($data['filter_order_count_max'])) {
          $having[] = "COUNT(o.order_id) <= " . (int)$data['filter_order_count_max'];
        }
      } else {
        if (!is_null($data['filter_order_count_min']) && !is_null($data['filter_order_count_max'])) {
          $having[] = "(COUNT(o.order_id) < " . (int)$data['filter_order_count_min'] . " OR COUNT(o.order_id) > " . (int)$data['filter_order_count_max'] . ")";
        } elseif (!is_null($data['filter_order_count_min'])) {
          $having[] = "COUNT(o.order_id) < " . (int)$data['filter_order_count_min'];
        } elseif (!is_null($data['filter_order_count_max'])) {
          $having[] = "COUNT(o.order_id) > " . (int)$data['filter_order_count_max'];
        }
      }

      //Сумма заказов
      if (!$invert) {
        if (!is_null($data['filter_orders_total_min']) && !is_null($data['filter_orders_total_max'])) {
          $having[] = "SUM(o.total) BETWEEN " . (float)$data['filter_orders_total_min'] . " AND " . (float)$data['filter_orders_total_max'];
        } elseif (!is_null($data['filter_orders_total_min'])) {
          $having[] = "SUM(o.total) >= " . (int)$data['filter_orders_total_min'];
        } elseif (!is_null($data['filter_orders_total_max'])) {
          $having[] = "SUM(o.total) <= " . (int)$data['filter_orders_total_max'];
        }
      } else {
        if (!is_null($data['filter_orders_total_min']) && !is_null($data['filter_orders_total_max'])) {
          $having[] = "SUM(o.total) < " . (int)$data['filter_orders_total_min'] . " OR SUM(o.total) > " . (int)$data['filter_orders_total_max'] . ")";
        } elseif (!is_null($data['filter_orders_total_min'])) {
          $having[] = "SUM(o.total) < " . (int)$data['filter_orders_total_min'];
        } elseif (!is_null($data['filter_orders_total_max'])) {
          $having[] = "SUM(o.total) > " . (int)$data['filter_orders_total_max'];
        }
      }

      if ($having) {
        $sql .= " HAVING " . implode(" AND ", $having);
      }

      $sql.= ") AS customers";
    }

    $query = $this->db->query($sql);

		return $query->row['total'];
	}
        
	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");

		return $query->row['total'];
	}

  public function getCustomerInfo($customer_id) {
    $sql = "SELECT c.*, SUM(cr.balance) AS sum_points FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_reward cr ON c.customer_id = cr.customer_id WHERE c.customer_id = '" . (int)$customer_id . "' AND cr.balance > 0 AND (DATE(date_valid) >= DATE(NOW()) OR date_valid = '0000-00-00')";

    $query = $this->db->query($sql);

    return $query->row;
  }

  /*
  * Добавляем шаблон для рассылки
  */
  public function addMailNewsletterTemplate($data) {
    $sql = "INSERT INTO " . DB_PREFIX . "mass_customer_reward_template SET `store_id` = " . (INT)$data['store_id'] . ", `reward` = " . (float)$data['reward'] . ", date_valid = '" . $this->db->escape($data['date_valid']) . "', `date_added` = NOW()";
    $this->db->query($sql);

    $template_id = $this->db->getLastId();

    foreach ($data['mail_language'] as $language_id => $language) {
      $sql = "INSERT INTO " . DB_PREFIX . "mass_customer_reward_template_language SET `template_id` = " . (int)$template_id . ", `language_id` = " . (int)$language_id . ", `subject` = '" . $this->db->escape($language['subject']) . "', `body` = '" . $this->db->escape($language['body']) . "'";

      $this->db->query($sql);
    }

    return $template_id;
  }

  /*
  * Добавляем рассылку в очередь для конкретного покупателя
  */
  public function addMailNewsLetter($mail_template_id, $customers) {
    $mail_template_id = (int)$mail_template_id;

    $groups = array_chunk($customers, 20);

    foreach ($groups as $group_customers) {
      $sql = "INSERT INTO `" . DB_PREFIX . "mass_customer_reward_newsletter` (`customer_id`, `template_id`) VALUES ";

      $values = array();

      foreach ($group_customers as $customer_id) {
        $values[] = "(" . (int)$customer_id . ", " . $mail_template_id . ")";
      }

      $sql .= implode(", ", $values);

      $this->db->query($sql);
    }
  }

  public function addMailNotify($customer_id, $store_id, $reward, $date_valid, $description) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "mass_customer_reward_mail` SET `customer_id` = '" . (int)$customer_id . "', `store_id` = '" . (int)$store_id . "', `reward` = '" . (float)$reward . "', reward_date_valid = '" . $this->db->escape($date_valid) . "', `description` = '" . $this->db->escape($description) . "', `date_added` = NOW()");
  }

  public function getMailNotify($limit = 0) {
    $sql = "
      SELECT nl.*, c.firstname, c.lastname, c.email, c.language_id, IFNULL(SUM(cr.balance), 0) AS reward_sum 
      FROM `" . DB_PREFIX . "mass_customer_reward_newsletter` nl 
      LEFT JOIN `" . DB_PREFIX . "customer` c 
          ON nl.customer_id = c.customer_id
      LEFT JOIN `" . DB_PREFIX . "customer_reward` cr 
          ON c.customer_id = cr.customer_id 
          AND cr.balance > 0
          AND (cr.date_valid >= CURDATE() OR cr.date_valid = '0000-00-00') 
      WHERE nl.sent = 0";

    if ($limit) {
      $sql .= " LIMIT " . (int)$limit;
    }

    $query = $this->db->query($sql);

    return $query->rows;
  }


  public function setMessageSend($newsletter_id, $error_message = '') {
    $sql = "UPDATE " . DB_PREFIX . "mass_customer_reward_newsletter SET `sent` = 1, date_time_send = NOW(), `error` = '" . $this->db->escape($error_message) . "' WHERE newsletter_id = " . (int)$newsletter_id;

    $this->db->query($sql);
  }

  public function setSentSendNotify($mail_notifies) {
    $ids = array();

    foreach ($mail_notifies as $mail_notify) {
      $ids[] = (int)$mail_notify['mass_customer_reward_mail_id'];
    }

    if ($ids) {
      $iterations = array_chunk($ids, 10);

      foreach ($iterations as $ids) {
        $ids = implode(',', $ids);

        $sql = "UPDATE " . DB_PREFIX . "mass_customer_reward_mail SET `sent` = 1, date_send = NOW() WHERE mass_customer_reward_mail_id IN (" . $ids . ")";

        $this->db->query($sql);
      }
    }
  }

  public function setErrorSent($errors) {
    foreach ($errors as $error) {
      $sql = "UPDATE " . DB_PREFIX . "mass_customer_reward_mail SET `error` = '" . $this->db->escape($error['message']) . "' WHERE mass_customer_reward_mail_id = " . (int)$error['mass_customer_reward_mail_id'];

      $this->db->query($sql);
    }
  }

  public function deleteSendNotify($mail_notifies) {
    $ids = array();

    foreach ($mail_notifies as $mail_notify) {
      $ids[] = (int)$mail_notify['mass_customer_reward_mail_id'];
    }

    if ($ids) {
      $ids = implode(",", $ids);

      $sql = "DELETE FROM " . DB_PREFIX . "mass_customer_reward_mail WHERE mass_customer_reward_mail_id IN (" . $ids . ")";

      $this->db->query($sql);
    }
  }

  public function addRewardCustomers($customers, $reward, $date_valid) {
    $groups = array_chunk($customers, 20);

    $reward = (float)$reward;
    $date_added = date('Y-m-d H:i:s');
    $date_valid = $this->db->escape($date_valid);

    foreach ($groups as $group_customers) {
      $sql = "INSERT INTO " . DB_PREFIX . "customer_reward (`customer_id`, `points`, `balance`, `description`, `date_added`, `date_valid`) VALUES ";

      $values = array();
      foreach ($group_customers as $customer) {
        $values[] = "(" . (int)$customer['customer_id'] . ", " . $reward . ", " . $reward . ", '" . $this->db->escape($customer['description']) . "', '" . $date_added . "', '" . $date_valid . "')";
      }

      $sql .= implode(", ", $values);
      $this->db->query($sql);
    }
  }

  /*
  * Получаем список шаблонов писем
  */
  public function getMailTemplates() {
    $sql = "SELECT * FROM " . DB_PREFIX . "sv_mail_template mt LEFT JOIN " . DB_PREFIX . "sv_mail_template_language mtl ON mt.mail_template_id = mtl.mail_template_id WHERE `module` = 'mass_reward' ORDER BY mt.name";

    $query = $this->db->query($sql);

    $templates = array();

    foreach ($query->rows as $row) {
      $mail_id = $row['mail_template_id'];
      $language_id = $row['language_id'];

      $language = array(
        'language_id' => $language_id,
        'subject' => $row['subject'],
        'body'    => $row['body']
      );

      if (!array_key_exists($mail_id, $templates)) {
        $templates[$mail_id] = array(
          'mail_template_id' => $mail_id,
          'name' => $row['name']
        );
      }

      $templates[$mail_id]['language'][$language_id] = $language;
    }

    return $templates;
  }

  public function addMailTemplate($data) {

    $sql = "INSERT INTO " . DB_PREFIX . "sv_mail_template SET `module` = 'mass_reward', `name` = '" . $this->db->escape($data['template_name']) . "'";
    $this->db->query($sql);

    $mail_template_id = $this->db->getLastId();

    $this->addMailLanguage($mail_template_id, $data['mail_language']);

    return $mail_template_id;
  }

  public function editMailTemplate($mail_template_id, $data) {
    $this->addMailLanguage($mail_template_id, $data['mail_language']);
  }

  private function addMailLanguage($mail_template_id, $languages) {

    $this->db->query("DELETE FROM " . DB_PREFIX . "sv_mail_template_language WHERE mail_template_id = " . (int)$mail_template_id);

    foreach ($languages as $language_id => $language) {
      $sql = "INSERT INTO " . DB_PREFIX . "sv_mail_template_language SET mail_template_id = " . (int)$mail_template_id . ", language_id = " . (int)$language_id . ", `subject` = '" . $this->db->escape($language['subject']) . "', `body` = '" . $this->db->escape($language['body']) . "'";

      $this->db->query($sql);
    }
  }

  public function getRewardTemplate($template_id) {
    $template_language = [];
    $sql = "SELECT * FROM " . DB_PREFIX . "mass_customer_reward_template_language WHERE template_id = " . (int)$template_id;

    $query = $this->db->query($sql);

    foreach ($query->rows as $row) {
      $template_language[$row['language_id']] = $row;
    }

    $sql = "SELECT * FROM " . DB_PREFIX . "mass_customer_reward_template WHERE template_id = " . (int)$template_id;
    $query = $this->db->query($sql);

    $template = array();

    if ($query->num_rows) {
      $template = array(
        'store_id' => $query->row['store_id'],
        'reward'  => $query->row['reward'],
        'date_valid' => $query->row['date_valid'],
        'date_added' => $query->row['date_added'],
        'languages' => $template_language
      );
    }

    return $template;
  }

  /*
  * Выбираем шаблоны сообщения
  */
  public function getMailLanguages($mail_template_id) {
    $languages = array();

    $sql = "SELECT * FROM " . DB_PREFIX . "sv_mail_template_language WHERE mail_template_id = " . (int)$mail_template_id;

    $query = $this->db->query($sql);

    foreach ($query->rows as $row) {
      $languages[$row['language_id']] = array(
        'subject' => $row['subject'],
        'body' => $row['body']
      );
    }

    return $languages;
  }

  /*
  * Получаем названия языков в ОС
  */
  public function getLanguages() {
    $sql = "SELECT * FROM `" . DB_PREFIX . "language`";

    $query = $this->db->query($sql);

    $languages = [];

    foreach ($query->rows as $row) {
      $languages[$row['language_id']] = $row['name'];
    }

    return $languages;
  }

  public function deleteMailTemplate($mail_template_id) {
    $this->db->query("DELETE FROM " . DB_PREFIX , "sv_mail_template WHERE mail_template_id = " . (int)$mail_template_id);
    $this->db->query("DELETE FROM " . DB_PREFIX . "sv_mail_template_language WHERE mail_template_id = " . (int)$mail_template_id);
  }

  public function isInstalled() {
    $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "extension` WHERE code = 'mass_customer_reward_add'";

    $query = $this->db->query($sql);

    return (int)$query->row['total'];
  }
}
