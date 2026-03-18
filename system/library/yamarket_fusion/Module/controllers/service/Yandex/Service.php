<?php
namespace yamarket_fusion\Module\controllers\service\Yandex;

use yamarket_fusion\API\Yandex\Market\Partner\PartnerClient;
use yamarket_fusion\Helpers\ServiceData;
use yamarket_fusion\Helpers\ArrayHelper;

class Service extends \yamarket_fusion\Module\controllers\module\Feed {
	const REQUEST_DELETE_OFFERS_LIMIT = PartnerClient::requestLimitOffers;
	const REQUEST_UPDATE_OFFERS_LIMIT = PartnerClient::requestLimitOffers;

	// todo log methods data and errors

	//public function __construct($registry) {
	//	parent::__construct($registry);
	//}

	protected function makeUpApiResult($result) {
		$output = new ServiceData($result->data);

		foreach ($result->errors as $error) {
			// todo locale error
			$output->addError("Ошибка API Yandex: code - {$error['code']}, message - {$error['message']}");
		}

		return $output;
	}

	public function updateOffersPrice($offers, $setting_profile_id) {
		$output = new ServiceData();
		$updated_offer_ids = array();

		set_time_limit(0);

		$partnerClient = new PartnerClient($this->setting('market_api_client', '', $setting_profile_id), $this->setting('market_api_token', '', $setting_profile_id));
		
		$partnerClient->shop_id = $this->setting('market_shop_id', '', $setting_profile_id);

		// todo get Limit form from api for current shop id
		$limit = PartnerClient::updateOfferPricesLimitPerTime > PartnerClient::requestLimitOffers ? (PartnerClient::requestLimitOffers) : (PartnerClient::updateOfferPricesLimitPerTime);
		$all_offers = array_chunk($offers, $limit);

		foreach ($all_offers as $i => $offers_part) {
			$params = array();
			
			foreach ($offers_part as $offer) {
				$offer_data = array(
					'feed' => array('id' => (int)$this->setting('market_shop_feed_id', 0, $setting_profile_id)), // todo validate use from setting of from arguments of $offers
					'id' => $offer['offer_id'],
					'price' => array(
						'currencyId' => 'RUR',
					)
				);

				if (isset($offer['price'])) {
					$offer_data['price']['value'] = (float)$offer['price'];
				}

				if (isset($offer['special_price'])) {
					$offer_data['price']['discountBase'] = (float)$offer['special_price'];
				}

				if (!empty($offer['delete'])) {
					$offer_data['delete'] = true;
					unset($offer_data['price']);
				}

				$params['offers'][] = $offer_data;
			}

			try {
				$result = $partnerClient->updateOfferPrices($params);

				if ($result->errors) {
					$output->addErrors($result->errors);
					// todo log error
					file_put_contents(DIR_LOGS . $this->config->get(self::s('errors_filename')), date('H:i:s d-m-Y') . " - " . implode(';', array_map(function($el) {return $el['code'] . ' - ' . $el['message'];}, $result->errors)) . PHP_EOL, FILE_APPEND);
				}
				else {
					$updated_offer_ids = array_merge($updated_offer_ids, $offers_part);
				}
			}
			catch (\Exception $e) {
				// todo log method
				file_put_contents(DIR_LOGS . $this->config->get(self::s('errors_filename')), date('H:i:s d-m-Y') . " - {$e->getMessage()}" . PHP_EOL, FILE_APPEND);
			}

			if (count($all_offers) > ($i + 1) && $limit == PartnerClient::updateOfferPricesLimitPerTime) {
				// Yandex requests limit
				sleep(PartnerClient::updateOfferPricesLimitTime / 1000);
			}
		}

		$output->setData($updated_offer_ids);
		
		return $output;
	}

	public function deleteOffersPrice($updated_market_products, $setting_profile_id) {
		$feed_id = $this->setting('market_shop_feed_id', 0, $setting_profile_id);
		
		$offers = array_map(function($el) use ($feed_id) {
			return array(
				'offer_id' => $el['offer_id'],
				'delete' => true,
				'feed' => array('id' => $feed_id)
			);
		}, $updated_market_products);
		
		$result = $this->updateOffersPrice($offers, $setting_profile_id);

		return $result;
	}
	
