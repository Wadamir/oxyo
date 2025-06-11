<?php
$this->load->language('oxyo/oxyo_theme');
// Wishlist Items
if ($this->customer->isLogged()) {
    $this->load->model('account/wishlist');
    $data['wishlist_counter'] = $this->model_account_wishlist->getTotalWishlist();
} else {
    $data['wishlist_counter'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
}
// Cart Items
$data['cart_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);

// Cart Total
$data['cart_amount'] = $this->load->controller('extension/oxyo/oxyo_features/total_amount');

// Language/Currency Title
$data['lang_curr_title'] = $this->load->controller('extension/oxyo/oxyo_features/lang_curr_title');
$lang_id = $this->config->get('config_language_id');

// Oxyo Search
$data['oxyo_search'] = $this->load->controller('extension/oxyo/oxyo_features/oxyo_search');

// Default menu with 3 levels
$data['default_menu'] = $this->load->controller('extension/oxyo/default_menu');

// Top Module Position
if ($this->config->get('theme_default_directory') == 'oxyo') {
    $data['position_top'] = $this->load->controller('extension/oxyo/position_top');
    if ($this->config->get('config_maintenance')) {
        $this->user = new Cart\User($this->registry);
        if (!$this->user->isLogged()) {
            $data['position_top'] = false;
        }
    }
}

// Datas
$data['oxyo_header'] = $this->config->get('oxyo_header');
$promo_message = $this->config->get('oxyo_promo');
$promo_message2 = $this->config->get('oxyo_promo2');
$data['promo_message'] = '';
if (isset($promo_message[$lang_id]))
    $data['promo_message'] = html_entity_decode($promo_message[$lang_id], ENT_QUOTES, 'UTF-8');
$data['promo_message2'] = '';
if (isset($promo_message2[$lang_id]))
    $data['promo_message2'] = html_entity_decode($promo_message2[$lang_id], ENT_QUOTES, 'UTF-8');
$data['top_line_style'] = $this->config->get('top_line_style');
$data['top_line_width'] = $this->config->get('top_line_width');
$data['main_header_width'] = $this->config->get('main_header_width');
$data['main_menu_align'] = $this->config->get('main_menu_align');
$data['header_login'] = $this->config->get('header_login');
$data['header_search'] = $this->config->get('header_search');
$data['oxyo_search_scheme'] = 'dark-search';



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
// var_dump($data['main_phone']);



$data['use_custom_links'] = $this->config->get('use_custom_links');
if ($this->config->get('use_custom_links')) {
    $oxyo_links = $this->config->get('oxyo_links');
    $data['oxyo_links'] = array();
    function sortlinks($a, $b)
    {
        return strcmp($a['sort'], $b['sort']);
    }
    usort($oxyo_links, 'sortlinks');
    foreach ($oxyo_links as $oxyo_link) {
        if (isset($oxyo_link['text'][$lang_id])) {
            $data['oxyo_links'][] = array(
                'text' => html_entity_decode($oxyo_link['text'][$lang_id], ENT_QUOTES, 'UTF-8'),
                'target' => $oxyo_link['target'],
                'sort' => $oxyo_link['sort']
            );
        }
    }
}

// Custom CSS
$data['oxyo_custom_css_status'] = $this->config->get('oxyo_custom_css_status');
$data['oxyo_custom_css'] = html_entity_decode($this->config->get('oxyo_custom_css'), ENT_QUOTES, 'UTF-8');

// Custom Javascript
$data['oxyo_custom_js_status'] = $this->config->get('oxyo_custom_js_status');
$data['oxyo_custom_js'] = html_entity_decode($this->config->get('oxyo_custom_js'), ENT_QUOTES, 'UTF-8');

// Menu Management
$data['primary_menu'] = $this->config->get('primary_menu');
$data['secondary_menu'] = $this->config->get('secondary_menu');
if (($this->config->get('primary_menu') > 0) || ($this->config->get('secondary_menu') > 0)) {
    $data['button_cart'] = $this->language->get('button_cart');
    $data['button_wishlist'] = $this->language->get('button_wishlist');
    $data['button_compare'] = $this->language->get('button_compare');
    $data['oxyo_button_quickview'] = $this->language->get('oxyo_button_quickview');
    $data['oxyo_text_sale'] = $this->language->get('oxyo_text_sale');
    $data['oxyo_text_new'] = $this->language->get('oxyo_text_new');
    $data['oxyo_text_days'] = $this->language->get('oxyo_text_days');
    $data['oxyo_text_hours'] = $this->language->get('oxyo_text_hours');
    $data['oxyo_text_mins'] = $this->language->get('oxyo_text_mins');
    $data['oxyo_text_secs'] = $this->language->get('oxyo_text_secs');
    $data['text_back'] = $this->language->get('text_back');
    $data['countdown_status'] = $this->config->get('countdown_status');
    $data['salebadge_status'] = $this->config->get('salebadge_status');
    $this->load->model('extension/oxyo/oxyo_megamenu');
    $data['lang_id'] = $this->config->get('config_language_id');
    $data['logo_maxwidth'] = $this->config->get('logo_maxwidth');
}
if ($this->config->get('primary_menu') > 0) {
    $data['primary_menu_desktop'] = $this->model_extension_oxyo_oxyo_megamenu->getMenu($this->config->get('primary_menu'), $mobile = false);
    $data['primary_menu_mobile'] = $this->model_extension_oxyo_oxyo_megamenu->getMenu($this->config->get('primary_menu'), $mobile = true);
}
if ($this->config->get('secondary_menu') > 0) {
    $data['secondary_menu_desktop'] = $this->model_extension_oxyo_oxyo_megamenu->getMenu($this->config->get('secondary_menu'), $mobile = false);
    $data['secondary_menu_mobile'] = $this->model_extension_oxyo_oxyo_megamenu->getMenu($this->config->get('secondary_menu'), $mobile = true);
}

// Body Class
$data['oxyo_body_class'] = '';
$data['oxyo_body_class'] .= ' product-style' . $this->config->get('oxyo_list_style');
$data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_cart_icon');
if ($this->config->get('catalog_mode')) $data['oxyo_body_class'] .= ' catalog_mode';
if ($this->config->get('oxyo_sticky_header')) $data['oxyo_body_class'] .= ' sticky-enabled';
if ($this->config->get('oxyo_home_overlay_header')) $data['oxyo_body_class'] .= ' home-fixed-header';
if (!$this->config->get('wishlist_status')) $data['oxyo_body_class'] .= ' wishlist_disabled';
if (!$this->config->get('compare_status')) $data['oxyo_body_class'] .= ' compare_disabled';
if (!$this->config->get('quickview_status')) $data['oxyo_body_class'] .= ' quickview_disabled';
if (!$this->config->get('ex_tax_status')) $data['oxyo_body_class'] .= ' hide_ex_tax';
if ($this->config->get('items_mobile_fw')) $data['oxyo_body_class'] .= ' mobile_1';
if ($this->config->get('oxyo_cut_names')) $data['oxyo_body_class'] .= ' cut-names';
if (!$this->config->get('oxyo_back_btn')) $data['oxyo_body_class'] .= ' oxyo-back-btn-disabled';
if ($this->config->get('oxyo_main_layout')) $data['oxyo_body_class'] .= ' boxed-layout';
if ($this->config->get('oxyo_content_width')) $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_content_width');
if ($this->config->get('oxyo_widget_title_style')) $data['oxyo_body_class'] .= ' widget-title-style' . $this->config->get('oxyo_widget_title_style');

// Title styles
if (isset($this->request->get['route'])) {
    $route = (string)$this->request->get['route'];
    if (isset($this->request->get['product_id'])) {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_product');
    } elseif (isset($this->request->get['path']) || isset($this->request->get['manufacturer_id']) || ($route == 'product/search') || ($route == 'product/special')) {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_listings');
    } elseif (preg_match('(affiliate|account)', $route) === 1) {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_account');
    } elseif (preg_match('(checkout)', $route) === 1) {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_checkout');
    } elseif ($route == 'information/contact') {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_contact');
    } elseif (substr($route, 0, 15) == 'extension/blog/') {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_blog');
    } else {
        $data['oxyo_body_class'] .= ' ' . $this->config->get('oxyo_titles_default');
    }
}

// For page specific css
if ((float)VERSION >= 3.0) {
    if (isset($this->request->get['route'])) {
        if (isset($this->request->get['product_id'])) {
            $class = '-' . $this->request->get['product_id'];
        } elseif (isset($this->request->get['path'])) {
            $class = '-' . $this->request->get['path'];
        } elseif (isset($this->request->get['manufacturer_id'])) {
            $class = '-' . $this->request->get['manufacturer_id'];
        } elseif (isset($this->request->get['information_id'])) {
            $class = '-' . $this->request->get['information_id'];
        } else {
            $class = '';
        }
        $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
    } else {
        $data['class'] = 'common-home';
    }
}

// Top promotion message
$data['notification_status'] = false;
$data['top_promo_width'] = $this->config->get('oxyo_top_promo_width');
$data['top_promo_close'] = $this->config->get('oxyo_top_promo_close');
$data['top_promo_align'] = $this->config->get('oxyo_top_promo_align');
$data['top_promo_text'] = '';
$top_promo_text = $this->config->get('oxyo_top_promo_text');
if (isset($top_promo_text[$lang_id]))
    $data['top_promo_text'] = html_entity_decode($top_promo_text[$lang_id], ENT_QUOTES, 'UTF-8');
if (($this->config->get('oxyo_top_promo_status')) && (!isset($_COOKIE['oxyo_top_promo']))) {
    $data['notification_status'] = true;
}

// Mandatory CSS
if ($this->cache->get('oxyo_mandatory_css_store_' . $this->config->get('config_store_id'))) {
    $data['oxyo_mandatory_css'] = $this->cache->get('oxyo_mandatory_css_store_' . $this->config->get('config_store_id'));
} else {
    $menu_height_normal = $this->config->get('menu_height_normal');
    $menu_height_sticky = $this->config->get('menu_height_sticky');

    $madatory_css = '.top_line {line-height:' . $this->config->get('top_line_height') . 'px;}';
    $madatory_css .= '.header-main, .header-main .sign-in, #logo, .header-main .font-zero {
        line-height:' . $this->config->get('main_header_height') . 'px; 
        height:' . $this->config->get('main_header_height') . 'px;
    }';
    $madatory_css .= '.sticky-enabled.sticky-active .sticky-header.short:not(.slidedown) .header-main,.sticky-enabled.offset250 .sticky-header.slidedown .header-main,.sticky-enabled.sticky-active .sticky-header.short .header-main .sign-in,.sticky-enabled.sticky-active .sticky-header.short:not(.slidedown) .header-main #logo,.sticky-enabled.sticky-active .header6 .sticky-header.short .header-main #logo {line-height:' . $this->config->get('main_header_height_sticky') . 'px;height:' . $this->config->get('main_header_height_sticky') . 'px;}';
    $madatory_css .= '@media (max-width: 1200px) {
        .header-main,
        .sticky-enabled.offset250 .sticky-header.slidedown .header-main,
        #logo,
        .sticky-enabled.sticky-active .sticky-header.short .header-main #logo, 
        .sticky-holder,
        .header-main .font-zero {
            line-height:' . $this->config->get('main_header_height_mobile') . 'px; 
            height:' . $this->config->get('main_header_height_mobile') . 'px;
        }
    }';
    if ($menu_height_normal !== '' && $menu_height_normal !== 'auto') {
        $madatory_css .= '.table-cell.menu-cell,.main-menu:not(.vertical) > ul,.main-menu:not(.vertical) > ul > li,.main-menu:not(.vertical) > ul > li > a,.main-menu:not(.vertical) > ul > li.dropdown-wrapper > a .fa-angle-down,.main-menu.vertical .menu-heading {line-height:' . $this->config->get('menu_height_normal') . 'px;height:' . $this->config->get('menu_height_normal') . 'px;}';
    }
    if ($menu_height_sticky !== '' && $menu_height_sticky !== 'auto') {
        $madatory_css .= '.sticky-enabled.sticky-active .table-cell.menu-cell:not(.vertical),.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li > a,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li.dropdown-wrapper > a .fa-angle-down {line-height:' . $this->config->get('menu_height_sticky') . 'px;height:' . $this->config->get('menu_height_sticky') . 'px;}';
    }
    if ($menu_height_normal !== '' && $menu_height_normal !== 'auto') {
        $search_height = round($this->config->get('menu_height_normal') * 0.7);
        $madatory_css .= '.full-search-wrapper .search-main input,.full-search-wrapper .search-category select {height:' . $search_height . 'px;min-height:' . $search_height . 'px;}';
    }
    if ($menu_height_sticky !== '' && $menu_height_sticky !== 'auto') {
        $madatory_css .= '@media (min-width: 992px) {.sticky-enabled.sticky-active .header3 .sticky-header-placeholder,.sticky-enabled.offset250 .header5 .header-main {padding-bottom:' . $this->config->get('menu_height_sticky') . 'px;}}';
    }
    $madatory_css .= '#logo img {max-width:' . $this->config->get('logo_maxwidth') . 'px;}';
    $this->cache->set('oxyo_mandatory_css_store_' . $this->config->get('config_store_id'), $madatory_css);
    $data['oxyo_mandatory_css'] = $this->cache->get('oxyo_mandatory_css_store_' . $this->config->get('config_store_id'));
}

