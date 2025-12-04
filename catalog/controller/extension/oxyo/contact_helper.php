<?php
$lang_id = $this->config->get('config_language_id');

// Language
$this->load->language('oxyo/oxyo_theme');
$data['text_directions'] = $this->language->get('text_directions');

$data['main_email'] = $this->config->get('config_email');

$data['contact_form'] = $this->config->get('contact_form');
$contact_agree_status = $this->config->get('contact_agree_status');
if ($contact_agree_status) {
    $this->load->model('catalog/information');
    $information_info = $this->model_catalog_information->getInformation($contact_agree_status);
    if ($information_info) {
        $contact_agree_title = $information_info['title'];

        $data['contact_agree_status'] = true;
        $data['entry_agreement'] = sprintf($this->language->get('oxyo_text_agree'), $this->url->link('information/information/agree', 'information_id=' . $contact_agree_status, true), $contact_agree_title);
    }
}

if (isset($this->error['agreement'])) {
    $data['error_agreement'] = $this->error['agreement'];
} else {
    $data['error_agreement'] = '';
}


$data['oxyo_map_style'] = $this->config->get('oxyo_map_style');
$data['oxyo_map_lat'] = $this->config->get('oxyo_map_lat');
$data['oxyo_map_lon'] = $this->config->get('oxyo_map_lon');

$data['oxyo_yandex_map'] = html_entity_decode($this->config->get('oxyo_yandex_map'));
$data['oxyo_yandex_scheme'] = $this->config->get('oxyo_yandex_scheme');

if ($this->config->get('oxyo_map_style')) {
    $this->document->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.24&key=' . $this->config->get('oxyo_map_api') . '');
    $this->document->addScript('catalog/view/theme/oxyo/js/maplace.min.js');
}


// Contacts
$contact_array = array();

/**
 * Contacts configuration map.
 * Each entry describes how to read and transform a contact value.
 */
