<?php
namespace yamarket_fusion\API\Yandex\Market\Partner;

use yamarket_fusion\Http\HttpClient;

class PartnerClient extends \yamarket_fusion\API\Yandex\Common\Client {
	public $client_id, $shop_id;
	public $service_domain = 'api.partner.market.yandex.ru';
	public $version = 'v2';

	const requestLimitOffers = 2000;
	const updateOfferPricesLimitTime = 1000; // millisecond
	const updateOfferPricesLimitPerTime = 50; // min; max - по формуле (количество предложений магазина) / 200
	const refreshLimitTime = 3600000; // millisecond
	const refreshLimitRequestPerTime = 3;
 
	//private $errors = array();

	public function __construct($client_id, $token) {
		$this->access_token = $token;
		$this->client_id = $client_id;

		// todo validate place to set headers
		$this->getClient()->addHeaders(['Content-Type' => HttpClient::CONTENT_TYPE_JSON]);
	}

	public function getAccessToken() {
		return 'oauth_token=' . $this->access_token . ', oauth_client_id=' . $this->client_id;
	}

	public function updateOfferPrices($offers) {
		$result = $this->sendRequest('POST', "campaigns/{$this->shop_id}/offer-prices/updates.json", json_encode($offers));

		return $this->formatingResult($result);
	}
	
	public function getUpdatedOfferPrices($page = 1) {
		$url = "campaigns/{$this->shop_id}/offer-prices.json?pageSize=" . self::requestLimitOffers . "&page=" . $page;

		$response = $this->sendRequest('GET', $url);

		return $this->formatingResult($response);
	}

	public function refreshPricelist($feed_id) {
		$url = "campaigns/{$this->shop_id}/feeds/{$feed_id}/refresh.json";
		$response = $this->sendRequest('POST', $url);

		return $this->formatingResult($response);
	}

	public function getShopOffersCount() {
		// todo validate
		$total = 0;
		$result = $this->getShopPricelists();

		if (!$result->errors) {
			foreach ($result->data as $feed) {
				if ($feed['content']['status'] == 'OK' && isset($feed['content']['total-offers-count'])) {
					$total += (int)$feed['content']['total-offers-count'];
				}
			}

			$output = new \stdClass();
			$output->data = $total;
			$output->errors = array();

			return $output;
		}

		return $result;
	}

	public function getShopPricelists() {
		$url = "campaigns/{$this->shop_id}/feeds/{$feed_id}/feeds.json";
		$response = $this->sendRequest('POST', $url);

		return $this->formatingResult($response, 'feeds');
	}

	protected function formatingResult($response, $data_container = 'result') {
		$result = $this->getDecodedBody($response);
		
		$output = new \stdClass();
		//$output->success = $result['status'] == 'OK';
		$output->errors = self::getErrors($result);
		$output->data = isset($result[$data_container]) ? $result[$data_container] : null;

		return $output;
	}

	public static function getErrors($response) {
		$errors = array();
		
		if ($response && $response['status'] == 'ERROR') {
			foreach ($response['errors'] as $error) {
				$errors[] = [
					'code' => $error['code'],
					'message' => $error['message'],
				];
			}
		}

		return $errors;
	}
}