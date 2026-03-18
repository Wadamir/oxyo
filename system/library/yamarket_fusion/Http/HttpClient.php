<?php
namespace yamarket_fusion\Http;

class HttpClient {
	public $domain, $statusCode;

	protected $headers = array();

	private $handler;

	const formatJSON = 'json';
	const CONTENT_TYPE_JSON = 'application/json';

	public function __construct($domain = '', $params = []) {
		if ($domain)
			$this->domain = $domain;

		if (isset($params['OAuth']))
			$this->setOAuthData($params['OAuth']);

		$this->handler = curl_init();

		//$proxy = json_decode(file_get_contents('https://gimmeproxy.com/api/getProxy'));
		//curl_setopt($this->handler, CURLOPT_PROXY, $proxy->curl);
		
		//curl_setopt($this->handler, CURLOPT_PROXY, 'http://216.57.167.11:32360');

		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->handler, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, false);
		
		curl_setopt($this->handler, CURLOPT_SSL_VERIFYHOST, false);
	}

	public function setAuthData($data) {
		$this->addHeaders([
			'Authorization' => 'Basic ' . base64_encode("{$data[0]}:{$data[1]}")
		]);

		return $this;
	}

	public function addHeaders($headers) {
		// todo validate $headers
		$this->headers = array_merge($this->headers, $headers);

		return $this;
	}

	private function setUrl($url) {
		curl_setopt($this->handler, CURLOPT_URL, $url);
	}

	private function setHeaders() {
		if ($this->headers) {
			$headers = array();

			foreach ($this->headers as $key => $val) {
				//if ($key == 'Authorization') $val = 'OAuth oauth_token=test, oauth_client_id=111111';
				$headers[] = "{$key}: {$val}";
			}

			//var_dump($headers);

			curl_setopt($this->handler, CURLOPT_HTTPHEADER, $headers);
		}
	}

	public function getResponse($format = null) {
		$result = curl_exec($this->handler);

		//var_dump($result);
		
		if (curl_error($this->handler)) {
			throw new \Exception(__CLASS__ . ': ' . curl_error($this->handler));
		}

		$this->statusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

		if ($format == self::formatJSON) {
			$result = json_decode($result, true);
		}

		return $result;
	}

	public function post($path = '', $params = array()) {
		$this->setHeaders();

		if ($params) {
			curl_setopt($this->handler, CURLOPT_POST, true);
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $params);
		}

		$this->setUrl($this->domain . $path);

		return $this;
	}

	public function get($path = '', $params = array()) {
		$this->setHeaders();

		$url = $this->domain . $path;

		if ($params)
			// todo validate
			$url .= http_build_query($params);

		$this->setUrl($url);

		return $this;
	}
}