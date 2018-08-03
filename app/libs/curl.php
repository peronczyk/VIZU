<?php


class Curl {

	private $handle; // cURL handle


	public function __construct() {
		$this->handle = curl_init();
	}


	/**
	 * Disable SSL verification
	 */

	public function disable_ssl() {
		curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
	}


	/**
	 * @param string $url
	 * @param string $method
	 * @param array $data
	 */

	public function call(string $url, string $method = 'GET', $data = false) {
		// Prepare call based on method
		switch ($method) {
			case 'POST':
				curl_setopt($this->handle, CURLOPT_POST, true);
				curl_setopt($this->handle, CURLOPT_HTTPHEADER, [
					'Content-Type: application/x-www-form-urlencoded'
				]);

				if (is_array($data)) {
					$query = http_build_query($data);
					curl_setopt($this->handle, CURLOPT_POSTFIELDS, $query);
				}
				break;

			case 'PUT':
				curl_setopt($this->handle, CURLOPT_PUT, 1);
				break;

			default:
				if ($data) {
					$url = sprintf("%s?%s", $url, http_build_query($data));
				}
		}

		curl_setopt($this->handle, CURLOPT_URL, $url);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);

		try {
			$exec_response = curl_exec($this->handle);
			$status_code = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
		}
		catch (Exception $e) {
			echo 'Error [' . $e->getCode() . ']: ' . $e->getMessage();
		}

		curl_close($this->handle);

		$parsed_response = json_decode($exec_response);
		return $parsed_response;
	}
}