// Custom colors
if ($this->config->get('oxyo_design_status')) {
    $data['oxyo_styles_status'] = $this->config->get('oxyo_design_status');
    $data['oxyo_search_scheme'] = $this->config->get('oxyo_search_scheme');
    if ($this->cache->get('oxyo_styles_cache_store_' . $this->config->get('config_store_id'))) {
        $data['oxyo_styles_cache'] = $this->cache->get('oxyo_styles_cache_store_' . $this->config->get('config_store_id'));
    } else {
        $oxyo_header_bg = $this->config->get('oxyo_header_bg');
        $oxyo_header_bg_rgba = sscanf($oxyo_header_bg, "#%02x%02x%02x");
        $oxyo_header_bg_rgba_main = 'rgba(' . $oxyo_header_bg_rgba[0] . ',' . $oxyo_header_bg_rgba[1] . ',' . $oxyo_header_bg_rgba[2] . ', 1)';
        $oxyo_header_bg_rgba_opacity = 'rgba(' . $oxyo_header_bg_rgba[0] . ',' . $oxyo_header_bg_rgba[1] . ',' . $oxyo_header_bg_rgba[2] . ', 0.5)';
        $styles = 'a:hover, a:focus, .menu-cell .dropdown-inner a:hover, .link-hover-color:hover, .primary-color, .cm_item .primary-color, .nav-tabs.text-center.nav-tabs-sm > li.active {color:' . $this->config->get('oxyo_primary_accent_color') . ';}';
        $styles .= '.primary-bg-color, .widget-title-style2 .widget .widget-title-separator:after, .nav-tabs.text-center.nav-tabs-sm > li.active > a:after,.nav-tabs > li > a:hover,.nav-tabs > li > a:focus,.nav-tabs > li.active > a,.nav-tabs > li.active > a:hover,.nav-tabs > li.active > a:focus {background-color:' . $this->config->get('oxyo_primary_accent_color') . ';}';
        $styles .= 'div.ui-slider-range.ui-widget-header, .ui-state-default, .ui-widget-content .ui-state-default {background:' . $this->config->get('oxyo_primary_accent_color') . ' !important;}';
        // $styles .= '.primary-color-border, .nav-tabs {border-color:' . $this->config->get('oxyo_primary_accent_color') . '!important;}';
        $styles .= '.primary-color-border {border-color:' . $this->config->get('oxyo_primary_accent_color') . '!important;}';
        $styles .= '.top_notificaiton {background-color:' . $this->config->get('oxyo_top_note_bg') . ';}';
        $styles .= '.top_notificaiton, .top_notificaiton a {color:' . $this->config->get('oxyo_top_note_color') . ';}';
        $styles .= '.top_line {background-color:' . $this->config->get('oxyo_top_line_bg') . ';}';
        $styles .= '.top_line, .top_line a {color:' . $this->config->get('oxyo_top_line_color') . ';}';
        $styles .= '.top_line .anim-underline:after, .top_line .links ul > li + li:before, .top_line .links .setting-ul > .setting-li:before {background-color:' . $this->config->get('oxyo_top_line_color') . ';}';

        $styles .= '.teswt, .header-style, .sticky-holder, .main-menu > ul > li .full-width-dropdown {background-color:' . $oxyo_header_bg_rgba_main . ';}';

        // $styles .= '.header-main, .header-main a:not(.btn), .header-main .main-menu > ul > li > a:hover {color:' . $this->config->get('oxyo_header_color') . ';}';
        $styles .= '.header-main, .header-main a:not(.btn) {color:' . $this->config->get('oxyo_header_color') . ';}';
        // Added
        $styles .= '.header-main .menu-element:hover > a, .header-main .menu-element:hover > div > a {color:' . $this->config->get('oxyo_header_accent') . ';}';
        $styles .= '.header-main .sign-in:after, .header-main .anim-underline:after, .header-main .sign-in .anim-underline:after {background-color:' . $this->config->get('oxyo_header_color') . ';}';
        $styles .= '.main-menu:not(.vertical) > ul > li:hover > a > .top, .header-main .shortcut-wrapper:hover .icon-magnifier, .header-main #cart:hover .shortcut-wrapper {opacity:0.8;}';
        $styles .= '.shortcut-wrapper .counter {background-color:' . $this->config->get('oxyo_header_accent') . ';}';
        $styles .= '.header-bottom, .menu-style {background-color:' . $this->config->get('oxyo_header_menu_bg') . ';}';
        $styles .= '.menu-style .main-menu a > .top,.menu-style .main-menu a > .fa-angle-down, .menu-style .main-menu .search-trigger, .main-menu .dropdown-inner h2, .main-menu .dropdown-inner h3, .main-menu .dropdown-inner h4, .main-menu .dropdown-inner ul a:not(:hover) {color:' . $this->config->get('oxyo_header_menu_color') . ';}';
        $styles .= '.menu-style .main-menu > ul > li:hover > a > .top, .menu-style .main-menu > ul > li:hover > a > i, .menu-style .main-menu > ul > li:hover > a {color:' . $this->config->get('oxyo_header_menu_accent') . ';}';
        $styles .= '.menu-tag.sale {background-color:' . $this->config->get('oxyo_menutag_sale_bg') . ';}';
        $styles .= '.menu-tag.sale:before {color:' . $this->config->get('oxyo_menutag_sale_bg') . ';}';
        $styles .= '.menu-tag.new {background-color:' . $this->config->get('oxyo_menutag_new_bg') . ';}';
        $styles .= '.menu-tag.new:before {color:' . $this->config->get('oxyo_menutag_new_bg') . ';}';
        $styles .= '.vertical-menu-bg, .vertical-menu-bg.dropdown-content {background-color:' . $this->config->get('oxyo_vertical_menu_bg') . ';}';
        $styles .= '.main-menu.vertical > ul > li:hover > a {background-color:' . $this->config->get('oxyo_vertical_menu_bg_hover') . ';}';
        $styles .= '.title_in_bc .breadcrumb-holder {background-color:' . $this->config->get('oxyo_bc_bg_color') . ';}';
        $styles .= '.title_in_bc .breadcrumb-holder, .title_in_bc .breadcrumb-holder .oxyo-back-btn {color:' . $this->config->get('oxyo_bc_color') . ';}';
        $styles .= '.title_in_bc .oxyo-back-btn>i,.title_in_bc .oxyo-back-btn>i:after {background-color:' . $this->config->get('oxyo_bc_color') . ';}';

        if ($this->config->get('oxyo_bc_bg_img')) {
            $styles .= '.title_in_bc .breadcrumb-holder {background-position:' . $this->config->get('oxyo_bc_bg_img_pos') . ';background-repeat:' . $this->config->get('oxyo_bc_bg_img_repeat') . ';background-size:' . $this->config->get('oxyo_bc_bg_img_size') . ';background-attachment:' . $this->config->get('oxyo_bc_bg_img_att') . ';background-image:url(' . $server . 'image/' . $this->config->get('oxyo_bc_bg_img') . ');}';
        }

        $styles .= '.btn-primary, a.btn-primary,.btn-neutral {background-color:' . $this->config->get('oxyo_default_btn_bg') . ';color:' . $this->config->get('oxyo_default_btn_color') . ';}';
        $styles .= '.btn-primary:hover,.btn-primary.active,.btn-primary:focus,.btn-default:hover,.btn-default.active,.btn-default:focus {background-color:' . $this->config->get('oxyo_default_btn_bg_hover') . '!important;color:' . $this->config->get('oxyo_default_btn_color_hover') . ' !important;}';
        $styles .= '.btn-contrast-outline {border-color:' . $this->config->get('oxyo_contrast_btn_bg') . ';color:' . $this->config->get('oxyo_contrast_btn_bg') . ';}';
        $styles .= '.btn-contrast, a.btn-contrast, .btn-contrast-outline:hover {background-color:' . $this->config->get('oxyo_contrast_btn_bg') . ';}';
        $styles .= '.price, #cart-content .totals tbody > tr:last-child > td:last-child {color:' . $this->config->get('oxyo_price_color') . '}';
        $styles .= '#footer {background-color:' . $this->config->get('oxyo_footer_bg') . ';}';
        $styles .= '#footer, #footer a, #footer a:hover, #footer h5 {color:' . $this->config->get('oxyo_footer_color') . ';}';
        $styles .= '#footer .footer-copyright:before {background-color:' . $this->config->get('oxyo_footer_color') . ';opacity:0.05;}';
        $styles .= '#footer h5:after {background-color:' . $this->config->get('oxyo_footer_h5_sep') . ';}';
        $styles .= 'body.boxed-layout {background-color:' . $this->config->get('oxyo_body_bg_color') . ';}';

        if ($this->config->get('oxyo_body_bg_img')) {
            $styles .= 'body.boxed-layout {background-position:' . $this->config->get('oxyo_body_bg_img_pos') . ';background-repeat:' . $this->config->get('oxyo_body_bg_img_repeat') . ';background-size:' . $this->config->get('oxyo_body_bg_img_size') . ';background-attachment:' . $this->config->get('oxyo_body_bg_img_att') . ';background-image:url(' . $server . 'image/' . $this->config->get('oxyo_body_bg_img') . ');}';
        }

        // Stickers
        if ($this->config->get('sticker_sale')) {
            $sticker_sale = $this->config->get('sticker_sale');
            if (isset($sticker_sale['status']) && $sticker_sale['status'] == 1) {
                $styles .= '.sale_badge {background-color:' . $sticker_sale['bg'] . ';color:' . $sticker_sale['color'] . ';}';
            }
        }
        if ($this->config->get('sticker_new')) {
            $sticker_new = $this->config->get('sticker_new');
            if (isset($sticker_new['status']) && $sticker_new['status'] == 1) {
                $styles .= '.new_badge {background-color:' . $sticker_new['bg'] . ';color:' . $sticker_new['color'] . ';}';
            }
        }
        // $styles .= '.sale_badge {background-color:' . $this->config->get('oxyo_salebadge_bg') . ';color:' . $this->config->get('oxyo_salebadge_color') . '}';
        // $styles .= '.new_badge {background-color:' . $this->config->get('oxyo_newbadge_bg') . ';color:' . $this->config->get('oxyo_newbadge_color') . '}';

        $styles .= '@media (min-width: 1201px) {';
        $styles .= '.common-home.home-fixed-header.sticky-enabled:not(.sticky-active) .fixed-header-possible .header-style, .common-home.home-fixed-header:not(.sticky-enabled) .fixed-header-possible .header-style, .common-home.home-fixed-header.sticky-enabled:not(.sticky-active) .fixed-header-possible .menu-style, .common-home.home-fixed-header:not(.sticky-enabled) .fixed-header-possible .menu-style, .common-home.home-fixed-header.sticky-enabled:not(.sticky-active) .full-width-dropdown {';
        $styles .= 'background-color:' . $oxyo_header_bg_rgba_opacity . ';';
        $styles .= '}';
        $styles .= '}';

        $this->cache->set('oxyo_styles_cache_store_' . $this->config->get('config_store_id'), $styles);
        $data['oxyo_styles_cache'] = $this->cache->get('oxyo_styles_cache_store_' . $this->config->get('config_store_id'));
    }
}

