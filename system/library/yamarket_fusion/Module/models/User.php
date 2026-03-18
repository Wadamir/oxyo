<?php
namespace yamarket_fusion\Module\models;

class User extends \yamarket_fusion\Base\Model {
	private $storage;
	private $has_user = false;

	protected $table_name = 'user';

	public $auth_token_lifetime = 600; // seconds

	const code = 'yamarket_fusion.user';

	public function __construct($registry) {
		$this->registry = $registry;

		$user = $this->getUser();

		// unset($this->session->data[self::code]);

		$this->has_user = !empty($user);
		
		if (!isset($this->session->data[self::code])) {
			$this->session->data[self::code] = $user;
		}
		
		$this->storage = &$this->session->data[self::code];
		
		if (!isset($this->storage['permissions'])) {
			$this->storage['permissions'] = array();
		}
	}

	private function getUser() {
		$query = $this->db->query("SELECT * FROM {$this->tableName()} WHERE user_id = " . $this->user->getId());
		$data = $query->row;

		if ($data)
			$data['permissions'] = json_decode($data['permissions'], true);

		return $data;
	}

	public function updateUser() {
		if ($this->has_user) {
			$this->db->query("UPDATE {$this->tableName()} SET 
					permissions = '{$this->db->escape(json_encode($this->storage['permissions']))}',
					auth_token = '{$this->db->escape($this->getAuthToken())}',
					auth_token_date_added = '{$this->db->escape($this->getAuthTokenDateAdded())}'
				WHERE user_id = " . (int)$this->user->getId()
			);
		}
		else {
			$this->db->query("INSERT INTO {$this->tableName()} SET 
				user_id = " . (int)$this->user->getId() . ",
				permissions = '{$this->db->escape(json_encode($this->storage['permissions']))}',
				auth_token = '{$this->getAuthToken()}',
				auth_token_date_added = '{$this->getAuthTokenDateAdded()}'"

			);
		}
	}

	public function hasPermission($name) {
		if (!$this->config->get('yamarket_fusion_use_auth'))
			return true;

		return in_array($name, $this->storage['permissions']);
	}

	public function setPermissions($permissions) {
		$this->storage['permissions'] = $permissions;
	}
	
	public function setAuthToken($token) {
		$this->storage['auth_token'] = $token;
		$this->updateAuthTokenDateAdded();
	}

	public function authTokenIsValid() {
		if (!empty($this->storage['auth_token']) && $this->storage['auth_token_date_added'] + $this->auth_token_lifetime > time()) {
			return true;
		}
		return false;
	}

	public function getAuthToken() {
		return isset($this->storage['auth_token']) ? $this->storage['auth_token'] : null;
	}

	public function updateAuthTokenDateAdded() {
		$this->storage['auth_token_date_added'] = time();
	}

	private function getAuthTokenDateAdded() {
		return isset($this->storage['auth_token_date_added']) ? $this->storage['auth_token_date_added'] : null;
	}
}