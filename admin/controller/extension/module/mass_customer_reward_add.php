<?php
/* 
 * 1.3.1 - Добавлена проверка на уведомления для клиента
 * 1.3.2 - Добавлено: учитывать текущий день при учете срока жизни, описание текстовым полем
 * 1.3.3 - Исправлена отправка сообщений когда отправка идет без галки "Все покупатели, которые попадают под фильтр"
 * 1.3.4 - Исправлена выборка покупателей если стоит галка "Все покупатели, которые попадают под фильтр"
 * 1.3.5 - Исправлен фильтр выборки покупателей, добавлена функция массового списания баллов
 * 1.3.6 - Добавлены пакетные запросы к БД при начислении баллов
 * 1.3.7 - сохранение эмодзи изменение кодировки
 * 1.4.0 - Добавлено редактирование шаблонов письма
 * 1.4.1 - Исправлен запрос подсчета кол-ва покупателей
 * 2026-09-22 1.4.2 - Добавлена вставка emoji и шорткодов, переработана модель создания рассылки
 */

use sergius_sv\RewardCustomer;
class ControllerExtensionModuleMassCustomerRewardAdd extends Controller {
  const module_version = '1.4.2';
	private $error = array();

  public function index() {
    $this->load->language('extension/module/mass_customer_reward_add');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/mass_customer_reward_add');

    if (!$this->model_extension_module_mass_customer_reward_add->isInstalled()) {
      $this->manualInstall();
    }

    if ($this->config->get('module_mass_customer_reward_add_version') != self::module_version) {
      $this->model_extension_module_mass_customer_reward_add->updateVersion();

      $this->load->model('setting/setting');

      $module_setting = $this->model_setting_setting->getSetting('module_mass_customer_reward_add');
      $module_setting['module_mass_customer_reward_add_version'] = self::module_version;
      $this->model_setting_setting->editSetting('module_mass_customer_reward_add', $module_setting);
    }

    $this->getList();
  }

