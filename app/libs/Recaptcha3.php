<?php

/**
 * =================================================================================
 *
 * VIZU CMS
 * Lib: reCAPTCHA v3
 *
 * =================================================================================
 */

class Recaptcha3 {
	const MIN_SCORE = 0.5; // Default minimal score
	const VERIFY_API_URL = 'https://www.google.com/recaptcha/api/siteverify';

	private $curl;
	private $secret;

	public function __construct(Curl $curl, string $secret) {
		$this->curl = $curl;
		$this->secret = $secret;
	}


	/**
	 * Validate user token by comparing minimal required score
	 * with score returned by Google reCAPTCHA siteverify API
	 *
	 * @param $token - received from front-end reCAPTCHA code
	 * @param $min_score - score that user should have to be recognised
	 *   as not a spammer (0.0 - 1.0)
	 */

	public function validate(string $token, int $min_score = self::MIN_SCORE) {
		$validation = $this->curl->call(self::VERIFY_API_URL, 'POST', [
			// POST params required by reCAPTCHA verifier
			'secret'   => $this->secret,
			'response' => $this->mailer->sanitiseString($_POST['recaptcha_token'] ?? ''),
			'remoteip' => $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']
		]);

		if ($validation->getErrno()) {
			throw new \Exception($validation->getError());
		}

		$result = json_decode($validation->getBody(), true);

		if (empty ($result['score']) || !is_numeric($result['score'])) {
			if (isset($result['error-codes']) && is_array($result['error-codes'])) {
				throw new \Exception(implode(',', $result['error-codes']));
			}
			else {
				throw new \Exception('Unknown reCAPTCHA error occured');
			}
		}

		return ($result['score'] > $min_score);
	}
}