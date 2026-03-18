<?php
namespace yamarket_fusion\API\Yandex\Common;

class Client {
	public $service_domain;
	public $access_token;
	public $token_expires_in;
	public $version;
	
	protected $client;

	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';
	const FORMAT_DEFAULT = self::FORMAT_JSON;

	public function getServiceUrl($resource = '') {
		return 'https://' . $this->service_domain . '/' . (is_null($this->version) ? '' : "{$this->version}/") . rawurlencode($resource);
	}
	
	public function getClient() {
		if (is_null($this->client)) {
			$client = new \yamarket_fusion\Http\HttpClient($this->getServiceUrl());

			$client->addHeaders(['Authorization' => "OAuth " . $this->getAccessToken()]);
			
			$this->client = $client;
		}

		return $this->client;
	}

	public function getAccessToken() {
		return $this->access_token;
	}

	public function sendRequest($method, $url, $data = array()) {
		$method = strtolower($method);
		
		//try{
			$response = $this->getClient()->{$method}($url, $data)->getResponse();

			
		//}
		//catch (\Exception $e) {
			// todo format exceptions
		//}

		return $response;
	}

	protected function getDecodedBody($body, $type = self::FORMAT_DEFAULT) {
		switch ($type) {
			case self::FORMAT_XML:
				return simplexml_load_string((string)$body);
			case self::FORMAT_JSON:
				return json_decode((string)$body, true);
		}
	}
}