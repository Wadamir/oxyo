<?php
namespace yamarket_fusion\Module\models;

use yamarket_fusion\Base\ClassMap;

class Setting extends \yamarket_fusion\Base\Model {

	protected $table_name = 'setting';
	
	public function deleteSettingByKey($key, $profile_id = 0) {
		$sql = "DELETE FROM `{$this->tableName()}` WHERE `profile_id` = " . (int)$profile_id;

		if (is_array($key)) {
			$db = $this->db;
			$key_data = implode(',', array_map(function($el) use ($db) {return "'{$db->escape($el)}'";}, $key));
			$sql .= " AND `key` IN ({$key_data})";
		}
		else {
			$sql .= " AND `key` = '{$this->db->escape($key)}'";
		}

		$this->db->query($sql);
	}

	public function deleteSettingByProfileId($profile_id) {
		//$sql = "DELETE FROM `{$this->tableName()}` WHERE `profile_id`";
		
		if (is_array($profile_id)) {
			$where = " IN(" . implode(',', array_map('intval', $profile_id)) . ")";
		}
		else {
			$where = " = " . (int)$profile_id;
		}

		//$sql .= $where;

		$module_class = ClassMap::$models['aliases']['module'];

		foreach ($module_class::module_tables as $table) {
			$this->db->query("DELETE FROM `$table` WHERE profile_id " . $where);
		}

		//$this->db->query($sql);
	}

	public function editSetting($data, $profile_id = 0) {
		$this->deleteSettingByKey(array_keys($data), $profile_id);

		$implode = array();
		$profile_id = (int)$profile_id;

		foreach ($data as $key => $value) {
			$serialized = 0;
			
			if (is_array($value)) {
				$value = json_encode($value);
				$serialized = 1;
			}

			$value = is_null($value) ? 'NULL' : "'{$this->db->escape($value)}'";
			
			$implode[] = "('{$profile_id}', '{$this->db->escape($key)}', {$value}, '{$serialized}')";
		}

		$sql = "INSERT INTO {$this->tableName()}(`profile_id`, `key`, `value`, `serialized`) VALUES " . implode(',', $implode);
		
		$this->db->query($sql);
	}

	public function editSettingValue($key = '', $value = '', $profile_id = 0) {
		//$profile_id = is_null($profile_id) ? 'IS NULL' : ' = ' . (int)$profile_id;
		
		if (!is_array($value)) {
			$this->db->query("UPDATE {$this->tableName()} 
								SET `value` = '" . $this->db->escape($value) . "', `serialized` = '0' 
								WHERE `key` = '" . $this->db->escape($key) . "' AND `profile_id` = " . (int)$profile_id
							);
		} else {
			$this->db->query("UPDATE {$this->tableName()}
								SET `value` = '" . $this->db->escape(json_encode($value)) . "', `serialized` = '1' 
								WHERE `key` = '" . $this->db->escape($key) . "' AND `profile_id` = " . (int)$profile_id
							);
		}
	}

	public function getSetting(/*$profile_id = null*/) {
		$query = $this->db->query("SELECT * FROM {$this->tableName()}");
		$output = array();

		foreach ($query->rows as $result) {
			$output[$result['profile_id']][$result['key']] = $result['serialized'] ? json_decode($result['value'], true) : $result['value'];
		}

		return $output;
	}

	public function getSettingValue($key, $profile_id = 0) {
		$query = $this->db->query(
			"SELECT * FROM {$this->tableName()}
				WHERE `key` = '{$this->db->escape($key)}'
					AND profile_id = " . (int)$profile_id
		);

		if ($query->row && $query->row['serialized']) {
			$query->row['value'] = json_decode($query->row['value'], true);
		}


		return $query->row;
	}

	//public funct
}