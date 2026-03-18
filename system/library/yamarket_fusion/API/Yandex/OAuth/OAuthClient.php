<?php
namespace yamarket_fusion\API\Yandex\OAuth;

class OAuthClient extends \yamarket_fusion\API\Yandex\Common\Client {
	public $service_domain = 'oauth.yandex.ru';
	public $client_id;
	public $client_secret;
	public $refresh_token;

	const CODE_AUTH_TYPE = 'code';
	const TOKEN_AUTH_TYPE = 'token';

	/*
	public function getServiceUrl($resource = '') {
		return 'https://' . $this->service_domain . '/' . (is_null($this->version) ? '' : "{$this->version}/") . rawurlencode($resource);
	}
	*/

	public function __construct($client_id = '', $client_secret = '') {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}

	public function getAuthUrl($redirect_uri = null, $state = null, $type = self::CODE_AUTH_TYPE) {
		$url = $this->getServiceUrl('authorize') . '?response_type=' . $type . '&client_id=' . $this->client_id;
		if ($state) {
			$url .= '&state=' . $state;
		}
		if ($redirect_uri) {
			$url .= '&redirect_uri=' . $redirect_uri;
		}
		return $url;
	}

	public function requestAccessToken($code) {
		$this->getClient()->setAuthData([$this->client_id, $this->client_secret]);

		$response = $this->sendRequest('post', 'token', [
			'grant_type' => 'authorization_code',
			'code' => $code
		]);

		$result = $this->getDecodedBody($response);
		
		if (!isset($result['access_token'])) {
			$err_message = 'Server response doesn\'t contain access token;' . PHP_EOL;
			if (isset($result['error'])) {
				$err_message .= " Error: {$result['error']}" . (isset($result['error_description']) ? " - {$result['error_description']}" : '');
			}
			throw new \Exception($err_message);
		}
		
		$expireDateTime = new \DateTime();
		$expireDateTime->add(new \DateInterval('PT' . $result['expires_in'] . 'S'));
		
		$this->token_expires_in = $expireDateTime->format('Y-m-d H:i:s');
		$this->access_token = $result['access_token'];
		$this->refresh_token = $result['refresh_token'];
		
		return $this;
	}
}