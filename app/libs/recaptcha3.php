<?php

namespace libs;

class Recaptcha3 {
	const MIN_SCORE = 0.5;
	const VERIFY_API_URL = 'https://www.google.com/recaptcha/api/siteverify';

	private $curl;
	private $secret;

	public function __construct(Curl $curl, string $secret) {
		$this->curl = $curl;
		$this->secret = $secret;
	}

	public function validate(string $token, number $min_score = self::MIN_SCORE, bool $disable_ssl) {
		$validation = $this->curl->call(self::VERIFY_API_URL, 'POST', [
			// POST params required by reCAPTCHA verifier
			'secret'   => $theme_config['contact']['recaptcha_secret'],
			'response' => $mailer->sanitiseString($_POST['recaptcha_token'] | ''),
			'remoteip' => $_SERVER['HTTP_CLIENT_IP'] | $_SERVER['HTTP_X_FORWARDED_FOR'] | $_SERVER['REMOTE_ADDR']
		]);

		if ($validation->getErrno()) {
			throw new Exception($validation->getError());
		}

		$result = json_decode($validation->getBody(), true);

		if (empty ($result['score']) || !is_numeric($result['score'])) {
			throw new Exception($recaptcha_validation_result['error-codes']);
		}

		return ($result['score'] > $min_score);
	}
}