	public function refreshPricelist($setting_profile_id) {
		$refresh_time = $this->setting('market_yandex_api_refresh_time', array(), 0);
		$refresh = true;
		$feed_id = $this->setting('market_shop_feed_id', '', $setting_profile_id);

		// Check for limits
		if (isset($refresh_time[$feed_id])) {
			$time = 0;
			for ($i = count($refresh_time[$feed_id]); $i >= 0; $i--) {
				if (isset($refresh_time[$feed_id][$i - 2])) {
					$time += $refresh_time[$feed_id][$i - 1]['ts'] - $refresh_time[$feed_id][$i - 2]['ts'];
				}
			}

			if (count($refresh_time[$feed_id]) >= PartnerClient::refreshLimitRequestPerTime 
				&& $time
				&& $time < PartnerClient::refreshLimitTime / 1000
			) {
				$response = new ServiceData();
				$response->addError("Error: Yandex refresh limit overrun for feed ID {$feed_id}, profile ID {$setting_profile_id}");
				return $response;
			}
		}
		else {
			$refresh_time[$feed_id] = array();
		}
		
		$partnerClient = new PartnerClient($this->setting('market_api_client', '', $setting_profile_id), $this->setting('market_api_token', '', $setting_profile_id));
		
		$partnerClient->shop_id = $this->setting('market_shop_id', '', $setting_profile_id);
		
		$result = $partnerClient->refreshPricelist($feed_id);
		$output = $this->makeUpApiResult($result);

		if (!$output->getErrors()) {
			if (count($refresh_time[$feed_id]) >= PartnerClient::refreshLimitRequestPerTime) {
				$refresh_time[$feed_id] = array_slice($refresh_time[$feed_id], PartnerClient::refreshLimitRequestPerTime - 1);
			}

			array_push($refresh_time[$feed_id], array('ts' => time()));
			$this->model_setting->editSettingValue('market_yandex_api_refresh_time', $refresh_time);
		}
		
		return $output;
	}

	public function getUpdatedOffers($setting_profile_id) {
		$page = 1;
		$req_loop = false;
		$offers = array();

		// todo model rewrite to service or not
		$this->loadModel('module');

		$partnerClient = new PartnerClient($this->setting('market_api_client', '', $setting_profile_id), $this->setting('market_api_token', '', $setting_profile_id));
		$partnerClient->shop_id = $this->setting('market_shop_id', '', $setting_profile_id);

		$output = new ServiceData();

		do {
			$response = $partnerClient->getUpdatedOfferPrices($page);
			
			if (!$response->errors) {
				// todo check array_merge for empty array();
				$offers = array_merge($offers, $response->data['offers']);

				// check for next request
				if (count($response->data['offers']) < $response->data['total']) {
					$req_loop = true;
					$page++;
				}
				else {
					$req_loop = false;
				}
			}
			else {
				foreach ($response->errors as $error) {
					// todo get language template for error
					$output->addError("Ошибка API Yandex: code - {$error['code']}, message - {$error['message']}");
				}

				// todo need? add error 'added not all offers'

				break;
			}
		} while ($req_loop);

		$output->setData($offers);

		return $output;
	}

	public function saveUpdatedOffers($offers, $profile_id) {
		$this->loadModel('module');

		$update_data = array();

		$offer_aliases = $this->model_module->getOffersAliases($profile_id, array('filter_alias' => ArrayHelper::getColumn($offers, 'id')));
		$offer_aliases = ArrayHelper::index($offer_aliases, 'alias');
		
		foreach ($offers as $offer) {
			$format = 'Y-m-d\TH:i:s.uO';
			$offer_date = \DateTime::createFromFormat($format, $offer['updatedAt']);
			$offer_date->setTimezone(new \DateTimeZone('UTC'));
			
			$offer_data = array(
				'product_id' => isset($offer_aliases[$offer['id']]) ? $offer_aliases[$offer['id']]['offer_id'] : (int)$offer['id'],
				'offer_id' => $offer['id'],
				'price' => $offer['price']['value'],
				'date_modified' => $offer_date->format("Y-m-d H:i:s"),
			);

			if (isset($offer['price']['discountBase'])) {
				$offer_data['special_price'] = $offer_data['price'];
				$offer_data['price'] = $offer['price']['discountBase'];
			}
			
			$update_data[] = $offer_data;
		}

		if ($update_data) {
			$this->model_module->addUpdatedOffers($update_data, $profile_id);
		}
	}
}