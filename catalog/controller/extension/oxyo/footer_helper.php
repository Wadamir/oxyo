<?php
	$lang_id = $this->config->get('config_language_id');
	
	// Footer positions
	if ($this->config->get('theme_default_directory') == 'oxyo') {
	$data['position_bottom_half'] = $this->load->controller('extension/oxyo/position_bottom_half');
	$data['position_bottom'] = $this->load->controller('extension/oxyo/position_bottom');
		if ($this->config->get('config_maintenance')) {
		$this->user = new Cart\User($this->registry);
		if (!$this->user->isLogged()) { 
		$data['position_bottom_half'] = false; 
		$data['position_bottom'] = false; 
		}
		}
	}
	
	$footer_block_1 = $this->config->get('footer_block_1');
	$footer_block_2 = $this->config->get('footer_block_2');
	$footer_infoline_1 = $this->config->get('footer_infoline_1');
	$footer_infoline_2 = $this->config->get('footer_infoline_2');
	$footer_infoline_3 = $this->config->get('footer_infoline_3');
	$footer_block_title = $this->config->get('footer_block_title');
	$oxyo_copyright = $this->config->get('oxyo_copyright');
	$data['footer_block_1'] = '';
	$data['footer_block_2'] = '';
	$data['footer_infoline_1'] = '';
	$data['footer_infoline_2'] = '';
	$data['footer_infoline_3'] = '';
	$data['footer_block_title'] = '';
	$data['oxyo_copyright'] = '';
	if (isset($footer_block_1[$lang_id])) 
	$data['footer_block_1'] = html_entity_decode($footer_block_1[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_block_2[$lang_id])) 
	$data['footer_block_2'] = html_entity_decode($footer_block_2[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_1[$lang_id])) 
	$data['footer_infoline_1'] = html_entity_decode($footer_infoline_1[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_2[$lang_id])) 
	$data['footer_infoline_2'] = html_entity_decode($footer_infoline_2[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_3[$lang_id])) 
	$data['footer_infoline_3'] = html_entity_decode($footer_infoline_3[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_block_title[$lang_id])) 
	$data['footer_block_title'] = html_entity_decode($footer_block_title[$lang_id], ENT_QUOTES, 'UTF-8');	
	if (isset($oxyo_copyright[$lang_id])) 
	$data['oxyo_copyright'] = html_entity_decode(str_replace('{year}',date("Y"),$oxyo_copyright[$lang_id]), ENT_QUOTES, 'UTF-8');
	$data['custom_links'] = $this->config->get('overwrite_footer_links');
	$data['sticky_columns'] = $this->config->get('oxyo_sticky_columns');
	$data['oxyo_version'] = $this->config->get('oxyo_theme_version');
	$data['sticky_columns_offset'] = $this->config->get('oxyo_sticky_columns_offset');
	if ($this->config->get('oxyo_sticky_columns')) $this->document->addScript('catalog/view/theme/oxyo/js/theia-sticky-sidebar.min.js');
	
	// Footer links
	if ($this->config->get('oxyo_footer_columns')) {
		$oxyo_footer_columns = $this->config->get('oxyo_footer_columns');
	} else {
		$oxyo_footer_columns = array();
	}
	if ($this->config->get('oxyo_footer_columns')) {
	$data['oxyo_footer_columns'] = array();
	$count_columns = count($oxyo_footer_columns);
	if ($count_columns == 5) {
	$data['oxyo_columns_count'] = 'col-lg-20';
	} else {
	$data['oxyo_columns_count'] = 'col-lg-' . round(12/$count_columns);
	}
	function sortcolumns($a, $b) {return strcmp($a['sort'], $b['sort']);} usort($oxyo_footer_columns, 'sortcolumns');	
	foreach ($oxyo_footer_columns as $oxyo_footer_column) {
	  $links = array();
		if (isset($oxyo_footer_column['links'])) {
			foreach($oxyo_footer_column['links'] as $link){
			   if (isset($link['title'][$this->config->get('config_language_id')])){
				   $link_title = html_entity_decode($link['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			   } else {
				   $link_title = false;
			   }
			   $links[] = array(
				   'title'      => $link_title,
				   'target'     => $link['target'],
				   'sort'       => $link['sort']
			   );
			 }
		usort($links, function ($a, $b) { return $a['sort'] - $b['sort']; });
		}
	  if(isset($oxyo_footer_column['title'][$lang_id])) {
		$data['oxyo_footer_columns'][] = array(
			'title' => html_entity_decode($oxyo_footer_column['title'][$lang_id], ENT_QUOTES, 'UTF-8'),
			'sort' => $oxyo_footer_column['sort'],
			'links'  => $links
		);
	  }
	}
	}
	
	// Payment icon
	if ($this->request->server['HTTPS']) {
		$server = $this->config->get('config_ssl');
	} else {
		$server = $this->config->get('config_url');
	}
	
	if (is_file(DIR_IMAGE . $this->config->get('oxyo_payment_img'))) {
		$data['payment_img'] = $server . 'image/' . $this->config->get('oxyo_payment_img');
	} else {
		$data['payment_img'] = '';
	}
	
	// Popup
	$data['popup_delay'] = $this->config->get('oxyo_popup_note_delay');
	$data['popup_width_limit'] = $this->config->get('oxyo_popup_note_m');
	$data['view_popup'] = false;
	if ( ($this->config->get('oxyo_popup_note_status')) && (!isset($_COOKIE['oxyo_popup'])) ) {
		if ((!isset($_GET['route']) || (isset($_GET['route']) && $_GET['route'] == 'common/home')) || (!$this->config->get('oxyo_popup_note_home'))) {
			$data['view_popup'] = true;
			if ($this->config->get('oxyo_popup_note_once')) setcookie("oxyo_popup", "1", time()+60*60*24*30);
		}
	}
	
	// Cookie bar
	$data['view_cookie_bar'] = false;
	$this->load->language('oxyo/oxyo_theme');
	$data['oxyo_cookie_info'] = $this->language->get('oxyo_cookie_info');
	$data['oxyo_cookie_btn_close'] = $this->language->get('oxyo_cookie_btn_close');
	$data['oxyo_cookie_btn_more_info'] = $this->language->get('oxyo_cookie_btn_more_info');
	$data['href_more_info'] = $this->config->get('oxyo_cookie_bar_url');	
	if ( ($this->config->get('oxyo_cookie_bar_status')) && (!isset($_COOKIE['oxyo_cookie'])) ) {
	$data['view_cookie_bar'] = true;
	setcookie("oxyo_cookie", "1", time()+60*60*24*30);
	}	