// Custom Fonts
if ($this->config->get('oxyo_typo_status')) {
    $data['oxyo_typo_status'] = $this->config->get('oxyo_typo_status');
    // Add import link to head tag
    $oxyo_fonts = $this->config->get('oxyo_fonts');
    $data['oxyo_fonts'] = array();
    $font_list = '';
    if (isset($oxyo_fonts)) {
        foreach ($oxyo_fonts as $oxyo_font) {
            $font_list .= $oxyo_font['import'] . '%7C';
        }
    }
    $this->document->addStyle('//fonts.googleapis.com/css?family=' . $font_list);
    if ($this->cache->get('oxyo_fonts_cache_store_' . $this->config->get('config_store_id'))) {
        $data['oxyo_fonts_cache'] = $this->cache->get('oxyo_fonts_cache_store_' . $this->config->get('config_store_id'));
    } else {
        $font_styles = 'body,.product-name.main-font,.gridlist .single-product .product-name,.gridlist .single-blog .blog-title,#bc-h1-holder #page-title {font-family:' . $this->config->get('body_font_fam') . ';}';
        if (!$this->config->get('body_font_italic_status')) {
            $font_styles .= '.header-main .sign-in .anim-underline,.special_countdown p,.blog .blog_stats i,label i,.cm_item i {font-style:normal;}';
        }
        $font_styles .= 'b, strong, .nav-tabs > li > a, #cart-content .totals tbody > tr:last-child > td, .main-menu .dropdown-inner .static-menu > ul > li.has-sub > a, .main-menu.vertical > ul > li > a, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td.total-cell, .table-bordered.totals tbody > tr:last-child > td:last-child, .compare-table table tbody tr td:first-child, .totals-slip .table-holder table tr:last-child td, .panel-group .panel-heading .panel-title, .badge i, .product-style1 .grid .single-product .price-wrapper .btn-outline, .options .name label, .dropdown-inner h4.column-title, .product-style6 .grid .single-product .price, .product-style6 .single-product .btn-contrast {font-weight:' . $this->config->get('body_font_bold_weight') . ';}';
        $font_styles .= '.product-name, .blog-title, .product-h1 h1, .contrast-heading, .contrast-font {font-family:' . $this->config->get('contrast_font_fam') . ';}';
        $font_styles .= '.promo-style-2 h3, .promo-style-4 h3,.table-bordered > tbody > tr > td.price-cell,.grid .single-product .price,.menu-product .sale-counter div,.table.specification > tbody > tr > td b, .bordered-list-title {font-size:' . $this->config->get('body_font_size_16') . ';}';
        $font_styles .= '.table.products .remove,.full-search-wrapper .search-category select,.blog_post .blog_comment,.video-jumbotron p,.compare-table table tbody tr td:first-child,.grid .single-product .product-name,.grid .single-product .product-name:hover,.list .single-product .product-name {font-size:' . $this->config->get('body_font_size_15') . ';}';
        $font_styles .= 'body,input,textarea,select,.form-control,.icon-element,.main-menu > ul > li,.grid-holder .item,.cm_content .cm_item,.instruction-box .caption a,.btn,a.button,input.button,button.button,a.button-circle,.single-product .price .price-old,.special_countdown p,.list .item.single-product .price-tax, .form-control,label,.icon-element {font-size:' . $this->config->get('body_font_size_14') . ';}';
        $font_styles .= 'small,.form-control.input-sm,.shortcut-wrapper,.header5 .links > ul > li > a,.header5 .setting-ul > .setting-li > a,.breadcrumb,.sign-up-field .sign-up-respond span,.badge i,.special_countdown div i,.top_line {font-size:' . $this->config->get('body_font_size_13') . ';}';
        $font_styles .= '.tooltip,.links ul > li > a,.setting-ul > .setting-li > a,.table.products .product-name,#cart-content .totals, .main-menu.vertical > ul > li > a,.single-blog .banner_wrap .tags a,.bordered-list a {font-size:' . $this->config->get('body_font_size_12') . ';}';
        $font_styles .= 'h1, h2, h3, h4, h5, h6 {font-family:' . $this->config->get('headings_fam') . ';font-weight:' . $this->config->get('headings_weight') . ';}';
        $font_styles .= '.panel-group .panel-heading .panel-title, legend {font-size:' . $this->config->get('headings_size_sm') . ';}';
        $font_styles .= '.title_in_bc .login-area h2, .panel-body h2, h3.lined-title.lg, .grid1 .single-blog .blog-title, .grid2 .single-blog .blog-title {font-size:' . $this->config->get('headings_size_lg') . ';}';
        $font_styles .= 'h1, .product-info .table-cell.right h1#page-title {font-family:' . $this->config->get('h1_inline_fam') . ';font-size:' . $this->config->get('h1_inline_size') . ';font-weight:' . $this->config->get('h1_inline_weight') . ';text-transform:' . $this->config->get('h1_inline_trans') . ';letter-spacing:' . $this->config->get('h1_inline_ls') . ';}';
        $font_styles .= '.title_in_bc .breadcrumb-holder #title-holder {font-family:' . $this->config->get('h1_breadcrumb_fam') . ';}';

        $font_styles .= '.title_in_bc .breadcrumb-holder #title-holder #page-title, .title_in_bc.tall_height_bc .breadcrumb-holder #title-holder #page-title, .title_in_bc.extra_tall_height_bc .breadcrumb-holder #title-holder #page-title {font-size:' . $this->config->get('h1_breadcrumb_size') . ';font-weight:' . $this->config->get('h1_breadcrumb_weight') . ';text-transform:' . $this->config->get('h1_breadcrumb_trans') . ';letter-spacing:' . $this->config->get('h1_breadcrumb_ls') . ';}';
        $font_styles .= '.widget .widget-title .main-title {font-family:' . $this->config->get('widget_lg_fam') . ';font-size:' . $this->config->get('widget_lg_size') . ';font-weight:' . $this->config->get('widget_lg_weight') . ';text-transform:' . $this->config->get('widget_lg_trans') . ';letter-spacing:' . $this->config->get('widget_lg_ls') . ';}';
        $font_styles .= '.lang-curr-wrapper h4, .column .widget .widget-title .main-title, #footer h5, .dropdown-inner h4.column-title b, .blog_post .section-title {font-family:' . $this->config->get('widget_sm_fam') . ';font-size:' . $this->config->get('widget_sm_size') . ';font-weight:' . $this->config->get('widget_sm_weight') . ';text-transform:' . $this->config->get('widget_sm_trans') . ';letter-spacing:' . $this->config->get('widget_sm_ls') . ';}';
        $font_styles .= '.main-menu:not(.vertical) > ul > li > a > .top {font-family:' . $this->config->get('menu_font_fam') . ';font-size:' . $this->config->get('menu_font_size') . ';font-weight:' . $this->config->get('menu_font_weight') . ';text-transform:' . $this->config->get('menu_font_trans') . ';letter-spacing:' . $this->config->get('menu_font_ls') . ';}';
        $this->cache->set('oxyo_fonts_cache_store_' . $this->config->get('config_store_id'), $font_styles);
        $data['oxyo_fonts_cache'] = $this->cache->get('oxyo_fonts_cache_store_' . $this->config->get('config_store_id'));
    }
} else {
    $this->document->addStyle('//fonts.googleapis.com/css?family=Karla:400,400i,700,700i%7CLora:400,400i');
}
