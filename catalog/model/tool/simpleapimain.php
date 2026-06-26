<?php
/*
@author Dmitriy Kubarev
@link   http://www.simpleopencart.com
*/

class ModelToolSimpleApiMain extends Model {
    static $data = [];

    private $enableSimpleApiDebugLog = true; // Set to true to enable detailed SimpleApi debug logging

    private function logSimpleApiDebug($stage, $value, $context = array())
    {
        if (!$this->enableSimpleApiDebugLog) {
            return;
        }

        $string_value = (string)$value;
        $hex_preview = bin2hex(substr($string_value, 0, 64));
        $message = '[' . date('Y-m-d H:i:s') . '] [simpleapimain][simpleapi][' . $stage . '] value="' . $string_value . '" len=' . strlen($string_value) . ' hex64=' . $hex_preview;

        if (!empty($context)) {
            $message .= ' context=' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        $log_file = defined('DIR_LOGS') ? DIR_LOGS . 'error.log' : '';

        if ($log_file) {
            @file_put_contents($log_file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            error_log($message);
        }
    }    

    public function getCustomerGroups($filter = '') {
        $values = [];

        $version = explode('.', VERSION);
        $version = floatval($version[0] . $version[1] . $version[2] . '.' . (isset($version[3]) ? $version[3] : 0));

        $requiredGroupId = 0;

        if ($this->customer->isLogged()) {
            if ($version < 200) {
                $requiredGroupId = $this->customer->getCustomerGroupId();
            } else {
                $requiredGroupId = $this->customer->getGroupId();
            }
        }

        if (file_exists(DIR_APPLICATION . 'model/account/customer_group.php') && $version >= 153) {
            $this->load->model('account/customer_group');

            $checked = false;

            if ($this->config->get('simple_disable_method_checking')) {
                $checked = true;
            } else {
                if (method_exists($this->model_account_customer_group, 'getCustomerGroups') || property_exists($this->model_account_customer_group, 'getCustomerGroups') || (method_exists($this->model_account_customer_group, 'isExistForSimple') && $this->model_account_customer_group->isExistForSimple('getCustomerGroups'))) {
                    $checked = true;
                }
            }

            if ($checked) {
                $customerGroups = $this->model_account_customer_group->getCustomerGroups();

                $displayedGroups = $this->config->get('config_customer_group_display');

                if (!empty($displayedGroups) && is_array($displayedGroups)) {
                    foreach ($customerGroups as $customerGroup) {
                        if (in_array($customerGroup['customer_group_id'], $displayedGroups) || $customerGroup['customer_group_id'] == $requiredGroupId) {
                            $values[] = [
                                'id' => $customerGroup['customer_group_id'],
                                'text' => $customerGroup['name'],
                            ];
                        }
                    }
                } else {
                    foreach ($customerGroups as $customerGroup) {
                        $values[] = [
                            'id' => $customerGroup['customer_group_id'],
                            'text' => $customerGroup['name'],
                        ];
                    }
                }
            }
        } else {
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'customer_group');

            $displayedGroups = $this->config->get('simple_customer_group_display');

            if (!empty($displayedGroups) && is_array($displayedGroups)) {
                foreach ($query->rows as $row) {
                    if (in_array($row['customer_group_id'], $displayedGroups) || $row['customer_group_id'] == $requiredGroupId) {
                        $values[] = [
                            'id' => $row['customer_group_id'],
                            'text' => $row['name'],
                        ];
                    }
                }
            } else {
                foreach ($query->rows as $row) {
                    $values[] = [
                        'id' => $row['customer_group_id'],
                        'text' => $row['name'],
                    ];
                }
            }
        }

        return $values;
    }

    public function getCountries($filter = '') {
        $values = [
            [
                'id' => '',
                'text' => $this->language->get('text_select'),
            ],
        ];

        $this->load->model('localisation/country');

        $results = $this->model_localisation_country->getCountries();

        foreach ($results as $result) {
            $values[] = [
                'id' => $result['country_id'],
                'text' => $result['name'],
            ];
        }

        if (!$results) {
            $values[] = [
                'id' => 0,
                'text' => $this->language->get('text_none'),
            ];
        }

        return $values;
    }

    public function getZones($countryId) {
        $values = [
            [
                'id' => '',
                'text' => $this->language->get('text_select'),
            ],
        ];

        $this->load->model('localisation/zone');

        $results = $this->model_localisation_zone->getZonesByCountryId($countryId);

        foreach ($results as $result) {
            $values[] = [
                'id' => $result['zone_id'],
                'text' => $result['name'],
            ];
        }

        if (!$results) {
            $values[] = [
                'id' => 0,
                'text' => $this->language->get('text_none'),
            ];
        }

        return $values;
    }

    public function getCities($zoneId) {
        $values = [
            [
                'id' => '',
                'text' => $this->language->get('text_select'),
            ],
        ];

        $this->load->model('localisation/city');

        $results = $this->model_localisation_city->getCitiesByZoneId($zoneId);

        foreach ($results as $result) {
            $values[] = [
                'id' => $result['name'],
                'text' => $result['name'],
            ];
        }

        if (!$results) {
            $values[] = [
                'id' => 0,
                'text' => $this->language->get('text_none'),
            ];
        }

        return $values;
    }

    public function getCitiesFromGeo($zoneId) {
        $values = $this->cache->get('geo_cities.' . $zoneId);

        if (is_array($values)) {
            return $values;
        }

        $values = [
            [
                'id' => '',
                'text' => $this->language->get('text_select'),
            ],
        ];

        $geo_links = $this->config->get('simple_geo_links');

        if (!empty($geo_links)) {
            $geo_links = array_flip($geo_links);
        }

        if (!empty($geo_links) && !empty($geo_links[$zoneId])) {
            $zoneId = $geo_links[$zoneId];
        }

        $query = $this->db->query("SELECT * FROM simple_geo WHERE zone_id = '" . (int) $zoneId . "'");

        if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $values[] = [
                    'id' => $result['name'],
                    'text' => $result['fullname'],
                ];
            }
        } else {
            $values[] = [
                'id' => 0,
                'text' => $this->language->get('text_none'),
            ];
        }

