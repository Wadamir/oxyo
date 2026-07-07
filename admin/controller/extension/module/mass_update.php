<?php
class ControllerExtensionModuleMassUpdate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/mass_update');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_mass_update', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

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
			'href' => $this->url->link('extension/module/mass_update', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/mass_update', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/mass_update', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$module_info = array();
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['module_mass_update_status'])) {
			$data['module_mass_update_status'] = $this->request->post['module_mass_update_status'];
		} else {
			$data['module_mass_update_status'] = $this->config->get('module_mass_update_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/mass_update', $data));
	}

	public function edit() {
		$this->load->language('extension/module/mass_update');
		
		// Check permissions first
		if (!$this->user->hasPermission('modify', 'extension/module/mass_update')) {
			$this->response->setOutput('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' . $this->language->get('error_permission') . '</div>');
			return;
		}
		
		$type = isset($this->request->get['type']) ? $this->request->get['type'] : '';
		
		if ($type == 'product_mass_sku_update') {
			$output = $this->editSkuUpdate();
			$this->response->setOutput($output);
			return;
		}
		
		$this->response->setOutput('');
	}

	public function install() {
		try {
			$this->installPermissions();
		} catch (Exception $e) {
			// Silently ignore permission setup errors
		}
	}

	public function uninstall() {
		try {
			$this->uninstallPermissions();
		} catch (Exception $e) {
			// Silently ignore permission cleanup errors
		}
	}

	private function ensurePermissions() {
		// Get all user groups
		$this->load->model('user/user_group');
		@$user_groups = $this->model_user_user_group->getUserGroups();
		
		if (empty($user_groups) || !is_array($user_groups)) {
			return;
		}
		
		foreach ($user_groups as $user_group) {
			// Validate user_group is an array and has user_group_id
			if (!is_array($user_group) || empty(@$user_group['user_group_id'])) {
				continue;
			}
			
			// Get group details to check the name
			$group_result = $this->db->query("SELECT name, permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)@$user_group['user_group_id'] . "'");
			
			if (!$group_result->num_rows) {
				continue;
			}
			
			// Only grant permissions to Administrator or Admin group
			if ($group_result->row['name'] !== 'Administrator' && $group_result->row['name'] !== 'Admin') {
				continue;
			}
			
			$permission_data = json_decode($group_result->row['permission'], true);
			
			if (!is_array($permission_data)) {
				$permission_data = array();
			}
			
			// Preserve existing access permissions, add module if not present
			if (!isset($permission_data['access'])) {
				$permission_data['access'] = array();
			}
			if (!is_array($permission_data['access'])) {
				$permission_data['access'] = array();
			}
			if (!in_array('extension/module/mass_update', $permission_data['access'])) {
				$permission_data['access'][] = 'extension/module/mass_update';
			}
			
			// Preserve existing modify permissions, add module if not present
			if (!isset($permission_data['modify'])) {
				$permission_data['modify'] = array();
			}
			if (!is_array($permission_data['modify'])) {
				$permission_data['modify'] = array();
			}
			if (!in_array('extension/module/mass_update', $permission_data['modify'])) {
				$permission_data['modify'][] = 'extension/module/mass_update';
			}
			
			// Update permissions in database
			$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(json_encode($permission_data)) . "' WHERE user_group_id = '" . (int)$user_group['user_group_id'] . "'");
		}
	}

	private function installPermissions() {
		// Get all user groups
		$this->load->model('user/user_group');
		@$user_groups = $this->model_user_user_group->getUserGroups();
		
		if (empty($user_groups) || !is_array($user_groups)) {
			return;
		}
		
		foreach ($user_groups as $user_group) {
			// Validate user_group is an array and has user_group_id
			if (!is_array($user_group) || empty(@$user_group['user_group_id'])) {
				continue;
			}
			
			// Get group details to check the name
			$group_result = $this->db->query("SELECT name, permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)@$user_group['user_group_id'] . "'");
			
			if (!$group_result->num_rows) {
				continue;
			}
			
			// Only grant permissions to Administrator or Admin group
			if ($group_result->row['name'] !== 'Administrator' && $group_result->row['name'] !== 'Admin') {
				continue;
			}
			
			$permission_data = json_decode($group_result->row['permission'], true);
			
			if (!is_array($permission_data)) {
				$permission_data = array();
			}
			
			// Preserve existing access permissions, add module if not present
			if (!isset($permission_data['access'])) {
				$permission_data['access'] = array();
			}
			if (!is_array($permission_data['access'])) {
				$permission_data['access'] = array();
			}
			if (!in_array('extension/module/mass_update', $permission_data['access'])) {
				$permission_data['access'][] = 'extension/module/mass_update';
			}
			
			// Preserve existing modify permissions, add module if not present
			if (!isset($permission_data['modify'])) {
				$permission_data['modify'] = array();
			}
			if (!is_array($permission_data['modify'])) {
				$permission_data['modify'] = array();
			}
			if (!in_array('extension/module/mass_update', $permission_data['modify'])) {
				$permission_data['modify'][] = 'extension/module/mass_update';
			}
			
			// Update permissions in database
			$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(json_encode($permission_data)) . "' WHERE user_group_id = '" . (int)$user_group['user_group_id'] . "'");
		}
	}

	private function uninstallPermissions() {
		// Get all user groups
		$this->load->model('user/user_group');
		@$user_groups = $this->model_user_user_group->getUserGroups();
		
		if (empty($user_groups) || !is_array($user_groups)) {
			return;
		}
		
		foreach ($user_groups as $user_group) {
			// Validate user_group is an array and has user_group_id
			if (!is_array($user_group) || empty(@$user_group['user_group_id'])) {
				continue;
			}
			
			// Get group details to check the name
			$group_result = $this->db->query("SELECT name, permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)@$user_group['user_group_id'] . "'");
			
			if (!$group_result->num_rows) {
				continue;
			}
			
			// Only remove permissions from Administrator or Admin group
			if ($group_result->row['name'] !== 'Administrator' && $group_result->row['name'] !== 'Admin') {
				continue;
			}
			
			$permission_data = json_decode($group_result->row['permission'], true);
			
			if (is_array($permission_data)) {
				// Preserve all permissions except mass_update module
				if (isset($permission_data['access']) && is_array($permission_data['access'])) {
					$permission_data['access'] = array_values(array_filter($permission_data['access'], function($item) {
						return $item !== 'extension/module/mass_update';
					}));
				}
				
				if (isset($permission_data['modify']) && is_array($permission_data['modify'])) {
					$permission_data['modify'] = array_values(array_filter($permission_data['modify'], function($item) {
						return $item !== 'extension/module/mass_update';
					}));
				}
				
				// Update permissions in database
				$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(json_encode($permission_data)) . "' WHERE user_group_id = '" . (int)@$user_group['user_group_id'] . "'");
			}
		}
	}

	private function editSkuUpdate() {
		// Load model and prepare data
		try {
			$this->load->model('catalog/product');
			
			$data = array();
			$data['type'] = 'product_mass_sku_update';
			$data['user_token'] = $this->session->data['user_token'];
			
			// Get all products using model without any restrictions
			$all_products = $this->model_catalog_product->getProducts(array(
				'start' => 0,
				'limit' => 999999
			));
			
            $data['total_products'] = array();
			$data['products_with_sku'] = array();
			$data['products_without_sku'] = array();
			
			if (!empty($all_products)) {
				foreach ($all_products as $product) {
					$data['total_products'][] = $product['product_id'];
					if (!empty($product['sku'])) {
						$data['products_with_sku'][] = $product['product_id'];
					} else {
						$data['products_without_sku'][] = $product['product_id'];
					}
				}
			}
			$data['count_products_with_sku'] = count($data['products_with_sku']);
			$data['count_products_without_sku'] = count($data['products_without_sku']);
			
			// Debug logging
			error_log('Mass Update - Products loaded: ' . count($all_products) . ', With SKU: ' . $data['count_products_with_sku'] . ', Without SKU: ' . $data['count_products_without_sku']);
			
			return $this->load->view('extension/module/mass_update_sku', $data);
		} catch (Exception $e) {
			error_log('Mass Update Error: ' . $e->getMessage());
			return '<div class="alert alert-danger">Error loading form: ' . $e->getMessage() . '</div>';
		}
	}

	// private function error($error_key) {
	// 	$this->load->language('extension/module/mass_update');
	// 	return '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' . $this->language->get($error_key) . '</div>';
	// }

	public function updateSkuForAll() {
		$this->load->language('extension/module/mass_update');
		$json = array();
		
		// Check permissions for both module and product editing
		if (!$this->user->hasPermission('modify', 'extension/module/mass_update') || !$this->user->hasPermission('modify', 'catalog/product')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/module/mass_update');
			
			$selected_product_ids = isset($this->request->post['selected_product_ids']) ? $this->request->post['selected_product_ids'] : array();
			
			if (empty($selected_product_ids)) {
				$json['error'] = $this->language->get('error_no_products');
			} else {
				// Use mass_update model to update SKU with product IDs
				$updated_count = $this->model_extension_module_mass_update->updateSkuWithProductIds($selected_product_ids);
				
				$json['success'] = sprintf($this->language->get('success_sku_updated'), $updated_count);
				$json['updated_count'] = $updated_count;
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mass_update')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}