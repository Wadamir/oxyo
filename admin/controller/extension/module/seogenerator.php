<?php
class ControllerExtensionModuleSeogenerator extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->language->load('extension/module/seogenerator');

		$this->document->setTitle($this->language->get('heading_title'));
		

		$this->load->model('setting/setting');
		$this->load->model('localisation/language');
		$this->load->model('extension/module/seogenerator');
		$this->load->model('setting/module');
		//$this->load->model('setting/store');
		//$data['store_id'] = $this->config->get('config_store_id');
		//$store_id = $this->model_setting_store->addStore($this->request->post);
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('module_seogenerator', $this->request->post);	
					
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/module/seogenerator', 'user_token=' . $this->session->data['user_token'], true));
			
			}	

	
 		if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = false;
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/seogenerator', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];
		if (isset($this->request->get['store_id']) && $this->request->get['store_id']) {
			$this->load->model('setting/store');
        $store = $this->model_setting_store->getStore($this->request->get['store_id']); 
		$data['store_id'] = $this->request->get['store_id'];
        }
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['action'] = $this->url->link('extension/module/seogenerator', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		

				$allow_input = array(
			'module_seogenerator_status' => '',
			'module_seogenerator_data' => '',
		);
		
		foreach ($allow_input as $input=>$value) {
			if (isset($this->request->post[$input])) {
				$data[$input] = $this->request->post[$input];
			} else {
				if ($this->config->has($input)) {
					$data[$input] = $this->config->get($input);
				} else {
					$data[$input] = $value;
				}
			}
		}
		


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/seogenerator', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seogenerator')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

}
?>