$contacts_config = array(
    'main_address' => array(
        'config_key'    => 'main_address',
        'icon'          => '',
        'icon_svg'      => '',
        'link_type'     => '',
        'onclick'       => '',
    ),
    'directions' => array(
        'config_key'        => 'oxyo_yandex_scheme',
        'icon'              => '',
        'icon_svg'          => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8z"/> <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/></svg>',
        'link_type'         => 'yandex_map',
        'onclick'           => '',
        'use_language_text' => true,
        'language_key'      => 'text_directions',
    ),
    'working_hours' => array(
        'config_key'    => 'working_hours',
        'icon'          => '<i class="bi bi-clock"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>',
        'link_type'     => 'none',
        'onclick'       => '',
    ),
    'main_phone' => array(
        'config_key'    => 'main_phone',
        'icon'          => '<i class="bi bi-telephone"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>',
        'link_type'     => 'tel',
        'onclick'       => '',
    ),
    'main_email' => array(
        'config_key'    => 'main_email',
        'icon'          => '<i class="bi bi-envelope"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="24" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/></svg>',
        'link_type'     => 'mailto',
        'onclick'       => '',
    ),
    'callback' => array(
        'config_key'        => 'callback',
        'icon'              => '',
        'icon_svg'          => '<svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 16 16" fill="currentColor"><path d="M10 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM6 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"></path><path d="M8 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2M1.599 4.058a.5.5 0 0 1 .208.676A7 7 0 0 0 1 8c0 1.18.292 2.292.807 3.266a.5.5 0 0 1-.884.468A8 8 0 0 1 0 8c0-1.347.334-2.619.923-3.734a.5.5 0 0 1 .676-.208m12.802 0a.5.5 0 0 1 .676.208A8 8 0 0 1 16 8a8 8 0 0 1-.923 3.734.5.5 0 0 1-.884-.468A7 7 0 0 0 15 8c0-1.18-.292-2.292-.807-3.266a.5.5 0 0 1 .208-.676M3.057 5.534a.5.5 0 0 1 .284.648A5 5 0 0 0 3 8c0 .642.12 1.255.34 1.818a.5.5 0 1 1-.93.364A6 6 0 0 1 2 8c0-.769.145-1.505.41-2.182a.5.5 0 0 1 .647-.284m9.886 0a.5.5 0 0 1 .648.284C13.855 6.495 14 7.231 14 8s-.145 1.505-.41 2.182a.5.5 0 0 1-.93-.364C12.88 9.255 13 8.642 13 8s-.12-1.255-.34-1.818a.5.5 0 0 1 .283-.648"></path></svg>',
        'link_type'         => 'callback',
        'onclick'           => 'callBackShow(event);',
        'use_language_text' => true,
        'language_key'      => 'text_callback',
    ),
    'max' => array(
        'config_key'    => 'max',
        'icon'          => '',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 42 42" fill="none" class="svelte-1qnk2y9"><path d="M21.47 41.88c-4.11 0-6.02-.6-9.34-3-2.1 2.7-8.75 4.81-9.04 1.2 0-2.71-.6-5-1.28-7.5C1 29.5.08 26.07.08 21.1.08 9.23 9.82.3 21.36.3c11.55 0 20.6 9.37 20.6 20.91a20.6 20.6 0 0 1-20.49 20.67m.17-31.32c-5.62-.29-10 3.6-10.97 9.7-.8 5.05.62 11.2 1.83 11.52.58.14 2.04-1.04 2.95-1.95a10.4 10.4 0 0 0 5.08 1.81 10.7 10.7 0 0 0 11.19-9.97 10.7 10.7 0 0 0-10.08-11.1Z" clip-rule="evenodd"/></svg>',
        'link_type'     => 'plain',
        'onclick'       => '',
    ),
    'whatsapp' => array(
        'config_key'    => 'whatsapp',
        'icon'          => '<i class="bi bi-whatsapp"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>',
        'link_type'     => 'whatsapp',
        'onclick'       => '',
    ),
    'telegram' => array(
        'config_key'    => 'telegram',
        'icon'          => '<i class="bi bi-telegram"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-telegram" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/></svg>',
        'link_type'     => 'telegram',
        'onclick'       => '',
    ),
    'vkontakte' => array(
        'config_key'    => 'vkontakte',
        'icon'          => '',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 68 43" fill="currentColor"><path d="M37.2082 42.042C14.4165 42.042 1.41667 26.417 0.875 0.416992H12.2916C12.6666 19.5003 21.0831 27.5836 27.7498 29.2503V0.416992H38.5001V16.8752C45.0835 16.1669 51.9993 8.66699 54.3326 0.416992H65.083C63.2913 10.5837 55.7913 18.0836 50.4579 21.1669C55.7913 23.6669 64.3334 30.2086 67.5834 42.042H55.7497C53.208 34.1253 46.8751 28.0003 38.5001 27.1669V42.042H37.2082Z"/></svg>',
        'link_type'     => 'plain',
        'onclick'       => '',
    ),
    'instagram' => array(
        'config_key'    => 'instagram',
        'icon'          => '<i class="bi bi-instagram"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 4.5a.5.5 0 1 1-1 .001A1.5 1.5 0 1 0 10.5 6a.5.5 0 1 1 .001-1A2.5 2.5 0 1 1 11.5 4.5zM8 .5a7.5 7.5 0 1 1-.001 15A7.5 7.5 0 0 1 8 .5z"/><path d="M11.5 7a2.5 2.5 0 1 1-4.999-.001A2.5 2.5 0 0 1 11.5 7z"/></svg>',
        'link_type'     => 'plain',
        'onclick'       => '',
    ),
    'tiktok' => array(
        'config_key'    => 'tiktok',
        'icon'          => '<i class="bi bi-tiktok"></i>',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16"><path d="M9.5 0a2.5 2.5 0 0 1 2.5 2.5v4a2.5 2.5 0 0 1-2.5-2.5V0z"/><path d="M8 .5a7.5 7.5 0 1 1-.001 15A7.5 7.5 0 0 1 8 .5zm3.5-.001a3.5 3.5 0 1 1-7 .002A3.5 3.5 0 0 1 11.5.499z"/></svg>',
        'link_type'     => 'plain',
        'onclick'       => '',
    ),
    'avito' => array(
        'config_key'    => 'avito',
        'icon'          => '',
        'icon_svg'      => '<svg xmlns="http://www.w3.org/2000/svg" width="62" height="22" viewBox="440 0 1054 380" fill="currentColor"><g clip-path="url(#clip0_1542_6)"><path d="M571.081 8.93481L442.188 345.694H511.456L538.205 275.36H675.054L701.676 345.694H770.418L642.003 8.93481H571.081ZM561.676 213.555L606.773 95.054L651.615 213.555H561.676Z" fill="currentColor"></path><path d="M1371.41 104.856C1347.17 104.856 1323.48 112.042 1303.33 125.506C1283.18 138.969 1267.48 158.106 1258.21 180.494C1248.93 202.883 1246.51 227.519 1251.23 251.287C1255.96 275.055 1267.63 296.888 1284.77 314.023C1301.9 331.159 1323.73 342.829 1347.5 347.557C1371.27 352.284 1395.91 349.858 1418.3 340.584C1440.68 331.31 1459.82 315.606 1473.28 295.456C1486.75 275.307 1493.93 251.617 1493.93 227.384C1493.93 194.887 1481.02 163.722 1458.05 140.744C1435.07 117.765 1403.9 104.856 1371.41 104.856ZM1371.41 287.088C1359.6 287.088 1348.06 283.587 1338.25 277.029C1328.43 270.47 1320.78 261.148 1316.26 250.241C1311.74 239.335 1310.56 227.333 1312.87 215.755C1315.17 204.177 1320.85 193.541 1329.2 185.194C1337.55 176.846 1348.18 171.161 1359.76 168.858C1371.34 166.555 1383.34 167.737 1394.25 172.255C1405.15 176.772 1414.48 184.423 1421.04 194.238C1427.59 204.054 1431.09 215.594 1431.09 227.4C1431.11 235.241 1429.57 243.009 1426.58 250.256C1423.58 257.503 1419.19 264.088 1413.64 269.633C1408.1 275.179 1401.51 279.575 1394.26 282.57C1387.02 285.565 1379.25 287.1 1371.41 287.088Z" fill="currentColor"></path><path d="M852.957 258.843L797.008 109.105H730.939L820.989 345.694H886.533L974.975 109.105H908.906L852.957 258.843Z" fill="currentColor"></path><path d="M1182.78 46.234H1119.91V109.105H1083.15V165.595H1119.91V266.306C1119.91 323.321 1151.35 347.826 1195.57 347.826C1210.57 348.043 1225.45 345.138 1239.27 339.297V280.691C1231.75 283.463 1223.82 284.932 1215.81 285.035C1196.72 285.035 1182.78 277.572 1182.78 252V165.595H1239.27V109.105H1182.78V46.234Z" fill="currentColor"></path><path d="M1026.66 92.0625C1051.97 92.0625 1072.49 71.5445 1072.49 46.2341C1072.49 20.9238 1051.97 0.405762 1026.66 0.405762C1001.35 0.405762 980.83 20.9238 980.83 46.2341C980.83 71.5445 1001.35 92.0625 1026.66 92.0625Z" fill="currentColor"></path><path d="M1058.1 109.105H995.232V345.694H1058.1V109.105Z" fill="currentColor"></path><path d="M122.965 379.27C190.652 379.27 245.524 324.398 245.524 256.711C245.524 189.023 190.652 134.152 122.965 134.152C55.2778 134.152 0.40625 189.023 0.40625 256.711C0.40625 324.398 55.2778 379.27 122.965 379.27Z" fill="#04E061"></path><path d="M335.574 363.803C376.475 363.803 409.631 330.646 409.631 289.745C409.631 248.844 376.475 215.688 335.574 215.688C294.673 215.688 261.516 248.844 261.516 289.745C261.516 330.646 294.673 363.803 335.574 363.803Z" fill="#FF4053"></path><path d="M146.404 118.175C171.715 118.175 192.233 97.6569 192.233 72.3466C192.233 47.0363 171.715 26.5182 146.404 26.5182C121.094 26.5182 100.576 47.0363 100.576 72.3466C100.576 97.6569 121.094 118.175 146.404 118.175Z" fill="#965EEB"></path><path d="M306.803 199.696C361.835 199.696 406.448 155.083 406.448 100.051C406.448 45.0183 361.835 0.405762 306.803 0.405762C251.77 0.405762 207.158 45.0183 207.158 100.051C207.158 155.083 251.77 199.696 306.803 199.696Z" fill="#00AAFF"></path></g></svg>',
        'link_type'     => 'plain',
        'onclick'       => '',
    ),
);

