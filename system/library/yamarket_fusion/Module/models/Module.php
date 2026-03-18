<?php
namespace yamarket_fusion\Module\models;

use yamarket_fusion\Base\Model;
use yamarket_fusion\Helpers\ArrayHelper;

class Module extends Model {
	// when work event for this model, change caching delete/set to event
	// todo rename updatedOffer methods for yandex market

	const OFFERS_QUERY_LIMIT = 1000;
	const module_tables = array(
		'updated_products'	=> DB_PREFIX . Model::module_table_prefix . 'updated_products',
		'setting'			=> DB_PREFIX . Model::module_table_prefix . 'setting',
		'market_products'	=> DB_PREFIX . Model::module_table_prefix . 'market_products',
		'offer_alias'		=> DB_PREFIX . Model::module_table_prefix . 'offer_alias',
		'user'				=> DB_PREFIX . Model::module_table_prefix . 'user',
	);

	public function addUpdatedOffers($offers, $profile_id) {
		$values = array();

		//$this->deleteUpdatedOffers(array(), $profile_id);
		$this->deleteUpdatedOffers(ArrayHelper::getColumn($offers, 'offer_id'), $profile_id);

		foreach ($offers as $offer) {
			$date_modified = isset($offer['date_modified']) ? $this->db->escape($offer['date_modified']) : date("Y-m-d H:i:s");
			$special_price = isset($offer['special_price']) ? (float)$offer['special_price'] : 'NULL';
			$values[] = "('" . (int)$offer['product_id'] . "', '{$this->db->escape($offer['offer_id'])}', '{$this->db->escape($profile_id)}', '" . (float)$offer['price'] . "', {$special_price}, '{$date_modified}')";
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . Model::module_table_prefix . "updated_products(`product_id`, `offer_id`, `profile_id`, `price`, `special_price`, `date_modified`) 
				VALUES " . implode(',', $values);

		$this->db->query($sql);
	}
	
	public function getUpdatedOffers($data = array(), $profile_id /*, $grouped = false */) {
		$sql = "SELECT DISTINCT u_p.product_id FROM " . DB_PREFIX . Model::module_table_prefix . "updated_products u_p
		LEFT JOIN " . DB_PREFIX . "product_description p_d ON p_d.product_id = u_p.product_id
		LEFT JOIN " . DB_PREFIX . "product AS p ON p.product_id = u_p.product_id 
		JOIN " . DB_PREFIX . "product_to_store p2s ON p2s.product_id = u_p.product_id
		WHERE p2s.store_id = " . (int)$this->config->get('config_store_id') . " 
			AND p_d.language_id = " . (int)$this->config->get('config_language_id')
		. " AND u_p.profile_id = " . (int)$profile_id;

		$implode = array();

		if (isset($data['filter_name'])) {
			$implode[] = "p_d.name LIKE '%{$this->db->escape($data['filter_name'])}%'";
		}
		
		if (isset($data['filter_model'])) {
			$implode[] = "p.model LIKE '%{$this->db->escape($data['filter_model'])}%'";
		}
		
		if (isset($data['filter_status'])) {
			$implode[] = "p.status = " . (int)$data['filter_status'];
		}
		
		if (isset($data['filter_quantity']) && isset($data['filter_quantity_sign'])) {
			$implode[] = "p.quantity {$data['filter_quantity_sign']} " . (int)$data['filter_quantity'];
		}

		if ($implode) {
			$sql .= ' AND ' . implode(' AND ', $implode);
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

		$product_ids = array_map(function($el) {return $el['product_id'];}, $query->rows);

		if (!$product_ids)
			return array();

		$sql_offers = "SELECT u_p.*, p.image, p_d.name, o_a.offer_id AS real_offer_id
					FROM " . DB_PREFIX . Model::module_table_prefix . "updated_products u_p
					LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = u_p.product_id
					LEFT JOIN " . DB_PREFIX . "product_description p_d ON p_d.product_id = u_p.product_id
					LEFT JOIN " . self::module_tables['offer_alias'] . " o_a ON o_a.alias = u_p.offer_id
					WHERE u_p.product_id IN(" . implode(',', $product_ids) . ") 
						AND p_d.language_id = " . (int)$this->config->get('config_language_id') . " 
						AND u_p.profile_id = " . (int)$profile_id . "
					ORDER BY u_p.date_modified DESC";

		$query = $this->db->query($sql_offers);
	
		return $query->rows;
	}

	public function getUpdatedOffersByProductId($product_id, $profile_id) {
		$sql = "SELECT m_p.*
				#, o_a.alias AS offer_id_alias 
				FROM " . DB_PREFIX . Model::module_table_prefix . "updated_products m_p
				#LEFT JOIN " . DB_PREFIX . Model::module_table_prefix . "offer_alias AS o_a ON o_a.offer_id = m_p.offer_id
				WHERE m_p.profile_id = " . (int)$profile_id;

		$implode = array();

		if (is_array($product_id)) {
			$implode[] = "m_p.product_id IN (" . implode(',', array_map(function($el) {return (int)$el;}, $product_id)) . ')';
		}
		else {
			$implode[] = "m_p.product_id = " . (int)$product_id;
		}

		if ($implode) {
			$sql .= " AND " . implode(' AND ', $implode);
		}

		$query = $this->db->query($sql);
		
		return ArrayHelper::index($query->rows, 'offer_id');
	}

	public function deleteUpdatedOffers($offers_ids = array(), $profile_id) {
		$sql = "DELETE FROM " . DB_PREFIX . Model::module_table_prefix . "updated_products WHERE profile_id = " . (int)$profile_id;

		if ($offers_ids) {
			//$offers_ids = array_map(function($el) use($db) {return $db->escape($el);}, $offers_ids);
			$offers_ids = $this->escapeArray($offers_ids);
			$sql .= " AND offer_id IN ('" . implode("','", $offers_ids) . "')";
		}

		$this->db->query($sql);
	}

	public function getTotalUpdatedOffers($data = array(), $profile_id) {
		// todo check * coundition to real result
		
		$sql = "SELECT COUNT(DISTINCT u_p.`product_id`) AS total FROM " . DB_PREFIX . Model::module_table_prefix . "updated_products u_p
				LEFT JOIN " . DB_PREFIX . "product_description p_d ON p_d.product_id = u_p.product_id
				LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = u_p.product_id
				JOIN " . DB_PREFIX . "product_to_store p2s ON p2s.product_id = u_p.product_id
				WHERE p2s.store_id = " . (int)$this->config->get('config_store_id') . "
					AND u_p.profile_id = " . (int)$profile_id;

		$implode = array();

		if (isset($data['filter_name'])) {
			$implode[] = "p_d.name LIKE '%{$this->db->escape($data['filter_name'])}%'";
		}
		
		if (isset($data['filter_model'])) {
			$implode[] = "p.model LIKE '%{$this->db->escape($data['filter_model'])}%'";
		}

		if (isset($data['filter_status'])) {
			$implode[] = "p.status = " . (int)$data['filter_status'];
		}
		
		if (isset($data['filter_quantity']) && isset($data['filter_quantity_sign'])) {
			$implode[] = "p.quantity {$data['filter_quantity_sign']} " . (int)$data['filter_quantity'];
		}

		if ($implode) {
			$sql .= ' AND ' . implode(' AND ', $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function addMarketOffers($products, $profile_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . Model::module_table_prefix . "market_products WHERE profile_id = " . (int)$profile_id);
		$this->deleteMarketOffers(array(), $profile_id);

		$sql = "INSERT INTO " . DB_PREFIX . Model::module_table_prefix . "market_products(`product_id`, `profile_id`, `offer_id`, `price`, `special`, `tax_class_id`) VALUES ";
		$implode = array();

		$offer_id_aliases = array_filter(ArrayHelper::getColumn($products, 'offer_id_alias'), function($el) {
			return (bool)strlen((string)$el);
		});
		
		if ($offer_id_aliases)
			$offer_id_aliases = $this->getOffersAliases(array('filter_offer_id' => $offer_id_aliases));

		$new_offer_id_aliases = array();

		foreach ($products as $product) {
			$implode[] = "('" . (int)$product['product_id'] . "', '" . (int)$profile_id . "', '" . $this->db->escape($product['offer_id']) . "', '{$this->db->escape($product['price'])}', '{$this->db->escape($product['special_price'])}', '" . (int)$product['tax_class_id'] . "')";

			if ($product['offer_id_alias'] && !isset($offer_id_aliases[$product['offer_id']])) {
				$new_offer_id_aliases[] = array(
					'offer_id' => $product['offer_id'],
					'profile_id' => $profile_id,
					'alias' => $product['offer_id_alias']
				);
			}
		}

		$sql .= implode(',', $implode);
		
		$this->db->query($sql);
		
		if ($new_offer_id_aliases) {
			$this->addOffersAliases($new_offer_id_aliases);
		}
	}

	public function getMarketOffers($profile_id, $data = array()) {
		$sql = "SELECT m_p.*, oa.alias FROM " . DB_PREFIX . Model::module_table_prefix . "market_products m_p
				LEFT JOIN " . DB_PREFIX . "product AS p ON p.product_id = m_p.product_id
				LEFT JOIN " . DB_PREFIX . Model::module_table_prefix . "offer_alias AS oa ON oa.offer_id = m_p.offer_id 
					AND oa.profile_id = m_p.profile_id
				WHERE m_p.profile_id = " . (int)$profile_id;

		$implode = array();
		
		if (isset($data['status'])) {
			$implode[] = 'p.status = ' . (int)$data['status'];
		}
		
		if (!empty($data['quantity']) && in_array($data['quantity']['sign'], array('>', '<', '=', '<=', '>='))) {
			$implode[] = "p.quantity {$data['quantity']['sign']} " . (int)$data['quantity']['value'];
		}

		if ($implode) {
			$sql .= " AND " . implode(' AND ', $implode);
		}

		if (isset($data['start']) || isset($data['limit'])) {
			$limit = (int)$data['limit'];
			$start = (int)$data['start'];
			
			if ($limit <= 0) $limit = self::OFFERS_QUERY_LIMIT;
			if ($start < 0) $start = 0;

			$sql .= " LIMIT {$start},{$limit}";
		}
		
		$query = $this->db->query($sql);

		// return ArrayHelper::index($query->rows, 'offer_id');
		return ArrayHelper::index($query->rows, 'alias');
	}

	public function updateMarketOffers($products, $profile_id) {
		$values = array();
		
		$this->deleteMarketOffers(array('filter_product_id' => ArrayHelper::getColumn($products, 'product_id')), $profile_id);

		// todo remove tax_class_id
		$offer_aliases = array();

		// todo move to addMarketOffers
		foreach ($products as $product) {
			$special_price = isset($product['special_price']) ? (float)$product['special_price'] : 0;
			$values[] = "('" . (int)$product['product_id'] . "', '" . (int)$profile_id . "', '". $this->db->escape($product['id']) . "', '" . (float)$product['price'] . "', '{$special_price}', '" . (int)$product['tax_class_id'] . "')";
		
			$offer_aliases[] = array(
				'offer_id' => $product['id'],
				'alias' => $product['offer_id'],
				'profile_id' => $profile_id,
			);
		}

		$sql = "INSERT INTO " . DB_PREFIX . Model::module_table_prefix . "market_products(`product_id`, `profile_id`, `offer_id`, `price`, `special`, `tax_class_id`) 
				VALUES " . implode(',', $values);

		$this->addOffersAliases($offer_aliases);

		$this->db->query($sql);
	}

	public function deleteMarketOffers($filter_data = array(), $profile_id) {
		$sql = "DELETE FROM " . DB_PREFIX . Model::module_table_prefix . "market_products WHERE profile_id = " . (int)$profile_id;
		$sql_ids = "SELECT offer_id FROM " . DB_PREFIX . Model::module_table_prefix . "market_products WHERE profile_id = " . (int)$profile_id;

		if (isset($filter_data['filter_product_id'])) {
			$sql .= " AND product_id IN(" . implode(',', array_map('intval', $filter_data['filter_product_id'])) . ")";
			$sql_ids .= " AND product_id IN(" . implode(',', array_map('intval', $filter_data['filter_product_id'])) . ")";
		}

		$query = $this->db->query($sql_ids);

		$this->db->query($sql);

		if ($query->rows)
			$this->deleteOfferAliasesByMarketOffers(ArrayHelper::getColumn($query->rows, 'offer_id'), $profile_id);
	}

	public function getMarketProductIds($profile_id, $data = array()) {
		$sql = "SELECT m_p.product_id FROM " . DB_PREFIX . Model::module_table_prefix . "market_products m_p
				LEFT JOIN " . DB_PREFIX . "product AS p ON p.product_id = m_p.product_id
				WHERE m_p.profile_id = " . (int)$profile_id;

		$implode = array();
		
		if (isset($data['status'])) {
			$implode[] = 'p.status = ' . (int)$data['status'];
		}
		
		if (!empty($data['quantity']) && in_array($data['quantity']['sign'], array('>', '<', '=', '<=', '>='))) {
			$implode[] = "p.quantity {$data['quantity']['sign']} " . (int)$data['quantity']['value'];
		}

		if ($implode) {
			$sql .= " AND " . implode(' AND ', $implode);
		}

		$sql .= " GROUP BY m_p.product_id";

		if (isset($data['start']) || isset($data['limit'])) {
			$limit = (int)$data['limit'];
			$start = (int)$data['start'];
			
			if ($limit <= 0) $limit = self::OFFERS_QUERY_LIMIT;
			if ($start < 0) $start = 0;

			$sql .= " LIMIT {$start},{$limit}";
		}
		
		$query = $this->db->query($sql);

		return ArrayHelper::getColumn($query->rows, 'product_id');
	}

	public function getTotalMarketProductIds($profile_id, $data = array()) {
		$sql = "SELECT COUNT(m_p.product_id) as total FROM " . DB_PREFIX . Model::module_table_prefix . "market_products m_p
				LEFT JOIN " . DB_PREFIX . "product AS p ON p.product_id = m_p.product_id
				WHERE m_p.profile_id = " . (int)$profile_id;

		$implode = array();

		if (isset($data['status'])) {
			$implode[] = 'p.status = ' . (int)$data['status'];
		}
		
		if (!empty($data['quantity']) && in_array($data['quantity']['sign'], array('>', '<', '=', '<=', '>='))) {
			$implode[] = "p.quantity {$data['quantity']['sign']} " . (int)$data['quantity']['value'];
		}

		if ($implode) {
			$sql .= " AND " . implode(' AND ', $implode);
		}

		//$sql .= " GROUP BY m_p.product_id";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCurrencies() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency");
		return ArrayHelper::index($query->rows, 'code');
	}

	public function getOffersAliases($profile_id, $data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . Model::module_table_prefix . "offer_alias WHERE profile_id = " . (int)$profile_id;

		if ($data) {
			$implode = array();
			
			if (isset($data['filter_offer_id'])) {
				if (is_array($data['filter_offer_id'])) {
					$implode[] = "offer_id IN ('" . implode("','", $this->escapeArray($data['filter_offer_id'])) . "')";
				}
				else {
					$implode[] = "offer_id = " . (int)$this->db->escape($data['filter_offer_id']);
				}
			}

			if (isset($data['filter_alias'])) {
				if (is_array($data['filter_alias'])) {
					$implode[] = "alias IN ('" . implode("','", $this->escapeArray($data['filter_alias'])) . "')";
				}
				else {
					$implode[] = "alias = " . (int)$this->db->escape($data['filter_alias']);
				}
			}

			if ($implode)
				$sql .= " AND " . implode(' AND ', $implode);

			$query = $this->db->query($sql);
			
			return ArrayHelper::index($query->rows, 'offer_id');
		}
		else {
			$cache_name = self::cache_prefix . 'offer_alias';
			$cache = $this->cache->get($cache_name);

			if (!$cache) {
				$query = $this->db->query($sql);
				$output = ArrayHelper::index($query->rows, 'offer_id');
				$this->cache->set($cache_name, $output);
				
				return $output;
			}
		}
	}

	public function addOffersAliases($data) {
		$aliases = array();

		foreach ($data as $value) {
			$aliases[] = "('{$this->db->escape($value['offer_id'])}', '{$this->db->escape($value['profile_id'])}', '{$this->db->escape($value['alias'])}')";
		}
		
		if ($aliases) {
			$sql = "INSERT INTO " . DB_PREFIX . Model::module_table_prefix . "offer_alias(`offer_id`, `profile_id`, `alias`) VALUES " . implode(',', $aliases);
			$this->db->query($sql);
		}
	}

	public function deleteOfferAliasesByMarketOffers($data, $profile_id) {
		if (!$data) return;
		
		$query = $this->db->query("SELECT offer_id 
									FROM " . DB_PREFIX . Model::module_table_prefix . "market_products 
									WHERE offer_id IN ('" . implode("','", $this->escapeArray($data)) . "')
										AND profile_id = " . (int)$profile_id
								);

		$offer_ids = ArrayHelper::index($query->rows, 'offer_id');
		$offer_ids_delete = array();

		foreach ($data as $offer_id) {
			if (!isset($offer_ids[$offer_id]))
				$offer_ids_delete[] = $offer_id;
		}

		if ($offer_ids_delete)
			$this->deleteOffersAliases(array('filter_offer_id' => $offer_ids_delete), $profile_id);
	}

	public function deleteOffersAliases($data, $profile_id) {
		$sql = "DELETE FROM " . DB_PREFIX . Model::module_table_prefix . "offer_alias WHERE profile_id = " . (int)$profile_id;

		if (isset($data['filter_offer_id'])) {
			$sql .= " AND offer_id IN ('" . implode("','", $this->escapeArray($data['filter_offer_id'])) . "')";
		}
		else if (isset($data['filter_alias'])) {
			$sql .= " AND alias IN ('" . implode("','", $this->escapeArray($data['filter_alias'])) . "')";
		}
		
		$this->db->query($sql);
	}

	public function checkTables() {
		// $tables = array(
		// 	DB_PREFIX . Model::module_table_prefix . "updated_products",
		// 	DB_PREFIX . Model::module_table_prefix . "market_products",
		// 	DB_PREFIX . Model::module_table_prefix . "setting"
		// );
		$tables = self::module_tables;
		
		$query = $this->db->query("SHOW TABLES FROM " . DB_DATABASE . " WHERE Tables_in_" . DB_DATABASE . " IN('" . implode("','", $tables) . "')");

		return $query->num_rows;
	}

	public function install() {
		$this->db->query("START TRANSACTION");

		$table_name_offers = self::module_tables['updated_products'];
		$table_name_market_products = self::module_tables['market_products'];
		$table_name_setting = self::module_tables['setting'];
		$table_name_offer_alias = self::module_tables['offer_alias'];
		$table_name_user = self::module_tables['user'];
		
		try {
			// updated products
			$this->db->query("DROP TABLE IF EXISTS `{$table_name_offers}`");

			$this->db->query("CREATE TABLE `{$table_name_offers}` (
				`offer_id` varchar(255) NOT NULL,
				`product_id` int(11) NOT NULL,
				`profile_id` int(11) NOT NULL,
				`price` decimal(15,4) NOT NULL,
				`special_price` decimal(15,4) DEFAULT NULL,
				`date_modified` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_name_offers}`
								ADD KEY `product_id` (`product_id`),
								ADD KEY `profile_id` (`profile_id`)"
							);
			
			// setting
			$this->db->query("DROP TABLE IF EXISTS `{$table_name_setting}`");

			$this->db->query("CREATE TABLE `{$table_name_setting}` (
				`id` int(11) NOT NULL,
				`profile_id` int(11) NOT NULL,
				`key` varchar(255) NOT NULL,
				`value` text NULL,
				`serialized` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_name_setting}`
								ADD PRIMARY KEY (`id`),
								ADD KEY `profile_id` (`profile_id`)"
							);

			$this->db->query("ALTER TABLE `{$table_name_setting}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
			
			// market_products
			$this->db->query("DROP TABLE IF EXISTS `{$table_name_market_products}`");

			$this->db->query("CREATE TABLE `{$table_name_market_products}` (
				`product_id` int(11) NOT NULL,
				`profile_id` int(11) NOT NULL,
				`offer_id` varchar(255) NOT NULL,
				`price` decimal(15,4) NOT NULL,
				`special` decimal(15,4) NOT NULL,
				`tax_class_id` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_name_market_products}`
								ADD KEY (`product_id`),
								ADD KEY (`offer_id`),
								ADD KEY (`profile_id`)"
							);
							
			// offer_alias
			$this->db->query("DROP TABLE IF EXISTS `{$table_name_offer_alias}`");

			$this->db->query("CREATE TABLE `{$table_name_offer_alias}` (
				`offer_id` varchar(255) NOT NULL,
				`profile_id` int(11) NOT NULL,
				`alias` varchar(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_name_offer_alias}`
								ADD KEY (`offer_id`),
								ADD KEY (`profile_id`),
								ADD KEY (`alias`)"
							);

			// User
			$this->db->query("DROP TABLE IF EXISTS `{$table_name_user}`");

			$this->db->query("CREATE TABLE `{$table_name_user}` (
				`user_id` int(11) NOT NULL,
				`permissions` text NOT NULL,
				`auth_token` varchar(255) NULL,
				`auth_token_date_added` int(255) NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_name_user}`ADD PRIMARY KEY (`user_id`)");

			$this->db->query("COMMIT");
		}
		catch (\Exception $e) {
			$this->db->query("ROLLBACK");
			throw $e;
		}
	}

	public function uninstall() {
		$tables = self::module_tables;

		foreach ($tables as $table) {
			$this->db->query("DROP TABLE IF EXISTS `{$table}`");
		}
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . Model::module_table_prefix . "updated_products`");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . Model::module_table_prefix . "setting`");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . Model::module_table_prefix . "market_products`");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . Model::module_table_prefix . "offer_alias`");

	}

	public function createProductGoTable() {
		$table_product_go = DB_PREFIX . 'product_go';

		$query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "` WHERE Tables_in_" . DB_DATABASE . " IN('{$table_product_go}')");

		if (!$query->num_rows) {
			$go_columns = array_map(function($el) {
				return "`{$el}`" . ' bit NOT NULL default 0';
			}, $this->config->get('yamarket_fusion_db_filter'));

			$this->db->query("CREATE TABLE IF NOT EXISTS `{$table_product_go}` (
				`product_id` int(255) NOT NULL, "
				. implode (',', $go_columns) . "
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("ALTER TABLE `{$table_product_go}`ADD PRIMARY KEY (`product_id`)");
			$this->db->query("INSERT INTO `{$table_product_go}`(product_id) SELECT product_id FROM " . DB_PREFIX . "product");
		}
	}

	public static function getModuleTables($expect = array()) {
		if ($expect) {
			return array_intersect_key(self::module_tables, array_flip($expect));
		}
		else {
			return self::module_tables;
		}
	}
}