  public function setting() {
    $this->load->language('extension/module/mass_customer_reward_add');

    $this->document->setTitle($this->language->get('heading_title_setting'));

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('module_mass_customer_reward_add', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'], true));
    }

    $data['error_warning'] = '';

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
      'href' => $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title_setting'),
      'href' => $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/module/mass_customer_reward_add/setting', 'user_token=' . $this->session->data['user_token'], true);

    $data['cancel'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

    if (isset($this->request->post['module_mass_customer_reward_add_limit'])) {
      $data['module_mass_customer_reward_add_limit'] = $this->request->post['module_mass_customer_reward_add_limit'];
    } else {
      $data['module_mass_customer_reward_add_limit'] = $this->config->get('module_mass_customer_reward_add_limit');
    }

    $data['version'] = self::module_version;
    $data['setting'] = true;
    $data['cron_path'] = 'php ' . DIR_APPLICATION . 'mass_customer_reward_cron.php';

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/mass_customer_reward_add', $data));

  }

  protected function getList() {

    $data['heading_title'] = $this->language->get('heading_title') . ' <small>v' . self::module_version . '</small>';

    if (isset($this->request->get['filter_name'])) {
      $filter_name = $this->request->get['filter_name'];
    } else {
      $filter_name = '';
    }

    if (isset($this->request->get['filter_telephone'])) {
      $filter_telephone = $this->request->get['filter_telephone'];
    } else {
      $filter_telephone = '';
    }

    if (isset($this->request->get['filter_email'])) {
      $filter_email = $this->request->get['filter_email'];
    } else {
      $filter_email = '';
    }

    if (isset($this->request->get['filter_customer_group_id'])) {
      $filter_customer_group_id = $this->request->get['filter_customer_group_id'];
    } else {
      $filter_customer_group_id = '';
    }

    if (isset($this->request->get['filter_date_registered_min'])) {
      $filter_date_registered_min = $this->request->get['filter_date_registered_min'];
    } else {
      $filter_date_registered_min = '';
    }

    if (isset($this->request->get['filter_date_registered_max'])) {
      $filter_date_registered_max = $this->request->get['filter_date_registered_max'];
    } else {
      $filter_date_registered_max = '';
    }

    if (isset($this->request->get['filter_point_total_min'])) {
      $filter_point_total_min = $this->request->get['filter_point_total_min'];
    } else {
      $filter_point_total_min = null;
    }

    if (isset($this->request->get['filter_point_total_max'])) {
      $filter_point_total_max = $this->request->get['filter_point_total_max'];
    } else {
      $filter_point_total_max = null;
    }

    if (isset($this->request->get['filter_order_count_min'])) {
      $filter_order_count_min = $this->request->get['filter_order_count_min'];
    } else {
      $filter_order_count_min = null;
    }

    if (isset($this->request->get['filter_order_count_max'])) {
      $filter_order_count_max = $this->request->get['filter_order_count_max'];
    } else {
      $filter_order_count_max = null;
    }

    if (isset($this->request->get['filter_orders_total_min'])) {
      $filter_orders_total_min = $this->request->get['filter_orders_total_min'];
    } else {
      $filter_orders_total_min = null;
    }

    if (isset($this->request->get['filter_orders_total_max'])) {
      $filter_orders_total_max = $this->request->get['filter_orders_total_max'];
    } else {
      $filter_orders_total_max = null;
    }

    if (isset($this->request->get['filter_order_date_added_min'])) {
      $filter_order_date_added_min = $this->request->get['filter_order_date_added_min'];
    } else {
      $filter_order_date_added_min = '';
    }

    if (isset($this->request->get['filter_order_date_added_max'])) {
      $filter_order_date_added_max = $this->request->get['filter_order_date_added_max'];
    } else {
      $filter_order_date_added_max = '';
    }

    if (isset($this->request->get['filter_order_status_id'])) {
      $filter_order_status_id = $this->request->get['filter_order_status_id'];
    } else {
      $filter_order_status_id = '';
    }

    if (isset($this->request->get['filter_country_id'])) {
      $filter_country_id = $this->request->get['filter_country_id'];
    } else {
      $filter_country_id = '';
    }

    if (isset($this->request->get['filter_zone_id'])) {
      $filter_zone_id = $this->request->get['filter_zone_id'];
    } else {
      $filter_zone_id = '';
    }

    if (isset($this->request->get['filter_city'])) {
      $filter_city = $this->request->get['filter_city'];
    } else {
      $filter_city = '';
    }

    $filter_inversion = isset($this->request->get['filter_inversion']);

    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = 'name';
    }

    if (isset($this->request->get['order'])) {
      $order = $this->request->get['order'];
    } else {
      $order = 'ASC';
    }

    if (isset($this->request->get['page'])) {
      $page = (int)$this->request->get['page'];
    } else {
      $page = 1;
    }

    if (isset($this->request->get['mail_template_id'])) {
      $mail_template_id = $this->request->get['mail_template_id'];
    } else {
      $mail_template_id = null;
    }

    $url = $this->makeUrl(['sort', 'order', 'page']);

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
      'href' => $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . $url, true)
    );

    $data['customers'] = array();

    $filter_data = array(
      'filter_name'               => $filter_name,
      'filter_telephone'          => $filter_telephone,
      'filter_email'              => $filter_email,
      'filter_customer_group_id'  => $filter_customer_group_id,
      'filter_date_registered_min' => $filter_date_registered_min,
      'filter_date_registered_max' => $filter_date_registered_max,
      'filter_point_total_min'    => $filter_point_total_min,
      'filter_point_total_max'    => $filter_point_total_max,
      'filter_order_count_min'    => $filter_order_count_min,
      'filter_order_count_max'    => $filter_order_count_max,
      'filter_orders_total_min'    => $filter_orders_total_min,
      'filter_orders_total_max'    => $filter_orders_total_max,
      'filter_order_status_id'    => $filter_order_status_id,
      'filter_order_date_added_min'     => $filter_order_date_added_min,
      'filter_order_date_added_max'     => $filter_order_date_added_max,
      'filter_country_id'         => $filter_country_id,
      'filter_zone_id'            => $filter_zone_id,
      'filter_city'               => $filter_city,
      'filter_inversion'          => $filter_inversion,
      'sort'                      => $sort,
      'order'                     => $order,
      'start'                     => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit'                     => $this->config->get('config_limit_admin')
    );

    $customer_total = $this->model_extension_module_mass_customer_reward_add->getTotalCustomers($filter_data);
    $results = $this->model_extension_module_mass_customer_reward_add->getCustomers($filter_data);

    foreach ($results as $result) {
      $data['customers'][] = array(
        'customer_id'    => $result['customer_id'],
        'name'           => $result['name'],
        'telephone'      => $result['telephone'],
        'email'          => $result['email'],
        'customer_group' => $result['customer_group'],
        'order_count'    => (int)$result['order_count'],
        'orders_total'   => (float)$result['orders_total'],
        'reward'         => round($result['reward'], $this->rwcsv->getSetting('ndp')),
        'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
        'edit'           => $this->url->link('customer/customer/edit', 'customer_id=' . $result['customer_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL')
      );
    }

    $data['user_token'] = $this->session->data['user_token'];

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    if (isset($this->request->post['selected'])) {
      $data['selected'] = (array)$this->request->post['selected'];
    } else {
      $data['selected'] = array();
    }

    $url = $this->makeUrl(['page']);

    $data['sort_name'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
    $data['sort_email'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, true);
    $data['sort_customer_group'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=customer_group' . $url, true);
    $data['sort_status'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
    $data['sort_ip'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=c.ip' . $url, true);
    $data['sort_date_added'] = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

    $url = $this->makeUrl(['sort', 'order']);

    $pagination = new Pagination();
    $pagination->total = $customer_total;
    $pagination->page = $page;
    $pagination->limit = $this->config->get('config_limit_admin');
    $pagination->url = $this->url->link('extension/module/mass_customer_reward_add', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

    $data['pagination'] = $pagination->render();

    $data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

    $data['action'] = $this->url->link('extension/module/mass_customer_reward_add/add_reward', 'user_token=' . $this->session->data['user_token'] . $url, true);
    $data['return'] = $this->url->link('sale/reward_customer_sv', 'user_token=' . $this->session->data['user_token']);

    $data['setting_form'] = $this->url->link('extension/module/mass_customer_reward_add/setting', 'user_token=' . $this->session->data['user_token'], true);

    $data['filter_name'] = $filter_name;
    $data['filter_email'] = $filter_email;
    $data['filter_customer_group_id'] = $filter_customer_group_id;
    $data['filter_date_registered_min'] = $filter_date_registered_min; //add
    $data['filter_date_registered_max'] = $filter_date_registered_max; //add
    $data['filter_order_count_min'] = $filter_order_count_min;
    $data['filter_point_total_min'] = $filter_point_total_min;
    $data['filter_point_total_max'] = $filter_point_total_max;
    $data['filter_order_count_max'] = $filter_order_count_max;
    $data['filter_orders_total_min'] = $filter_orders_total_min;
    $data['filter_orders_total_max'] = $filter_orders_total_max;
    $data['filter_order_status_id'] = $filter_order_status_id;
    $data['filter_order_date_added_min'] = $filter_order_date_added_min;
    $data['filter_order_date_added_max'] = $filter_order_date_added_max;
    $data['filter_country_id'] = $filter_country_id;
    $data['filter_zone_id'] = $filter_zone_id;
    $data['filter_city'] = $filter_city;
    $data['filter_inversion'] = $filter_inversion;

    $this->load->model('customer/customer_group');

    $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

    $this->load->model('localisation/order_status');
    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $this->load->model('localisation/country');
    $data['countries'] = $this->model_localisation_country->getCountries();

    $this->load->model('localisation/language');
    $data['languages'] = $this->model_localisation_language->getLanguages();

    $this->load->model('setting/store');

    $stores = $this->model_setting_store->getStores();

    $data['stores'][] = array(
      'store_id' => 0,
      'name' => $this->config->get('config_name')
    );

    foreach ($stores as $store) {
      $data['stores'][] = array(
        'store_id' => $store['store_id'],
        'name'     => $store['name']
      );
    }

    $data['sort'] = $sort;
    $data['order'] = $order;

    $data['setting'] = false;

    //Шаблоны писем
    $templates = $this->model_extension_module_mass_customer_reward_add->getMailTemplates();

    $data['templates'] = array();

    foreach ($templates as $template) {
      $data['templates'][$template['mail_template_id']] = array(
        'mail_template_id' => $template['mail_template_id'],
        'name'  => $template['name'],
        'language' => $template['language']
      );
    };

    if ($mail_template_id === null || !array_key_exists($mail_template_id, $data['templates'])) {
      $template = reset($data['templates']);

      if ($template) {
        $mail_template_id = $template['mail_template_id'];
      }
    }

    $data['mail_template_id'] = $mail_template_id;
    $data['version'] = self::module_version;

    $this->document->addStyle('view/stylesheet/mass_customer_reward_add.css?ver=' . self::module_version);

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/mass_customer_reward_add', $data));
  }

  private function makeUrl($includes = []) {
    $url = '';

    if (isset($this->request->get['mail_template_id'])) {
      $url .= '&mail_template_id=' . $this->request->get['mail_template_id'];
    }  


    if (isset($this->request->get['filter_name'])) {
      $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_telephone'])) {
      $url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_email'])) {
      $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
      $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_customer_group_id'])) {
      $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
    }

    if (isset($this->request->get['filter_date_registered_min'])) {
      $url .= '&filter_date_registered_min=' . urlencode(html_entity_decode($this->request->get['filter_date_registered_min']));
    }

    if (isset($this->request->get['filter_date_registered_max'])) {
      $url .= '&filter_date_registered_max=' . urlencode(html_entity_decode($this->request->get['filter_date_registered_max']));
    }

    if (isset($this->request->get['filter_point_total_min'])) {
      $url .= '&filter_point_total_min=' . $this->request->get['filter_point_total_min'];
    }

    if (isset($this->request->get['filter_point_total_max'])) {
      $url .= '&filter_point_total_max=' . $this->request->get['filter_point_total_max'];
    }

    if (isset($this->request->get['filter_order_count_min'])) {
      $url .= '&filter_order_count_min=' . $this->request->get['filter_order_count_min'];
    }

    if (isset($this->request->get['filter_order_count_max'])) {
      $url .= '&filter_order_count_max=' . $this->request->get['filter_order_count_max'];
    }

    if (isset($this->request->get['filter_orders_total_min'])) {
      $url .= '&filter_orders_total_min=' . $this->request->get['filter_orders_total_min'];
    }

    if (isset($this->request->get['filter_orders_total_max'])) {
      $url .= '&filter_orders_total_max=' . $this->request->get['filter_orders_total_max'];
    }

    if (isset($this->request->get['filter_order_date_added_min'])) {
      $url .= '&filter_order_date_added_min=' . urlencode(html_entity_decode($this->request->get['filter_order_date_added_min']));
    }

    if (isset($this->request->get['filter_order_date_added_max'])) {
      $url .= '&filter_order_date_added_max=' . urlencode(html_entity_decode($this->request->get['filter_order_date_added_max']));
    }

    if (isset($this->request->get['filter_order_status_id'])) {
      $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
    }

    if (isset($this->request->get['filter_country_id'])) {
      $url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
    }

    if (isset($this->request->get['filter_zone_id'])) {
      $url .= '&filter_zone_id=' . $this->request->get['filter_zone_id'];
    }

    if (isset($this->request->get['filter_city'])) {
      $url .= '&filter_city=' . $this->request->get['filter_city'];
    }

    if (isset($this->request->get['filter_inversion'])) {
      $url .= '&filter_inversion=1';
    }

    if (in_array('sort', $includes) && isset($this->request->get['sort'])) {
      $url .= '&sort=' . $this->request->get['sort'];
    }

    if (in_array('order', $includes) && isset($this->request->get['order'])) {
      $url .= '&order=' . $this->request->get['order'];
    }

    if (in_array('page', $includes) && isset($this->request->get['page'])) {
      $url .= '&page'. $this->request->get['page'];
    }

    return $url;
  }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mass_customer_reward_add')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

  public function autocompleteCustomer() {
    $json = array();

    if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_telephone'])) {
      if (isset($this->request->get['filter_name'])) {
        $filter_name = $this->request->get['filter_name'];
      } else {
        $filter_name = '';
      }

      if (isset($this->request->get['filter_email'])) {
        $filter_email = $this->request->get['filter_email'];
      } else {
        $filter_email = '';
      }

      if (isset($this->request->get['filter_telephone'])) {
        $filter_telephone = $this->request->get['filter_telephone'];
      } else {
        $filter_telephone = '';
      }

      $this->load->model('extension/module/mass_customer_reward_add');

      $filter_data = array(
        'filter_name'      => $filter_name,
        'filter_email'     => $filter_email,
        'filter_telephone' => $filter_telephone,
        'filter_order_count_min' => null,
        'filter_order_count_min' => null,
        'filter_orders_total_min' => null,
        'filter_orders_total_max' => null,
        'filter_order_status_id' => 0,
        'start'            => 0,
        'limit'            => $this->config->get('config_limit_autocomplete')
      );

      $results = $this->model_extension_module_mass_customer_reward_add->getCustomers($filter_data);

      foreach ($results as $result) {
        $json[] = array(
          'customer_id'       => $result['customer_id'],
          'customer_group_id' => $result['customer_group_id'],
          'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          'customer_group'    => $result['customer_group'],
          'firstname'         => $result['firstname'],
          'lastname'          => $result['lastname'],
          'email'             => $result['email'],
          'telephone'         => $result['telephone'],
          'custom_field'      => json_decode($result['custom_field'], true),
        );
      }
    }

    $sort_order = array();

    foreach ($json as $key => $value) {
      $sort_order[$key] = $value['name'];
    }

    array_multisort($sort_order, SORT_ASC, $json);

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  /*
  * Добавление баллов покупателям и формирование рассылки
  */
  public function add_reward() {
    ini_set('max_execution_time', 0);

    $json = array();

    $log = new Log('mass_customer_reward_filter.log');

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $this->load->language('extension/module/mass_customer_reward_add');
      $this->load->model('extension/module/mass_customer_reward_add');

      if (!$this->user->hasPermission('modify', 'extension/module/mass_customer_reward_add')) {
        $json['error'] = $this->language->get('error_permission');
      }

      if (!$json && empty($this->request->post['reward_customers']) && !isset($this->request->post['customer_filter'])) {
        $json['error'] = $this->language->get('error_customers');
      }

      if (!$json && (empty($this->request->post['reward']) || !is_numeric($this->request->post['reward']))) {
        $json['error'] = $this->language->get('error_reward');
      }

      if (!$json) {
        $send_mail = !empty($this->request->post['send_mail']); //Отправляем письма

        $languages = $this->model_extension_module_mass_customer_reward_add->getLanguages();

        $error = [];

        //Проверка что заполнены все нужные поля
        foreach ($this->request->post['mail_language'] as $language_id => $mail_language) {
          $language_name = $languages[$language_id]; //Название языка для вывода ошибки
          
          //Проверка описания которое попадет в таблицу ЛК
          if (trim($mail_language['description']) == '') {
            $error[] = sprintf($this->language->get('error_description'), $language_name);
          }
          

          //Стоит галка отправки письма
          if (!empty($this->request->post['send_mail'])) {
            //Проверяем тему письма
            if (trim($mail_language['mail_subject']) == '') {
              $error[] = sprintf($this->language->get('error_subject'), $language_name);
            }
            
            //Проверяем тему начисления
            if (trim($mail_language['mail_description']) == '') {
              $error[] = sprintf($this->language->get('error_body'), $language_name);
            }
          }

          //Проверка наличия всех языков у шаблона
          if ($send_mail) {

            $mail_template = $this->model_extension_module_mass_customer_reward_add->getMailLanguages($this->request->post['mail_template_id']); //Загружаем шаблон

            foreach ($this->request->post['mail_language'] as $language_id => $mail_language) {
              //Проверяем что в шаблоне есть все языковые пакеты
              if (!isset($mail_template[$language_id])) {
                $language_name = $languages[$language_id];

                $error[] = sprintf($this->language->get('error_template_language'), $language_name);
              }
            }
          }

        }

        if ($error) {
          //Есть ошибки у формы
          $json['error'] = implode('<br>', $error);
        }
      }

      if (!$json) {
        //Ошибок нет продолжаем

        $customers = $this->getFilterCustomers(); //Получаем покупателей попадающих под фильтр

        $log->write('=====================');
        $log->write($this->request->post);

        $log->write('Customers found: ' . count($customers));
        $log->write($customers);

        $reward_days = (int)$this->request->post['reward_days'];
        if ($reward_days && !empty($this->request->post['current_day'])) {
          //Ели указаны дни срока жизни, и стоит галка учитывать текущий день, уменьшаем срок жизни на один день
          $date_start = date('Y-m-d', strtotime("-1 day"));
        } else {
          $date_start = '';
        }

        $date_valid = $this->rwcsv->getDateValid($reward_days, $date_start); 

        $reward = abs($this->request->post['reward']);

        //Подготавливаем описание для таблицы начисления баллов

        //Получаем language_id поумолчанию, на всякий случай если не будет найдено описание для customer[language_id];
        $this->load->model('localisation/language');
        $default_language = $this->model_localisation_language->getLanguageByCode($this->config->get('config_language'));
        $default_language_id = $default_language['language_id'];

        $mail_language = $this->request->post['mail_language'];

        $description_by_language = [];

        foreach ($customers as $index => $customer) {
          $language_id = $customer['language_id'];

          if (!isset($description_by_language[$language_id])) {
            if (!empty($mail_language[$customer['language_id']]['description'])) {
              $description = $mail_language[$customer['language_id']]['description'];
            } else {
              $description = $mail_language[$default_language_id]['description'];
            }

            $description = trim($description);

            $description_by_language[$language_id] = $description;
          }

          $customers[$index]['description'] = $description_by_language[$language_id];
        }


        if ($this->request->post['reward'] > 0) {
          //Пакетное начисление баллов

          $this->model_extension_module_mass_customer_reward_add->addRewardCustomers($customers, $reward, $date_valid);

          //Есть отправка письма, формируем шаблон отправки для конкретной рассылки
          if ($send_mail) {
            $mail_language = [];
            foreach ($this->request->post['mail_language'] as $language_id => $mail) {

              //Подставляем описание рассылки в шаблон письма
              $body = str_replace('{DESCRIPTION}', $mail['mail_description'], $mail_template[$language_id]['body']);

              $mail_language[$language_id] = array(
                'subject' => $mail['mail_subject'],
                'body' => $body
              );
            }

            //Сохраняем данные рассылки
            $newsletter_data = array(
              'store_id' => $this->request->post['store_id'],
              'reward'   => $reward,
              'date_valid' => $date_valid,
              'mail_language' => $mail_language
            );

            $template_id = $this->model_extension_module_mass_customer_reward_add->addMailNewsletterTemplate($newsletter_data);

            $customers_notify = array();
            
            foreach ($customers as $customer) {
              if ($this->rwcsv->sendCustomerEvent($customer, RewardCustomer::NOTIFY_AUTO_REWARD_ADD, RewardCustomer::NOTIFY_TRANSPORT_EMAIL)) {
                $customers_notify[] = $customer['customer_id'];
              }
            }

            if ($customers_notify) {
              $this->model_extension_module_mass_customer_reward_add->addMailNewsLetter($template_id, $customers_notify);

              //$this->model_extension_module_mass_customer_reward_add->addMailNotifyCustomers($customers_notify, $this->request->post['store_id'], $this->request->post['reward'], $date_valid, $this->request->post['mail_template_id']);
            }
          }
        } else { 
          //Пакетное списание баллов
          
          foreach ($customers as $customer) {
            $write_off = $reward;
            if ($reward > $customer['reward']) {
              $write_off = $customer['reward'];
            }

            $this->rwcsv->writeOffReward($customer['customer_id'], $write_off, $customer['description']); //Записываем в таблицу баллов информацию о списании
            $this->rwcsv->pointConfirm($customer['customer_id'], $write_off); //Списываем баллы с баланса
          }

        }
        $json['success'] = $this->language->get('text_success_reward_add');
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  /*
  * Получаем покупателей попадающих под фильтр для начисления баллов
  */
  private function getFilterCustomers() {
    if (!empty($this->request->post['customer_filter'])) {
      if (isset($this->request->get['filter_name'])) {
        $filter_name = $this->request->get['filter_name'];
      } else {
        $filter_name = '';
      }

      if (isset($this->request->get['filter_telephone'])) {
        $filter_telephone = $this->request->get['filter_telephone'];
      } else {
        $filter_telephone = '';
      }

      if (isset($this->request->get['filter_email'])) {
        $filter_email = $this->request->get['filter_email'];
      } else {
        $filter_email = '';
      }

      if (isset($this->request->get['filter_customer_group_id'])) {
        $filter_customer_group_id = $this->request->get['filter_customer_group_id'];
      } else {
        $filter_customer_group_id = '';
      }

      if (isset($this->request->get['filter_date_registered_min'])) {
        $filter_date_registered_min = $this->request->get['filter_date_registered_min'];
      } else {
        $filter_date_registered_min = '';
      }

      if (isset($this->request->get['filter_date_registered_max'])) {
        $filter_date_registered_max = $this->request->get['filter_date_registered_max'];
      } else {
        $filter_date_registered_max = '';
      }


      if (isset($this->request->get['filter_point_total_min'])) {
        $filter_point_total_min = $this->request->get['filter_point_total_min'];
      } else {
        $filter_point_total_min = null;
      }

      if (isset($this->request->get['filter_point_total_max'])) {
        $filter_point_total_max = $this->request->get['filter_point_total_max'];
      } else {
        $filter_point_total_max = null;
      }

      if (isset($this->request->get['filter_order_count_min'])) {
        $filter_order_count_min = $this->request->get['filter_order_count_min'];
      } else {
        $filter_order_count_min = null;
      }

      if (isset($this->request->get['filter_order_count_max'])) {
        $filter_order_count_max = $this->request->get['filter_order_count_max'];
      } else {
        $filter_order_count_max = null;
      }

      if (isset($this->request->get['filter_orders_total_min'])) {
        $filter_orders_total_min = $this->request->get['filter_orders_total_min'];
      } else {
        $filter_orders_total_min = null;
      }

      if (isset($this->request->get['filter_orders_total_max'])) {
        $filter_orders_total_max = $this->request->get['filter_orders_total_max'];
      } else {
        $filter_orders_total_max = null;
      }

      if (isset($this->request->get['filter_order_date_added_min'])) {
        $filter_order_date_added_min = $this->request->get['filter_order_date_added_min'];
      } else {
        $filter_order_date_added_min = '';
      }

      if (isset($this->request->get['filter_order_date_added_max'])) {
        $filter_order_date_added_max = $this->request->get['filter_order_date_added_max'];
      } else {
        $filter_order_date_added_max = '';
      }

      if (isset($this->request->get['filter_order_status_id'])) {
        $filter_order_status_id = $this->request->get['filter_order_status_id'];
      } else {
        $filter_order_status_id = '';
      }

      if (isset($this->request->get['filter_country_id'])) {
        $filter_country_id = $this->request->get['filter_country_id'];
      } else {
        $filter_country_id = '';
      }

      if (isset($this->request->get['filter_zone_id'])) {
        $filter_zone_id = $this->request->get['filter_zone_id'];
      } else {
        $filter_zone_id = '';
      }

      if (isset($this->request->get['filter_city'])) {
        $filter_city = $this->request->get['filter_city'];
      } else {
        $filter_city = '';
      }

      $filter_inversion = isset($this->request->get['filter_inversion']);

      $filter_data = array(
        'filter_name'               => $filter_name,
        'filter_telephone'          => $filter_telephone,
        'filter_email'              => $filter_email,
        'filter_customer_group_id'  => $filter_customer_group_id,
        'filter_date_registered_min' => $filter_date_registered_min,
        'filter_date_registered_max' => $filter_date_registered_max,
        'filter_point_total_min'    => $filter_point_total_min,
        'filter_point_total_max'    => $filter_point_total_max,
        'filter_order_count_min'    => $filter_order_count_min,
        'filter_order_count_max'    => $filter_order_count_max,
        'filter_orders_total_min'   => $filter_orders_total_min,
        'filter_orders_total_max'   => $filter_orders_total_max,
        'filter_order_status_id'    => $filter_order_status_id,
        'filter_order_date_added_min'     => $filter_order_date_added_min,
        'filter_order_date_added_max'     => $filter_order_date_added_max,
        'filter_country_id'         => $filter_country_id,
        'filter_zone_id'            => $filter_zone_id,
        'filter_city'               => $filter_city,
        'filter_inversion'          => $filter_inversion
      );

      $customers = $this->model_extension_module_mass_customer_reward_add->getCustomers($filter_data);
    } else {
      $this->load->model('customer/customer');

      $customers = array();

      foreach ($this->request->post['reward_customers'] as $customer_id) {
        $customer_info = $this->model_extension_module_mass_customer_reward_add->getCustomer($customer_id);

        if ($customer_info) {
          $customers[] = $customer_info;
        }
      }
    }

    return $customers;
  }
  
  /*
  * Добавление/Редактирование шаблона
  */
  public function editTemplate() {
    $json = array();

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $this->load->model('extension/module/mass_customer_reward_add');
      $this->language->load('extension/module/mass_customer_reward_add');

      if (empty($this->request->post['mail_template_id'])) {
        //Добавление нового шаблона
        $mail_template_id = $this->model_extension_module_mass_customer_reward_add->addMailTemplate($this->request->post);
        $this->session->data['success'] = $this->language->get('text_success_add_template');
        $message = '';
      } else {
        //Обновление существующего
        $this->model_extension_module_mass_customer_reward_add->editMailTemplate($this->request->post['mail_template_id'], $this->request->post);
        $mail_template_id = null;
        $message = $this->language->get('text_success_edit_template');
      }

      $json['success'] = array(
        'message' => $message,
        'mail_template_id' => $mail_template_id
      );
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  /*
  * Удаление шаблона
  */
  public function deleteTemplate() {
    $json = array();

    if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['mail_template_id'])) {
      $this->load->model('extension/module/mass_customer_reward_add');
      $this->model_extension_module_mass_customer_reward_add->deleteMailTemplate($this->request->post['mail_template_id']);

      $this->session->data['text_success_delete_mail_template'];
      $json['success'] = true;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  private function deleteLinkFile() {
    if (file_exists(DIR_LOGS . 'mass_customer_reward_send.info')) {
      unlink(DIR_LOGS . 'mass_customer_reward_send.info');
    }
  }

  /*
  * Отправка писем
  * index.php?route=extension/module/mass_customer_reward_add/cronMail&user_token=wv7qN6SJ8tPrHzqo2ghGr48RcxR2fy8l
  */
  public function cronMail() {

    ini_set('max_execution_time', 0);

    $log = new \Log('mass_customer_reward_cron.log');

    if (file_exists(DIR_LOGS . 'mass_customer_reward_send.info')) {
      $log->write('Exit: Second Thread!!!!');
      exit('Second Thread');
    }

    file_put_contents(DIR_LOGS . 'mass_customer_reward_send.info', 'Start Work: ' . date('Y-m-d H:i:s'));

    register_shutdown_function(function(){
      if (file_exists(DIR_LOGS . 'mass_customer_reward_send.info')) {
        unlink(DIR_LOGS . 'mass_customer_reward_send.info');
      }
    });

    $log->write('START CRON!');

    $this->load->language('extension/module/mass_customer_reward_add');
    $this->load->model('extension/module/mass_customer_reward_add');
    $mail_infos = $this->model_extension_module_mass_customer_reward_add->getMailNotify((int)$this->config->get('module_mass_customer_reward_add_limit'));

    $log->write('Found Mails: ' . count($mail_infos));

    if ($mail_infos) {
      $stores = array();

      $this->load->model('localisation/language');

      $default_language = $this->model_localisation_language->getLanguageByCode($this->config->get('config_language'));
      $default_language_id = $default_language['language_id'];

      $languages = array();
      $languages[$default_language_id] = new Language($default_language['code']);
      $languages[$default_language_id]->load($default_language['code']);
      $languages[$default_language_id]->load('mail/mass_customer_reward_add');

      $mail_templates = array();

      $skip_newsletter = array(); //Массив пропущенных рассылок по разным причинам

      foreach ($mail_infos as $mail_info) {
        if ($mail_info['email']) {
          $mail_template_id = $mail_info['template_id'];
          
          //Получаем шаблон рассылки
          if (!isset($mail_templates[$mail_template_id])) {
            $mail_templates[$mail_template_id] = $this->model_extension_module_mass_customer_reward_add->getRewardTemplate($mail_template_id);

            //$mail_templates[$mail_info['mail_template_id']] = $this->model_extension_module_mass_customer_reward_add->getMailLanguages($mail_info['mail_template_id']);
          }

          $mail_template = $mail_templates[$mail_template_id];

          $log->write('=================================');
          //$log->write($mail_template);

          if ($mail_template) {

            $language_id = $mail_info['language_id'];
            
            if (!$language_id) $language_id = $default_language_id;

            if (!array_key_exists($language_id, $languages)) {
              $language_info = $this->model_localisation_language->getLanguage($language_id);

              $languages[$language_id] = new \Language($language_info['code']);
              $languages[$language_id]->load($language_info['code']);
              $languages[$language_id]->load('mail/mass_customer_reward_add');
            }

            $store_id = $mail_template['store_id'];

            if (!isset($stores[$store_id])) {
              $stores[$store_id] = $this->rwcsv->getStoreInfo($store_id);
            }

            $language = $languages[$language_id];

            $store = $stores[$store_id];
            $store_logo = $store['url'] . $store['logo'];
            $store_name = $store['name'];
            $store_url = $store['url'];


            $reward = $this->rwcsv->plural_form(round($mail_template['reward'], $this->rwcsv->getSetting('ndp')), '', $language->get('text_points_forms'));

            if ($mail_template['date_valid'] != '0000-00-00') {
              $date_valid = date($language->get('date_format_short'), strtotime($mail_template['date_valid']));
              $date = new DateTime($date_valid);
              $date->modify('+1 day');
              $date_off = $date->format($language->get('date_format_short'));
            } else {
              $date_valid = '';
              $date_off = '';
            }

            $reward_total = $this->rwcsv->plural_form(round($mail_info['reward_sum'], $this->rwcsv->getSetting('ndp')), '', $language->get('text_points_forms'));

            $search = array(
              '{STORE_LOGO}',
              '{STORE_NAME}',
              '{STORE_URL}',
              '{FIRSTNAME}',
              '{LASTNAME}',
              '{REWARD_RECEIVED}',
              '{REWARD_TOTAL}',
              '{REWARD_DATE_VALID}',
              '{REWARD_DATE_OFF}',
              '{REWARD_PAGE}',
              '{CURR_DATE}'
            );

            $replace = array(
              $store_logo,
              $store_name,
              $store_url,
              $mail_info['firstname'],
              $mail_info['lastname'],
              $reward,
              $reward_total,
              $date_valid,
              $date_off,
              $store['url'] . 'index.php?route=account/reward',
              date($language->get('date_format_short'))
            );

            $mail_language = $mail_template['languages'][$language_id];

            $title = $mail_language['description'];

            $subject = str_replace($search, $replace, $mail_language['subject']);
            $content = str_replace($search, $replace, $mail_language['body']);

            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($mail_info['email']);
            $mail->setFrom($store['email']);
            $mail->setSender(html_entity_decode($store['name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($this->getMailHtml($title, $content));

            $error_message = '';

            try {
              $mail->send();
            } catch (Exception $e) {
              $error_message = $e->getMessage();
            }

            if (!$error_message) {
              $this->model_extension_module_mass_customer_reward_add->setMessageSend($mail_info['newsletter_id']);
            } else {
              $skip_newsletter[] = array(
                'newsletter_id' => $mail_info['newsletter_id'],
                'error' => $error_message
              );
            }

          } else {
            $skip_newsletter[] = array(
              'newsletter_id' => $mail_info['newsletter_id'],
              'error' => $this->language->get('error_no_newsletter_template')
            );
          }
        } else {
          $skip_newsletter[] = array(
            'newsletter_id' => $mail_info['newsletter_id'],
            'error' => $this->language->get('error_customer_empty_email')
          );
        }
      }

      if ($skip_newsletter) {
        //Отмечаем что была ошибка в рассылке
        foreach ($skip_newsletter as $skip) {
          $this->model_extension_module_mass_customer_reward_add->setMessageSend($skip['newsletter_id'], $skip['error']);
        }
      }
    }

    $log->write('CRONE DONE!');
    $this->deleteLinkFile();


    $this->response->setOutput('CRON DONE!');;
  }

  private function getMailHtml($title, $body) {
    $html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">';
    $html .= '<html>';
    $html .= '  <head>';
    $html .= '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    $html .= '    <title>' . $title . '</title>';
    $html .= '  </head>';
    $html .= '  <body>' . html_entity_decode($body, ENT_QUOTES, 'UTF-8') . '</body>';
    $html .= '</html>';

    return $html;
  }

  public function install() {
    $this->load->model('extension/module/mass_customer_reward_add');
    $this->model_extension_module_mass_customer_reward_add->install();

    $this->load->model('setting/setting');
    $this->model_setting_setting->editSetting('module_mass_customer_reward_add', array('module_mass_customer_reward_add_version' => self::module_version));
  }

  private function manualInstall() {
    $this->load->model('setting/extension');
    $this->model_setting_extension->install('module', 'mass_customer_reward_add');

    $this->load->model('user/user_group');
    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/mass_customer_reward_add');
    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/mass_customer_reward_add');

    $this->install();
  }

  public function uninstall() {
    $this->load->model('extension/module/mass_customer_reward_add');
    $this->model_extension_module_mass_customer_reward_add->uninstall();
  }

}