<?php
class ModelExtensionXdCheckoutPaymentAddress extends Model
{
    public function getCitiesByName($filter_city, $limit = 15)
    {
        $cities = array();
        $country_cache = array();
        $zone_cache = array();

        $filter_city = trim((string)$filter_city);
        $limit = (int)$limit;

        if (utf8_strlen($filter_city) < 2) {
            return $cities;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        if ($limit > 50) {
            $limit = 50;
        }

        $sql = "SELECT name, cityName, regionName FROM `" . DB_PREFIX . "cdek_city` WHERE cityName LIKE '" . $this->db->escape($filter_city) . "%' ORDER BY cityName ASC LIMIT " . $limit;

        $query = $this->db->query($sql);

        foreach ($query->rows as $row) {
            $country_name = $this->extractCountryName($row['name']);
            $country_id = $this->resolveCountryId($country_name, $country_cache);
            $zone_id = $this->resolveZoneId($row['regionName'], $country_id, $zone_cache);

            $cities[] = array(
                'city' => $row['cityName'],
                'region' => $row['regionName'],
                'country' => $country_name,
                'country_id' => $country_id,
                'zone_id' => $zone_id,
            );
        }

        return $cities;
    }

    private function extractCountryName($full_name)
    {
        $full_name = trim((string)$full_name);

        if ($full_name === '') {
            return '';
        }

        $parts = array_map('trim', explode(',', $full_name));

        if (count($parts) >= 3) {
            return (string)end($parts);
        }

        return '';
    }

    private function resolveCountryId($country_name, &$country_cache)
    {
        $country_name = trim((string)$country_name);

        if (isset($country_cache[$country_name])) {
            return $country_cache[$country_name];
        }

        $country_id = 0;

        if ($country_name !== '') {
            $country_query = $this->db->query("SELECT country_id FROM `" . DB_PREFIX . "country` WHERE LCASE(name) = '" . $this->db->escape(utf8_strtolower($country_name)) . "' LIMIT 1");

            if ($country_query->num_rows) {
                $country_id = (int)$country_query->row['country_id'];
            }
        }

        if (!$country_id && ($country_name === '' || mb_stripos($country_name, 'рос') !== false || mb_stripos($country_name, 'russia') !== false)) {
            $country_query = $this->db->query("SELECT country_id FROM `" . DB_PREFIX . "country` WHERE iso_code_2 = 'RU' LIMIT 1");

            if ($country_query->num_rows) {
                $country_id = (int)$country_query->row['country_id'];
            }
        }

        if (!$country_id) {
            $country_id = (int)$this->config->get('config_country_id');
        }

        $country_cache[$country_name] = $country_id;

        return $country_id;
    }

    private function resolveZoneId($region_name, $country_id, &$zone_cache)
    {
        $region_name = trim((string)$region_name);
        $country_id = (int)$country_id;
        $cache_key = $country_id . ':' . $region_name;

        if (isset($zone_cache[$cache_key])) {
            return $zone_cache[$cache_key];
        }

        $zone_id = 0;

        if ($region_name !== '' && $country_id > 0) {
            $zone_query = $this->db->query("SELECT zone_id FROM `" . DB_PREFIX . "zone` WHERE country_id = '" . $country_id . "' AND LCASE(name) = '" . $this->db->escape(utf8_strtolower($region_name)) . "' LIMIT 1");

            if ($zone_query->num_rows) {
                $zone_id = (int)$zone_query->row['zone_id'];
            } else {
                $zone_query = $this->db->query("SELECT zone_id FROM `" . DB_PREFIX . "zone` WHERE country_id = '" . $country_id . "' AND name LIKE '" . $this->db->escape($region_name) . "%' LIMIT 1");

                if ($zone_query->num_rows) {
                    $zone_id = (int)$zone_query->row['zone_id'];
                }
            }
        }

        $zone_cache[$cache_key] = $zone_id;

        return $zone_id;
    }
}
