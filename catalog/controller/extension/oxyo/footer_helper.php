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
    $data['oxyo_copyright'] = html_entity_decode(str_replace('{year}', date("Y"), $oxyo_copyright[$lang_id]), ENT_QUOTES, 'UTF-8');
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
        $data['oxyo_columns_count'] = 'col-lg-' . round(12 / $count_columns);
    }
    function sortcolumns($a, $b)
    {
        return strcmp($a['sort'], $b['sort']);
    }
    usort($oxyo_footer_columns, 'sortcolumns');
    foreach ($oxyo_footer_columns as $oxyo_footer_column) {
        $links = array();
        if (isset($oxyo_footer_column['links'])) {
            foreach ($oxyo_footer_column['links'] as $link) {
                if (isset($link['title'][$this->config->get('config_language_id')])) {
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
            usort($links, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
        }
        if (isset($oxyo_footer_column['title'][$lang_id])) {
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
if (($this->config->get('oxyo_popup_note_status')) && (!isset($_COOKIE['oxyo_popup']))) {
    if ((!isset($_GET['route']) || (isset($_GET['route']) && $_GET['route'] == 'common/home')) || (!$this->config->get('oxyo_popup_note_home'))) {
        $data['view_popup'] = true;
        if ($this->config->get('oxyo_popup_note_once')) setcookie("oxyo_popup", "1", time() + 60 * 60 * 24 * 30);
    }
}

// Cookie bar
$data['view_cookie_bar'] = false;
$this->load->language('oxyo/oxyo_theme');
$data['oxyo_cookie_info'] = $this->language->get('oxyo_cookie_info');
$data['oxyo_cookie_btn_close'] = $this->language->get('oxyo_cookie_btn_close');
$data['oxyo_cookie_btn_more_info'] = $this->language->get('oxyo_cookie_btn_more_info');
$data['href_more_info'] = $this->config->get('oxyo_cookie_bar_url');
if (($this->config->get('oxyo_cookie_bar_status')) && (!isset($_COOKIE['oxyo_cookie']))) {
    $data['view_cookie_bar'] = true;
    setcookie("oxyo_cookie", "1", time() + 60 * 60 * 24 * 30);
}



// Contacts
$contact_array = array();
if ($this->config->get('main_phone') && $this->config->get('main_phone') != '') {
    // $contact_array['main_phone'] = $this->config->get('main_phone');
    $data['main_phone'] = trim($this->config->get('main_phone'));
    if (substr($data['main_phone'], 0, 1) === '+' || substr($data['main_phone'], 0, 1) === '7') {
        $data['main_phone_link'] = 'tel:+' . preg_replace('/[^0-9]/', '', $data['main_phone']);
    } else {
        $data['main_phone_link'] = 'tel:' . preg_replace('/[^0-9]/', '', $data['main_phone']);
    }
    $contact_array[] = [
        'name' => 'main_phone',
        'icon' => '<i class="bi bi-telephone"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">  <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>',
        'value' => $data['main_phone'],
        'link' => $data['main_phone_link']
    ];
}
if ($this->config->get('main_phone_header') && $this->config->get('main_phone_header') == '1') {
    $data['main_phone_header'] = true;
} else {
    $data['main_phone_header'] = false;
}

if ($this->config->get('whatsapp_phone') && $this->config->get('whatsapp_phone') != '') {
    // $contact_array['whatsapp_phone'] = $this->config->get('whatsapp_phone');
    $data['whatsapp_phone'] = $this->config->get('whatsapp_phone');
    if (substr($data['whatsapp_phone'], 0, 1) === '+' || substr($data['whatsapp_phone'], 0, 1) === '7') {
        $data['whatsapp_phone_link'] = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $data['whatsapp_phone']);
    } else {
        $data['whatsapp_phone_link'] = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $data['whatsapp_phone']);
    }
    $contact_array[] = [
        'name' => 'whatsapp_phone',
        'icon' => '<i class="bi bi-whatsapp"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">  <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>',
        'value' => $data['whatsapp_phone'],
        'link' => $data['whatsapp_phone_link']
    ];
}
if ($this->config->get('whatsapp_phone_header') && $this->config->get('whatsapp_phone_header') == '1') {
    $data['whatsapp_phone_header'] = true;
} else {
    $data['whatsapp_phone_header'] = false;
}

if ($this->config->get('telegram') && $this->config->get('telegram') != '') {
    // $contact_array['telegram'] = $this->config->get('telegram');
    $data['telegram'] = trim($this->config->get('telegram'));
    if (substr($data['telegram'], 0, 1) === '@') {
        $data['telegram_link'] = 'https://t.me/' . substr($data['telegram'], 1);
    } else {
        $data['telegram_link'] = 'https://t.me/' . $data['telegram'];
    }
    $contact_array[] = [
        'name' => 'telegram',
        'icon' => '<i class="bi bi-telegram"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" fill="currentColor" class="bi bi-telegram" viewBox="0 0 16 16">  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/></svg>',
        'value' => $data['telegram'],
        'link' => $data['telegram_link']
    ];
}
if ($this->config->get('telegram_header') && $this->config->get('telegram_header') == '1') {
    $data['telegram_header'] = true;
} else {
    $data['telegram_header'] = false;
}

if ($this->config->get('vkontakte') && $this->config->get('vkontakte') != '') {
    // $contact_array['vkontakte'] = $this->config->get('vkontakte');
    $data['vkontakte'] = trim($this->config->get('vkontakte'));
    $data['vkontakte_link'] = $data['vkontakte'];
    $data['vkontakte_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="31" height="20" viewBox="0 0 68 43" fill="currentColor"><path d="M37.2082 42.042C14.4165 42.042 1.41667 26.417 0.875 0.416992H12.2916C12.6666 19.5003 21.0831 27.5836 27.7498 29.2503V0.416992H38.5001V16.8752C45.0835 16.1669 51.9993 8.66699 54.3326 0.416992H65.083C63.2913 10.5837 55.7913 18.0836 50.4579 21.1669C55.7913 23.6669 64.3334 30.2086 67.5834 42.042H55.7497C53.208 34.1253 46.8751 28.0003 38.5001 27.1669V42.042H37.2082Z"/></svg>';
    $contact_array[] = [
        'name' => 'vkontakte',
        'icon' => '',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="31" height="20" viewBox="0 0 68 43" fill="currentColor"><path d="M37.2082 42.042C14.4165 42.042 1.41667 26.417 0.875 0.416992H12.2916C12.6666 19.5003 21.0831 27.5836 27.7498 29.2503V0.416992H38.5001V16.8752C45.0835 16.1669 51.9993 8.66699 54.3326 0.416992H65.083C63.2913 10.5837 55.7913 18.0836 50.4579 21.1669C55.7913 23.6669 64.3334 30.2086 67.5834 42.042H55.7497C53.208 34.1253 46.8751 28.0003 38.5001 27.1669V42.042H37.2082Z"/></svg>',
        'value' => $data['vkontakte'],
        'link' => $data['vkontakte_link']
    ];
}
if ($this->config->get('vkontakte_header') && $this->config->get('vkontakte_header') == '1') {
    $data['vkontakte_header'] = true;
} else {
    $data['vkontakte_header'] = false;
}

if ($this->config->get('callback') && $this->config->get('callback') == '1') {
    $data['callback'] = true;
    $data['callback_text'] = $this->language->get('callback_text');
    $data['callback_link'] = '#';
    $data['callback_icon'] = '<i class="bi bi-phone-vibrate"></i>';
    $data['callback_icon_svg'] = '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 16 16" fill="currentColor"><path d="M10 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM6 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/><path d="M8 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2M1.599 4.058a.5.5 0 0 1 .208.676A7 7 0 0 0 1 8c0 1.18.292 2.292.807 3.266a.5.5 0 0 1-.884.468A8 8 0 0 1 0 8c0-1.347.334-2.619.923-3.734a.5.5 0 0 1 .676-.208m12.802 0a.5.5 0 0 1 .676.208A8 8 0 0 1 16 8a8 8 0 0 1-.923 3.734.5.5 0 0 1-.884-.468A7 7 0 0 0 15 8c0-1.18-.292-2.292-.807-3.266a.5.5 0 0 1 .208-.676M3.057 5.534a.5.5 0 0 1 .284.648A5 5 0 0 0 3 8c0 .642.12 1.255.34 1.818a.5.5 0 1 1-.93.364A6 6 0 0 1 2 8c0-.769.145-1.505.41-2.182a.5.5 0 0 1 .647-.284m9.886 0a.5.5 0 0 1 .648.284C13.855 6.495 14 7.231 14 8s-.145 1.505-.41 2.182a.5.5 0 0 1-.93-.364C12.88 9.255 13 8.642 13 8s-.12-1.255-.34-1.818a.5.5 0 0 1 .283-.648"/></svg>';
    $contact_array[] = [
        'name' => 'callback',
        'icon' => '<i class="bi bi-phone-vibrate"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 16 16" fill="currentColor"><path d="M10 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM6 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/><path d="M8 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2M1.599 4.058a.5.5 0 0 1 .208.676A7 7 0 0 0 1 8c0 1.18.292 2.292.807 3.266a.5.5 0 0 1-.884.468A8 8 0 0 1 0 8c0-1.347.334-2.619.923-3.734a.5.5 0 0 1 .676-.208m12.802 0a.5.5 0 0 1 .676.208A8 8 0 0 1 16 8a8 8 0 0 1-.923 3.734.5.5 0 0 1-.884-.468A7 7 0 0 0 15 8c0-1.18-.292-2.292-.807-3.266a.5.5 0 0 1 .208-.676M3.057 5.534a.5.5 0 0 1 .284.648A5 5 0 0 0 3 8c0 .642.12 1.255.34 1.818a.5.5 0 1 1-.93.364A6 6 0 0 1 2 8c0-.769.145-1.505.41-2.182a.5.5 0 0 1 .647-.284m9.886 0a.5.5 0 0 1 .648.284C13.855 6.495 14 7.231 14 8s-.145 1.505-.41 2.182a.5.5 0 0 1-.93-.364C12.88 9.255 13 8.642 13 8s-.12-1.255-.34-1.818a.5.5 0 0 1 .283-.648"/></svg>',
        'value' => $data['callback_text'],
        'link' => $data['callback_link']
    ];
}
if ($this->config->get('callback_header') && $this->config->get('callback_header') == '1') {
    $data['callback_header'] = true;
} else {
    $data['callback_header'] = false;
}

if ($this->config->get('main_email') && $this->config->get('main_email') != '') {
    // $contact_array['main_email'] = $this->config->get('main_email');
    $data['main_email'] = trim($this->config->get('main_email'));
    $data['main_email_link'] = 'mailto:' . preg_replace('/[^0-9a-zA-Z@._-]/', '', $data['main_email']);
    $contact_array[] = [
        'name' => 'main_email',
        'icon' => '<i class="bi bi-envelope"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/></svg>',
        'value' => $data['main_email'],
        'link' => $data['main_email_link']
    ];
}
if ($this->config->get('main_address') && $this->config->get('main_address') != '') {
    // $contact_array['main_address'] = $this->config->get('main_address');
    $data['main_address'] = trim($this->config->get('main_address'));
    $data['main_address_link'] = 'https://yandex.ru/maps/?text=' . urlencode($data['main_address']);
    $contact_array[] = [
        'name' => 'main_address',
        'icon' => '<i class="bi bi-geo-alt"></i>',
        'icon_svg' => '',
        'value' => $data['main_address'],
        'link' => $data['main_address_link']
    ];
}
if ($this->config->get('working_hours') && $this->config->get('working_hours') != '') {
    // $contact_array['working_hours'] = $this->config->get('working_hours');
    $data['working_hours'] = trim($this->config->get('working_hours'));
    $data['working_hours_link'] = '';
    $contact_array[] = [
        'name' => 'working_hours',
        'icon' => '<i class="bi bi-clock"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">  <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>',
        'value' => $data['working_hours'],
        'link' => $data['working_hours_link']
    ];
}
if ($this->config->get('instagram') && $this->config->get('instagram') != '') {
    // $contact_array['instagram'] = $this->config->get('instagram');
    $data['instagram'] = trim($this->config->get('instagram'));
    $data['instagram_link'] = $data['instagram'];
    $contact_array[] = [
        'name' => 'instagram',
        'icon' => '<i class="bi bi-instagram"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">  <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 4.5a.5.5 0 1 1-1 .001A1.5 1.5 0 1 0 10.5 6a.5.5 0 1 1 .001-1A2.5 2.5 0 1 1 11.5 4.5zM8 .5a7.5 7.5 0 1 1-.001 15A7.5 7.5 0 0 1 8 .5z"/><path d="M11.5 7a2.5 2.5 0 1 1-4.999-.001A2.5 2.5 0 0 1 11.5 7z"/></svg>',
        'value' => $data['instagram'],
        'link' => $data['instagram_link']
    ];
}
if ($this->config->get('tiktok') && $this->config->get('tiktok') != '') {
    // $contact_array['tiktok'] = $this->config->get('tiktok');
    $data['tiktok'] = trim($this->config->get('tiktok'));
    $data['tiktok_link'] = $data['tiktok'];
    $contact_array[] = [
        'name' => 'tiktok',
        'icon' => '<i class="bi bi-tiktok"></i>',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">  <path d="M9.5 0a2.5 2.5 0 0 1 2.5 2.5v4a2.5 2.5 0 0 1-2.5-2.5V0z"/><path d="M8 .5a7.5 7.5 0 1 1-.001 15A7.5 7.5 0 0 1 8 .5zm3.5-.001a3.5 3.5 0 1 1-7 .002A3.5 3.5 0 0 1 11.5.499z"/></svg>',
        'value' => $data['tiktok'],
        'link' => $data['tiktok_link']
    ];
}
if ($this->config->get('avito') && $this->config->get('avito') != '') {
    // $contact_array['avito'] = $this->config->get('avito');
    $data['avito'] = trim($this->config->get('avito'));
    $data['avito_link'] = $data['avito'];
    $contact_array[] = [
        'name' => 'avito',
        'icon' => '',
        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="20" viewBox="440 0 1054 380" fill="currentColor"> <g clip-path="url(#clip0_1542_6)"> <path d="M571.081 8.93481L442.188 345.694H511.456L538.205 275.36H675.054L701.676 345.694H770.418L642.003 8.93481H571.081ZM561.676 213.555L606.773 95.054L651.615 213.555H561.676Z" fill="#ffffff"></path> <path d="M1371.41 104.856C1347.17 104.856 1323.48 112.042 1303.33 125.506C1283.18 138.969 1267.48 158.106 1258.21 180.494C1248.93 202.883 1246.51 227.519 1251.23 251.287C1255.96 275.055 1267.63 296.888 1284.77 314.023C1301.9 331.159 1323.73 342.829 1347.5 347.557C1371.27 352.284 1395.91 349.858 1418.3 340.584C1440.68 331.31 1459.82 315.606 1473.28 295.456C1486.75 275.307 1493.93 251.617 1493.93 227.384C1493.93 194.887 1481.02 163.722 1458.05 140.744C1435.07 117.765 1403.9 104.856 1371.41 104.856ZM1371.41 287.088C1359.6 287.088 1348.06 283.587 1338.25 277.029C1328.43 270.47 1320.78 261.148 1316.26 250.241C1311.74 239.335 1310.56 227.333 1312.87 215.755C1315.17 204.177 1320.85 193.541 1329.2 185.194C1337.55 176.846 1348.18 171.161 1359.76 168.858C1371.34 166.555 1383.34 167.737 1394.25 172.255C1405.15 176.772 1414.48 184.423 1421.04 194.238C1427.59 204.054 1431.09 215.594 1431.09 227.4C1431.11 235.241 1429.57 243.009 1426.58 250.256C1423.58 257.503 1419.19 264.088 1413.64 269.633C1408.1 275.179 1401.51 279.575 1394.26 282.57C1387.02 285.565 1379.25 287.1 1371.41 287.088Z" fill="#ffffff"></path> <path d="M852.957 258.843L797.008 109.105H730.939L820.989 345.694H886.533L974.975 109.105H908.906L852.957 258.843Z" fill="#ffffff"></path> <path d="M1182.78 46.234H1119.91V109.105H1083.15V165.595H1119.91V266.306C1119.91 323.321 1151.35 347.826 1195.57 347.826C1210.57 348.043 1225.45 345.138 1239.27 339.297V280.691C1231.75 283.463 1223.82 284.932 1215.81 285.035C1196.72 285.035 1182.78 277.572 1182.78 252V165.595H1239.27V109.105H1182.78V46.234Z" fill="#ffffff"></path> <path d="M1026.66 92.0625C1051.97 92.0625 1072.49 71.5445 1072.49 46.2341C1072.49 20.9238 1051.97 0.405762 1026.66 0.405762C1001.35 0.405762 980.83 20.9238 980.83 46.2341C980.83 71.5445 1001.35 92.0625 1026.66 92.0625Z" fill="#ffffff"></path> <path d="M1058.1 109.105H995.232V345.694H1058.1V109.105Z" fill="#ffffff"></path> <path d="M122.965 379.27C190.652 379.27 245.524 324.398 245.524 256.711C245.524 189.023 190.652 134.152 122.965 134.152C55.2778 134.152 0.40625 189.023 0.40625 256.711C0.40625 324.398 55.2778 379.27 122.965 379.27Z" fill="#04E061"></path> <path d="M335.574 363.803C376.475 363.803 409.631 330.646 409.631 289.745C409.631 248.844 376.475 215.688 335.574 215.688C294.673 215.688 261.516 248.844 261.516 289.745C261.516 330.646 294.673 363.803 335.574 363.803Z" fill="#FF4053"></path> <path d="M146.404 118.175C171.715 118.175 192.233 97.6569 192.233 72.3466C192.233 47.0363 171.715 26.5182 146.404 26.5182C121.094 26.5182 100.576 47.0363 100.576 72.3466C100.576 97.6569 121.094 118.175 146.404 118.175Z" fill="#965EEB"></path> <path d="M306.803 199.696C361.835 199.696 406.448 155.083 406.448 100.051C406.448 45.0183 361.835 0.405762 306.803 0.405762C251.77 0.405762 207.158 45.0183 207.158 100.051C207.158 155.083 251.77 199.696 306.803 199.696Z" fill="#00AAFF"></path> </g> </svg>  ',
        'value' => $data['avito'],
        'link' => $data['avito_link']
    ];
}
$data['contact_array'] = $contact_array;