        $this->cache->set('geo_cities.' . $zoneId, $values);

        return $values;
    }

    public function getYesNo($filter = '') {
        return [
            [
                'id' => '1',
                'text' => $this->language->get('text_yes'),
            ],
            [
                'id' => '0',
                'text' => $this->language->get('text_no'),
            ],
        ];
    }

    public function checkTelephoneForUniqueness($telephone, $register) {
        $telephone = trim($telephone);

        if ($telephone && (!$this->customer->isLogged() || ($this->customer->isLogged() && $telephone != $this->customer->getTelephone()))) {
            $query = $this->db->query('SELECT COUNT(*) AS total FROM ' . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($telephone) . "'");

            return $query->row['total'] > 0 ? false : true;
        }

        return true;
    }

    public function checkEmailForUniqueness($email, $register) {
        $email = trim($email);

        if ((!$this->customer->isLogged() && $register && $email) || ($this->customer->isLogged() && $email != $this->customer->getEmail())) {
            $this->load->model('account/customer');
            return $this->model_account_customer->getTotalCustomersByEmail($email) > 0 ? false : true;
        }

        return true;
    }

    public function checkCaptcha($value, $filter) {
        if (!empty($this->session->data['captcha_verified'])) {
            return true;
        }

        if ($this->config->get('simple_use_google_captcha') && $this->config->get('simple_captcha_secret_key')) {
            $g_recaptcha_response = isset($this->request->post['g-recaptcha-response']) ? $this->request->post['g-recaptcha-response'] : $value;

            if (!empty($g_recaptcha_response)) {
                $secret = $this->config->get('simple_captcha_secret_key');

                $recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret) . '&response=' . $g_recaptcha_response . '&remoteip=' . $this->request->server['REMOTE_ADDR']);

                $recaptcha = json_decode($recaptcha, true);

                if (!$recaptcha['success']) {
                    return false;
                } else {
                    $this->session->data['captcha_verified'] = true;

                    return true;
                }
            } else {
                return false;
            }
        } else {
            if (isset($this->session->data['captcha']) && $this->session->data['captcha'] == $value) {
                $this->session->data['captcha_verified'] = true;

                return true;
            }

            return false;
        }

        return true;
    }

    public function getDefaultGroup() {
        if ($this->customer->isLogged()) {
            $version = explode('.', VERSION);
            $version = floatval($version[0] . $version[1] . $version[2] . '.' . (isset($version[3]) ? $version[3] : 0));

            if ($version < 200) {
                return $this->customer->getCustomerGroupId();
            } else {
                return $this->customer->getGroupId();
            }
        } else {
            return $this->config->get('config_customer_group_id');
        }
    }

    public function getRandomPassword() {
        if (!empty(self::$data['password'])) {
            return self::$data['password'];
        }

        $eng = 'qwertyuiopasdfghjklzxcvbnm1234567890';
        $length = 6;
        $password = '';

        while ($length) {
            $password .= $eng[rand(0, 35)];
            $length--;
        }

        self::$data['password'] = $password;

        return $password;
    }

    public function getAddresses() {
        if ($this->customer->isLogged()) {
            $this->load->language('checkout/simplecheckout');
            $this->load->model('account/address');
            $this->load->model('tool/simplecustom');

            $result = [];

            $addresses = $this->model_account_address->getAddresses();
            $format = $this->config->get('simple_address_format');

            $find = ['{firstname}', '{lastname}', '{company}', '{address_1}', '{address_2}', '{city}', '{postcode}', '{zone}', '{zone_code}', '{country}', '{company_id}', '{tax_id}'];

            $result[] = [
                'id' => 0,
                'text' => $this->language->get('text_add_new'),
            ];

            foreach ($addresses as $address) {
                $replace = [
                    'firstname' => $address['firstname'],
                    'lastname' => $address['lastname'],
                    'company' => $address['company'],
                    'address_1' => $address['address_1'],
                    'address_2' => $address['address_2'],
                    'city' => $address['city'],
                    'postcode' => $address['postcode'],
                    'zone' => $address['zone'],
                    'zone_code' => $address['zone_code'],
                    'country' => $address['country'],
                ];

                $replace['company_id'] = isset($address['company_id']) ? $address['company_id'] : '';
                $replace['tax_id'] = isset($address['tax_id']) ? $address['tax_id'] : '';

                $customInfo = $this->model_tool_simplecustom->getCustomFields('address', $address['address_id']);

                foreach ($customInfo as $id => $value) {
                    $find[] = '{' . $id . '}';
                    $replace[$id] = $value;
                }

                $result[] = [
                    'id' => $address['address_id'],
                    'text' => str_replace($find, $replace, $format),
                ];
            }

            return $result;
        }

        return [];
    }

    public function getDefaultAddressId($filter) {
        if ($this->customer->isLogged()) {
            if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['address_id'])) {
                return $this->request->get['address_id'];
            }

            return $this->customer->getAddressId();
        }

        return 0;
    }

    public function isDefaultAddress($addressId) {
        if ($this->customer->isLogged()) {
            return $this->customer->getAddressId() == $addressId;
        }

        return false;
    }

    public function getDefaultCountry() {
        return '';
    }

    public function getDefaultZone() {
        return '';
    }

    public function getDefaultCity() {
        return '';
    }

    public function getDefaultPostcode() {
        return '';
    }

    // example of code for getting a mask of field
    public function getTelephoneMask($country_id) {
        switch ($country_id) {
            case 176:
                return '+7(999)9999999';
                break;
            case 220:
                return '+38(999)9999999';
                break;
        }
    }

    public function getZoneCapital($zone_id) {
        $zone_id = (int) $zone_id;

        if ($zone_id <= 0) {
            return '';
        }

        $query = $this->db->query('SELECT city_name, name FROM ' . DB_PREFIX . "xd_checkout_city WHERE zone_id = '" . $zone_id . "' ORDER BY is_center DESC, city_name ASC, name ASC LIMIT 1");

        if ($query->num_rows) {
            $city_name = isset($query->row['city_name']) ? trim((string) $query->row['city_name']) : '';

            if ($city_name !== '') {
                return $city_name;
            }

            $name = isset($query->row['name']) ? trim((string) $query->row['name']) : '';

            if ($name !== '') {
                return $name;
            }
        }

        return '';
    }

    public function getCityByZone($zone_id) {
        return $this->getZoneCapital($zone_id);
    }

    public function searchDadataAddresses($zone, $query, $accountIndex = 0) {
        $zone = $this->normalizeZoneName($zone);
        $query = trim($query);

        $this->logSimpleApiDebug('searchDadataAddresses', 'Received query', ['query' => $query, 'zone' => $zone, 'accountIndex' => $accountIndex]);
        
        if (strlen($query) < 3) {
            $this->logSimpleApiDebug('searchDadataAddresses', 'Query too short', ['query' => $query, 'zone' => $zone, 'accountIndex' => $accountIndex]);
            return [];
        }

        // Get Dadata credentials from config
        $dadata_accounts = $this->config->get('oxyo_dadata_accounts') ?: [];
        
        if (!isset($dadata_accounts[$accountIndex])) {
            $this->logSimpleApiDebug('searchDadataAddresses', 'Invalid account index, defaulting to 0', ['zone' => $zone, 'accountIndex' => $accountIndex]);
            $accountIndex = 0;
        }
        
        if (!isset($dadata_accounts[$accountIndex]) || empty($dadata_accounts[$accountIndex]['api_key'])) {
            $this->logSimpleApiDebug('searchDadataAddresses', 'Missing Dadata API credentials for account index', ['zone' => $zone, 'accountIndex' => $accountIndex]);
            return [];
        }

        $token = $dadata_accounts[$accountIndex]['api_key'];
        $secret = $dadata_accounts[$accountIndex]['secret_key'];

        if (!$token || !$secret) {
            $this->logSimpleApiDebug('searchDadataAddresses', 'Missing Dadata API credentials', ['zone' => $zone, 'accountIndex' => $accountIndex]);
            return [];
        }

        try {
            // Call Dadata API
            $response = $this->callDadataApi('suggest/address', [
                'query' => $query,
                'count' => 5,
                // 'language' => 'en'
                'locations' => [
                    [
                        'region' => $zone, // Assuming $zone is the FIAS ID of the region
                    ],
                ],
            ], $token, $secret);

            if ($response && isset($response['suggestions'])) {
                $results = [];
                
                foreach ($response['suggestions'] as $suggestion) {
                    $data = $suggestion['data'];
                    $results[] = [
                        'value' => $suggestion['value'],
                        'unrestricted_value' => $suggestion['unrestricted_value'],
                        'postal_code' => isset($data['postal_code']) ? $data['postal_code'] : '',
                        'country' => isset($data['country']) ? $data['country'] : '',
                        'region' => isset($data['region_with_type']) ? $data['region_with_type'] : '',
                        'region_type_full' => isset($data['region_type_full']) ? $data['region_type_full'] : '',
                        'area' => isset($data['area_with_type']) ? $data['area_with_type'] : '',
                        'area_type_full' => isset($data['area_type_full']) ? $data['area_type_full'] : '',
                        'city' => isset($data['city_with_type']) ? $data['city_with_type'] : '',
                        'city_type_full' => isset($data['city_type_full']) ? $data['city_type_full'] : '',
                        'settlement' => isset($data['settlement_with_type']) ? $data['settlement_with_type'] : '',
                        'settlement_type_full' => isset($data['settlement_type_full']) ? $data['settlement_type_full'] : '',
                        'street' => isset($data['street_with_type']) ? $data['street_with_type'] : '',
                        'street_type_full' => isset($data['street_type_full']) ? $data['street_type_full'] : '',
                        'house' => isset($data['house']) ? $data['house'] : '',
                        'flat' => isset($data['flat']) ? $data['flat'] : '',
                        'geo_lat' => isset($data['geo_lat']) ? $data['geo_lat'] : '',
                        'geo_lon' => isset($data['geo_lon']) ? $data['geo_lon'] : '',
                    ];
                }
                
                return $results;
            }
        } catch (Exception $e) {
            $this->logSimpleApiDebug('searchDadataAddresses', 'Error calling Dadata API: ' . $e->getMessage(), ['zone' => $zone, 'accountIndex' => $accountIndex]);
        }

        return [];
    }

    private function normalizeZoneName(string $zone) {
        // Normalize the zone name to match Dadata's expected format
        // This may involve mapping local names to Dadata's region names or FIAS IDs
        // For now, we will just return the zone as is, but this can be extended as needed
        $zone = trim($zone);
        $zone = mb_strtolower($zone, 'UTF-8');
        if (empty($zone)) {
            return 'Санкт-Петербург'; // Default to a known region if zone is empty
        }
        // Remove область, край, республика, etc. from the zone name for better matching
        if (preg_match('/югра/i', $zone)) {
            $zone = 'Ханты-Мансийский Автономный округ - Югра';
        }
        if (preg_match('/байконур/i', $zone)) {
            $zone = 'байконур';
        }
        $zone = preg_replace('/народная республика/iu', '', $zone);
        $zone = preg_replace('/(область|край|республика| ао|г\.|город)/iu', '', $zone);
        return trim($zone);
    }

    private function callDadataApi($method, $params, $token, $secret) {
        // POST https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address

        $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/' . $method;
        
        $options = [
            'http' => [
                'header' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Token ' . $token,
                    'X-Secret: ' . $secret
                ],
                'method' => 'POST',
                'content' => json_encode($params),
                'timeout' => 5
            ]
        ];

        $context = stream_context_create($options);
        
        try {
            $response = @file_get_contents($url, false, $context);
            $this->logSimpleApiDebug('callDadataApi', 'API call made', ['method' => $method, 'params' => $params, 'response' => $response]);
            if ($response === false) {
                return null;
            }
            
            return json_decode($response, true);
        } catch (Exception $e) {
            $this->logSimpleApiDebug('callDadataApi', 'Exception: ' . $e->getMessage(), ['method' => $method, 'params' => $params]);
            return null;
        }
    }
}