/**
 * Helper to build responsive display classes for contact
 * based on *_contact_xs, *_contact_md, *_contact_lg, *_contact_xl flags.
 *
 * Logic:
 * - xs controls base `d-none` / `d-flex`
 * - for each next breakpoint, if value differs from previous,
 *   we add `d-{bp}-flex` or `d-{bp}-none` to override.
 */
$contact_breakpoints = array('xs', 'md', 'lg', 'xl');

foreach ($contacts_config as $name => $cfg) {
    $raw = $this->config->get($cfg['config_key']);

    // For boolean-like fields (callback) skip empty/'0'
    if (!$raw || $raw === '' || $raw === '0') {
        continue;
    }

    // Value from config
    $value = trim($raw);

    // For items using language text instead of config value
    if (!empty($cfg['use_language_text'])) {
        $lang_key = !empty($cfg['language_key']) ? $cfg['language_key'] : 'text_' . $name;
        $value    = $this->language->get($lang_key);
        $data[$lang_key] = $value; // backward compatibility
    }

    // Build link
    $link = '';

    switch ($cfg['link_type']) {
        case 'tel':
            if (substr($value, 0, 1) === '+' || substr($value, 0, 1) === '7') {
                $link = 'tel:+' . preg_replace('/[^0-9]/', '', $value);
            } else {
                $link = 'tel:' . preg_replace('/[^0-9]/', '', $value);
            }
            break;

        case 'whatsapp':
            $link = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $value);
            break;

        case 'telegram':
            if (substr($value, 0, 1) === '@') {
                $link = 'https://t.me/' . substr($value, 1);
            } else {
                $link = 'https://t.me/' . $value;
            }
            break;

        case 'mailto':
            $link = 'mailto:' . preg_replace('/[^0-9a-zA-Z@._-]/', '', $value);
            break;

        case 'yandex_map':
            $yandex_scheme = $this->config->get('oxyo_yandex_scheme');
            if ($yandex_scheme != '') {
                $link = $yandex_scheme;
            } else {
                $link = 'https://yandex.ru/maps/?text=' . urlencode($value);
            }
            break;

        case 'callback':
            $link = '#';
            break;

        case 'plain':
            $link = $value;
            break;

        case 'none':
        default:
            $link = '';
            break;
    }

    $onclick = !empty($cfg['onclick']) ? $cfg['onclick'] : '';

    // Read contact_* breakpoint flags and build responsive classes
    $bp_values = array();
    foreach ($contact_breakpoints as $bp) {
        // config key: main_phone_contact_xs, whatsapp_contact_md, etc.
        $key = $name . '_contact_' . $bp;
        $flag = $this->config->get($key);
        $bp_values[$bp] = ($flag && $flag == '1') ? 1 : 0;

        // expose flags to view if needed
        $data[$key] = $bp_values[$bp];
    }

    // Build Bootstrap 5 display classes
    $display_classes = array();

    // Base (xs)
    if ($bp_values['xs']) {
        $display_classes[] = 'd-flex';
    } else {
        $display_classes[] = 'd-none';
    }

    // md / lg / xl overrides, only when value changes from previous breakpoint
    $prev = $bp_values['xs'];

    // md
    if ($bp_values['md'] !== $prev) {
        $display_classes[] = $bp_values['md'] ? 'd-md-flex' : 'd-md-none';
    }
    $prev = $bp_values['md'];

    // lg
    if ($bp_values['lg'] !== $prev) {
        $display_classes[] = $bp_values['lg'] ? 'd-lg-flex' : 'd-lg-none';
    }
    $prev = $bp_values['lg'];

    // xl
    if ($bp_values['xl'] !== $prev) {
        $display_classes[] = $bp_values['xl'] ? 'd-xl-flex' : 'd-xl-none';
    }

    // Common wrapper class for contact icon element
    $wrapper_base_class = 'icon-element menu-element';
    $contact_display_class = $wrapper_base_class . ' ' . implode(' ', $display_classes);

    if ($name === 'callback') {
        // Additional class for callback to identify it in JS
        $contact_display_class .= ' callback_action d-none d-md-flex animate-text animate-splash';
    }

    // Expose to view
    $data[$name]                    = $value;
    $data[$name . '_link']          = $link;
    $data[$name . '_onclick']       = $onclick;
    $data[$name . '_contact_class']  = $contact_display_class;

    $contact_array[] = array(
        'name'           => $name,
        'icon'           => $cfg['icon'],
        'icon_svg'       => $cfg['icon_svg'],
        'value'          => $value,
        'link'           => $link,
        'onclick'        => $onclick,
        'contact_class'   => $contact_display_class,
        'contact_flags'   => $bp_values, // если хочешь использовать в Twig по-отдельности
    );
}
$data['contact_array'] = $contact_array;
