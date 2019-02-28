<?php

# ==================================================================================
#
#	VIZU CMS
#	Lib: Curl
#
# ==================================================================================

namespace libs;

class Curl {

	private $handle; // cURL handle
	private $response = []; // Last call result data
	private $default_options = [
		CURLOPT_HEADER         => 0,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_VERBOSE        => true,
		CURLOPT_TIMEOUT        => 10,
	];
	private $ssl_enabled = true;


	/**
	 * Execute a call to defined url
	 *
	 * @param string $url
	 * @param string $method
	 * @param array $data
	 * @param array $options
	 */

	public function call(string $url, string $method = 'GET', array $data = [], array $options = []) : self {
		$this->handle = curl_init();
		$options = $options + $this->default_options;

		// Prepare call based on method
		switch ($method) {
			case 'POST':
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_HTTPHEADER] = [
					'Content-Type: application/x-www-form-urlencoded'
				];

				if (is_array($data)) {
					$query = http_build_query($data);
					$options[CURLOPT_POSTFIELDS] = $query;
				}
				break;

			case 'PUT':
				$options[CURLOPT_PUT] = true;
				break;

			case 'HEAD':
				$options[CURLOPT_NOBODY] = true;
				break;

			default:
				if (!empty($data)) {
					$url = $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($data);
				}
		}

		// Set options
		$options[CURLOPT_SSL_VERIFYPEER] = $this->ssl_enabled;
		$options[CURLOPT_URL] = $url;
		curl_setopt_array($this->handle, $options);

		$this->response = [
			'body'          => curl_exec($this->handle),
			'errno'         => curl_errno($this->handle),
			'error'         => curl_error($this->handle),
			'response_code' => curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE),
			'time'          => curl_getinfo($this->handle, CURLINFO_TOTAL_TIME),
		];

		curl_close($this->handle);

		return $this;
	}


	public function disableSsl() {
		$this->ssl_enabled = false;
	}


	public function enableSsl() {
		$this->ssl_enabled = true;
	}


	public function getBody() {
		return $this->response['body'];
	}


	public function getErrno() {
		return $this->response['errno'];
	}


	public function getError() {
		return $this->response['error'];
	}


	public function getResponseCode() {
		return $this->response['response_code'];
	}


	public function getTime() {
		return $this->response['time'];
	}
}